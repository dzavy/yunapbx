<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

function Login() {
	
	$smarty  = smarty_init(dirname(__FILE__).'/templates');
	if (isset($_REQUEST['submit'])) {	
		$data   = formdata_from_post();
		$errors = formdata_validate($data);		
		
		if (count($errors) == 0) {		
			header('Location:index.php');
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
	
	$enc_pw = md5($data['Password']);
	// Check if there is a user with that extension
	if (count($errors) == 0) {
		$query = "
			SELECT
				*
			FROM 
				Admins
			WHERE 
				name = '".mysql_real_escape_string($data['User'])."' 
				AND 
				password = '".mysql_real_escape_string($enc_pw)."' 
			LIMIT 1
		";
		
		$result = mysql_query($query) or die(mysql_error().$query);
		 
		if (mysql_num_rows($result) != 1) {
			$errors['Wrong'] = true;
		} else {			
			$_SESSION["_USER"] = $data['User'];
		}
	}
	return $errors;
}

admin_run('Login', 'User_NoMenu.tpl', false);
