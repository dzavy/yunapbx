<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/user_utils.inc.php');

function TimeFrames_Delete() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Timeframe = $_REQUEST['PK_Timeframe'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        // See if user is alowed to update this timeframe
        $query = "
			SELECT
				*
			FROM
				Timeframes
			WHERE
				PK_Timeframe  = $PK_Timeframe
				AND
				FK_Extension = '" . $mysqli->real_escape_string($_SESSION['_USER']['PK_Extension']) . "'
		";
        $result = $mysqli->query($query) or die($mysqli->error() . $query);
        if ($result->num_rows != 1) {
            header('Location: TimeFrames.php?msg=DELETE_TIMEFRAME');
            die();
        }

        $query = "DELETE FROM Timeframes WHERE PK_Timeframe = $PK_Timeframe LIMIT 1";
        $mysqli->query($query) or die($mysqli->error());

        $query = "DELETE FROM Timeframe_Intervals WHERE FK_Timeframe = $PK_Timeframe";
        $mysqli->query($query) or die($mysqli->error());

        header('Location: TimeFrames.php?msg=DELETE_TIMEFRAME');
        die();
    }

    // Init template info (Template)
    $query = "SELECT * FROM Timeframes WHERE PK_Timeframe = $PK_Timeframe LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error());
    $Timeframe = $result->fetch_assoc();

    $smarty->assign('Timeframe', $Timeframe);

    return $smarty->fetch('TimeFrames_Delete.tpl');
}

user_run('TimeFrames_Delete', 'User.tpl');
?>