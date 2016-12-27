<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/generator.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Extensions'  , Get_Ext_SipPhones());
$smarty->assign('SipProviders', Get_SipProviders());
$smarty->assign('Settings'    , Get_Settings());

file_put_contents($conf['output']['dir'] . '/sip.conf', $smarty->fetch('sip.conf.tpl'));
chmod($conf['output']['dir'] . '/sip.conf', $conf['output']['perms']);

?>
