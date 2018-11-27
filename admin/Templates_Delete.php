<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Templates_Delete() {
    $db = DB::getInstance();
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Template = $_REQUEST['PK_Template'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Template_Codecs WHERE FK_Template = $PK_Template";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Template_Groups WHERE FK_Template = $PK_Template";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Template_Features WHERE FK_Template = $PK_Template";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Templates WHERE PK_Template = $PK_Template AND Protected = 0 LIMIT 1";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        header('Location: Templates_List.php?msg=DELETE_TEMPLATE');
        die();
    }

    // Init template info (Template)
    $query = "SELECT PK_Template, Name FROM Templates WHERE	PK_Template = $PK_Template LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Template = $result->fetch(PDO::FETCH_ASSOC);

    $smarty->assign('Template', $Template);

    return $smarty->fetch('Templates_Delete.tpl');
}

admin_run('Templates_Delete', 'Admin.tpl');
?>