<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

function Extensions_Queue_Modify() {
	session_start();
	$session = &$_SESSION['Extensions_Queue_Modify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init message (Message)
	$Message = $_REQUEST['msg'];

	// Init Available ringing strategies (RingStrategies)
	$query  = "SELECT PK_RingStrategy, Name, Description FROM RingStrategies";
	$result = mysql_query($query) or die(mysql_error().$query);
	$RingStrategies = array();
	while ($row = mysql_fetch_assoc($result)) {
		$RingStrategies[] = $row;
	}

	// Init Available Members (Members)
	$query  = "
		SELECT
			Extensions.PK_Extension,
			Extension,
			CONCAT(IFNULL(Ext_SipPhones.FirstName,''),IFNULL(Ext_Virtual.FirstName,'')) AS FirstName,
			CONCAT(IFNULL(Ext_SipPhones.LastName,'') ,IFNULL(Ext_Virtual.LastName,''))  AS LastName
		FROM
			Extensions
			LEFT JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			LEFT JOIN Ext_Virtual   ON Ext_Virtual.PK_Extension   = Extensions.PK_Extension
		WHERE
			Extensions.Type IN ('Virtual', 'SipPhone')
		ORDER BY Extension
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$Members = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Members[] = $row;
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

	// Init available moh groups
	$query  = "SELECT * FROM Moh_Groups ORDER BY Name ASC";
	$result = mysql_query($query) or die(mysql_error());
	$MohGroups = array();
	while ($row = mysql_fetch_assoc($result)) {
		$MohGroups[] = $row;
	}

	// Init form data (Queue)
	if (@$_REQUEST['submit'] == 'save') {
		$Queue  = formdata_from_post();
		$Errors = formdata_validate($Queue);

		if (count($Errors) == 0) {
			$id = formdata_save($Queue);
			asterisk_UpdateConf('queues.conf');
			asterisk_Reload();
			header("Location: Extensions_List.php?msg=MODIFY_QUEUE_EXTENSION&hilight={$id}"); die();
		}
	} elseif (@$_REQUEST['PK_Extension'] != "") {
		$Queue = formdata_from_db($_REQUEST['PK_Extension']);
	} else {
		$Queue = array(
			'MemberRingTime'    => '16',
			'NextWaitTime'      => '5',
			'WrapUpTime'        => '30',
			'PlayMohInQueue'    => '1',
			'AnnounceFrequency' => '90',
			'FK_Sound_PickupAnnouncement' => '4552',
			'FK_Sound_YouAreNext'         => '4401',
			'FK_Sound_ThereAre'           => '4249',
			'FK_Sound_CallsWaiting'       => '4394',
			'FK_Sound_HoldTime'           => '4351',
			'FK_Sound_Minutes'            => '4361',
			'FK_Sound_ThankYou'           => '4243'
		);
	}

	$smarty->assign('Queue'         , $Queue);
	$smarty->assign('RingStrategies', $RingStrategies);
	$smarty->assign('SoundFiles'    , $SoundFiles);
	$smarty->assign('MohGroups'     , $MohGroups);
	$smarty->assign('Members'       , $Members);
	$smarty->assign('Message'       , $Message);
	$smarty->assign('Errors'        , $Errors);

	return $smarty->fetch('Extensions_Queue_Modify.tpl');
}

function formdata_from_db($id) {

	// Init data from 'Extensions'
	$query = "
		SELECT
			*
		FROM
			Ext_Queues
			INNER JOIN Extensions ON Extensions.PK_Extension = Ext_Queues.PK_Extension
		WHERE
			Ext_Queues.PK_Extension = $id
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error());
	$data   = mysql_fetch_assoc($result);

	// Init data from 'Queue_Memebers'
	$query ="
		SELECT
			Extensions.PK_Extension,
			Extension,
			CONCAT(IFNULL(Ext_SipPhones.FirstName,''),IFNULL(Ext_Virtual.FirstName,'')) AS FirstName,
			CONCAT(IFNULL(Ext_SipPhones.LastName,'') ,IFNULL(Ext_Virtual.LastName,''))  AS LastName,
			LoginRequired
		FROM
			Ext_Queue_Members
			INNER JOIN Extensions    ON FK_Extension_Member = PK_Extension
			LEFT  JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			LEFT  JOIN Ext_Virtual   ON Ext_Virtual.PK_Extension   = Extensions.PK_Extension
		WHERE
			FK_Extension = $id
		ORDER
			By QueueOrder
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data['Members'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Members'][] = $row['LoginRequired']."~".$row['PK_Extension'];
	}

	return $data;
}

function formdata_from_post() {
	return $_POST;
}

function formdata_save($data) {
	if ($data['PK_Extension'] == "") {
		$query = "INSERT INTO Extensions(Type, Extension) VALUES('Queue', '".mysql_real_escape_string($data['Extension'])."')";
		mysql_query($query) or die(mysql_error().$query);
		$data['PK_Extension'] = mysql_insert_id();

		$query = "INSERT INTO Ext_Queues(PK_Extension) VALUES('".mysql_real_escape_string($data['PK_Extension'])."')";
		mysql_query($query) or die(mysql_error().$query);
	}

	// Update 'Ext_Queues'
	$query = "
		UPDATE
			Ext_Queues
		SET
			Name               = '".mysql_real_escape_string($data['Name'])."',
			FK_RingStrategy    =  ".mysql_real_escape_string($data['FK_RingStrategy']).",
			RingInUse          = '".($data['RingInUse']=='yes'?'yes':'no')."',
			FK_MohGroup        = ".intval($data['FK_MohGroup']).",
			PlayMohInQueue     = '".mysql_real_escape_string($data['PlayMohInQueue'])."',
			MemberRingTime     = '".mysql_real_escape_string($data['MemberRingTime'])."',
			NextWaitTime       = '".mysql_real_escape_string($data['NextWaitTime'])."',
			WrapUpTime         = '".mysql_real_escape_string($data['WrapUpTime'])."',
			".($data['PlayMohInQueue']?"
			AnnouncePosition   = '".mysql_real_escape_string($data['AnnouncePosition'])."',
			AnnounceHoldtime   = '".mysql_real_escape_string($data['AnnounceHoldtime'])."',
			AnnounceFrequency  = '".mysql_real_escape_string($data['AnnounceFrequency']+0)."',
			FK_Sound_PickupAnnouncement = ".intval($data['FK_Sound_PickupAnnouncement']).",
			FK_Sound_YouAreNext         = ".intval($data['FK_Sound_YouAreNext']).",
			FK_Sound_ThereAre           = ".intval($data['FK_Sound_ThereAre']).",
			FK_Sound_CallsWaiting       = ".intval($data['FK_Sound_CallsWaiting']).",
			FK_Sound_HoldTime           = ".intval($data['FK_Sound_HoldTime']).",
			FK_Sound_Minutes            = ".intval($data['FK_Sound_Minutes']).",
			FK_Sound_ThankYou           = ".intval($data['FK_Sound_ThankYou']).",
			":"")."
			Timeout            = '".mysql_real_escape_string($data['Timeout']+0)."',
			TimeoutExtension   = '".mysql_real_escape_string($data['TimeoutExtension'])."',
			JoinEmptyExtension = '".mysql_real_escape_string($data['JoinEmptyExtension'])."',
			MaxLen             = '".mysql_real_escape_string($data['MaxLen']+0)."',
			MaxLenExtension    = '".mysql_real_escape_string($data['MaxLenExtension'])."',
			Cycles             = '".mysql_real_escape_string($data['Cycles']+0)."',
			CyclesExtension    = '".mysql_real_escape_string($data['CyclesExtension'])."',
			OperatorExtension  = '".mysql_real_escape_string($data['OperatorExtension'])."',
			AckCall            = '".mysql_real_escape_string($data['AckCall'])."',
			MissedCallsAllowed = '".mysql_real_escape_string($data['MissedCallsAllowed'])."'
		WHERE
			PK_Extension = ".mysql_real_escape_string($data['PK_Extension'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);

	// Update 'Ext_Queue_Members'
	$query = "DELETE FROM Ext_Queue_Members WHERE FK_Extension = ".mysql_real_escape_string($data['PK_Extension'])." ";
	mysql_query($query) or die(mysql_error());
	if (is_array($data['Members'])) {
		$QueueOrder = 0;
		foreach ($data['Members'] as $member_value) {
			$QueueOrder++;
			$MemberString = explode('~', $member_value);
			$member['FK_Extension']  = $MemberString[1];
			$member['LoginRequired'] = $MemberString[0];
			$query = "INSERT INTO Ext_Queue_Members (FK_Extension, FK_Extension_Member, LoginRequired, QueueOrder) VALUES ({$data['PK_Extension']}, {$member['FK_Extension']}, {$member['LoginRequired']}, $QueueOrder)";
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

	// Check if first name is proper length
	if ((strlen($data['Name'])<1) || (strlen($data['Name'])>20)) {
		$errors['Name']['Invalid'] = true;
	}

	return $errors;
}

admin_run('Extensions_Queue_Modify', 'Admin.tpl');

?>
