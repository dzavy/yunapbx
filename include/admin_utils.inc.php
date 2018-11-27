<?php

session_start();

include_once(dirname(__FILE__) . '/smarty_utils.inc.php');

function admin_run($generator_function, $template = "", $need_auth = true) {
    if ($need_auth) {
        if (empty($_SESSION['_ADMIN'])) {
            header('Location: Login.php');
            die();
            return;
        }
    }
    $Output = $generator_function();

    if ($template != "") {
        $smarty = smarty_init(dirname(__FILE__) . '/../admin/templates');
        $smarty->assign('Output', $Output);
        echo $smarty->fetch($template);
    } else {
        echo $Output;
    }
}

function pbx_var_get($name) {
    $db = DB::getInstance();
    $query = "SELECT Value FROM	Settings WHERE Name = '" . $mysqli->real_escape_string($name) . "'";

    $result = $db->query($query) or die(__LINE__ . __FILE__);
    $row = $result->fetch_object();
    return $row->Value;
}

function pbx_var_set($name, $value) {
    $db = DB::getInstance();
    $query = "DELETE FROM Settings WHERE Name='" . $mysqli->real_escape_string($name) . "' LIMIT 1";
    $db->query($query) or die(__LINE__ . __FILE__);

    $query = "INSERT INTO Settings (Name,Value) VALUES ('" . $mysqli->real_escape_string($name) . "','" . $mysqli->real_escape_string($value) . "')";
    $db->query($query) or die(__LINE__ . __FILE__);
}

function get_web_page($url) {
    $options = array('http' => array(
            'user_agent' => 'nelu', // who am i
            'max_redirects' => 10, // stop after 10 redirects
            'timeout' => 120, // timeout on response
        ));
    $context = stream_context_create($options);
    $page = file_get_contents($url, false, $context);

    if ($page != false) {
        return $page;
    } else if (!isset($http_response_header)) {
        return null;    // Bad url, timeout   
    }
}
