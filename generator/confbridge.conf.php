<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/generator.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Rooms', Get_Ext_ConfCenter_Rooms());

file_put_contents($conf['output']['dir'] . '/confbridge.conf', $smarty->fetch('confbridge.conf.tpl'));
chmod($conf['output']['dir'] . '/confbridge.conf', $conf['output']['perms']);

?>
