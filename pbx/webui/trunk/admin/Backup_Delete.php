<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Backup_Delete() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK = $_REQUEST['PK_Backup'];
    $Date = $_REQUEST['Date'];
    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {

        $query = "DELETE FROM Backups WHERE PK_Backup = '" . $PK . "' LIMIT 1";

        $mysqli->query($query) or die($mysqli->error() . $query);

        header('Location: Backup.php?msg=DELETE_BACKUP');
        die();
    }



    $smarty->assign('PK', $PK);
    $smarty->assign('Date', $Date);
    return $smarty->fetch('Backup_Delete.tpl');
}

admin_run('Backup_Delete', 'Admin.tpl');
