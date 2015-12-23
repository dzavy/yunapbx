<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function VoipProviders_List() {
    global $mysqli;
    
    $session = &$_SESSION['VoipProviders_List'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

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
    $query = "SELECT COUNT(*) FROM SipProviders";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $row = $result->fetch_array();
    $Total = $row[0];

    // Init table fields (Extensions)
    $Providers = array();
    $query = "
			SELECT
				PK_SipProvider    AS _PK_,
				Name              AS Name,
				'SIP'             AS Type,
				AccountID         AS AccountID,
				Host              AS Host,
				CallbackExtension AS CallbackExtension
			FROM
				SipProviders
		ORDER BY
			$Sort $Order
		LIMIT $Start, $PageSize
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $Providers[] = $row;
    }

    // Init end record (End)
    $End = count($Providers);

    $smarty->assign('Errors', $Errors);
    $smarty->assign('Providers', $Providers);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Start', $Start);
    $smarty->assign('End', $End);
    $smarty->assign('Total', $Total);
    $smarty->assign('PageSize', $PageSize);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('VoipProviders_List.tpl');
}

admin_run('VoipProviders_List', 'Admin.tpl');
