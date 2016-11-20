<?php
include_once(dirname(__FILE__) . "/../lib/AsteriskManager.class.php");

function asterisk_UpdateConf($file) {
    global $conf;
    global $mysqli;
    
    require(dirname(__FILE__) . "/../generator/$file.php");
}

function asterisk_Cmd($cmd) {
    global $conf;
    
    $manager = new AsteriskManager();
    $manager->Connect($conf['astman']['user'], $conf['astman']['password']);

    return $manager->Run($cmd);
}

function asterisk_Reload() {
    global $conf;

    $manager = new AsteriskManager();
    $manager->Connect($conf['astman']['user'], $conf['astman']['password']);

    $message = new AsteriskManagerMessage();
    $message->SetKey('Action', 'Reload');

    $manager->Send($message);
}

function asterisk_RecordSound($Extension, $Message, $TmpFile) {
    global $conf;

    $manager = new AsteriskManager();
    $manager->Connect($conf['astman']['user'], $conf['astman']['password']);

    $message = new AsteriskManagerMessage();
    $message->SetKey('Action', 'Originate');
    $message->SetKey('Channel', 'SIP/' . $Extension);
    $message->SetKey('Exten', 's');
    $message->SetKey('Priority', '1');
    $message->SetKey('Context', 'record_web_sound');
    $message->SetVar('MESSAGE', $Message);
    $message->SetVar('FILE', $TmpFile);

    $manager->Send($message);
}

function asterisk_PlaySound($Extension, $File) {
    global $conf;

    $manager = new AsteriskManager();
    $manager->Connect($conf['astman']['user'], $conf['astman']['password']);

    $message = new AsteriskManagerMessage();
    $message->SetKey('Action', 'Originate');
    $message->SetKey('Channel', 'SIP/' . $Extension);
    $message->SetKey('Exten', 's');
    $message->SetKey('Priority', '1');
    $message->SetKey('Context', 'play_sound');
    $message->SetVar('FILE', $File);

    $manager->Send($message);
}

?>
