<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

function Extensions_Intercom_Modify() {
	session_start();
	$session = &$_SESSION['Extensions_Intercom_Modify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init message (Message)
	$Message = $_REQUEST['msg'];


	// Groups
	$query  = "SELECT PK_Group, Name FROM Groups";
	$result = mysql_query($query) or die(mysql_error().$query);
	$Groups = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Groups[] = $row;
	}

	// Init Available Accounts (Accounts)
	$query  = "
		SELECT
			Extensions.PK_Extension,
			Extension,
			CONCAT(IFNULL(Ext_SipPhones.FirstName,''),IFNULL(Ext_Virtual.FirstName,'')) AS FirstName,
			CONCAT(IFNULL(Ext_SipPhones.LastName ,''),IFNULL(Ext_Virtual.LastName,''))  AS LastName
		FROM
			Extensions
			LEFT JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			LEFT JOIN Ext_Virtual   ON Ext_Virtual.PK_Extension   = Extensions.PK_Extension
		WHERE
			Extensions.Type IN ('Virtual', 'SipPhone')
		ORDER BY Extension
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$Accounts = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Accounts[] = $row;
	}

	// Init Folders (SoundFolders)
	$SoundFolders = array();
	$query = "
		SELECT
			PK_SoundFolder,
			Name
		FROM
			SoundFolders
		ORDER BY Name
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$SoundFolders[] = $row;
	}

	// Init SoundLanguages
	$SoundLanguages = array();
	$query = "
		SELECT
			PK_SoundLanguage,
			`Default`,
			Name
		FROM
			SoundLanguages
		ORDER BY Name
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$SoundLanguages[] = $row;
		if ($row['Default']) {
			$SoundLanguage_Default = $row;
		}
	}

	// Init SoundEntries
	$SoundEntries = array();
	foreach ($SoundLanguages as $SoundLanguage) {
		$lid = $SoundLanguage['PK_SoundLanguage'];
		$query_select .= "
			SoundFiles_{$lid}.Name        AS Name_{$lid}       ,
			SoundFiles_{$lid}.Description AS Description_{$lid},
		";
		$query_from .= "
			LEFT JOIN SoundFiles SoundFiles_{$lid} ON SoundFiles_{$lid}.FK_SoundEntry = PK_SoundEntry AND SoundFiles_{$lid}.FK_SoundLanguage = {$lid}
		";
	}
	$query = "
		SELECT
			$query_select
			PK_SoundEntry,
			FK_SoundFolder
		FROM
			SoundEntries
			$query_from
		ORDER BY
			PK_SoundEntry
	";

	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$SoundEntry = $row;

		foreach ($SoundLanguages as $SoundLanguage) {
			$lid   = $SoundLanguage['PK_SoundLanguage'];

			$SoundEntry['Name'][$lid]        = $SoundEntry["Name_$lid"];
			$SoundEntry['Description'][$lid] = $SoundEntry["Description_$lid"];

			if ($SoundEntry["Name_$lid"] != "") {
				$SoundEntry['Name']['Default'] = "{$SoundEntry['Name'][$SoundLanguage['PK_SoundLanguage']]}";
			}
		}

		if ($SoundEntry['Name'][$SoundLanguage_Default['PK_SoundLanguage']] != "") {
			$SoundEntry['Name']['Default'] = "{$SoundEntry['Name'][$SoundLanguage_Default['PK_SoundLanguage']]}";
		}

		$SoundEntries[] = $SoundEntry;
	}

	// Init available Sounds (SoundsFiles)
	$query  = "
		SELECT
			PK_SoundFile,
			SoundFiles.Name,
			SoundFiles.Description,
			SoundLanguages.Name AS Language
		FROM
			SoundFiles
			INNER JOIN SoundEntries   ON FK_SoundEntry    = PK_SoundEntry
			INNER JOIN SoundFolders   ON FK_SoundFolder   = PK_SoundFolder
			INNER JOIN SoundLanguages ON FK_SoundLanguage = PK_SoundLanguage
		WHERE
			SoundFolders.Name = 'Call Queues'
		ORDER BY
			SoundLanguages.Name ASC,
			SoundFiles.Name     ASC
	";
	$result = mysql_query($query) or die(mysql_error());
	$SoundFiles = array();
	while ($row = mysql_fetch_assoc($result)) {
		$SoundFiles[$row['Language']][$row['PK_SoundFile']] = $row['Name'];
	}

	// Init form data (Intercom)
	if (@$_REQUEST['submit'] == 'save') {
		$Intercom = formdata_from_post();
		$Errors   = formdata_validate($Intercom);

		if (count($Errors) == 0) {
			$id = formdata_save($Intercom);
			header("Location: Extensions_List.php?msg=MODIFY_INTERCOM_EXTENSION&hilight={$id}"); die();
		}
	} elseif (@$_REQUEST['PK_Extension'] != "") {
		$Intercom = formdata_from_db($_REQUEST['PK_Extension']);
	} else {
		$Intercom = array();
		$Intercom['Header']    = 'Intercom';
		$Intercom['Timeout']   = 120;
		$Intercom['TwoWay']    = 1;
		$Intercom['PlaySound'] = 1;
	}

	$smarty->assign('Intercom'      , $Intercom);
	$smarty->assign('Groups'        , $Groups);
	$smarty->assign('Accounts'      , $Accounts);
	$smarty->assign('Message'       , $Message);
	$smarty->assign('Errors'        , $Errors);

	$smarty->assign('SoundEntries'  , $SoundEntries);
	$smarty->assign('SoundFolders'  , $SoundFolders);
	$smarty->assign('SoundLanguages', $SoundLanguages);
	$smarty->assign('SoundLanugage_Default',$SoundLanguage_Default );

	return $smarty->fetch('Extensions_Intercom_Modify.tpl');
}

function formdata_from_db($id) {

	// Init data from 'Extensions'
	$query = "
		SELECT
			*
		FROM
			Ext_Intercom
			INNER JOIN Extensions ON Extensions.PK_Extension = Ext_Intercom.PK_Extension
		WHERE
			Ext_Intercom.PK_Extension = $id
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error());
	$data   = mysql_fetch_assoc($result);

	// Init data from 'Intercom_Memebers'
	$data['Members'] = array();
	$query ="SELECT FK_Ext_Member, FK_Ext_Group FROM Ext_Intercom_Members WHERE FK_Extension = $id";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_array($result)) {
		$data['Members'][] = $row[0];
		if ($row[1] != 0) { $data['FK_Group_Member'] = $row[1]; }
	}

	// Init data from 'Intercom_Admins'
	$data['Admins'] = array();
	$query ="SELECT FK_Ext_Admin, FK_Ext_Group FROM Ext_Intercom_Admins WHERE FK_Extension = $id";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_array($result)) {
		$data['Admins'][] = $row[0];
		if ($row[1] != 0) { $data['FK_Group_Admin'] = $row[1]; }
	}

	return $data;
}

function formdata_from_post() {
	return $_POST;
}

function formdata_save($data) {
	if ($data['PK_Extension'] == "") {
		$query = "INSERT INTO Extensions(Type, Extension) VALUES('Intercom', '".mysql_real_escape_string($data['Extension'])."')";
		mysql_query($query) or die(mysql_error().$query);
		$data['PK_Extension'] = mysql_insert_id();

		$query = "INSERT INTO Ext_Intercom(PK_Extension) VALUES('".mysql_real_escape_string($data['PK_Extension'])."')";
		mysql_query($query) or die(mysql_error().$query);
	}

	// Update 'Ext_Intercom'
	$query = "
		UPDATE
			Ext_Intercom
		SET
			Header             = '".mysql_real_escape_string($data['Header'])."',
			Timeout            = '".mysql_real_escape_string($data['Timeout'])."',
			TwoWay             = '".($data['TwoWay']=='1'?'1':'0')."',
			PlaySound          = '".($data['PlaySound']=='0'?'0':'1')."',
			FK_Folder          = '".mysql_real_escape_string($data['SoundFolders'])."',
			FK_Lang            = '".mysql_real_escape_string($data['SoundLanguages'])."',
			FK_Sound           = '".mysql_real_escape_string($data['SoundEntries'])."',			
			Use_Members_ByAccount = '".mysql_real_escape_string($data['Use_Members_ByAccount'])."',
			Use_Admins_ByAccount  = '".mysql_real_escape_string($data['Use_Admins_ByAccount'])."'
		WHERE
			PK_Extension       = ".mysql_real_escape_string($data['PK_Extension'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);

	// Update 'Ext_Intercom_Members'
	$query = "DELETE FROM Ext_Intercom_Members WHERE FK_Extension = ".mysql_real_escape_string($data['PK_Extension'])." ";
	mysql_query($query) or die(mysql_error());

	if (is_array($data['Members'])) {
		if($data['Use_Members_ByAccount']){
			foreach ($data['Members'] as $member) {
				$query = "INSERT INTO Ext_Intercom_Members (FK_Extension, FK_Ext_Member,FK_Ext_Group) VALUES ({$data['PK_Extension']}, {$member}, 0)";
				mysql_query($query) or die(mysql_error().$query);
			}
		}
	}else{
		$query = "INSERT INTO Ext_Intercom_Members (FK_Extension, FK_Ext_Member,FK_Ext_Group) VALUES ({$data['PK_Extension']}, 0, {$data['GroupsM']})";
		mysql_query($query) or die(mysql_error().$query);
	}

	// Update 'Ext_Intercom_Admins'
	$query = "DELETE FROM Ext_Intercom_Admins WHERE FK_Extension = ".mysql_real_escape_string($data['PK_Extension'])." ";
	mysql_query($query) or die(mysql_error());

	if (is_array($data['Admins'])) {
		if($data['Use_Admins_ByAccount']){
			foreach ($data['Admins'] as $admin) {
				$query = "INSERT INTO Ext_Intercom_Admins (FK_Extension, FK_Ext_Admin,FK_Ext_Group) VALUES ({$data['PK_Extension']}, {$admin}, 0)";
				mysql_query($query) or die(mysql_error().$query);
			}
		}
	}else{
		$query = "INSERT INTO Ext_Intercom_Admins (FK_Extension, FK_Ext_Admin,FK_Ext_Group) VALUES ({$data['PK_Extension']}, 0, {$data['GroupsA']})";
		mysql_query($query) or die(mysql_error().$query);
	}



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

//	// Check if first name is proper length
//	if ((strlen($data['Name'])<1) || (strlen($data['Name'])>20)) {
//		$errors['Name']['Invalid'] = true;
//	}

	return $errors;
}

admin_run('Extensions_Intercom_Modify', 'Admin.tpl');

?>