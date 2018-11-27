<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function SoundFiles_Download() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['SoundFilesAjax'];

    $PK_SoundFile = $_REQUEST['PK_SoundFile'];

    $query = "SELECT Filename FROM SoundFiles WHERE PK_SoundFile = $PK_SoundFile LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $Filename = $row['Filename'];

    if (file_exists($Filename)) {
        header("Content-type: " . mime_content_type($Filename));
        header("Content-Disposition: attachment; filename=\"" . basename($Filename) . "\"");
        echo file_get_contents($Filename);
    } else {
        echo "File not found.";
    }
    die();
}

admin_run('SoundFiles_Download');
?>