<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Backup() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['Backup'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    $PageSize = 4;

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
        $Sort = 'Optionals';
    }

    $session['Sort'] = $Sort;

    // Init listing start (Start)
    if (isset($_REQUEST['Start'])) {
        $Start = $_REQUEST['Start'];
    } else {
        $Start = 0;
    }

    // Init total entries (Total)
    $query = "SELECT COUNT(*) FROM Backups";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $row = $result->fetch_array();
    $Total = $row[0];

    // Init table fields (Backups)
    $Backups = array();
    $query = "SELECT PK_Backup, Optionals, Size, Date FROM Backups ORDER BY $Sort $Order LIMIT $Start, $PageSize";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Backups[] = $row;
    }

    // Init end record (End)
    $End = $Start + $PageSize;

    $smarty->assign('Errors', $Errors);
    $smarty->assign('Backups', $Backups);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Start', $Start);
    $smarty->assign('End', $End);
    $smarty->assign('Total', $Total);
    $smarty->assign('PageSize', $PageSize);
    $smarty->assign('Message', $Message);

    return $smarty->fetch('Backup.tpl');
}

admin_run('Backup', 'Admin.tpl');
