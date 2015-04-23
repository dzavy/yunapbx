<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

function SoundFolders_Delete() {
	$smarty  = smarty_init(dirname(__FILE__).'/templates');
	
	$PK_SoundFolder = $_REQUEST['PK_SoundFolder'];
	if ($PK_SoundFolder == "") {
		$PK_SoundFolder = $_REQUEST['PK'];	
	}
	
	// In confirmed, do the actual delete
	if (@$_REQUEST['submit'] == 'delete_confirm') {
		$query = "DELETE FROM SoundFolders WHERE PK_SoundFolder = $PK_SoundFolder LIMIT 1";
		mysql_query($query) or die(mysql_error());
		
		if (mysql_affected_rows() != 1) {
			return ;
		}

		header('Location: SoundFolders_List.php?msg=DELETE_FOLDER');
		die();
	}
	
	// Init extension info (Extension)
	$query = "SELECT * FROM SoundFolders WHERE PK_SoundFolder = $PK_SoundFolder LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	$SoundFolder = mysql_fetch_assoc($result);
	
	$smarty->assign('SoundFolder' , $SoundFolder);
	
	return $smarty->fetch('SoundFolders_Delete.tpl');
}

admin_run('SoundFolders_Delete', 'Admin.tpl');
?>