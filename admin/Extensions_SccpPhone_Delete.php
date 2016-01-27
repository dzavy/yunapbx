<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Extensions_SccpPhone_Delete() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Extension = $_REQUEST['PK_Extension'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Extensions WHERE PK_Extension = $PK_Extension LIMIT 1";
        $mysqli->query($query) or die($mysqli->error);

        $query = "DELETE FROM Ext_SipPhones WHERE PK_Extension = $PK_Extension LIMIT 1";
        $mysqli->query($query) or die($mysqli->error);

        if ($mysqli->affected_rows != 1) {
            return;
        }

        $query = "DELETE FROM Ext_SipPhones_Codecs WHERE FK_Extension = $PK_Extension";
        $mysqli->query($query) or die($mysqli->error);

        $query = "DELETE FROM Extension_Groups WHERE FK_Extension = $PK_Extension";
        $mysqli->query($query) or die($mysqli->error);

        $query = "DELETE FROM Ext_SipPhones_Features WHERE FK_Extension = $PK_Extension";
        $mysqli->query($query) or die($mysqli->error);

        $query = "DELETE FROM Extension_Rules WHERE FK_Extension = $PK_Extension";
        $mysqli->query($query) or die($mysqli->error);

        asterisk_UpdateConf('sip.conf');
        asterisk_UpdateConf('voicemail.conf');
        asterisk_Reload();

        header('Location: Extensions_List.php?msg=DELETE_SIPPHONE_EXTENSION');
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
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $Extension = $result->fetch_assoc();

    $smarty->assign('Extension', $Extension);

    return $smarty->fetch('Extensions_SccpPhone_Delete.tpl');
}

admin_run('Extensions_SccpPhone_Delete', 'Admin.tpl');
?>
