<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function MOH_Files_ListGroup_Ajax() {
    global $mysqli;
    global $conf;
    
    $path = $conf['dirs']['moh'];
    
    $session = &$_SESSION['MOH_Files_ListGroup_Ajax'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');
    $response = array();
    switch ($_REQUEST['Action']) {
        case 'UpdateRuleOrder': {
                $order = 1;
                foreach ($_REQUEST['Rules'] as $PK_Rule) {
                    $PK_Rule = explode('_', $PK_Rule);
                    $PK = $PK_Rule[1];

                    //disk
                    $query = "SELECT
								`FK_Group`, `Order`, `Fileext`
						  FROM
								`Moh_Files`
						  WHERE `PK_File` = $PK";
                    $result = $mysqli->query($query) or die($mysqli->error);
                    $File_src = $result->fetch_assoc();

                    $PK_Group = $File_src['FK_Group'];
                    $old_order = $File_src['Order'];
                    $extension = $File_src['Fileext'];


                    $src = $path;
                    $src.="/group_" . str_pad($PK_Group, 10, "0", STR_PAD_LEFT);
                    $src.="/file_" . str_pad($old_order, 6, "0", STR_PAD_LEFT);
                    $src.="_" . str_pad($PK, 9, "0", STR_PAD_LEFT) . "." . $extension;

                    $dest = $path;
                    $dest.="/group_" . str_pad($PK_Group, 10, "0", STR_PAD_LEFT);
                    $dest.="/file_" . str_pad($order, 6, "0", STR_PAD_LEFT);
                    $dest.="_" . str_pad($PK, 9, "0", STR_PAD_LEFT) . "." . $extension;

                    rename($src, $dest);

                    //db
                    $query = "UPDATE `Moh_Files` SET `Order` = '" . intval($order) . "' WHERE `PK_File` = '" . intval($PK) . "'";
                    $mysqli->query($query) or die($mysqli->error);

                    $order++;
                }
                asterisk_UpdateConf('musiconhold.conf');
                asterisk_Reload();
            }break;
    }
    echo json_encode($response);
}

admin_run('MOH_Files_ListGroup_Ajax');
