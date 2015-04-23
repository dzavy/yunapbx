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

// Get Caller Extension infomation
$cid = $agi->parse_callerid();
$caller_ext  = $cid['username'];
$Extension_S = DB_Extension($caller_ext);

// Park Calls
if ($Extension_D['Type'] == 'ParkingLot') {
	$agi->exec_goto('park_call', 0, 1);

// UnPark Calls
} else {
	// See if the extension is alowed to unpark calls
	if ($Extension_S['Type'] == 'SipPhone') {
		$query = "SELECT * FROM Ext_SipPhones_Features WHERE FK_Extension  = {$Extension_S['PK_Extension']} AND FK_Feature = 6";
		$result = mysql_query($query) or $agi->verbose($query);
	} else {
		$query = "SELECT * FROM Ext_Virtual_Features   WHERE FK_Extension  = {$Extension_S['PK_Extension']} AND FK_Feature = 6";
		$result = mysql_query($query) or $agi->verbose($query);
	}

	$agi->verbose($query);
	// Unpark calls if allowed
	if (mysql_num_rows($result) == 1) {
		$agi->exec_goto('park_call', $Extension_D['Extension'], 1);
	}
}
?>
