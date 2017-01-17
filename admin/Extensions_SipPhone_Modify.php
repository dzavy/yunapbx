<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function Extensions_SipPhone_Modify() {
    global $mysqli;
    
    $session = &$_SESSION['Extensions_Sip_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Init Available DTMF Modes (DTMFModes)
    $query = "SELECT PK_DTMFMode, Name, Description FROM DTMFModes";
    $result = $mysqli->query($query) or die($mysqli->errno());
    $DTMFModes = array();
    while ($row = $result->fetch_assoc()) {
        $DTMFModes[] = $row;
    }

    // Init available codecs (Codecs)
    $query = "SELECT PK_Codec, Name, Description, Recomended FROM Codecs";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $Codecs = array();
    while ($row = $result->fetch_assoc()) {
        $Codecs[] = $row;
    }

    // Init available nat types (NATTypes)
    $query = "SELECT PK_NATType, Name, Description FROM NATTypes";
    $result = $mysqli->query($query) or die($mysqli->errno());
    $NATTypes = array();
    while ($row = $result->fetch_assoc()) {
        $NATTypes[] = $row;
    }

    // Init available extension groups (Groups)
    $query = "SELECT PK_Group, Name FROM Groups";
    $result = $mysqli->query($query) or die($mysqli->errno());
    $Groups = array();
    while ($row = $result->fetch_assoc()) {
        $Groups[] = $row;
    }

    // Init available outgoing rules (Rules)
    $query = "SELECT * FROM OutgoingRules ORDER BY Name";
    $result = $mysqli->query($query) or die($mysqli->errno());
    $Rules = array();
    while ($row = $result->fetch_assoc()) {
        $Rules[] = $row;
    }

    // Init available extension groups (Features)
    $query = "SELECT PK_Feature, Name FROM Features";
    $result = $mysqli->query($query) or die($mysqli->errno());
    $Features = array();
    while ($row = $result->fetch_assoc()) {
        $Features[] = $row;
    }

    // Init form data (Extension)
    if (@$_REQUEST['submit'] == 'save') {
        $SipPhone = formdata_from_post();
        $Errors = formdata_validate($SipPhone);

        if (count($Errors) == 0) {
            $id = formdata_save($SipPhone);
            asterisk_UpdateConf('sip.conf');
            asterisk_UpdateConf('pjsip.conf');
            asterisk_UpdateConf('voicemail.conf');
            asterisk_UpdateConf('extensions.conf');
            asterisk_Reload();
            header("Location: Extensions_List.php?msg=MODIFY_SIPPHONE_EXTENSION&hilight={$id}");
            die();
        }
    } elseif (@$_REQUEST['PK_Extension'] != "") {
        $SipPhone = formdata_from_db($_REQUEST['PK_Extension']);
    } else {
        $SipPhone = formdata_from_template($_REQUEST['FK_Template']);
    }

    $smarty->assign('SipPhone', $SipPhone);
    $smarty->assign('DTMFModes', $DTMFModes);
    $smarty->assign('Codecs', $Codecs);
    $smarty->assign('Features', $Features);
    $smarty->assign('NATTypes', $NATTypes);
    $smarty->assign('Groups', $Groups);
    $smarty->assign('Rules', $Rules);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('Extensions_SipPhone_Modify.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    // Init data from 'Extensions'
    $query = "
		SELECT
			Ext_SipPhones.PK_Extension,
			Extension,
			Name,
			Name_Editable,
			Password,
			Password_Editable,
			Email,
			Email_Editable,
			FK_NATType,
			FK_DTMFMode,
			IVRDial
		FROM
			Ext_SipPhones
			INNER JOIN Extensions ON Extensions.PK_Extension=Ext_SipPhones.PK_Extension
		WHERE
			Ext_SipPhones.PK_Extension = $id
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data = $result->fetch_assoc();

    // Init data from 'Extension_Codecs'
    $query = "
		SELECT
			FK_Codec
		FROM
			Ext_SipPhones_Codecs
		WHERE
			FK_Extension = {$data['PK_Extension']}
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data['Codecs'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Codecs'][] = $row['FK_Codec'];
    }

    // Init data from 'Extension_Groups'
    $query = "
		SELECT
			FK_Group
		FROM
			Extension_Groups
		WHERE
			FK_Extension = {$data['PK_Extension']}
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data['Groups'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Groups'][] = $row['FK_Group'];
    }

    // Init data from 'Extension_Features'
    $query = "
		SELECT
			FK_Feature
		FROM
			Ext_SipPhones_Features
		WHERE
			FK_Extension = {$data['PK_Extension']}
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data['Features'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Features'][] = $row['FK_Feature'];
    }

    // Init outgoing rules
    $query = "
		SELECT
			FK_OutgoingRule
		FROM
			Extension_Rules
		WHERE
			FK_Extension = {$data['PK_Extension']}
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data['Rules'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Rules'][] = $row['FK_OutgoingRule'];
    }

    return $data;
}

function formdata_from_template($id) {
    global $mysqli;
    $query = "
		SELECT
			PK_Template,
			Name_Editable,
			Password_Editable,
			Email_Editable,
			FK_NATType,
			FK_DTMFMode,
			IVRDial
		FROM
			Templates
		WHERE
			PK_Template = $id
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $data = $result->fetch_assoc();

    $query = "
		SELECT
			FK_Codec
		FROM
			Template_Codecs
		WHERE
			FK_Template = $id
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $data['Codecs'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Codecs'][] = $row['FK_Codec'];
    }

    $query = "
		SELECT
			FK_Group
		FROM
			Template_Groups
		WHERE
			FK_Template = $id
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $data['Groups'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Groups'][] = $row['FK_Group'];
    }

    $query = "
		SELECT
			FK_Feature
		FROM
			Template_Features
		WHERE
			FK_Template = $id
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $data['Features'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Features'][] = $row['FK_Feature'];
    }

    $query = "
		SELECT
			FK_OutgoingRule
		FROM
			Template_Rules
		WHERE
			FK_Template = $id
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $data['Rules'] = array();
    while ($row = $result->fetch_assoc()) {
        $data['Rules'][] = $row['FK_OutgoingRule'];
    }

    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    global $mysqli;
    if ($data['PK_Extension'] == "") {
        $query = "INSERT INTO Extensions(Type, Extension) VALUES('SipPhone','" . $mysqli->real_escape_string($data['Extension']) . "')";
        $mysqli->query($query) or die($mysqli->error . $query);
        $data['PK_Extension'] = $mysqli->insert_id;

        $query = "INSERT INTO Ext_SipPhones(PK_Extension) VALUES({$data['PK_Extension']})";
        $mysqli->query($query) or die($mysqli->error . $query);
    }

    $query = "UPDATE Extensions SET Name = '". $mysqli->real_escape_string($data['Name']) . "' WHERE PK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']);
    $mysqli->query($query) or die($mysqli->error . $query);

    // Update 'Extensions'
    $query = "
		UPDATE
			Ext_SipPhones
		SET
			Name_Editable  = " . ($data['Name_Editable'] ? '1' : '0') . ",
			Password_Editable  = " . ($data['Password_Editable'] ? '1' : '0') . ",
			Email              = '" . $mysqli->real_escape_string($data['Email']) . "',
			Email_Editable     = " . ($data['Email_Editable'] ? '1' : '0') . ",
			FK_NATType         = " . $mysqli->real_escape_string($data['FK_NATType']) . ",
			FK_DTMFMode        = " . $mysqli->real_escape_string($data['FK_DTMFMode']) . "
		WHERE
			PK_Extension       = " . $mysqli->real_escape_string($data['PK_Extension']) . "
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);

    // Update Password if requested and set PhonePassword if it was never set
    if ($data['Password'] != '') {
        $query = "
			UPDATE
				Ext_SipPhones
			SET
				Password     = '" . $mysqli->real_escape_string($data['Password']) . "'
			WHERE
				PK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . "
			LIMIT 1
		";
        $mysqli->query($query) or die($mysqli->error . $query);

        $query = "
			UPDATE
				Ext_SipPhones
			SET
				PhonePassword = '" . $mysqli->real_escape_string($data['Password']) . "'
			WHERE
				PK_Extension  = " . $mysqli->real_escape_string($data['PK_Extension']) . "
				AND
				PhonePassword = ''
			LIMIT 1
		";
        $mysqli->query($query) or die($mysqli->error . $query);
    }

    // Update PhonePassword if requested
    if ($data['PhonePassword']) {
        $query = "
			UPDATE
				Ext_SipPhones
			SET
				PhonePassword = '" . $mysqli->real_escape_string($data['PhonePassword']) . "'
			WHERE
				PK_Extension  = " . $mysqli->real_escape_string($data['PK_Extension']) . "
			LIMIT 1
		";
        $mysqli->query($query) or die($mysqli->error . $query);
    }

    // Update 'Ext_SipPhones_Codecs'
    $query = "DELETE FROM Ext_SipPhones_Codecs WHERE FK_Extension = {$data['PK_Extension']} ";
    $mysqli->query($query) or die($mysqli->error . $query);
    if (is_array($data['Codecs'])) {
        foreach ($data['Codecs'] as $FK_Codec) {
            $query = "INSERT INTO Ext_SipPhones_Codecs (FK_Extension, FK_Codec) VALUES ({$data['PK_Extension']}, $FK_Codec)";
            $mysqli->query($query) or die($mysqli->error . $query);
        }
    }

    // Update 'Extension_Groups'
    $query = "DELETE FROM Extension_Groups WHERE FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $mysqli->query($query) or die($mysqli->error . $query);
    if (is_array($data['Groups'])) {
        foreach ($data['Groups'] as $FK_Group) {
            $query = "INSERT INTO Extension_Groups (FK_Extension, FK_Group) VALUES ({$data['PK_Extension']}, $FK_Group)";
            $mysqli->query($query) or die($mysqli->error . $query);
        }
    }

    // Update 'Ext_SipPhones_Features'
    $query = "DELETE FROM Ext_SipPhones_Features WHERE FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $mysqli->query($query) or die($mysqli->error . $query);

    if (is_array($data['Features'])) {
        foreach ($data['Features'] as $FK_Feature) {
            $query = "INSERT INTO Ext_SipPhones_Features (FK_Extension, FK_Feature) VALUES ({$data['PK_Extension']}, $FK_Feature)";
            $mysqli->query($query) or die($mysqli->error . $query);
        }
    }

    // Update 'Extension_Rules'
    $query = "DELETE FROM Extension_Rules WHERE FK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . " ";
    $mysqli->query($query) or die($mysqli->error . $query);

    if (is_array($data['Rules'])) {
        foreach ($data['Rules'] as $FK_OutgoingRule => $Status) {
            if ($Status == 0) {
                continue;
            }
            $query = "INSERT INTO Extension_Rules (FK_Extension, FK_OutgoingRule) VALUES ({$data['PK_Extension']}, {$FK_OutgoingRule})";
            $mysqli->query($query) or die($mysqli->error . $query);
        }
    }

    // Update 'IVRDial"
    $query = "UPDATE Extensions SET IVRDial = " . ($data['IVRDial'] == 1 ? '1' : '0') . " WHERE PK_Extension = {$data['PK_Extension']}";
    $mysqli->query($query) or die($mysqli->error . $query);

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
        } elseif (strlen($data['Extension']) < 3 || strlen($data['Extension']) > 5) {
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

    // Check if password is empty
    if ($data['Password'] == "") {
        if ($create_new) {
            $errors['Password']['Invalid'] = true;
        }
        // Check if password is numeric
    } elseif (intval($data['Password']) . "" != $data['Password']) {
        $errors['Password']['Invalid'] = true;
        // Check if password is proper lenght
    } elseif (strlen($data['Password']) < 3 || strlen($data['Password']) > 10) {
        $errors['Password']['Invalid'] = true;
        // Check if passwords match it's retype
    } elseif ($data['Password'] != $data['Password_Retype']) {
        $errors['Password']['Match'] = true;
    }

    // Check if first name is proper length
    if ((strlen($data['Name']) < 1) || (strlen($data['Name']) > 32)) {
        $errors['Name']['Invalid'] = true;
    }

    // Check if the phone passwords match
    if ($data['PhonePassword'] != $data['PhonePassword_Retype']) {
        $errors['PhonePassword']['Match'] = true;
    }

    return $errors;
}

admin_run('Extensions_SipPhone_Modify', 'Admin.tpl');
?>
