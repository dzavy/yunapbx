<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Groups_Delete() {
    $db = DB::getInstance();
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Group = $_REQUEST['PK_Group'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Groups WHERE PK_Group = $PK_Group LIMIT 1";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        header('Location: Groups_List.php?msg=DELETE_GROUP');
        die();
    }

    // Init template info (Group)
    $query = "SELECT PK_Group, Name	FROM Groups	WHERE PK_Group = $PK_Group LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $Group = $result->fetch(PDO::FETCH_ASSOC);

    $query = "
		SELECT
			Extension,
			Extensions.PK_Extension,
			Name
		FROM
			Extension_Groups
			INNER JOIN Extensions    ON Extensions.PK_Extension = FK_Extension
		WHERE
			FK_Group = $PK_Group
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $Group['Extensions'] = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Group['Extensions'][] = $row;
    }

    $smarty->assign('Group', $Group);

    return $smarty->fetch('Groups_Delete.tpl');
}

admin_run('Groups_Delete', 'Admin.tpl');
?>
