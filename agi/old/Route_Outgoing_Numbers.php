#!/usr/bin/php -q
<?php
require(dirname(__FILE__).'/../lib/phpagi/phpagi.php');
require(dirname(__FILE__).'/../include/db_utils.inc.php');
require(dirname(__FILE__).'/common/AGI_Logger.class.php');
require(dirname(__FILE__).'/common/AGI_CDR.class.php');
require(dirname(__FILE__).'/common/AGI_Utils.php');

function get_callerid($agi, $FK_OugoingRule, $Extension) {

	// First read the callerid from the channel
	$result = $agi->get_variable('CALLERID(name)');
	$callerid['Name']   = $result['data'];

	$result = $agi->get_variable('CALLERID(number)');
	$callerid['Number'] = $result['data'];

	// Second try to see if we have a matching rule
	$query = "
		SELECT
			*
		FROM
			OutgoingCIDRules
		WHERE
			(
				(
					`Type` = 'Single'
					AND
					ExtensionStart = '".$mysqli->real_escape_string($Extension)."'
				) OR (
					`Type` = 'Multiple'
					AND
					ExtensionStart <= '".$mysqli->real_escape_string($Extension)."'
					AND
					ExtensionEnd   >= '".$mysqli->real_escape_string($Extension)."'
				)
			) AND (
				FK_OutgoingRule = 0
				OR
				FK_OutgoingRule = '".$mysqli->real_escape_string($FK_OugoingRule)."'
			)
		ORDER BY
			PK_OutgoingCIDRule ASC
		LIMIT 1
	";
	$result = $mysqli->query($query) or $agi->verbose($mysqli->error().$query);
	if ($mysqli->numrows($result) == 1) {
		$rule = $result->fetch_assoc();
		if ($rule['Type'] == 'Single') {
			if( !empty($rule['Name'])   ) { $callerid['Name']   = $rule['Name'];   }
			if( !empty($rule['Number']) ) { $callerid['Number'] = $rule['Number']; }

		} elseif ($rule['Type'] == 'Multiple') {
			$callerid['Number'] = $callerid['Number'] + $rule['Add'];
			$callerid['Number'] = $rule['PrependDigits'].$callerid['Number'];
		}
	}

	return $callerid;
}

$agi    = new AGI();
$logger = new AGI_Logger($agi);
$cdr    = new AGI_CDR($agi);

//CDR : Flush existing entry if both called and caller are known
if ($cdr->caller_known() && $cdr->called_known()) {
	$cdr->flush_cdr();
}

//CDR : set entry type
$cdr->set_type('OUT');

// Get Called Extension informations
$called_ext   = $agi->request['agi_extension'];

// Get Caller Extension infomation
$cid = $agi->parse_callerid();
$caller_ext  = $cid['username'];
$Extension_S = DB_Extension($caller_ext);

//CDR: Set caller information
$cdr->set_caller($Extension_S['PK_Extension'], $Extension_S['Type'], DB_Extension_Name($caller_ext), $caller_ext);

//CDR: Set called information
$cdr->set_called(0, '', '', $called_ext);

// CDR: Push dial event
$cdr->push_event("DIAL", $called_ext);

// See if we can match any outgoing rule
$query = "SELECT * FROM OutgoingRules ORDER BY RuleOrder";
$result = $mysqli->query($query) or $agi->verbose($mysqli->error().$query);
while ($rule = $result->fetch_assoc()) {
	// Check if rule matches
	$regex = "/^{$rule['BeginWith']}[0-9]{{$rule['RestBetweenLow']},{$rule['RestBetweenHigh']}}/";
	if (! preg_match($regex, $called_ext)) {
		continue;
	}

	// Create new_number
	$new_number = substr($called_ext, $rule['TrimFront']);
	$new_number = $new_number.$rule['PrependDigits'];
	break;
}
// Exit if no rule was matched
if ($new_number == "") {
	$agi->verbose('Cannot match any outgoing rule for number :'.$called_ext);
	exit(0);
}

// Detect and Set the caller id information that we should set for this call
$CallerID = get_callerid($agi, $rule['PK_OutgoingRule'], $caller_ext);
$agi->set_variable('CALLERID(name)'  , $CallerID['Name']);
$agi->set_variable('CALLERID(number)', $CallerID['Number']);


// If matched rules ask us to route the number trought a SIP provider
if ($rule['ProviderType'] == 'SIP') {
	$agi->verbose("CALLING {$new_number} USING RULE {$rule['Name']}  AND SIP PROVIDER [{$rule['ProviderID']}]");

	// Get the needed information about this sip provider
	$query = "SELECT * FROM `SipProviders` WHERE PK_SipProvider = '{$rule['ProviderID']}' LIMIT 1";
	$result = $mysqli->query($query) or die($mysqli->error().$query);
	if ($mysqli->num_rows($result) != '1') {
		exit(0);
	}
	$SipProvider = $result->fetch_assoc();

	// If providers requires the P-Asserted-Identity for caller id setup
	if ($SipProvider['CallerIDMethod'] == 'P-Asserted-Identity') {
		$agi->exec('SipAddHeader', "P-Asserted-Identity:\ '".str_replace(' ', '\ ',$CallerID['Name'])."'\ <sip:{$CallerID['Number']}>");
	}

	//$cdr->push_event("OUTPROVIDER", "SIP,{$SipProvider['Name']},$new_number");

	if ($SipProvider['DTMFDial']) {
		$agi->exec_dial("SIP/sip_provider_{$rule['ProviderID']}", $new_number, 60, "D(ww$new_number)");
	} else {
		$agi->exec('Dial', array("SIP/sip_provider_{$rule['ProviderID']}/{$new_number}",60));
		//$agi->exec_dial("SIP/sip_provider_{$rule['ProviderID']}", $new_number, 60);
	}

// If matched rules ask us to route the number trought a IAX provider
} elseif ($new_number != "" && $rule['ProviderType'] == 'IAX') {
	$agi->verbose("CALLING $new_number USING RULE {$rule['Name']} AND IAX PROVIDER [{$rule['ProviderID']}]");

	// Get the needed information about this sip provider
	$query = "SELECT * FROM `IaxProviders` WHERE PK_IaxProvider = '{$rule['ProviderID']}' LIMIT 1";
	$result = $mysqli->query($query) or die($mysqli->error().$query);
	if ($mysqli->num_rows($result) != '1') {
		exit(0);
	}
	$IaxProvider = $result->fetch_assoc();

	$cdr->push_event("OUTPROVIDER", "IAX,{$IaxProvder['Name']},$new_number");

	//Execute the dial
	$agi->exec_dial("IAX2/{$IaxProvider['Label']}", $new_number, 60);
}
?>
