<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Phone_Create() {
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	// Init default template (FK_Template)
	$query  = "SELECT PK_Template FROM Templates WHERE Protected = 1 LIMIT 1";
	$result = mysql_query($query) or die(mysql_error().$query);
	$row    = mysql_fetch_array($result);
	$FK_Template = $row[0];

	// If user chosed Type and FK_Template
	if ($_REQUEST['submit'] == 'submit`') {
		myprint ($_REQUEST);
		$Type   = $_REQUEST['Type'];
		$Vendor = $_REQUEST['Vendor'];
		$Model  = $_REQUEST['Model'];
		

		$location = form_location($Type);
	
		if ($location != "") {
			return " <script> document.location='$location'	</script>	";
		} else {
			$Errors['Type'] = true;
		}
	}

	$smarty->assign('Cisco'      , $Cisco);
	$smarty->assign('Tehnoton'   , $Tehnoton);
	$smarty->assign('Type'       , $Type);
	$smarty->assign('Errors'     , $Errors);

	return $smarty->fetch('Phone_Create.tpl');
}

function form_location($Type) {
	$location = "Phone_Modify_{$Type}.php";
	return $location;
}

admin_run('Phone_Create', 'Admin.tpl');