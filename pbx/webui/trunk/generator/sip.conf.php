<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");


$smarty = smarty_init(dirname(__FILE__));

$smarty->assign('Extensions'  , Get_Ext_SipPhones());
$smarty->assign('SipProviders', Get_SipProviders());
$smarty->assign('Settings'    , Get_Settings());

$out = $smarty->fetch('sip.conf.tpl');

$fh = fopen(dirname(__FILE__).'/output/sip.conf', 'w');
fwrite($fh, $out);
fclose($fh);

//echo "<pre>$out</pre>";
?>
