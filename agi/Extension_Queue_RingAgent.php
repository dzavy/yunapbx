#!/usr/bin/php-cli -q
<?php
require(dirname(__FILE__) . '/../lib/phpagi/phpagi.php');
require(dirname(__FILE__) . '/../include/db_utils.inc.php');
require(dirname(__FILE__) . '/common/AGI_Logger.class.php');
require(dirname(__FILE__) . '/common/AGI_CDR.class.php');
require(dirname(__FILE__) . '/common/AGI_Utils.php');

$agi = new AGI();
$logger = new AGI_Logger($agi);
$cdr = new AGI_CDR($agi);

// Find out caller number
$cid = $agi->parse_callerid();
$caller_ext = $cid['username'];

// Find out agent extension and queue id
$ext = $agi->request['agi_extension'];
$ext = explode('-', $ext);
$agent_exten = $ext[0];
$queue_id = $ext[1];

// Get 'Extension' table info for the agent
$query = "SELECT PK_Extension, Extension, Type FROM Extensions WHERE Extension = '{$agent_exten}' LIMIT 1";
$result = $db->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
$Extension_Agent = $result->fetch(PDO::FETCH_ASSOC);

// Get 'Extension' table info for the queue
$query = "SELECT PK_Extension, Extension, Type FROM Extensions WHERE PK_Extension = '{$queue_id}' LIMIT 1";
$result = $db->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
$Extension_Queue = $result->fetch(PDO::FETCH_ASSOC);

// See if agent requires login
$query = "SELECT * FROM Ext_Queue_Members WHERE FK_Extension_Member = '{$Extension_Agent['PK_Extension']}' AND FK_Extension = '{$Extension_Queue['PK_Extension']}' LIMIT 1";
$result = $db->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
$row = $result->fetch(PDO::FETCH_ASSOC);
$agent_login = $row[0];

if ($agent_login) {
    $query = "SELECT * FROM Ext_Queue_Members_Status WHERE FK_Extension = {$Extension_Agent['PK_Extension']} LIMIT 1";
    $result = $db->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    $agent_exten = $row['From'];
}

$agi->exec('Dial', array("SIP/{$agent_exten}", 10));
?>