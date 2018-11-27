<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function VoipProviders_Delete() {
    $db = DB::getInstance();
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_SipProvider = $_REQUEST['PK_SipProvider'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM SipProvider_Codecs WHERE FK_SipProvider = $PK_SipProvider";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM SipProvider_Rules WHERE FK_SipProvider = $PK_SipProvider";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM SipProvider_Status WHERE FK_SipProvider = $PK_SipProvider";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM IncomingRoutes WHERE ProviderType='SIP' AND ProviderID = $PK_SipProvider";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM SipProviders WHERE PK_SipProvider = $PK_SipProvider LIMIT 1";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        asterisk_UpdateConf('sip.conf');
        asterisk_UpdateConf('pjsip.conf');
        asterisk_UpdateConf('extensions.conf');
        asterisk_Reload();

        header('Location: VoipProviders_List.php?msg=DELETE_SIP_PROVIDER');
        die();
    }

    // Init extension info (Extension)
    $query = "SELECT PK_SipProvider, Name FROM SipProviders	WHERE PK_SipProvider = $PK_SipProvider LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Provider = $result->fetch(PDO::FETCH_ASSOC);

    $smarty->assign('Provider', $Provider);

    return $smarty->fetch('VoipProviders_Delete.tpl');
}

admin_run('VoipProviders_Delete', 'Admin.tpl');
?>
