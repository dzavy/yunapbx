<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");

$smarty = smarty_init(dirname(__FILE__));

$smarty->assign('Parking', Get_Ext_Parking());

$out = $smarty->fetch('features.conf.tpl');

$fh = fopen(dirname(__FILE__).'/output/features.conf', 'w');
fwrite($fh, $out);
fclose($fh);

//echo "<pre>$out</pre>";
?>