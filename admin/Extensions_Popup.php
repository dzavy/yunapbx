<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Groups_Popup_Create() {
	session_start();
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Set 'Extensions'
	$query  = "
		SELECT
			Extension,
			LPAD(Extension,5,' ') AS Extension_Pad,
			Type,
			CONCAT(
				IFNULL(Ext_SipPhones.FirstName,''),' ',IFNULL(Ext_SipPhones.LastName,''),
				IFNULL(Ext_Virtual.FirstName,'')  ,' ',IFNULL(Ext_Virtual.LastName,''),
				IFNULL(Ext_Agent.FirstName,'')    ,' ',IFNULL(Ext_Agent.LastName,''),
				IFNULL(Ext_Queues.Name,''),
				IFNULL(IVR_Menus.Name,'')
			) AS Name
		FROM
			Extensions
			LEFT JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			LEFT JOIN Ext_Virtual   ON Ext_Virtual.PK_Extension   = Extensions.PK_Extension
			LEFT JOIN Ext_Queues    ON Ext_Queues.PK_Extension    = Extensions.PK_Extension
			LEFT JOIN Ext_IVR       ON Ext_IVR.PK_Extension       = Extensions.PK_Extension
				LEFT JOIN IVR_Menus ON Ext_IVR.FK_Menu = IVR_Menus.PK_Menu
			LEFT JOIN Ext_Agent     ON Ext_Agent.PK_Extension     = Extensions.PK_Extension
		WHERE
			( NOT Type  LIKE '%Reserved%' )
		ORDER BY
			Extension_Pad ASC
	";
	$result = mysql_query($query) or die(mysql_error());

	$Extensions = array();
	while ($row = mysql_fetch_assoc($result)) {
		$Extensions[] = $row;
	}

	$smarty->assign('FillID'    , $_REQUEST['FillID']);
	$smarty->assign('Extensions', $Extensions);
	return $smarty->fetch('Extensions_Popup.tpl');
}

admin_run('Groups_Popup_Create', 'AdminPopup.tpl');
?>