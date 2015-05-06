<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function VoipProviders_Sip_Delete() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_SipProvider = $_REQUEST['PK_SipProvider'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM SipProviders WHERE PK_SipProvider = $PK_SipProvider LIMIT 1";
        $mysqli->query($query) or die($mysqli->error() . $query);

        if ($mysqli->affected_rows() != 1) {
            return;
        }

        $query = "DELETE FROM SipProvider_Codecs WHERE FK_SipProvider = $PK_SipProvider";
        $mysqli->query($query) or die($mysqli->error() . $query);

        $query = "DELETE FROM SipProvider_Hosts WHERE FK_SipProvider = $PK_SipProvider";
        $mysqli->query($query) or die($mysqli->error() . $query);

        $query = "DELETE FROM IncomingRoutes WHERE ProviderType='SIP' AND ProviderID = $PK_SipProvider";
        $mysqli->query($query) or die($mysqli->error() . $query);

        asterisk_UpdateConf('sip.conf');
        asterisk_UpdateConf('extensions.conf');
        asterisk_Reload();

        header('Location: VoipProviders_List.php?msg=DELETE_SIP_PROVIDER');
        die();
    }

    // Init extension info (Extension)
    $query = "
		SELECT
			PK_SipProvider,
			Name
		FROM
			SipProviders
		WHERE
			PK_SipProvider = $PK_SipProvider
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error());
    $Provider = $result->fetch_assoc();

    $smarty->assign('Provider', $Provider);

    return $smarty->fetch('VoipProviders_Sip_Delete.tpl');
}

admin_run('VoipProviders_Sip_Delete', 'Admin.tpl');
?>
