<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Recordings_DeleteRule() {
    $db = DB::getInstance();
    global $conf;
    
    $smarty = smarty_init(dirname(__FILE__) . '/templates');
    $path = $conf['dirs']['moh'];

    $PK_Rule = $_REQUEST['PK_Rule'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {

        //delete files from database
        $query = "DELETE FROM RecordingRules WHERE PK_Rule = $PK_Rule";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM RecordingRules_Extensions WHERE FK_Rule = $PK_Rule";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM RecordingRules_Groups WHERE FK_Rule = $PK_Rule";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        asterisk_UpdateConf('musiconhold.conf');
        asterisk_Reload();

        header('Location: Recordings_List.php?msg=DELETE_REC_RULE');

    } else {

        $query = "SELECT PK_Rule, Label FROM RecordingRules WHERE PK_Rule =  $PK_Rule;";
        $result = $db->query($query) or die(print_r($db->errorInfo(), true));
        $RecordingRule = $result->fetch(PDO::FETCH_ASSOC);

        $smarty->assign('RecordingRule', $RecordingRule);

        return $smarty->fetch('Recordings_DeleteRule.tpl');
    }
}

admin_run('Recordings_DeleteRule', 'Admin.tpl');
