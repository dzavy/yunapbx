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

// Get 'Voicemail' Parameters
$Voicemail = Database_Entry('Ext_Voicemail', $Extension_D['PK_Extension']);

//CDR: Set called info
$cdr->set_called(
	"{$Voicemail['PK_Extension']}",                     // CalledID
	"Voicemail",                                        // CalledType
	"Voice Mail",                                       // CalledName
	"{$Extension_D['Extension']}"                       // CalledNumber
);

// Get the extension of the caller
$cid = $agi->parse_callerid();
$caller_ext = $cid['username'];

// Call Voicemail application
$VM_MAIN_ARGS = "$caller_ext@default";
if ($Voicemail['RequirePassword'] != 1) {
	$VM_MAIN_ARGS .= "|s";
}
$agi->exec('VoicemailMain', $VM_MAIN_ARGS)
?>