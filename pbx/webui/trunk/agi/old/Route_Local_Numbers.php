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

//CDR : Flush existing entry if both called and caller are known
if ($cdr->caller_known() && $cdr->called_known()) {
	$cdr->push_transfer($called_ext);
	$TRANSFER=1;
}

//CDR : set entry type if the caller is unknown yet (new call)
if (!$cdr->caller_known() || $TRANSFER) {
	$cdr->set_type('LOCAL');
}

//CDR: Set caller information
if (! $cdr->caller_known() || $TRANSFER) {
	$cdr->set_caller($Extension_S['PK_Extension'], $Extension_S['Type'], DB_Extension_Name($caller_ext), $caller_ext);
}

// CDR: Push dial event
if (!$TRANSFER) {
	$cdr->push_event("DIAL", $called_ext);
}

$agi->set_variable('__TRANSFER_CONTEXT', 'transfer');

// Do the internal routing
switch ($Extension_D['Type']) {
	case 'SipPhone'  : $agi->goto('Extension_SipPhone'  , $Extension_D['Extension'], 1); break;
	case 'Queue'     : $agi->goto('Extension_Queue'     , $Extension_D['Extension'], 1); break;
	case 'AgentLogin': $agi->goto('Extension_AgentLogin', $Extension_D['Extension'], 1); break;
	case 'IVR'       : $agi->goto('Extension_IVR'       , $Extension_D['Extension'], 1); break;
	case 'Voicemail' : $agi->goto('Extension_Voicemail' , $Extension_D['Extension'], 1); break;
	case 'SimpleConf': $agi->goto('Extension_SimpleConf', $Extension_D['Extension'], 1); break;
	case 'DialTone'  : $agi->goto('Extension_DialTone'  , $Extension_D['Extension'], 1); break;
	case 'Intercom'  : $agi->goto('Extension_Intercom'  , $Extension_D['Extension'], 1); break;
	case 'ParkingLot':
	case 'ParkingLot_Reserved':
                       $agi->goto('Extension_ParkingLot', $Extension_D['Extension'], 1); break;
	case 'ConfCenter': $agi->goto('Extension_ConfCenter', $Extension_D['Extension'], 1); break;

	default          : $agi->verbose('ERROR: Unknown extension type '.$Extension_D['Type']); break;
}

?>
