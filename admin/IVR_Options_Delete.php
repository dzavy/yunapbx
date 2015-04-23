<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function IVR_Options_Delete() {
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$PK_Option = $_REQUEST['PK_Option'];
	$PK_Menu   = $_REQUEST['PK_Menu'];

	// In confirmed, do the actual delete
	if (@$_REQUEST['submit'] == 'delete_confirm') {
		$query = "DELETE FROM IVR_Options WHERE PK_Option = $PK_Option LIMIT 1";
		mysql_query($query) or die(mysql_error());

		header('Location: IVR_Options_List.php?PK_Menu='.$PK_Menu.'&msg=DELETE_OPTION');
		die();
	}

	// Init template info (Group)
	$query = "SELECT * FROM IVR_Options WHERE PK_Option = $PK_Option LIMIT 1 ";
	$result = mysql_query($query) or die(mysql_error().$query);
	$Option = mysql_fetch_assoc($result);

	$smarty->assign('Option' , $Option);

	return $smarty->fetch('IVR_Options_Delete.tpl');

}

admin_run('IVR_Options_Delete', 'Admin.tpl');
?>