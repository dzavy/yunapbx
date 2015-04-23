<?php
	include (dirname(__FILE__).'/config.inc.php');

	mysql_connect($conf['database']['host'], $conf['database']['user'], $conf['database']['password']);
	mysql_select_db($conf['database']['database']);
?>
