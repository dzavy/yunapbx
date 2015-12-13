<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Backup_Manual() {
    
    $session = &$_SESSION['Backup_Manual'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");
    if ($_REQUEST['submit']) {
        $Settings = formdata_from_post();
        $Errors = formdata_validate($Settings);

        if (count($Errors) == 0) {
            formdata_save($Settings);
            header("Location: Backup.php?msg=SUCCESSFULLY_CREATE_BACKUP");
            die();
        }
    }

    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('Backup_Manual.tpl');
}

function formdata_save($data) {
    global $mysqli;

    $VM = ($data['VM'] == 'on' ? 'VM' : '0');
    $VMG = ($data['VMG'] == 'on' ? 'VMG' : '0');
    $RC = ($data['RC'] == 'on' ? 'RC' : '0');
    $MOH = ($data['MOH'] == 'on' ? 'MOH' : '0');
    $EL = ($data['EL'] == 'on' ? 'EL' : '0');

    $Optionals = array();
    $i = 0;
    if ($VM) {
        $Optionals[$i] = $VM;
        $i++;
    }
    if ($VMG) {
        $Optionals[$i] = $VMG;
        $i++;
    }
    if ($RC) {
        $Optionals[$i] = $RC;
        $i++;
    }
    if ($MOH) {
        $Optionals[$i] = $MOH;
        $i++;
    }
    if ($EL) {
        $Optionals[$i] = $EL;
    }

    $Optionals = implode(",", $Optionals);

    $Size = $data['total'];

    $query = "	INSERT INTO `Backups` (
						`PK_Backup` ,
						`Optionals` ,
						`Size` ,
						`Date`
					)
					VALUES (
						NULL , 
						'" . $mysqli->real_escape_string($Optionals) . "', 
						'" . $mysqli->real_escape_string($Size) . "',
					    CURRENT_TIMESTAMP)";

    $mysqli->query($query) or die($mysqli->error . $query);
}

function formdata_from_post() {
    $data = $_REQUEST;
    return $data;
}

function formdata_validate($data) {
    $errors = array();
    return $errors;
}

admin_run('Backup_Manual', 'Admin.tpl');
