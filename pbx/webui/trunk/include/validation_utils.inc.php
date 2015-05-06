<?php

function validateIpAddress($ip_addr) {
    //first of all the format of the ip address is matched
    if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $ip_addr)) {
        //now all the integer values are separated
        $parts = explode(".", $ip_addr);
        //now we need to check each part can range from 0-255
        foreach ($parts as $ip_parts) {
            if (intval($ip_parts) > 255 || intval($ip_parts) < 0) {
                return false; //if number is not within range of 0-255
            }
        }
        return true;
    } else {
        return false; //if format of ip address doesn't matches
    }
}

function validateHostName($url) {
    if (validateIpAddress($url)) {
        return true;
    } else if (preg_match('|^[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url)) {
        return true;
    } else {
        return false;
    }
}

?>