<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Get_IVR_Tree($PK_Menu, $PK_Action = 0) {
    global $mysqli;
    static $VisitedNodes = array();

    $VisitedNodes[] = $PK_Menu;

    // Get IVR_Tree['Name'|'PK_Menu'|'Description']
    $query = "SELECT PK_Menu, Name, Description FROM IVR_Menus WHERE PK_Menu = $PK_Menu LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $row = $result->fetch_assoc();
    $IVR_Tree = $row;

    // Get IVR_Tree['Actions']
    $query = "
		SELECT
			PK_Action,
			Type
		FROM
			IVR_Actions
		WHERE
			FK_Menu = $PK_Menu
		ORDER BY
			`Order` ASC
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $action_disabled = true;
    while ($row = $result->fetch_assoc()) {
        if ($row['PK_Action'] == $PK_Action || $PK_Action == 0) {
            $action_disabled = false;
        }

        $row['Disabled'] = $action_disabled;
        $IVR_Tree['Actions'][] = $row;
    }

    // Get IVR_Tree['Options']
    $IVR_Tree['Options'] = array();
    $query = "
		SELECT
			PK_Option,
			`Key`,
			FK_Menu_Entry,
			FK_Action_Entry
		FROM
			IVR_Options
		WHERE
			FK_Menu = $PK_Menu
		ORDER BY
			`Key`
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['FK_Menu_Entry'], $VisitedNodes)) {
            $IVR_Tree['Options'][$row['Key']] = Get_IVR_Tree($row['FK_Menu_Entry'], $row['FK_Action_Entry']);
        } else {
            $query2 = "SELECT PK_Menu, Name, Description FROM IVR_Menus WHERE PK_Menu = {$row['FK_Menu_Entry']} LIMIT 1";
            $result2 = $mysqli->query($query2) or die($mysqli->error . $query2);
            $visited = $result2->fetch_assoc();

            $IVR_Tree['Visited'][$row['Key']] = $visited;
        }
    }

    return $IVR_Tree;
}

function IVR_Menus() {
    global $mysqli;
    
    $session = &$_SESSION['IVR_Menus'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Menu = $_REQUEST['PK_Menu'];
    $_SESSION['IVR_HISTORY']['PK_Menu'] = $PK_Menu;

    // Init table fields (IVR_Menus)
    $IVR_Menus = array();
    $query = "
		SELECT
			PK_Menu,
			Name,
			Description
		FROM
			IVR_Menus
		ORDER BY
			Name
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $menu = $row;

        // Get extensions maped to this menu
        $menu['Extensions'] = array();
        $query_ext = "
			SELECT
				Extension
			FROM
				Ext_IVR
				INNER JOIN Extensions ON Ext_IVR.PK_Extension = Extensions.PK_Extension
			WHERE
				FK_Menu = {$menu['PK_Menu']}
		";
        $result_ext = $mysqli->query($query_ext) or die($mysqli->error . $query_ext);
        while ($row_ext = $result_ext->fetch_assoc()) {
            $menu['Extensions'][] = $row_ext['Extension'];
        }
        $menu['Extensions'] = implode(' , ', $menu['Extensions']);

        $IVR_Menus[] = $menu;
    }

    if ($PK_Menu != "") {
        $IVR_Tree = Get_IVR_Tree($PK_Menu);
    }

    $smarty->assign('PK_Menu', $PK_Menu);
    $smarty->assign('IVR_Menus', $IVR_Menus);
    $smarty->assign('IVR_Tree', $IVR_Tree);
    $smarty->assign('Message', (isset($_REQUEST['msg'])?$_REQUEST['msg']:""));

    return $smarty->fetch('IVR_Menus.tpl');
}

admin_run('IVR_Menus', 'Admin.tpl');
?>