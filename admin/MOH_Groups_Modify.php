<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function MOH_Groups_Modify() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['MOH_Groups_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    if (@$_REQUEST['submit'] == 'save') {
        $Group = formdata_from_post();
        $Errors = formdata_validate($Group);

        if (count($Errors) == 0) {
            if ($Group['PK_Group'] != '') {
                $msg = 'MODIFY_MOH_GROUP';
            } else {
                $msg = 'ADD_MOH_GROUP';
            }
            $id = formdata_save($Group);

            asterisk_UpdateConf('musiconhold.conf');
            asterisk_Reload();

            header("Location: MOH_Groups_List.php?hilight={$id}&msg={$msg}");
            die();
        }
    } elseif (@$_REQUEST['PK_Group'] != "") {
        $Group = formdata_from_db($_REQUEST['PK_Group']);
    } else {
        $Group = formdata_from_default();
    }

    $smarty->assign('Group', $Group);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('MOH_Groups_Modify.tpl');
}

function formdata_from_db($id) {
    $db = DB::getInstance();
    $query = "
		SELECT
			*
		FROM
			Moh_Groups
		WHERE
			PK_Group = '$id'
		LIMIT 1
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data = $result->fetch(PDO::FETCH_ASSOC);
    return $data;
}

function formdata_from_default() {
    $data = array();

    $data['Ordered'] = '1';
    $data['Volume'] = '100';

    return $data;
}

function formdata_from_post() {
    $data = $_REQUEST;
    $data['Volume'] = intval($_REQUEST['Volume']);
    return $data;
}

function formdata_save($data) {
    $db = DB::getInstance();
    global $conf;
    
    if ($data['PK_Group'] == "") {
        $query = "INSERT INTO Moh_Groups() VALUES()";
        $db->query($query) or die(print_r($db->errorInfo(), true));
        $data['PK_Group'] = $db->lastInsertId();

        $bigPK_Group = str_pad($data['PK_Group'], 10, "0", STR_PAD_LEFT);

        mkdir($conf['dirs']['moh'] . "/group_" . $bigPK_Group, 0755, true);
    }

    // Update 'Moh_Groups'
    $query = "
		UPDATE
			Moh_Groups
		SET
			Name        = '" . $mysqli->real_escape_string($data['Name']) . "',
			Description = '" . $mysqli->real_escape_string($data['Description']) . "'
		WHERE
			PK_Group = " . $mysqli->real_escape_string($data['PK_Group']) . "
			AND
			Protected = 0
		LIMIT 1
	";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    $query = "
		UPDATE
			Moh_Groups
		SET
			Volume      = " . intval($data['Volume']) . ",
			Ordered     = " . ($data['Ordered'] ? '1' : '0') . "
		WHERE
			PK_Group = " . $mysqli->real_escape_string($data['PK_Group']) . "
		LIMIT 1
	";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    return $data['PK_Group'];
}

function formdata_validate($data) {
    $db = DB::getInstance();
    $errors = array();

    if (strlen($data['Name']) < 1 || strlen($data['Name']) > 15) {
        $errors['Name']['Invalid'] = true;
    } else {
        $query = "SELECT * FROM Moh_Groups WHERE Name = '" . ($mysqli->escape_string($data['Name'])) . "' AND NOT PK_Group = '" . intval($data['PK_Group']) . "' LIMIT 1";
        $result = $db->query($query) or die(print_r($db->errorInfo(), true));

        if ($result->num_rows != 0) {
            $errors['Name']['Duplicate'] = 1;
        }
    }

    return $errors;
}

admin_run('MOH_Groups_Modify', 'Admin.tpl');
?>
