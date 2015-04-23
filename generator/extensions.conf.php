<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");
include(dirname(__FILE__).'/../include/config.inc.php');

$smarty = smarty_init(dirname(__FILE__));

$smarty->assign('SipProviders', Get_SipProviders());
$smarty->assign('IaxProviders', Get_IaxProviders());
$smarty->assign('AGI_DIR'     , $conf['dirs']['agi']);

$out = $smarty->fetch('extensions.conf.tpl');

$fh = fopen(dirname(__FILE__).'/output/extensions.conf', 'w');
fwrite($fh, $out);
fclose($fh);

//echo "<pre>$out</pre>";
?>
