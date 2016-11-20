<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/network_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/validation_utils.inc.php');
include_once(dirname(__FILE__) . "/../include/asterisk_utils.inc.php");

function NetworkSettings() {

    $session = &$_SESSION['NetworkSettings'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $Message = (isset($_REQUEST['msg']) ? $_REQUEST['msg'] : "");

    if (!empty($_REQUEST['submit'])) {
        $Settings = formdata_from_post();
        $Errors = formdata_validate($Settings, $variables);

        if (count($Errors) == 0) {
            $Message = 'SAVED_NETWORK_SETTINGS';
            formdata_save($Settings);
            set_network_interfaces($Settings);
            asterisk_UpdateConf('sip.conf');
            //asterisk_Reload();
        }

        $OldSettings = array_merge(formdata_from_db(), get_network_interfaces());
        foreach ($OldSettings as $variable => $value) {
            if (!isset($Settings[$variable])) {
                $Settings[$variable] = $value;
            }
        }
    } else {
        $Settings = formdata_from_db();
    }

    $Interface = get_network_interfaces();

    $smarty->assign('Errors', $Errors);
    $smarty->assign('Message', $Message);
    $smarty->assign('Settings', array_merge($Settings, $Interface));

    return $smarty->fetch('NetworkSettings.tpl');
}

function formdata_from_post() {
    $data = $_REQUEST;
    return $data;
}

function formdata_from_db() {
    $variables = array(
        'Network_Additional_LAN',
        'Network_UseNAT',
        'Network_ExternalAddress',
    );

    foreach ($variables as $name) {
        $data[$name] = pbx_var_get($name);
    }

    if ($data['Network_Additional_LAN'] != "") {
        $data['Network_Additional_LAN'] = @explode(';', $data['Network_Additional_LAN']);
    } else {
        $data['Network_Additional_LAN'] = array();
    }

    return $data;
}

function formdata_save($data) {
    $variables = array(
        'Network_Additional_LAN',
        'Network_UseNAT',
        'Network_ExternalAddress',
    );

    $data['Network_Additional_LAN'] = @implode(';', $data['Network_Additional_LAN']);

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

    if($data['Network_Protocol']=="static") {
        if (!validateIpAddress($data['Network_Gateway'])) {
            $errors['Network_Gateway'] = true;
        }

        for ($i = 0; $i < 2; $i++) {
            if ($data['dns' . $i]) {
                if (!validateIpAddress($data['dns' . $i])) {
                    $errors['Network_DNS' . $i] = true;
                }
            }
        }
    }

    foreach ($data['Network_Additional_LAN'] as $Network) {
        if (!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,2}$/', $Network)) {
            if (!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $Network)) {
                $errors['Network_Additional_LAN'][] = $Network;
            }
        }
    }

    if ($data['Network_UseNAT'] == '1') {
        if (!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $data['Network_ExternalAddress'])) {
            $errors['Network_ExternalAddress']['Invalid'] = true;
        }
    }

    return $errors;
}

admin_run('NetworkSettings', 'Admin.tpl');
?>