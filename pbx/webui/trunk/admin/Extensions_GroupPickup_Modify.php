<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Extensions_GroupPickup_Modify() {
	session_start();
	$session = &$_SESSION['Extensions_GroupPickup_Modify'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	/////myprint($_REQUEST);

	// Init message (Message)
	$Message = $_REQUEST['msg'];

	// Groups
	$query  = "SELECT PK_Group, Name FROM Groups";
	$result = mysql_query($query) or die(mysql_error().$query);
	$Groups = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Groups[] = $row;
	}

	// Init Available Accounts (Accounts)
	$query  = "
		SELECT
			Extensions.PK_Extension,
			Extension,
			CONCAT(IFNULL(Ext_SipPhones.FirstName,''),IFNULL(Ext_Virtual.FirstName,'')) AS FirstName,
			CONCAT(IFNULL(Ext_SipPhones.LastName ,''),IFNULL(Ext_Virtual.LastName,''))  AS LastName
		FROM
			Extensions
			LEFT JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			LEFT JOIN Ext_Virtual   ON Ext_Virtual.PK_Extension   = Extensions.PK_Extension
		WHERE
			Extensions.Type IN ('Virtual', 'SipPhone')
		ORDER BY Extension
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$Accounts = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Accounts[] = $row;
	}



	// Init form data (GroupPickup)
	if (@$_REQUEST['submit'] == 'save') {
		$GroupPickup = formdata_from_post();
		$Errors   = formdata_validate($GroupPickup);

		if (count($Errors) == 0) {
			$id = formdata_save($GroupPickup);
			header("Location: Extensions_List.php?msg=MODIFY_GroupPickup_EXTENSION&hilight={$id}"); die();
		}
	} elseif (@$_REQUEST['PK_Extension'] != "") {
		$GroupPickup = formdata_from_db($_REQUEST['PK_Extension']);
	} else {
		$GroupPickup = array();
	}

	$smarty->assign('GroupPickup'   , $GroupPickup);
	$smarty->assign('Groups'        , $Groups);
	$smarty->assign('Accounts'      , $Accounts);
	$smarty->assign('Message'       , $Message);
	$smarty->assign('Errors'        , $Errors);


	return $smarty->fetch('Extensions_GroupPickup_Modify.tpl');
}

function formdata_from_db($id) {

	// Init data from 'Extensions'
	$query = "
		SELECT
			*
		FROM
			Ext_GroupPickup
			INNER JOIN Extensions ON Extensions.PK_Extension = Ext_GroupPickup.PK_Extension
		WHERE
			Ext_GroupPickup.PK_Extension = $id
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error());
	$data   = mysql_fetch_assoc($result);

	// Init data from 'GroupPickup_Memebers'
	$data['Members'] = array();
	$query ="SELECT FK_Ext_Member, FK_Ext_Group FROM Ext_GroupPickup_Members WHERE FK_Extension = $id";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_array($result)) {
		$data['Members'][] = $row[0];
		if ($row[1] != 0) { $data['FK_Group_Member'] = $row[1]; }
	}

	// Init data from 'GroupPickup_Admins'
	$data['Admins'] = array();
	$query ="SELECT FK_Ext_Admin, FK_Ext_Group FROM Ext_GroupPickup_Admins WHERE FK_Extension = $id";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_array($result)) {
		$data['Admins'][] = $row[0];
		if ($row[1] != 0) { $data['FK_Group_Admin'] = $row[1]; }
	}

	return $data;
}

function formdata_from_post() {
	return $_POST;
}

function formdata_save($data) {

	if ($data['PK_Extension'] == "") {
		$query = "INSERT INTO Extensions(Feature, Type, Extension) VALUES(1, 'GroupPickup', '".mysql_real_escape_string($data['Extension'])."')";
		mysql_query($query) or die(mysql_error().$query);
		$data['PK_Extension'] = mysql_insert_id();

		$query = "INSERT INTO Ext_GroupPickup(PK_Extension) VALUES('".mysql_real_escape_string($data['PK_Extension'])."')";
		mysql_query($query) or die(mysql_error().$query);
	}

	// Update 'Ext_GroupPickup'
	$query = "
		UPDATE
			Ext_GroupPickup
		SET
			Use_Members_ByAccount = '".mysql_real_escape_string($data['Use_Members_ByAccount'])."',
			Use_Admins_ByAccount  = '".mysql_real_escape_string($data['Use_Admins_ByAccount'])."'
		WHERE
			PK_Extension       = ".mysql_real_escape_string($data['PK_Extension'])."
		LIMIT 1
	";
	mysql_query($query) or die(mysql_error().$query);

	// Update 'Ext_GroupPickup_Members'
	$query = "DELETE FROM Ext_GroupPickup_Members WHERE FK_Extension = ".mysql_real_escape_string($data['PK_Extension'])." ";
	mysql_query($query) or die(mysql_error());

	if (is_array($data['Members'])) {
		if($data['Use_Members_ByAccount']){
			foreach ($data['Members'] as $member) {
				$query = "INSERT INTO Ext_GroupPickup_Members (FK_Extension, FK_Ext_Member,FK_Ext_Group) VALUES ({$data['PK_Extension']}, {$member}, 0)";
				mysql_query($query) or die(mysql_error().$query);
			}
		}
	}else{
		$query = "INSERT INTO Ext_GroupPickup_Members (FK_Extension, FK_Ext_Member,FK_Ext_Group) VALUES ({$data['PK_Extension']}, 0, {$data['GroupsM']})";
		mysql_query($query) or die(mysql_error().$query);
	}

	// Update 'Ext_GroupPickup_Admins'
	$query = "DELETE FROM Ext_GroupPickup_Admins WHERE FK_Extension = ".mysql_real_escape_string($data['PK_Extension'])." ";
	mysql_query($query) or die(mysql_error());

	if (is_array($data['Admins'])) {
		if($data['Use_Admins_ByAccount']){
			foreach ($data['Admins'] as $admin) {
				$query = "INSERT INTO Ext_GroupPickup_Admins (FK_Extension, FK_Ext_Admin,FK_Ext_Group) VALUES ({$data['PK_Extension']}, {$admin}, 0)";
				mysql_query($query) or die(mysql_error().$query);
			}
		}
	}else{
		$query = "INSERT INTO Ext_GroupPickup_Admins (FK_Extension, FK_Ext_Admin,FK_Ext_Group) VALUES ({$data['PK_Extension']}, 0, {$data['GroupsA']})";
		mysql_query($query) or die(mysql_error().$query);
	}
	return $data['PK_Extension'];
}

function formdata_validate($data) {
	$errors = array();

	if ($data['PK_Extension'] == '') {
		$create_new = true;
	}

	if ($create_new) {
		// Check if extension is empty
		if ($data['Extension'] == "") {
			$errors['Extension']['Invalid'] = true;
		// Check if Extension is numeric
		} elseif (intval($data['Extension'])."" != $data['Extension']) {
			$errors['Extension']['Invalid'] = true;
		// Check if extension is proper length
		} elseif (strlen($data['Extension']) < 3 || strlen($data['Extension']) > 5) {
			$errors['Extension']['Invalid'] = true;
		// Check if extension in unique
		} else {
			$query  = "SELECT Extension FROM Extensions WHERE Extension = '{$data['Extension']}' LIMIT 1";
			$result = mysql_query($query) or die(mysql_error().$query);
			if (mysql_num_rows($result) > 0) {
				$errors['Extension']['Duplicate'] = true;
			}

		}
	}

//	// Check if first name is proper length
//	if ((strlen($data['Name'])<1) || (strlen($data['Name'])>20)) {
//		$errors['Name']['Invalid'] = true;
//	}

	return $errors;
}

admin_run('Extensions_GroupPickup_Modify', 'Admin.tpl');

?>