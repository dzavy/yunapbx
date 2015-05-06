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
    global $mysqli;

    $query = "SELECT * FROM Timezones";
    $result = $mysqli->query($query) or die(__LINE__ . __FILE__);
    $row = array();
    for ($i = 0; $row[$i] = $result->fetch_array(); $i++)
        ;

    $data = array();
    for ($i = 0; $row[$i][0]; $i++) {
        $data[$i] = $row[$i][0];
    };
    return $data;
}

admin_run('SystemClockSettings', 'Admin.tpl');
?>