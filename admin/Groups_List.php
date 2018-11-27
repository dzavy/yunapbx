<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Groups_List() {
    $db = DB::getInstance();
    
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
			PK_Group  		    AS _PK_,
			Name                AS Name,
			Count(FK_Extension) AS Members,
			DATE_FORMAT(DateCreated,'%m/%d/%y, %h:%i %p') AS DateCreated
		FROM
			Groups
			LEFT JOIN Extension_Groups ON FK_Group = PK_Group
		GROUP BY
			PK_Group
		ORDER BY 
			$Sort $Order
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Groups[] = $row;
    }

    $smarty->assign('Groups', $Groups);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('Groups_List.tpl');
}

admin_run('Groups_List', 'Admin.tpl');
?>
