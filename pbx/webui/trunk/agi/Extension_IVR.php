#!/usr/bin/php -q
<?php

require(dirname(__FILE__).'/../lib/phpagi/phpagi.php');
require(dirname(__FILE__).'/../include/db_utils.inc.php');
require(dirname(__FILE__).'/common/AGI_Logger.class.php');
require(dirname(__FILE__).'/common/AGI_CDR.class.php');
require(dirname(__FILE__).'/common/AGI_Utils.php');

function play_sound(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;

	$step++;

	$file_to_play = SoundFile($param['FK_SoundEntry'], $param['FK_SoundLanguage']);

	if ($param['Intrerruptible'] == "1") {
		$result = $agi->get_data("{$file_to_play}", 100, 1);
		$key    = $result['result'];
	} else {
		$agi->exec('Playback', "{$file_to_play}");
	}
}

function playback_rec_sound(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	$var_name      = $param['__Sound'];
	$sound_to_play = $vars[$var_name];

	$agi->exec('Playback', $sound_to_play);
}

function email_rec_sound(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	$var_name       = $param['__Sound'];
	$sound_to_email = $vars[$var_name].".wav";

	$email_to       = $param['Email'];
	$email_subject  = "IVR Recorded Sound";
	$email_headers  = "From: office@telesoft.ro\r\n";
	$email_hash     = md5(date('r', time()));
	$email_headers .= "Reply-To: office@telesoft.ro\r\n";
	$email_headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$email_hash."\"";
	$email_attach   = chunk_split(base64_encode(file_get_contents($sound_to_email)));

	$email_body     = "--PHP-mixed-{$email_hash}
Content-Type: multipart/alternative; boundary=\"PHP-alt-$email_hash\"
--PHP-alt-{$email_hash}
Content-Type: text/plain; charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit

Hello World!!!

--PHP-alt-{$email_hash}
Content-Type: text/html; charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit

Hello World <b>!!!</b>

--PHP-alt-{$email_hash}

--PHP-mixed-{$email_hash}
Content-Type: application/zip; name=\"sound.wav\"
Content-Transfer-Encoding: base64
Content-Disposition: attachment

{$email_attach}
--PHP-mixed-{$email_hash}
";

	mail($email_to, $email_subject, $email_body, $email_headers);
}

function play_digits(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	// Get digits to play
	if (!empty($param['Digits'])) {
		$digits_to_play = $param['Digits'];
	} else {
		$var_name       = $param['__Digits'];
		$digits_to_play = $vars[$var_name];
	}

	$tones = array(
		'1'=>'697+1209','2'=>'697+1336','3'=>'697+1477','A'=>'697+1633','a'=>'697+1633',
		'4'=>'770+1209','5'=>'770+1336','6'=>'770+1477','B'=>'770+1633','b'=>'770+1633',
		'7'=>'852+1209','8'=>'852+1336','9'=>'852+1477','C'=>'852+1633','c'=>'852+1633',
		'*'=>'941+1209','0'=>'941+1336','#'=>'941+1477','D'=>'941+1633','d'=>'941+1633'
	);

	$tonestime  = 1000;
	$tonestring = 0;
	for ($i=0; $i<strlen($digits_to_play); $i++) {
		$digit    = $digits_to_play[$i];
		$playtime = $param['PlayTime'];
		$waittime = $param['WaitTime'];

		if (array_key_exists($digit, $tones)) {
			$tonestring .= ",!{$tones[$digit]}/$playtime,!0/$waittime";
			$tonestime  += $playtime+$waittime;
		}
	}

	$agi->exec('PlayTones','!0/100'.$tonestring);
	sleep(ceil($tonestime/1000));
	$agi->exec('StopPlaytones');
}

function dial_extension(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	if (!empty($param['Extension'])) {
		$ext_to_dial = $param['Extension'];
	} else {
		$var_name    = $param['__Extension'];
		$ext_to_dial = $vars[$var_name];
	}

	$query  = "SELECT * FROM Extensions WHERE Extension = '$ext_to_dial' LIMIT 1";
	$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
	if ($mysqli->numrows($result) == '1') {
		$agi->exec('Goto',array('internal', $ext_to_dial, 1));
		die();
	}

}

function send_to_voicemail(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	if (!empty($param['Extension'])) {
		$mailbox = $param['Extension'];
	} else {
		$var_name    = $param['__Extension'];
		$mailbox = $vars[$var_name];
	}

	$query  = "SELECT * FROM Extensions WHERE Extension = '$mailbox' LIMIT 1";
	$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
	if ($mysqli->numrows($result) == '1') {
		$agi->exec('Voicemail', $mailbox);
		die();
	}

}

function wait(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	if ($param['Intrerruptible'] == "1") {
		$result = $agi->get_data("silence/1", $param['Time']*1000, 1);
		$key    = $result['result'];
	} else {
		sleep($param['Time']);
	}
}

function give_busy(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;

	$agi->exec('Busy', '10');
	die();
}

function hang_up(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	//$agi->hangup();
	exit();
}

function say_digits(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	if (! empty($param['Digits'])) {
		$digits_to_say = $param['Digits'];
	} else {
		$var_name      = $param['__Digits'];
		$digits_to_say = $vars[$var_name];
	}

	$agi->say_phonetic($digits_to_say);
}

function say_number(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	if (! empty($param['Number'])) {
		$number_to_say = $param['Number'];
	} else {
		$var_name      = $param['__Number'];
		$number_to_say = $vars[$var_name];
	}

	$agi->say_number($number_to_say);
}

function set_variable(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	$vars[$param['Name']] = $param['Value'];
}

function record_digits(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	$result          = $agi->get_data('beep', 10000, $param['Length']);
	$recorded_digits = $result['result'];

	$vars[$param['Name']] = $recorded_digits;
}

function record_sound(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	$filename = "/tmp/ivr-".uniqid(time());
	$agi->record_file($filename, 'wav', '#', -1, null, true);

	$vars[$param['Name']] = $filename;
}

function gatekeeper(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	$vars[$param['Name']] += 1;
}

function conditional_clause(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;

	$condition = false;

	switch ($param['Operator']) {
		case 'eq':
			if ($vars[$param['__Value1']] == $param['Value2']) { $condition = true; }
			break;
		case 'neq':
			if ($vars[$param['__Value1']] != $param['Value2']) { $condition = true;	}
			break;
		case 'gt':
			if ($vars[$param['__Value1']] > $param['Value2']) { $condition = true;	}
			break;
		case 'lt':
			if ($vars[$param['__Value1']] < $param['Value2']) { $condition = true;	}
			break;
	}

	if ($condition) {
		$menu  = $param['Menu'];

		$query  = "SELECT `Order` FROM IVR_Actions WHERE FK_Menu='{$param['Menu']}' AND PK_Action='{$param['Action']}'";
		$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
		if ($mysqli->numrows($result) == 1) {
			$row  = $mysqli->fetch_array($result);
			$step = $row[0];
		} else {
			$step = 1;
		}
	} else {
		$step++;
	}
}

function time_clause(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;

	$condition = false;

	$query  = "SELECT COUNT(*) FROM Timeframe_Intervals WHERE ValidTimeInterval(PK_Interval) AND FK_Timeframe = {$param['FK_Timeframe']}";
	$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
	$row    = $mysqli->fetch_array($result);
	$condition = $row[0];

	if ($condition) {
		$menu  = $param['Menu'];

		$query  = "SELECT `Order` FROM IVR_Actions WHERE FK_Menu='{$param['Menu']}' AND PK_Action='{$param['Action']}'";
		$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
		if ($mysqli->numrows($result) == 1) {
			$row  = $mysqli->fetch_array($result);
			$step = $row[0];
		} else {
			$step = 1;
		}
	} else {
		$step++;
	}
}

function goto_context(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;

	$menu  = $param['Menu'];

	$query  = "SELECT `Order` FROM IVR_Actions WHERE FK_Menu='{$param['Menu']}' AND PK_Action='{$param['Action']}'";
	$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
	if ($mysqli->numrows($result) == 1) {
		$row  = $mysqli->fetch_array($result);
		$step = $row[0];
	} else {
		$step = 1;
	}
}

function ivr_run(&$menu, &$step, &$vars, &$key) {
	global $agi, $logger;

	/* Get Action */
	$query  = "SELECT * FROM IVR_Actions WHERE FK_Menu = $menu AND `Order` = $step";
	$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
	$action = $result->fetch_assoc();

	/* Get Params for action */
	$param  = array();
	$query  = "SELECT * FROM IVR_Action_Params WHERE FK_Action = {$action['PK_Action']}";
	$result = $mysqli->query($query) or die($mysqli->error());
	while ($row = $result->fetch_assoc()) {
		$param[$row['Name']]        = $row['Value'];
		$param["__{$row['Name']}"]  = $row['Variable'];
	}

	$logger->debug("{$step} - {$action['Type']}", __FILE__, __LINE__);

	$action['Type']($menu, $step, $vars, $key, $param);
}

function alter_callerid(&$menu, &$step, &$vars, &$key, $param) {
	global $agi, $logger;
	$step++;

	$cid = $agi->parse_callerid();
	$callerid['name']   = $agi->request['agi_calleridname'];
	$callerid['number'] = $cid['username'];

	$text = $param['Text'];
	if ($vars[$param['__Text']] != "") {
		$text = $vars[$param['__Text']];
	}

	switch ($param['Method']) {
		case 'prepend':
			$callerid[$param['Section']] = "$text".$callerid[$param['Section']];
			break;
		case 'append':
			$callerid[$param['Section']] = $callerid[$param['Section']]."$text";
			break;
		case 'replace':
			$callerid[$param['Section']] = "$text";
			break;
	}

	$agi->request['calledid_name'] = $callerid['name'];
	$agi->request['agi_callerid']  = $callerid['number'];

	$agi->set_callerid("\"{$callerid['name']}\"<{$callerid['number']}>");
}

set_time_limit(300);
error_reporting(E_ALL);

$agi    = new AGI();
$logger = new AGI_Logger($agi);
$cdr    = new AGI_CDR($agi);

$agi->answer();
$ext = $agi->request['agi_extension'];

// Get 'Extension' table info
$query = "SELECT PK_Extension, Extension, Type FROM Extensions WHERE Extension = '$ext' LIMIT 1";
$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
$Extension = $result->fetch_assoc();

// Get Starting Parameters
$query   = "SELECT * FROM Ext_IVR WHERE PK_Extension = '{$Extension['PK_Extension']}' LIMIT 1";
$result  = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
$Ext_IVR = $result->fetch_assoc();

// Get Starting Order
$query  = "SELECT `Order` FROM IVR_Actions WHERE PK_Action = {$Ext_IVR['FK_Action']} LIMIT 1";
$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
if ($mysqli->numrows($result) == 1) {
	$step = $mysqli->fetch_array($result);
	$step = $step[0];
} else {
	$step = 1;
}

// Initialization
$vars  = array();
$menu  = $Ext_IVR['FK_Menu'];
$key   = "";
$retry = 0;


// Add CDR 'rang' event
$query    = "SELECT * FROM IVR_Menus WHERE PK_Menu = '$menu' LIMIT 1";
$result   = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
$IVR_Menu = $result->fetch_assoc();

// Set CDR called info
$cdr->set_called($Extension['PK_Extension'], 'IVR', $IVR_Menu['Name'], $Extension['Extension']);


while ($menu > 0 && $step > 0) {
	ivr_run($menu, $step, $vars, $key); // <- that also modify the $step variable

	// See if there is a next step in this menu
	$query  = "SELECT * FROM IVR_Actions WHERE FK_Menu = $menu AND `Order` = $step";
	$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
	$next_step_available = $mysqli->num_rows($result);

	if (! $next_step_available && $key=='') {
		// Get the timeout for this menu
		$query   = "SELECT Timeout FROM IVR_Menus WHERE PK_Menu = '$menu' LIMIT 1";
		$result  = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
		$row     = $mysqli->fetch_array($result);
		$timeout = $row[0]*1000;
		
		if ($timeout) {
			$result    = $agi->get_data('beep', $timeout, 1);
			$key       = $result['result'];
		}
	}

	// Test if a key was pressed
	if ($key != "") {
		$dialing_enabled   = false;
		$extension_to_dial = '';
		$option_to_jumpto  = '';

		// See if extension dialing is enabled in this menu
		$query  = "SELECT ExtensionDialing FROM IVR_Menus WHERE PK_Menu = '$menu' LIMIT 1";
		$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
		$row    = $mysqli->fetch_array($result);
		$dialing_enabled = $row[0];

		// If extension dialing is enabled
		if ($dialing_enabled) {

			// Loop while user timeouts on keypress or pound key is pressed
			$new_digit = $key;
			while ($new_digit != '' && $new_digit!='#') {
				$extension_to_dial .= $new_digit;				
				$result    = $agi->get_data('beep', 2000, 1);
				$new_digit = $result['result'];
			}

			// Check if the extension entered is valid to be dialed, if so dial and get out
			$query  = "SELECT * FROM Extensions WHERE IVRDial = '1' AND Extension = '$extension_to_dial' LIMIT 1";
			$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
			if ($mysqli->num_rows($result)) {
				$agi->exec('Goto',array('internal', $extension_to_dial, 1));
				die();
			}
		}
		
		// Check for requested option
		$query  = "SELECT * FROM IVR_Options WHERE FK_Menu= '$menu' AND `Key`='$key' LIMIT 1";
		$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
		
		// If it's a valid option
		if ($mysqli->num_rows($result)) {
			$row  = $result->fetch_assoc();
			
			// Get Starting Menus
			$menu = $row['FK_Menu_Entry'];
			
			// Get Starting Order
			$query  = "SELECT `Order` FROM IVR_Actions WHERE PK_Action = '{$row['FK_Action_Entry']}' LIMIT 1";
			$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
			if ($mysqli->numrows($result) == 1) {
				$step = $mysqli->fetch_array($result);
				$step = $step[0];
			} else {
				$step = 1;
			}
			
			// Reset the key pressed
			$key = "";
			
		// If it's an invalid option
		} else {
			// Check for invalid option menu / action
			$query  = "SELECT * FROM IVR_Menus WHERE PK_Menu= '$menu' LIMIT 1";
			$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
			$row    = $result->fetch_assoc();
			
			// Play invalid sound file
			$file_to_play = SoundFile($row['FK_SoundEntry_Invalid'], $row['FK_SoundLanguage_Invalid']);
			$agi->exec('Playback', "{$file_to_play}");
		
			// Set invalid menu
			$menu   = $row['FK_Menu_Invalid'];
			
			// Set invalid menu order
			$query  = "SELECT `Order` FROM IVR_Actions WHERE PK_Action = '{$row['FK_Action_Invalid']}' LIMIT 1";
			$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
			if ($mysqli->numrows($result) == 1) {
				$step = $mysqli->fetch_array($result);
				$step = $step[0];
			} else {
				$step = 1;
			}
			
			// Reset the key pressed
			$key = "";
			$retry ++;
		}
		
	// If not key was pressed an we ritched the end of the ivr
	} elseif (! $next_step_available) {
		// Check for timeout option sound / menu / action 
		$query  = "SELECT * FROM IVR_Menus WHERE PK_Menu= '$menu' LIMIT 1";
		$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
		$row    = $result->fetch_assoc();
		
		// Play invalid sound file
		$file_to_play = SoundFile($row['FK_SoundEntry_Timeout'], $row['FK_SoundLanguage_Timeout']);
		$agi->exec('Playback', "{$file_to_play}");
		
		// Set timeout menu
		$menu   = $row['FK_Menu_Timeout'];
		
		// Set timeout menu order
		$query  = "SELECT `Order` FROM IVR_Actions WHERE PK_Action = '{$row['FK_Action_Timeout']}' LIMIT 1";
		$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
		if ($mysqli->numrows($result) == 1) {
			$step = $mysqli->fetch_array($result);
			$step = $step[0];
		} else {
			$step = 1;
		}
		
		// Reset the key pressed
		$key = "";
		$retry++;
	}
	
	// Get the number of allowed retries for this menu
	$query   = "SELECT Retry FROM IVR_Menus WHERE PK_Menu = '$menu' LIMIT 1";
	$result  = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
	$row     = $mysqli->fetch_array($result);
	$retries = $row[0];
	
	if ($retries != 0 && $retry>=$retries) {
		// Check for retry option sound / menu / action 
		$query  = "SELECT * FROM IVR_Menus WHERE PK_Menu= '$menu' LIMIT 1";
		$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
		$row    = $result->fetch_assoc();
		
		// Play invalid sound file
		$file_to_play = SoundFile($row['FK_SoundEntry_Retry'], $row['FK_SoundLanguage_Retry']);
		$agi->exec('Playback', "{$file_to_play}");
		
		// Set timeout menu
		$menu   = $row['FK_Menu_Retry'];
		
		// Set timeout menu order
		$query  = "SELECT `Order` FROM IVR_Actions WHERE PK_Action = '{$row['FK_Action_Retry']}' LIMIT 1";
		$result = $mysqli->query($query) or $logger->error_sql("", $query, __FILE__, __LINE__);
		if ($mysqli->numrows($result) == 1) {
			$step = $mysqli->fetch_array($result);
			$step = $step[0];
		} else {
			$step = 1;
		}
		
		// Reset the key pressed
		$key = "";
		
		// Reset the no of retries
		$retry = 0;
	}
}

?>