<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function IVR_Menus_Modify() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['IVR_Menus_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    if (@$_REQUEST['submit'] == 'save') {
        $Menu = formdata_from_post();
        $Errors = formdata_validate($Action);

        if (count($Errors) == 0) {
            if ($Menu['PK_Menu'] != '') {
                $msg = 'MODIFY_MENU';
            } else {
                $msg = 'ADD_MENU';
            }
            $id = formdata_save($Menu);

            header("Location: IVR_Menus.php?PK_Menu={$id}&msg={$msg}");
            die();
        }
    } elseif (@$_REQUEST['PK_Menu'] != "") {
        $Menu = formdata_from_db($_REQUEST['PK_Menu']);
    } else {
        $Menu = formdata_from_default();
    }

    $smarty->assign('Menu', $Menu);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('IVR_Menus_Modify.tpl');
}

function formdata_from_db($id) {
    $db = DB::getInstance();
    $query = "SELECT * FROM IVR_Menus WHERE PK_Menu = '$id' LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data = $result->fetch(PDO::FETCH_ASSOC);

    return $data;
}

function formdata_from_default() {
    $data = array();

    return $data;
}

function formdata_from_post() {
    return $_REQUEST;
}

function formdata_save($data) {
    $db = DB::getInstance();
    if ($data['PK_Menu'] == "") {
        $query = "INSERT INTO IVR_Menus() VALUES()";
        $db->query($query) or die(print_r($db->errorInfo(), true));
        $data['PK_Menu'] = $db->lastInsertId();
    }

    $query = "
		UPDATE
			IVR_Menus
		SET
			Name        = '" . $mysqli->real_escape_string($data['Name']) . "',
			Description = '" . $mysqli->real_escape_string($data['Description']) . "'
		WHERE
			PK_Menu = {$data['PK_Menu']}
		LIMIT 1
	";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    return $data['PK_Menu'];
}

function formdata_validate($data) {
    $errors = array();

    return $errors;
}

admin_run('IVR_Menus_Modify', 'Admin.tpl');
?>