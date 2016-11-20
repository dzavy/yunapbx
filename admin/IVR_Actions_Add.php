<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function IVR_Actions_Add() {
    
    $session = &$_SESSION['IVR_Actions_Add'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Menu = $_REQUEST['PK_Menu'];
    $smarty->assign('PK_Menu', $PK_Menu);

    return $smarty->fetch('IVR_Actions_Add.tpl');
}

admin_run('IVR_Actions_Add', 'Admin.tpl');
?>