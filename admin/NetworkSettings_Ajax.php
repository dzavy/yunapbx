<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function NetworkSettings_Ajax() {
    
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $data = $_REQUEST;
    $response = array();

    switch ($data['Action']) {
        case 'LookupExternalIP':
            $url = "http://ipinfo.io/ip";
            $response['IP'] = get_web_page($url);
            break;
    }

    echo json_encode($response);
}

admin_run('NetworkSettings_Ajax');
