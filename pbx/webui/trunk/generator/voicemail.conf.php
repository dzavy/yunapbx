<?php
include_once(dirname(__FILE__)."/../include/db_utils.inc.php");
include_once(dirname(__FILE__)."/../include/smarty_utils.inc.php");
include_once(dirname(__FILE__)."/tables.inc.php");

$smarty = smarty_init(dirname(__FILE__));

$query  = "SELECT * FROM Settings";
$result = mysql_query($query) or $agi->verbose(mysql_error().$query);
while ($row = mysql_fetch_assoc($result)) {
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