<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function IVR_Actions() {
    $db = DB::getInstance();
    
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
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $action = $row;

        $query2 = "SELECT Name,Value,Variable FROM IVR_Action_Params WHERE FK_Action = '{$action['PK_Action']}'";
        $result2 = $db->query($query2) or die(print_r($db->errorInfo(), true));
        while ($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
            $action['Param'][$row2['Name']] = $row2['Value'];
            $action['Var'][$row2['Name']] = $row2['Variable'];

            if ($row2['Name'] == 'FK_SoundEntry') {
                $query_snd_name = "SELECT Name FROM SoundFiles WHERE FK_SoundEntry = '{$row2['Value']}' LIMIT 1";
                $result_snd_name = $db->query($query_snd_name) or die(print_r($db->errorInfo(), true));
                $row_snd_name = $result_snd_name->fetch(PDO::FETCH_ASSOC);
                $action['Sound'][$row2['Name']] = $row_snd_name['Name'];
            }
        }

        $IVR_Actions[] = $action;
    }

    $smarty->assign('IVR_Actions', $IVR_Actions);
    $smarty->assign('PK_Menu', $PK_Menu);
    $smarty->assign('History', $_SESSION['IVR_HISTORY']);

    return $smarty->fetch('IVR_Actions.tpl');
}

admin_run('IVR_Actions', 'Admin.tpl');
?>