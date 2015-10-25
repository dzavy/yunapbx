<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function HardwareMonitor() {
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $Output = array();

    exec('lsusb', $Output['lsusb']);
    $Output['lsusb'] = implode("\n", $Output['lsusb']);

    $Output['interrupts'] = file_get_contents('/proc/interrupts');

    exec('free', $Output['free']);
    $Output['free'] = implode("\n", $Output['free']);

    exec('df -h', $Output['df']);
    $Output['df'] = implode("\n", $Output['df']);

    exec('ifconfig', $Output['ifconfig']);
    $Output['ifconfig'] = implode("\n", $Output['ifconfig']);

    exec('arp -n', $Output['arp']);
    $Output['arp'] = implode("\n", $Output['arp']);

    exec('route -n', $Output['route']);
    $Output['route'] = implode("\n", $Output['route']);

    $Output['resolv_conf'] = file_get_contents('/etc/resolv.conf');

    $Output['uptime'] = exec('uptime');

    $smarty->assign('Output', $Output);
    return $smarty->fetch('HardwareMonitor.tpl');
}

admin_run('HardwareMonitor', 'Admin.tpl');
