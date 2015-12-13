<?php

define('SOUND_FILES_FOLDER', '/var/lib/asterisk/sounds/custom/');

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function SoundFiles_Modify() {
    global $mysqli;
    
    $session = &$_SESSION['SoundFiles_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    if (@$_REQUEST['submit'] == 'save') {
        $SoundFile = formdata_from_post();
        $Errors = formdata_validate($SoundFile);

        if (count($Errors) == 0) {
            $id = formdata_save($SoundFile);
            header("Location: SoundEntries_List.php?msg=MODIFY_ENTRY&hilight={$SoundFile['FK_SoundEntry']}");
            die();
        }
    } elseif ($_REQUEST['PK_SoundFile'] != "") {
        $SoundFile = formdata_from_db($_REQUEST['PK_SoundFile']);
    }

    $query = "SELECT PK_SoundFolder, Name FROM SoundFolders ORDER BY Name";
    $result = $mysqli->query($query) or die($mysqli->error);
    while ($row = $result->fetch_assoc()) {
        $SoundFolders[] = $row;
    }

    $query = "SELECT PK_SoundLanguage, Name FROM SoundLanguages ORDER BY Name";
    $result = $mysqli->query($query) or die($mysqli->error);
    while ($row = $result->fetch_assoc()) {
        $SoundLanguages[] = $row;
    }

    if ($_REQUEST['PK_SoundEntry'] != "") {
        $PK_SoundEntry = $_REQUEST['PK_SoundEntry'];
    } elseif ($SoundFile['PK_SoundFile'] != "") {
        $PK_SoundEntry = $SoundFile['FK_SoundEntry'];
    }

    if ($_REQUEST['PK_SoundLanguage'] != "") {
        $PK_SoundLanguage = $_REQUEST['PK_SoundLanguage'];
    } elseif ($SoundFile['PK_SoundFile'] != "") {
        $PK_SoundLanguage = $SoundFile['FK_SoundLanguage'];
    }

    if ($PK_SoundEntry != "") {
        $query = "SELECT FK_SoundFolder FROM SoundEntries WHERE PK_SoundEntry = $PK_SoundEntry LIMIT 1";
        $result = $mysqli->query($query) or die($mysqli->error . $query);
        $row = $result->fetch_array();

        $PK_SoundFolder = $row[0];
    }


    $smarty->assign('SoundFile', $SoundFile);
    $smarty->assign('SoundFolders', $SoundFolders);
    $smarty->assign('SoundLanguages', $SoundLanguages);
    $smarty->assign('PK_SoundEntry', $PK_SoundEntry);
    $smarty->assign('PK_SoundLanguage', $PK_SoundLanguage);
    $smarty->assign('PK_SoundFolder', $PK_SoundFolder);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('SoundFiles_Modify.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    $query = "SELECT * FROM SoundFiles WHERE PK_SoundFile = $id LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error);
    $data = $result->fetch_assoc();

    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    global $mysqli;
    // Insert 'SoundEntry'
    if (empty($data['FK_SoundEntry'])) {
        $query = "
			INSERT INTO
				SoundEntries
			SET
				FK_SoundFolder = " . $mysqli->real_escape_string($data['FK_SoundFolder']) . ",
				Type           = 'User'
		";
        $mysqli->query($query) or die($mysqli->error . $query);

        $data['FK_SoundEntry'] = $mysqli->insert_id;
    }

    // Insert 'SoundFiles'
    if (empty($data['PK_SoundFile'])) {
        $query = "INSERT INTO SoundFiles() VALUES()";
        $mysqli->query($query) or die($mysqli->error . $query);

        $data['PK_SoundFile'] = $mysqli->insert_id;
    }

    // Move uploaded file
    if ($data['Source'] == 'file' && !empty($_FILES['File']['name'])) {
        copy($_FILES['File']['tmp_name'], SOUND_FILES_FOLDER . "/telesoft-web-{$data['PK_SoundFile']}.wav")
                or die("Cannot Move : {$_FILES['File']['tmp_name']} to " . SOUND_FILES_FOLDER);
        $data['Filename'] = SOUND_FILES_FOLDER . "/telesoft-web-{$data['PK_SoundFile']}.wav";
    }

    if ($data['Source'] == 'phone' && $data['RecordedFile'] != "") {
        copy($data['RecordedFile'], SOUND_FILES_FOLDER . "/telesoft-web-{$data['PK_SoundFile']}.wav")
                or die("Cannot Copy : {$data['RecordedFile']} to " . SOUND_FILES_FOLDER);
        $data['Filename'] = SOUND_FILES_FOLDER . "/telesoft-web-{$data['PK_SoundFile']}.wav";
    }

    // Update 'SoundFiles'
    $query = "
		UPDATE
			SoundFiles
		SET
			Name             = '" . $mysqli->real_escape_string($data['Name']) . "',
			Description      = '" . $mysqli->real_escape_string($data['Description']) . "',
			FK_SoundLanguage = " . $mysqli->real_escape_string($data['FK_SoundLanguage']) . ",
			" . (!empty($data['Filename']) ? "
			Filename         = '" . $mysqli->real_escape_string($data['Filename']) . "',
			" : '') . "
			FK_SoundEntry    =  " . $mysqli->real_escape_string($data['FK_SoundEntry']) . "
		WHERE
			PK_SoundFile   = " . $mysqli->real_escape_string($data['PK_SoundFile']) . "
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);

    return $data['PK_SoundFile'];
}

function formdata_validate($data) {
    $errors = array();

    // If new file and no file uploaded or recorded
    if (empty($data['PK_SoundFile'])) {
        if (empty($_FILES['File']['name'])) {
            if (!file_exists($data['RecordedFile'])) {
                $errors['File']['missing'] = true;
            }
        }
    }

    // Name
    if (!preg_match('/^[^ ]{1}.{1,29}$/', $data['Name'])) {
        $errors['Name'] = true;
    }
    return $errors;
}

admin_run('SoundFiles_Modify', 'Admin.tpl');
?>
