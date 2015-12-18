<?php

include_once(dirname(__FILE__) . '/../../include/config.inc.php');

class AGI_CDR {

    private $agi;
    public $cdr_id;
    private $cdr_parent_id;
    private $call_start;
    private $call_type;
    private $caller_id;
    private $caller_type;
    private $caller_name;
    private $caller_number;
    private $called_id;
    private $called_type;
    private $called_name;
    private $called_number;
    private $monitoring_started = false;

    function AGI_CDR($agi) {
        $this->agi = $agi;

        // Initialize 'cdr_id'
        $resp = $this->agi->get_variable('cdr_id');
        if ($resp['result']) {
            $this->cdr_id = $resp['data'];
        } else {
            $this->cdr_id = uniqid(time());
            $this->agi->set_variable('CDR(userfield)', $this->cdr_id);
            $this->agi->set_variable('cdr_id', $this->cdr_id);
        }

        // Initialize 'cdr_parent_id
        $resp = $this->agi->get_variable('cdr_parent_id');
        if ($resp['result']) {
            $this->cdr_parent_id = $resp['data'];
        } else {
            $this->cdr_parent_id = $this->cdr_id;
        }

        // Initialize call_type
        $resp = $agi->get_variable('call_type');
        if ($resp['result']) {
            $this->call_type = $resp['data'];
        }

        // Initialize call_start
        $resp = $agi->get_variable('call_start');
        if ($resp['result']) {
            $this->call_start = $resp['data'];
        } else {
            $this->call_start = time();
            $this->agi->set_variable('call_start', $this->call_start);
        }

        // Initialize caller information
        $resp = $agi->get_variable('caller_id');
        if ($resp['result']) {
            $this->caller_id = $resp['data'];
        }
        $resp = $agi->get_variable('caller_type');
        if ($resp['result']) {
            $this->caller_type = $resp['data'];
        }
        $resp = $agi->get_variable('caller_name');
        if ($resp['result']) {
            $this->caller_name = $resp['data'];
        }
        $resp = $agi->get_variable('caller_number');
        if ($resp['result']) {
            $this->caller_number = $resp['data'];
        }

        // Initialize called information
        $resp = $agi->get_variable('called_id');
        if ($resp['result']) {
            $this->called_id = $resp['data'];
        }
        $resp = $agi->get_variable('called_type');
        if ($resp['result']) {
            $this->called_type = $resp['data'];
        }
        $resp = $agi->get_variable('called_name');
        if ($resp['result']) {
            $this->called_name = $resp['data'];
        }
        $resp = $agi->get_variable('called_number');
        if ($resp['result']) {
            $this->called_number = $resp['data'];
        }

        $resp = $agi->get_variable('monitoring_started');
        if ($resp['result']) {
            $this->monitoring_started = $resp['data'];
        }
    }

    function caller_known() {
        $resp = $this->agi->get_variable('caller_number');
        return $resp['result'];
    }

    function called_known() {
        $resp = $this->agi->get_variable('called_number');
        return $resp['result'];
    }

    function set_caller($caller_id, $caller_type, $caller_name, $caller_number) {
        $this->caller_id = $caller_id;
        $this->caller_type = $caller_type;
        $this->caller_name = $caller_name;
        $this->caller_number = $caller_number;

        $this->agi->set_variable('__caller_id', $this->caller_id);
        $this->agi->set_variable('__caller_type', $this->caller_type);
        $this->agi->set_variable('__caller_name', $this->caller_name);
        $this->agi->set_variable('__caller_number', $this->caller_number);

        $this->monitor_start();
    }

    function set_called($called_id, $called_type, $called_name, $called_number) {
        $this->called_id = $called_id;
        $this->called_type = $called_type;
        $this->called_name = $called_name;
        $this->called_number = $called_number;

        $this->agi->set_variable('__called_id', $this->called_id);
        $this->agi->set_variable('__called_type', $this->called_type);
        $this->agi->set_variable('__called_name', $this->called_name);
        $this->agi->set_variable('__called_number', $this->called_number);

        $this->monitor_start();
    }

    function set_type($call_type) {
        $this->call_type = $call_type;

        $this->agi->set_variable('call_type', $this->call_type);
    }

    function save_cdr() {
        global $mysqli;
        // Get cause of hangup
        $resp = $this->agi->get_variable('HANGUPCAUSE');
        $cause = $resp['data'];

        // Get status of hangup
        $resp = $this->agi->get_variable('AGISTATUS');
        $status = $resp['data'];

        // Who hung up the phone ?
        if ($status != 'HANGUP') {
            $extension = $called_extension;
        } else {
            $extension = $caller_extension;
        }

        $query = "
			INSERT INTO
				CallLog
			SET
				PK_CallLog        = '{$this->cdr_id}',
				FK_CallLog_Parent = '{$this->cdr_parent_id}',

				CallType     = '{$this->call_type}',
				StartDate    =  FROM_UNIXTIME({$this->call_start}),

				CallerID     = '{$this->caller_id}',
				CallerType   = '{$this->caller_type}',
				CallerName   = '{$this->caller_name}',
				CallerNumber = '{$this->caller_number}',

				CalledID     = '{$this->called_id}',
				CalledType   = '{$this->called_type}',
				CalledName   = '{$this->called_name}',
				CalledNumber = '{$this->called_number}',

				BillSec      = (SELECT billsec FROM CDR WHERE userfield = '{$this->cdr_id}' LIMIT 1),
				Duration     = (SELECT duration FROM CDR WHERE userfield = '{$this->cdr_id}' LIMIT 1)
		";
        $mysqli->query($query) or $this->agi->verbose($mysqli->error . $query);
    }

    function check_transfer() {
        global $mysqli;
        // See if the phone was hung up using a attened transfer
        $query = "SELECT * FROM CDR WHERE userfield = '$this->cdr_id' LIMIT 1";
        $result = $mysqli->query($query) or $this->agi->verbose($mysqli->error . $query);
        $cdr_row = $result->fetch_assoc();

        $query = "
			SELECT
				*
			FROM
				CDR
			WHERE
				(
					channel = '" . $mysqli->escape_string($cdr_row['dstchannel']) . "'
					OR
					dstchannel = '" . $mysqli->escape_string($cdr_row['dstchannel']) . "'
				) AND dst = 's'
			LIMIT 1";
        $result = $mysqli->query($query) or $this->agi->verbose($mysqli->error . $query);
        if ($mysqli->num_rows($result) != 1) {
            return;
        }
        $cdr_s_row = $result->fetch_assoc();

        $this->agi->verbose("--------------------------------ATENDED TRANSFER DETECTED-------src:{$cdr_row['src']}-d:src:{$cdr_row['dst']}");
        $this->agi->verbose("--------------------------------ATENDED TRANSFER DETECTED-------src:{$cdr_row['src']}-d:src:{$cdr_row['dst']}");
        $this->agi->verbose("--------------------------------ATENDED TRANSFER DETECTED-------src:{$cdr_row['src']}-d:src:{$cdr_row['dst']}");
        $this->agi->verbose("--------------------------------ATENDED TRANSFER DETECTED-------src:{$cdr_row['src']}-d:src:{$cdr_row['dst']}");
    }

    function flush_cdr($save = true) {
        $this->agi->exec('ResetCDR', 'w');

        $this->monitor_stop();
        if ($save) {
            $this->save_cdr();
            $this->check_transfer();
        }

        if (empty($this->cdr_parent_id)) {
            $this->cdr_parent_id = $this->cdr_id;
        }

        $this->cdr_id = uniqid(time());
        $this->agi->set_variable('CDR(userfield)', $this->cdr_id);
        $this->agi->set_variable('cdr_id', $this->cdr_id);
        $this->agi->set_variable('cdr_parent_id', $this->cdr_parent_id);

        $this->call_start = time();
        $this->agi->set_variable('call_start', $this->call_start);
    }

    function push_event($event, $data) {
        global $mysqli;
        global $agi;
        $this->agi->verbose("CDR[{$this->cdr_id}] $event $data");
        $query = "
			INSERT INTO
				CallLog_Details
			SET
				FK_CallLog = '{$this->cdr_id}',
				Event      = '$event',
				Data       = '$data'
		";
        $mysqli->query($query) or $agi->verbose($mysqli->error);
    }

    function push_hangup() {
        // Get status of hangup
        $resp = $this->agi->get_variable('AGISTATUS');
        $AGISTATUS = $resp['data'];

        // Who hung up the phone ?
        if ($AGISTATUS != 'HANGUP') {
            $this->push_event('HANGUP', "$this->called_number,$this->called_name");
        } else {
            $this->push_event('HANGUP', "$this->caller_number,$this->caller_name");
        }
    }

    function push_transfer($called_ext) {
        $trans_name = $this->caller_name;
        $trans_number = $this->caller_number;

        $this->flush_cdr(false);

        $this->push_event('TRANSFER', "$called_ext,$trans_number,$trans_name");
    }

    function monitor_getrules($caller_id, $called_id, $call_date = 0) {
        global $mysqli;
        $ActiveRules = array();

        // Get a list with active rules
        $query = "
			SELECT
				*
			FROM
				RecordingRules
			WHERE
				(UNIX_TIMESTAMP(EndDate) > {$this->call_start} OR EndDate = 0 OR EndDate IS NULL)
			ORDER BY
				DateCreated
		";
        $result_rules = $mysqli->query($query) or $this->agi->verbose($mysqli->error . $query);
        while ($Rule = $mysqli->fetch_assoc($result_rules)) {

            // Skip rules which have riched the EndCount
            if ($Rule['EndCount'] > 0) {
                $query = "SELECT COUNT(*) FROM RecordingLog WHERE FK_Rule = {$Rule['PK_Rule']}";
                $result = $mysqli->query($query) or $this->agi->verbose($mysqli->error . $query);
                $row = $result->fetch_row();

                if ($row[0] >= $Rule['EndCount']) {
                    continue;
                }
            }

            // See what phones are recorded by this rule (RecPhones)
            $RecPhones = array();
            $query = "
					SELECT FK_Extension	FROM RecordingRules_Extensions
					WHERE FK_Rule = {$Rule['PK_Rule']} AND NOT '{$Rule['Type']}' = 'Queue'
				UNION
					SELECT FK_Extension
					FROM RecordingRules_Groups
					INNER JOIN Extension_Groups ON Extension_Groups.FK_Group = RecordingRules_Groups.FK_Group
					WHERE FK_Rule = {$Rule['PK_Rule']}
			";
            $result_ids = $mysqli->query($query) or $this->agi->verbose($mysqli->error . $query);
            while ($id = $mysqli->fetch_assoc($result_ids)) {
                $RecPhones[] = $id['FK_Extension'];
            }

            // See what phones are recorded by this rule (RecQueues)
            $RecQueues = array();
            $query = "
				SELECT FK_Extension FROM RecordingRules_Extensions
					WHERE FK_Rule = {$Rule['PK_Rule']} AND '{$Rule['Type']}' = 'Queue'
			";
            $result_ids = $mysqli->query($query) or $this->agi->verbose($mysqli->error . $query);
            while ($id = $mysqli->fetch_assoc($result_ids)) {
                $RecQueues[] = $id['FK_Extension'];
            }


            if ($Rule['Call_Incoming'] == 1 && in_array($called_id, $RecPhones)) {
                $match['Rule'] = $Rule;
                $match['PK_Extension'] = $called_id;
                $match['Name'] = $this->called_name;
                $match['Number'] = $this->called_number;
                $ActiveRules[] = $match;
            }

            if ($Rule['Call_Outgoing'] == 1 && in_array($caller_id, $RecPhones)) {
                $match['Rule'] = $Rule;
                $match['PK_Extension'] = $caller_id;
                $match['Name'] = $this->caller_name;
                $match['Number'] = $this->caller_number;
                $ActiveRules[] = $match;
            }
        }

        return $ActiveRules;
    }

    function monitor_start() {
        // If we don't know one of the parties or the recording is already started, bail out
        if (!$this->caller_known() || !$this->called_known() || $this->monitoring_started) {
            return;
        }

        // See if we have any active rules for this call
        $ActiveRules = $this->monitor_getrules($this->caller_id, $this->called_id);
        if (count($ActiveRules) != 0) {
            // Set the monitoring state flag on true (monitoring_started)
            $this->monitoring_started = 1;
            $this->agi->set_variable('monitoring_started', $this->monitoring_started);

            // Start Monitoring
            $this->agi->exec("MixMonitor", array("/{$GLOBALS['config']['monitor_dir']}/{$this->cdr_id}.wav"));
        }
    }

    function monitor_stop() {
        global $mysqli;
        if ($this->monitoring_started) {

            // Set the monitoring state flag on false (monitoring_started)
            $this->monitoring_started = 0;
            $this->agi->set_variable('monitoring_started', $this->monitoring_started);

            // Stop Monitoring
            $this->agi->exec("StopMixMonitor");

            // See how long we where talking
            $query = "SELECT billsec FROM CDR WHERE userfield = '{$this->cdr_id}' LIMIT 1";
            $result = $mysqli->query($query) or $this->agi->verbose($mysqli->error . $query);
            $row = $result->fetch_row();
            $duration = $row[0] + 0;

            // Asume the call didn't is not valid for any of the active rules
            $ValidRule = array();

            // Get all active recording rules for this call
            $ActiveRule = $this->monitor_getrules($this->caller_id, $this->called_id, $this->call_start);
            foreach ($ActiveRule as $Active) {

                // Skip recording which are smaller than the minimum limit
                if ($duration < $Active['Rule']['MinLength'] || $duration == 0) {
                    continue;
                }

                // Skip rules which have riched the EndCount
                if (intval($Active['Rule']['EndCount']) > 0) {
                    $query = "SELECT COUNT(*) FROM RecordingLog WHERE FK_Rule = {$Active['Rule']['PK_Rule']}";
                    $result = $mysqli->query($query) or $this->agi->verbose($mysqli->error . $query);
                    $row = $result->fetch_row();

                    if ($row[0] >= $Active['Rule']['EndCount']) {
                        continue;
                    }
                }

                // Validate the recording
                $ValidRule = $Active['Rule'];

                // Insert entry into database
                $query = "
					INSERT INTO
						RecordingLog
					SET
						FK_CallLog   = '{$this->cdr_id}',
						FK_Rule      = '{$Active['Rule']['PK_Rule']}',
						Label        = '" . $mysqli->escape_string($Active['Rule']['Label']) . "',

						RecordedID     = '{$Active['PK_Extension']}',
						RecordedName   = '{$Active['Name']}',
						RecordedNumber = '{$Active['Number']}',

						StartDate    =  FROM_UNIXTIME({$this->call_start}),

						CallerID     = '{$this->caller_id}',
						CallerType   = '{$this->caller_type}',
						CallerName   = '{$this->caller_name}',
						CallerNumber = '{$this->caller_number}',

						CalledID     = '{$this->called_id}',
						CalledType   = '{$this->called_type}',
						CalledName   = '{$this->called_name}',
						CalledNumber = '{$this->called_number}',

						Duration     = '$duration',
						Size         = '" . filesize("/{$GLOBALS['config']['monitor_dir']}/{$this->cdr_id}.wav") . "'
				";
                $mysqli->query($query) or $this->agi->verbose($mysqli->error . $query);
            }
        }

        // If no active rule validated the recording(ex: call time was to short) than we delete the file from disk
        if (count($ValidRule) == 0) {
            @unlink("/{$GLOBALS['config']['monitor_dir']}/{$this->cdr_id}.wav");
        }
    }
}

?>
