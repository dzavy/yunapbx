<?php

header("Cache-Control: no-cache");

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function OutgoingCalls() {
    global $mysqli;
    
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
        $mysqli->query($query) or die($mysqli->error . $query);

        $HiligthRule = $mysqli->insert_id;
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
    $result = $mysqli->query($query) or die($mysqli->error);
    while ($row = $result->fetch_assoc()) {
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
    $result = $mysqli->query($query) or die($mysqli->error);
    while ($row = $result->fetch_assoc()) {
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
