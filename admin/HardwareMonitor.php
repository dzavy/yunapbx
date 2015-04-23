<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function HardwareMonitor() {
	session_start();
	$session = &$_SESSION['HardwareMonitor'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$Advanced = $_REQUEST['Advanced'];

	if ($Advanced){
		$Output = array();

		exec('lspci', $Output['lspci']);
		$Output['lspci'] = implode("\n", $Output['lspci']);

		$Output['interrupts'] = implode("", file('/proc/interrupts'));

		exec('free', $Output['free']);
		$Output['free'] = implode("\n", $Output['free']);

		exec('df -h', $Output['df']);
		$Output['df'] = implode("\n", $Output['df']);

		exec('/sbin/ifconfig', $Output['ifconfig']);
		$Output['ifconfig'] = implode("\n", $Output['ifconfig']);

		exec('/usr/sbin/arp -n', $Output['arp']);
		$Output['arp'] = implode("\n", $Output['arp']);

		exec('/sbin/route -n', $Output['route']);
		$Output['route'] = implode("\n", $Output['route']);

		$Output['resolv_conf'] = implode("", file('/etc/resolv.conf'));

		$Output['uptime'] = exec('uptime');

		$smarty->assign('Output' , $Output);
	}


	//test varibles
	$HardwareMonitor = array();
	$query  = "
		SELECT
			*,
			DATE_FORMAT(LastUpdate,'%m/%d/%y %h:%i:%s %p') AS LastUpdate
		FROM
			HardwareMonitor
		WHERE
			LastUpdate = (SELECT MAX(LastUpdate) FROM HardwareMonitor)";
	$result = mysql_query($query) or die(mysql_error().$query);
	
	while ($row = mysql_fetch_assoc($result)) {
		$HardwareMonitor = $row;
	}	
	
	$smarty->assign('Advanced'        , $Advanced);
	$smarty->assign('HardwareMonitor' , $HardwareMonitor);
	return $smarty->fetch('HardwareMonitor.tpl');
}

admin_run('HardwareMonitor', 'Admin.tpl');
