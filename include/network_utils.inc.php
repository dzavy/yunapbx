<?php

function get_network_interfaces() {
    $data = array();
    
    exec('sudo uci -q get network.lan.proto', $data['Network_Protocol']);
    exec('sudo uci -q get network.lan.ipaddr', $data['Network_Address']);
    exec('sudo uci -q get network.lan.netmask', $data['Network_Mask']);
    exec('sudo uci -q get network.lan.gateway', $data['Network_Gateway']);
    exec('sudo uci -q get network.lan.dns', $data['Network_DNS']);
    
    foreach ($data as $field => $value) {
        $data[$field] = implode("\n", $value);
    }
    $data['Network_DNS'] = explode(" ", $data['Network_DNS']);

    return $data;
}

function set_network_interfaces($data) {
    switch($data['Network_Protocol']) {
        case "dhcp":
            exec('sudo uci set network.lan.proto=dhcp');
            exec('sudo uci -q delete network.lan.ipaddr');
            exec('sudo uci -q delete network.lan.netmask');
            exec('sudo uci -q delete network.lan.gateway');
            exec('sudo uci -q delete network.lan.dns');
            exec('sudo uci commit');
          
            break;
        case "static":
            exec('sudo uci set network.lan.proto=static');
            exec('sudo uci set network.lan.ipaddr=' . $data['Network_Address']);
            exec('sudo uci set network.lan.netmask=' . $data['Network_Mask']);
            exec('sudo uci set network.lan.gateway=' . $data['Network_Gateway']);
            exec('sudo uci set network.lan.dns="' . implode(" ", $data['Network_DNS']) . '"');
            exec('sudo uci commit');
            
            break;
    }
}
