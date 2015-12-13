<?php
class AGI_Logger {
	private $agi;
	private $debug = true;

	function AGI_Logger($agi) {
		$this->agi = $agi;
	}

	function error($message, $file='', $line='') {
		$prefix = "ERROR [$file,$line] : ";
		$this->agi->verbose("{$prefix}{$message}", 1);

		die();
	}

	function warning($message, $file='', $line='') {
		$prefix = "WARNING [$file,$line] : ";
		$this->agi->verbose("{$prefix}{$message}", 2);
	}

	function debug($message, $file='', $line='') {
		$prefix = "DEBUG [$file,$line] : ";
		$this->agi->verbose("{$prefix}{$message}", 3);
	}

	function info($message, $file='', $line='') {
		$prefix = "INFO [$file,$line] : ";
		$this->agi->verbose("{$prefix}{$message}", 4);
	}


	function error_sql($message, $query, $file='', $line='') {
		$prefix = "ERROR [$file,$line] :";

		$this->agi->verbose("{$prefix}{$message}", 1);
		$this->agi->verbose("{$prefix}{$query}", 1);
		$this->agi->verbose("{$prefix}".$mysqli->error, 1);

		die();
	}
}
?>