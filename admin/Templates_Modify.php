<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Templates_Modify() {
	session_start();
	$session = &$_SESSION['Templates_Modify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init message (Message)
	$Message = $_REQUEST['msg'];

	// Init Available DTMF Modes (DTMFModes)
	$query  = "SELECT PK_DTMFMode, Name, Description FROM DTMFModes";
	$result = mysql_query($query) or die(mysql_errno());
	$DTMFModes = array();
	while ($row = mysql_fetch_assoc($result)) {
		$DTMFModes[] = $row;
	}

	// Init available codecs (Codecs)
	$query  = "SELECT PK_Codec, Name, Description, Recomended FROM Codecs";
	$result = mysql_query($query) or die(mysql_error());
	$Codecs = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Codecs[] = $row;
	}

	// Init available nat types (NATTypes)
	$query  = "SELECT PK_NATType, Name, Description FROM NATTypes";
	$result = mysql_query($query) or die(mysql_errno());
	$NATTypes = array();
	while ($row = mysql_fetch_assoc($result)) {
		$NATTypes[] = $row;
	}

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

	// Init form data (Template)
	if (@$_REQUEST['submit'] == 'save') {
		$Template = formdata_from_post();
		formdata_save($Template);

		header("Location: Templates_List.php?msg=MODIFY_TEMPLATE&hilight={$_REQUEST['PK_Template']}"); die();
	} else {
		$Template = formdata_from_db($_REQUEST['PK_Template']);
	}

	$smarty->assign('Template'       , $Template);
	$smarty->assign('DTMFModes'      , $DTMFModes);
	$smarty->assign('Codecs'         , $Codecs);
	$smarty->assign('Features'       , $Features);
	$smarty->assign('NATTypes'       , $NATTypes);
	$smarty->assign('Groups'         , $Groups);
	$smarty->assign('Rules'          , $Rules);
	$smarty->assign('Message'        , $Message);

	return $smarty->fetch('Templates_Modify.tpl');
}

function formdata_from_db($id) {
	$query = "
		SELECT
			PK_Template,
			Name,
			FirstName_Editable,
			LastName_Editable,
			Password_Editable,
			Email_Editable,
			FK_NATType,
			FK_DTMFMode,
			IVRDial
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
			FK_Codec
		FROM
			Template_Codecs
		WHERE
			FK_Template = $id
	";
	$result = mysql_query($query) or die(mysql_error());

	$data['Codecs'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Codecs'][] = $row['FK_Codec'];
	}

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

	// Init outgoing rules
	$query ="
		SELECT
			FK_OutgoingRule
		FROM
			Template_Rules
		WHERE
			FK_Template = {$data['PK_Template']}
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
	if (!isset($data['PK_Template'])) {
		return;
	}

	// Update 'Templates'
	$query = "
		UPDATE
			Templates
		SET
			FirstName_Editable = ".($data['FirstName_Editable']?'1':'0').",
			LastName_Editable  = ".($data['LastName_Editable']?'1':'0').",
			Password_Editable  = ".($data['Password_Editable']?'1':'0').",
			Email_Editable     = ".($data['Email_Editable']?'1':'0').",
			FK_NATType         = ".mysql_real_escape_string($data['FK_NATType']).",
			FK_DTMFMode        = ".mysql_real_escape_string($data['FK_DTMFMode']).",
			IVRDial            = ".($data['IVRDial']==1?'1':'0')."
		WHERE
			PK_Template = ".mysql_real_escape_string($data['PK_Template'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);

	// Update 'Template_Codecs'
	$query = "DELETE FROM Template_Codecs WHERE FK_Template = ".mysql_real_escape_string($data['PK_Template'])." ";
	mysql_query($query) or die(mysql_error());

	if (is_array($data['Codecs'])) {
		foreach ($data['Codecs'] as $FK_Codec) {
			$query = "INSERT INTO Template_Codecs (FK_Template, FK_Codec) VALUES ({$data['PK_Template']}, $FK_Codec)";
			mysql_query($query) or die(mysql_error());
		}
	}

	// Update 'Template_Groups'
	$query = "DELETE FROM Template_Groups WHERE FK_Template = ".mysql_real_escape_string($data['PK_Template'])." ";
	mysql_query($query) or die(mysql_error());

	if (is_array($data['Groups'])) {
		foreach ($data['Groups'] as $FK_Group) {
			$query = "INSERT INTO Template_Groups (FK_Template, FK_Group) VALUES ({$data['PK_Template']}, $FK_Group)";
			mysql_query($query) or die(mysql_error());
		}
	}

	// Update 'Template_Features'
	$query = "DELETE FROM Template_Features WHERE FK_Template = ".mysql_real_escape_string($data['PK_Template'])." ";
	mysql_query($query) or die(mysql_error().$query);

	if (is_array($data['Features'])) {
		foreach ($data['Features'] as $FK_Feature) {
			$query = "INSERT INTO Template_Features (FK_Template, FK_Feature) VALUES ({$data['PK_Template']}, $FK_Feature)";
			mysql_query($query) or die(mysql_error().$query);
		}
	}

	// Update 'Template_Rules'
	$query = "DELETE FROM Template_Rules WHERE FK_Template = ".mysql_real_escape_string($data['PK_Template'])." ";
	mysql_query($query) or die(mysql_error().$query);

	if (is_array($data['Rules'])) {
		foreach ($data['Rules'] as $FK_OutgoingRule => $Status) {
			if ($Status == 0) continue;
			$query = "INSERT INTO Template_Rules (FK_Template, FK_OutgoingRule) VALUES ({$data['PK_Template']}, {$FK_OutgoingRule})";
			mysql_query($query) or die(mysql_error().$query);
		}
	}
}

admin_run('Templates_Modify', 'Admin.tpl');

?>
