<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Dongles_List() {
    global $mysqli;
    
    $session = &$_SESSION['Dongles_List'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

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

    // Init table fields (Extensions)
    $Dongles = array();
    $query = "
			SELECT
				PK_Dongle         AS _PK_,
				Name              AS Name,
				IMEI              AS IMEI,
				IMSI              AS IMSI,
				CallbackExtension AS CallbackExtension
			FROM
				Dongles
		ORDER BY
			$Sort $Order
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $Dongles[] = $row;
    }

    $smarty->assign('Errors', $Errors);
    $smarty->assign('Dongles', $Dongles);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('Dongles_List.tpl');
}

admin_run('Dongles_List', 'Admin.tpl');
