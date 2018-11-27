<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Extensions_Agent_Delete() {
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

        $query = "DELETE FROM Ext_Agent_Features WHERE FK_Extension = $PK_Extension";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Extension_Rules WHERE FK_Extension = $PK_Extension";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Ext_Agent WHERE PK_Extension = $PK_Extension";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Extensions WHERE PK_Extension = $PK_Extension";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        asterisk_UpdateConf('sip.conf');
        asterisk_UpdateConf('voicemail.conf');
        asterisk_Reload();

        header('Location: Extensions_List.php?msg=DELETE_AGENT_EXTENSION');
        die();
    }

    // Init extension info (Extension)
    $query = "SELECT PK_Extension, Extension FROM Extensions WHERE PK_Extension = $PK_Extension";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Extension = $result->fetch(PDO::FETCH_ASSOC);

    $smarty->assign('Extension', $Extension);

    return $smarty->fetch('Extensions_Agent_Delete.tpl');
}

admin_run('Extensions_Agent_Delete', 'Admin.tpl');
?>