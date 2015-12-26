#!/usr/bin/php-cli -q
<?php
require(dirname(__FILE__) . '/../lib/phpagi/phpagi.php');
require(dirname(__FILE__) . '/../include/db_utils.inc.php');
require(dirname(__FILE__) . '/common/AGI_Logger.class.php');
require(dirname(__FILE__) . '/common/AGI_CDR.class.php');
require(dirname(__FILE__) . '/common/AGI_Utils.php');

$agi = new AGI();
$logger = new AGI_Logger($agi);
$cdr = new AGI_CDR($agi);

// Get Called Extension informations
$called_ext = $agi->request['agi_extension'];
$Extension_D = DB_Extension($called_ext);

// Get 'Voicemail' Parameters
$SimpleConf = Database_Entry('Ext_SimpleConf', $Extension_D['PK_Extension']);

//CDR: Set called info
$cdr->set_called(
        "{$SimpleConf['PK_Extension']}", // CalledID
        "SimpleConf", // CalledType
        "Simple Conference", // CalledName
        "{$Extension_D['Extension']}"                       // CalledNumber
);

// Set transfer extension if we have one
if ($SimpleConf['TransferExt'] != "") {
    $agi->set_variable('CONF_TRANSFER_EXT', $SimpleConf['TransferExt']);
}

// Call SimpleConf application
$params = 'S';
if ($SimpleConf['PlaySound'] == 1) {
    $params .='n';
}
if ($SimpleConf['PlayMOH'] == 1) {
    $params .='m';
}

$agi->exec('Conference', "simpleconf-{$Extension_D['PK_Extension']}/{$params}");
?>