<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function SoundEntries_Move() {
    global $mysqli;

    $PK_SoundEntries = $_REQUEST['PK_SoundEntries'];
    $PK_Folder = $_REQUEST['PK_Folder'];

    $query = "UPDATE SoundEntries SET FK_SoundFolder = $PK_Folder WHERE PK_SoundEntry IN ($PK_SoundEntries)";
    $mysqli->query($query) or die($mysqli->error);

    header('Location: SoundEntries_List.php?msg=MOVE_ENTRY');
    die();
}

admin_run('SoundEntries_Move');
?>