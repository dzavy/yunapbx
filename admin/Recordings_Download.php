<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Recordings_Download() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['Recordings_Download'];

    $ID = $mysqli->real_escape_string($_REQUEST['ID']);

    $query = "SELECT * FROM RecordingLog WHERE FK_CallLog = '{$ID}' LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $File = $result->fetch(PDO::FETCH_ASSOC);

    $Filename = $GLOBALS['config']['monitor_dir'] . "/{$File['FK_CallLog']}.mp3";
    if (file_exists($Filename)) {
        header("Content-type: audio/mpeg");
        header("Content-Disposition: attachment; filename=\"" . basename($Filename) . "\"");
        echo file_get_contents($Filename);
    } else {
        echo "File not found.";
    }
    die();
}

admin_run('Recordings_Download');
?>