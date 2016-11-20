<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/moh_utils.inc.php');

function MOH_Files_List() {
    global $mysqli;
    
    $session = &$_SESSION['MOH_Files_List'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $errors = array();
    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    if ($_REQUEST['submit']) {
        $selectedPKFiles = $_REQUEST['Files'];
        if (is_array($selectedPKFiles)) {
            $move_PK_Group = $_REQUEST['move_group'];
            $copy_PK_Group = $_REQUEST['copy_group'];
            $submit = $_REQUEST['submit'];
            switch ($submit) {
                case 'delete':
                    foreach ($selectedPKFiles as $PK_File) {
                        $errors = delete_file($PK_File);
                    }
                    break;
                case 'move':
                    foreach ($selectedPKFiles as $PK_File) {
                        $errors = move_file($PK_File, $move_PK_Group);
                    }
                    break;
                case 'copy':
                    foreach ($selectedPKFiles as $PK_File) {
                        $errors = copy_file($PK_File, $copy_PK_Group);
                    }
                    break;
            }
            if (count($errors) == 0) {
                $Message = strtoupper($submit) . "_SUCCESSFULLY";
            }
        } else {
            $errors['EmptySelection'] = true;
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
        $Sort = 'Filename';
    }
    $session['Sort'] = $Sort;

    // Init files list (Files)
    $Files = array();
    $query = "
		SELECT
			PK_File               AS `_PK_`, 
			Filename              AS `Filename`, 
			Fileext               AS `Fileext`, 
			`Order`               AS `Order`, 
			Moh_Files.DateCreated AS `DateCreated`,
			Moh_Groups.Name       AS `Group`,
			Moh_Groups.PK_Group   AS `_PK_Group_`
		FROM
			Moh_Files 
			INNER JOIN Moh_Groups ON FK_Group = PK_Group
		ORDER BY 
			`$Sort` $Order
	";

    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $Files[] = $row;
    }

    // Init available groups (Groups)
    $query = "SELECT * FROM Moh_Groups ORDER BY Name";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $Groups[] = $row;
    }

    $smarty->assign('Errors', $errors);
    $smarty->assign('Files', $Files);
    $smarty->assign('Groups', $Groups);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('MOH_Files_List.tpl');
}

admin_run('MOH_Files_List', 'Admin.tpl');
