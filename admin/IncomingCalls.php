<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function IncomingCalls() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['IncomingCalls'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $HiligthRule = "";
    $HiligthRoute = "";

    // Add Incoming Rule
    if (isset($_REQUEST['add_rule']) && ($_REQUEST['Type'] == 'block' || $_REQUEST['Type'] == 'transfer')) {
        $query = "SELECT MAX(RuleOrder) FROM IncomingRules";
        $result = $db->query($query) or die(print_r($db->errorInfo(), true));
        $row = $result->fetch_row();
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
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $HiligthRule = $db->lastInsertId();
    }

    // Add Incoming Route
    if (isset($_REQUEST['add_route']) && ($_REQUEST['Type'] == 'single' || $_REQUEST['Type'] == 'multiple')) {
        $query = "
			INSERT INTO
				IncomingRoutes
			SET
				RouteType    = '{$_REQUEST['Type']}',
				ProviderType = 0,
				Extension    = '0000'
		";

        $db->query($query) or die(print_r($db->errorInfo(), true));

        $HiligthRoute = $db->lastInsertId();
    }

    // Incoming Rules (IncomingRules)
    $IncomingRules = array();
    $query = "SELECT * FROM IncomingRules ORDER BY RuleOrder ASC";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $IncomingRules[] = $row;
    }

    // Incoming Routes (IncomingRoutes)
    $IncomingRoutes = array();
    $query = "SELECT * FROM IncomingRoutes ORDER BY RouteOrder DESC";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $IncomingRoutes[] = $row;
    }

    // Timeframes
    $Timeframes = array();
    $query = "SELECT PK_Timeframe, Name FROM Timeframes WHERE FK_Extension = 0 ORDER BY Name";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Timeframes[] = $row;
    }

    // SipProviders
    $SipProviders = array();
    $query = "SELECT * FROM SipProviders WHERE ApplyIncomingRules = 1 ORDER BY Name";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $SipProviders[] = $row;
    }

    // IaxProviders
    $Dongles = array();
    $query = "SELECT * FROM Dongles WHERE ApplyIncomingRules = 1 ORDER BY Name";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Dongles[] = $row;
    }

    $smarty->assign('IncomingRules', $IncomingRules);
    $smarty->assign('IncomingRoutes', $IncomingRoutes);
    $smarty->assign('Timeframes', $Timeframes);
    $smarty->assign('SipProviders', $SipProviders);
    $smarty->assign('Dongles', $Dongles);
    $smarty->assign('HilightRule', $HiligthRule);
    $smarty->assign('HilightRoute', $HiligthRoute);

    return $smarty->fetch('IncomingCalls.tpl');
}

admin_run('IncomingCalls', 'Admin.tpl');
?>