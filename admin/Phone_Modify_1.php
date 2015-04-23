<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function Phone_Modify_1() {
	session_start();
	$session = &$_SESSION['Phone_Modify_1'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	myprint($_REQUEST);
	// Init form data 
	if (@$_REQUEST['submit'] == 'save') {
		$Phone = formdata_from_post();		
		$Errors   = formdata_validate($Phone);

		if (count($Errors) == 0) {
			$FK_Model = 1;
			$id = formdata_save($Phone, $FK_Model);	
			header("Location: Phone_Setup.php?msg=MODIFY_PHONE_SETUP&hilight={$PK_Phone}"); die();
		}
	} 
	
	// Init message (Message)
	$Message = $_REQUEST['msg'];
	if (@$_REQUEST['PK_Phone']){
		$PK_Phone = $_REQUEST['PK_Phone'];
		
		// Init available phone
		$Phone = array();
		$query  = "SELECT MAC, IP, `Date`, FK_Model, FK_Extension FROM Phones WHERE PK_Phone=".$PK_Phone;
		$result = mysql_query($query) or die(mysql_error().$query);
		
		$row = mysql_fetch_assoc($result); 
		$Phone = $row;
	  
		// model 
		$query  = "SELECT FK_Vendor, Name FROM Phone_Model WHERE PK_Model=".$Phone['FK_Model'];
		$result = mysql_query($query) or die(mysql_error().$query);	
		$row = mysql_fetch_assoc($result); 
		$Model = $row['Name'];
		$PK_Vendor = $row['FK_Vendor'];
		
		//brand
		$query  = "SELECT Name FROM Phone_Vendor WHERE PK_Vendor=".$PK_Vendor;
		$result = mysql_query($query) or die(mysql_error().$query);	
		$row = mysql_fetch_assoc($result); 
		$Vendor = $row['Name'];
		
		//extension
		$query  = "SELECT Extension FROM Extensions WHERE PK_Extension=".$Phone['FK_Extension'];
		$result = mysql_query($query) or die(mysql_error().$query);	
		$row = mysql_fetch_assoc($result); 
		$Extension = $row['Extension'];	
	}
	
	else {		
		$Models = array();
		$query  = "SELECT * FROM Phone_Model";
		$result = mysql_query($query) or die(mysql_error().$query);
		while ($row = mysql_fetch_assoc($result)) {
			$Models[] = $row;			
		} 
		
		$Vendors = array();
		$query  = "SELECT * FROM Phone_Vendor";
		$result = mysql_query($query) or die(mysql_error().$query);
		while ($row = mysql_fetch_assoc($result)) {
			$Vendors[] = $row;
		} 
	
		$smarty->assign('Vendors' , $Vendors);	
		$smarty->assign('Models'  , $Models);
	}
	
	
	$Phone['Extension']= $Extension;
	$Phone['Model' ]   = $Model;
	$Phone['Vendor']   = $Vendor;
	$Phone['PK_Phone'] = $PK_Phone;
	
	$smarty->assign('Phone'    , $Phone);	
	$smarty->assign('Message'  , $Message);
	$smarty->assign('Errors'   , $Errors);

	return $smarty->fetch('Phone_Modify_1.tpl');
}

function formdata_from_db($id) {

	/*// Init data from 'Extensions'
	$query = "
		SELECT
			PK_,
			Extension,
			FirstName,
			FirstName_Editable,
			LastName,
			LastName_Editable,
			Password,
			Password_Editable,
			Email,
			Email_Editable,
			FK_NATType,
			FK_DTMFMode,
			IVRDial
		FROM
			Ext_SipPhones
			INNER JOIN Extensions ON Extensions.PK_Extension=Ext_SipPhones.PK_Extension
		WHERE
			Ext_SipPhones.PK_Extension = $id
		LIMIT 1
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data   = mysql_fetch_assoc($result);

	// Init data from 'Extension_Codecs'
	$query ="
		SELECT
			FK_Codec
		FROM
			Ext_SipPhones_Codecs
		WHERE
			FK_Extension = {$data['PK_Extension']}
	";
	$result = mysql_query($query) or die(mysql_error().$query);
	$data['Codecs'] = array();
	while ($row = mysql_fetch_assoc($result)) {
		$data['Codecs'][] = $row['FK_Codec'];
	}
	
	return $data;*/
}

function formdata_from_post() {
	return $_POST;
}

function formdata_save($data, $FK_Model) {
	
	//PK_Extension
	$query  = "SELECT PK_Extension FROM Extensions WHERE Extension=".$data['Extension'];
	$result = mysql_query($query) or die(mysql_error().$query);	
	$row = mysql_fetch_assoc($result); 
	$data['PK_Extension'] = $row['PK_Extension'];	
	
	if ($data['PK_Phone'] == "") {
		$query = "INSERT INTO 
							Phones(MAC, IP, FK_Model, FK_Extension) 
		          VALUES   (".mysql_real_escape_string($data['MAC']).",
				            ".mysql_real_escape_string($data['IP']).",
							".mysql_real_escape_string($FK_Model).",
							".mysql_real_escape_string($data['PK_Extension']).")";
										
		mysql_query($query) or die(mysql_error().$query);
		$data['PK_Extension'] = mysql_insert_id();

		$query = "INSERT INTO Ext_SipPhones(PK_Extension) VALUES({$data['PK_Extension']})";
		mysql_query($query) or die(mysql_error().$query);
	} else {
		// Update 'Phones'
		$query = "
			UPDATE
				Phones
			SET
				MAC          = '".mysql_real_escape_string($data['MAC'])."',
				IP           = '".mysql_real_escape_string($data['IP'])."',						
				FK_Extension = '".mysql_real_escape_string($data['PK_Extension'])."'
			WHERE
				PK_Phone = ".mysql_real_escape_string($data['PK_Phone'])."
			LIMIT 1
		";
		mysql_query($query) or die(mysql_error().$query);
	}


}

function formdata_validate($data) {
	$errors = array();
	return $errors;
}

admin_run('Phone_Modify_1', 'Admin.tpl');