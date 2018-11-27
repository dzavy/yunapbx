<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/user_utils.inc.php');

function Extensions_Popup() {
    $db = DB::getInstance();
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Set 'Extensions'
    $query = "
		SELECT
			Extension,
			LPAD(Extension,5,' ') AS Extension_Pad,
			Type,
			CONCAT(
				IFNULL(Extensions.Name,''),
				IFNULL(IVR_Menus.Name,'')
			) AS Name
		FROM
			Extensions
			LEFT JOIN Ext_IVR       ON Ext_IVR.PK_Extension       = Extensions.PK_Extension
				LEFT JOIN IVR_Menus ON Ext_IVR.FK_Menu = IVR_Menus.PK_Menu
		WHERE
			( NOT Type  LIKE '%Reserved%' )
		ORDER BY
			Extension_Pad ASC
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $Extensions = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Extensions[] = $row;
    }

    $smarty->assign('FillID', $_REQUEST['FillID']);
    $smarty->assign('Extensions', $Extensions);

    return $smarty->fetch('Extensions_Popup.tpl');
}

user_run('Extensions_Popup', 'UserPopup.tpl');
?>