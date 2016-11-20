<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_Voicemail_Modify() {

    $session = &$_SESSION['Extensions_Voicemail_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg']) ? $_REQUEST['msg'] : "");

    if (@$_REQUEST['submit'] == 'save') {
        $Extension = formdata_from_post();
        $Errors = formdata_validate($Extension);

        if (count($Errors) == 0) {
            if ($Extension['PK_Extension'] != '') {
                $msg = 'MODIFY_VOICEMAIL_EXTENSION';
            } else {
                $msg = 'ADD_VOICEMAIL_EXTENSION';
            }
            $id = formdata_save($Extension);

            header("Location: Extensions_List.php?hilight={$id}&msg={$msg}");
            die();
        }
    } elseif (@$_REQUEST['PK_Extension'] != "") {
        $Extension = formdata_from_db($_REQUEST['PK_Extension']);
    } else {
        $Extension = formdata_from_default();
    }

    $smarty->assign('Extension', $Extension);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('Extensions_Voicemail_Modify.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    $query = "
		SELECT
			*
		FROM
			Ext_Voicemail
			INNER JOIN Extensions ON Extensions.PK_Extension = Ext_Voicemail.PK_Extension
		WHERE
			Ext_Voicemail.PK_Extension = '$id'
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data = $result->fetch_assoc();
    return $data;
}

function formdata_from_default() {
    $data = array();

    $data['IVRDial'] = 0;
    $data['RequirePassword'] = 1;

    return $data;
}

function formdata_from_post() {
    return $_REQUEST;
}

function formdata_save($data) {
    global $mysqli;
    if ($data['PK_Extension'] == "") {
        $query = "INSERT INTO Extensions(Type, Extension) VALUES('Voicemail', '" . $mysqli->real_escape_string($data['Extension']) . "')";
        $mysqli->query($query) or die($mysqli->error . $query);
        $data['PK_Extension'] = $mysqli->insert_id;

        $query = "INSERT INTO Ext_Voicemail(PK_Extension) VALUES({$data['PK_Extension']})";
        $mysqli->query($query) or die($mysqli->error . $query);
    }

    // Update 'Ext_Voicemail'
    $query = "
		UPDATE
			Ext_Voicemail
		SET
			RequirePassword = " . ($data['RequirePassword'] == '0' ? '0' : '1') . "
		WHERE
			PK_Extension    = " . $mysqli->real_escape_string($data['PK_Extension']) . "
		LIMIT 1
	";

    $mysqli->query($query) or die($mysqli->error . $query);

    // Update 'IVRDial"
    $query = "UPDATE Extensions SET IVRDial = " . ($data['IVRDial'] == 1 ? '1' : '0') . " WHERE PK_Extension = {$data['PK_Extension']}";
    $mysqli->query($query) or die($mysqli->error . $query);

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

    return $errors;
}

admin_run('Extensions_Voicemail_Modify', 'Admin.tpl');
?>