<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Extensions_Virtual_Modify() {
    global $mysqli;
    
    $session = &$_SESSION['Extensions_Virtual_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Init available extension groups (Groups)
    $query = "SELECT PK_Group, Name FROM Groups";
    $result = $mysqli->query($query) or die($mysqli->errno());
    $Groups = array();
    while ($row = $result->fetch_assoc()) {
        $Groups[] = $row;
    }

    // Init available outgoing rules (Rules)
    $query = "SELECT * FROM OutgoingRules ORDER BY Name";
    $result = $mysqli->query($query) or die($mysqli->errno());
    $Rules = array();
    while ($row = $result->fetch_assoc()) {
        $Rules[] = $row;
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
            header("Location: Extensions_List.php?msg=MODIFY_VIRTUAL_EXTENSION&hilight={$id}");
            die();
        }
    } elseif (@$_REQUEST['PK_Extension'] != "") {
        $Extension = formdata_from_db($_REQUEST['PK_Extension']);
    } else {
        $Extension = formdata_from_template($_REQUEST['FK_Template']);
    }

    $smarty->assign('Extension', $Extension);
    $smarty->assign('Groups', $Groups);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);
    $smarty->assign('Rules', $Rules);

    return $smarty->fetch('Extensions_Virtual_Modify.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    // Init data from 'Extensions'
    $query = "
		SELECT
			Ext_Virtual.PK_Extension AS PK_Extension,
			Extension,
			Name,
            IsInternal,
            TargetExtension,
            TargetNumber
		FROM
			Extensions
			INNER JOIN Ext_Virtual ON Ext_Virtual.PK_Extension = Extensions.PK_Extension
		WHERE
			Extensions.PK_Extension = $id
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data = $result->fetch_assoc();

    // Init data from 'Extension_Groups'
    $query = "
		SELECT
			FK_Group
		FROM
			Extension_Groups
		WHERE
			FK_Extension = $id
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data['Groups'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Groups'][] = $row['FK_Group'];
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
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data['Rules'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Rules'][] = $row['FK_OutgoingRule'];
    }

    return $data;
}

function formdata_from_template($id) {
    global $mysqli;

    $data = array();
    
    $data["IsInternal"] = 1;
    
    $query = "
		SELECT
			FK_Group
		FROM
			Template_Groups
		WHERE
			FK_Template = $id
	";
    $result = $mysqli->query($query) or die($mysqli->error);

    $data['Groups'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Groups'][] = $row['FK_Group'];
    }

    $query = "
		SELECT
			FK_OutgoingRule
		FROM
			Template_Rules
		WHERE
			FK_Template = $id
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $data['Rules'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Rules'][] = $row['FK_OutgoingRule'];
    }

    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    global $mysqli;
    if ($data['PK_Extension'] == "") {
        $query = "INSERT INTO Extensions(Extension,Type) VALUES('" . $mysqli->real_escape_string($data['Extension']) . "', 'Virtual')";
        $mysqli->query($query) or die($mysqli->error . $query);
        $data['PK_Extension'] = $mysqli->insert_id;

        $query = "INSERT INTO Ext_Virtual(PK_Extension) VALUES('" . $mysqli->real_escape_string($data['PK_Extension']) . "')";
        $mysqli->query($query) or die($mysqli->error . $query);
    }

    $query = "UPDATE Extensions SET Name = '". $mysqli->real_escape_string($data['Name']) . "' WHERE PK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']);
    $mysqli->query($query) or die($mysqli->error . $query);
    
    // Update 'Extensions'
    $query = "
		UPDATE
			Ext_Virtual
		SET
			IsInternal         = " . ($data['IsInternal'] ? '1' : '0') . ",
			TargetExtension       = '" . $mysqli->real_escape_string($data['TargetExtension']) . "',
			TargetNumber       = '" . $mysqli->real_escape_string($data['TargetNumber']) . "'
		WHERE
			PK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . "
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);

    // Update 'Extension_Groups'
    $query = "DELETE FROM Extension_Groups WHERE FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $mysqli->query($query) or die($mysqli->error);
    if (is_array($data['Groups'])) {
        foreach ($data['Groups'] as $FK_Group) {
            $query = "INSERT INTO Extension_Groups (FK_Extension, FK_Group) VALUES ({$data['PK_Extension']}, $FK_Group)";
            $mysqli->query($query) or die($mysqli->error);
        }
    }

    // Update 'Extension_Rules'
    $query = "DELETE FROM Extension_Rules WHERE FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $mysqli->query($query) or die($mysqli->error . $query);

    if ((is_array($data['Rules'])) && (!$data['IsInternal'])) {
        foreach ($data['Rules'] as $FK_OutgoingRule => $Status) {
            if ($Status == 0) {
                continue;
            }
            $query = "INSERT INTO Extension_Rules (FK_Extension, FK_OutgoingRule) VALUES ({$data['PK_Extension']}, {$FK_OutgoingRule})";
            $mysqli->query($query) or die($mysqli->error . $query);
        }
    }

    return $data['PK_Extension'];
}

function formdata_validate($data) {
    global $mysqli;
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
            $result = $mysqli->query($query) or die($mysqli->error . $query);
            if ($result->num_rows > 0) {
                $errors['Extension']['Duplicate'] = true;
            }
        }
    }

    // Check if first name is proper length
    if ((strlen($data['Name']) < 1) || (strlen($data['Name']) > 32)) {
        $errors['Name']['Invalid'] = true;
    }

    return $errors;
}

admin_run('Extensions_Virtual_Modify', 'Admin.tpl');
?>
