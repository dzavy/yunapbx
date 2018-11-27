<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Groups_Modify() {
    $db = DB::getInstance();
    
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init form data (Group, Errors)
    if (@$_REQUEST['submit'] == 'save') {
        $Group = formdata_from_post();
        $Errors = formdata_validate($Group);

        if (count($Errors) == 0) {
            $id = formdata_save($Group);
            header("Location: Groups_List.php?msg=MODIFY_GROUP&hilight={$id}");
            die();
        }
    } else {
        if ($_REQUEST['PK_Group'] != "") {
            $Group = formdata_from_db($_REQUEST['PK_Group']);
        }
    }

    // Init Availabe Extensions
    $query = "
		SELECT
			Extensions.PK_Extension AS PK_Extension,
			Extension,
            Name
		FROM
			Extensions
		WHERE
			Type IN ('Virtual','SipPhone')
		ORDER BY
			Extension ASC
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $Extensions = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Extensions[] = $row;
    }

    $smarty->assign('Errors', $Errors);
    $smarty->assign('Group', $Group);
    $smarty->assign('Extensions', $Extensions);
    return $smarty->fetch('Groups_Modify.tpl');
}

function formdata_from_db($id) {
    $db = DB::getInstance();
    $data = $_REQUEST;
    $query = "
		SELECT
			PK_Group,
			Name
		FROM
			Groups
		WHERE
			PK_Group = {$data['PK_Group']}
		LIMIT 1
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data = $result->fetch(PDO::FETCH_ASSOC);

    $query = "
		SELECT
			FK_Extension
		FROM
			Extension_Groups
		WHERE
			FK_Group = {$data['PK_Group']}
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $data['Extensions'] = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data['Extensions'][] = $row['FK_Extension'];
    }

    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    $db = DB::getInstance();
    if ($data['PK_Group'] == "") {
        $query = "INSERT INTO Groups () VALUES()";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        $data['PK_Group'] = $db->lastInsertId();
    }

    $query = "
		UPDATE
			Groups
		SET
			Name = '" . $mysqli->real_escape_string($data['Name']) . "'
		WHERE
			PK_Group = " . $mysqli->real_escape_string($data['PK_Group']) . "
		LIMIT 1
	";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    $query = "DELETE FROM Extension_Groups WHERE FK_Group = " . $mysqli->real_escape_string($data['PK_Group']) . "";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    if (is_array($data['Extensions'])) {
        foreach ($data['Extensions'] as $FK_Extension) {
            $query = "INSERT INTO Extension_Groups (FK_Group, FK_Extension) VALUES ({$data['PK_Group']}, $FK_Extension )";
            $db->query($query) or die(print_r($db->errorInfo(), true));
        }
    }

    return $data['PK_Group'];
}

function formdata_validate($data) {
    $errors = array();
    if ($data['Name'] == "") {
        $errors['Name'] = true;
    }

    return $errors;
}

admin_run('Groups_Modify', 'Admin.tpl');
?>