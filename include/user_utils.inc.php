<?php

include_once(dirname(__FILE__) . '/smarty_utils.inc.php');

session_start();

function user_run($generator_function, $template = "", $need_auth = true) {
    if ($need_auth) {
        if (empty($_SESSION['_USER'])) {
            header('Location: Login.php');
            die();
            return;
        }
    }

    $Output = $generator_function();

    if ($template != "") {
        $smarty = smarty_init(dirname(__FILE__) . '/../user/templates');
        $smarty->assign('Output', $Output);
        $smarty->assign('User', $_SESSION['_USER']);
        echo $smarty->fetch($template);
    } else {
        echo $Output;
    }
}

function user_login($extension) {
    global $mysqli;
    $query = "SELECT * FROM Extensions WHERE Extension = " . $mysqli->real_escape_string($extension) . " LIMIT 1";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $user = $result->fetch_assoc();

    $_SESSION['_USER'] = $user;
}

function user_logout() {
    unset($_SESSION['_USER']);
}
