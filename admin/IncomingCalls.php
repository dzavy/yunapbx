<?php
header("Cache-Control: no-cache");

include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function IncomingCalls() {
	session_start();
	$session = &$_SESSION['IncomingCalls'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$HiligthRule  = "";
	$HiligthRoute = "";

	// Add Incoming Rule
	if (isset($_REQUEST['add_rule']) && ($_REQUEST['Type'] == 'block' || $_REQUEST['Type'] == 'transfer')) {
		$query  = "SELECT MAX(RuleOrder) FROM IncomingRules";
		$result = mysql_query($query) or die(mysql_error());
		$row    = mysql_fetch_row($result);
		$RuleOrder = $row[0] + 1;

		$query = "
			INSERT INTO
				IncomingRules
			SET
				RuleOrder = $RuleOrder,
				RuleType = '{$_REQUEST['Type']}',
				Subject  = 'phone',
				Digits   = '0',
				BlockType = 'hangup',
				Extension = '000',
				FK_Timeframe = 0
		";
		mysql_query($query) or die(mysql_error());

		$HiligthRule = mysql_insert_id();
	}

	// Add Incoming Route
	if (isset($_REQUEST['add_route']) && ($_REQUEST['Type'] == 'single' || $_REQUEST['Type'] == 'multiple')) {
		$query = "
			INSERT INTO
				IncomingRoutes
			SET
				RouteType    = '{$_REQUEST['Type']}',
				ProviderType = 0,
				Extension    = '000'
		";

		mysql_query($query) or die(mysql_error());

		$HiligthRoute = mysql_insert_id();
	}

	// Incoming Rules (IncomingRules)
	$IncomingRules = array();
	$query  = "SELECT * FROM IncomingRules ORDER BY RuleOrder ASC";
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$IncomingRules[] = $row;
	}

	// Incoming Routes (IncomingRoutes)
	$IncomingRoutes = array();
	$query  = "SELECT * FROM IncomingRoutes ORDER BY RouteOrder DESC";
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$IncomingRoutes[] = $row;
	}

	// Timeframes
	$Timeframes = array();
	$query = "SELECT PK_Timeframe, Name FROM Timeframes WHERE FK_Extension = 0 ORDER BY Name";
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$Timeframes[] = $row;
	}

	// SipProviders
	$SipProviders = array();
	$query = "SELECT * FROM SipProviders WHERE ApplyIncomingRules = 1 ORDER BY Name";
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$SipProviders[] = $row;
	}

	// IaxProviders
	$IaxProviders = array();
	$query = "SELECT * FROM IaxProviders WHERE ApplyIncomingRules = 1 ORDER BY Name";
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$IaxProviders[] = $row;
	}

	$smarty->assign('IncomingRules' , $IncomingRules);
	$smarty->assign('IncomingRoutes', $IncomingRoutes);
	$smarty->assign('Timeframes'    , $Timeframes);
	$smarty->assign('SipProviders'  , $SipProviders);
	$smarty->assign('IaxProviders'  , $IaxProviders);
	$smarty->assign('HilightRule'   , $HiligthRule);
	$smarty->assign('HilightRoute'  , $HiligthRoute);

	return $smarty->fetch('IncomingCalls.tpl');
}

admin_run('IncomingCalls', 'Admin.tpl');
?>