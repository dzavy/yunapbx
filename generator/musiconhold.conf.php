<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/tables.inc.php');

$smarty = smarty_init(dirname(__FILE__) . '/templates');

$smarty->assign('Groups', Get_Moh_Groups());

$out = $smarty->fetch('musiconhold.conf.tpl');
$fh = fopen($conf['dirs']['output'] . '/musiconhold.conf', 'w');
fwrite($fh, $out);
fclose($fh);

?>
