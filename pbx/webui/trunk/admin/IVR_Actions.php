<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function IVR_Actions() {
	session_start();
	$session = &$_SESSION['IVR_Actions'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$PK_Menu = $_REQUEST['PK_Menu'];

	// Get IVR_Actions
	$query = "
		SELECT
			*
		FROM
			IVR_Actions
		WHERE
			FK_Menu = $PK_Menu
		ORDER BY
			`Order` ASC
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$action = $row;

		$query2  = "SELECT Name,Value,Variable FROM IVR_Action_Params WHERE FK_Action = '{$action['PK_Action']}'";
		$result2 = mysql_query($query2) or die(mysql_error().$query2);
		while ($row2 = mysql_fetch_assoc($result2)) {
			$action['Param'][$row2['Name']] = $row2['Value'];
			$action['Var'][$row2['Name']]   = $row2['Variable'];

			if ($row2['Name'] == 'FK_SoundEntry') {
				$query_snd_name  = "SELECT Name FROM SoundFiles WHERE FK_SoundEntry = '{$row2['Value']}' LIMIT 1";
				$result_snd_name = mysql_query($query_snd_name) or die(mysql_error().$query_snd_name);
				$row_snd_name    = mysql_fetch_assoc($result_snd_name);
				$action['Sound'][$row2['Name']] = $row_snd_name['Name'];
			}
		}

		$IVR_Actions[] = $action;
	}

	$smarty->assign('IVR_Actions', $IVR_Actions);
	$smarty->assign('PK_Menu'    , $PK_Menu);
	$smarty->assign('Total'      , count($IVR_Actions));
	$smarty->assign('History'    , $_SESSION['IVR_HISTORY']);

	return $smarty->fetch('IVR_Actions.tpl');
}

admin_run('IVR_Actions', 'Admin.tpl');

?>