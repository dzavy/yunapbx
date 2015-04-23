<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');


function IVR_Menus_Delete() {
	session_start();
	$session = &$_SESSION['IVR_Menus_Delete'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$PK_Menu = $_REQUEST['PK_Menu'];

	// Get information about this ivr menu
	$query    = "SELECT * FROM IVR_Menus WHERE PK_Menu = $PK_Menu LIMIT 1";
	$result   = mysql_query($query) or die(mysql_error().$query);
	$IVR_Menu = mysql_fetch_assoc($result);
	
	
	// Get a list of ivr actions pointing to this menu
	$IVR_Actions = array();
	$query = "
		SELECT
			IVR_Actions.PK_Action,
			IVR_Menu_Parent.Name AS Parent,
			IVR_Menu_Child.Name AS Child,
			IVR_Actions.PK_Action,
			IVR_Actions.Type,
			IVR_Actions.Order
		FROM
			IVR_Actions
			INNER JOIN IVR_Action_Params ON IVR_Action_Params.FK_Action = IVR_Actions.PK_Action
			INNER JOIN IVR_Menus IVR_Menu_Parent ON IVR_Menu_Parent.PK_Menu = IVR_Actions.FK_Menu
			INNER JOIN IVR_Menus IVR_Menu_Child ON IVR_Menu_Child.PK_Menu = IVR_Action_Params.Value
		WHERE
			IVR_Action_Params.Name = 'Menu' AND IVR_Action_Params.Value = '$PK_Menu'
			AND
			IVR_Menu_Parent.PK_Menu != '$PK_Menu'
	";
	$result = mysql_query($query) or die($query.mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$IVR_Actions[] = $row;
	}
	
	// Get a list of ivr options pointing to this ivr
	$IVR_Options = array();
	$query = "
		SELECT
			IVR_Options.PK_Option AS PK_Option,
			IVR_Options.`Key`     AS `Key`,
			IVR_Menu_Parent.Name  AS Parent,
			IVR_Menu_Child.Name   AS Child
		FROM
			IVR_Options
			INNER JOIN IVR_Menus IVR_Menu_Parent ON IVR_Menu_Parent.PK_Menu = IVR_Options.FK_Menu
			INNER JOIN IVR_Menus IVR_Menu_Child  ON IVR_Menu_Child.PK_Menu  = IVR_Options.FK_Menu_Entry
		WHERE
			IVR_Options.FK_Menu_Entry = $PK_Menu
			AND
			IVR_Menu_Parent.PK_Menu != '$PK_Menu'
		ORDER BY
			`Key`
	";
	$result = mysql_query($query) or die($query.mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$IVR_Options[] = $row;
	}
	
	
	// Get a list of ivr extensions pointing to this ivr
	$IVR_Extensions = array();
	$query = "
		SELECT
			Extensions.PK_Extension,
			Extensions.Extension,
			Extensions.DateCreated,
			IVR_Menus.Name
		FROM
			Extensions
			INNER JOIN Ext_IVR ON Ext_IVR.PK_Extension = Extensions.PK_Extension
			INNER JOIN IVR_Menus ON IVR_Menus.PK_Menu = Ext_IVR.FK_Menu
		WHERE
			IVR_Menus.PK_Menu = $PK_Menu
		ORDER BY
			Extension		
	";
	$result = mysql_query($query) or die($query.mysql_error());
	while ($row = mysql_fetch_assoc($result)) {
		$IVR_Extensions[] = $row;
	}
	
	// In confirmed, do the actual delete
	if (count($IVR_Extensions) == 0 && count($IVR_Extensions)==0 && count($IVR_Extensions)==0) {
		if (@$_REQUEST['submit'] == 'delete_confirm') {
		
			$query = "DELETE FROM IVR_Action_Params WHERE FK_Action IN (SELECT PK_Action FROM IVR_Actions WHERE FK_Menu = $PK_Menu)";
			mysql_query($query) or die(mysql_error().$query);		
	
			$query = "DELETE FROM IVR_Actions WHERE FK_Menu = $PK_Menu";
			mysql_query($query) or die(mysql_error().$query);		
			
			$query = "DELETE FROM IVR_Menus WHERE PK_Menu = $PK_Menu LIMIT 1";
			mysql_query($query) or die(mysql_error().$query);
	
			header('Location: IVR_Menus.php');
			die();
		}
	}
	
	$smarty->assign('IVR_Extensions', $IVR_Extensions);
	$smarty->assign('IVR_Options'   , $IVR_Options);
	$smarty->assign('IVR_Actions'   , $IVR_Actions);
	$smarty->assign('IVR_Menu'      , $IVR_Menu);
	
	return $smarty->fetch('IVR_Menus_Delete.tpl');
}

admin_run('IVR_Menus_Delete', 'Admin.tpl');
?>