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


// Get 'DialTone' Parameters
$DialTone = Database_Entry('Ext_DialTone', $Extension_D['PK_Extension']);

//CDR: Set called info
$cdr->set_called(
	"{$DialTone['PK_Extension']}",                    // CalledID
	"DialTone",                                       // CalledType
	"Dial Tone",                                      // CalledName
	"{$Extension_D['Extension']}"                     // CalledNumber
);

// Call DISA Application
if ($DialTone['Password'] != '') {
	$agi->exec('DISA', array($DialTone['Password'], 'internal'));
} else {
	$agi->exec('DISA', array('no-password','internal'));
}
?>