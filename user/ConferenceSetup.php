<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/user_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/voicemail_utils.inc.php');

function ConferenceSetup() {
    $db = DB::getInstance();
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
            asterisk_UpdateConf('confbridge.conf');
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

    $smarty->assign('Conference', $Conference);
    $smarty->assign('Accounts', $Accounts);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('ConferenceSetup.tpl');
}

function formdata_from_db($id) {
    $db = DB::getInstance();
    $query = "SELECT * FROM Ext_ConfCenter_Rooms WHERE FK_Extension_Owner = '{$_SESSION['_USER']['PK_Extension']}' LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data = $result->fetch(PDO::FETCH_ASSOC);

    $query = "SELECT FK_Extension FROM Ext_ConfCenter_Admins WHERE FK_Room = '{$data['PK_Room']}'";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data['Admins'][] = $row['FK_Extension'];
    }

    return $data;
}

function formdata_from_post() {
    return $_REQUEST;
}

function formdata_save($data) {
    $db = DB::getInstance();
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
    $db->query($query) or die(print_r($db->errorInfo(), true));

    $query = "SELECT PK_Room FROM Ext_ConfCenter_Rooms WHERE FK_Extension_Owner = '{$_SESSION['_USER']['PK_Extension']}' LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $PK_Room = $row['PK_Room'];

    $query = "DELETE FROM Ext_ConfCenter_Admins WHERE FK_Room = {$PK_Room}";
    $db->query($query) or die(print_r($db->errorInfo(), true));
    if (is_array($data['Admins'])) {
        foreach ($data['Admins'] as $FK_Extension) {
            $FK_Extension = intval($FK_Extension);
            $query = "INSERT INTO Ext_ConfCenter_Admins (FK_Room, FK_Extension) VALUES('{$PK_Room}','$FK_Extension')";
            $db->query($query) or die(print_r($db->errorInfo(), true));
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
