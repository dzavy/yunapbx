#!/usr/bin/php-cli -q
<?php
require(dirname(__FILE__) . '/../lib/phpagi/phpagi.php');
require(dirname(__FILE__) . '/../include/db_utils.inc.php');
require(dirname(__FILE__) . '/common/AGI_Logger.class.php');
require(dirname(__FILE__) . '/common/AGI_CDR.class.php');
require(dirname(__FILE__) . '/common/AGI_Utils.php');

$agi = new AGI();
$logger = new AGI_Logger($agi);
$cdr = new AGI_CDR($agi);

// Get Called Extension informations
$called_ext = $agi->request['agi_extension'];
$Extension_D = DB_Extension($called_ext);

// Get 'Intercom' Parameters
$Intercom = Database_Entry('Ext_Intercom', $Extension_D['PK_Extension']);

// Get the extension of the caller
$cid = $agi->parse_callerid();
$caller_ext = $cid['username'];

// See if the caller is allowed to use this extension
if ($Intercom['Use_Admins_ByAccount']) {
    $query = "
		SELECT
			Extension
		FROM
			Ext_Intercom_Admins
			INNER JOIN Extensions ON PK_Extension = FK_Ext_Admin
		WHERE
			FK_Extension = {$Intercom['PK_Extension']}
			AND
			Extensions.Extension = {$caller_ext}
		LIMIT 1
	";
} else {
    $query = "
		SELECT
			Extension
		FROM
			Ext_Intercom_Admins
			INNER JOIN Extension_Groups ON Extension_Groups.FK_Group = Ext_Intercom_Admins.FK_Ext_Group
			INNER JOIN Extensions       ON Extensions.PK_Extension   = Extension_Groups.FK_Extension
		WHERE
			Ext_Intercom_Admins.FK_Extension = {$Intercom['PK_Extension']}
			AND
			Extensions.Extension = {$caller_ext}
		LIMIT 1
	";
}
$result = $mysqli->query($query) or $agi->verbose($mysqli->error . $query);
if ($mysqli->numrows($result) != 1) {
    $agi->stream_file('beeperr');
    $agi->hangup();
    exit(0);
}

//CDR: Set called info
$cdr->set_called(
        "{$Intercom['PK_Extension']}", // CalledID
        "Intercom", // CalledType
        "Intercom", // CalledName
        "{$Extension_D['Extension']}"                     // CalledNumber
);

$agi->set_variable('__SIP_URI_OPTIONS', 'intercom=true');
$agi->set_variable('SIPURI', 'intercom=true');
$agi->set_variable('_VXML_URL', 'intercom=true');
$agi->exec('AbsoluteTimeout', $Intercom['Timeout']);
$agi->exec('SipAddHeader', $Intercom['Header']);
$agi->exec('SipAddHeader', '"Call Info: Answer-After=0"');
$agi->exec('SipAddHeader', '"Alert-Info: Ring Answer"');
$agi->exec('SipAddHeader', '"Call-Info: <uri>\;answer-after=0"');

// Get a list of phones we want to page
if ($Intercom['Use_Members_ByAccount']) {
    $query = "
		SELECT
			Extension
		FROM
			Ext_Intercom_Members
			INNER JOIN Extensions ON PK_Extension = FK_Ext_Member
		WHERE
			FK_Extension = {$Intercom['PK_Extension']}
	";
} else {
    $query = "
		SELECT
			Extension
		FROM
			Ext_Intercom_Members
			INNER JOIN Extension_Groups ON Extension_Groups.FK_Group = Ext_Intercom_Members.FK_Ext_Group
			INNER JOIN Extensions       ON Extensions.PK_Extension   = Extension_Groups.FK_Extension
		WHERE
			Ext_Intercom_Members.FK_Extension = {$Intercom['PK_Extension']}
	";
}

$result = $mysqli->query($query) or $agi->verbose($mysqli->error . $query);
while ($row = $result->fetch_assoc()) {
    if ($page_phones != "") {
        $page_phones .='&';
    }
    $page_phones .= "SIP/{$row['Extension']}";
}

// Set the page options
if ($Intercom['TwoWay']) {
    $page_options .= "d";
}
if (!$Intercom['PlaySound']) {
    $page_options .= "q";
}

// Run the page command
$agi->exec('Page', "$page_phones|$page_options");
?>