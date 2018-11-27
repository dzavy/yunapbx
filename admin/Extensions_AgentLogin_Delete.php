<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_AgentLogin_Delete() {
    $db = DB::getInstance();
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Extension = $_REQUEST['PK_Extension'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Ext_AgentLogin WHERE PK_Extension = $PK_Extension";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Extensions WHERE PK_Extension = $PK_Extension";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        header('Location: Extensions_List.php?msg=DELETE_AGENTLOGIN_EXTENSION');
        die();
    }

    // Init extension info (Extension)
    $query = "SELECT PK_Extension, Extension FROM Extensions WHERE PK_Extension = $PK_Extension";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Extension = $result->fetch(PDO::FETCH_ASSOC);

    $smarty->assign('Extension', $Extension);

    return $smarty->fetch('Extensions_AgentLogin_Delete.tpl');
}

admin_run('Extensions_AgentLogin_Delete', 'Admin.tpl');
?>