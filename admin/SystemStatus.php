<?php

include_once(dirname(__FILE__) . '/../include/db_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/smarty_utils.inc.php');
include_once(dirname(__FILE__) . '/../include/admin_utils.inc.php');
include_once(dirname(__FILE__) . "/../lib/AsteriskManager.class.php");
include_once(dirname(__FILE__) . '/../include/asterisk_utils.inc.php');

function SystemStatus() {
    global $mysqli;
    
    $session = &$_SESSION['SystemStatus'];
    $smarty = smarty_init(dirname(__FILE__) . '/templates');

    update_provider_statuses();
    update_Ext_SipPhones_Statuses();

    // Init no element on page (P_PageSize)
    $P_PageSize = 50;

    // Init P_Sort P_Order (P_Order)
    if ($session['P_Sort'] == $_REQUEST['P_Sort']) {
        $P_Order = ($session['P_Order'] == "asc" ? "desc" : "asc");
    } elseif ($session['P_Sort'] != $_REQUEST['P_Sort']) {
        $P_Order = 'asc';
    }
    $session['P_Order'] = $P_Order;

    // Init P_Sort field (P_Sort)
    if (isset($_REQUEST['P_Sort'])) {
        $P_Sort = $_REQUEST['P_Sort'];
    } else {
        $P_Sort = 'Name';
    }
    $session['P_Sort'] = $P_Sort;

    // Init listing P_Start (P_Start)
    if (isset($_REQUEST['P_Start'])) {
        $P_Start = $_REQUEST['P_Start'];
    } else {
        $P_Start = 0;
    }

    // Init P_Total entries (P_Total)
    $query = "SELECT COUNT(PK_SipProvider) FROM SipProviders";
    $result = $mysqli->query($query) or die($mysqli->error());
    $row = $result->fetch_array();
    $P_Total = $row[0];

    // Init table fields (Extensions)
    $Providers = array();
    $query = "
		(
		SELECT
			PK_SipProvider          AS _PK_,
			Name                    AS Name,
			'SIP'                   AS Type,
			SipProviders.AccountID  AS AccountID,
			SipProviders.Host       AS Host,
			CallbackExtension       AS CallbackExtension,
			Status                  AS Status,
			Latency                 AS Latency
		FROM
			SipProviders
			LEFT JOIN SipProvider_Statuses ON SipProviders.PK_SipProvider = SipProvider_Statuses.FK_SipProvider
		) UNION (
		SELECT
			PK_IaxProvider          AS _PK_,
			Name                    AS Name,
			'IAX'                   AS Type,
			IaxProviders.AccountID  AS AccountID,
			IaxProviders.Host       AS Host,
			CallbackExtension       AS CallbackExtension,
			Status                  AS Status,
			Latency                 AS Latency
		FROM
			IaxProviders
			LEFT JOIN IaxProvider_Statuses ON IaxProviders.PK_IaxProvider = IaxProvider_Statuses.FK_IaxProvider
		)
		ORDER BY
			$P_Sort $P_Order
		LIMIT $P_Start, $P_PageSize
	";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    while ($row = $result->fetch_assoc()) {
        $Providers[] = $row;
    }

    // Init P_End record (P_End)
    $P_End = count($Providers);

    $smarty->assign('Providers', $Providers);
    $smarty->assign('P_Sort', $P_Sort);
    $smarty->assign('P_Order', $P_Order);
    $smarty->assign('P_Start', $P_Start);
    $smarty->assign('P_End', $P_End);
    $smarty->assign('P_Total', $P_Total);
    $smarty->assign('P_PageSize', $P_PageSize);
    $smarty->assign('Message', $Message);
    $smarty->assign('Hilight', (isset($_REQUEST['hilight'])?$_REQUEST['hilight']:""));





    // Init no element on page (C_PageSize)
    $C_PageSize = 50;

    // Init sort order (C_Order)
    if ($session['C_Sort'] == $_REQUEST['C_Sort']) {
        $C_Order = ($session['C_Order'] == "asc" ? "desc" : "asc");
    } elseif ($session['C_Sort'] != $_REQUEST['C_Sort']) {
        $C_Order = 'asc';
    }
    $session['C_Order'] = $C_Order;

    // Init sort field (C_Sort)
    if (isset($_REQUEST['C_Sort'])) {
        $C_Sort = $_REQUEST['C_Sort'];
    } else {
        $C_Sort = 'Extension';
    }
    $session['C_Sort'] = $C_Sort;

    // Init listing start (C_Start)
    if (isset($_REQUEST['C_Start'])) {
        $C_Start = $_REQUEST['C_Start'];
    } else {
        $C_Start = 0;
    }

    // Init total entries (C_Total)
    $query = "SELECT COUNT(PK_Extension) FROM Ext_SipPhones;";
    $result = $mysqli->query($query) or die($mysqli->error());
    $row = $result->fetch_array();
    $C_Total = $row[0];

    // Init table fields (Extensions)
    $Extensions = array();
    $query = "
		SELECT
			Extensions.PK_Extension         AS _PK_,
			Extensions.Extension            AS Extension,
			CONCAT(FirstName,' ', LastName) AS Name,
			UserAgent,
			IPAddress,
			Status
		FROM
			Extensions
			INNER JOIN Ext_SipPhones        ON Ext_SipPhones.PK_Extension     = Extensions.PK_Extension
			INNER JOIN Ext_SipPhones_Status ON Ext_SipPhones_Status.Extension = Extensions.Extension
		ORDER BY
			$C_Sort $C_Order
		LIMIT $C_Start, $C_PageSize

	";
    $result = $mysqli->query($query) or die($mysqli->error());
    while ($row = $result->fetch_assoc()) {
        $Extensions[] = $row;
    }

    // Init end record (C_End)
    $C_End = count($Extensions);

    $smarty->assign('Extensions', $Extensions);
    $smarty->assign('C_Sort', $C_Sort);
    $smarty->assign('C_Order', $C_Order);
    $smarty->assign('C_Start', $C_Start);
    $smarty->assign('C_End', $C_End);
    $smarty->assign('C_Total', $C_Total);
    $smarty->assign('C_PageSize', $C_PageSize);

    return $smarty->fetch('SystemStatus.tpl');
}

function update_provider_statuses() {
    global $mysqli;
    $mysqli->query("DELETE FROM SipProvider_Statuses");
    $query = "SELECT * FROM SipProviders";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    while ($provider = $result->fetch_assoc()) {
        $Status = sip_get_status($provider);
        $query = "INSERT INTO SipProvider_Statuses SET
			FK_SipProvider = '" . $mysqli->real_escape_string($Status['FK_SipProvider']) . "',
			Latency        = '" . $mysqli->real_escape_string($Status['Latency']) . "',
			Status         = '" . $mysqli->real_escape_string($Status['Status']) . "'
		";
        $mysqli->query($query) or die($mysqli->error() . $query);
    }

    /////////////////////
    $mysqli->query("DELETE FROM IaxProvider_Statuses");
    $query = "SELECT * FROM IaxProviders";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);
    while ($provider = $result->fetch_assoc()) {
        $Status = iax_get_status($provider);
        $query = "INSERT INTO IaxProvider_Statuses SET
			FK_IaxProvider = '" . $mysqli->real_escape_string($Status['FK_IaxProvider']) . "',
			Latency        = '" . $mysqli->real_escape_string($Status['Latency']) . "',
			Status         = '" . $mysqli->real_escape_string($Status['Status']) . "'
		";
        $mysqli->query($query) or die($mysqli->error() . $query);
    }
}

function update_Ext_SipPhones_Statuses() {
    global $mysqli;
    $query = "SELECT Extension FROM Extensions WHERE Type = 'SipPhone'";
    $result = $mysqli->query($query) or die($mysqli->error() . $query);

    while ($row = $result->fetch_assoc()) {
        $response = asterisk_Cmd('sip show peer ' . $row['Extension']);
        $response = explode("\n", $response);

        $status = array();
        foreach ($response as $line) {
            $line = ereg('[ *]*([A-Z].*[a-z]) *: (.*)', $line, $regs);
            $status[$regs['1']] = $regs['2'];
        }

        // Extension
        $Extension = $status['Name'];
        // User Agent
        $UserAgent = $status['Useragent'];
        // IPAddress
        ereg('.*@([0-9.]*).*', $status['Reg. Contact'], $regs);
        if (ip2long($regs[1]) == true) {
            $IPAddress = $regs[1];
        } else {
            $IPAddress = "";
        }
        // Status
        if (strpos($status['Status'], "OK") === false) {
            $query2 = "SELECT * FROM Ext_SipPhones_Status WHERE Extension = '" . $mysqli->real_escape_string($Extension) . "' AND Status IN ('OK','TIMEOUT') LIMIT 1";
            $result2 = $mysqli->query($query2);
            if ($result2->num_rows == 1) {
                $Status = "TIMEOUT";
            } else {
                $Status = "UNREACHABLE";
            }
        } else {
            $Status = "OK";
        }

        if ($Status != "TIMEOUT") {
            $query = "
				INSERT INTO Ext_SipPhones_Status(Extension,UserAgent,IPAddress,Status)
				VALUES(
					'" . $mysqli->real_escape_string($Extension) . "',
					'" . $mysqli->real_escape_string($UserAgent) . "',
					'" . $mysqli->real_escape_string($IPAddress) . "',
					'" . $mysqli->real_escape_string($Status) . "'
				)
			";
            $mysqli->query("DELETE FROM Ext_SipPhones_Status WHERE Extension = $Extension LIMIT 1");
            $mysqli->query($query) or die($mysqli->error() . $query);
        } else {
            $mysqli->query("UPDATE Ext_SipPhones_Status SET Status='TIMEOUT' WHERE Extension = $Extension LIMIT 1");
        }

        $mysqli->query("DELETE FROM Ext_SipPhones_Status WHERE Extension NOT IN (SELECT Extension FROM Extensions WHERE Type = 'SipPhone')");
    }
}

function iax_get_status($IaxProvider) {
    $Status = array(
        'FK_IaxProvider' => $IaxProvider['PK_IaxProvider'],
        'Status' => 'Unknown',
        'Latency' => ''
    );

    /*
      iax2 show peer razvanpbx
      ........
      Addr->IP     : 70.183.85.70 Port 4569
      .......
      Status       : OK (256 ms)
      ......
     */
    $response = asterisk_Cmd("iax2 show peer {$IaxProvider['Label']}");
    $response = explode("\n", $response);
    foreach ($response as $line) {
        unset($regs);
        if (ereg(' *Addr->IP *: *([0-9.]*) Port ([0-9]*)', $line, $regs)) {
            $IaxProvider['IP'] = $regs[1];
            $IaxProvider['Port'] = $regs[2];
        }

        unset($regs);
        if (ereg(' *Status *: OK \(([0-9]*) ms\)', $line, $regs)) {
            $Status['Latency'] = $regs['1'];
        }

        unset($regs);
        if ($IaxProvider['RegisterType'] == 'Client' || $IaxProvider['RegisterType'] == 'Peer') {
            if (ereg(' *Status *: ([0-9a-zA-Z]*)', $line, $regs)) {
                if ($regs[1] == 'OK') {
                    $Status['Status'] = 'Registered';
                } else {
                    $Status['Status'] = ucfirst(strtolower($regs[1]));
                }
            }
        }
    }

    /*
      iax2 show registry
      Host                  dnsmgr  Username    Perceived             Refresh  State
      70.183.85.70:4569     N       switchvoxp  <Unregistered>             60  Rejected
     */
    $response = asterisk_Cmd('iax2 show registry');
    $response = explode("\n", $response);
    foreach ($response as $line) {
        unset($regs);
        if (ereg("^{$IaxProvider['IP']}:{$IaxProvider['Port']} *([^ ]*) *([^ ]*) *([^ ]*) *([^ ]*) *(.*)$", $line, $regs)) {
            $Status['Status'] = $regs['5'];
        }
    }

    return $Status;
}

function sip_get_status($SipProvider) {
    $Status = array(
        'FK_SipProvider' => $SipProvider['PK_SipProvider'],
        'Status' => 'Unknown',
        'Latency' => ''
    );

    /*
      sip show peer sip_provider_17
      .......
      Status       : Unmonitored
      ......
     */
    $response = asterisk_Cmd("sip show peer sip_provider_{$SipProvider['PK_SipProvider']}");
    $response = explode("\n", $response);
    foreach ($response as $line) {
        unset($regs);
        if (ereg(' *Status *: OK \(([0-9]*) ms\)', $line, $regs)) {
            $Status['Latency'] = $regs['1'];
        }

        if ($SipProvider['HostType'] == 'Peer') {
            unset($regs);
            if (ereg(' *Status *: ([0-9a-zA-Z]*)', $line, $regs)) {
                if ($regs[1] == 'OK') {
                    $Status['Status'] = 'Registered';
                } else {
                    $Status['Status'] = ucfirst(strtolower($regs[1]));
                }
            }
        }
    }

    /*
      sip show registry
      Host                            Username       Refresh State                Reg.Time
      sip_provider_9:5060             40336560020        105 Registered           Mon, 18 May 2009 17:31:13
     */
    $response = asterisk_Cmd('sip show registry');
    $response = explode("\n", $response);
    foreach ($response as $line) {
        unset($regs);
        if (ereg("^sip_provider_{$SipProvider['PK_SipProvider']}:([0-9]*) *[^ ]* *([^ ]*) *([0-9]*) *(.*[^ ]{1})[ ]{2} *(.*)$", $line, $regs)) {
            $Status['Status'] = $regs['4'];
        }
    }

    return $Status;
}

admin_run('SystemStatus', 'Admin.tpl');
?>