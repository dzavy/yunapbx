<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');


function CallLog() {
	session_start();
	$session = &$_SESSION['CallLog'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init no element on page (PageSize)
	$PageSize = 50;

	// Init sort order (Order)
	if ($session['Sort'] == $_REQUEST['Sort']) {
		$Order = ($session['Order']=="asc"?"desc":"asc");
	} elseif ($session['Sort'] != $_REQUEST['Sort']) {
		$Order = 'desc';
	}
	$session['Order'] = $Order;

	// Init sort field (Sort)
	if(isset($_REQUEST['Sort'])) {
		$Sort = $_REQUEST['Sort'];
	} else {
		$Sort = 'StartDate';
	}
	$session['Sort'] = $Sort;

	// Init listing start (Start)
	if(isset($_REQUEST['Start'])) {
		$Start = $_REQUEST['Start'];
	} else {
		$Start = 0;
	}

	// Init Filter
	$Filter = init_filter();

	// Init total entries (Total)
	$query  = "
		SELECT COUNT(*) FROM CallLog
		WHERE
			DATE(StartDate) >= STR_TO_DATE('".mysql_real_escape_string($Filter['StartDate'])."','%m/%d/%Y')
			AND
			DATE(StartDate) <= STR_TO_DATE('".mysql_real_escape_string($Filter['EndDate'])."','%m/%d/%Y')
	";
	$result = mysql_query($query) or die(mysql_error());
	$row    = mysql_fetch_array($result);
	$Total  = $row[0];

	// Init table fields (CDRs)
	$CDRs = array();
	$query = "
		SELECT
			*,
			DATE_FORMAT(StartDate,'%m/%d/%y %h:%i:%s %p') AS StartDate_Formated
		FROM
			CallLog
		WHERE
			DATE(StartDate) >= STR_TO_DATE('".mysql_real_escape_string($Filter['StartDate'])."','%m/%d/%Y')
			AND
			DATE(StartDate) <= STR_TO_DATE('".mysql_real_escape_string($Filter['EndDate'])."','%m/%d/%Y')
		ORDER BY
			$Sort $Order
		LIMIT $Start, $PageSize
	";

	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$CDRs[] = $row;
	}

	// Init end record (End)
	$End = count($CDRs);

	$smarty->assign('CDRs'     , $CDRs);
	$smarty->assign('Filter'   , $Filter);
	$smarty->assign('Sort'     , $Sort);
	$smarty->assign('Order'    , $Order);
	$smarty->assign('Start'    , $Start);
	$smarty->assign('End'      , $End);
	$smarty->assign('Total'    , $Total);
	$smarty->assign('PageSize'  , $PageSize);

	return $smarty->fetch('CallLog.tpl');
}

function init_filter() {
	$filter = array();

	$session = &$_SESSION['CallLog'];

	if (isset($_GET['StartDate'])) {
		$filter['StartDate'] = $_GET['StartDate'];
	} elseif (isset($session['StartDate'])) {
		$filter['StartDate'] = $session['StartDate'];
	} else {
		$filter['StartDate'] = date('m/d/Y', time());
	}
	$session['StartDate'] = $filter['StartDate'];

	if (isset($_GET['EndDate'])) {
		$filter['EndDate'] = $_GET['EndDate'];
	} elseif (isset($session['EndDate'])) {
		$filter['EndDate'] = $session['EndDate'];
	} else {
		$filter['EndDate'] = date('m/d/Y', time());
	}
	$session['EndDate'] = $filter['EndDate'];

	return $filter;
}


admin_run('CallLog', 'Admin.tpl');

?>