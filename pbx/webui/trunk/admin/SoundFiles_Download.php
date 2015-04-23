<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function SoundFiles_Download() {
	session_start();
	$session = &$_SESSION['SoundFilesAjax'];
	
	$PK_SoundFile = $_REQUEST['PK_SoundFile'];
	
	$query  = "SELECT Filename FROM SoundFiles WHERE PK_SoundFile = $PK_SoundFile LIMIT 1";
	$result = mysql_query($query) or die(mysql_error().$query);
	$row    = mysql_fetch_assoc($result);
	$Filename = $row['Filename'];
	
	if (file_exists($Filename)) {
		header("Content-type: ".mime_content_type($Filename));
		header("Content-Disposition: attachment; filename=\"".basename($Filename)."\"");
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

admin_run('SoundFiles_Download');
?>