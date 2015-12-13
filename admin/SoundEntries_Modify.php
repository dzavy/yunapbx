<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function SoundEntries_Modify() {
    global $mysqli;
    
    $session = &$_SESSION['SoundEntries_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_SoundEntry = intval($_REQUEST['PK_SoundEntry']);

    $SoundFiles = array();
    $query = "
		SELECT
			SoundFiles.PK_SoundFile  AS PK_SoundFile,
			SoundFiles.Name          AS Name,
			SoundFiles.Description   AS Description,
			SoundLanguages.PK_SoundLanguage AS FK_SoundLanguage,
			'$PK_SoundEntry'         AS FK_SoundEntry,     
			SoundLanguages.Name      AS Language
		FROM
			SoundLanguages
			LEFT JOIN SoundFiles ON PK_SoundLanguage = FK_SoundLanguage AND FK_SoundEntry = $PK_SoundEntry
	";

    $result = $mysqli->query($query) or die($mysqli->error);
    while ($row = $result->fetch_assoc()) {
        $SoundFiles[] = $row;
    }

    $smarty->assign('SoundFiles', $SoundFiles);
    return $smarty->fetch('SoundEntries_Modify.tpl');
}

admin_run('SoundEntries_Modify', 'Admin.tpl');
?>