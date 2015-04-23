<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Groups_Popup_Create() {
	session_start();
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init form data (Group, Errors)
	if (@$_REQUEST['submit'] == 'save') {
		$Group  = formdata_from_post();
		$Errors = formdata_validate($Group);

		if (count($Errors) == 0) {
			$id = formdata_save($Group);
			return('
			<script type="text/javascript" src="../script/jquery.selectboxes.js"></script>
			<script>
				$("#Groups", opener.document).addOption("'.$id.'", "'.$Group['Name'].'");
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
			CONCAT(
				IFNULL(Ext_SipPhones.FirstName,''),' ',IFNULL(Ext_SipPhones.LastName,''),
				IFNULL(Ext_Virtual.FirstName,'')  ,' ',IFNULL(Ext_Virtual.LastName,'')
			) AS Name
		FROM
			Extensions
			LEFT JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			LEFT JOIN Ext_Virtual   ON Ext_Virtual.PK_Extension   = Extensions.PK_Extension
		WHERE
			Type IN ('Virtual','SipPhone')
		ORDER BY
			Extension ASC
	";
	$result = mysql_query($query) or die(mysql_error().$query);

	$Extensions = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Extensions[] = $row;
	}

	$smarty->assign('Errors'    , $Errors);
	$smarty->assign('Group'     , $Group);
	$smarty->assign('Extensions', $Extensions);
	return $smarty->fetch('Groups_Popup_Create.tpl');
}

function formdata_from_db($id) {
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
	$result = mysql_query($query) or die(mysql_error());
	$data   = mysql_fetch_assoc($result);

	$query = "
		SELECT
			FK_Extension
		FROM
			Extension_Groups
		WHERE
			FK_Group = {$data['PK_Group']}
	";
	$result = mysql_query($query) or die(mysql_error().$query);

	$data['Extensions'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Extensions'][] = $row['FK_Extension'];
	}

	return $data;
}

function formdata_from_post() {
	return $_POST;
}

function formdata_save($data) {
	if ($data['PK_Group'] == "") {
		$query = "INSERT INTO Groups () VALUES()";
		mysql_query($query) or die(mysql_error().$query);

		$data['PK_Group'] = mysql_insert_id();
	}

	$query = "
		UPDATE
			Groups
		SET
			Name = '".mysql_real_escape_string($data['Name'])."'
		WHERE
			PK_Group = ".mysql_real_escape_string($data['PK_Group'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);

	$query = "DELETE FROM Extension_Groups WHERE FK_Group = ".mysql_real_escape_string($data['PK_Group'])."";
	mysql_query($query) or die(mysql_error().$query);

	if (is_array($data['Extensions'])) {
		foreach ($data['Extensions'] as $FK_Extension) {
			$query = "INSERT INTO Extension_Groups (FK_Group, FK_Extension) VALUES ({$data['PK_Group']}, $FK_Extension )";
			mysql_query($query) or die(mysql_error().$query);
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