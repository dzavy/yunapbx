<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Extensions_Agent_Modify() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['Extensions_Agent_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Init available extension groups (Groups)
    $query = "SELECT PK_Group, Name FROM Groups";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Groups = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Groups[] = $row;
    }

    // Init available outgoing rules (Rules)
    $query = "SELECT * FROM OutgoingRules ORDER BY Name";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Rules = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Rules[] = $row;
    }

    // Init available extension groups (Features)
    $query = "SELECT PK_Feature, Name FROM Features";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Features = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Features[] = $row;
    }

    // Init form data (Extension)
    if (@$_REQUEST['submit'] == 'save') {
        $Extension = formdata_from_post();
        $Errors = formdata_validate($Extension);

        if (count($Errors) == 0) {
            $id = formdata_save($Extension);
            asterisk_UpdateConf('sip.conf');
            asterisk_UpdateConf('voicemail.conf');
            asterisk_Reload();
            header("Location: Extensions_List.php?msg=MODIFY_AGENT_EXTENSION&hilight={$id}");
            die();
        }
    } elseif (@$_REQUEST['PK_Extension'] != "") {
        $Extension = formdata_from_db($_REQUEST['PK_Extension']);
    } else {
        $Extension = formdata_from_template($_REQUEST['FK_Template']);
    }

    $smarty->assign('Extension', $Extension);
    $smarty->assign('Features', $Features);
    $smarty->assign('Groups', $Groups);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);
    $smarty->assign('Rules', $Rules);

    return $smarty->fetch('Extensions_Agent_Modify.tpl');
}

function formdata_from_db($id) {
    $db = DB::getInstance();
    // Init data from 'Extensions'
    $query = "
		SELECT
			Ext_Agent.PK_Extension AS PK_Extension,
			Extension,
			Name,
			Name_Editable,
			Password_Editable,
			Email,
			Email_Editable,
			IVRDial
		FROM
			Extensions
			INNER JOIN Ext_Agent ON Ext_Agent.PK_Extension = Extensions.PK_Extension
		WHERE
			Extensions.PK_Extension = $id";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data = $result->fetch(PDO::FETCH_ASSOC);

    // Init data from 'Extension_Groups'
    $query = "
		SELECT
			FK_Group
		FROM
			Extension_Groups
		WHERE
			FK_Extension = $id
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data['Groups'] = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data['Groups'][] = $row['FK_Group'];
    }

    // Init data from 'Ext_Virtual_Features'
    $query = "
		SELECT
			FK_Feature
		FROM
			Ext_Agent_Features
		WHERE
			FK_Extension = $id
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data['Features'] = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data['Features'][] = $row['FK_Feature'];
    }

    // Init outgoing rules
    $query = "
		SELECT
			FK_OutgoingRule
		FROM
			Extension_Rules
		WHERE
			FK_Extension = {$data['PK_Extension']}
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data['Rules'] = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data['Rules'][] = $row['FK_OutgoingRule'];
    }

    return $data;
}

function formdata_from_template($id) {
    $db = DB::getInstance();
    $query = "
		SELECT
			PK_Template,
			Name_Editable,
			Password_Editable,
			Email_Editable
		FROM
			Templates
		WHERE
			PK_Template = $id";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data = $result->fetch(PDO::FETCH_ASSOC);

    $query = "
		SELECT
			FK_Group
		FROM
			Template_Groups
		WHERE
			FK_Template = $id
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $data['Groups'] = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data['Groups'][] = $row['FK_Group'];
    }

    $query = "
		SELECT
			FK_Feature
		FROM
			Template_Features
		WHERE
			FK_Template = $id
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $data['Features'] = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data['Features'][] = $row['FK_Feature'];
    }

    $query = "
		SELECT
			FK_OutgoingRule
		FROM
			Template_Rules
		WHERE
			FK_Template = $id
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $data['Rules'] = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data['Rules'][] = $row['FK_OutgoingRule'];
    }

    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    $db = DB::getInstance();
    if ($data['PK_Extension'] == "") {
        $query = "INSERT INTO Extensions(Extension,Type) VALUES('" . $mysqli->real_escape_string($data['Extension']) . "', 'Agent')";
        $db->query($query) or die(print_r($db->errorInfo(), true));
        $data['PK_Extension'] = $db->lastInsertId();

        $query = "INSERT INTO Ext_Agent(PK_Extension) VALUES('" . $mysqli->real_escape_string($data['PK_Extension']) . "')";
        $db->query($query) or die(print_r($db->errorInfo(), true));
    }

    $query = "UPDATE Extensions SET Name = '". $mysqli->real_escape_string($data['Name']) . "' WHERE PK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']);
    $db->query($query) or die(print_r($db->errorInfo(), true));
    
    // Update 'Extensions'
    $query = "
		UPDATE
			Ext_Agent
		SET
			Name_Editable      = " . ($data['Name_Editable'] ? '1' : '0') . ",
			Password_Editable  = " . ($data['Password_Editable'] ? '1' : '0') . ",
			Email              = '" . $mysqli->real_escape_string($data['Email']) . "',
			Email_Editable     = " . ($data['Email_Editable'] ? '1' : '0') . "
		WHERE
			PK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']);
			
    $db->query($query) or die(print_r($db->errorInfo(), true));

    // Update 'Extension_Groups'
    $query = "DELETE FROM Extension_Groups WHERE FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $db->query($query) or die(print_r($db->errorInfo(), true));
    if (is_array($data['Groups'])) {
        foreach ($data['Groups'] as $FK_Group) {
            $query = "INSERT INTO Extension_Groups (FK_Extension, FK_Group) VALUES ({$data['PK_Extension']}, $FK_Group)";
            $db->query($query) or die(print_r($db->errorInfo(), true));
        }
    }

    // Update 'Ext_Virtual_Features'
    $query = "DELETE FROM Ext_Agent_Features WHERE FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    if (is_array($data['Features'])) {
        foreach ($data['Features'] as $FK_Feature) {
            $query = "INSERT INTO Ext_Agent_Features (FK_Extension, FK_Feature) VALUES ({$data['PK_Extension']}, $FK_Feature)";
            $db->query($query) or die(print_r($db->errorInfo(), true));
        }
    }

    // Update Password if requested
    if ($data['Password'] != '') {
        $query = "
			UPDATE
				Ext_Agent
			SET
				Password     = '" . $mysqli->real_escape_string($data['Password']) . "'
			WHERE
				PK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']);
				
        $db->query($query) or die(print_r($db->errorInfo(), true));
    }


    // Update 'Extension_Rules'
    $query = "DELETE FROM Extension_Rules WHERE FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    if (is_array($data['Rules'])) {
        foreach ($data['Rules'] as $FK_OutgoingRule => $Status) {
            if ($Status == 0) {
                continue;
            }
            $query = "INSERT INTO Extension_Rules (FK_Extension, FK_OutgoingRule) VALUES ({$data['PK_Extension']}, {$FK_OutgoingRule})";
            $db->query($query) or die(print_r($db->errorInfo(), true));
        }
    }

    // Update 'IVRDial"
    $query = "UPDATE Extensions SET IVRDial = " . ($data['IVRDial'] == 1 ? '1' : '0') . " WHERE PK_Extension = {$data['PK_Extension']}";
    $db->query($query) or die(print_r($db->errorInfo(), true));

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

    // Check if password is empty
    if ($data['Password'] == "") {
        if ($create_new) {
            $errors['Password']['Invalid'] = true;
        }
        // Check if password is numeric
    } elseif (intval($data['Password']) . "" != $data['Password']) {
        $errors['Password']['Invalid'] = true;
        // Check if password is proper lenght
    } elseif (strlen($data['Password']) < 3 || strlen($data['Password']) > 10) {
        $errors['Password']['Invalid'] = true;
        // Check if passwords match it's retype
    } elseif ($data['Password'] != $data['Password_Retype']) {
        $errors['Password']['Match'] = true;
    }

    // Check if first name is proper length
    if ((strlen($data['Name']) < 1) || (strlen($data['Name']) > 32)) {
        $errors['Name']['Invalid'] = true;
    }

    return $errors;
}

admin_run('Extensions_Agent_Modify', 'Admin.tpl');
?>
