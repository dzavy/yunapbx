<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function VoicemailSettings() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['AgentSettings'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // SipProviders
    $SipProviders = array();
    $query = "SELECT * FROM SipProviders ORDER BY Name";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $SipProviders[] = $row;
    }

    if (!empty($_REQUEST['submit'])) {
        $Settings = formdata_from_post();

        // Save Email Settings
        if ($_REQUEST['submit'] == 'save_email_settings') {
            $variables = array('Voicemail_From', 'Voicemail_SMTP_Server', 'Voicemail_SMTP_User', 'Voicemail_SMTP_Pass');
            $Message = 'SAVED_EMAIL_SETTINGS';
        }

        // Save Routing Settings
        if ($_REQUEST['submit'] == 'save_routing_settings') {
            $variables = array('Voicemail_AllowLogin', 'Voicemail_OperatorExtension');
            $Message = 'SAVED_ROUTING_SETTINGS';
        }

        // Save Voicemail Template
        if ($_REQUEST['submit'] == 'save_voicemail_template') {
            $variables = array('Voicemail_EmailTemplate');
            $Message = 'SAVED_EMAIL_TEMPLATE';
        }

        // Save Voicemail Template
        if ($_REQUEST['submit'] == 'save_external_settings') {
            $variables = array('Voicemail_UseExternal', 'Voicemail_PK_SipProvider');
            $Message = 'SAVED_EXTERNAL_SETTINGS';
        }

        // Restore original email template if requested
        if ($_REQUEST['submit'] == 'restore_original_template') {
            $variables = array();
            $OldSettings = formdata_from_db();
            $Settings['Voicemail_EmailTemplate'] = $OldSettings['Voicemail_EmailTemplate_Original'];
            $Message = 'RESTORE_EMAIL_TEMPLATE';
        }

        $Errors = formdata_validate($Settings, $variables);
        if (count($Errors) == 0) {
            formdata_save($Settings, $variables);
        } else {
            $Message = "";
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

    $smarty->assign('Errors', $Errors);
    $smarty->assign('Message', $Message);
    $smarty->assign('Settings', $Settings);
    $smarty->assign('SipProviders', $SipProviders);

    return $smarty->fetch('VoicemailSettings.tpl');
}

function formdata_from_post() {
    $data = $_REQUEST;

    if (!isset($data['Voicemail_UseExternal'])) {
        $data['Voicemail_UseExternal'] = 0;
        if (!isset($data['Voicemail_AllowLogin'])) {
            $data['Voicemail_AllowLogin'] = 0;
        }
    }

    return $data;
}

function formdata_from_db() {
    $variables = array('Voicemail_From', 'Voicemail_SMTP_Server', 'Voicemail_SMTP_User', 'Voicemail_SMTP_Pass', 'Voicemail_AllowLogin', 'Voicemail_OperatorExtension', 'Voicemail_EmailTemplate', 'Voicemail_EmailTemplate_Original', 'Voicemail_UseExternal', 'Voicemail_PK_SipProvider');

    foreach ($variables as $name) {
        $data[$name] = pbx_var_get($name);
    }

    return $data;
}

function formdata_save($data, $variables) {
    $variables = array('Voicemail_From', 'Voicemail_SMTP_Server', 'Voicemail_SMTP_User', 'Voicemail_SMTP_Pass', 'Voicemail_AllowLogin', 'Voicemail_OperatorExtension', 'Voicemail_EmailTemplate', 'Voicemail_UseExternal', 'Voicemail_PK_SipProvider');

    if (is_array($data)) {
        foreach ($data as $name => $value) {
            if (in_array($name, $variables)) {
                pbx_var_set($name, $value);
            }
        }
    }
}

function formdata_validate($data, $variables) {
    $errors = array();

    if (in_array('Voicemail_OperatorExtension', $variables)) {
        if (!empty($data['Voicemail_OperatorExtension'])) {
            if (!preg_match('/^[0-9]{3,4}$/', $data['Voicemail_OperatorExtension'])) {
                $errors['Voicemail_OperatorExtension']['Invalid'] = true;
            }
        }
    }

    return $errors;
}

admin_run('VoicemailSettings', 'Admin.tpl');
?>
