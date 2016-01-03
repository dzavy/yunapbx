<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Parking', Get_Ext_Parking());

$out = $smarty->fetch('features.conf.tpl');
$fh = fopen('/etc/asterisk/features.conf', 'w');
fwrite($fh, $out);
fclose($fh);

?>