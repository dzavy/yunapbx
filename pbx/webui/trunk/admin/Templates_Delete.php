<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Templates_Delete() {
	$smarty  = smarty_init(dirname(__FILE__).'/templates');
	
	$PK_Template = $_REQUEST['PK_Template'];
	
	// In confirmed, do the actual delete
	if (@$_REQUEST['submit'] == 'delete_confirm') {
		$query = "DELETE FROM Templates WHERE PK_Template = $PK_Template AND Protected = 0 LIMIT 1";
		mysql_query($query) or die(mysql_error());
		
		if (mysql_affected_rows() != 1) {
			return ;
		}
		
		$query = "DELETE FROM Template_Codecs WHERE FK_Template = $PK_Template";
		mysql_query($query) or die(mysql_error());
		
		$query = "DELETE FROM Template_Groups WHERE FK_Template = $PK_Template";
		mysql_query($query) or die(mysql_error());
		
		$query = "DELETE FROM Template_Features WHERE FK_Template = $PK_Template";
		mysql_query($query) or die(mysql_error());
		
		header('Location: Templates_List.php?msg=DELETE_TEMPLATE');
		die();
	}
	
	// Init template info (Template)
	$query = "
		SELECT
			PK_Template,
			Name,
			FirstName_Editable,
			LastName_Editable,
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
	$result = mysql_query($query) or die(mysql_error());
	$Template   = mysql_fetch_assoc($result);
	
	$smarty->assign('Template' , $Template);
	
	return $smarty->fetch('Templates_Delete.tpl');
}

admin_run('Templates_Delete', 'Admin.tpl');
?>