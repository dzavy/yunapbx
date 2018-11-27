<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/moh_utils.inc.php');

function MOH_Files_Download() {
    $db = DB::getInstance();
    
    $session = &$_SESSION['MOH_Files_Download'];

    $PK_File = intval($_REQUEST['PK_File']);

    $query = "SELECT * FROM Moh_Files WHERE PK_File = '{$PK_File}' LIMIT 1";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));
    $File = $result->fetch(PDO::FETCH_ASSOC);

    $Filename = moh_filename($PK_File);

    if (file_exists($Filename)) {
        //header("Content-type: " . mime_content_type($Filename));
        header("Content-Disposition: attachment; filename=\"" . basename($File['Filename'] . "." . $File['Extension']) . "\"");
        echo file_get_contents($Filename);
    } else {
        echo "File not found.";
    }
    die();
}

admin_run('MOH_Files_Download');
?>
