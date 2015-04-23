<?php
include_once(dirname(__FILE__).'/../lib/smarty/Smarty.class.php');

function smarty_init($templates_folder) {
	$smarty = new Smarty;

	$smarty->template_dir = $templates_folder;
	$smarty->config_dir   = $templates_folder.'/config/';
	$smarty->compile_dir  = $templates_folder.'/compile/';
	$smarty->cache_dir    = $templates_folder.'/cache/';

	return $smarty;
}
?>
