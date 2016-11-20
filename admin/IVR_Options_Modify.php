<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function IVR_Options_Modify() {
    global $mysqli;
    
    $session = &$_SESSION['IVR_Options_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    if (@$_REQUEST['submit'] == 'save') {
        $Option = formdata_from_post();
        $Errors = formdata_validate($Action);

        if (count($Errors) == 0) {
            if ($Option['PK_Option'] != '') {
                $msg = 'MODIFY_OPTION';
            } else {
                $msg = 'ADD_OPTION';
            }
            $id = formdata_save($Option);

            header("Location: IVR_Options_List.php?PK_Menu={$Option['FK_Menu']}&hilight={$id}&msg={$msg}");
            die();
        }
    } elseif (@$_REQUEST['PK_Option'] != "") {
        $Option = formdata_from_db($_REQUEST['PK_Option']);
    } else {
        $Option = formdata_from_default();
    }

    // Get available menus
    $Menus = array();
    $query = "SELECT PK_Menu, Name FROM IVR_Menus ORDER BY Name";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $menu = $row;

        $query2 = "SELECT * FROM IVR_Actions WHERE FK_Menu = '{$menu['PK_Menu']}' ORDER BY `Order`";
        $result2 = $mysqli->query($query2) or die($mysqli->error . $query2);
        while ($row2 = $result2->fetch_assoc()) {
            $action = $row2;

            $query3 = "SELECT * FROM IVR_Action_Params WHERE FK_Action = {$action['PK_Action']}";
            $result3 = $mysqli->query($query3) or die($mysqli->error . $query3);
            while ($row3 = $result3->fetch_assoc()) {
                $action['Param'][$row3['Name']] = $row3['Value'];
                $action['Var'][$row3['Name']] = $row3['Variable'];
            }

            $menu['Actions'][] = $action;
        }

        $Menus[] = $menu;
    }

    // Get used keys
    $UsedKeys = array();
    $query = "SELECT `Key` FROM IVR_Options WHERE PK_Option != '{$Option['PK_Option']}' AND FK_Menu={$Option['FK_Menu']}";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $UsedKeys[] = $row['Key'];
    }

    $smarty->assign('Menus', $Menus);
    $smarty->assign('Option', $Option);
    $smarty->assign('UsedKeys', $UsedKeys);

    return $smarty->fetch('IVR_Options_Modify.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    $query = "SELECT * FROM IVR_Options WHERE PK_Option = '$id' LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data = $result->fetch_assoc();

    return $data;
}

function formdata_from_default() {
    $data = array();

    $data['FK_Menu'] = $_REQUEST['FK_Menu'];

    return $data;
}

function formdata_from_post() {
    return $_REQUEST;
}

function formdata_save($data) {
    global $mysqli;
    if ($data['PK_Option'] == "") {
        $query = "INSERT INTO IVR_Options (FK_Menu) VALUES({$data['FK_Menu']})";
        $mysqli->query($query) or die($mysqli->error . $query);
        $data['PK_Option'] = $mysqli->insert_id;
    }

    $query = "
		UPDATE
			IVR_Options
		SET
			`Key`           = '{$data['Key']}',
			FK_Menu         =  {$data['FK_Menu']},
			FK_Menu_Entry   =  {$data['FK_Menu_Entry']},
			FK_Action_Entry =  {$data['FK_Action_Entry']}
		WHERE
			PK_Option = {$data['PK_Option']}
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);

    return $data['PK_Option'];
}

function formdata_validate($data) {
    $errors = array();

    return $errors;
}

admin_run('IVR_Options_Modify', 'Admin.tpl');
?>