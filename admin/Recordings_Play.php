<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Recordings_Play() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['Recordings_Play'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $ID = $mysqli->real_escape_string($_REQUEST['ID']);

    $query = "SELECT * FROM RecordingLog WHERE FK_CallLog = '{$ID}' LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $File = $result->fetch(PDO::FETCH_ASSOC);

    $File['Filename'] = $GLOBALS['config']['monitor_dir'] . "{$File['PK_CallLog']}.wav";

    $smarty->assign('File', $File);

    return $smarty->fetch('Recordings_Play.tpl');
}

admin_run('Recordings_Play', 'AdminPopup.tpl');
?>