<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

include_once(dirname(__FILE__).'/../include/config.inc.php');

function VoipProviders_Iax_Modify() {
	session_start();
	$session = &$_SESSION['VoipProviders_Iax_Modify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init message (Message)
	$Message = $_REQUEST['msg'];

	// Init available RSA keys
	$query  = "SELECT PK_Key, Name FROM RSA_Keys ORDER BY Name";
	$result = mysql_query($query) or die(mysql_error());
	$Keys = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Keys[] = $row;
	}

	// Init available codecs (Codecs)
	$query  = "SELECT PK_Codec, Name, Description, Recomended FROM Codecs";
	$result = mysql_query($query) or die(mysql_error());
	$Codecs = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Codecs[] = $row;
	}
	$NrCodecs = count($Codecs);

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
			asterisk_UpdateConf('iax.conf');
			asterisk_UpdateConf('extensions.conf');
			asterisk_Reload();
			header("Location: VoipProviders_List.php?msg=MODIFY_IAX_PROVIDER&hilight={$id}"); die();
		}
	} elseif (@$_REQUEST['PK_IaxProvider'] != "") {
		$Provider = formdata_from_db($_REQUEST['PK_IaxProvider']);
	} else {
		$Provider = formdata_from_default();
	}

	$smarty->assign('Provider'  , $Provider);
	$smarty->assign('DTMFModes' , $DTMFModes);
	$smarty->assign('Keys'      , $Keys);
	$smarty->assign('Codecs'    , $Codecs);
	$smarty->assign('Message'   , $Message);
	$smarty->assign('Errors'    , $Errors);
	$smarty->assign('Rules'     , $Rules);

	return $smarty->fetch('VoipProviders_Iax_Modify.tpl');
}

function upload_key($id){
	$errors = array();
	$uploadPath = $GLOBALS['config']['RSA_keys_dir'];
	if ($_FILES['upload_key']['error'][0] == 1) {
		//file too big
		return false;
	}

	//update databse
	$query = "
		INSERT INTO
			`RSA_Keys`
		SET
			`Name` = '".mysql_escape_string($_FILES['upload_key']['name']['0'])."'
	";
	$result =  mysql_query($query) or die(mysql_error().$query);
	$PK_File = mysql_insert_id();

	//upload to disk
	$result = rename($_FILES['upload_key']['tmp_name'][0], "$uploadPath/".$_FILES['upload_key']['name']['0']);
	if ($result == false) {
		//upload to disk false
		return false;
	}

	// Update 'RSA_Keys'
	$query = "DELETE FROM IaxProvider_Keys WHERE FK_IaxProvider = ".mysql_real_escape_string($id)." ";
		mysql_query($query) or die(mysql_error());
	$query = "INSERT INTO IaxProvider_Keys (FK_IaxProvider, FK_Key) VALUES (".mysql_real_escape_string($id).",".mysql_real_escape_string($PK_File).")";
		mysql_query($query) or die(mysql_error());



	return true;
}

function formdata_from_db($id) {
	// Init data from 'IaxProviders'
	$query = "
		SELECT
			*
		FROM
			IaxProviders
		WHERE
			PK_IaxProvider = $id
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error());
	$data   = mysql_fetch_assoc($result);

	// Init data from 'IaxProvider_Codecs'
	$query ="
		SELECT
			FK_Codec
		FROM
			IaxProvider_Codecs
		WHERE
			FK_IaxProvider = $id
	";

	$result = mysql_query($query) or die(mysql_error());
	$data['Codecs'] = array();
	while ($row = mysql_fetch_assoc($result))
		{	$data['Codecs'][] = $row['FK_Codec'];	}

	// Init data from 'IaxProvider_Hosts'
	$query ="
		SELECT
			PK_IaxProvider_Host,
			Host
		FROM
			IaxProvider_Hosts
		WHERE
			FK_IaxProvider = $id
	";
	$result = mysql_query($query) or die(mysql_error());
	$data['Hosts'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Hosts'][] = $row['Host'];
	}

	// Init keys from 'RSA_Keys'
	$query ="
		SELECT
			FK_Key
		FROM
			IaxProvider_Keys
		WHERE
			FK_IaxProvider = $id
	";
	$result = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$data['Key'] = $row['FK_Key'];


	// Init outgoing rules
	$query ="
		SELECT
			FK_OutgoingRule
		FROM
			IaxProvider_Rules
		WHERE
			FK_IaxProvider = $id
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data['Rules'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Rules'][] = $row['FK_OutgoingRule'];
	}
	return $data;
}

function formdata_from_default() {
	$data = array(
		'Codecs'             => array(1,2),
		'MinRateFax'         => '2400',
		'MaxRateFax'         => '14400'
	);
	$data['Rules'] = array();
	return $data;
}

function formdata_from_post() {
	return $_POST;
}

function formdata_save($data) {

	if ($data['PK_IaxProvider'] == "") {
		$query = "INSERT INTO IaxProviders() VALUES()";
		mysql_query($query) or die(mysql_error().$query);
		$data['PK_IaxProvider'] = mysql_insert_id();
	}

	// Update 'Extensions'
	$query = "
		UPDATE
			IaxProviders
		SET
			Name               = '".mysql_real_escape_string($data['Name'])."',
			Label              = '".mysql_real_escape_string($data['Label'])."',
         	AccountID          = '".mysql_real_escape_string($data['AccountID'])."',
			Host               = '".mysql_real_escape_string($data['Host'])."',
			CallbackExtension  = '".mysql_real_escape_string($data['CallbackExtension'])."',
			AuthType           = '".mysql_real_escape_string($data['AuthType'])."',
			CallerIDName       = '".mysql_real_escape_string($data['CallerIDName'])."',
			CallerIDNumber     = '".mysql_real_escape_string($data['CallerIDNumber'])."',
			RegisterType       = '".mysql_real_escape_string($data['RegisterType'])."',
			TelesoftPBX        = ".($data['TelesoftPBX']?'1':'0').",
			LocalUser          = ".($data['LocalUser']?'1':'0').",
			JabberHostname     = '".mysql_real_escape_string($data['JabberHostname'])."',
			ApplyIncomingRules = ".($data['ApplyIncomingRules']?'1':'0').",
			PrOutbHost         = '".mysql_real_escape_string($data['PrOutbHost'])."',
			SecOutbHost        = '".mysql_real_escape_string($data['SecOutbHost'])."',
			TertOutbHost       = '".mysql_real_escape_string($data['TertOutbHost'])."',
			IncomingPassword   = '".mysql_real_escape_string($data['IncomingPassword'])."',
			Qualify            = ".($data['Qualify']?'1':'0').",
			Jitterbuffer       = ".($data['Jitterbuffer']?'1':'0').",
			Trunking           = ".($data['Trunking']?'1':'0').",
			RSA_auth           = ".($data['RSA_auth']?'1':'0').",
			ErrorCorrection    = ".($data['ErrorCorrection']?'1':'0').",
			MinRateFax         = '".mysql_real_escape_string($data['MinRateFax'])."',
			MaxRateFax         = '".mysql_real_escape_string($data['MaxRateFax'])."'
		WHERE
			PK_IaxProvider     = ".mysql_real_escape_string($data['PK_IaxProvider'])."
		LIMIT 1
	";

	mysql_query($query) or die(mysql_error().$query);

	// Update Password if requested and set PhonePassword if it was never set
	if ($data['Password'] != '') {
		$query = "
			UPDATE
				IaxProviders
			SET
				Password       = '".mysql_real_escape_string($data['Password'])."'
			WHERE
				PK_IaxProvider = ".mysql_real_escape_string($data['PK_IaxProvider'])."
			LIMIT 1
		";
		mysql_query($query) or die(mysql_error().$query);
	}

	// Update 'IaxProvider_Codecs'
	$query = "DELETE FROM IaxProvider_Codecs WHERE FK_IaxProvider = ".mysql_real_escape_string($data['PK_IaxProvider'])." ";
	mysql_query($query) or die(mysql_error());
	if (is_array($data['Codecs'])) {
		foreach ($data['Codecs'] as $FK_Codec) {
			$query = "INSERT INTO IaxProvider_Codecs (FK_IaxProvider, FK_Codec) VALUES ({$data['PK_IaxProvider']}, $FK_Codec)";
			mysql_query($query) or die(mysql_error());
		}
	}

	// Update 'RSA_Keys'
	if ($data['RSA_key']){
		$query = "DELETE FROM IaxProvider_Keys WHERE FK_IaxProvider = ".mysql_real_escape_string($data['PK_IaxProvider'])." ";
		mysql_query($query) or die(mysql_error());

		$query = "INSERT INTO IaxProvider_Keys (FK_IaxProvider, FK_Key) VALUES (".mysql_real_escape_string($data['PK_IaxProvider']).",".mysql_real_escape_string($data['RSA_key']).")";
		mysql_query($query) or die(mysql_error());

	}

	// Update 'IaxProviders_Hosts'
	$query = "DELETE FROM IaxProvider_Hosts WHERE FK_IaxProvider = ".mysql_real_escape_string($data['PK_IaxProvider'])." ";
	mysql_query($query) or die(mysql_error());
	if (is_array($data['Hosts'])) {
		foreach ($data['Hosts'] as $Host) {
			$query = "INSERT INTO IaxProvider_Hosts (FK_IaxProvider, Host) VALUES ({$data['PK_IaxProvider']}, '".mysql_real_escape_string($Host)."')";
			mysql_query($query) or die(mysql_error());
		}
	}

	// Update 'IaxProvider_Rules'
	$query = "DELETE FROM IaxProvider_Rules WHERE FK_IaxProvider = ".mysql_real_escape_string($data['PK_IaxProvider'])." ";
	mysql_query($query) or die(mysql_error());
	if (is_array($data['Rules'])) {
		foreach ($data['Rules'] as $FK_OutgoingRule) {
			if ($FK_OutgoingRule != 0) {
				$query = "INSERT INTO IaxProvider_Rules(FK_IaxProvider, FK_OutgoingRule) VALUES ({$data['PK_IaxProvider']}, $FK_OutgoingRule)";
				mysql_query($query) or die(mysql_error());
			}
		}
	}

	// If we don't apply incoming rules to this provider, make sure we remove existing ones (if exists)
	if ($data['ApplyIncomingRules'] == 0) {
		$query = "DELETE FROM IncomingRoutes WHERE ProviderType='IAX' AND ProviderID = {$data['PK_IaxProvider']}";
		mysql_query($query) or die(mysql_error());
	}

	return $data['PK_IaxProvider'];
}

function formdata_validate($data) {
	$errors = array();
	if ($_FILES['upload_key']['name']['0']){
			if (!upload_key($data['PK_IaxProvider']))
				$errors['UploadKey'] = true; ;
	}

	if ($data['IaxProvider'] == '') {
		$create_new = true;
	}

	// Check if name is 1-32 chars long
	if (strlen($data['Name']) < 1 || strlen($data['Name']) > 32) {
		$errors['Name']['Invalid'] = true;
	}

	// Check if name is 1-32 chars long
	if (!preg_match('/^[a-z0-9_]{1,32}$/i',$data['Label'])) {
		$errors['Label']['Invalid'] = true;
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

	return $errors;
}

admin_run('VoipProviders_Iax_Modify', 'Admin.tpl');
