<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function AgentSettings() {
    
    $session = &$_SESSION['AgentSettings'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    if (@$_REQUEST['submit'] == 'save') {
        $Settings = formdata_from_post();
        $Errors = formdata_validate($Settings);
        if (count($Errors) == 0) {
            formdata_save($Settings);
        }
    } else {
        $Settings = formdata_from_db();
    }

    $smarty->assign('Errors', $Errors);
    $smarty->assign('Settings', $Settings);

    return $smarty->fetch('AgentSettings.tpl');
}

function formdata_from_post() {
    $data = $_REQUEST;

    return $data;
}

function formdata_from_db() {
    $variables = array('Agent_AckCall', 'Agent_MissedCalls');

    foreach ($variables as $name) {
        $data[$name] = pbx_var_get($name);
    }

    return $data;
}

function formdata_save($data) {
    $variables = array('Agent_AckCall', 'Agent_MissedCalls');

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

    if (!preg_match('/^[0-9]{1,3}$/', $data['Agent_MissedCalls'])) {
        $errors['Agent_MissedCalls']['Invalid'] = true;
    }

    return $errors;
}

admin_run('AgentSettings', 'Admin.tpl');
?>