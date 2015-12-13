<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Extensions_Queue_Delete() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Extension = $_REQUEST['PK_Extension'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Extensions WHERE PK_Extension = $PK_Extension LIMIT 1";
        $mysqli->query($query) or die($mysqli->error);

        $query = "DELETE FROM Ext_Queues WHERE PK_Extension = $PK_Extension LIMIT 1";
        $mysqli->query($query) or die($mysqli->error);

        $query = "DELETE FROM Ext_Queue_Members WHERE FK_Extension = $PK_Extension";
        $mysqli->query($query) or die($mysqli->error);

        asterisk_UpdateConf('queues.conf');
        asterisk_Reload();

        header('Location: Extensions_List.php?msg=DELETE_QUEUE_EXTENSION');
        die();
    }

    // Init extension info (Extension)
    $query = "
		SELECT
			PK_Extension,
			Name
		FROM
			Ext_Queues
		WHERE
			PK_Extension = $PK_Extension
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error);
    $Queue = $result->fetch_assoc();

    $smarty->assign('Queue', $Queue);

    return $smarty->fetch('Extensions_Queue_Delete.tpl');
}

admin_run('Extensions_Queue_Delete', 'Admin.tpl');
?>