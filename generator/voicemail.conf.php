<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Settings'  , Get_Settings());
$smarty->assign('Extensions', Get_Ext_SipPhones());

$out = $smarty->fetch('voicemail.conf.tpl');
$fh = fopen('/etc/asterisk/voicemail.conf', 'w');
fwrite($fh, $out);
fclose($fh);

?>