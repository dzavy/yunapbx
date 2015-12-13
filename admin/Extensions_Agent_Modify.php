<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Extensions_Agent_Modify() {
    
    $session = &$_SESSION['Extensions_Agent_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

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
        $Extension = array();
    }

    $smarty->assign('Extension', $Extension);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('Extensions_Agent_Modify.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    // Init data from 'Extensions'
    $query = "
		SELECT
			Ext_Agent.PK_Extension AS PK_Extension,
			Extension,
			FirstName,
			FirstName_Editable,
			LastName,
			LastName_Editable,
			Password,
			Password_Editable,
			WebAccess
		FROM
			Extensions
			INNER JOIN Ext_Agent ON Ext_Agent.PK_Extension = Extensions.PK_Extension
		WHERE
			Extensions.PK_Extension = $id
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data = $result->fetch_assoc();

    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    global $mysqli;
    if ($data['PK_Extension'] == "") {
        $query = "INSERT INTO Extensions(Extension,Type) VALUES('" . $mysqli->real_escape_string($data['Extension']) . "', 'Agent')";
        $mysqli->query($query) or die($mysqli->error . $query);
        $data['PK_Extension'] = $mysqli->insert_id;

        $query = "INSERT INTO Ext_Agent(PK_Extension) VALUES('" . $mysqli->real_escape_string($data['PK_Extension']) . "')";
        $mysqli->query($query) or die($mysqli->error . $query);
    }

    // Update 'Extensions'
    $query = "
		UPDATE
			Ext_Agent
		SET
			FirstName          = '" . $mysqli->real_escape_string($data['FirstName']) . "',
			FirstName_Editable = " . ($data['FirstName_Editable'] ? '1' : '0') . ",
			LastName           = '" . $mysqli->real_escape_string($data['LastName']) . "',
			LastName_Editable  = " . ($data['LastName_Editable'] ? '1' : '0') . ",
			Password_Editable  = " . ($data['Password_Editable'] ? '1' : '0') . ",
			WebAccess          = " . ($data['WebAccess'] ? '1' : '0') . "
		WHERE
			PK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . "
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);

    // Update Password if requested
    if ($data['Password'] != '') {
        $query = "
			UPDATE
				Ext_Agent
			SET
				Password     = '" . $mysqli->real_escape_string($data['Password']) . "'
			WHERE
				PK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . "
			LIMIT 1
		";
        $mysqli->query($query) or die($mysqli->error . $query);
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
    if ((strlen($data['FirstName']) < 1) || (strlen($data['FirstName']) > 32)) {
        $errors['FirstName']['Invalid'] = true;
    }

    return $errors;
}

admin_run('Extensions_Agent_Modify', 'Admin.tpl');
?>
