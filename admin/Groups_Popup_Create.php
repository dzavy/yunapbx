<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Groups_Popup_Create() {
    global $mysqli;
    
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init form data (Group, Errors)
    if (@$_REQUEST['submit'] == 'save') {
        $Group = formdata_from_post();
        $Errors = formdata_validate($Group);

        if (count($Errors) == 0) {
            $id = formdata_save($Group);
            return('
			<script type="text/javascript" src="../script/jquery.selectboxes.js"></script>
			<script>
				$("#Groups", opener.document).addOption("' . $id . '", "' . $Group['Name'] . '");
				window.close();
			</script>
			');
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
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $Extensions = array();
    while ($row = $result->fetch_assoc()) {
        $Extensions[] = $row;
    }

    $smarty->assign('Errors', $Errors);
    $smarty->assign('Group', $Group);
    $smarty->assign('Extensions', $Extensions);
    return $smarty->fetch('Groups_Popup_Create.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
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
    $result = $mysqli->query($query) or die($mysqli->error);
    $data = $result->fetch_assoc();

    $query = "
		SELECT
			FK_Extension
		FROM
			Extension_Groups
		WHERE
			FK_Group = {$data['PK_Group']}
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $data['Extensions'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Extensions'][] = $row['FK_Extension'];
    }

    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    global $mysqli;
    if ($data['PK_Group'] == "") {
        $query = "INSERT INTO Groups () VALUES()";
        $mysqli->query($query) or die($mysqli->error . $query);

        $data['PK_Group'] = $mysqli->insert_id;
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
    $mysqli->query($query) or die($mysqli->error . $query);

    $query = "DELETE FROM Extension_Groups WHERE FK_Group = " . $mysqli->real_escape_string($data['PK_Group']) . "";
    $mysqli->query($query) or die($mysqli->error . $query);

    if (is_array($data['Extensions'])) {
        foreach ($data['Extensions'] as $FK_Extension) {
            $query = "INSERT INTO Extension_Groups (FK_Group, FK_Extension) VALUES ({$data['PK_Group']}, $FK_Extension )";
            $mysqli->query($query) or die($mysqli->error . $query);
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

admin_run('Groups_Popup_Create', 'AdminPopup.tpl');
?>