<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/config.inc.php');
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

function MOH_Groups_Delete() {
	include(dirname(__FILE__).'/../include/config.inc.php');
	$smarty  = smarty_init(dirname(__FILE__).'/templates');
	$path = $conf['dirs']['moh'];

	$PK_Group = $_REQUEST['PK_Group'];

	// In confirmed, do the actual delete
	if (@$_REQUEST['submit'] == 'delete_confirm') {

		//delete files from database
		$query = "DELETE FROM Moh_Files  WHERE FK_Group = $PK_Group";
		mysql_query($query) or die(mysql_error().$query);

		//delete files from hdd
		$handle = @opendir($path."/group_".str_pad($PK_Group,10,"0", STR_PAD_LEFT)."/");
		if ($handle) {
			while (false !== ($file = readdir($handle)))     {
				if ($file != "." && $file != "..")  {
					@unlink($path."/group_".str_pad($PK_Group,10,"0", STR_PAD_LEFT)."/".$file);
				}
			}
			closedir($handle);
			@rmdir($path."/group_".str_pad($PK_Group,10,"0", STR_PAD_LEFT));
		}

		//delete directories from database
		$query = "DELETE FROM Moh_Groups WHERE PK_Group = '$PK_Group'";
		mysql_query($query) or die(mysql_error().$query);

		asterisk_UpdateConf('musiconhold.conf');
		asterisk_Reload();

		header('Location: MOH_Groups_List.php');
		die();
	}

	$query  = "SELECT * FROM Moh_Groups WHERE PK_Group =  $PK_Group;";
	$result = mysql_query($query) or die(mysql_error());
	$Group  = mysql_fetch_assoc($result);

	$smarty->assign('Group'   , $Group);

	return $smarty->fetch('MOH_Groups_Delete.tpl');
}
admin_run('MOH_Groups_Delete', 'Admin.tpl');
