<?php

include_once(dirname(__FILE__).'/../include/db_utils.inc.php');
include_once(dirname(__FILE__).'/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__).'/../include/admin_utils.inc.php');
include_once(dirname(__FILE__)."/../lib/AsteriskManager.class.php");
include_once(dirname(__FILE__).'/../include/asterisk_utils.inc.php');

function SoundFiles_Ajax() {
	session_start();
	$session = &$_SESSION['SoundFilesAjax'];
	$smarty  = smarty_init(dirname(__FILE__).'/templates');

	$data     = $_REQUEST;
	$response = array();

	switch ($data['Action']) {
		case 'RecordSound':
			$UID = uniqid(time());
			asterisk_RecordSound($_REQUEST['Extension'], '/usr/share/asterisk/sounds/vm-then-pound', $UID);

			$response['TmpFile'] = "/tmp/$UID.wav";

			break;

		case 'PlayFile':
			$PK_SoundFile = $_REQUEST['PK_SoundFile'];
			$Extension    = $_REQUEST['Extension'];

			$query  = "SELECT Filename FROM SoundFiles WHERE PK_SoundFile = $PK_SoundFile LIMIT 1";
			$result = mysql_query($query) or die(mysql_error().$query);
			$row    = mysql_fetch_row($result);

			$File = pathinfo($row[0]);

			asterisk_PlaySound($Extension, $File['dirname'].'/'.$File['filename']);
			break;

		case 'VerifyFile':
			if (file_exists($_REQUEST['File'])) {
				$response['FileExists'] = 1;
			} else {
				$response['FileExists'] = 0;
			}
			break;
	}

	echo json_encode($response);
}

admin_run('SoundFiles_Ajax');
?>