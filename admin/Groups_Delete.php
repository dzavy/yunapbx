<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Groups_Delete() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Group = $_REQUEST['PK_Group'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Groups WHERE PK_Group = $PK_Group LIMIT 1";
        $mysqli->query($query) or die($mysqli->error());

        header('Location: Groups_List.php?msg=DELETE_GROUP');
        die();
    }

    // Init template info (Group)
    $query = "
		SELECT
			PK_Group,
			Name
		FROM
			Groups
		WHERE
			PK_Group = $PK_Group
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    $Group = $result->fetch_assoc();

    $query = "
		SELECT
			Extension,
			Extensions.PK_Extension,
			FirstName,
			LastName
		FROM
			Extension_Groups
			INNER JOIN Extensions    ON Extensions.PK_Extension = FK_Extension
			INNER JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = FK_Extension

		WHERE
			FK_Group = $PK_Group
	";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);

    $Group['Extensions'] = array();
    while ($row = $result->fetch_assoc()) {
        $Group['Extensions'][] = $row;
    }

    $smarty->assign('Group', $Group);

    return $smarty->fetch('Groups_Delete.tpl');
}

admin_run('Groups_Delete', 'Admin.tpl');
?>
