<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function SoundFiles_Delete() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['SoundFilesDelete'];

    $PK_SoundFile = $_REQUEST['PK_SoundFile'];

    // Detect Filename and PK_SoundEntry
    $query = "SELECT FK_SoundEntry, Filename FROM SoundFiles WHERE PK_SoundFile = $PK_SoundFile LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $Filename = $row['Filename'];
    $PK_SoundEntry = $row['FK_SoundEntry'];

    // Delete SoundFile from disk
    @unlink($row['Filename']);

    // Delete SoundFile from db
    $query = "DELETE FROM SoundFiles WHERE PK_SoundFile = $PK_SoundFile LIMIT 1";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    // See if the SoundEntry is orphat now
    $query = "SELECT COUNT(*) FROM SoundFiles WHERE FK_SoundEntry = $PK_SoundEntry";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $row = $result->fetch_row();
    $SoundFilesLeft = $row[0];

    if ($SoundFilesLeft == 0) {
        $query = "DELETE FROM SoundEntries WHERE PK_SoundEntry = $PK_SoundEntry LIMIT 1";
        $db->query($query) or die(print_r($db->errorInfo(), true));
    }

    header('Location: SoundEntries_List.php?msg=DELETE_ENTRY&hilight=' . $PK_SoundEntry);

    die();
}

admin_run('SoundFiles_Delete');
?>