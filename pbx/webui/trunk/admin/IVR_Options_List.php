<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function IVR_Options_List() {
    global $mysqli;
    
    $session = &$_SESSION['IVR_Options_List'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_Menu = $_REQUEST['PK_Menu'];
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    if (!empty($_REQUEST['submit'])) {
        $IVR_Menu = formdata_from_post();
        $Errors = formdata_validate($IVR_Menu);

        if (count($Errors) == 0) {
            $id = formdata_save($IVR_Menu);
        }
    } else {
        $IVR_Menu = formdata_from_db($_REQUEST['PK_Menu']);
    }

    // Init available options
    $IVR_Options = array();
    $query = "
		SELECT
			PK_Option         AS `PK_Option`,
			`Key`             AS `Key`,
			IVR_Menus.Name    AS `Menu`,
			IVR_Actions.Type  AS `Action_Type`,
			IVR_Actions.Order AS `Action_Order`
		FROM
			IVR_Options
			LEFT JOIN IVR_Menus   ON FK_Menu_Entry = PK_Menu
			LEFT JOIN IVR_Actions ON FK_Action_Entry = PK_Action
		WHERE
			IVR_Options.FK_Menu = $PK_Menu
		ORDER BY
			`Key`
	";
    $result = $mysqli->query($query) or die($mysqli->error());
    while ($row = $result->fetch_assoc()) {
        $IVR_Options[] = $row;
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
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    while ($row = $result->fetch_assoc()) {
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
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    while ($row = $result->fetch_assoc()) {
        $SoundLanguages[] = $row;
        if ($row['Default']) {
            $SoundLanguage_Default = $row;
        }
    }

    // Init SoundEntries
    $SoundEntries = array();
    $query_select = "";
    $query_from = "";
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

    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    while ($row = $result->fetch_assoc()) {
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

    // Get available menus
    $Menus = array();
    $query = "SELECT PK_Menu, Name FROM IVR_Menus ORDER BY Name";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    while ($row = $result->fetch_assoc()) {
        $menu = $row;

        $query2 = "SELECT * FROM IVR_Actions WHERE FK_Menu = '{$menu['PK_Menu']}' ORDER BY `Order`";
        $result2 = $mysqli->query($query2) or die($mysqli->error() . $query2);
        while ($row2 = $result2->fetch_assoc()) {
            $action = $row2;

            $query3 = "SELECT * FROM IVR_Action_Params WHERE FK_Action = {$action['PK_Action']}";
            $result3 = $mysqli->query($query3) or die($mysqli->error() . $query3);
            while ($row3 = $result3->fetch_assoc()) {
                $action['Param'][$row3['Name']] = $row3['Value'];
                $action['Var'][$row3['Name']] = $row3['Variable'];
            }

            $menu['Actions'][] = $action;
        }

        $Menus[] = $menu;
    }

    $smarty->assign('IVR_Menu', $IVR_Menu);
    $smarty->assign('IVR_Options', $IVR_Options);
    $smarty->assign('PK_Menu', $PK_Menu);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));
    $smarty->assign('History', $_SESSION['IVR_HISTORY']);
    $smarty->assign('SoundEntries', $SoundEntries);
    $smarty->assign('SoundFolders', $SoundFolders);
    $smarty->assign('SoundLanguages', $SoundLanguages);
    $smarty->assign('SoundLanugage_Default', $SoundLanguage_Default);
    $smarty->assign('Menus', $Menus);

    return $smarty->fetch('IVR_Options_List.tpl');
}

function formdata_from_post() {
    $data = $_REQUEST;

    empty($data['ExtensionDialing']) ? $data['ExtensionDialing'] = 0 : null;

    return $data;
}

function formdata_save($data) {
    global $mysqli;
    // Update 'Ext_IVR'
    $query = "
		UPDATE
			IVR_Menus
		SET
			ExtensionDialing = " . $mysqli->real_escape_string($data['ExtensionDialing']) . ",
			
			FK_SoundLanguage_Invalid = " . intval($data['FK_SoundLanguage_Invalid']) . ",
			FK_SoundEntry_Invalid    = " . intval($data['FK_SoundEntry_Invalid']) . ",
			FK_Menu_Invalid          = " . intval($data['FK_Menu_Invalid']) . ",
			FK_Action_Invalid        = " . intval($data['FK_Action_Invalid']) . ",
			
			Timeout                  = " . intval($data['Timeout']) . ",
			FK_SoundLanguage_Timeout = " . intval($data['FK_SoundLanguage_Timeout']) . ",
			FK_SoundEntry_Timeout    = " . intval($data['FK_SoundEntry_Timeout']) . ",
			FK_Menu_Timeout          = " . intval($data['FK_Menu_Timeout']) . ",
			FK_Action_Timeout        = " . intval($data['FK_Action_Timeout']) . ",
			
			Retry                    = " . intval($data['Retry']) . ",
			FK_SoundLanguage_Retry   = " . intval($data['FK_SoundLanguage_Retry']) . ",
			FK_SoundEntry_Retry      = " . intval($data['FK_SoundEntry_Retry']) . ",
			FK_Menu_Retry            = " . intval($data['FK_Menu_Retry']) . ",
			FK_Action_Retry          = " . intval($data['FK_Action_Retry']) . "

		WHERE
			PK_Menu                  = " . $mysqli->real_escape_string($data['PK_Menu']) . "
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error() . $query);
}

function formdata_validate($data) {
    $errors = array();

    return $errors;
}

function formdata_from_db($id) {
    global $mysqli;
    $query = "
		SELECT
			*
		FROM
			IVR_Menus
		WHERE
			PK_Menu = '$id'
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    $data = $result->fetch_assoc();
    return $data;
}

admin_run('IVR_Options_List', 'Admin.tpl');
?>