<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function IVR_Actions() {
    global $mysqli;
    
    $session = &$_SESSION['IVR_Actions'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

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
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $action = $row;

        $query2 = "SELECT Name,Value,Variable FROM IVR_Action_Params WHERE FK_Action = '{$action['PK_Action']}'";
        $result2 = $mysqli->query($query2) or die($mysqli->error . $query2);
        while ($row2 = $result2->fetch_assoc()) {
            $action['Param'][$row2['Name']] = $row2['Value'];
            $action['Var'][$row2['Name']] = $row2['Variable'];

            if ($row2['Name'] == 'FK_SoundEntry') {
                $query_snd_name = "SELECT Name FROM SoundFiles WHERE FK_SoundEntry = '{$row2['Value']}' LIMIT 1";
                $result_snd_name = $mysqli->query($query_snd_name) or die($mysqli->error . $query_snd_name);
                $row_snd_name = $result_snd_name->fetch_assoc();
                $action['Sound'][$row2['Name']] = $row_snd_name['Name'];
            }
        }

        $IVR_Actions[] = $action;
    }

    $smarty->assign('IVR_Actions', $IVR_Actions);
    $smarty->assign('PK_Menu', $PK_Menu);
    $smarty->assign('Total', count($IVR_Actions));
    $smarty->assign('History', $_SESSION['IVR_HISTORY']);

    return $smarty->fetch('IVR_Actions.tpl');
}

admin_run('IVR_Actions', 'Admin.tpl');
?>