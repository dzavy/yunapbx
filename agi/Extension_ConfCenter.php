#!/usr/bin/php -q
<?php
require(dirname(__FILE__).'/../lib/phpagi/phpagi.php');
require(dirname(__FILE__).'/../include/db_utils.inc.php');
require(dirname(__FILE__).'/common/AGI_Logger.class.php');
require(dirname(__FILE__).'/common/AGI_CDR.class.php');
require(dirname(__FILE__).'/common/AGI_Utils.php');

$agi    = new AGI();
$logger = new AGI_Logger($agi);
$cdr    = new AGI_CDR($agi);

// Get Called Extension informations
$called_ext   = $agi->request['agi_extension'];
$Extension_D  = DB_Extension($called_ext);


// Get the extension of the caller
$cid = $agi->parse_callerid();
$caller_ext = $cid['username'];

// Get 'ConfCenter' Parameters
$ConfCenter = Database_Entry('Ext_ConfCenter', $Extension_D['PK_Extension']);

//CDR: Set called info
//$cdr->set_called(
//	"{$ConfCenter['PK_Extension']}",                    // CalledID
//	"ConfCenter",                                       // CalledType
//	"Conference Center",                                // CalledName
//	"{$Extension_D['Extension']}"                      // CalledNumber
//);

// Request a conference pin number
$invalid_atempts = 0;
while ($conf_no == '') {
	$result = $agi->get_data('conf-getconfno',3000, 100);
	$conf_no = $result['result'];
	
	$query  = "SELECT * FROM Ext_ConfCenter_Rooms WHERE Number='$conf_no' LIMIT 1";
	$result = $mysqli->query($query) or $logger->error_sql($mysqli->error, $query, __FILE__, __LINE__);
	if ($mysqli->numrows($result) == 1) {
		$Room = $result->fetch_assoc();
	} else {
		$agi->stream_file('conf-invalid');
		$conf_no = '';
				$invalid_atempts += 1;
	}
}

// Get a list of conference admins
$Admins = array();
$query = "
	SELECT 
		Extension
	FROM
		Extensions
		INNER JOIN Ext_ConfCenter_Admins ON FK_Extension = PK_Extension
	WHERE
		FK_Room = '{$Room['PK_Room']}'
";
$result = $mysqli->query($query) or $logger->error_sql($mysqli->error, $query, __FILE__, __LINE__);
while ($row = $result->fetch_assoc()) {
	$Admins[] = $row['Extension'];
}

// Set conference options
$conf_options = '';

if (in_array($caller_ext, $Admins)) { 
	$conf_options .= 'Aa';
} elseif ($Room['OnlyAdminTalk'] == '1') {
	$conf_options .= 'm';
}
if ($Room['PlayMOH']        == '1') { $conf_options .= 'M';  }
if ($Room['PlayEnterSound'] != '1') { $conf_options .= 'q';  }
if ($Room['Operator']       != '' ) { $conf_options .= 'p';  }
if ($Room['NoTalkTillAdmin']== '1') { $conf_options .= 'w';  }
if ($Room['HangupIfNoAdmin']== '1') { $conf_options .= 'x';  }

// Call MeetMe Applications
$agi->exec('MeetMe',array($conf_no, $conf_options));

$agi->exec('Goto',array('internal', $Room['Operator'], 1));
?>
