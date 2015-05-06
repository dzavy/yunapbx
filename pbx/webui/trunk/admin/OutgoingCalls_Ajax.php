<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function OutgoingCalls_Ajax() {
    global $mysqli;
    
    $session = &$_SESSION['OutgoingCallsAjax'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $data = $_POST;
    $response = array();

    switch ($data['Action']) {
        case 'DeleteRule':
            $query = "DELETE FROM OutgoingRules WHERE PK_OutgoingRule = " . intval($data['ID']) . " LIMIT 1";
            $mysqli->query($query) or die($mysqli->error());

            $query = "DELETE FROM Extension_Rules WHERE FK_OutgoingRule = " . intval($data['ID']) . "";
            $mysqli->query($query) or die($mysqli->error());

            $query = "DELETE FROM Template_Rules WHERE FK_OutgoingRule = " . intval($data['ID']) . "";
            $mysqli->query($query) or die($mysqli->error());

            $query = "DELETE FROM OutgoingCIDRules WHERE FK_OutgoingRule = " . intval($data['ID']) . " ";
            $mysqli->query($query) or die($mysqli->error());

            $response['ID'] = $data['ID'];
            break;

        case 'DeleteCIDRule':
            $query = "DELETE FROM OutgoingCIDRules WHERE PK_OutgoingCIDRule = " . intval($data['ID']) . " LIMIT 1";
            $mysqli->query($query) or die($mysqli->error());

            $response['ID'] = $data['ID'];
            break;

        case 'UpdateRuleOrder':
            $order = 1;
            foreach ($_REQUEST['Rules'] as $PK_Rule) {
                $PK_Rule = explode('_', $PK_Rule);
                $PK_Rule = $PK_Rule[1];

                $response['test'] .= "$order-$PK_Rule , ";
                $query = "UPDATE OutgoingRules SET RuleOrder = $order WHERE PK_OutgoingRule = $PK_Rule LIMIT 1";
                $mysqli->query($query) or die($mysqli->error());

                $order++;
            }
            break;

        case 'UpdateCIDRule':
            if ($data['ExtensionStart'] == "") {
                $errors['ExtensionStart']['Invalid'] = true;
            } elseif (intval($data['ExtensionStart']) . "" != $data['ExtensionStart']) {
                $errors['ExtensionStart']['Invalid'] = true;
            } elseif (strlen($data['ExtensionStart']) < 3 || strlen($data['ExtensionStart']) > 5) {
                $errors['ExtensionStart']['Invalid'] = true;
            }

            if ($data['Type'] == 'Multiple') {
                if ($data['ExtensionEnd'] == "") {
                    $errors['ExtensionEnd']['Invalid'] = true;
                } elseif (intval($data['ExtensionEnd']) . "" != $data['ExtensionEnd']) {
                    $errors['ExtensionEnd']['Invalid'] = true;
                } elseif (strlen($data['ExtensionEnd']) < 3 || strlen($data['ExtensionEnd']) > 5) {
                    $errors['ExtensionEnd']['Invalid'] = true;
                }
            } else {
                $data['ExtensionEnd'] = 0;
            }

            if (!preg_match('/^[0-9]?$/', $data['FK_OutgoingRule'])) {
                $data['FK_OutgoingRule'] = 0;
            }

            if (!preg_match('/^[0-9]?$/', $data['Add'])) {
                $data['Add'] = 0;
            }

            $query = "
				UPDATE
					OutgoingCIDRules
				SET
					Type            = '" . $mysqli->real_escape_string($data['Type']) . "',
					ExtensionStart  = '" . $mysqli->real_escape_string($data['ExtensionStart']) . "',
					ExtensionEnd    = '" . $mysqli->real_escape_string($data['ExtensionEnd']) . "',
					FK_OutgoingRule = '" . $mysqli->real_escape_string($data['FK_OutgoingRule']) . "',
					`Add`           =  " . intval($data['Add']) . ",
					PrependDigits   = '" . $mysqli->real_escape_string($data['PrependDigits']) . "',
					Name            = '" . $mysqli->real_escape_string($data['Name']) . "',
					Number          = '" . $mysqli->real_escape_string($data['Number']) . "'
				WHERE
					PK_OutgoingCIDRule = " . intval($data['ID']) . "
				LIMIT 1
			";
            $mysqli->query($query) or die($mysqli->error() . $query);
            $response['ID'] = $data['ID'];
            $response['Errors'] = $errors;
            break;
    }

    echo json_encode($response);
}

admin_run('OutgoingCalls_Ajax');
?>