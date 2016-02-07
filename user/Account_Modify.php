<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/user_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/voicemail_utils.inc.php');

function Account_Modify() {
    $session = &$_SESSION['Account_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg']) ? $_REQUEST['msg'] : "");

    // Init form data (Extension)
    if (@$_REQUEST['submit'] == 'save') {
        $Extension = formdata_from_post();
        $Errors = formdata_validate($Extension);

        if (count($Errors) == 0) {
            $id = formdata_save($Extension);
            asterisk_UpdateConf('sip.conf');
            asterisk_UpdateConf('voicemail.conf');
            asterisk_Reload();
            header("Location: Account_Modify.php?msg=MODIFY_EXTENSION&hilight={$id}");
            die();
        }
    } else {
        $Extension = formdata_from_db($_SESSION['_USER']['PK_Extension']);
    }

    $smarty->assign('Extension', $Extension);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('Account_Modify.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    // Init data from 'Extensions'
    $query = "
		SELECT
			Extensions.PK_Extension AS PK_Extension,
			Extension,
			Name,
			Name_Editable,
			Password,
			Password_Editable,
			Email,
			Email_Editable
		FROM
			Extensions
			" . ($_SESSION['_USER']['Type'] == 'SipPhone' ? "
				INNER JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			" : "") . "
			" . ($_SESSION['_USER']['Type'] == 'Virtual' ? "
				INNER JOIN Ext_Virtual ON Ext_Virtual.PK_Extension = Extensions.PK_Extension
			" : "") . "
			" . ($_SESSION['_USER']['Type'] == 'Agent' ? "
				INNER JOIN Ext_Agent ON Ext_Agent.PK_Extension = Extensions.PK_Extension
			" : "") . "
		WHERE
			Extensions.PK_Extension = $id
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data = $result->fetch_assoc();

    return $data;
}

function formdata_from_post() {
    return $_REQUEST;
}

function formdata_save($data) {
    global $mysqli;
    // Update 'First Name'
    $query = "
		UPDATE
            Extensions,
			" . ($_SESSION['_USER']['Type'] == 'SipPhone' ? "Ext_SipPhones" : "") . "
			" . ($_SESSION['_USER']['Type'] == 'Virtual' ? "Ext_Virtual  " : "") . "
			" . ($_SESSION['_USER']['Type'] == 'Agent' ? "Ext_Agent    " : "") . "
		SET
			Name          = '" . $mysqli->real_escape_string($data['Name']) . "'
		WHERE
			PK_Extension       = " . $mysqli->real_escape_string($_SESSION['_USER']['PK_Extension']) . "
			AND
			Name_Editable = 1
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);

    // Update 'Email'
    $query = "
		UPDATE
			" . ($_SESSION['_USER']['Type'] == 'SipPhone' ? "Ext_SipPhones" : "") . "
			" . ($_SESSION['_USER']['Type'] == 'Virtual' ? "Ext_Virtual  " : "") . "
			" . ($_SESSION['_USER']['Type'] == 'Agent' ? "Ext_Agent    " : "") . "
		SET
			Email              = '" . $mysqli->real_escape_string($data['Email']) . "'
		WHERE
			PK_Extension       = " . $mysqli->real_escape_string($_SESSION['_USER']['PK_Extension']) . "
			AND
			Email_Editable = 1
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);

    // Update Password if requested
    if ($data['Password'] != '') {
        $query = "
			UPDATE
				" . ($_SESSION['_USER']['Type'] == 'SipPhone' ? "Ext_SipPhones" : "") . "
				" . ($_SESSION['_USER']['Type'] == 'Virtual' ? "Ext_Virtual  " : "") . "
				" . ($_SESSION['_USER']['Type'] == 'Agent' ? "Ext_Agent    " : "") . "
			SET
				Password     = '" . $mysqli->real_escape_string($data['Password']) . "'
			WHERE
				PK_Extension = " . $mysqli->real_escape_string($_SESSION['_USER']['PK_Extension']) . "
				AND
				Password_Editable = 1
			LIMIT 1
		";
        $mysqli->query($query) or die($mysqli->error . $query);
    }

    return $_SESSION['_USER']['PK_Extension'];
}

function formdata_validate($data) {
    $errors = array();

    // Check if password is empty
    if ($data['Password'] == "") {
        // Nothing to do
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

user_run('Account_Modify', 'User.tpl');
?>
