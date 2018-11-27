<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Extensions_Virtual_Delete() {
    $db = DB::getInstance();
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Extension = $_REQUEST['PK_Extension'];
    if ($PK_Extension == "") {
        $PK_Extension = $_REQUEST['PK'];
    }

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Extension_Groups WHERE FK_Extension = $PK_Extension";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Extension_Rules WHERE FK_Extension = $PK_Extension";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Ext_Virtual WHERE PK_Extension = $PK_Extension LIMIT 1";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Extensions WHERE PK_Extension = $PK_Extension LIMIT 1";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        asterisk_UpdateConf('extensions.conf');
        asterisk_Reload();

        header('Location: Extensions_List.php?msg=DELETE_VIRTUAL_EXTENSION');
        die();
    }

    // Init extension info (Extension)
    $query = "SELECT PK_Extension, Extension FROM Extensions WHERE PK_Extension = $PK_Extension	LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Extension = $result->fetch(PDO::FETCH_ASSOC);

    $smarty->assign('Extension', $Extension);

    return $smarty->fetch('Extensions_Virtual_Delete.tpl');
}

admin_run('Extensions_Virtual_Delete', 'Admin.tpl');
?>