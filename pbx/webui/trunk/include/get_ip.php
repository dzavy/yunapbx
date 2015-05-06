<?php

function get_web_page($url) {
    $options = array('http' => array(
            'user_agent' => 'nelu', // who am i
            'max_redirects' => 10, // stop after 10 redirects
            'timeout' => 120, // timeout on response
    ));
    $context = stream_context_create($options);
    $page = @file_get_contents($url, false, $context);

    if ($page != false) {
        return $page;
    } else {
        return null;    // Bad url, timeout  
    }
}

?> 