<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function TimeFrames_Delete() {
	$smarty  = smarty_init(dirname(__FILE__).'/templates');
	
	$PK_Timeframe = $_REQUEST['PK_Timeframe'];
	
	// In confirmed, do the actual delete
	if (@$_REQUEST['submit'] == 'delete_confirm') {
		$query = "DELETE FROM Timeframes WHERE PK_Timeframe = $PK_Timeframe LIMIT 1";
		mysql_query($query) or die(mysql_error());
				
		$query = "DELETE FROM Timeframe_Intervals WHERE FK_Timeframe = $PK_Timeframe";
		mysql_query($query) or die(mysql_error());
				
		header('Location: TimeFrames.php?msg=DELETE_TIMEFRAME');
		die();
	}
	
	// Init template info (Template)
	$query  = "SELECT * FROM Timeframes WHERE PK_Timeframe = $PK_Timeframe LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	$Timeframe = mysql_fetch_assoc($result);
	
	$smarty->assign('Timeframe' , $Timeframe);
	
	return $smarty->fetch('TimeFrames_Delete.tpl');
}

admin_run('TimeFrames_Delete', 'Admin.tpl');
?>