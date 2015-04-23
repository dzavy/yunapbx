<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');


function Backup_Setup() {
	session_start();
	$session = &$_SESSION['Backup_Setup'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$Message = $_REQUEST['msg'];	
	
	if ($_REQUEST['submit']) {				
		$Settings= formdata_from_post();	
		$Errors = formdata_validate($Settings);

		if (count($Errors) == 0 ) {				
			formdata_save($Settings);	
			header("Location: Backup.php?msg=BACKUP_SETTINGS_SAVED"); 
			die(); 
		}
	}	
	$Settings = formdata_from_db();		
	
	$smarty->assign('Message'  , $Message);
	$smarty->assign('Settings' , $Settings);
	$smarty->assign('Errors'   , $Errors);
	
	return $smarty->fetch('Backup_Setup.tpl');
}

function formdata_save($data){	
	pbx_var_set("Backup_Weekday"    , $data['Weekday']); 
	pbx_var_set("Backup_Hour"       , $data['Hour']); 
	pbx_var_set("Backup_NrBackups"  , $data['NrBackups']); 
	pbx_var_set("Backup_FtpHostname", $data['FtpHostname']); 
	pbx_var_set("Backup_FtpUsername", $data['FtpUsername']); 
	pbx_var_set("Backup_FtpPassword", $data['FtpPassword']); 
	pbx_var_set("Backup_FtpPath"    , $data['FtpPath']); 
	pbx_var_set("Backup_VM" , ($data['VM'] =='on'?'1':'0')); 
	pbx_var_set("Backup_VMG", ($data['VMG']=='on'?'1':'0')); 
	pbx_var_set("Backup_RC" , ($data['RC'] =='on'?'1':'0')); 
	pbx_var_set("Backup_MOH", ($data['MOH']=='on'?'1':'0')); 
	pbx_var_set("Backup_EL" , ($data['EL'] =='on'?'1':'0'));    
}

function formdata_from_db(){
	$variables = array(
		"Weekday",
		"Hour",
		"NrBackups",
		"FtpHostname",
		"FtpUsername",
		"FtpPassword",
		"FtpPath",
		"VMG",
		"RC",
		"MOH",
		"EL"	 
	 );
	
	foreach ($variables as $name) {
		$data[$name] = pbx_var_get("Backup_".$name);
	} 
	return $data; 
}

function formdata_from_post() {
	$data = $_REQUEST;
	return $data;
}
function formdata_validate($data){	
	$errors = array();
	if (($data['NrBackups']<1) || ($data['NrBackups']>99)){
		$errors['NrBackups'] = true;		
	}
	
	if (ftp_test($data['FtpHostname'], $data['FtpUsername'], $data['FtpPassword'], $data['FtpPath'])){
		$errors['FTP'] = true;		
	}
	
	return $errors;
}
admin_run('Backup_Setup', 'Admin.tpl');