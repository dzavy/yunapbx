<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');


function SystemReload() {
	session_start();
	$session = &$_SESSION['SystemReload'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$Message = $_REQUEST['msg'];
	
	
	
	
	$smarty->assign('Errors'     , $Errors);
	
	return $smarty->fetch('SystemReload.tpl');
}

admin_run('SystemReload', 'Admin.tpl');