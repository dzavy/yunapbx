<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");

$smarty = smarty_init(dirname(__FILE__));

$smarty->assign('Rooms', Get_Ext_ConfCenter_Rooms());

$out = $smarty->fetch('meetme.conf.tpl');

$fh = fopen(dirname(__FILE__).'/output/meetme.conf', 'w');
fwrite($fh, $out);
fclose($fh);

//echo "<pre>$out</pre>";
?>