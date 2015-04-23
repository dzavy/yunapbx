<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');


function Phone_Setup() {
	session_start();
	$session = &$_SESSION['Phone_Setup'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$Message = $_REQUEST['msg'];		
	$PageSize = 4;
	
	// Init sort order (Order)
	if ($session['Sort'] == $_REQUEST['Sort']) 
		{ $Order = ($session['Order']=="asc"?"desc":"asc");	} 
	elseif ($session['Sort'] != $_REQUEST['Sort'])
		{ $Order = 'asc';	}
		
	$session['Order'] = $Order;

	// Init sort field (Sort)
	if(isset($_REQUEST['Sort'])) 		
			{ $Sort = $_REQUEST['Sort'];}
	else    { $Sort = 'Optionals';      }
	   
	$session['Sort'] = $Sort;

	// Init listing start (Start)
	if(isset($_REQUEST['Start'])) 
		 {	$Start = $_REQUEST['Start']; }
	else {  $Start = 0;	                 }

	// Init total entries (Total)
	$query  = "SELECT COUNT(*) FROM Phones";
	$result = mysql_query($query) or die(mysql_error().$query);
	$row    = mysql_fetch_array($result);
	$Total  = $row[0];
	
	
	$Phones = array();
	$Phones= formdata_from_db();	
	
	$i = 0;

	foreach($Phones as $Phone){
		$query  = "	SELECT 
						FK_Vendor, Name 
					FROM 
						`Phone_Model`	
					WHERE
						PK_Model = ".$Phone['FK_Model'];
		$result = mysql_query($query) or die(mysql_error().$query);
		
		$row = mysql_fetch_assoc($result);
		$PK_Vendor = $row['FK_Vendor'];
		$Model     = $row['Name'];		
		
		///////////////////////////////////
		
		$query  = "	SELECT 
						Name 
					FROM 
						`Phone_Vendor`	
					WHERE
						PK_Vendor = ".$PK_Vendor;											
		$result = mysql_query($query) or die(mysql_error().$query);		
		$row    = mysql_fetch_assoc($result);
		$Vendor = $row['Name'];
		
		///////////////////////////////////
		$query  = "	SELECT 
						Extension 
					FROM 
						`Extensions`	
					WHERE
						PK_Extension = ".$Phone['FK_Extension'];											
		$result = mysql_query($query) or die(mysql_error().$query);		
		$row    = mysql_fetch_assoc($result);
		$Extension = $row['Extension'];	
		
		///////////////////////////////////
		
		$Phones[$i]['Extension'] = 	$Extension;
		$Phones[$i]['Model'] = $Model;
		$Phones[$i]['Vendor'] = $Vendor;  
		$i++;		
	}	
	
	$End = $Start + $PageSize;
	
	$smarty->assign('Errors'    , $Errors  );	
	$smarty->assign('Sort'      , $Sort    );
	$smarty->assign('Order'     , $Order   );
	$smarty->assign('Start'     , $Start   );
	$smarty->assign('End'       , $End     );
	$smarty->assign('Total'     , $Total   );
	$smarty->assign('PageSize'  , $PageSize);
	$smarty->assign('Message'   , $Message );	
	
	$smarty->assign('Phones', $Phones);
	
	return $smarty->fetch('Phone_Setup.tpl');
}

function formdata_from_db() {
	$data = array();
	$query  = "
				SELECT 
					*
				FROM 
					`Phones`
				ORDER BY 
					`MAC` 
				ASC
				LIMIT 0 , 30 	
		
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	
	while ($row = mysql_fetch_assoc($result)) {
		$data[] = $row;
	}
	return $data;
}

admin_run('Phone_Setup', 'Admin.tpl');