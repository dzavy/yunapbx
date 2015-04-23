<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

function VoipProviders_Iax_Delete() {
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$PK_IaxProvider = $_REQUEST['PK_IaxProvider'];

	// In confirmed, do the actual delete
	if (@$_REQUEST['submit'] == 'delete_confirm') {
		$query = "DELETE FROM IaxProviders WHERE PK_IaxProvider = $PK_IaxProvider LIMIT 1";
		mysql_query($query) or die(mysql_error().$query	);

		if (mysql_affected_rows() != 1) {
			return ;
		}

		$query = "DELETE FROM IaxProvider_Codecs WHERE FK_IaxProvider = $PK_IaxProvider";
		mysql_query($query) or die(mysql_error().$query);

		$query = "DELETE FROM IaxProvider_Hosts WHERE FK_IaxProvider = $PK_IaxProvider";
		mysql_query($query) or die(mysql_error().$query);

		$query = "DELETE FROM IncomingRoutes WHERE ProviderType='IAX' AND ProviderID = {$data['PK_IaxProvider']}";
		mysql_query($query) or die(mysql_error());

		asterisk_UpdateConf('iax.conf');
		asterisk_UpdateConf('extensions.conf');
		asterisk_Reload();

		header('Location: VoipProviders_List.php?msg=DELETE_IAX_PROVIDER');
		die();
	}

	// Init extension info (Extension)
	$query = "
		SELECT
			PK_IaxProvider,
			Name
		FROM
			IaxProviders
		WHERE
			PK_IaxProvider = $PK_IaxProvider
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error());
	$Provider = mysql_fetch_assoc($result);

	$smarty->assign('Provider' , $Provider);

	return $smarty->fetch('VoipProviders_Iax_Delete.tpl');
}

admin_run('VoipProviders_Iax_Delete', 'Admin.tpl');
?>
