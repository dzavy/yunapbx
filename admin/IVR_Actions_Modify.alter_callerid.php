<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_Action_Modify() {
    global $mysqli;
    
    $session = &$_SESSION['IVR_Action_Modify_alter_callerid'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    if (@$_REQUEST['submit'] == 'save') {
        $Action = formdata_from_post();
        $Errors = formdata_validate($Action);

        if (count($Errors) == 0) {
            $id = formdata_save($Action);
            header("Location: IVR_Actions.php?PK_Menu={$Action['FK_Menu']}&hilight={$id}");
            die();
        }
    } elseif (@$_REQUEST['PK_Action'] != "") {
        $Action = formdata_from_db($_REQUEST['PK_Action']);
    } else {
        $Action = formdata_from_default();
    }

    // Get available vars
    $query = "SELECT DISTINCT(Variable) FROM IVR_Action_Params ORDER BY Variable";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_row()) {
        if (!empty($row[0])) {
            $Variables[] = $row[0];
        }
    }

    $smarty->assign('Action', $Action);
    $smarty->assign('Variables', $Variables);

    return $smarty->fetch('IVR_Actions_Modify.alter_callerid.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    $query = "SELECT * FROM IVR_Actions WHERE PK_Action = '$id' LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data = $result->fetch_assoc();

    $query = "SELECT * FROM IVR_Action_Params WHERE FK_Action = '$id'";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $data['Param'][$row['Name']] = $row['Value'];
        $data['Var'][$row['Name']] = $row['Variable'];
    }

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
    if ($data['PK_Action'] == "") {
        $query = "SELECT COUNT(*) FROM IVR_Actions WHERE FK_Menu={$data['FK_Menu']}";
        $result = $mysqli->query($query) or die($mysqli->error . $query);
        $row = $result->fetch_row();
        $data['Order'] = $row[0] + 1;

        $query = "INSERT INTO IVR_Actions (FK_Menu, `Order`, Type) VALUES({$data['FK_Menu']}, {$data['Order']}, 'alter_callerid')";
        $mysqli->query($query) or die($mysqli->error . $query);
        $data['PK_Action'] = $mysqli->insert_id;
    }

    $query = "DELETE FROM IVR_Action_Params WHERE FK_Action = {$data['PK_Action']}";
    $mysqli->query($query) or die($mysqli->error . $query);

    if (is_array($data['Param'])) {
        foreach ($data['Param'] as $Name => $Value) {
            if (empty($data['Param'][$Name])) {
                continue;
            }
            $query = "
				INSERT INTO
					IVR_Action_Params
				SET
					`Name`      = '" . $mysqli->real_escape_string($Name) . "',
					`Value`     = '" . $mysqli->real_escape_string($Value) . "',
					`FK_Action` = {$data['PK_Action']}
			";
            $mysqli->query($query) or die($mysqli->error . $query);
        }
    }

    if (is_array($data['Var'])) {
        foreach ($data['Var'] as $Name => $Value) {
            if (!empty($data['Param'][$Name])) {
                continue;
            }

            $query = "
				INSERT INTO
					IVR_Action_Params
				SET
					`Name`      = '" . $mysqli->real_escape_string($Name) . "',
					`Variable`  = '" . $mysqli->real_escape_string($Value) . "',
					`FK_Action` = {$data['PK_Action']}
			";
            $mysqli->query($query) or die($mysqli->error . $query);
        }
    }

    return $data['PK_Action'];
}

function formdata_validate($data) {
    $errors = array();

    return $errors;
}

admin_run('Extensions_Action_Modify', 'Admin.tpl');
?>