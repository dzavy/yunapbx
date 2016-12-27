<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/generator.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Dongles', Get_Dongles());

file_put_contents($conf['output']['dir'] . '/dongle.conf', $smarty->fetch('dongle.conf.tpl'));
chmod($conf['output']['dir'] . '/dongle.conf', $conf['output']['perms']);

?>
