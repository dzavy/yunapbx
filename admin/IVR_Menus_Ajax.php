<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function IVR_Menus_Ajax() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['IVR_Menus_Ajax'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $data = $_REQUEST;
    $_SESSION['IVR_HISTORY']['Tree'] = "{$data['type']}_{$data['id']}";

    switch ($data['type']) {
        case 'menu':
            $PK_Menu = $data['id'];

            // Get IVR_Tree['Name'|'PK_Menu'|'Description']
            $query = "SELECT PK_Menu, Name, Description FROM IVR_Menus WHERE PK_Menu = $PK_Menu LIMIT 1";
            $result = $db->query($query) or die(print_r($db->errorInfo(), true));
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $IVR_Tree = $row;

            // Get IVR_Tree['Actions']
            $query = "SELECT * FROM IVR_Actions WHERE FK_Menu = '{$PK_Menu}' ORDER BY `Order`";
            $result = $db->query($query) or die(print_r($db->errorInfo(), true));
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $action = $row;

                $query3 = "SELECT * FROM IVR_Action_Params WHERE FK_Action = {$action['PK_Action']}";
                $result3 = $db->query($query3) or die(print_r($db->errorInfo(), true));
                while ($row3 = $result3->fetch(PDO::FETCH_ASSOC)) {
                    $action['Param'][$row3['Name']] = $row3['Value'];
                    $action['Var'][$row3['Name']] = $row3['Variable'];

                    if ($row3['Name'] == 'FK_SoundEntry') {
                        $query_snd_name = "SELECT Name, Description FROM SoundFiles WHERE FK_SoundEntry = '{$row3['Value']}' LIMIT 1";
                        $result_snd_name = $db->query($query_snd_name) or die(print_r($db->errorInfo(), true));
                        $row_snd_name = $result_snd_name->fetch(PDO::FETCH_ASSOC);
                        $action['Sound'][$row3['Name']] = $row_snd_name['Name'];
                        $action['Description'][$row3['Name']] = $row_snd_name['Description'];
                    }
                }

                $IVR_Tree['Actions'][] = $action;
            }

            // Get IVR_Tree['Options']
            $IVR_Tree['Options'] = array();
            $query = "
					SELECT
						PK_Option           AS PK_Option,
						`Key`               AS `Key`,
						IVR_Menus.Name      AS `Name`,
						IVR_Actions.`Order` AS `Order`
					FROM
						IVR_Options
						LEFT JOIN IVR_Menus ON FK_Menu_Entry = PK_Menu
						LEFT JOIN IVR_Actions ON FK_Action_Entry = PK_Action
					WHERE
						IVR_Options.FK_Menu = $PK_Menu
					ORDER BY
						`Key`
				";
            $result = $db->query($query) or die(print_r($db->errorInfo(), true));
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $IVR_Tree['Options'][] = $row;
            }

            $smarty->assign('IVR_Tree', $IVR_Tree);
            $output = $smarty->fetch('IVR_Menus_menu.tpl');
            break;

        case 'action':
            $PK_Action = $data['id'];
            $query = "SELECT PK_Action, Type FROM IVR_Actions WHERE PK_Action = $PK_Action LIMIT 1";
            $result = $db->query($query) or die(print_r($db->errorInfo(), true));
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $IVR_Action = $row;

            $smarty->assign('IVR_Action', $IVR_Action);
            $output = $smarty->fetch('IVR_Menus_action.tpl');
            break;
    }

    return $output;
}

admin_run('IVR_Menus_Ajax');
?>