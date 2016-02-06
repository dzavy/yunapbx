<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/config.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Recordings_DeleteRule() {
    global $mysqli;
    include(dirname(__FILE__) . '/../include/config.inc.php');
    $smarty = smarty_init(dirname(__FILE__) . '/templates');
    $path = $conf['dirs']['moh'];

    $PK_Rule = $_REQUEST['PK_Rule'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {

        //delete files from database
        $query = "DELETE FROM RecordingRules WHERE PK_Rule = $PK_Rule";
        $mysqli->query($query) or die($mysqli->error . $query);

        $query = "DELETE FROM RecordingRules_Extensions WHERE FK_Rule = $PK_Rule";
        $mysqli->query($query) or die($mysqli->error . $query);

        $query = "DELETE FROM RecordingRules_Groups WHERE FK_Rule = $PK_Rule";
        $mysqli->query($query) or die($mysqli->error . $query);

        asterisk_UpdateConf('musiconhold.conf');
        asterisk_Reload();

        header('Location: Recordings_List.php?msg=DELETE_REC_RULE');

    } else {

        $query = "SELECT * FROM RecordingRules WHERE PK_Rule =  $PK_Rule;";
        $result = $mysqli->query($query) or die($mysqli->error);
        $RecordingRule = $result->fetch_assoc();

        $smarty->assign('RecordingRule', $RecordingRule);

        return $smarty->fetch('Recordings_DeleteRule.tpl');
    }
}

admin_run('Recordings_DeleteRule', 'Admin.tpl');
