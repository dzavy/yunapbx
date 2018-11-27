<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_GroupPickup_Modify() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['Extensions_GroupPickup_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    /////myprint($_REQUEST);
    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Groups
    $query = "SELECT PK_Group, Name FROM Groups";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Groups = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Groups[] = $row;
    }

    // Init Available Accounts (Accounts)
    $query = "
		SELECT
			Extensions.PK_Extension,
			Extension,
			Name
		FROM
			Extensions
		WHERE
			Extensions.Type IN ('Virtual', 'SipPhone')
		ORDER BY Extension
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Accounts = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Accounts[] = $row;
    }



    // Init form data (GroupPickup)
    if (@$_REQUEST['submit'] == 'save') {
        $GroupPickup = formdata_from_post();
        $Errors = formdata_validate($GroupPickup);

        if (count($Errors) == 0) {
            $id = formdata_save($GroupPickup);
            header("Location: Extensions_List.php?msg=MODIFY_GroupPickup_EXTENSION&hilight={$id}");
            die();
        }
    } elseif (@$_REQUEST['PK_Extension'] != "") {
        $GroupPickup = formdata_from_db($_REQUEST['PK_Extension']);
    } else {
        $GroupPickup = array();
    }

    $smarty->assign('GroupPickup', $GroupPickup);
    $smarty->assign('Groups', $Groups);
    $smarty->assign('Accounts', $Accounts);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);


    return $smarty->fetch('Extensions_GroupPickup_Modify.tpl');
}

function formdata_from_db($id) {
    $db = DB::getInstance();
    // Init data from 'Extensions'
    $query = "
		SELECT
			*
		FROM
			Ext_GroupPickup
			INNER JOIN Extensions ON Extensions.PK_Extension = Ext_GroupPickup.PK_Extension
		WHERE
			Ext_GroupPickup.PK_Extension = $id
		LIMIT 1
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data = $result->fetch(PDO::FETCH_ASSOC);

    // Init data from 'GroupPickup_Memebers'
    $data['Members'] = array();
    $query = "SELECT FK_Ext_Member, FK_Ext_Group FROM Ext_GroupPickup_Members WHERE FK_Extension = $id";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch_array()) {
        $data['Members'][] = $row[0];
        if ($row[1] != 0) {
            $data['FK_Group_Member'] = $row[1];
        }
    }

    // Init data from 'GroupPickup_Admins'
    $data['Admins'] = array();
    $query = "SELECT FK_Ext_Admin, FK_Ext_Group FROM Ext_GroupPickup_Admins WHERE FK_Extension = $id";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch_array()) {
        $data['Admins'][] = $row[0];
        if ($row[1] != 0) {
            $data['FK_Group_Admin'] = $row[1];
        }
    }

    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    $db = DB::getInstance();
    if ($data['PK_Extension'] == "") {
        $query = "INSERT INTO Extensions(Feature, Type, Extension) VALUES(1, 'GroupPickup', '" . $mysqli->real_escape_string($data['Extension']) . "')";
        $db->query($query) or die(print_r($db->errorInfo(), true));
        $data['PK_Extension'] = $db->lastInsertId();

        $query = "INSERT INTO Ext_GroupPickup(PK_Extension) VALUES('" . $mysqli->real_escape_string($data['PK_Extension']) . "')";
        $db->query($query) or die(print_r($db->errorInfo(), true));
    }

    // Update 'Ext_GroupPickup'
    $query = "
		UPDATE
			Ext_GroupPickup
		SET
			Use_Members_ByAccount = '" . $mysqli->real_escape_string($data['Use_Members_ByAccount']) . "',
			Use_Admins_ByAccount  = '" . $mysqli->real_escape_string($data['Use_Admins_ByAccount']) . "'
		WHERE
			PK_Extension       = " . $mysqli->real_escape_string($data['PK_Extension']) . "
		LIMIT 1
	";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    // Update 'Ext_GroupPickup_Members'
    $query = "DELETE FROM Ext_GroupPickup_Members WHERE FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    if (is_array($data['Members'])) {
        if ($data['Use_Members_ByAccount']) {
            foreach ($data['Members'] as $member) {
                $query = "INSERT INTO Ext_GroupPickup_Members (FK_Extension, FK_Ext_Member,FK_Ext_Group) VALUES ({$data['PK_Extension']}, {$member}, 0)";
                $db->query($query) or die(print_r($db->errorInfo(), true));
            }
        }
    } else {
        $query = "INSERT INTO Ext_GroupPickup_Members (FK_Extension, FK_Ext_Member,FK_Ext_Group) VALUES ({$data['PK_Extension']}, 0, {$data['GroupsM']})";
        $db->query($query) or die(print_r($db->errorInfo(), true));
    }

    // Update 'Ext_GroupPickup_Admins'
    $query = "DELETE FROM Ext_GroupPickup_Admins WHERE FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    if (is_array($data['Admins'])) {
        if ($data['Use_Admins_ByAccount']) {
            foreach ($data['Admins'] as $admin) {
                $query = "INSERT INTO Ext_GroupPickup_Admins (FK_Extension, FK_Ext_Admin,FK_Ext_Group) VALUES ({$data['PK_Extension']}, {$admin}, 0)";
                $db->query($query) or die(print_r($db->errorInfo(), true));
            }
        }
    } else {
        $query = "INSERT INTO Ext_GroupPickup_Admins (FK_Extension, FK_Ext_Admin,FK_Ext_Group) VALUES ({$data['PK_Extension']}, 0, {$data['GroupsA']})";
        $db->query($query) or die(print_r($db->errorInfo(), true));
    }
    return $data['PK_Extension'];
}

function formdata_validate($data) {
    $db = DB::getInstance();
    $errors = array();

    if ($data['PK_Extension'] == '') {
        $create_new = true;
    }

    if ($create_new) {
        // Check if extension is empty
        if ($data['Extension'] == "") {
            $errors['Extension']['Invalid'] = true;
            // Check if Extension is numeric
        } elseif (intval($data['Extension']) . "" != $data['Extension']) {
            $errors['Extension']['Invalid'] = true;
            // Check if extension is proper length
        } elseif (strlen($data['Extension']) < 3 || strlen($data['Extension']) > 5) {
            $errors['Extension']['Invalid'] = true;
            // Check if extension in unique
        } else {
            $query = "SELECT Extension FROM Extensions WHERE Extension = '{$data['Extension']}' LIMIT 1";
            $result = $db->query($query) or die(print_r($db->errorInfo(), true));
            if ($result->num_rows > 0) {
                $errors['Extension']['Duplicate'] = true;
            }
        }
    }

//	// Check if first name is proper length
//	if ((strlen($data['Name'])<1) || (strlen($data['Name'])>20)) {
//		$errors['Name']['Invalid'] = true;
//	}

    return $errors;
}

admin_run('Extensions_GroupPickup_Modify', 'Admin.tpl');
?>