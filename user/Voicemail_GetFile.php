<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/user_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/voicemail_utils.inc.php');

function Voicemail_GetFile() {
    $Folder = $_REQUEST['Folder'];
    $File = $_REQUEST['File'];

    vm_getfile($_SESSION['_USER']['Extension'], $Folder, $File);

    return "ERROR";
}

user_run('Voicemail_GetFile', 'User.tpl');
?>