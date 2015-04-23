<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Recording_ModifyRule() {
	session_start();
	$session = &$_SESSION['Recording_ModifyRule'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init message (Message)
	$Message = $_REQUEST['msg'];

	if (@$_REQUEST['submit'] == 'save') {
		$Rule   = formdata_from_post();
		$Errors = formdata_validate($Rule);

		if (count($Errors) == 0) {
			if ($Rule['PK_Rule'] != '') {
				$msg = 'MODIFY_REC_RULE';
			} else {
				$msg = 'ADD_REC_RULE';
			}
			$id = formdata_save($Rule);

			header("Location: Recordings_List.php?hilight={$id}&msg={$msg}");
			die();
		}
	} elseif (@$_REQUEST['PK_Rule'] != "") {
		$Rule = formdata_from_db($_REQUEST['PK_Rule']);
	} else {
		$Rule = formdata_from_default();
	}

	// Geting a list of phone extension (Phones)
	$Phones = array();
	$query  = "
		SELECT
			Extensions.PK_Extension AS PK_Extension,
			Extension,
			CONCAT(
				IFNULL(Ext_SipPhones.FirstName,''),' ',IFNULL(Ext_SipPhones.LastName,''),
				IFNULL(Ext_Virtual.FirstName,'')  ,' ',IFNULL(Ext_Virtual.LastName,'')
			) AS Name
		FROM
			Extensions
			LEFT JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			LEFT JOIN Ext_Virtual   ON Ext_Virtual.PK_Extension   = Extensions.PK_Extension
		WHERE
			Type IN ('Virtual','SipPhone')
		ORDER BY
			Extension ASC
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$Phones[] = $row;
	}

	// Geting a list of extension groups (Groups)
	$Groups = array();
	$query  = "SELECT * FROM Groups ORDER BY Name";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$Groups[] = $row;
	}

	// Geting a list of queues (Queues)
	$Queues = array();
	$query  = "SELECT * FROM Ext_Queues INNER JOIN Extensions ON Ext_Queues.PK_Extension = Extensions.PK_Extension ORDER BY Extension ASC";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$Queues[] = $row;
	}

	$smarty->assign('Groups', $Groups);
	$smarty->assign('Queues', $Queues);
	$smarty->assign('Phones', $Phones);
	$smarty->assign('Rule'  , $Rule);
	$smarty->assign('Errors', $Errors);

	return $smarty->fetch('Recording_ModifyRule.tpl');
}

function formdata_from_db($id) {
	$query  = "
		SELECT
			*,
			DATE_FORMAT(EndDate, '%Y/%m/%d %H:%i') AS EndDate
		FROM
			RecordingRules
		WHERE
			PK_Rule = '$id'
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data   = mysql_fetch_assoc($result);

	$data['Extensions'] = array();
	$query = "SELECT FK_Extension FROM RecordingRules_Extensions WHERE FK_Rule = '$id'";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$data['Extensions'][] = $row['FK_Extension'];
	}

	$data['Groups'] = array();
	$query = "SELECT FK_Group FROM RecordingRules_Groups WHERE FK_Rule = '$id'";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$data['Groups'][] = $row['FK_Group'];
	}

	return $data;
}

function formdata_from_default() {
	$data = array();

	$data['Type']      = 'Phone';
	$data['EndCount']  = '10';
	$data['KeepCount'] = '0';
	$data['KeepSize']  = '0';
	$data['Backup']    = '0';

	$data['Extensions'] = array();
	$data['Groups']     = array();

	return $data;
}

function formdata_from_post() {
	$data = $_REQUEST;

	if (empty($data['Extensions'])) {
		$data['Extensions'] = array();
	}

	if (empty($data['Groups'])) {
		$data['Groups'] = array();
	}

	return $data;
}

function formdata_save($data) {
	if ($data['PK_Rule'] == "") {
		$query = "INSERT INTO RecordingRules() VALUES()";
		mysql_query($query) or die(mysql_error().$query);
		$data['PK_Rule'] = mysql_insert_id();
	}

	// Update 'RecordingRules'
	$query = "
		UPDATE
			RecordingRules
		SET
			Label         = '".mysql_real_escape_string($data['Label'])."',
			Type          = '".mysql_real_escape_string($data['Type'])."',
			Call_Incoming = '".($data['Call_Incoming']==1?1:0)."',
			Call_Outgoing = '".($data['Call_Outgoing']==1?1:0)."',
			Call_Queue    = '".($data['Call_Queue']==1?1:0)."',
			EndCount      = '".mysql_real_escape_string($data['EndCount'])."',
			EndDate       = ".(isset($data['EndDate'])?" STR_TO_DATE('".mysql_real_escape_string($data['EndDate'])."', '%Y/%m/%d %H:%i')":"NULL").",
			KeepCount     = '".mysql_real_escape_string($data['KeepCount'])."',
			KeepSize      = '".mysql_real_escape_string($data['KeepSize'])."',
			Backup        = '".mysql_real_escape_string($data['Backup'])."',
			MinLength     = '".intval($data['MinLength'])."'
		WHERE
			PK_Rule       = ".mysql_real_escape_string($data['PK_Rule'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);

	// Update 'RecordingRules_Extensions'
	$query = "DELETE FROM RecordingRules_Extensions WHERE FK_Rule = ".mysql_real_escape_string($data['PK_Rule'])." ";
	mysql_query($query) or die(mysql_error().$query);
	if (is_array($data['Extensions'])) {
		foreach ($data['Extensions'] as $FK_Extension) {
			$query = "INSERT INTO RecordingRules_Extensions(FK_Rule, FK_Extension) VALUES (".mysql_real_escape_string($data['PK_Rule']).", ".intval($FK_Extension).")";
			mysql_query($query) or die(mysql_error().$query);
		}
	}

	// Update 'RecordingRules_Groups'
	$query = "DELETE FROM RecordingRules_Groups WHERE FK_Rule = ".mysql_real_escape_string($data['PK_Rule'])." ";
	mysql_query($query) or die(mysql_error().$query);
	if (is_array($data['Groups'])) {
		foreach ($data['Groups'] as $FK_Group) {
			$query = "INSERT INTO RecordingRules_Groups(FK_Rule, FK_Group) VALUES (".mysql_real_escape_string($data['PK_Rule']).", ".intval($FK_Group).")";
			mysql_query($query) or die(mysql_error().$query);
		}
	}

	return $data['PK_Rule'];
}

function formdata_validate($data) {
	$errors = array();

	if (!preg_match('/^[a-zA-Z1-9 -]{1,30}$/', $data['Label'])) {
		$errors['Label']['Invalid'] = true;
	}

	if (intval($data['Call_Incoming']) + intval($data['Call_Outgoing']) + intval($data['Call_Queue']) == 0) {
		$errors['Call_Type']['None'] = true;
	}

	if ($data['EndDate'] == "") {
		if (!preg_match('/^[1-9]{1}[0-9]{0,1}[0-9]{0,1}$/', $data['EndCount'])) {
			$errors['EndCount']['Invalid'] = true;
		}
	}

	if (count($data['Extensions']) == 0 && count($data['Groups']) == 0) {
		$errors['Calls']['Empty'] = true;
	}

	if ($data['MinLength_Delete'] == "1") {
		if (!preg_match('/^[1-9]{1}[0-9]{0,1}[0-9]{0,1}$/', $data['MinLength'])) {
			$errors['MinLength']['Invalid'] = true;
		}
	}

	if ($data['KeepType'] != 1) {
		if (!preg_match('/^[1-9]{1}[0-9]{0,1}[0-9]{0,1}$/', $data['KeepValue'])) {
			$errors['KeepValue']['Invalid'] = true;
		}
	}

	return $errors;
}

admin_run('Recording_ModifyRule', 'Admin.tpl');
?>