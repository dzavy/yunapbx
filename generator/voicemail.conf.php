<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/generator.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Settings'  , Get_Settings());
$smarty->assign('Extensions', Get_Ext_SipPhones());

file_put_contents($conf['output']['dir'] . '/voicemail.conf', $smarty->fetch('voicemail.conf.tpl'));
chmod($conf['output']['dir'] . '/voicemail.conf', $conf['output']['perms']);

?>