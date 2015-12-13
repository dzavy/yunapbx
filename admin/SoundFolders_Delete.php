<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function SoundFolders_Delete() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_SoundFolder = $_REQUEST['PK_SoundFolder'];
    if ($PK_SoundFolder == "") {
        $PK_SoundFolder = $_REQUEST['PK'];
    }

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM SoundFolders WHERE PK_SoundFolder = $PK_SoundFolder LIMIT 1";
        $mysqli->query($query) or die($mysqli->error);

        if ($mysqli->affected_rows() != 1) {
            return;
        }

        header('Location: SoundFolders_List.php?msg=DELETE_FOLDER');
        die();
    }

    // Init extension info (Extension)
    $query = "SELECT * FROM SoundFolders WHERE PK_SoundFolder = $PK_SoundFolder LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error);
    $SoundFolder = $result->fetch_assoc();

    $smarty->assign('SoundFolder', $SoundFolder);

    return $smarty->fetch('SoundFolders_Delete.tpl');
}

admin_run('SoundFolders_Delete', 'Admin.tpl');
?>