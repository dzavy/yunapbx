<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Recordings_List() {
	session_start();
	$session = &$_SESSION['Recordings_List'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init no element on page (PageSize)
	$Rules['PageSize'] = 50;

	// Init sort order (Order)
	if ($session['Rules']['Sort'] == $_REQUEST['Rules']['Sort']) {
		$Rules['Order'] = ($session['Rules']['Order']=="asc"?"desc":"asc");
	} elseif ($session['Rules']['Sort'] != $_REQUEST['Rules']['Sort']) {
		$Rules['Order'] = 'asc';
	}
	$session['Rules']['Order'] = $Rules['Order'];

	// Init sort field (Sort)
	if(isset($_REQUEST['Rules']['Sort'])) {
		$Rules['Sort'] = $_REQUEST['Rules']['Sort'];
	} else {
		$Rules['Sort'] = 'DateCreated';
	}
	$session['Rules']['Sort'] = $Rules['Sort'];

	// Init listing start (Start)
	if(isset($_REQUEST['Rules']['Start'])) {
		$Rules['Start'] = $_REQUEST['Rules']['Start'];
	} else {
		$Rules['Start'] = 0;
	}

	// Init total entries (Total)
	$query  = "SELECT COUNT(PK_Rule) FROM RecordingRules;";
	$result = mysql_query($query) or die(mysql_error());
	$row    = mysql_fetch_array($result);
	$Rules['Total'] = $row[0];

	// Init table fields (Rules)
	$Rules['Fields'] = array();
	$query = "
		SELECT
			*,
			DATE_FORMAT(DateCreated,'%m/%d/%y, %h:%i %p') AS DateCreated
		FROM
			RecordingRules
		ORDER BY
			{$Rules['Sort']} {$Rules['Order']}
		LIMIT {$Rules['Start']}, {$Rules['PageSize']}
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$Rules['Fields'][] = $row;
	}

	// Init end record (End)
	$Rules['End'] = $Rules['Start'] + count($Rules['Fields']);

	$smarty->assign('Rules', $Rules);
	$smarty->assign('Hilight', $_REQUEST['hilight']);


	include(dirname(__FILE__).'/../include/config.inc.php');
	$df_output = exec("df -P '".$conf['dirs']['monitor']."' | tail -n 1 | awk '{print \$2,\$3,\$4}'");
	$du_outpt  = exec("du -s '".$conf['dirs']['monitor']."' | awk '{print $1}'");
	$df_output = explode(' ', $df_output);

	$Disk['Size']['Bytes']  = $df_output[0];
	$Disk['Used']['Bytes']  = $df_output[1];
	$Disk['Avail']['Bytes'] = $df_output[2];
	$Disk['Rec']['Bytes']   = $du_outpt;
	$Disk['Other']['Bytes'] = $Disk['Used']['Bytes'] - $Disk['Rec']['Bytes'];

	$Disk['Size']['Percent']  = ($Disk['Size']['Bytes'] * 100)/$Disk['Size']['Bytes'];
	$Disk['Used']['Percent']  = ($Disk['Used']['Bytes'] * 100)/$Disk['Size']['Bytes'];
	$Disk['Avail']['Percent'] = ($Disk['Avail']['Bytes'] * 100)/$Disk['Size']['Bytes'];
	$Disk['Rec']['Percent']   = ($Disk['Rec']['Bytes'] * 100)/$Disk['Size']['Bytes'];
	$Disk['Other']['Percent'] = ($Disk['Other']['Bytes'] * 100)/$Disk['Size']['Bytes'];

	$Disk['Size']['HSize']  = fileSizeInfo($Disk['Size']['Bytes']);
	$Disk['Used']['HSize']  = fileSizeInfo($Disk['Used']['Bytes']);
	$Disk['Avail']['HSize'] = fileSizeInfo($Disk['Avail']['Bytes']);
	$Disk['Rec']['HSize']   = fileSizeInfo($Disk['Rec']['Bytes']);
	$Disk['Other']['HSize'] = fileSizeInfo($Disk['Other']['Bytes']);

	$smarty->assign('Disk', $Disk);

//////////////////////////////////////////



	// Init no element on page (PageSize)
	$Calls['PageSize'] = 50;

	// Init sort order (Order)
	if ($session['Calls']['Sort'] == $_REQUEST['Calls']['Sort']) {
		$Calls['Order'] = ($session['Calls']['Order']=="asc"?"desc":"asc");
	} elseif ($session['Calls']['Sort'] != $_REQUEST['Calls']['Sort']) {
		$Calls['Order'] = 'asc';
	}
	$session['Calls']['Order'] = $Calls['Order'];

	// Init sort field (Sort)
	if(isset($_REQUEST['Calls']['Sort'])) {
		$Calls['Sort'] = $_REQUEST['Calls']['Sort'];
	} else {
		$Calls['Sort'] = 'DateCreated';
	}
	$session['Calls']['Sort'] = $Calls['Sort'];

	// Init listing start (Start)
	if(isset($_REQUEST['CaMoh_Fileslls']['Start'])) {
		$Calls['Start'] = $_REQUEST['Calls']['Start'];
	} else {
		$Calls['Start'] = 0;
	}

	// Init total entries (Total)
	$query  = "SELECT COUNT(*) FROM RecordingLog;";
	$result = mysql_query($query) or die(mysql_error());
	$row    = mysql_fetch_array($result);
	$Calls['Total'] = $row[0];

	// Init table fields (Calls)
	$Calls['Fields'] = array();
	$query = "
		SELECT
			*,
			DATE_FORMAT(StartDate,'%m/%d/%y, %h:%i:%s %p') AS DateCreated
		FROM
			RecordingLog
		ORDER BY
			{$Calls['Sort']} {$Calls['Order']}
		LIMIT {$Calls['Start']}, {$Calls['PageSize']}
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$Calls['Fields'][] = $row;
	}

	// Init end record (End)
	$Calls['End'] = $Calls['Start'] + count($Calls['Fields']);

	$smarty->assign('Calls', $Calls);

	return $smarty->fetch('Recordings_List.tpl');


}

function fileSizeInfo($size, $retstring = '%01.1f %s')
{
    $sizes = array('k', 'mb', 'gb', 'tb', 'pb');
    $mod   = 1024;

	$depth = count($sizes) - 1;

    // Loop
    $i = 0;
    while ($size >= 1024 && $i < $depth) {
        $size /= $mod;
        $i++;
    }
    return sprintf($retstring, $size, $sizes[$i]);
}

admin_run('Recordings_List', 'Admin.tpl');
?>
