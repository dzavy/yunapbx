<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/user_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/voicemail_utils.inc.php');

function Voicemail() {
    global $mysqli;
    $session = &$_SESSION['User_Voicemail'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Init no element on page (PageSize)
    $PageSize = 50;

    // Init current folder (Path)
    if ($_REQUEST['Path'] != '') {
        $Path = $_REQUEST['Path'];
    } elseif ($session['Path']) {
        $Path = $session['Path'];
    } else {
        $Path = 'INBOX';
    }
    $session['Path'] = $Path;


    if ($_REQUEST['action'] == 'delete' && $_REQUEST['Messages']) {
        foreach ($_REQUEST['Messages'] as $Message) {
            vm_delfile($_SESSION['_USER']['Extension'], $Path, $Message);
        }
    }

    if ($_REQUEST['action'] == 'move' && $_REQUEST['Messages']) {
        foreach ($_REQUEST['Messages'] as $Message) {
            vm_movfile($_SESSION['_USER']['Extension'], $Path, $_REQUEST['MoveFolder'], $Message);
        }
    }

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
        $Sort = 'no';
    }
    $session['Sort'] = $Sort;

    // Init listing start (Start)
    if (isset($_REQUEST['Start'])) {
        $Start = $_REQUEST['Start'];
    } else {
        $Start = 0;
    }

    // Init available mailboxes
    $query = "
		SELECT
			LPAD(Extension,5,' ')          AS Extension,
			Name
		FROM
			Extensions
		ORDER BY
			Extension
	";
    $result = $mysqli->query($query) or die($mysqli->error);

    $Folders = vm_folders($_SESSION['_USER']['Extension']);
    $Messages = vm_files($_SESSION['_USER']['Extension'], $Path);
    $Total = count($Messages);

    array_order($Messages, $Sort, $Order);
    $Messages = array_slice($Messages, $Start, $PageSize);

    $End = $Start + count($Messages);

    $smarty->assign('Folders', $Folders);
    $smarty->assign('Messages', $Messages);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Start', $Start);
    $smarty->assign('End', $End);
    $smarty->assign('Total', $Total);
    $smarty->assign('PageSize', $PageSize);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));
    $smarty->assign('Path', $Path);


    return $smarty->fetch('Voicemail.tpl');
}

function array_order(&$array, $field, $order) {
    if (!is_array($array)) {
        return;
    }

    foreach ($array as $key => $row) {
        $aux[$key] = $row[$field];
    }

    if (!is_array($aux)) {
        return;
    }

    if (strtolower($order) == 'desc') {
        array_multisort($aux, SORT_DESC, $array);
    } else {
        array_multisort($aux, SORT_ASC, $array);
    }
}

user_run('Voicemail', 'User.tpl');
?>
