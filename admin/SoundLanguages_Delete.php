<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function SoundLanguages_Delete() {
    $db = DB::getInstance();
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $PK_SoundLanguage = $_REQUEST['PK_SoundLanguage'];
    if ($PK_SoundLanguage == "") {
        $PK_SoundLanguage = $_REQUEST['PK'];
    }

    // In confirmed, do the actual delete
    if (@$_REQUEST['submit'] == 'delete_confirm') {
        $query = "DELETE FROM SoundLanguages WHERE PK_SoundLanguage = $PK_SoundLanguage LIMIT 1";
        $db->query($query) or die(print_r($db->errorInfo(), true));

        header('Location: SoundLanguages_List.php?msg=DELETE_LANGUAGE');
        die();
    }

    // Init extension info (Extension)
    $query = "SELECT PK_SoundLanguage, Name FROM SoundLanguages WHERE PK_SoundLanguage = $PK_SoundLanguage LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $SoundLanguage = $result->fetch(PDO::FETCH_ASSOC);

    $smarty->assign('SoundLanguage', $SoundLanguage);

    return $smarty->fetch('SoundLanguages_Delete.tpl');
}

admin_run('SoundLanguages_Delete', 'Admin.tpl');
?>