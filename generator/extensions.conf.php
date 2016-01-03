<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");
include(dirname(__FILE__).'/../include/config.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('SipProviders', Get_SipProviders());
$smarty->assign('AGI_DIR'     , $conf['dirs']['agi']);

$out = $smarty->fetch('extensions.conf.tpl');
$fh = fopen('/etc/asterisk/extensions.conf', 'w');
fwrite($fh, $out);
fclose($fh);

?>
