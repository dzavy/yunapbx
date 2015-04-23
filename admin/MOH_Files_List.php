<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/moh_utils.inc.php');

function MOH_Files_List() {
	session_start();
	$session = &$_SESSION['MOH_Files_List'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$errors = array();
	// Init message (Message)
	$Message = $_REQUEST['msg'];
	
	if ($_REQUEST['submit']){			
		$selectedPKFiles = $_REQUEST['Files'];
		if (is_array($selectedPKFiles)) {
			$move_PK_Group   = $_REQUEST['move_group'];
			$copy_PK_Group   = $_REQUEST['copy_group'];	
			$submit = $_REQUEST['submit'];
			switch ($submit) {
				case 'delete':						
					foreach  ($selectedPKFiles as $PK_File) { $errors = delete_file($PK_File);               }				
					break;		
				case 'move':
					foreach  ($selectedPKFiles as $PK_File) { $errors = move_file($PK_File, $move_PK_Group); }
					break;		
				case 'copy':
					foreach  ($selectedPKFiles as $PK_File) { $errors = copy_file($PK_File, $copy_PK_Group); }
					break;
			}	
			if (count($errors) == 0) {
				$Message = strtoupper($submit)."_SUCCESSFULLY";	
			}
		} else {
			$errors['EmptySelection'] = true;
		}
	}
		
	
	
	// Init no element on page (PageSize)
	$PageSize = 50;
	
	// Init sort order (Order)
	if ($session['Sort'] == $_REQUEST['Sort']) {
		$Order = ($session['Order']=="asc"?"desc":"asc");
	} elseif ($session['Sort'] != $_REQUEST['Sort']) {
		$Order = 'asc';
	}
	$session['Order'] = $Order;

	// Init sort field (Sort)
	if(isset($_REQUEST['Sort'])) {
		$Sort = $_REQUEST['Sort'];
	} else {
		$Sort = 'Filename';
	}
	$session['Sort'] = $Sort;
	
	// Init listing start (Start)	
	if(isset($_REQUEST['Start'])) {
		$Start = $_REQUEST['Start'];
	} else {
		$Start = 0;
	}
	
	// Init total entries (Total)
	$query  = "SELECT COUNT(PK_File) FROM Moh_Files;";
	$result = mysql_query($query) or die(mysql_error());
	$row    = mysql_fetch_array($result);
	$Total  = $row[0];

	// Init files list (Files)
	$Files = array();
	$query = "
		SELECT
			PK_File               AS `_PK_`, 
			Filename              AS `Filename`, 
			Fileext               AS `Fileext`, 
			`Order`               AS `Order`, 
			Moh_Files.DateCreated AS `DateCreated`,
			Moh_Groups.Name       AS `Group`,
			Moh_Groups.PK_Group   AS `_PK_Group_`
		FROM
			Moh_Files 
			INNER JOIN Moh_Groups ON FK_Group = PK_Group
		ORDER BY 
			`$Sort` $Order
		LIMIT $Start, $PageSize
	";
	
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$Files[] = $row;
	}
	
	// Init file list end (End)
	$End = $Start + count($Files);	
	
	// Init available groups (Groups)
	$query  = "SELECT * FROM Moh_Groups ORDER BY Name";
	$result =  mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$Groups[] = $row;
	}
		
	$smarty->assign('Errors'  , $errors);
	$smarty->assign('Files'   , $Files);
	$smarty->assign('Groups'  , $Groups);
	$smarty->assign('Sort'    , $Sort );
	$smarty->assign('Order'   , $Order);
	$smarty->assign('Start'   , $Start);
	$smarty->assign('End'     , $End);
	$smarty->assign('Total'   , $Total);
	$smarty->assign('PageSize', $PageSize);
	$smarty->assign('Message' , $Message);
	$smarty->assign('Hilight' , $_REQUEST['hilight']);
	
	return $smarty->fetch('MOH_Files_List.tpl');	
}

admin_run('MOH_Files_List', 'Admin.tpl');
	