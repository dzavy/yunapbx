<?php
include (dirname(__FILE__).'/config.inc.php');

$mysqli = new mysqli($conf['database']['host'], $conf['database']['user'], $conf['database']['password'], $conf['database']['database']);
	//$mysqli->select_db($conf['database']['database']);
?>
