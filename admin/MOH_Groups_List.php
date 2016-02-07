<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function MOH_Groups_List() {
    global $mysqli;
    
    $session = &$_SESSION['Groups'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Init sort order (Order)
    if ($session['Sort'] == $_REQUEST['Sort']) {
        $Order = ($session['Order'] == "asc" ? "desc" : "asc");
    } elseif ($session['Sort'] != $_REQUEST['Sort']) {
        $Order = 'asc';
    }
    $session['Order'] = $Order;

    // Init sort field (Sort)
    if (isset($_REQUEST['Sort'])) {
        $Sort = $_REQUEST['Sort'];
    } else {
        $Sort = 'Name';
    }
    $session['Sort'] = $Sort;

    // Init table fields (Groups)
    $Groups = array();
    $query = "
		SELECT
			*
		FROM
			Moh_Groups
		ORDER BY 
			`$Sort` $Order
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $Groups[] = $row;
    }

    $smarty->assign('Groups', $Groups);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('MOH_Groups_List.tpl');
}

admin_run('MOH_Groups_List', 'Admin.tpl');
?>
