<?php

include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function IVR_Actions_Ajax() {
	session_start();
	$session = &$_SESSION['IVRActionAjax'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$data     = $_POST;
	$response = array();

	switch ($data['Action']) {
		case 'DeleteAction':
			$query = "DELETE FROM IVR_Actions WHERE PK_Action = ".intval($data['ID'])." LIMIT 1";
			mysql_query($query) or die(mysql_error());

			$query = "DELETE FROM IVR_Action_Params WHERE FK_Action = ".intval($data['ID'])." LIMIT 1";
			mysql_query($query) or die(mysql_error());

			$response['ID'] = $data['ID'];
			break;
		case 'UpdateActionOrder':
			$order = 1;
			foreach ($_REQUEST['Actions'] as $PK_Action) {
				$PK_Action = explode('_', $PK_Action);
				$PK_Action = $PK_Action[1];

				$query = "UPDATE IVR_Actions SET `Order` = $order WHERE PK_Action = $PK_Action LIMIT 1";
				mysql_query($query) or die(mysql_error());

				$order++;
			}
			break;
	}

	echo json_encode($response);
}

admin_run('IVR_Actions_Ajax');
