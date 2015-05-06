<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');

function VoipProviders_List() {
    global $mysqli;
    
    $session = &$_SESSION['VoipProviders_List'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    $Message = (isset($_REQUEST['msg'])?$_REQUEST['msg']:"");

    // Init no element on page (PageSize)
    $PageSize = 50;

    // Init sort order (Order)
    if ($session['Sort'] == $_REQUEST['Sort']) {
        $Order = ($session['Order'] == "asc" ? "desc" : "asc");
    } elseif ($session['Sort'] != $_REQUEST['Sort']) {
        $Order = 'asc';
    }
    $session['Order'] = $Order;

    // Init sort field (Sort)
    if (isset($_REQUEST['Sort'])) {
        $Sort = $_REQUEST['Sort'];
    } else {
        $Sort = 'Name';
    }

    $session['Sort'] = $Sort;

    // Init listing start (Start)
    if (isset($_REQUEST['Start'])) {
        $Start = $_REQUEST['Start'];
    } else {
        $Start = 0;
    }

    // Init total entries (Total)
    $query = "SELECT COUNT(*) FROM SipProviders";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    $row = $result->fetch_array();
    $Total = $row[0];

    $query = "SELECT COUNT(*) FROM IaxProviders";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    $row = $result->fetch_array();
    $Total += $row[0];


    // Init table fields (Extensions)
    $Providers = array();
    $query = "
			SELECT
				PK_SipProvider    AS _PK_,
				Name              AS Name,
				'SIP'             AS Type,
				AccountID         AS AccountID,
				Host              AS Host,
				CallbackExtension AS CallbackExtension
			FROM
				SipProviders
		UNION
			SELECT
				PK_IaxProvider    AS _PK_,
				Name              AS Name,
				'IAX'             AS Type,
				AccountID         AS AccountID,
				Host              AS Host,
				CallbackExtension AS CallbackExtension
			FROM
				IaxProviders
		ORDER BY
			$Sort $Order
		LIMIT $Start, $PageSize
	";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    while ($row = $result->fetch_assoc()) {
        $Providers[] = $row;
    }

    // Init end record (End)
    $End = count($Providers);



    //Init RSAKey_Name
    $query = "SELECT Value FROM Settings WHERE Name = 'RSAKey_Name'";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    $row = $result->fetch_assoc();

    $RSAKey_Name = $row['Value'];
    $path_key = "/home/rgavril/Work/TeleSoftPBX2/moh/";


    // Init form data
    $RTP_Ports = formdata_from_db();

    if (($_REQUEST['submit'] == "rename_rsa_key") && ($_REQUEST['RSAKey_Name'] != NULL)) {

        $sanitized_name = preg_replace('/[^0-9a-z\.\_\-]/i', '', $_REQUEST['RSAKey_Name']);
        $query = "  UPDATE 
						Settings
				    SET 
						Value='" . $mysqli->real_escape_string($sanitized_name) . "'
					WHERE 
						Name = 'RSAKey_Name'
		";
        $result = $mysqli->query($query) or die($mysqli->error() . $query);
        rename($path_key . $RSAKey_Name, $path_key . $sanitized_name);
        $RSAKey_Name = $sanitized_name;
    } else if ($_REQUEST['submit'] == "rtp") {
        $RTP_Ports = formdata_from_post();
        $Errors = formdata_validate($RTP_Ports);

        if (count($Errors) == 0) {
            $id = formdata_save($RTP_Ports);
            $Message = "MODIFY_RTP_RANGE";
            $RTP_Ports = formdata_from_db();
        } else {
            $Message = "ERRORS_RTP_RANGE";
            $RTP_Ports = formdata_from_db();
        }
    }

    $smarty->assign('RSAKey_Name', $RSAKey_Name);

    $smarty->assign('RTP_Ports', $RTP_Ports);

    $smarty->assign('Errors', $Errors);
    $smarty->assign('Providers', $Providers);
    $smarty->assign('Sort', $Sort);
    $smarty->assign('Order', $Order);
    $smarty->assign('Start', $Start);
    $smarty->assign('End', $End);
    $smarty->assign('Total', $Total);
    $smarty->assign('PageSize', $PageSize);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));

    return $smarty->fetch('VoipProviders_List.tpl');
}

function formdata_from_post() {
    return $_POST;
}

function formdata_save($data) {
    pbx_var_set("RTP_PortStart", $data['RTP_PortStart']);
    pbx_var_set("RTP_PortEnd", $data['RTP_PortEnd']);
}

function formdata_from_db() {
    $data = array();
    $data['RTP_PortStart'] = pbx_var_get("RTP_PortStart");
    $data['RTP_PortEnd'] = pbx_var_get("RTP_PortEnd");
    return $data;
}

function formdata_validate($data) {

    $errors = array();

    if ((!is_numeric($data['RTP_PortStart'])) || ($data['RTP_PortStart'] < 0)) {
        $errors['RTP_PortStart'] = true;
    }

    if ((!is_numeric($data['RTP_PortEnd'])) || ($data['RTP_PortEnd'] > 99999)) {
        $errors['RTP_PortEnd'] = true;
    }

    if ($data['RTP_PortStart'] > $data['RTP_PortEnd']) {
        $errors['RTP_PortStart'] = true;
        $errors['RTP_PortEnd'] = true;
    }

    return $errors;
}

admin_run('VoipProviders_List', 'Admin.tpl');
