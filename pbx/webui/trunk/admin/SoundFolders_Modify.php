<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function SoundFolders_Modify() {
	session_start();
	$session = &$_SESSION['Templates_Modify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init message (Message)
	$Message = $_REQUEST['msg'];
		
	if (@$_REQUEST['submit'] == 'save') {
		$SoundFolder = formdata_from_post();
		$Errors      = formdata_validate($Rule);
		
		if (count($Errors) == 0) {
			if ($SoundFolder['PK_SoundFolder'] == '') {
				$id = formdata_save($SoundFolder);
				header("Location: SoundFolders_List.php?msg=CREATE_FOLDER&hilight={$id}"); die();
			} else {
				$id = formdata_save($SoundFolder);
				header("Location: SoundFolders_List.php?msg=MODIFY_FOLDER&hilight={$id}"); die();
			}
		}
	} elseif ($_REQUEST['PK_SoundFolder'] != "") {
		$SoundFolder = formdata_from_db($_REQUEST['PK_SoundFolder']);
	}
	
	$smarty->assign('SoundFolder'    , $SoundFolder);
	$smarty->assign('Message'        , $Message);
	
	return $smarty->fetch('SoundFolders_Modify.tpl');
}

function formdata_from_db($id) {
	$query = "SELECT * FROM SoundFolders WHERE PK_SoundFolder = $id	LIMIT 1";
	$result = mysql_query($query) or die(mysql_error());
	$data   = mysql_fetch_assoc($result);

	return $data;
}

function formdata_from_post() {
	return $_POST;
}

function formdata_save($data) {
	if (empty($data['PK_SoundFolder'])) {
		$query = "INSERT INTO SoundFolders(Type) VALUES('User')";
		mysql_query($query) or die(mysql_error().$query);
		
		$data['PK_SoundFolder'] = mysql_insert_id();
	}
	
	// Update 'SoundFolders'
	$query = "
		UPDATE
			SoundFolders
		SET
			Name        = '".mysql_real_escape_string($data['Name'])."',
			Description = '".mysql_real_escape_string($data['Description'])."'
		WHERE
			PK_SoundFolder = ".mysql_real_escape_string($data['PK_SoundFolder'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);
	
	return $data['PK_SoundFolder'];
}

function formdata_validate($data) {
	$errors = array();
	
	return $errors;
}
admin_run('SoundFolders_Modify', 'Admin.tpl');

?>
