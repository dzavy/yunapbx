<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/tables.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Queues', Get_Ext_Queues());

$out = $smarty->fetch('queues.conf.tpl');
$fh = fopen($conf['dirs']['output'] . '/queues.conf', 'w');
fwrite($fh, $out);
fclose($fh);

?>