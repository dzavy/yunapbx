<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_List_Phones() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['Extensions_List_Phones'];
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

    // Init table fields (Extensions)
    $Extensions = array();
    $query = "
		SELECT
			Extensions.PK_Extension        AS _PK_,
			LPAD(Extension,5,' ')          AS Extension,
			Type                           AS Type,
			Name                           AS Name,
			DateCreated AS DateCreated,
			DATE_FORMAT(DateCreated,'%m/%d/%y, %h:%i %p') AS DateCreated_Formated
		FROM
			Extensions
			LEFT JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
		HAVING
			Type IN ('SipPhone')
			AND
			(Extension LIKE '%$Search%'	OR Name LIKE '%$Search%')
		ORDER BY
			$Sort $Order
	";
    // -- LIMIT $Start, $PageSize
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Extensions[] = $row;
    }

    $smarty->assign('Extensions', $Extensions);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Search', $Search);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('Extensions_List_Phones.tpl');
}

admin_run('Extensions_List_Phones', 'Admin.tpl');
?>

