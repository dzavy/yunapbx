#!/usr/bin/php -q
<?php
set_time_limit(30);
error_reporting(E_ALL);

require(dirname(__FILE__).'/../lib/phpagi/phpagi.php');
require(dirname(__FILE__).'/../include/db_utils.inc.php');
require(dirname(__FILE__).'/common/AGI_Logger.class.php');
require(dirname(__FILE__).'/common/AGI_CDR.class.php');

$agi    = new AGI();
$logger = new AGI_Logger($agi);
$cdr    = new AGI_CDR($agi);

// CDR: Set call type
$cdr->set_type('IN');

// CDR: Set caller name and number
$cid = $agi->parse_callerid();

$caller_ext  = $cid['username'];
$caller_name = $agi->request['agi_calleridname'];
$cdr->set_caller('', '', $caller_name, $caller_ext);

if($agi->request['agi_type'] == 'SIP') {
	preg_match('/^sip_provider_(\d+)/', $agi->request['agi_context'], $matches);
	$PK_SipProvider = $matches[1];

	$query  = "SELECT Name FROM SipProviders WHERE PK_SipProvider=$PK_SipProvider LIMIT 1";
	$result = $mysqli->query($query) or $agi->verbose($mysqli->error.$query);
	$prov   = $result->fetch_assoc();

	$cdr->push_event("INPROVIDER", "SIP,{$prov['Name']}");

} elseif ($agi->request['agi_type'] == 'IAX2') {
	preg_match('/^iax_provider_(\d+)/', $agi->request['agi_context'], $matches);
	$PK_IaxProvider = $matches[1];

	$query  = "SELECT Name FROM IaxProviders WHERE PK_IaxProvider=$PK_IaxProvider LIMIT 1";
	$result = $mysqli->query($query) or $agi->verbose($mysqli->error.$query);
	$prov   = $result->fetch_assoc();

	$cdr->push_event("INPROVIDER", "IAX,{$prov['Name']}");
}
/* Try to match a Incoming Call Rule */
run_incoming_call_rules($agi);

/* Try to call the Callback Extension of the Provider */
run_callback_extension($agi);

/* Call the Operator */
$agi->exec_goto('internal', '301', 1);

function run_incoming_call_rules($agi) {
	$cid = $agi->parse_callerid();

	/* Get the first rule that this call matched */
	$query = "
		SELECT
			PK_IncomingRule,
			RuleType,
			Extension,
			BlockType
		FROM
			IncomingRules
			LEFT JOIN Timeframe_Intervals ON Timeframe_Intervals.FK_Timeframe = IncomingRules.FK_Timeframe
		WHERE
			(
				ValidTimeInterval(PK_Interval)
				OR
				IncomingRules.FK_Timeframe = 0
			)
			AND
			(
				(Subject = 'phone'  AND Digits = '{$cid['username']}')
				OR
				(Subject = 'prefix' AND '{$cid['username']}' LIKE CONCAT(Digits,'%'))
			)
		ORDER BY
			RuleOrder ASC
		LIMIT 1
	";
	$res = $mysqli->query($query) or $agi->verbose($mysqli->error.$query);

	/* Iterate the matching rules */
	while ($row = $mysqli->fetch_assoc($res)) {
		$agi->verbose('Incoming Rule : '.$row['PK_IncomingRule']);

		/* If the rule requested a block */
		if ($row['RuleType'] == 'block') {
			switch ($row['RuleType']) {
				case 'busy':
					$agi->hangup();
					break;
				case 'congestion':
					$agi->hangup();
					break;
				case 'hangup':
					$agi->hangup();
					break;
				default:
					$agi->hangup();
			}
			die();

		/* If the rule requested a transfer */
		} elseif ($row['RuleType'] == 'transfer') {
			$agi->exec_goto('internal', $row['Extension'], 1);
			die();
		}
	}
}

function run_callback_extension($agi) {
	if($agi->request['agi_type'] == 'SIP') {
		preg_match('/^sip_provider_(\d+)/', $agi->request['agi_context'], $matches);
		$PK_SipProvider = $matches[1];
		$query = "SELECT CallbackExtension FROM SipProviders WHERE PK_SipProvider = $PK_SipProvider LIMIT 1";
		$res   = $mysqli->query($query) or die($mysqli->error);
		$row   = $mysqli->fetch_assoc($res);
		$CallbackExtension = $row['CallbackExtension'];

	} elseif ($agi->request['agi_type'] == 'IAX2') {
		preg_match('/^iax_provider_(\d+)/', $agi->request['agi_context'], $matches);
		$PK_IaxProvider = $matches[1];
		$query = "SELECT CallbackExtension FROM IaxProviders WHERE PK_IaxProvider = $PK_IaxProvider LIMIT 1";
		$res   = $mysqli->query($query) or die($mysqli->error);
		$row   = $mysqli->fetch_assoc($res);
		$CallbackExtension = $row['CallbackExtension'];
	}

	if ($CallbackExtension != "") {
		$agi->verbose('Dialing callback extension: '.$CallbackExtension);
		$agi->exec_goto('internal', $CallbackExtension, 1);
		die();
	}
}
?>
