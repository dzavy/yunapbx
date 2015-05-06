<?php

function get_network_interfaces() {
    $output = shell_exec(dirname(__FILE__) . '/../cli/get_network_settings.sh 2<&1');

    $output = explode("\n", $output);
    foreach ($output as $line) {
        $line = explode(',', $line);
        if (count($line) != 4) {
            continue;
        }

        $data[$line[0]] = array(
            'protocol' => "{$line[3]}",
            'ip' => "{$line[1]}",
            'netmask' => "{$line[2]}"
        );
    }

    return $data;
}

function set_network_interfaces($data) {
    
}
