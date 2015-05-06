<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

function Extensions_Virtual_Modify() {
	session_start();
	$session = &$_SESSION['Extensions_Virtual_Modify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init message (Message)
	$Message = $_REQUEST['msg'];

	// Init available extension groups (Groups)
	$query  = "SELECT PK_Group, Name FROM Groups";
	$result = mysql_query($query) or die(mysql_errno());
	$Groups = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Groups[] = $row;
	}

	// Init available outgoing rules (Rules)
	$query  = "SELECT * FROM OutgoingRules ORDER BY Name";
	$result = mysql_query($query) or die(mysql_errno());
	$Rules = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Rules[] = $row;
	}

	// Init available extension groups (Features)
	$query  = "SELECT PK_Feature, Name FROM Features";
	$result = mysql_query($query) or die(mysql_errno());
	$Features = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Features[] = $row;
	}

	// Init form data (Extension)
	if (@$_REQUEST['submit'] == 'save') {
		$Extension = formdata_from_post();
		$Errors    = formdata_validate($Extension);

		if (count($Errors) == 0) {
			$id = formdata_save($Extension);
			asterisk_UpdateConf('sip.conf');
			asterisk_UpdateConf('voicemail.conf');
			asterisk_Reload();
			header("Location: Extensions_List.php?msg=MODIFY_VIRTUAL_EXTENSION&hilight={$id}"); die();
		}
	} elseif (@$_REQUEST['PK_Extension'] != "") {
		$Extension = formdata_from_db($_REQUEST['PK_Extension']);
	} else {
		$Extension = formdata_from_template($_REQUEST['FK_Template']);
	}

	$smarty->assign('Extension', $Extension);
	$smarty->assign('Features' , $Features);
	$smarty->assign('Groups'   , $Groups);
	$smarty->assign('Message'  , $Message);
	$smarty->assign('Errors'   , $Errors);
	$smarty->assign('Rules'    , $Rules);

	return $smarty->fetch('Extensions_Virtual_Modify.tpl');
}

function formdata_from_db($id) {

	// Init data from 'Extensions'
	$query = "
		SELECT
			Ext_Virtual.PK_Extension AS PK_Extension,
			Extension,
			FirstName,
			FirstName_Editable,
			LastName,
			LastName_Editable,
			Password,
			Password_Editable,
			Email,
			Email_Editable,
			IVRDial
		FROM
			Extensions
			INNER JOIN Ext_Virtual ON Ext_Virtual.PK_Extension = Extensions.PK_Extension
		WHERE
			Extensions.PK_Extension = $id
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data   = mysql_fetch_assoc($result);

	// Init data from 'Extension_Groups'
	$query ="
		SELECT
			FK_Group
		FROM
			Extension_Groups
		WHERE
			FK_Extension = $id
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data['Groups'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Groups'][] = $row['FK_Group'];
	}

	// Init data from 'Ext_Virtual_Features'
	$query ="
		SELECT
			FK_Feature
		FROM
			Ext_Virtual_Features
		WHERE
			FK_Extension = $id
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data['Features'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Features'][] = $row['FK_Feature'];
	}

	// Init outgoing rules
	$query ="
		SELECT
			FK_OutgoingRule
		FROM
			Extension_Rules
		WHERE
			FK_Extension = {$data['PK_Extension']}
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data['Rules'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Rules'][] = $row['FK_OutgoingRule'];
	}


	return $data;
}

function formdata_from_template($id) {
	$query = "
		SELECT
			PK_Template,
			Name,
			FirstName_Editable,
			LastName_Editable,
			Password_Editable,
			Email_Editable
		FROM
			Templates
		WHERE
			PK_Template = $id
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error());
	$data   = mysql_fetch_assoc($result);

	$query ="
		SELECT
			FK_Group
		FROM
			Template_Groups
		WHERE
			FK_Template = $id
	";
	$result = mysql_query($query) or die(mysql_error());

	$data['Groups'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Groups'][] = $row['FK_Group'];
	}

	$query ="
		SELECT
			FK_Feature
		FROM
			Template_Features
		WHERE
			FK_Template = $id
	";
	$result = mysql_query($query) or die(mysql_error());

	$data['Features'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Features'][] = $row['FK_Feature'];
	}

	$query ="
		SELECT
			FK_OutgoingRule
		FROM
			Template_Rules
		WHERE
			FK_Template = $id
	";
	$result = mysql_query($query) or die(mysql_error().$query);

	$data['Rules'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Rules'][] = $row['FK_OutgoingRule'];
	}

	return $data;
}

function formdata_from_post() {
	return $_POST;
}

function formdata_save($data) {
	if ($data['PK_Extension'] == "") {
		$query = "INSERT INTO Extensions(Extension,Type) VALUES('".mysql_real_escape_string($data['Extension'])."', 'Virtual')";
		mysql_query($query) or die(mysql_error().$query);
		$data['PK_Extension'] = mysql_insert_id();

		$query = "INSERT INTO Ext_Virtual(PK_Extension) VALUES('".mysql_real_escape_string($data['PK_Extension'])."')";
		mysql_query($query) or die(mysql_error().$query);
	}

	// Update 'Extensions'
	$query = "
		UPDATE
			Ext_Virtual
		SET
			FirstName          = '".mysql_real_escape_string($data['FirstName'])."',
			FirstName_Editable = ".($data['FirstName_Editable']?'1':'0').",
			LastName           = '".mysql_real_escape_string($data['LastName'])."',
			LastName_Editable  = ".($data['LastName_Editable']?'1':'0').",
			Password_Editable  = ".($data['Password_Editable']?'1':'0').",
			Email              = '".mysql_real_escape_string($data['Email'])."',
			Email_Editable     = ".($data['Email_Editable']?'1':'0')."
		WHERE
			PK_Extension = ".mysql_real_escape_string($data['PK_Extension'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);

	// Update 'Extension_Groups'
	$query = "DELETE FROM Extension_Groups WHERE FK_Extension = ".mysql_real_escape_string($data['PK_Extension'])." ";
	mysql_query($query) or die(mysql_error());
	if (is_array($data['Groups'])) {
		foreach ($data['Groups'] as $FK_Group) {
			$query = "INSERT INTO Extension_Groups (FK_Extension, FK_Group) VALUES ({$data['PK_Extension']}, $FK_Group)";
			mysql_query($query) or die(mysql_error());
		}
	}

	// Update 'Ext_Virtual_Features'
	$query = "DELETE FROM Ext_Virtual_Features WHERE FK_Extension = ".mysql_real_escape_string($data['PK_Extension'])." ";
	mysql_query($query) or die(mysql_error().$query);

	if (is_array($data['Features'])) {
		foreach ($data['Features'] as $FK_Feature) {
			$query = "INSERT INTO Ext_Virtual_Features (FK_Extension, FK_Feature) VALUES ({$data['PK_Extension']}, $FK_Feature)";
			mysql_query($query) or die(mysql_error().$query);
		}
	}

	// Update Password if requested
	if ($data['Password'] != '') {
		$query = "
			UPDATE
				Ext_Agent
			SET
				Password     = '".mysql_real_escape_string($data['Password'])."'
			WHERE
				PK_Extension = ".mysql_real_escape_string($data['PK_Extension'])."
			LIMIT 1
		";
		mysql_query($query) or die(mysql_error().$query);
	}


	// Update 'Extension_Rules'
	$query = "DELETE FROM Extension_Rules WHERE FK_Extension = ".mysql_real_escape_string($data['PK_Extension'])." ";
	mysql_query($query) or die(mysql_error().$query);

	if (is_array($data['Rules'])) {
		foreach ($data['Rules'] as $FK_OutgoingRule => $Status) {
			if ($Status == 0) continue;
			$query = "INSERT INTO Extension_Rules (FK_Extension, FK_OutgoingRule) VALUES ({$data['PK_Extension']}, {$FK_OutgoingRule})";
			mysql_query($query) or die(mysql_error().$query);
		}
	}

	// Update 'IVRDial"
	$query = "UPDATE Extensions SET IVRDial = ".($data['IVRDial']==1?'1':'0')." WHERE PK_Extension = {$data['PK_Extension']}";
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
		} elseif (strlen($data['Extension']) < 3 || strlen($data['Extension']) > 5) {
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

	// Check if password is empty
	if ($data['Password'] == "") {
		if ($create_new) {
			$errors['Password']['Invalid'] = true;
		}
	// Check if password is numeric
	} elseif (intval($data['Password'])."" != $data['Password']) {
		$errors['Password']['Invalid'] = true;
	// Check if password is proper lenght
	} elseif (strlen($data['Password']) < 3 || strlen($data['Password']) > 10) {
		$errors['Password']['Invalid'] = true;
	// Check if passwords match it's retype
	} elseif ($data['Password'] != $data['Password_Retype']) {
		$errors['Password']['Match'] = true;
	}

	// Check if first name is proper length
	if ((strlen($data['FirstName'])<1) || (strlen($data['FirstName'])>32)) {
		$errors['FirstName']['Invalid'] = true;
	}

	return $errors;
}

admin_run('Extensions_Virtual_Modify', 'Admin.tpl');

?>