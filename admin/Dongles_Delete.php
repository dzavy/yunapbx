<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Dongles_Delete() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Dongle = $_REQUEST['PK_Dongle'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Dongles WHERE PK_Dongle = $PK_Dongle LIMIT 1";
        $mysqli->query($query) or die($mysqli->error . $query);

        if ($mysqli->affected_rows != 1) {
            return;
        }
        
        $query = "DELETE FROM Dongle_Rules WHERE FK_Dongle = $PK_Dongle";
        $mysqli->query($query) or die($mysqli->error . $query);

        $query = "DELETE FROM Dongle_Statuses WHERE FK_Dongle = $PK_Dongle";
        $mysqli->query($query) or die($mysqli->error . $query);

        asterisk_UpdateConf('dongle.conf');
        asterisk_UpdateConf('extensions.conf');
        asterisk_Reload();

        header('Location: Dongles_List.php?msg=DELETE_DONGLE');
        die();
    }

    // Init extension info (Extension)
    $query = "
		SELECT
			PK_Dongle,
			Name
		FROM
			Dongles
		WHERE
			PK_Dongle = $PK_Dongle
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error);
    $Dongle = $result->fetch_assoc();

    $smarty->assign('Dongle', $Dongle);

    return $smarty->fetch('Dongles_Delete.tpl');
}

admin_run('Dongles_Delete', 'Admin.tpl');
?>
