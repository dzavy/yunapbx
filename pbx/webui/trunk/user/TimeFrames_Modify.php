<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/user_utils.inc.php');

function TimeFrames_Modify() {
	session_start();
	$session = &$_SESSION['TimeModify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Message
	$Message = $_REQUEST['msg'];

	// Save if requested
	if (isset($_REQUEST['add'])) {
		$Interval = formdata_from_post();
		$Errors   = formdata_validate($Interval);

		if (count($Errors) == 0 ) {
			formdata_save($Interval);
			$Interval = array();
			$Message = "ADD_INTERVAL";
		}
	}

	// Delete if requested
	if (isset($_REQUEST['del'])) {
		$query = "DELETE FROM Timeframe_Intervals WHERE PK_Interval = {$_REQUEST['PK_Interval']} LIMIT 1";
		mysql_query($query) or die(mysql_error().$query);
		if (mysql_affected_rows()) {
			$Message = "DELETE_INTERVAL";
		}
	}

	// See if user is alowed to update this timeframe
	$query = "
		SELECT
			*
		FROM
			Timeframes
		WHERE
			PK_Timeframe  = {$_REQUEST['FK_Timeframe']}
			AND
			FK_Extension = '".mysql_real_escape_string($_SESSION['_USER']['PK_Extension'])."'
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	if (mysql_numrows($result) != 1) {
		$ReadOnly = true;	
	} else {
		$ReadOnly = false;
	}
	
	// Get existing intervals
	$query  = "
		SELECT
			*,
			DATE_FORMAT(StartDate, '%Y/%m/%d') AS StartDate,
			DATE_FORMAT(EndDate, '%Y/%m/%d')   AS EndDate
		FROM
			Timeframe_Intervals
		WHERE
			FK_Timeframe = {$_REQUEST['FK_Timeframe']}
		ORDER BY
			OrderDummy
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$Intervals = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Intervals[] = $row;
	}

	$Interval['FK_Timeframe'] = $_REQUEST['FK_Timeframe'];

	$smarty->assign('Interval' , $Interval);
	$smarty->assign('ReadOnly' , $ReadOnly);
	$smarty->assign('Intervals', $Intervals);
	$smarty->assign('Errors'   , $Errors);
	$smarty->assign('Message'  , $Message);

	return $smarty->fetch('TimeFrames_Modify.tpl');
}

function formdata_from_post() {
	return $_POST;
}

function formdata_validate($data) {
	$errors = array();

	// Test StartDate
	if ($data['StartDate'] != "") {
		$Date = explode('/',$data['StartDate']);
		if (count($Date) != 3) {
			$errors['StartDate']['Invalid'] = true;
		} elseif (intval($Date[0])."" != "$Date[0]") {
			$errors['StartDate']['Invalid'] = true;
		} elseif (intval($Date[1])."" != "$Date[1]") {
			$errors['StartDate']['Invalid'] = true;
		} elseif (intval($Date[2])."" != "$Date[2]") {
			$errors['StartDate']['Invalid'] = true;
		} elseif ($Date[0] < 0 ) {
			$errors['StartDate']['Invalid'] = true;
		} elseif ($Date[1] < 0 || $Date[1] > 12 ) {
			$errors['StartDate']['Invalid'] = true;
		} elseif ($Date[2] < 0 || $Date[2] > 31 ) {
			$errors['StartDate']['Invalid'] = true;
		}
	}

	// Test EndDate
	if ($data['EndDate'] != "") {
		$Date = explode('/',$data['EndDate']);
		if (count($Date) != 3) {
			$errors['EndDate']['Invalid'] = true;
		} elseif (intval($Date[0])."" != "$Date[0]") {
			$errors['EndDate']['Invalid'] = true;
		} elseif (intval($Date[1])."" != "$Date[1]") {
			$errors['EndDate']['Invalid'] = true;
		} elseif (intval($Date[2])."" != "$Date[2]") {
			$errors['EndDate']['Invalid'] = true;
		} elseif ($Date[0] < 0 ) {
			$errors['EndDate']['Invalid'] = true;
		} elseif ($Date[1] < 0 || $Date[1] > 12 ) {
			$errors['EndDate']['Invalid'] = true;
		} elseif ($Date[2] < 0 || $Date[2] > 31 ) {
			$errors['EndDate']['Invalid'] = true;
		}
	}

	// Test StartTime
	if ($data['StartTime'] != "") {
		$Time = explode(':', $data['StartTime']);
		if (count($Time) != 2) {
			$errors['StartTime']['Invalid'] = true;
		} elseif (intval($Time[0])."" != "$Time[0]") {
			$errors['StartTime']['Invalid'] = true;
		} elseif (intval($Time[1])."" != "$Time[1]") {
			$errors['StartTime']['Invalid'] = true;
		} elseif ($Time[0] < 0 || $Time[0] > 12 ) {
			$errors['StartTime']['Invalid'] = true;
		} elseif ($Time[1] < 0 || $Time[1] > 60 ) {
			$errors['StartTime']['Invalid'] = true;
		}
	}

	// Test StartTime
	if ($data['EndTime'] != "") {
		$Time = explode(':', $data['EndTime']);
		if (count($Time) != 2) {
			$errors['EndTime']['Invalid'] = true;
		} elseif (intval($Time[0])."" != "$Time[0]") {
			$errors['EndTime']['Invalid'] = true;
		} elseif (intval($Time[1])."" != "$Time[1]") {
			$errors['EndTime']['Invalid'] = true;
		} elseif ($Time[0] < 0 || $Time[0] > 12 ) {
			$errors['EndTime']['Invalid'] = true;
		} elseif ($Time[1] < 0 || $Time[1] > 60 ) {
			$errors['EndTime']['Invalid'] = true;
		}
	}

	if ($data['StartTime']!= "" && $data['EndTime']== "" ) {
		$errors['EndTime']['Missing'] = true;
	}
	if ($data['StartTime']== "" && $data['EndTime']!= "" ) {
		$errors['StartTime']['Missing'] = true;
	}

	if ($data['StartDay']!= "" && $data['EndDay']== "" ) {
		$errors['EndDay']['Missing'] = true;
	}
	if ($data['StartDay']== "" && $data['EndDay']!= "" ) {
		$errors['StartDay']['Missing'] = true;
	}

	if ($data['StartDate']!= "" && $data['EndDate']== "" ) {
		$errors['EndDate']['Missing'] = true;
	}
	if ($data['StartDate']== "" && $data['EndDate']!= "" ) {
		$errors['StartDate']['Missing'] = true;
	}

	return $errors;
}

function formdata_save($data) {
	// See if user is alowed to update this timeframe
	$query = "
		SELECT
			*
		FROM
			Timeframes
		WHERE
			PK_Timeframe  = {$data['FK_Timeframe']}
			AND
			FK_Extension = '".mysql_real_escape_string($_SESSION['_USER']['PK_Extension'])."'
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	if (mysql_numrows($result) != 1) {
		return;	
	}
	
	$query = "INSERT INTO Timeframe_Intervals() VALUES()";
	mysql_query($query) or die(mysql_error().$query);
	$data['PK_Interval'] = mysql_insert_id();

	$query = "
		UPDATE
			Timeframe_Intervals
		SET
			FK_Timeframe  = ".intval($data['FK_Timeframe']).",
			StartDate     = '".mysql_real_escape_string($data['StartDate'])."',
			EndDate       = '".mysql_real_escape_string($data['EndDate'])."',
			StartDay      = ".intval($data['StartDay']).",
			EndDay        = ".intval($data['EndDay']).",
			StartTime     = '".mysql_real_escape_string($data['StartTime'])."',
			EndTime       = '".mysql_real_escape_string($data['EndTime'])."',
			StartTimeMode = '".mysql_real_escape_string($data['StartTimeMode'])."',
			EndTimeMode   = '".mysql_real_escape_string($data['EndTimeMode'])."'
		WHERE
			PK_Interval  = {$data['PK_Interval']}
	";
	mysql_query($query) or die(mysql_error().$query);
}

user_run('TimeFrames_Modify', 'User.tpl');