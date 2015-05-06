<?php

include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

include_once(dirname(__FILE__) . '/../include/network_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/validation_utils.inc.php');
include_once(dirname(__FILE__) . "/../include/asterisk_utils.inc.php");
include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');

function Backup_FTP_Test() {
    
    $session = &$_SESSION['Backup_FTP_Test'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $server = $_REQUEST['FtpHostname'];
    $user = $_REQUEST['FtpUsername'];
    $password = $_REQUEST['FtpPassword'];
    $path = $_REQUEST['FtpPath'];

    $Errors = @ftp_test($server, $user, $password, $path);
    $Connect = array();
    $Login = array();
    $Path = array();
    $Upload = array();
    $Delete = array();

    /*     * ******************************************************************************************** */
    if (!$Errors) {
        $Connect['Class'] = "small_success_message";
        $Login['Class'] = "small_success_message";
        $Path['Class'] = "small_success_message";
        $Upload['Class'] = "small_success_message";
        $Delete['Class'] = "small_success_message";

        $Connect['Status'] = "Passed";
        $Login['Status'] = "Passed";
        $Path['Status'] = "Passed";
        $Upload['Status'] = "Passed";
        $Delete['Status'] = "Passed";

        $Connect['Message'] = "Successfully connected to the ftp server.";
        $Login['Message'] = "Successfully logged in using the user name and password.";
        $Path['Message'] = "Successfully changed to the correct path.";
        $Upload['Message'] = "Successfully uploaded a test file.";
        $Delete['Message'] = "Successfully deleted the test file.";
    }
    /*     * ******************************************************************************************** */ else if ($Errors['Connect']) {
        $Connect['Class'] = "small_warning_message";
        $Login['Class'] = "small_not_tested_message";
        $Path['Class'] = "small_not_tested_message";
        $Upload['Class'] = "small_not_tested_message";
        $Delete['Class'] = "small_not_tested_message";

        $Connect['Status'] = "Failed";
        $Login['Status'] = "Not Tested";
        $Path['Status'] = "Not Tested";
        $Upload['Status'] = "Not Tested";
        $Delete['Status'] = "Not Tested";

        $Connect['Message'] = "No connection could be established with the ftp server. Please make sure you typed in the host correctly, it's running a valid ftp server, and your PBX machine has a route to your ftp host (eg no firewall restrictions). If you are using a hostname for your ftp server, instead try entering the IP address of the ftp server.";
        $Login['Message'] = "This test was not performed because of the above errors. Please fix the above errors and rerun this diagnosis.";
        $Path['Message'] = "This test was not performed because of the above errors. Please fix the above errors and rerun this diagnosis.";
        $Upload['Message'] = "This test was not performed because of the above errors. Please fix the above errors and rerun this diagnosis..";
        $Delete['Message'] = "This test was not performed because of the above errors. Please fix the above errors and rerun this diagnosis.";
    }
    /*     * ******************************************************************************************** */ else if ($Errors['Login']) {
        $Connect['Class'] = "small_success_message";
        $Login['Class'] = "small_warning_message";
        $Path['Class'] = "small_not_tested_message";
        $Upload['Class'] = "small_not_tested_message";
        $Delete['Class'] = "small_not_tested_message";

        $Connect['Status'] = "Passed";
        $Login['Status'] = "Failed";
        $Path['Status'] = "Not Tested";
        $Upload['Status'] = "Not Tested";
        $Delete['Status'] = "Not Tested";

        $Connect['Message'] = "Successfully connected to the ftp server.";
        $Login['Message'] = "Unable to log into the ftp server. Make sure you entered the correct username and password for this server.";
        $Path['Message'] = "This test was not performed because of the above errors. Please fix the above errors and rerun this diagnosis.";
        $Upload['Message'] = "This test was not performed because of the above errors. Please fix the above errors and rerun this diagnosis.";
        $Delete['Message'] = "This test was not performed because of the above errors. Please fix the above errors and rerun this diagnosis.";
    }
    /*     * ******************************************************************************************** */ else if ($Errors['Path']) {
        $Connect['Class'] = "small_success_message";
        $Login['Class'] = "small_success_message";
        $Path['Class'] = "small_warning_message";
        $Upload['Class'] = "small_not_tested_message";
        $Delete['Class'] = "small_not_tested_message";

        $Connect['Status'] = "Passed";
        $Login['Status'] = "Passed";
        $Path['Status'] = "Failed";
        $Upload['Status'] = "Not Tested";
        $Delete['Status'] = "Not Tested";

        $Connect['Message'] = "Successfully connected to the ftp server.";
        $Login['Message'] = "Successfully logged in using the user name and password.";
        $Path['Message'] = "Unable to change to the ftp path. Make sure the path exists and the user has proper permissions to access this path.";
        $Upload['Message'] = "This test was not performed because of the above errors. Please fix the above errors and rerun this diagnosis.";
        $Delete['Message'] = "This test was not performed because of the above errors. Please fix the above errors and rerun this diagnosis.";
    }
    /*     * ******************************************************************************************** */ else if ($Errors['Upload']) {
        $Connect['Class'] = "small_success_message";
        $Login['Class'] = "small_success_message";
        $Path['Class'] = "small_success_message";
        $Upload['Class'] = "small_warning_message";
        $Delete['Class'] = "small_not_tested_message";

        $Connect['Status'] = "Passed";
        $Login['Status'] = "Passed";
        $Path['Status'] = "Passed";
        $Upload['Status'] = "Failed";
        $Delete['Status'] = "Not Tested";

        $Connect['Message'] = "Successfully connected to the ftp server.";
        $Login['Message'] = "Successfully logged in using the user name and password.";
        $Path['Message'] = "Successfully changed to the correct path.";
        $Upload['Message'] = "Unable to upload a test file. Make sure the user has write permissions in the ftp path.";
        $Delete['Message'] = "This test was not performed because of the above errors. Please fix the above errors and rerun this diagnosis.";
    }
    /*     * ******************************************************************************************** */ else if ($Errors['Delete']) {
        $Connect['Class'] = "small_success_message";
        $Login['Class'] = "small_success_message";
        $Path['Class'] = "small_success_message";
        $Upload['Class'] = "small_success_message";
        $Delete['Class'] = "small_warning_message";

        $Connect['Status'] = "Passed";
        $Login['Status'] = "Passed";
        $Path['Status'] = "Passed";
        $Upload['Status'] = "Passed";
        $Delete['Status'] = "Failed";

        $Connect['Message'] = "Successfully connected to the ftp server.";
        $Login['Message'] = "Successfully logged in using the user name and password.";
        $Path['Message'] = "Successfully changed to the correct path.";
        $Upload['Message'] = "Successfully uploaded a test file.";
        $Delete['Message'] = "Unable to delete test file. Make sure the user has delete permissions in the ftp path.";
    }
    //end if

    $smarty->assign('Connect', $Connect);
    $smarty->assign('Login', $Login);
    $smarty->assign('Path', $Path);
    $smarty->assign('Upload', $Upload);
    $smarty->assign('Delete', $Delete);

    $smarty->assign('Errors', $Errors);

    return $smarty->fetch('Backup_FTP_Test.tpl');
}

admin_run('Backup_FTP_Test', 'FTP_Test_Window.tpl');
