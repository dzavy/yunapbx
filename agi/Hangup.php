#!/usr/bin/php-cli -q
<?php
set_time_limit(300);
error_reporting(E_ALL);

require(dirname(__FILE__) . '/../lib/phpagi/phpagi.php');
require(dirname(__FILE__) . '/../include/db_utils.inc.php');
require(dirname(__FILE__) . '/common/AGI_Logger.class.php');
require(dirname(__FILE__) . '/common/AGI_CDR.class.php');

$agi = new AGI();
$logger = new AGI_Logger($agi);
$cdr = new AGI_CDR($agi);

//CDR : Flush existing entry if both called and caller are known
if ($cdr->caller_known() && $cdr->called_known()) {
    $cdr->push_hangup();
    $cdr->flush_cdr();
}
?>