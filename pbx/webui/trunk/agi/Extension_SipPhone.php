#!/usr/bin/php -q
<?php
require(dirname(__FILE__).'/../lib/phpagi/phpagi.php');
require(dirname(__FILE__).'/../include/db_utils.inc.php');
require(dirname(__FILE__).'/common/AGI_Logger.class.php');
require(dirname(__FILE__).'/common/AGI_CDR.class.php');
require(dirname(__FILE__).'/common/AGI_Utils.php');

$agi    = new AGI();
$logger = new AGI_Logger($agi);
$cdr    = new AGI_CDR($agi);

// Get Called Extension informations
$called_ext   = $agi->request['agi_extension'];
$Extension_D  = DB_Extension($called_ext);

// Get Called SipPhone information
$SipPhone = Database_Entry('Ext_SipPhones', $Extension_D['PK_Extension']);

//CDR: Set called info
$cdr->set_called(
	"{$SipPhone['PK_Extension']}",                      // CalledID
	"SipPhone",                                         // CalledType
	"{$SipPhone['FirstName']} {$SipPhone['LastName']}", // CalledName
	"{$Extension_D['Extension']}"                       // CalledNumber
);

//CDR: Push RING event
$cdr->push_event('RING', "{$SipPhone['FirstName']} {$SipPhone['LastName']},{$Extension_D['Extension']}");

// Perform the Dial
$agi->exec('Dial', array("SIP/{$Extension_D['Extension']}",10,'tT'));

//CDR: Push DIALSTATUS
$resp = $agi->get_variable('DIALSTATUS');
$DIALSTATUS = $resp['data'];

$resp = $agi->get_variable('HANGUPCAUSE');
$HANGUPCAUSE = $resp['data'];

if ($DIALSTATUS != "") {
	$cdr->push_event('DIALSTATUS', "$DIALSTATUS,$HANGUPCAUSE");
}

if ($DIALSTATUS != 'ANSWER') {
	// Exit if voicemail is not defined for this extension
	$query = "SELECT * FROM Ext_SipPhones_Features WHERE FK_Extension = {$SipPhone['PK_Extension']} AND FK_Feature=1 LIMIT 1";
	$agi->verbose($query);
	$result = mysql_query($query) or $agi->verbose(mysql_error());
	if (mysql_numrows($result) != 1) { exit(0); }

	// CDR: Push VOICEMAIL
	$cdr->push_event('VOICEMAIL', "{$SipPhone['FirstName']} {$SipPhone['LastName']},{$Extension_D['Extension']}");
	$agi->exec('Voicemail', "{$Extension_D['Extension']}");
}
?>