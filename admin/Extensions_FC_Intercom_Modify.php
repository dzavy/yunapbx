<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_FC_Intercom_Modify() {
    global $mysqli;
    
    $session = &$_SESSION['Extensions_FC_Intercom_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    //myprint($_REQUEST);

    if ($_REQUEST['PK_Extension']) {
        $pk_ext = $_REQUEST['PK_Extension'];
        //Preexisted Members
        $query = " SELECT 
						FK_Ext_Member, ConnectionID, FK_Ext_Group 
					FROM 
						FC_Intercom_Members
					WHERE
						FK_Extension = '" . $mysqli->escape_string($pk_ext) . "' ORDER BY FK_Ext_Member, FK_Ext_Group";
        $result = $mysqli->query($query) or die($mysqli->error . $query);
        $Members = array();
        while ($row = $result->fetch_assoc()) {
            $Members[] = $row;
        }

        //Preexisted Admins
        $query = " SELECT 
						FK_Ext_Admin, ConnectionID, FK_Ext_Group 
					FROM 
						FC_Intercom_Admins
					WHERE
						FK_Extension = '" . $mysqli->escape_string($pk_ext) . "' ORDER BY FK_Ext_Admin, FK_Ext_Group";
        $result = $mysqli->query($query) or die($mysqli->error . $query);
        $Admins = array();
        while ($row = $result->fetch_assoc()) {
            $Admins[] = $row;
        }

        //Preexisted ConnectionIDs
        $query = " SELECT DISTINCT 
						ConnectionID
					FROM 
						FC_Intercom_Admins
					ORDER BY ConnectionID";
        $result = $mysqli->query($query) or die($mysqli->error . $query);
        $IDs = array();
        while ($row = $result->fetch_assoc()) {
            $IDs[] = $row['ConnectionID'];
        }
    }

    $Rows = @data_rows($Admins, $Members, $IDs);

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Groups
    $query = "SELECT PK_Group, Name FROM Groups";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $Groups = array();
    while ($row = $result->fetch_assoc()) {
        $Groups[] = $row;
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
    $result = $mysqli->query($query) or die($mysqli->error . $query);
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
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
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

    $result = $mysqli->query($query) or die($mysqli->error . $query);
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

    // Init available Sounds (SoundsFiles)
    $query = "
		SELECT
			PK_SoundFile,
			SoundFiles.Name,
			SoundFiles.Description,
			SoundLanguages.Name AS Language
		FROM
			SoundFiles
			INNER JOIN SoundEntries   ON FK_SoundEntry    = PK_SoundEntry
			INNER JOIN SoundFolders   ON FK_SoundFolder   = PK_SoundFolder
			INNER JOIN SoundLanguages ON FK_SoundLanguage = PK_SoundLanguage
		WHERE
			SoundFolders.Name = 'Call Queues'
		ORDER BY
			SoundLanguages.Name ASC,
			SoundFiles.Name     ASC
	";
    $result = $mysqli->query($query) or die($mysqli->error);
    $SoundFiles = array();
    while ($row = $result->fetch_assoc()) {
        $SoundFiles[$row['Language']][$row['PK_SoundFile']] = $row['Name'];
    }

    // Init Available Accounts (Accounts)
    $query = "
		SELECT
			Extensions.PK_Extension,
			Extension,
			CONCAT(IFNULL(Ext_SipPhones.FirstName,''),IFNULL(Ext_Virtual.FirstName,'')) AS FirstName,
			CONCAT(IFNULL(Ext_SipPhones.LastName ,''),IFNULL(Ext_Virtual.LastName,''))  AS LastName
		FROM
			Extensions
			LEFT JOIN Ext_SipPhones ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			LEFT JOIN Ext_Virtual   ON Ext_Virtual.PK_Extension   = Extensions.PK_Extension
		WHERE
			Extensions.Type IN ('Virtual', 'SipPhone')
		ORDER BY Extension
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $Accounts = array();
    while ($row = $result->fetch_assoc()) {
        $Accounts[] = $row;
    }

    // Init form data (Intercom)
    if (@$_REQUEST['submit'] == 'save') {
        $Intercom = formdata_from_post();
        $Errors = formdata_validate($Intercom);

        if (count($Errors) == 0) {
            if ($Intercom['PK_Extension'] != '') {
                $msg = 'MODIFY_FC_INTERCOM_EXTENSION';
            } else {
                $msg = 'ADD_FC_INTERCOM_EXTENSION';
            }

            $id = formdata_save($Intercom);
            header("Location: Extensions_List.php?msg={$msg}&hilight={$id}");
            die();
        }
    } elseif (@$_REQUEST['PK_Extension'] != "") {
        $Intercom = formdata_from_db($_REQUEST['PK_Extension']);
    } else {
        $Intercom = array();
        $Intercom['Header'] = 'Intercom';
        $Intercom['Timeout'] = 120;
        $Intercom['TwoWay'] = 1;
        $Intercom['PlaySound'] = 1;
    }

    $smarty->assign('Groups', $Groups);
    $smarty->assign('Accounts', $Accounts);
    $smarty->assign('Rows', $Rows);

    $smarty->assign('FC_Intercom', $Intercom);
    $smarty->assign('Errors', $Errors);


    $smarty->assign('SoundEntries', $SoundEntries);
    $smarty->assign('SoundFolders', $SoundFolders);
    $smarty->assign('SoundLanguages', $SoundLanguages);
    $smarty->assign('SoundLanugage_Default', $SoundLanguage_Default);

    return $smarty->fetch('Extensions_FC_Intercom_Modify.tpl');
}

function data_rows($master, $slave, $IDs) {
    foreach ($IDs as $id) {
        for ($i = 0; $i < count($master); $i++) {
            if ($master[$i]['ConnectionID'] == $id) {
                if ($master[$i]['FK_Ext_Admin'] != 0) {
                    $data[$id]['master']['pk_ext'][] = $master[$i]['FK_Ext_Admin'];
                } else {
                    $data[$id]['master']['pk_group'][] = $master[$i]['FK_Ext_Group'];
                }
            }
        }
        for ($i = 0; $i < count($slave); $i++) {
            if ($slave[$i]['ConnectionID'] == $id) {
                if ($slave[$i]['FK_Ext_Member'] != 0) {
                    $data[$id]['slave']['pk_ext'][] = $slave[$i]['FK_Ext_Member'];
                } else {
                    $data[$id]['slave']['pk_group'][] = $slave[$i]['FK_Ext_Group'];
                }
            }
        }
    }
    return $data;
}

function formdata_from_db($id) {
    global $mysqli;
    $query = "
		SELECT
			*
		FROM
			FC_Intercom
			INNER JOIN Extensions ON Extensions.PK_Extension = FC_Intercom.FK_Extension
		WHERE
			FC_Intercom.FK_Extension = '$id'
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data = $result->fetch_assoc();

    return $data;
}

function formdata_from_default() {
    $data = array();
    return $data;
}

function formdata_from_post() {
    return $_REQUEST;
}

function formdata_save($data) {
    global $mysqli;
    if ($data['PK_Extension'] == "") {
        $query = "INSERT INTO Extensions(Feature, Type, Extension) VALUES(1, 'FC_Intercom', '" . $mysqli->real_escape_string($data['Extension']) . "')";
        $mysqli->query($query) or die($mysqli->error . $query);
        $data['PK_Extension'] = $mysqli->insert_id;

        $query = "INSERT INTO FC_Intercom(FK_Extension) VALUES('" . $mysqli->real_escape_string($data['PK_Extension']) . "')";
        $mysqli->query($query) or die($mysqli->error . $query);
    }

    $query = "
		UPDATE
			FC_Intercom
		SET
			Header             = '" . $mysqli->real_escape_string($data['Header']) . "',
			Timeout            = '" . $mysqli->real_escape_string($data['Timeout']) . "',
			TwoWay             = '" . ($data['TwoWay'] == '1' ? '1' : '0') . "',
			PlaySound          = '" . ($data['PlaySound'] == '0' ? '0' : '1') . "',
			FK_Folder          = '" . $mysqli->real_escape_string($data['SoundFolders']) . "',
			FK_Lang            = '" . $mysqli->real_escape_string($data['SoundLanguages']) . "',
			FK_Sound           = '" . $mysqli->real_escape_string($data['SoundEntries']) . "'					
		WHERE
			FK_Extension       = " . $mysqli->real_escape_string($data['PK_Extension']) . "
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);


    // Update 'FC_Intercom_Admins'
    $query = "DELETE FROM 
					FC_Intercom_Admins 
		      WHERE 
					FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $mysqli->query($query) or die($mysqli->error);

    foreach (array_keys($data['Admin']) as $connectionId) {
        $ExtOrGroup = array_keys($data['Admin'][$connectionId]);
        foreach ($data['Admin'][$connectionId] as $pks) {
            foreach ($pks as $pk_ext) {
                if ($ExtOrGroup[0] == "Extension") {
                    $FK_Ext_Admin = $pk_ext;
                    $FK_Ext_Group = 0;
                } else if ($ExtOrGroup[0] == "Group") {
                    $FK_Ext_Admin = 0;
                    $FK_Ext_Group = $pk_ext;
                } else
                    die("Error Extension/Group category.");

                $query = "
					INSERT INTO 
						FC_Intercom_Admins (FK_Extension, ConnectionID, FK_Ext_Admin, FK_Ext_Group) 
					VALUES 
						({$data['PK_Extension']}, {$connectionId}, {$FK_Ext_Admin}, {$FK_Ext_Group} )";
                $mysqli->query($query) or die($mysqli->error . $query);
            }
        }
    }


    // Update 'FC_Intercom_Members'
    $query = " DELETE FROM 
					FC_Intercom_Members 
			   WHERE 
					FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $mysqli->query($query) or die($mysqli->error);

    foreach (array_keys($data['Member']) as $connectionId) {
        $ExtOrGroup = array_keys($data['Member'][$connectionId]);
        foreach ($data['Member'][$connectionId] as $pks) {
            foreach ($pks as $pk_ext) {
                if ($ExtOrGroup[0] == "Extension") {
                    $FK_Ext_Member = $pk_ext;
                    $FK_Ext_Group = 0;
                } else {
                    $FK_Ext_Member = 0;
                    $FK_Ext_Group = $pk_ext;
                }
                $query = "
					INSERT INTO 
						FC_Intercom_Members (FK_Extension, ConnectionID, FK_Ext_Member, FK_Ext_Group) 
					VALUES 
						({$data['PK_Extension']}, {$connectionId}, {$FK_Ext_Member}, {$FK_Ext_Group} )";
                $mysqli->query($query) or die($mysqli->error . $query);
            }
        }
    }
    return $data['PK_Extension'];
}

function formdata_validate($data) {
    global $mysqli;
    $errors = array();

    if ($data['PK_Extension'] == '') {
        $create_new = true;
    }

    if ($create_new) {
        // Check if extension is empty
        if ($data['Extension'] == "") {
            $errors['Extension']['Invalid'] = true;
            // Check if Extension is numeric
        } elseif (intval($data['Extension']) . "" != $data['Extension']) {
            $errors['Extension']['Invalid'] = true;
            // Check if extension is proper length
        } elseif (strlen($data['Extension']) < 1 || strlen($data['Extension']) > 2) {
            $errors['Extension']['Invalid'] = true;
            // Check if extension in unique
        } else {
            $query = "SELECT Extension FROM Extensions WHERE Extension = '{$data['Extension']}' LIMIT 1";
            $result = $mysqli->query($query) or die($mysqli->error . $query);
            if ($result->num_rows > 0) {
                $errors['Extension']['Duplicate'] = true;
            }
        }
    }
    return $errors;
}

admin_run('Extensions_FC_Intercom_Modify', 'Admin.tpl');
