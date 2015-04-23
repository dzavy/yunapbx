<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__).'/../include/moh_utils.inc.php');

function MOH_Files_Download() {
	session_start();
	$session = &$_SESSION['MOH_Files_Download'];
	
	$PK_File = intval($_REQUEST['PK_File']);
	
	$query  = "SELECT * FROM Moh_Files WHERE PK_File = '{$PK_File}' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error().$query);
	$File   = mysql_fetch_assoc($result);
	
	$Filename = moh_filename($PK_File);
	
	if (file_exists($Filename)) {
		header("Content-type: ".mime_content_type($Filename));
		header("Content-Disposition: attachment; filename=\"".basename($File['Filename'].".".$File['Extension'])."\"");
		$handle = fopen($Filename,'r');
		while (!feof($handle)) {
	  		echo fread($handle, 8192);
		}
		fclose($handle);
	} else {
		echo "File not found.";
	}
	die();
}

admin_run('MOH_Files_Download');
?>
