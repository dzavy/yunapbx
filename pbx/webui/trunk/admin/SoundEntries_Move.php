<?php
include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');

function SoundEntries_Move() {
	
	$PK_SoundEntries = $_REQUEST['PK_SoundEntries'];
	$PK_Folder       = $_REQUEST['PK_Folder'];
	
	$query = "UPDATE SoundEntries SET FK_SoundFolder = $PK_Folder WHERE PK_SoundEntry IN ($PK_SoundEntries)";
	mysql_query($query) or die(mysql_error());
	
	header('Location: SoundEntries_List.php?msg=MOVE_ENTRY');
	die();
}

admin_run('SoundEntries_Move');
?>