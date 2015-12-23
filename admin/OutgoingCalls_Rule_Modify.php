<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function OutgoingCalls_Rule_Modify() {
    global $mysqli;
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    // Init form data (Rule, Errors)
    if (@$_REQUEST['submit'] == 'save') {
        $Rule = formdata_from_post();
        $Errors = formdata_validate($Rule);

        if (count($Errors) == 0) {
            if ($Rule['PK_OutgoingRule'] == '') {
                $id = formdata_save($Rule);
                header("Location: OutgoingCalls.php?msg=CREATE_RULE&hilight={$id}");
                die();
            } else {
                $id = formdata_save($Rule);
                header("Location: OutgoingCalls.php?msg=MODIFY_RULE&hilight={$id}");
                die();
            }
        }
    } else {
        if ($_REQUEST['PK_OutgoingRule'] != "") {
            $Rule = formdata_from_db($_REQUEST['PK_OutgoingRule']);
        } else {
            $Rule['Allow'] = '1';
            $Rule['Final'] = '1';
        }
    }

    // SipProviders
    $SipProviders = array();
    $query = "SELECT * FROM SipProviders ORDER BY Name";
    $result = $mysqli->query($query) or die($mysqli->error);
    while ($row = $result->fetch_assoc()) {
        $SipProviders[] = $row;
    }

    // IaxProviders
    //$IaxProviders = array();
    //$query = "SELECT * FROM IaxProviders ORDER BY Name";
    //$result = $mysqli->query($query) or die($mysqli->error);
    //while ($row = $result->fetch_assoc()) {
    //    $IaxProviders[] = $row;
    //}

    $smarty->assign('SipProviders', $SipProviders);
    //$smarty->assign('IaxProviders', $IaxProviders);
    $smarty->assign('Rule', $Rule);
    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('OutgoingCalls_Rule_Modify.tpl');
}

function formdata_from_db($id) {
    global $mysqli;
    $data = $_REQUEST;
    $query = "
		SELECT
			PK_OutgoingRule,
			Name,
			Final,
			BeginWith,
			RestBetweenLow,
			RestBetweenHigh,
			TrimFront,
			PrependDigits,
			ProviderType,
			ProviderID
		FROM
			OutgoingRules
		WHERE
			PK_OutgoingRule = $id
		LIMIT 1
	";
    $result = $mysqli->query($query) or die($mysqli->error);
    $data = $result->fetch_assoc();

    return $data;
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    global $mysqli;
    if ($data['PK_OutgoingRule'] == "") {
        $query = "SELECT MAX(RuleOrder) FROM OutgoingRules";
        $result = $mysqli->query($query) or die($mysqli->error);
        $row = $result->fetch_row();
        $RuleOrder = $row[0] + 1;

        $query = "INSERT INTO OutgoingRules(RuleOrder) VALUES($RuleOrder)";
        $mysqli->query($query) or die($mysqli->error . $query);

        $data['PK_OutgoingRule'] = $mysqli->insert_id;
    }

    $query = "
		UPDATE
			OutgoingRules
		SET
			Name            = '" . $mysqli->real_escape_string($data['Name']) . "',
			Final           =  " . ($data['Final'] ? '1' : '0') . ",
			BeginWith       = '" . $mysqli->real_escape_string($data['BeginWith']) . "',
			RestBetweenLow  = '" . intval($data['RestBetweenLow']) . "',
			RestBetweenHigh = '" . intval($data['RestBetweenHigh']) . "',
			PrependDigits   = '" . $mysqli->real_escape_string($data['PrependDigits']) . "',
			ProviderType    = '" . $mysqli->real_escape_string($data['ProviderType']) . "',
			TrimFront       = '" . $mysqli->real_escape_string($data['TrimFront']) . "',
			ProviderID      =  " . intval($data['ProviderID']["{$data['ProviderType']}"]) . "
		WHERE
			PK_OutgoingRule = " . $mysqli->real_escape_string($data['PK_OutgoingRule']) . "
		LIMIT 1
	";
    $mysqli->query($query) or die($mysqli->error . $query);

    return $data['PK_OutgoingRule'];
}

function formdata_validate($data) {
    $errors = array();
    if ($data['Name'] == "") {
        $errors['Name'] = true;
    }
    if (!preg_match('/^[0-9]+$/', $data['RestBetweenLow'])) {
        $errors['RestBetweenLow'] = true;
    }
    if (!preg_match('/^[0-9]+$/', $data['RestBetweenHigh'])) {
        $errors['RestBetweenHigh'] = true;
    }
    if (!preg_match('/^[0-9]{0,2}$/', $data['TrimFront'])) {
        $errors['TrimFront'] = true;
    }
    if (!preg_match('/^[+]{0,1}[*#0-9]{0,20}$/', $data['PrependDigits'])) {
        $errors['PrependDigits'] = true;
    }
    /* if (!preg_match('/^[*#(|)^0-9]{1,24}$/', $data['BeginWith'])) {
      $errors['BeginWith'] = true;
      } */
    return $errors;
}

admin_run('OutgoingCalls_Rule_Modify', 'Admin.tpl');
?>
