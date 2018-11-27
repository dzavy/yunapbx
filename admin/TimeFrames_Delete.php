<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function TimeFrames_Delete() {
    $db = DB::getInstance();
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Timeframe = $_REQUEST['PK_Timeframe'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Timeframes WHERE PK_Timeframe = $PK_Timeframe LIMIT 1";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $query = "DELETE FROM Timeframe_Intervals WHERE FK_Timeframe = $PK_Timeframe";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        header('Location: TimeFrames.php?msg=DELETE_TIMEFRAME');
        die();
    }

    // Init template info (Template)
    $query = "SELECT PK_Timeframe, Name FROM Timeframes WHERE PK_Timeframe = $PK_Timeframe LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Timeframe = $result->fetch(PDO::FETCH_ASSOC);

    $smarty->assign('Timeframe', $Timeframe);

    return $smarty->fetch('TimeFrames_Delete.tpl');
}

admin_run('TimeFrames_Delete', 'Admin.tpl');
?>