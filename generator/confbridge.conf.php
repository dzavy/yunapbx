<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/tables.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Rooms', Get_Ext_ConfCenter_Rooms());

$out = $smarty->fetch('confbridge.conf.tpl');
$fh = fopen($conf['dirs']['output'] . '/confbridge.conf', 'w');
fwrite($fh, $out);
fclose($fh);

?>
