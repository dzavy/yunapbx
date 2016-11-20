<?php

include_once(dirname(__FILE__) . '/../lib/smarty/Smarty.class.php');

function smarty_init($templates_folder) {
    $smarty = new Smarty;

    $smarty->template_dir = $templates_folder;
    $smarty->config_dir = $templates_folder . '/config/';
    $smarty->compile_dir = '/tmp/templates_c/';
    $smarty->cache_dir = $templates_folder . '/cache/';
    $smarty->_dir_perms = 0777;

    return $smarty;
}

?>
