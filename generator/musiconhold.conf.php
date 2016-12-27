<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/generator.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Groups', Get_Moh_Groups());

file_put_contents($conf['output']['dir'] . '/musiconhold.conf', $smarty->fetch('musiconhold.conf.tpl'));
chmod($conf['output']['dir'] . '/musiconhold.conf', $conf['output']['perms']);

?>
