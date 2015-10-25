<?php

function get_network_interfaces() {
    $data = array();
    
    exec('uci -q get network.lan.proto', $data['Network_Protocol']);
    exec('uci -q get network.lan.ipaddr', $data['Network_Address']);
    exec('uci -q get network.lan.netmask', $data['Network_Mask']);
    exec('uci -q get network.lan.gateway', $data['Network_Gateway']);
    exec('uci -q get network.lan.dns', $data['Network_DNS']);
    
    foreach ($data as $field => $value) {
        $data[$field] = implode("\n", $value);
    }
    $data['Network_DNS'] = explode(" ", $data['Network_DNS']);

    return $data;
}

function set_network_interfaces($data) {
    switch($data['Network_Protocol']) {
        case "dhcp":
            exec('uci set network.lan.proto=dhcp');
            exec('uci -q delete network.lan.ipaddr');
            exec('uci -q delete network.lan.netmask');
            exec('uci -q delete network.lan.gateway');
            exec('uci -q delete network.lan.dns');
            exec('uci commit');
          
            break;
        case "static":
            exec('uci set network.lan.proto=static');
            exec('uci set network.lan.ipaddr=' . $data['Network_Address']);
            exec('uci set network.lan.netmask=' . $data['Network_Mask']);
            exec('uci set network.lan.gateway=' . $data['Network_Gateway']);
            exec('uci set network.lan.dns="' . implode(" ", $data['Network_DNS']) . '"');
            exec('uci commit');
            
            break;
    }
}
