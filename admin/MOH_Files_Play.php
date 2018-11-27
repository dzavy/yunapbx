<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/moh_utils.inc.php');

function MOH_Files_Play() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['MOH_Files_Play'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_File = intval($_REQUEST['PK_File']);

    $query = "SELECT * FROM Moh_Files WHERE PK_File = '{$PK_File}' LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $File = $result->fetch(PDO::FETCH_ASSOC);

    $smarty->assign('File', $File);

    return $smarty->fetch('MOH_Files_Play.tpl');
}

admin_run('MOH_Files_Play', 'AdminPopup.tpl');
?>