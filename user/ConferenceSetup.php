<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/user_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/voicemail_utils.inc.php');

function ConferenceSetup() {
    global $mysqli;
    $session = &$_SESSION['ConferenceSetup'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Init form data (Extension)
    if (@$_REQUEST['submit'] == 'save') {
        $Conference = formdata_from_post();
        $Errors = formdata_validate($Conference);

        if (count($Errors) == 0) {
            formdata_save($Conference);
            asterisk_UpdateConf('meetme.conf');
            asterisk_Reload();
            header("Location: ConferenceSetup.php?msg=MODIFY_CONFERENCE");
            die();
        }
    } else {
        $Conference = formdata_from_db($_SESSION['_USER']['PK_Extension']);
    }

    // Init Available Accounts (Accounts)
    $query = "
		SELECT
			Extensions.PK_Extension,
			Extension,
			CONCAT(IFNULL(Ext_SipPhones.FirstName,''),IFNULL(Ext_Virtual.FirstName,'')) AS FirstName,
			CONCAT(IFNULL(Ext_SipPhones.LastName,'') ,IFNULL(Ext_Virtual.LastName,''))  AS LastName
		FROM
			Extensions
			LEFT JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			LEFT JOIN Ext_Virtual   ON Ext_Virtual.PK_Extension   = Extensions.PK_Extension
		WHERE
			Extensions.Type IN ('Virtual', 'SipPhone')
		ORDER BY Extension
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $Accounts = array();
    while ($row = $result->fetch_assoc()) {
        $Accounts[] = $row;
    }

    $smarty->assign('Conference', $Conference);
    $smarty->assign('Accounts', $Accounts);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('ConferenceSetup.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    $query = "SELECT * FROM Ext_ConfCenter_Rooms WHERE FK_Extension_Owner = '{$_SESSION['_USER']['PK_Extension']}' LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error);
    $data = $result->fetch_assoc();

    $query = "SELECT FK_Extension FROM Ext_ConfCenter_Admins WHERE FK_Room = '{$data['PK_Room']}'";
    $result = $mysqli->query($query) or die($mysqli->error);
    while ($row = $result->fetch_assoc()) {
        $data['Admins'][] = $row['FK_Extension'];
    }

    return $data;
}

function formdata_from_post() {
    return $_REQUEST;
}

function formdata_save($data) {
    global $mysqli;
    $query = "
		UPDATE
			Ext_ConfCenter_Rooms
		SET
			Number          = '" . $mysqli->real_escape_string($data['Number']) . "',
			Operator        = '" . $mysqli->real_escape_string($data['Operator']) . "',
			PlayEnterSound  = '" . ($data['PlayEnterSound'] == '1' ? '1' : '0') . "',
			PlayMOH         = '" . ($data['PlayMOH'] == '1' ? '1' : '0') . "',
			OnlyAdminTalk   = '" . ($data['OnlyAdminTalk'] == '1' ? '1' : '0') . "',
			HangupIfNoAdmin = '" . ($data['HangupIfNoAdmin'] == '1' ? '1' : '0') . "',
			NoTalkTillAdmin = '" . ($data['NoTalkTillAdmin'] == '1' ? '1' : '0') . "'
		WHERE
			FK_Extension_Owner = '{$_SESSION['_USER']['PK_Extension']}' 
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);

    $query = "SELECT PK_Room FROM Ext_ConfCenter_Rooms WHERE FK_Extension_Owner = '{$_SESSION['_USER']['PK_Extension']}' LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $row = $result->fetch_assoc();
    $PK_Room = $row['PK_Room'];

    $query = "DELETE FROM Ext_ConfCenter_Admins WHERE FK_Room = {$PK_Room}";
    $mysqli->query($query) or die($mysqli->error . $query);
    if (is_array($data['Admins'])) {
        foreach ($data['Admins'] as $FK_Extension) {
            $FK_Extension = intval($FK_Extension);
            $query = "INSERT INTO Ext_ConfCenter_Admins (FK_Room, FK_Extension) VALUES('{$PK_Room}','$FK_Extension')";
            $mysqli->query($query) or die($mysqli->error . $query);
        }
    }
}

function formdata_validate($data) {
    $errors = array();

    if ($data['Number'] != '') {
        if (!preg_match('/^[0-9]{5}$/', $data['Number'])) {
            $errors['Number']['Format'] = true;
        }
    }

    if ($data['Operator'] != '') {
        if (!preg_match('/^[0-9]{3,5}$/', $data['Operator'])) {
            $errors['Operator']['Format'] = true;
        }
    }
    return $errors;
}

user_run('ConferenceSetup', 'User.tpl');
