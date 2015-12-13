<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/validation_utils.inc.php');

function SystemClockSettings() {
    
    $session = &$_SESSION['SystemClockSettings'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    if (!empty($_REQUEST['submit'])) {
        $Settings = formdata_from_post();

        $Settings['Current_TimeZone'] = $Settings['TimeZones'][0];
        $Settings['DisableNTP'] = $Settings['DisableNTPLast'];
        $Errors = formdata_validate($Settings, $variables);

        if (count($Errors) == 0) {
            $Message = 'SAVED_SYSTEMCLOCK_SETTINGS';
            formdata_save($Settings);
        }

        $OldSettings = formdata_from_db();
        foreach ($OldSettings as $variable => $value) {
            if (!isset($Settings[$variable])) {
                $Settings[$variable] = $value;
            }
        }
    } else {
        $Settings = formdata_from_db();
    }

    $TimeZones = get_timezone_list();

    $smarty->assign('Errors', $Errors);
    $smarty->assign('Message', $Message);
    $smarty->assign('Settings', $Settings);
    $smarty->assign('TimeZones', $TimeZones);
    $smarty->assign('CurrentTime', date("F j, Y, g:i a"));

    return $smarty->fetch('SystemClockSettings.tpl');
}

function formdata_from_post() {
    $data = $_REQUEST;
    return $data;
}

function formdata_from_db() {
    $variables = array(
        'Current_TimeZone',
        'Current_SysTime',
        'NTPServer',
        'DisableNTP'
    );

    foreach ($variables as $name) {
        $data[$name] = pbx_var_get($name);
    }

    return $data;
}

function formdata_save($data) {
    $variables = array(
        'Current_TimeZone',
        'Current_SysTime',
        'NTPServer',
        'DisableNTP'
    );


    if (is_array($data)) {
        foreach ($data as $name => $value) {
            if (in_array($name, $variables)) {
                pbx_var_set($name, $value);
            }
        }
    }
}

function formdata_validate($data) {
    $errors = array();

    if (!validateHostName($data['NTPServer'])) {
        $errors['NTPServer'] = true;
    }
    return $errors;
}

function get_timezone_list() {
    $path = realpath('/usr/share/zoneinfo');

    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY);
    foreach($objects as $name => $object){
        if($name != $path . "/zone.tab") {
            $data[] = str_replace($path . "/", "", $name);
        }
    }
    sort($data);
    return $data;
}

admin_run('SystemClockSettings', 'Admin.tpl');
?>