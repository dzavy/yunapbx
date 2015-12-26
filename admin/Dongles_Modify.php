<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . "/../include/asterisk_utils.inc.php");

function Dongles_Modify() {
    global $mysqli;
    
    $session = &$_SESSION['Dongles_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');


//	myprint($_REQUEST);
    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Init available outgoing rules (Rules)
    $query = "SELECT * FROM OutgoingRules ORDER BY Name";
    $result = $mysqli->query($query) or die($mysqli->errno());
    $Rules = array();
    while ($row = $result->fetch_assoc()) {
        $Rules[] = $row;
    }

    // Init form data (Providers)
    if ($_REQUEST['submit'] == 'save') {
        $Dongle = formdata_from_post();
        $Errors = formdata_validate($Dongle);

        if (count($Errors) == 0) {
            $id = formdata_save($Dongle);
            asterisk_UpdateConf('dongle.conf');
            asterisk_UpdateConf('extensions.conf');
            asterisk_Reload();
            header("Location: Dongles_List.php?msg=MODIFY_DONGLE&hilight={$id}");
            die();
        }
    } elseif ($_REQUEST['PK_Dongle'] != "") {
        $Dongle = formdata_from_db($_REQUEST['PK_Dongle']);
    } else {
        $Dongle = formdata_from_default();
    }

    $smarty->assign('Dongle', $Dongle);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);
    $smarty->assign('Rules', $Rules);

    return $smarty->fetch('Dongles_Modify.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    // Init data from 'SipProviders'
    $query = "
		SELECT
			*
		FROM
			Dongles
		WHERE
			PK_Dongle = $id
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error);
    $data = $result->fetch_assoc();

    // Init outgoing rules
    $query = "
		SELECT
			FK_OutgoingRule
		FROM
			Dongle_Rules
		WHERE
			FK_Dongle = $id
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data['Rules'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Rules'][] = $row['FK_OutgoingRule'];
    }

    return $data;
}

function formdata_from_default() {
    $data = array();
    return $data;
}

function formdata_from_post() {
    $data = $_POST;

    if (!is_array($data['Rules'])) {
        $data['Rules'] = array();
    }

    return $data;
}

function formdata_save($data) {
    global $mysqli;
    if ($data['PK_Dongle'] == "") {
        $query = "INSERT INTO Dongles() VALUES()";
        $mysqli->query($query) or die($mysqli->error . $query);

        $data['PK_Dongle'] = $mysqli->insert_id;
    }

    // Update 'Extensions'
    $query = "
		UPDATE
			Dongles
		SET
			Name               = '" . $mysqli->real_escape_string($data['Name']) . "',
			IMEI               = '" . $mysqli->real_escape_string($data['IMEI']) . "',
			IMSI               = '" . $mysqli->real_escape_string($data['IMSI']) . "',
			CallbackExtension  = '" . $mysqli->real_escape_string($data['CallbackExtension']) . "',
            ApplyIncomingRules = " . ($data['ApplyIncomingRules'] ? '1' : '0') . "
		WHERE
			PK_Dongle          = " . $mysqli->real_escape_string($data['PK_Dongle']) . "
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);

    // Update 'SipProvider_Rules'
    $query = "DELETE FROM Dongle_Rules WHERE FK_Dongle = " . $mysqli->real_escape_string($data['PK_Dongle']) . " ";
    $mysqli->query($query) or die($mysqli->error);
    if (is_array($data['Rules'])) {
        foreach ($data['Rules'] as $FK_OutgoingRule) {
            if ($FK_OutgoingRule != 0) {
                $query = "INSERT INTO Dongle_Rules(FK_Dongle, FK_OutgoingRule) VALUES ({$data['PK_Dongle']}, $FK_OutgoingRule)";
                $mysqli->query($query) or die($mysqli->error);
            }
        }
    }

    // If we don't apply incoming rules to this provider, make sure we remove existing ones (if exists)
    if ($data['ApplyIncomingRules'] == 0) {
        $query = "DELETE FROM IncomingRoutes WHERE ProviderType='SIP' AND ProviderID = {$data['PK_Dongle']}";
        $mysqli->query($query) or die($mysqli->error);
    }

    return $data['PK_Dongle'];
}

function formdata_validate($data) {
    global $mysqli;
    $errors = array();
    if ($data['Dongle'] == '') {
        $create_new = true;
    }

    // Check if name is 1-32 chars long
    if (strlen($data['Name']) < 1 || strlen($data['Name']) > 32) {
        $errors['Name']['Invalid'] = true;
    }

    // Check if account id is 1-32 chars long
    if (!preg_match("/^[0-9]{15,16}$/", $data['IMEI'])) {
        $errors['IMEI']['Invalid'] = true;
    }

    // Check if account id is 1-32 chars long
    if (!preg_match("/^[0-9]{14,15}$/", $data['IMSI'])) {
        $errors['IMSI']['Invalid'] = true;
    }

    //if ($data['ApplyIncomingRules'] == 1) {
    //    // Check if callback extension is formed of digits only
    //    if ($data['CallbackExtension'] != "" . intval($data['CallbackExtension'])) {
    //        $errors['CallbackExtension']['Invalid'] = true;
    //        // Check if extension is 3-5 digits long
    //    } elseif (strlen($data['CallbackExtension']) < 3 || strlen($data['CallbackExtension']) > 5) {
    //        $errors['CallbackExtension']['Invalid'] = true;
    //        // Check if extension is valid on the system
    //    } else {
    //        $query = "SELECT PK_Extension FROM Extensions WHERE Extension = '" . $mysqli->real_escape_string($data['CallbackExtension']) . "' LIMIT 1";
    //        $result = $mysqli->query($query) or die($mysqli->error . $query);
    //        if ($result->num_rows < 1) {
    //            $errors['CallbackExtension']['NoMatch'] = true;
    //        }
    //    }
    //}

    return $errors;
}

admin_run('Dongles_Modify', 'Admin.tpl');
?>
