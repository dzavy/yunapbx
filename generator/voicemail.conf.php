<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");
//include(dirname(__FILE__)."/../include/config.inc.php");

global $mysqli;
$smarty = smarty_init(dirname(__FILE__));
$query  = "SELECT * FROM Settings";
$result = $mysqli->query($query) or $agi->verbose($mysqli->error.$query);
while ($row = $result->fetch_assoc()) {
	$Settings[$row['Name']] = $row['Value'];
}

$smarty->assign('Settings'  , $Settings);
$smarty->assign('Extensions', Get_Ext_SipPhones());

$out = $smarty->fetch('voicemail.conf.tpl');

$fh = fopen(dirname(__FILE__).'/output/voicemail.conf', 'w');
fwrite($fh, $out);
fclose($fh);

//echo "<pre>$out</pre>";
?>