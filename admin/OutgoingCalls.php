<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function OutgoingCalls() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['OutgoingCalls'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    if (isset($_REQUEST['submit']) && ($_REQUEST['submit'] == 'add_cid_rule' || $_REQUEST['submit'] == 'add_cids_rule')) {
        $query = "
			INSERT INTO
				OutgoingCIDRules
			SET

				Type            = '" . ($_REQUEST['submit'] == 'add_cid_rule' ? 'Single' : 'Multiple') . "',
				ExtensionStart  = 0,
				ExtensionEnd    = 0,
				FK_OutgoingRule = 0,
				`Add`           = 0,
				PrependDigits   = '',
				Name            = '',
				Number          = ''
		";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $HiligthRule = $db->lastInsertId();
    }

    // Outgoing Rules (OutgoingRules)
    $OutgoingRules = array();
    $query = "
		(SELECT
			PK_OutgoingRule,
			RuleOrder,
			BeginWith,
			OutgoingRules.Name,
			RestBetweenHigh,
			RestBetweenLow,
			'VoIP' AS ProviderType,
			Protected,
			SipProviders.Name AS ProviderName
		FROM
			OutgoingRules
        LEFT JOIN SipProviders ON PK_SipProvider = ProviderID
        WHERE ProviderType = 'SIP'
        )
        UNION
		(SELECT
			PK_OutgoingRule,
			RuleOrder,
			BeginWith,
			OutgoingRules.Name,
			RestBetweenHigh,
			RestBetweenLow,
			'3G Dongle' AS ProviderType,
			Protected,
			Dongles.Name AS ProviderName
		FROM
			OutgoingRules
        LEFT JOIN Dongles ON PK_Dongle = ProviderID
        WHERE ProviderType = 'DONGLE'
        )
        UNION
		(SELECT
			PK_OutgoingRule,
			RuleOrder,
			BeginWith,
			OutgoingRules.Name,
			RestBetweenHigh,
			RestBetweenLow,
			'internal' AS ProviderType,
			Protected,
			'' AS ProviderName
		FROM
			OutgoingRules
        WHERE ProviderType = 'INTERNAL'
        )
        
		ORDER BY
			RuleOrder ASC
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $OutgoingRules[] = $row;
    }

    // Outgoing Rules (OutgoingCIDRules)
    $OutgoingCIDRules = array();
    $query = "
		SELECT
				*
		FROM
			OutgoingCIDRules
		ORDER BY
			Type
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $OutgoingCIDRules[] = $row;
    }

    $smarty->assign('OutgoingRules', $OutgoingRules);
    $smarty->assign('OutgoingCIDRules', $OutgoingCIDRules);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('OutgoingCalls.tpl');
}

admin_run('OutgoingCalls', 'Admin.tpl');
?>
