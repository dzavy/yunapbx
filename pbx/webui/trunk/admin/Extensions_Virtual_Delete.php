<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

function Extensions_Virtual_Delete() {
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$PK_Extension = $_REQUEST['PK_Extension'];
	if ($PK_Extension == "") {
		$PK_Extension = $_REQUEST['PK'];
	}

	// In confirmed, do the actual delete
	if (@$_REQUEST['submit'] == 'delete_confirm') {
		$query = "DELETE FROM Extensions WHERE PK_Extension = $PK_Extension LIMIT 1";
		mysql_query($query) or die(mysql_error());

		$query = "DELETE FROM Ext_Virtual WHERE PK_Extension = $PK_Extension LIMIT 1";
		mysql_query($query) or die(mysql_error());

		if (mysql_affected_rows() != 1) {
			return ;
		}

		$query = "DELETE FROM Extension_Groups WHERE FK_Extension = $PK_Extension";
		mysql_query($query) or die(mysql_error());

		$query = "DELETE FROM Ext_Virtual_Features WHERE FK_Extension = $PK_Extension";
		mysql_query($query) or die(mysql_error());

		$query = "DELETE FROM Extension_Rules WHERE FK_Extension = $PK_Extension";
		mysql_query($query) or die(mysql_error());

		asterisk_UpdateConf('sip.conf');
		asterisk_UpdateConf('voicemail.conf');
		asterisk_Reload();

		header('Location: Extensions_List.php?msg=DELETE_VIRTUAL_EXTENSION');
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
	$result = mysql_query($query) or die(mysql_error());
	$Extension = mysql_fetch_assoc($result);

	$smarty->assign('Extension' , $Extension);

	return $smarty->fetch('Extensions_Virtual_Delete.tpl');
}

admin_run('Extensions_Virtual_Delete', 'Admin.tpl');
?>