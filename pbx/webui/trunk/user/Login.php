<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/user_utils.inc.php');
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

function Login() {
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	if (isset($_REQUEST['submit'])) {	
		$data   = formdata_from_post();
		$errors = formdata_validate($data);
		
		if (count($errors) == 0) {
			user_login($data['Extension']);
			header('Location: Account_Modify.php');
			die();
		}
	}
	
	$smarty->assign('Errors', $errors);
	return $smarty->fetch('Login.tpl');
}

function formdata_from_post() {
	return $_REQUEST;
}

function formdata_validate($data) {
	$errors = array();
	
	if (! preg_match('/[0-9]{3,5}/', $data['Extension'])) {
		$errors['Extension']['Invalid'] = true;
	}
	
	// Check if there is a user with that extension
	if (count($errors) == 0) {
		$query = "
			SELECT
				* 
			FROM 
				Extensions 
			WHERE 
				Extension = '".mysql_real_escape_string($data['Extension'])."' 
				AND 
				Type IN ('SipPhone', 'Virtual', 'Agent')
			LIMIT 1
		";
		$result = mysql_query($query) or die(mysql_error().$query);
		 
		if (mysql_num_rows($result) != "1") {
			$errors['Wrong'] = true;
		} else {
			$Extension = mysql_fetch_assoc($result);
		}
	}
	
	// Check if the user's password matches
	if (count($errors) == 0) {
		switch ($Extension['Type']) {
			case 'SipPhone': $query = "SELECT Password FROM Ext_SipPhones WHERE PK_Extension={$Extension['PK_Extension']} LIMIT 1"; break;
			case 'Virtual' : $query = "SELECT Password FROM Ext_Virtual   WHERE PK_Extension={$Extension['PK_Extension']} LIMIT 1"; break;
			case 'Agent'   : $query = "SELECT Password FROM Ext_Agents    WHERE PK_Extension={$Extension['PK_Extension']} LIMIT 1"; break;
		}
		$result = mysql_query($query) or die(mysql_error().$query);
		$row    = mysql_fetch_row($result);
		
		if ($data['Password'] != $row[0]) {
			$errors['Wrong'] = true;
		}
	}
	
	// Check if the user is allowed to access the webtool
	if (count($errors) == 0) {
		switch ($Extension['Type']) {
			case 'SipPhone': $query = "SELECT * FROM Ext_SipPhones_Features WHERE FK_Extension={$Extension['PK_Extension']} AND FK_Feature = 5 LIMIT 1"; break;
			case 'Virtual' : $query = "SELECT * FROM Ext_Virtual_Features   WHERE FK_Extension={$Extension['PK_Extension']} AND FK_Feature = 5 LIMIT 1"; break;
			case 'Agent'   : $query = "SELECT * FROM Agents WHERE PK_Extension={$Extension['PK_Extension']} AND WebAccess = 1 LIMIT 1"; break;
		}
		
		$result = mysql_query($query) or die(mysql_error().$query);
		if (mysql_num_rows($result) != "1") {
			$errors['Wrong'] = true;
		}
	}
	
	return $errors;
}

user_run('Login', 'User_NoMenu.tpl', false);