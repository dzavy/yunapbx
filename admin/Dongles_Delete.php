<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Dongles_Delete() {
    $db = DB::getInstance();
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Dongle = $_REQUEST['PK_Dongle'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Dongle_Rules WHERE FK_Dongle = $PK_Dongle";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Dongle_Status WHERE FK_Dongle = $PK_Dongle";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Dongles WHERE PK_Dongle = $PK_Dongle";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        asterisk_UpdateConf('dongle.conf');
        asterisk_UpdateConf('extensions.conf');
        asterisk_Reload();

        header('Location: Dongles_List.php?msg=DELETE_DONGLE');
    } else {

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
        $result = $db->query($query) or die(print_r($db->errorInfo(), true));
        $Dongle = $result->fetch(PDO::FETCH_ASSOC);

        $smarty->assign('Dongle', $Dongle);

        return $smarty->fetch('Dongles_Delete.tpl');
    }
}

admin_run('Dongles_Delete', 'Admin.tpl');
?>
