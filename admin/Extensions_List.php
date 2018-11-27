<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_List() {
    $db = DB::getInstance();
    $session = &$_SESSION['Extensions_List'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg']) ? $_REQUEST['msg'] : "");
    $ErrMessage = (isset($_REQUEST['errmsg']) ? $_REQUEST['errmsg'] : "");
    //$Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");
    //$ErrMessage = $_REQUEST['errmsg'];
    // Init no element on page (PageSize)
    $PageSize = 50;

    // Init sort order (Order)

    if (!isset($_REQUEST['Sort'])) {
        $Order = 'asc';
    } elseif ($session['Sort'] != $_REQUEST['Sort']) {
        $Order = 'asc';
    } else {
        $Order = ($session['Order'] == "asc" ? "desc" : "asc");
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
    } else {
        $Search = "";
    }

    // Init table fields (Extensions)
    $Extensions = array();
    $query = "
		SELECT
			Extensions.PK_Extension        AS _PK_,
			CAST(Extension AS UNSIGNED) AS Extension,
			Feature                        AS Feature,
			Type                           AS Type,
			CONCAT(
				IFNULL(Extensions.Name,''),
				IFNULL(IVR_Menus.Name,'')
			) AS Name,

			DateCreated AS DateCreated,
			DATE_FORMAT(DateCreated,'%m/%d/%y, %h:%i %p') AS DateCreated_Formated
		FROM
			Extensions
            LEFT JOIN Ext_IVR       ON Ext_IVR.PK_Extension       = Extensions.PK_Extension
				LEFT JOIN IVR_Menus ON Ext_IVR.FK_Menu = IVR_Menus.PK_Menu
		HAVING
			(Extension LIKE '%$Search%'	OR Name LIKE '%$Search%')
			AND
			( NOT Type  LIKE '%Reserved%' )
		ORDER BY
			$Sort $Order
	";
    // -- LIMIT $Start, $PageSize
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Total = $result->num_rows;
    $entries_allowed = $PageSize;
    //$result->data_seek($Start);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
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
    $smarty->assign('ErrMessage', $ErrMessage);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('Extensions_List.tpl');
}

admin_run('Extensions_List', 'Admin.tpl');
