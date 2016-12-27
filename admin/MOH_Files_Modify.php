<?php
include_once(dirname(__FILE__) . '/../config/yunapbx.php');
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function MOH_Files_Modify() {
    global $mysqli;
    global $conf;

    $session = &$_SESSION['MOH_Files_Modify'];
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $action = $_REQUEST['action'];
    if ($action == 'uploadfile') {
        $FK_Group = $_REQUEST['id_group'];

        $bigFK_Group = str_pad($FK_Group, 10, "0", STR_PAD_LEFT);
        $uploadPath = $conf['dirs']['moh'] . "/group_" . $bigFK_Group . "/";

        if(is_readable($_FILES['file']['name']['0'])) {
            $finfo = new finfo(FILEINFO_MIME);
            $mime_type = $finfo->file($_FILES['file']['name']['0'], FILEINFO_MIME_TYPE);
            
            if(in_array($mime_type, array("audio/x-wav", "audio/mpeg"))) {
                $query = "SELECT MAX(`Order`) FROM Moh_Files WHERE FK_Group = '$FK_Group'";
                $result = $mysqli->query($query) or die($mysqli->error);
                $row = $result->fetch_row();
                $order = $row['0'] + 1;

                $Errors = upload_file($uploadPath, $mime_type, $order, $FK_Group);
            }
        } else {
            
        }
        if (empty($Errors)) {
            asterisk_UpdateConf('musiconhold.conf');
            asterisk_Reload();
            header("Location: MOH_Files_ListGroup.php?PK_Group=$FK_Group");
        }
    }

    // Init available groups (Groups)
    $query = "SELECT * FROM Moh_Groups";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    while ($row = $result->fetch_assoc()) {
        $Groups[] = $row;
    }

    $smarty->assign('Groups', $Groups);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);
    return $smarty->fetch('MOH_Files_Modify.tpl');
}

function upload_file($uploadPath, $mime_type, $order, $FK_Group) {
    global $mysqli;
    $errors = array();

    if ($_FILES['file']['error'][0] == 1) {
        $errors['FileToBig'] = true;
        return $errors;
    }

    //update databse
    $query = "
		INSERT INTO
			`Moh_Files`
		SET
			`Filename` = '" . $mysqli->escape_string(basename($_FILES['file']['error'][0])) . "',
			`FK_Group` = " . intval($FK_Group) . ",
			`Order`    = " . intval($order) . "
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);
    $PK_File = $mysqli->insert_id;

    //upload to disk
    $order = str_pad($order, 6, "0", STR_PAD_LEFT);
    $PK_File = str_pad($PK_File, 9, "0", STR_PAD_LEFT);

    @mkdir($uploadPath, 0755, true);
    if($mime_type == "audio/x-wav") {
        $result = rename($_FILES['file']['tmp_name'][0], "$uploadPath/file_" . $order . "_" . $PK_File . ".wav");
        if ($result == false) {
            $errors['UploadToDisk'] = true;
            return $errors;
        }
    } else {
        exec("/usr/bin/madplay \"" . $_FILES['file']['tmp_name'][0] . "\" -Q -1 -R 8000 -o wave:" . $uploadPath . "/file_" . $order . "_" . $PK_File . ".wav", $exec_output, $exec_rc);
        unlink($_FILES['file']['tmp_name'][0]);
        if($exec_rc!=0) {
            $errors['UploadToDisk'] = true;
            return $errors;
        }
    }

    chmod("$uploadPath/file_" . $order . "_" . $PK_File . ".wav", 0644);
}

admin_run('MOH_Files_Modify', 'Admin.tpl');
