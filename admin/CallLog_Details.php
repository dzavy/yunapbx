<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function CallLog_Details() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['CallLog_Details'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_CallLog = $_REQUEST['PK_CallLog'];

    $Details = array();
    $query = "
		SELECT
			*,
			DATE_FORMAT(Date,'%h:%i:%s %p') AS Date_Formated
		FROM
			CallLog_Details
		WHERE
			FK_CallLog = '" . $mysqli->real_escape_string($PK_CallLog) . "'
		ORDER BY
			Date
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $row['Data_CSV'] = $row['Data'];
        $row['Data'] = explode(',', $row['Data_CSV']);
        $Details[] = $row;
    }

    $smarty->assign('Details', $Details);
    return $smarty->fetch('CallLog_Details.tpl');
}

admin_run('CallLog_Details', '');
?>