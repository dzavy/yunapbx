<?php

function DB_Extension($ext) {
	$query = "SELECT * FROM Extensions WHERE Extension = '$ext' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error().$query);
	if (mysql_num_rows($result) != '1') {
		return null;
	}

	return mysql_fetch_assoc($result);
}

function DB_Extension_Name($ext) {
	$query = "SELECT PK_Extension, Type FROM Extensions WHERE Extension = '$ext' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error().$query);
	$row = mysql_fetch_assoc($result);
	$id   = $row['PK_Extension'];
	$type = $row['Type'];

	switch ($type) {
		case 'SipPhone':
			$query = "SELECT CONCAT(FirstName,' ',LastName) FROM Ext_SipPhones WHERE PK_Extension = '$id'";
			break;
		case 'Virtual':
			$query = "SELECT Name FROM Ext_Virtual WHERE PK_Extension = '$id'";
			break;
		case 'Queue':
			$query = "SELECT Name FROM Ext_SipPhones WHERE PK_Extension = '$id'";
			break;
		case 'IVR':
			$query = "SELECT Name FROM Ext_IVR WHERE PK_Extension = '$id'";
			break;
		default:
			break;
	}

	$result = mysql_query($query) or die(mysql_error().$query);
	$row = mysql_fetch_row($result);
	return $row[0];
}

function Database_Entry($table, $id) {
	$query = "SELECT * FROM `$table` WHERE PK_Extension = '$id' LIMIT 1";
	$result = mysql_query($query) or die(mysql_error().$query);
	if (mysql_num_rows($result) != '1') {
		return null;
	}

	return mysql_fetch_assoc($result);
}

function SoundFile($PK_SoundEntry, $PK_SoundLanguage=0) {
	global $agi, $logger;
	
	$query  = "SELECT * FROM SoundFiles WHERE FK_SoundEntry = '{$PK_SoundEntry}' AND FK_SoundLanguage='{$PK_SoundLanguage}' LIMIT 1";
	$result = mysql_query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
	$file   = mysql_fetch_assoc($result);
	
	$file_to_play = pathinfo($file['Filename']);
	$file_to_play = "{$file_to_play['dirname']}/{$file_to_play['filename']}";
	
	return $file_to_play;
}
?>
