<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');


function Extensions_ParkingLot_Modify() {
	session_start();
	$session = &$_SESSION['Extensions_ParkingLot_Modify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init message (Message)
	$Message = $_REQUEST['msg'];

	if (@$_REQUEST['submit'] == 'save') {
		$Extension = formdata_from_post();
		$Errors    = formdata_validate($Extension);

		if (count($Errors) == 0) {
			asterisk_UpdateConf('features.conf');
			asterisk_Reload();

			if ($Extension['PK_Extension'] != '') {
				$msg = 'MODIFY_PARKINGLOT_EXTENSION';
			} else {
				$msg = 'ADD_PARKINGLOT_EXTENSION';
			}
			$id = formdata_save($Extension);

			header("Location: Extensions_List.php?hilight={$id}&msg={$msg}");
			die();
		}

	} elseif (@$_REQUEST['PK_Extension'] != "") {
		$Extension = formdata_from_db($_REQUEST['PK_Extension']);
	} else {
		$query  = "SELECT PK_Extension FROM Extensions WHERE Type = 'ParkingLot' LIMIT 1";
		$result = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($result) == 1) {
			$row = mysql_fetch_row($result);
			$id  = $row[0];
			header("Location: Extensions_List.php?errmsg=CONFLICT_PARKINGLOT_EXTENSION&hilight={$id}");
			die();
		}
	}

	$smarty->assign('Extension', $Extension);
	$smarty->assign('Errors'   , $Errors);

	return $smarty->fetch('Extensions_ParkingLot_Modify.tpl');
}

function formdata_from_db($id) {
	$query  = "
		SELECT
			*
		FROM
			Ext_ParkingLot
			INNER JOIN Extensions ON Extensions.PK_Extension = Ext_ParkingLot.PK_Extension
		WHERE
			Ext_ParkingLot.PK_Extension = '$id'
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
		$query = "INSERT INTO Extensions(Type, Extension) VALUES('ParkingLot', '".mysql_real_escape_string($data['Extension'])."')";
		mysql_query($query) or die(mysql_error().$query);
		$data['PK_Extension'] = mysql_insert_id();

		$query = "INSERT INTO Ext_ParkingLot(PK_Extension) VALUES({$data['PK_Extension']})";
		mysql_query($query) or die(mysql_error().$query);
	}

	// Update 'Ext_ParkingLot'
	$query = "
		UPDATE
			Ext_ParkingLot
		SET
			Start   = '".mysql_real_escape_string($data['Start'])."',
			Stop    = '".mysql_real_escape_string($data['Stop'])."',
			Timeout = '".mysql_real_escape_string($data['Timeout'])."'
		WHERE
			PK_Extension = ".mysql_real_escape_string($data['PK_Extension'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);

	$query = "DELETE FROM Extensions WHERE Type = 'ParkingLot_Reserved'";
	mysql_query($query) or die(mysql_error().$query);
	for($extension = $data['Start']; $extension <= $data['Stop']; $extension++) {
		$query = "INSERT INTO Extensions(Type, Extension) VALUES('ParkingLot_Reserved', '{$extension}')";
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

	// Check that parking timeout it's a valid number (1-999)
	if (! preg_match('/^[1-9]{1}[0-9]{0,3}$/', $data['Timeout'])) {
		$errors['Timeout']['Invalid'] = true;
	}

	// Check that parking lot start extension is valid
	if (! preg_match('/^[0-9]{3,5}$/', $data['Start'])) {
		$errors['Start']['Invalid'] = true;
	}

	// Check that parking lot stop extension is valid
	if (! preg_match('/^[0-9]{3,5}$/', $data['Stop'])) {
		$errors['Stop']['Invalid'] = true;
	}

	// Check that parking lot's stop extension is greater that the start
	if (empty($errors['Stop']) && empty($errors['Start'])) {
		if (intval($data['Start']) > intval($data['Stop'])) {
			$errors['Stop']['TooSmall'] = true;
		}
	}

	// Check if the parking extensions are empty
	if (empty($errors['Stop']) && empty($errors['Start'])) {
		for($extension = $data['Start']; $extension <= $data['Stop']; $extension++) {
			$query  = "SELECT Extension FROM Extensions WHERE Extension = '$extension' AND NOT Type = 'ParkingLot_Reserved' LIMIT 1";
			$result = mysql_query($query) or die(mysql_error().$query);
			if (mysql_num_rows($result) > 0) {
				$errors['Stop']['Conflict'] = true;
				$errors['Start']['Conflict'] = true;
				$errors['Conflicts'][] = $extension;
			}
		}
	}

	return $errors;
}

admin_run('Extensions_ParkingLot_Modify', 'Admin.tpl');
?>