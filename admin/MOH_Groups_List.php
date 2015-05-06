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

    // Init no element on page (PageSize)
    $PageSize = 50;

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

    // Init listing start (Start)
    if (isset($_REQUEST['Start'])) {
        $Start = $_REQUEST['Start'];
    } else {
        $Start = 0;
    }

    // Init total entries (Total)
    $query = "SELECT COUNT(PK_Group) FROM Moh_Groups;";
    $result = $mysqli->query($query) or die($mysqli->error());
    $row = $result->fetch_array();
    $Total = $row[0];

    // Init table fields (Groups)
    $Groups = array();
    $query = "
		SELECT
			*
		FROM
			Moh_Groups
		ORDER BY 
			`$Sort` $Order
		LIMIT $Start, $PageSize

	";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    while ($row = $result->fetch_assoc()) {
        $Groups[] = $row;
    }

    // Init end record (End)
    $End = $Start + count($Groups);

    $smarty->assign('Groups', $Groups);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Start', $Start);
    $smarty->assign('End', $End);
    $smarty->assign('Total', $Total);
    $smarty->assign('PageSize', $PageSize);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('MOH_Groups_List.tpl');
}

admin_run('MOH_Groups_List', 'Admin.tpl');
?>
