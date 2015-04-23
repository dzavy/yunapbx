<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function GSMModems_List() {
	session_start();
	$session = &$_SESSION['GSMModems_List'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');
 
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
	if(isset($_REQUEST['Sort'])) 
		 { $Sort = $_REQUEST['Sort'];}
	else 
		 { $Sort = 'Name';       	 }
	
	$session['Sort'] = $Sort;

	// Init listing start (Start)
	if(isset($_REQUEST['Start'])) 
		 {	$Start = $_REQUEST['Start']; }
	else 
		 {  $Start = 0;	}

	// Init total entries (Total)
	$query  = "SELECT COUNT(*) FROM GSMModems";
	$result = mysql_query($query) or die(mysql_error().$query);
	$row    = mysql_fetch_array($result);
	$Total  = $row[0];

	// Init table fields (Extensions)
	$Modems = array();
	$query = "
			SELECT
				PK_GsmModem       AS _PK_,
				Name              AS Name,
				AudioPort         AS AudioPort,
				DataPort          AS DataPort,
				CallbackExtension AS CallbackExtension
			FROM
				GSMModems
		ORDER BY
			$Sort $Order
		LIMIT $Start, $PageSize
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	while ($row = mysql_fetch_assoc($result)) {
		$Modems[] = $row;
	}

	// Init end record (End)
	$End = count($Modems);
	
	$smarty->assign('Errors'     , $Errors);
	$smarty->assign('Modems'     , $Modems);
	$smarty->assign('Sort'       , $Sort);
	$smarty->assign('Order'      , $Order);
	$smarty->assign('Start'      , $Start);
	$smarty->assign('End'        , $End);
	$smarty->assign('Total'      , $Total);
	$smarty->assign('PageSize'   , $PageSize);
	$smarty->assign('Message'    , $Message);
	$smarty->assign('Hilight'    , $_REQUEST['hilight']);

	return $smarty->fetch('GSMModems_List.tpl');
}

function formdata_from_post() {
	return $_POST;
}

function formdata_save($data) {
    pbx_var_set("RTP_PortStart", $data['RTP_PortStart']);
	pbx_var_set("RTP_PortEnd"  , $data['RTP_PortEnd'])  ;
}

function formdata_from_db() {
	$data = array();
	$data['RTP_PortStart'] = pbx_var_get("RTP_PortStart");
	$data['RTP_PortEnd']   = pbx_var_get("RTP_PortEnd");
	return $data;
}

function formdata_validate($data) {

	$errors = array();
	
	if ( (!is_numeric($data['RTP_PortStart'])) 	||  ($data['RTP_PortStart']	< 0) ) 
		$errors['RTP_PortStart'] = true; 	
	
	if ( (!is_numeric($data['RTP_PortEnd'])) 	||  ($data['RTP_PortEnd']	> 99999) ) 
		$errors['RTP_PortEnd'] = true; 	
	
	if 	($data['RTP_PortStart'] > $data['RTP_PortEnd']){
		$errors['RTP_PortStart'] = true; 	
		$errors['RTP_PortEnd'] = true; 	
	}	
		
	return $errors;
}

admin_run('GSMModems_List', 'Admin.tpl');