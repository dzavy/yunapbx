<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Extensions_FC_Voicemail_Modify() {
	session_start();
	$session = &$_SESSION['Extensions_SimpleConf_Modify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init message (Message)
	$Message = $_REQUEST['msg'];

	if (@$_REQUEST['submit'] == 'save') {
		$Extension = formdata_from_post();
		$Errors    = formdata_validate($Extension);

		if (count($Errors) == 0) {
			if ($Extension['PK_Extension'] != '') {
				$msg = 'MODIFY_FC_VOICEMAIL_EXTENSION';
			} else {
				$msg = 'ADD_FC_VOICEMAIL_EXTENSION';
			}
			$id = formdata_save($Extension);

			header("Location: Extensions_List.php?hilight={$id}&msg={$msg}");
			die();
		}

	} elseif (@$_REQUEST['PK_Extension'] != "") {
		$Extension = formdata_from_db($_REQUEST['PK_Extension']);

	} else {
		$Extension = formdata_from_default();
	}

	$smarty->assign('FC_Voicemail', $Extension);
	$smarty->assign('Errors'   , $Errors);

	return $smarty->fetch('Extensions_FC_Voicemail_Modify.tpl');
}

function formdata_from_db($id) {
	$query  = "
		SELECT
			*
		FROM
			FC_Voicemail
			INNER JOIN Extensions ON Extensions.PK_Extension = FC_Voicemail.FK_Extension
		WHERE
			FC_Voicemail.FK_Extension = '$id'
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data   = mysql_fetch_assoc($result);

	return $data;
}

function formdata_from_default() {
	$data = array();
	return $data;
}

function formdata_from_post() {
	return $_REQUEST;
}

function formdata_save($data) {
	if ($data['PK_Extension'] == "") {
		$query =   "INSERT INTO
						Extensions(Feature, Type, Extension)
					VALUES
						(1, 'FC_Voicemail', '".mysql_real_escape_string($data['Extension'])."')";
		mysql_query($query) or die(mysql_error().$query);
		$data['PK_Extension'] = mysql_insert_id();

		$query = "INSERT INTO FC_Voicemail(FK_Extension) VALUES({$data['PK_Extension']})";
		mysql_query($query) or die(mysql_error().$query);
	}

	// Update 'Ext_FC_Voicemail'
	/*$query = "
		UPDATE
			FC_Voicemail
		SET
			Password     = '".mysql_real_escape_string($data['Password'])."'
		WHERE
			PK_Extension = ".mysql_real_escape_string($data['PK_Extension'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);*/

	// Update 'IVRDial"
	$query = "UPDATE
				Extensions
			  SET
				IVRDial = ".($data['IVRDial']==1?'1':'0')." WHERE PK_Extension = {$data['PK_Extension']}";
	mysql_query($query) or die(mysql_error().$query);

	return $data['PK_Extension'];
}

function formdata_validate($data) {
	$errors = array();

	if ($data['PK_Extension'] == '') {
		$create_new = true;
	}

	if ($create_new) {
		// Check if extension is empty
		if ($data['Extension'] == "") {
			$errors['Extension']['Invalid'] = true;
		// Check if Extension is numeric
		} elseif (intval($data['Extension'])."" != $data['Extension']) {
			$errors['Extension']['Invalid'] = true;
		// Check if extension is proper length
		} elseif (strlen($data['Extension']) < 1 || strlen($data['Extension']) > 2) {
			$errors['Extension']['Invalid'] = true;
		// Check if extension in unique
		} else {
			$query  = "SELECT Extension FROM Extensions WHERE Extension = '{$data['Extension']}' LIMIT 1";
			$result = mysql_query($query) or die(mysql_error().$query);
			if (mysql_num_rows($result) > 0) {
				$errors['Extension']['Duplicate'] = true;
			}
		}
	}


	return $errors;
}

admin_run('Extensions_FC_Voicemail_Modify', 'Admin.tpl');
