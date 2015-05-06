<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_List_Agents() {
    global $mysqli;
    
    $session = &$_SESSION['Extensions_List_Agents'];
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
        $Sort = 'Extension';
    }
    $session['Sort'] = $Sort;

    // Init listing start (Start)
    if (isset($_REQUEST['Start'])) {
        $Start = $_REQUEST['Start'];
    } else {
        $Start = 0;
    }

    // Init search string (Search)
    if (isset($_REQUEST['Search'])) {
        $Search = $_REQUEST['Search'];
    }

    // Init table fields (Extensions)
    $Extensions = array();
    $query = "
		SELECT
			Extensions.PK_Extension        AS _PK_,
			LPAD(Extension,5,' ')          AS Extension,
			Type                           AS Type,
			Ext_Agent.FirstName            AS FirstName,
			Ext_Agent.LastName             AS LastName,
			DateCreated AS DateCreated,
			DATE_FORMAT(DateCreated,'%m/%d/%y, %h:%i %p') AS DateCreated_Formated
		FROM
			Extensions
			LEFT JOIN Ext_Agent ON Ext_Agent.PK_Extension = Extensions.PK_Extension
		HAVING
			Type IN ('Agent')
			AND
			(Extension LIKE '%$Search%'	OR FirstName LIKE '%$Search%' OR LastName LIKE '%$Search%')
		ORDER BY
			$Sort $Order
	";
    // -- LIMIT $Start, $PageSize
    $result = $mysqli->query($query) or die($mysqli->error());

    $Total = $result->num_rows;
    $entries_allowed = $PageSize;
    @$result->data_seek($Start);
    while ($row = $result->fetch_assoc()) {
        $Extensions[] = $row;
        if (($entries_allowed--) == 1) {
            break;
        }
    }

    // Init end record (End)
    $End = count($Extensions) + $Start;

    $smarty->assign('Extensions', $Extensions);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Start', $Start);
    $smarty->assign('End', $End);
    $smarty->assign('Total', $Total);
    $smarty->assign('PageSize', $PageSize);
    $smarty->assign('Search', $Search);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('Extensions_List_Agents.tpl');
}

admin_run('Extensions_List_Agents', 'Admin.tpl');
?>

