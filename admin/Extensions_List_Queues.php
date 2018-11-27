<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_List_Queues() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['Extensions_List_Queues'];
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
        $Sort = 'Extension';
    }
    $session['Sort'] = $Sort;

    // Init search string (Search)
    if (isset($_REQUEST['Search'])) {
        $Search = $_REQUEST['Search'];
    }

    // Init total entries (Total)
    $query = "SELECT COUNT(PK_Extension) FROM Extensions;";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $row = $result->fetch_array();
    $Total = $row[0];

    // Init table fields (Extensions)
    $Extensions = array();
    $query = "
		SELECT
			Extensions.PK_Extension        AS _PK_,
			LPAD(Extension,5,' ')          AS Extension,
			Type                           AS Type,
			Extensions.Name                AS Name,
			RingStrategies.Name            AS Strategy,
			DateCreated AS DateCreated,
			DATE_FORMAT(DateCreated,'%m/%d/%y, %h:%i %p') AS DateCreated_Formated
		FROM
			Extensions
			LEFT  JOIN Ext_Queues     ON Ext_Queues.PK_Extension  = Extensions.PK_Extension
			INNER JOIN RingStrategies ON FK_RingStrategy          = PK_RingStrategy
		HAVING
			Type = 'Queue'
			AND
			(Extension LIKE '%$Search%' OR Name LIKE '%$Search%')
		ORDER BY
			$Sort $Order
	";
    // -- LIMIT $Start, $PageSize
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $extension = $row;

        $query2 = "SELECT * FROM Ext_Queue_Members WHERE FK_Extension = {$extension['_PK_']}";
        $result2 = $db->query($query2) or die(print_r($db->errorInfo(), true));
        $extension['Members'] = $result2->num_rows;

        $Extensions[] = $extension;
    }

    $smarty->assign('Extensions', $Extensions);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Search', $Search);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('Extensions_List_Queues.tpl');
}

admin_run('Extensions_List_Queues', 'Admin.tpl');
?>