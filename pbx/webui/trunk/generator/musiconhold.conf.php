<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");
include(dirname(__FILE__)."/../include/config.inc.php");

$smarty = smarty_init(dirname(__FILE__));

$Groups = Get_Moh_Groups();
foreach ($Groups as $key=>$Group) {
	$Groups[$key]['Folder'] = $conf['dirs']['moh']."/group_".str_pad($Group['PK_Group'],10,'0', STR_PAD_LEFT);
	if ($Group['Volume']>=100) {
		$Groups[$key]['Gain']   = (($Group['Volume']-100)*18)/100;
	} else {
		$Groups[$key]['Gain']   = ($Group['Volume']*50)/100 - 50;
	}
	//$Groups[$key]['Gain'] -= 12;
}
$smarty->assign('Groups', $Groups);


$out = $smarty->fetch('musiconhold.conf.tpl');

$fh = fopen(dirname(__FILE__).'/output/musiconhold.conf', 'w');
fwrite($fh, $out);
fclose($fh);

//echo "<pre>$out</pre>";
?>
