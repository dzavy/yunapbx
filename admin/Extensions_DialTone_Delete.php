<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_DialTone_Delete() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Extension = $_REQUEST['PK_Extension'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Ext_DialTone WHERE PK_Extension = $PK_Extension LIMIT 1";
        $mysqli->query($query) or die($mysqli->error());

        $query = "DELETE FROM Extensions WHERE PK_Extension = $PK_Extension LIMIT 1";
        $mysqli->query($query) or die($mysqli->error());

        header('Location: Extensions_List.php?msg=DELETE_DIALTONE_EXTENSION');
        die();
    }

    // Init extension info (Extension)
    $query = "
		SELECT
			PK_Extension,
			Extension
		FROM
			Extensions
		WHERE
			PK_Extension = $PK_Extension
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    $Extension = $result->fetch_assoc();

    $smarty->assign('Extension', $Extension);

    return $smarty->fetch('Extensions_DialTone_Delete.tpl');
}

admin_run('Extensions_DialTone_Delete', 'Admin.tpl');
?>