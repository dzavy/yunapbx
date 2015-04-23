<?php
header("Cache-Control: no-cache");

include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function OutgoingCalls() {
	session_start();
	$session = &$_SESSION['OutgoingCalls'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init message (Message)
	$Message = $_REQUEST['msg'];

	if (isset($_REQUEST['submit']) && ($_REQUEST['submit'] == 'add_cid_rule' || $_REQUEST['submit'] == 'add_cids_rule')) {
		$query = "
			INSERT INTO
				OutgoingCIDRules
			SET

				Type            = '".($_REQUEST['submit']=='add_cid_rule'?'Single':'Multiple')."',
				ExtensionStart  = 0,
				ExtensionEnd    = 0,
				FK_OutgoingRule = 0,
				`Add`           = 0,
				PrependDigits   = '',
				Name            = '',
				Number          = ''
		";
		mysql_query($query) or die(mysql_error().$query);

		$HiligthRule = mysql_insert_id();
	}

	// Outgoing Rules (OutgoingRules)
	$OutgoingRules = array();
	$query  = "
		SELECT
			PK_OutgoingRule,
			RuleOrder,
			BeginWith,
			OutgoingRules.Name,
			RestBetweenHigh,
			RestBetweenLow,
			ProviderType,
			Protected,
			CONCAT(IFNULL(SipProviders.Name,''), IFNULL(IaxProviders.Name,'')) AS ProviderName
		FROM
			OutgoingRules
			LEFT JOIN SipProviders ON PK_SipProvider = ProviderID AND ProviderType = 'SIP'
			LEFT JOIN IaxProviders ON PK_IaxProvider = ProviderID AND ProviderType = 'IAX'
		ORDER BY
			RuleOrder ASC
	";
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$OutgoingRules[] = $row;
	}

	// Outgoing Rules (OutgoingCIDRules)
	$OutgoingCIDRules = array();
	$query  = "
		SELECT
				*
		FROM
			OutgoingCIDRules
		ORDER BY
			Type
	";
	$result = mysql_query($query) or die(mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$OutgoingCIDRules[] = $row;
	}

	$smarty->assign('OutgoingRules'   , $OutgoingRules);
	$smarty->assign('OutgoingCIDRules', $OutgoingCIDRules);
	$smarty->assign('Message'         , $Message);
	$smarty->assign('Hilight'         , $_REQUEST['hilight']);

	return $smarty->fetch('OutgoingCalls.tpl');
}

admin_run('OutgoingCalls', 'Admin.tpl');
?>
