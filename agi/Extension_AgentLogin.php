#!/usr/bin/php-cli -q
<?php
require(dirname(__FILE__) . '/../lib/phpagi/phpagi.php');
require(dirname(__FILE__) . '/../include/db_utils.inc.php');
require(dirname(__FILE__) . '/common/AGI_Logger.class.php');
require(dirname(__FILE__) . '/common/AGI_CDR.class.php');
require(dirname(__FILE__) . '/common/AGI_Utils.php');

$agi = new AGI();
$logger = new AGI_Logger($agi);
//$cdr    = new AGI_CDR($agi);
// Get Called Extension informations
$called_ext = $agi->request['agi_extension'];
$Extension_D = DB_Extension($called_ext);

// Get 'AgentLogin' Parameters
$AgentLogin = Database_Entry('Ext_AgentLogin', $Extension_D['PK_Extension']);
//
////CDR: Set called info
//$cdr->set_called(
//	"{$Extension_D['PK_Extension']}",                   // CalledID
//	"AgentLogin",                                       // CalledType
//	"Agent Login",                                      // CalledName
//	"{$Extension_D['Extension']}"                       // CalledNumber
//);
// Get the extension of the caller
$cid = $agi->parse_callerid();
$caller_ext = $cid['username'];

function read_agent_number() {
    global $agi;
    static $first_time = true;

    if ($first_time) {
        $result = $agi->get_data('agent-user', 9000, 100);
    } else {
        $result = $agi->get_data('agent-incorrect', 9000, 100);
    }
    $agent_number = $result['result'];

    $first_time = false;
    return $agent_number;
}

function read_agent_password() {
    global $agi;

    $result = $agi->get_data('agent-pass', 9000, 100);
    $agent_password = $result['result'];

    return $agent_password;
}

function agent_auth_ok($agent_number, $agent_password) {
    global $logger, $mysqli;

    // Find out the PK_Extension and Type for the agent_number
    $query = "SELECT PK_Extension, Type FROM Extensions WHERE Extension = '$agent_number' LIMIT 1";
    $result = $db->query($query) or $logger->error_sql("", $query, __LINE__, __FILE__);
    if ($mysqli->num_rows($result) == 0) {
        return false;
    }
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $PK_Extension = $row['PK_Extension'];
    $Type = $row['Type'];

    // Find out if the password matches for that extension
    switch ($Type) {
        case 'SipPhone':
            $query = "SELECT Password FROM Ext_SipPhones WHERE PK_Extension = '$PK_Extension' AND Password='$agent_password' LIMIT 1";
            break;
        case 'Virtual':
            $query = "SELECT Password FROM Ext_Virtual WHERE PK_Extension = '$PK_Extension' AND Password='$agent_password' LIMIT 1";
            break;
        default:
            $query = "SELECT * FROM Extensions WHERE 1=0";
            break;
    }

    $result = $db->query($query) or $logger->error_sql("", $query, __LINE__, __FILE__);
    if ($mysqli->num_rows($result) == 1) {
        return true;
    } else {
        return false;
    }
}

function agent_logout($agent_number) {
    global $agi, $mysqli;
    // Get previous Phone number of this agent
    $query = "
		SELECT
			`From`
		FROM
			Ext_Queue_Members_Status
			INNER JOIN Extensions ON PK_Extension = FK_Extension
		WHERE
			Extension = '$agent_number'
		LIMIT 1
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    if ($mysqli->numrows($result) == 1) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $agent_phone_number_old = $row['From'];
    }

    if ($agent_phone_number_old == "") {
        return false;
    }

    // Get a list with all the queues this member belongs to
    $query = "
		SELECT
			FK_Extension
		FROM
			Ext_Queue_Members
			INNER JOIN Extensions ON FK_Extension_Member = PK_Extension
		WHERE
			Extension = '$agent_number'
	";

    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    // Remove it from every queue
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $QueueName = "queue-{$row['FK_Extension']}";
        $Interface_Old = "Local/{$agent_phone_number_old}-{$row['FK_Extension']}@Extension_Queue_RingAgent/n";

        $agi->exec('RemoveQueueMember', "$QueueName,$Interface_Old");
    }

    // Delete from Active Table
    $query = "SELECT PK_Extension FROM Extensions WHERE Extension = '$agent_number' LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $PK_Extension = $row['PK_Extension'];

    $query = "DELETE FROM Ext_Queue_Members_Status WHERE FK_Extension = '$PK_Extension'";
    $db->query($query) or die(print_r($db->errorInfo(), true));

    return true;
}

function agent_login($agent_number, $agent_phone_number) {
    global $agi, $mysqli;

    // Get a list with all the queues this member belongs to
    $query = "
		SELECT
			FK_Extension
		FROM
			Ext_Queue_Members
			INNER JOIN Extensions ON FK_Extension_Member = PK_Extension
		WHERE
			Extension = '$agent_number'
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    // Add it to every queue
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $QueueName = "queue-{$row['FK_Extension']}";
        $Interface = "Local/$agent_phone_number-{$row['FK_Extension']}@Extension_Queue_RingAgent/n";

        $agi->exec('AddQueueMember', "$QueueName,$Interface");
    }

    // Add it to Active Table
    $query = "SELECT PK_Extension FROM Extensions WHERE Extension = '$agent_number' LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $PK_Extension = $row['PK_Extension'];

    $query = "INSERT INTO Ext_Queue_Members_Status (FK_Extension, `From`) VALUES($PK_Extension, '$agent_phone_number')";
    $db->query($query) or die(print_r($db->errorInfo(), true));
}

while (true) {
    // Get agent number if we shoud request it else use calling extension al agent number
    if ($AgentLogin['EnterExtension']) {
        $agent_number = read_agent_number();
    } else {
        $agent_number = $caller_ext;
    }

    // Request password for this agent
    if ($AgentLogin['RequirePassword']) {
        $agent_password = read_agent_password();
        if (agent_auth_ok($agent_number, $agent_password)) {
            $agi->verbose('---------------------------------------HERE');
            break;
        }
    } else {
        break;
    }
}

// Agent phone number = extension from this agentlogin was called
$agent_phone_number = $caller_ext;

if ($AgentLogin['LoginToggle']) {
    if (agent_logout($agent_number)) {
        $agi->stream_file('agent-loggedoff');
    } else {
        agent_login($agent_number, $agent_phone_number);
        $agi->stream_file('agent-loginok');
    }
} else {
    agent_logout($agent_number);
    agent_login($agent_number, $agent_phone_number);
    $agi->stream_file('agent-loginok');
}
?>
