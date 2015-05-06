<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/config.inc.php');
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function MOH_Files_Modify() {
    global $mysqli;
    include(dirname(__FILE__) . '/../include/config.inc.php');

    $session = &$_SESSION['MOH_Files_Modify'];
    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $action = $_REQUEST['action'];
    if ($action == 'uploadfile') {
        $FK_Group = $_REQUEST['id_group'];

        $bigFK_Group = str_pad($FK_Group, 10, "0", STR_PAD_LEFT);
        $uploadPath = $conf['dirs']['moh'] . "/group_" . $bigFK_Group . "/";

        $filename_ext = explode(".", $_FILES['file']['name']['0']);

        $filename = "";
        for ($i = 0; $i < (count($filename_ext) - 1); $i++) {
            $filename .= $filename_ext[$i];
        }

        $extension = $filename_ext[count($filename_ext) - 1];

        $query = "SELECT MAX(`Order`) FROM Moh_Files WHERE FK_Group = '$FK_Group'";
        $result = $mysqli->query($query) or die($mysqli->error());
        $row = $result->fetch_row();
        $order = $row['0'] + 1;

        $Errors = upload_file($uploadPath, $filename, $extension, $order, $FK_Group);

        if (empty($Errors)) {
            asterisk_UpdateConf('musiconhold.conf');
            asterisk_Reload();
            header("Location: MOH_Files_ListGroup.php?PK_Group=$FK_Group");
            die();
        }
    }

    // Init available groups (Groups)
    $query = "SELECT * FROM Moh_Groups";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    while ($row = $result->fetch_assoc()) {
        $Groups[] = $row;
    }

    $smarty->assign('Groups', $Groups);
    $smarty->assign('Message', $Message);
    $smarty->assign('Errors', $Errors);
    return $smarty->fetch('MOH_Files_Modify.tpl');
}

function upload_file($uploadPath, $filename, $extension, $order, $FK_Group) {
    global $mysqli;
    $errors = array();
    $extension = strtolower($extension);
    if (!$filename || !$extension) {
        $errors['BadFilename'] = true;
        return $errors;
    }

    if ($_FILES['file']['error'][0] == 1) {
        $errors['FileToBig'] = true;
        return $errors;
    }

    //update databse
    $query = "
		INSERT INTO
			`Moh_Files`
		SET
			`Filename` = '" . $mysqli->escape_string($filename) . "',
			`Fileext`  = '" . $mysqli->escape_string($extension) . "',
			`FK_Group` = " . intval($FK_Group) . ",
			`Order`    = " . intval($order) . "
	";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    $PK_File = $mysqli->insert_id;

    //upload to disk
    $order = str_pad($order, 6, "0", STR_PAD_LEFT);
    $PK_File = str_pad($PK_File, 9, "0", STR_PAD_LEFT);

    @mkdir($uploadPath, 0755, true);
    $result = rename($_FILES['file']['tmp_name'][0], "$uploadPath/file_" . $order . "_" . $PK_File . "." . $extension);
    if ($result == false) {
        $errors['UploadToDisk'] = true;
        return $errors;
    }
    chmod("$uploadPath/file_" . $order . "_" . $PK_File . "." . $extension, 0644);
}

admin_run('MOH_Files_Modify', 'Admin.tpl');
