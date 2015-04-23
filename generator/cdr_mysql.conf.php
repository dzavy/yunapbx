<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");

$smarty = smarty_init(dirname(__FILE__));
$smarty->assign('Conf', $conf);

$out = $smarty->fetch('cdr_mysql.conf.tpl');

$fh = fopen(dirname(__FILE__).'/output/cdr_mysql.conf', 'w');
fwrite($fh, $out);
fclose($fh);

echo "$out\n";
?>
