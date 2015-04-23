<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');


function PhoneSetup() {
	session_start();
	$session = &$_SESSION['PhoneSetup'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$Message = $_REQUEST['msg'];		
	$Phones = array();
	$Phones= formdata_from_db();	
	
	//myprint ($Phones);
	$smarty->assign('Phones', $Phones);
	
	return $smarty->fetch('PhoneSetup.tpl');
}

function formdata_from_db() {
	$data = array();
	$query  = "
				SELECT 
					*
				FROM 
					`Phones`
				ORDER BY 
					`MAC` 
				ASC
				LIMIT 0 , 30 	
		
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	
	while ($row = mysql_fetch_assoc($result)) {
		$data[] = $row;
	}
	return $data;
}

admin_run('PhoneSetup', 'Admin.tpl');