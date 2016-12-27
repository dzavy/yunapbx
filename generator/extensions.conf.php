<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/generator.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('OutgoingRules', Get_OutgoingRules());
$smarty->assign('SipProviders', Get_SipProviders());
$smarty->assign('Dongles', Get_Dongles());
$smarty->assign('Ext_SipPhones', Get_Ext_SipPhones());
$smarty->assign('Ext_Queues', Get_Ext_Queues());

file_put_contents($conf['output']['dir'] . '/extensions.conf', $smarty->fetch('extensions.conf.tpl'));
chmod($conf['output']['dir'] . '/extensions.conf', $conf['output']['perms']);

?>
