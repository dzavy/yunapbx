<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");

$smarty = smarty_init(dirname(__FILE__));

$smarty->assign('Queues', Get_Ext_Queues());

$out = $smarty->fetch('queues.conf.tpl');

$fh = fopen(dirname(__FILE__).'/output/queues.conf', 'w');
fwrite($fh, $out);
fclose($fh);

//echo "<pre>$out</pre>";
?>