<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Extensions_FC_Intercom_Delete() {
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$PK_Extension = $_REQUEST['PK_Extension'];

	// In confirmed, do the actual delete
	if (@$_REQUEST['submit'] == 'delete_confirm') {
		$query = "DELETE FROM FC_Intercom WHERE FK_Extension = $PK_Extension LIMIT 1";
		mysql_query($query) or die(mysql_error());

		$query = "DELETE FROM FC_Intercom_Admins WHERE FK_Extension = $PK_Extension LIMIT 1";
		mysql_query($query) or die(mysql_error());

		$query = "DELETE FROM FC_Intercom_Members WHERE FK_Extension = $PK_Extension LIMIT 1";
		mysql_query($query) or die(mysql_error());

		$query = "DELETE FROM Extensions WHERE PK_Extension = $PK_Extension LIMIT 1";
		mysql_query($query) or die(mysql_error());

		header('Location: Extensions_List.php?msg=DELETE_FC_INTERCOM_EXTENSION');
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
	$result = mysql_query($query) or die(mysql_error().$query);
	$Extension = mysql_fetch_assoc($result);

	$smarty->assign('Extension' , $Extension);

	return $smarty->fetch('Extensions_FC_Intercom_Delete.tpl');
}

admin_run('Extensions_FC_Intercom_Delete', 'Admin.tpl');
?>