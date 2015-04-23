<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Groups_List() {
	session_start();
	$session = &$_SESSION['Groups'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');
		
	// Init message (Message)
	$Message = $_REQUEST['msg'];
	
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
		$Sort = 'Name';
	}
	$session['Sort'] = $Sort;
	
	// Init listing start (Start)
	if(isset($_REQUEST['Start'])) {
		$Start = $_REQUEST['Start'];
	} else {
		$Start = 0;
	}
		
	// Init total entries (Total)
	$query  = "SELECT COUNT(PK_Group) FROM Groups;";
	$result = mysql_query($query) or die(mysql_error());
	$row    = mysql_fetch_array($result);
	$Total  = $row[0];

	// Init table fields (Groups)
	$Groups = array();
	$query = "
		SELECT
			PK_Group  		    AS _PK_,
			Name                AS Name,
			Count(FK_Extension) AS Members,
			DATE_FORMAT(DateCreated,'%m/%d/%y, %h:%i %p') AS DateCreated
		FROM
			Groups
			LEFT JOIN Extension_Groups ON FK_Group = PK_Group
		GROUP BY
			PK_Group
		ORDER BY 
			$Sort $Order
		LIMIT $Start, $PageSize

	";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$Groups[] = $row;
	}
	
	// Init end record (End)
	$End = $Start + count($Groups);
	
	$smarty->assign('Groups'   , $Groups);	
	$smarty->assign('Sort'     , $Sort);
	$smarty->assign('Order'    , $Order);
	$smarty->assign('Start'    , $Start);
	$smarty->assign('End'      , $End);
	$smarty->assign('Total'    , $Total);
	$smarty->assign('PageSize' , $PageSize);
	$smarty->assign('Message'  , $Message);
	$smarty->assign('Hilight'  , $_REQUEST['hilight']);
	
	return $smarty->fetch('Groups_List.tpl');
}

admin_run('Groups_List', 'Admin.tpl');
?>
