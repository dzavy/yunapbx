#!/usr/bin/php -q
<?php
require(dirname(__FILE__).'/../lib/phpagi/phpagi.php');
require(dirname(__FILE__).'/../include/db_utils.inc.php');
require(dirname(__FILE__).'/common/AGI_Logger.class.php');
require(dirname(__FILE__).'/common/AGI_CDR.class.php');
require(dirname(__FILE__).'/common/AGI_Utils.php');

$agi    = new AGI();
$logger = new AGI_Logger($agi);
$cdr    = new AGI_CDR($agi);

// Get Called Extension informations
$called_ext   = $agi->request['agi_extension'];
$Extension_D  = DB_Extension($called_ext);

// Get Called Queue information
$Queue = Database_Entry('Ext_Queues', $Extension_D['PK_Extension']);

//CDR: Set called info
$cdr->set_called(
	"{$Queue['PK_Extension']}",                         // CalledID
	"Queue",                                            // CalledType
	"{$Queue['Name']}",                                 // CalledName
	"{$Extension_D['Extension']}"                       // CalledNumber
);

// Set values to pass to [Extension_Queue_RingQueue]
$QUEUE_ARGS = "queue-{$Queue['PK_Extension']}";
if ($Queue['PlayMohInQueue']==0) {
	$QUEUE_ARGS .= "|r||||";
} else {
	$QUEUE_ARGS .= "|||||";
}

// Maybe we can pass our cdr id to the agents called throught this url
//$QUEUE_ARGS .= "|http://www.google.com";


$QUEUES_NOAGENTS_EXTEN = "{$Queue['JoinEmptyExtension']}";
$QUEUE_MAXCYCLES_EXTEN = "{$Queue['CyclesExtension']}";
$QUEUE_MAXLEN_EXTEN    = "{$Queue['MaxLenExtension']}";
$QUEUE_TIMEOUT_EXTEN   = "{$Queue['TimeoutExtension']}";
$QUEUE_OPER_EXTEN      = "{$Queue['OperatorExtension']}";

$agi->set_variable('QUEUE_ARGS'           , $QUEUE_ARGS);
$agi->set_variable('QUEUE_NOAGENTS_EXTEN' , $QUEUES_NOAGENTS_EXTEN);
$agi->set_variable('QUEUE_MAXCYCLES_EXTEN', $QUEUE_MAXCYCLES_EXTEN);
$agi->set_variable('QUEUE_MAXLEN_EXTEN'   , $QUEUE_MAXLEN_EXTEN);
$agi->set_variable('QUEUE_TIMEOUT_EXTEN'  , $QUEUE_TIMEOUT_EXTEN);
$agi->set_variable('QUEUE_OPER_EXTEN'     , $QUEUE_OPER_EXTEN);

$agi->exec_goto('Extension_Queue_RingQueue', $called_ext);
?>
