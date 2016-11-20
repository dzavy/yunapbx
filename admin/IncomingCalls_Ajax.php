<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . "/../include/asterisk_utils.inc.php");

function IncomingCalls_Ajax() {
    global $mysqli;
    
    $session = &$_SESSION['IncomingCallsAjax'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $data = $_POST;
    $response = array();

    switch ($data['Action']) {
        case 'DeleteRule':
            $query = "DELETE FROM IncomingRules WHERE PK_IncomingRule = " . intval($data['ID']) . " LIMIT 1";
            $mysqli->query($query) or die($mysqli->error);
            $response['ID'] = $data['ID'];
            break;
        case 'SaveRule':
            // Validation
            if ($data['Digits'] == "") {
                $errors['Digits']['Invalid'] = true;
            } elseif (intval($data['Digits']) . "" != $data['Digits']) {
                $errors['Digits']['Invalid'] = true;
            } elseif (strlen($data['Digits']) > 20) {
                $errors['Digits']['Invalid'] = true;
            }

            if ($data['BlockType'] == 'undefined') {
                if ($data['Extension'] == "") {
                    $errors['Extension']['Invalid'] = true;
                } elseif (intval($data['Extension']) . "" != $data['Extension']) {
                    $errors['Extension']['Invalid'] = true;
                } elseif (strlen($data['Extension']) < 3 || strlen($data['Extension']) > 5) {
                    $errors['Extension']['Invalid'] = true;
                }
            }
            // Update
            if (count($errors) == 0) {
                $query = "
					UPDATE
						IncomingRules
					SET
						Subject         = '" . $mysqli->real_escape_string($data['Subject']) . "',
						Digits          = '" . $mysqli->real_escape_string($data['Digits']) . "',
						Extension       = '" . $mysqli->real_escape_string($data['Extension']) . "',
						" . (
                        $data['BlockType'] != 'undefined' ?
                                "BlockType = '" . $mysqli->real_escape_string($data['BlockType']) . "'," :
                                ''
                        ) . "
							FK_Timeframe    =  " . intval($data['FK_Timeframe']) . "
					WHERE
						PK_IncomingRule =  " . intval($data['ID']) . "
					LIMIT 1
				";

                $mysqli->query($query) or die($mysqli->error);
            }

            $response['ID'] = $data['ID'];
            $response['Errors'] = $errors;
            break;
        case 'UpdateRuleOrder':
            $order = 1;
            foreach ($_REQUEST['Rules'] as $PK_Rule) {
                $PK_Rule = explode('_', $PK_Rule);
                $PK_Rule = $PK_Rule[1];

                $response['test'] .= "$order-$PK_Rule , ";
                $query = "UPDATE IncomingRules SET RuleOrder = $order WHERE PK_IncomingRule = $PK_Rule LIMIT 1";
                $mysqli->query($query) or die($mysqli->error);

                $order++;
            }
            break;
        case 'DeleteRoute':
            $query = "DELETE FROM IncomingRoutes WHERE PK_IncomingRoute = " . intval($data['ID']) . " LIMIT 1";
            $mysqli->query($query) or die($mysqli->error);
            $response['ID'] = $data['ID'];
            break;
        case 'SaveRoute':
            // Validation
            $errors = array();

            $aux = explode('~', $data['Provider'], 2);
            $data['ProviderType'] = $aux[0];
            $data['ProviderID'] = $aux[1];

            if ($data['StartNumber'] == "") {
                $errors['StartNumber']['Invalid'] = true;
            } elseif (!preg_match('/^[#*]{0,1}[0-9]{1,31}$/', $data['StartNumber'])) {
                $errors['StartNumber']['Invalid'] = true;
            }

            if ($data['RouteType'] == 'multiple') {
                if ($data['EndNumber'] == "") {
                    $errors['EndNumber']['Invalid'] = true;
                } elseif (!preg_match('/^[#*]{0,1}[0-9]{1,31}$/', $data['EndNumber'])) {
                    $errors['EndNumber']['Invalid'] = true;
                }

                if (!preg_match('/^[0-9]+$/', $data['TrimFront'])) {
                    $errors['TrimFront']['Invalid'] = true;
                }

                if (!preg_match('/^[0-9]+$/', $data['Add'])) {
                    $errors['Add']['Invalid'] = true;
                }
            }

            // Update
            if (count($errors) == 0) {
                $query = "
					UPDATE
						IncomingRoutes
					SET
						" . (
                        $data['EndNumber'] != 'undefined' ?
                                "EndNumber   = '" . $mysqli->real_escape_string($data['EndNumber']) . "'," :
                                ''
                        ) . "
						" . (
                        $data['TrimFront'] != 'undefined' ?
                                "TrimFront   = " . intval($data['TrimFront']) . "," :
                                ''
                        ) . "
						" . (
                        $data['Add'] != 'undefined' ?
                                "`Add`         = " . intval($data['Add']) . "," :
                                ''
                        ) . "
						" . (
                        $data['Extension'] != 'undefined' ?
                                "Extension   = '" . $mysqli->real_escape_string($data['Extension']) . "'," :
                                ''
                        ) . "
						StartNumber  = '" . $mysqli->real_escape_string($data['StartNumber']) . "',
						ProviderType = '" . $mysqli->real_escape_string($data['ProviderType']) . "',
						ProviderID   = '" . $mysqli->real_escape_string($data['ProviderID']) . "'
					WHERE
						PK_IncomingRoute =  " . intval($data['ID']) . "
					LIMIT 1
				";

                $mysqli->query($query); # or die($mysqli->error);
            }
            $response['ID'] = $data['ID'];
            $response['Errors'] = $errors;
            break;
    }

    asterisk_UpdateConf('extensions.conf');
    asterisk_Reload();

    echo json_encode($response);
}

admin_run('IncomingCalls_Ajax');
?>