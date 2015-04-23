<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__)."/../include/asterisk_utils.inc.php");

function VoipProviders_Sip_Modify() {
	session_start();
	$session = &$_SESSION['VoipProviders_Sip_Modify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');


//	myprint($_REQUEST);
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

	// Init available outgoing rules (Rules)
	$query  = "SELECT * FROM OutgoingRules ORDER BY Name";
	$result = mysql_query($query) or die(mysql_errno());
	$Rules = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Rules[] = $row;
	}

	// Init form data (Providers)
	if (@$_REQUEST['submit'] == 'save') {
		$Provider = formdata_from_post();
		$Errors   = formdata_validate($Provider);

		if (count($Errors) == 0) {
			$id = formdata_save($Provider);
			asterisk_UpdateConf('sip.conf');
			asterisk_UpdateConf('extensions.conf');
			asterisk_Reload();
			header("Location: VoipProviders_List.php?msg=MODIFY_SIP_PROVIDER&hilight={$id}"); die();
		}
	} elseif (@$_REQUEST['PK_SipProvider'] != "") {
		$Provider = formdata_from_db($_REQUEST['PK_SipProvider']);
	} else {
		$Provider = formdata_from_default();
	}

	$smarty->assign('Provider'  , $Provider);
	$smarty->assign('DTMFModes' , $DTMFModes);
	$smarty->assign('Codecs'    , $Codecs);
	$smarty->assign('Message'   , $Message);
	$smarty->assign('Errors'    , $Errors);
	$smarty->assign('Rules'     , $Rules);

	return $smarty->fetch('VoipProviders_Sip_Modify.tpl');
}

function formdata_from_db($id) {
	// Init data from 'SipProviders'
	$query = "
		SELECT
			*
		FROM
			SipProviders
		WHERE
			PK_SipProvider = $id
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error());
	$data   = mysql_fetch_assoc($result);

	// Init data from 'SipProvider_Codecs'
	$query ="
		SELECT
			FK_Codec
		FROM
			SipProvider_Codecs
		WHERE
			FK_SipProvider = $id
	";
	$result = mysql_query($query) or die(mysql_error());
	$data['Codecs'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Codecs'][] = $row['FK_Codec'];
	}

	// Init data from 'SipProvider_Hosts'
	$query ="
		SELECT
			PK_SipProvider_Host,
			Host
		FROM
			SipProvider_Hosts
		WHERE
			FK_SipProvider = $id
	";
	$result = mysql_query($query) or die(mysql_error());
	$data['Hosts'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Hosts'][] = $row['Host'];
	}

	// Init outgoing rules
	$query ="
		SELECT
			FK_OutgoingRule
		FROM
			SipProvider_Rules
		WHERE
			FK_SipProvider = $id
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data['Rules'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Rules'][] = $row['FK_OutgoingRule'];
	}

	$data['MapRings'] = explode(";",$data['MapRings']);
	return $data;


}

function formdata_from_default() {
	$data = array(
		'FK_DTMFMode'        => '1',
		'HostType'           => 'Provider',
		'CallerIDChange'     => '0',
		'Codecs'             => array(1,2),
		'SipPort'            => '5060',
		'SipExpiry'          => '120',
		'AlwaysTrust'        => '1',
		'SendEarlyMedia'     => '0',
		'ApplyIncomingRules' => '1',
		'Qualify'            => '0',
		'UserEqPhone'        => '0',
		'LocalAddrFrom'      => '0',
		'Rules'              => array()
	);
	return $data;
}

function formdata_from_post() {
	$data = $_POST;

	if (!is_array($data['Rules'])) {
		$data['Rules'] = array();
	}

	if ($data['AuthUser'] == '') {
		$data['AuthUser'] = $data['AccountID'];
	}
	if ($data['ProxyHost'] == '') {
		$data['ProxyHost'] = $data['Host'];
	}

	return $data;
}

function formdata_save($data) {
	if ($data['PK_SipProvider'] == "") {
		$query = "INSERT INTO SipProviders() VALUES()";
		mysql_query($query) or die(mysql_error().$query);

		$data['PK_SipProvider'] = mysql_insert_id();
	}

	$data['MapRings'] = @implode(";", $data['MapRing']);

	// Update 'Extensions'
	$query = "
		UPDATE
			SipProviders
		SET
			Name               = '".mysql_real_escape_string($data['Name'])."',
			AccountID          = '".mysql_real_escape_string($data['AccountID'])."',
			Host               = '".mysql_real_escape_string($data['Host'])."',
			CallbackExtension  = '".mysql_real_escape_string($data['CallbackExtension'])."',
			FK_DTMFMode        = ".mysql_real_escape_string($data['FK_DTMFMode']).",
			HostType           = '".mysql_real_escape_string($data['HostType'])."',
			CallerIDChange     = ".($data['CallerIDChange']?'1':'0').",
			CallerIDName       = '".mysql_real_escape_string($data['CallerIDName'])."',
			CallerIDNumber     = '".mysql_real_escape_string($data['CallerIDNumber'])."',
			CallerIDMethod     = '".mysql_real_escape_string($data['CallerIDMethod'])."',
			SipPort            = '".mysql_real_escape_string($data['SipPort'])."',
			SipExpiry          = '".mysql_real_escape_string($data['SipExpiry'])."',
			ProxyHost          = '".mysql_real_escape_string($data['ProxyHost'])."',
			AuthUser           = '".mysql_real_escape_string($data['AuthUser'])."',
			AlwaysTrust        = ".($data['AlwaysTrust']?'1':'0').",
			SendEarlyMedia     = ".($data['SendEarlyMedia']?'1':'0').",
			ApplyIncomingRules = ".($data['ApplyIncomingRules']?'1':'0').",
			Qualify            = ".($data['Qualify']?'1':'0').",
			UserEqPhone        = ".($data['UserEqPhone']?'1':'0').",
			LocalAddrFrom      = ".($data['LocalAddrFrom']?'1':'0').",
			DTMFDial           = ".($data['DTMFDial']?'1':'0').",
			TelesoftPBX        = ".($data['TelesoftPBX']?'1':'0').",
			LocalUser          = ".($data['LocalUser']?'1':'0').",
			Voicepulse         = ".($data['Voicepulse']?'1':'0').",
			JabberHostname     = '".mysql_real_escape_string($data['JabberHostname'])."',
			Jitterbuffer       = '".mysql_real_escape_string($data['Jitterbuffer'])."',
			Reinvite           = '".mysql_real_escape_string($data['Reinvite'])."',
			ErrorCorrection    = ".($data['ErrorCorrection']?'1':'0').",
			MinRateFax         = '".mysql_real_escape_string($data['MinRateFax'])."',
			MaxRateFax         = '".mysql_real_escape_string($data['MaxRateFax'])."',
			RSP_fax            = '".mysql_real_escape_string($data['RSP_fax'])."',
			RIP_fax            = '".mysql_real_escape_string($data['RIP_fax'])."',
			MaxDelayFax        = '".mysql_real_escape_string($data['MaxDelayFax'])."',
			MapRings            = '".mysql_real_escape_string($data['MapRings'])."'
		WHERE
			PK_SipProvider     = ".mysql_real_escape_string($data['PK_SipProvider'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);

	// Update Password if requested and set PhonePassword if it was never set
	if ($data['Password'] != '') {
		$query = "
			UPDATE
				SipProviders
			SET
				Password       = '".mysql_real_escape_string($data['Password'])."'
			WHERE
				PK_SipProvider = ".mysql_real_escape_string($data['PK_SipProvider'])."
			LIMIT 1
		";
		mysql_query($query) or die(mysql_error().$query);
	}

	// Update 'SipProvider_Codecs'
	$query = "DELETE FROM SipProvider_Codecs WHERE FK_SipProvider = ".mysql_real_escape_string($data['PK_SipProvider'])." ";
	mysql_query($query) or die(mysql_error());
	if (is_array($data['Codecs'])) {
		foreach ($data['Codecs'] as $FK_Codec) {
			$query = "INSERT INTO SipProvider_Codecs (FK_SipProvider, FK_Codec) VALUES ({$data['PK_SipProvider']}, $FK_Codec)";
			mysql_query($query) or die(mysql_error());
		}
	}

	// Update 'SipProviders_Hosts'
	$query = "DELETE FROM SipProvider_Hosts WHERE FK_SipProvider = ".mysql_real_escape_string($data['PK_SipProvider'])." ";
	mysql_query($query) or die(mysql_error());
	if (is_array($data['Hosts'])) {
		foreach ($data['Hosts'] as $Host) {
			$query = "INSERT INTO SipProvider_Hosts (FK_SipProvider, Host) VALUES ({$data['PK_SipProvider']}, '".mysql_real_escape_string($Host)."')";
			mysql_query($query) or die(mysql_error());
		}
	}

	// Update 'SipProvider_Rules'
	$query = "DELETE FROM SipProvider_Rules WHERE FK_SipProvider = ".mysql_real_escape_string($data['PK_SipProvider'])." ";
	mysql_query($query) or die(mysql_error());
	if (is_array($data['Rules'])) {
		foreach ($data['Rules'] as $FK_OutgoingRule) {
			if ($FK_OutgoingRule != 0) {
				$query = "INSERT INTO SipProvider_Rules(FK_SipProvider, FK_OutgoingRule) VALUES ({$data['PK_SipProvider']}, $FK_OutgoingRule)";
				mysql_query($query) or die(mysql_error());
			}
		}
	}

	// If we don't apply incoming rules to this provider, make sure we remove existing ones (if exists)
	if ($data['ApplyIncomingRules'] == 0) {
		$query = "DELETE FROM IncomingRoutes WHERE ProviderType='SIP' AND ProviderID = {$data['PK_SipProvider']}";
		mysql_query($query) or die(mysql_error());
	}

	return $data['PK_SipProvider'];
}

function formdata_validate($data) {
	$errors = array();
	if ($data['SipProvider'] == '') {
		$create_new = true;
	}

	// Check if name is 1-32 chars long
	if (strlen($data['Name']) < 1 || strlen($data['Name']) > 32) {
		$errors['Name']['Invalid'] = true;
	}

	// Check if account id is 1-32 chars long
	if (strlen($data['AccountID']) < 1 || strlen($data['AccountID']) > 32) {
		$errors['AccountID']['Invalid'] = true;
	// Check if account id contains spaces
	} elseif (count(explode(" ", $data['AccountID'])) > 1) {
		$errors['AccountID']['Invalid'] = true;
	}

	// Check if password is 1-32 chars long
	if (strlen($data['Password']) < 1 || strlen($data['Password']) > 32) {
		$errors['Password']['Invalid'] = true;
	}

	if ($data['ApplyIncomingRules'] == 1) {
	// Check if callback extension is formed of digits only
	if ($data['CallbackExtension'] != "".intval($data['CallbackExtension'])) {
		$errors['CallbackExtension']['Invalid'] = true;
	// Check if extension is 3-5 digits long
	} elseif (strlen($data['CallbackExtension']) < 3 || strlen($data['CallbackExtension']) > 5) {
		$errors['CallbackExtension']['Invalid'] = true;
	// Check if extension is valid on the system
	} else {
		$query  = "SELECT PK_Extension FROM Extensions WHERE Extension = '".mysql_real_escape_string($data['CallbackExtension'])."' LIMIT 1";
		$result = mysql_query($query) or die(mysql_error().$query);
		if (mysql_num_rows($result) < 1) {
			$errors['CallbackExtension']['NoMatch'] = true;
		}
	}
	}

	// Check if a hostaname was supplied for this provider
	if (! preg_match ("/^([a-z0-9][a-z0-9-.]{0,62})$/i", $data['Host'])) {
		$errors['Host']['Invalid'] = true;
	}

	// Check every host from the hosts list
	if (is_array($data['Hosts'])) {
	foreach ($data['Hosts'] as $Host) {
		// If is not a hostname
		if (! preg_match ("/^[a-z0-9][a-z0-9\-]+[a-z0-9]$/i", $Host)) {
			// and is not an ip address
			if (ip2long($Host) === false) {
				$errors['Hosts']['Invalid'] = true;
			}
		}
	}
	}
	return $errors;
}

admin_run('VoipProviders_Sip_Modify', 'Admin.tpl');

?>
