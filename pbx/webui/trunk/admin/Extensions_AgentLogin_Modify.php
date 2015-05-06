<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function Extensions_AgentLogin_Modify() {
    
    $session = &$_SESSION['Extensions_AgentLogin_Modify'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init message (Message)
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Init form data (AgentLogin)
    if (@$_REQUEST['submit'] == 'save') {
        $AgentLogin = formdata_from_post();
        $Errors = formdata_validate($AgentLogin);

        if (count($Errors) == 0) {
            $id = formdata_save($AgentLogin);
            header("Location: Extensions_List.php?msg=MODIFY_AGENTLOGIN_EXTENSION&hilight={$id}");
            die();
        }
    } elseif (@$_REQUEST['PK_Extension'] != "") {
        $AgentLogin = formdata_from_db($_REQUEST['PK_Extension']);
    } else {
        
    }

    $smarty->assign('AgentLogin', $AgentLogin);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('Extensions_AgentLogin_Modify.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    // Init data from 'Ext_AgentLogin'
    $query = "
		SELECT
			Ext_AgentLogin.PK_Extension AS PK_Extension,
			Extension,
			RequirePassword,
			LoginToggle,
			EnterExtension
		FROM
			Extensions
			INNER JOIN Ext_AgentLogin ON Ext_AgentLogin.PK_Extension = Extensions.PK_Extension
		WHERE
			Extensions.PK_Extension = $id
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    $data = $result->fetch_assoc();
    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    global $mysqli;
    if ($data['PK_Extension'] == "") {
        $query = "INSERT INTO Extensions(Extension,Type) VALUES('" . $mysqli->real_escape_string($data['Extension']) . "', 'AgentLogin')";
        $mysqli->query($query) or die($mysqli->error() . $query);
        $data['PK_Extension'] = $mysqli->insert_id;

        $query = "INSERT INTO Ext_AgentLogin(PK_Extension) VALUES('" . $mysqli->real_escape_string($data['PK_Extension']) . "')";
        $mysqli->query($query) or die($mysqli->error() . $query);
    }

    // Update 'Ext_AgentLogin'
    $query = "
		UPDATE
			Ext_AgentLogin
		SET
			RequirePassword = " . ($data['RequirePassword'] == 0 ? '0' : '1') . ",
			LoginToggle     = " . ($data['LoginToggle'] ? '1' : '0') . ",
			EnterExtension  = " . ($data['EnterExtension'] ? '1' : '0') . "
		WHERE
			PK_Extension = " . $mysqli->real_escape_string($data['PK_Extension']) . "
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error() . $query);

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
            $result = $mysqli->query($query) or die($mysqli->error() . $query);
            if ($result->num_rows > 0) {
                $errors['Extension']['Duplicate'] = true;
            }
        }
    }

    return $errors;
}

admin_run('Extensions_AgentLogin_Modify', 'Admin.tpl');
?>