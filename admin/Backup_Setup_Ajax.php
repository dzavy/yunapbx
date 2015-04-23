<?php

include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Backup_Setup_Ajax() {
	session_start();
	$session = &$_SESSION['Backup_Setup_Ajax'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');	
	
	$data = $_REQUEST;
	
	$response = array();
	
	switch ($data['Action']) {
		case 'RequestedSizes':
			$response['PBX_Conf']            = rand(0,1000000);
			$response['Hardware_Conf']       = rand(0,1000000);
			$response['DB_Conf']             = rand(0,1000000);
			$response['Sounds']              = rand(0,1000000);
			$response['IAX_RSA_Keys']        = rand(0,1000000);
			$response['Audio_Codecs']        = rand(0,1000000);
			$response['VM']                  = rand(0,1000000);
			$response['VMG']                 = rand(0,1000000);
			$response['RC']                  = rand(0,1000000);
			$response['MOH']                 = rand(0,1000000);
			$response['EL']                  = rand(0,1000000);		
			
			break;
	}
	echo json_encode($response);
}

admin_run('Backup_Setup_Ajax');