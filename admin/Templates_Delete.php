<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Templates_Delete() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Template = $_REQUEST['PK_Template'];

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM Templates WHERE PK_Template = $PK_Template AND Protected = 0 LIMIT 1";
        $mysqli->query($query) or die($mysqli->error);

        if ($mysqli->affected_rows != 1) {
            return;
        }

        $query = "DELETE FROM Template_Codecs WHERE FK_Template = $PK_Template";
        $mysqli->query($query) or die($mysqli->error);

        $query = "DELETE FROM Template_Groups WHERE FK_Template = $PK_Template";
        $mysqli->query($query) or die($mysqli->error);

        $query = "DELETE FROM Template_Features WHERE FK_Template = $PK_Template";
        $mysqli->query($query) or die($mysqli->error);

        header('Location: Templates_List.php?msg=DELETE_TEMPLATE');
        die();
    }

    // Init template info (Template)
    $query = "
		SELECT
			PK_Template,
			Name,
			Name_Editable,
			Password_Editable,
			Email_Editable,
			FK_NATType,
			FK_DTMFMode
		FROM
			Templates
		WHERE
			PK_Template = $PK_Template
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error);
    $Template = $result->fetch_assoc();

    $smarty->assign('Template', $Template);

    return $smarty->fetch('Templates_Delete.tpl');
}

admin_run('Templates_Delete', 'Admin.tpl');
?>