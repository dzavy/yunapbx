<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/generator.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Queues', Get_Ext_Queues());

file_put_contents($conf['output']['dir'] . '/queues.conf', $smarty->fetch('queues.conf.tpl'));
chmod($conf['output']['dir'] . '/queues.conf', $conf['output']['perms']);

?>