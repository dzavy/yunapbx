<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/config.inc.php');

function Recordings_Download() {
    global $mysqli;
    
    $session = &$_SESSION['Recordings_Download'];

    $ID = $mysqli->real_escape_string($_REQUEST['ID']);

    $query = "SELECT * FROM RecordingLog WHERE FK_CallLog = '{$ID}' LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $File = $result->fetch_assoc();

//	system("ffmpeg -f wav -i {$GLOBALS['config']['monitor_dir']}/{$File['PK_CallLog']}.wav {$GLOBALS['config']['monitor_dir']}/{$File['PK_CallLog']}.flv");
//	$Filename = $GLOBALS['config']['monitor_dir']."/{$File['PK_CallLog']}.flv";
    $Filename = $GLOBALS['config']['monitor_dir'] . "/{$File['FK_CallLog']}.wav";
    if (file_exists($Filename)) {
        header("Content-type: " . mime_content_type($Filename));
        header("Content-Disposition: attachment; filename=\"" . basename($Filename) . "\"");
        $handle = fopen($Filename, 'r');
        while (!feof($handle)) {
            echo fread($handle, 8192);
        }
        fclose($handle);
    } else {
        echo "File not found.";
    }
    die();
}

admin_run('Recordings_Download');
?>