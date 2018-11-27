<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_Action_Modify() {
    $db = DB::getInstance();

    $session = &$_SESSION['IVR_Action_Modify_play_sound'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    if (@$_REQUEST['submit'] == 'save') {
        $Action = formdata_from_post();
        $Errors = formdata_validate($Action);

        if (count($Errors) == 0) {
            $id = formdata_save($Action);
            header("Location: IVR_Actions.php?PK_Menu={$Action['FK_Menu']}&hilight={$id}");
            die();
        }
    } elseif (@$_REQUEST['PK_Action'] != "") {
        $Action = formdata_from_db($_REQUEST['PK_Action']);
    } else {
        $Action = formdata_from_default();
    }

    // Init Folders (SoundFolders)
    $SoundFolders = array();
    $query = "
		SELECT
			PK_SoundFolder,
			Name
		FROM
			SoundFolders
		ORDER BY Name
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $SoundFolders[] = $row;
    }

    // Init SoundLanguages
    $SoundLanguages = array();
    $query = "
		SELECT
			PK_SoundLanguage,
			`Default`,
			Name
		FROM
			SoundLanguages
		ORDER BY Name
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $SoundLanguages[] = $row;
        if ($row['Default']) {
            $SoundLanguage_Default = $row;
        }
    }

    // Init SoundEntries
    $SoundEntries = array();
    foreach ($SoundLanguages as $SoundLanguage) {
        $lid = $SoundLanguage['PK_SoundLanguage'];
        $query_select .= "
			SoundFiles_{$lid}.Name        AS Name_{$lid}       ,
			SoundFiles_{$lid}.Description AS Description_{$lid},
		";
        $query_from .= "
			LEFT JOIN SoundFiles SoundFiles_{$lid} ON SoundFiles_{$lid}.FK_SoundEntry = PK_SoundEntry AND SoundFiles_{$lid}.FK_SoundLanguage = {$lid}
		";
    }
    $query = "
		SELECT
			$query_select
			PK_SoundEntry,
			FK_SoundFolder
		FROM
			SoundEntries
			$query_from
		ORDER BY
			PK_SoundEntry
	";

    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $SoundEntry = $row;

        foreach ($SoundLanguages as $SoundLanguage) {
            $lid = $SoundLanguage['PK_SoundLanguage'];

            $SoundEntry['Name'][$lid] = $SoundEntry["Name_$lid"];
            $SoundEntry['Description'][$lid] = $SoundEntry["Description_$lid"];

            if ($SoundEntry["Name_$lid"] != "") {
                $SoundEntry['Name']['Default'] = "{$SoundEntry['Name'][$SoundLanguage['PK_SoundLanguage']]}";
            }
        }

        if ($SoundEntry['Name'][$SoundLanguage_Default['PK_SoundLanguage']] != "") {
            $SoundEntry['Name']['Default'] = "{$SoundEntry['Name'][$SoundLanguage_Default['PK_SoundLanguage']]}";
        }

        $SoundEntries[] = $SoundEntry;
    }


    $smarty->assign('Action', $Action);
    $smarty->assign('SoundEntries', $SoundEntries);
    $smarty->assign('SoundFolders', $SoundFolders);
    $smarty->assign('SoundLanguages', $SoundLanguages);
    $smarty->assign('SoundLanugage_Default', $SoundLanguage_Default);

    return $smarty->fetch('IVR_Actions_Modify.play_sound.tpl');
}

function formdata_from_db($id) {
    $db = DB::getInstance();
    
    $query = "SELECT * FROM IVR_Actions WHERE PK_Action = '$id' LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $data = $result->fetch(PDO::FETCH_ASSOC);

    $query = "SELECT * FROM IVR_Action_Params WHERE FK_Action = '$id'";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $data['Param'][$row['Name']] = $row['Value'];
    }

    return $data;
}

function formdata_from_default() {
    $data = array();

    $data['FK_Menu'] = $_REQUEST['FK_Menu'];

    return $data;
}

function formdata_from_post() {
    return $_REQUEST;
}

function formdata_save($data) {
    $db = DB::getInstance();
    
    if ($data['PK_Action'] == "") {
        $query = "SELECT COUNT(*) FROM IVR_Actions WHERE FK_Menu={$data['FK_Menu']}";
        $result = $db->query($query) or die(print_r($db->errorInfo(), true));
        $row = $result->fetch_row();
        $data['Order'] = $row[0] + 1;

        $query = "INSERT INTO IVR_Actions (FK_Menu, `Order`, Type) VALUES({$data['FK_Menu']}, {$data['Order']}, 'play_sound')";
        $db->query($query) or die(print_r($db->errorInfo(), true));
        $data['PK_Action'] = $db->lastInsertId();
    }

    $query = "DELETE FROM IVR_Action_Params WHERE FK_Action = {$data['PK_Action']}";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    if (is_array($data['Param'])) {
        foreach ($data['Param'] as $Name => $Value) {
            $query = "
				INSERT INTO
					IVR_Action_Params
				SET
					`Name`      = '" . $mysqli->real_escape_string($Name) . "',
					`Value`     = '" . $mysqli->real_escape_string($Value) . "',
					`FK_Action` = {$data['PK_Action']}
			";
            $db->query($query) or die(print_r($db->errorInfo(), true));
        }
    }

    return $data['PK_Action'];
}

function formdata_validate($data) {
    $errors = array();

    return $errors;
}

admin_run('Extensions_Action_Modify', 'Admin.tpl');
?>