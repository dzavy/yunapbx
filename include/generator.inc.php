<?php

function Get_SipProviders() {
    $db = DB::getInstance();
    $query = "
		SELECT
			SipProviders.*,
			DTMFModes.Name AS DTMFMode
		FROM
			SipProviders
			INNER JOIN DTMFModes ON FK_DTMFMode = PK_DTMFMode
		ORDER BY
			Name
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $SipProviders = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $provider = $row;

        // Get allowed codecs
        $codecs = array();
        $query_codecs = "
			SELECT
				Name
			FROM
				Codecs
				INNER JOIN SipProvider_Codecs ON PK_Codec = FK_Codec
			WHERE
				FK_SipProvider = {$provider['PK_SipProvider']}
		";
        $result_codecs = $db->query($query_codecs) or die(print_r($db->errorInfo(), true));
        while ($row_codec = $result_codecs->fetch(PDO::FETCH_ASSOC)) {
            $codecs[] = $row_codec['Name'];
        }
        $provider['Codecs'] = implode(',', $codecs);

        // Get hosts list
        $provider['Hosts'][] = $provider['Host'];

        $provider['OugoingRules'] = array();
        $query_outrules = "
			SELECT
				OutgoingRules.*
			FROM
				SipProvider_Rules JOIN OutgoingRules ON (PK_OutgoingRule = FK_OutgoingRule)
			WHERE
				FK_SipProvider = {$provider['PK_SipProvider']} ORDER BY RuleOrder
		";
        $result_outrules = $db->query($query_outrules) or die(print_r($db->errorInfo(), true));
        while ($row_outrule = $result_outrules->fetch(PDO::FETCH_ASSOC)) {
            $provider['OutgoingRules'][] = $row_outrule;
        }
 
        $SipProviders[] = $provider;
    }

    return $SipProviders;
}

function Get_Dongles() {
    $db = DB::getInstance();
    $query = "
		SELECT
			Dongles.*
		FROM
			Dongles
		ORDER BY
			Name
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $Dongles = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $dongle = $row;
        
        
        $dongle['Rules'] = array();
        $query_rules = "
			SELECT
				OutgoingRules.*
			FROM
				Dongle_Rules JOIN OutgoingRules ON (PK_OutgoingRule = FK_OutgoingRule)
			WHERE
				FK_Dongle = {$dongle['PK_Dongle']} ORDER BY RuleOrder
		";
        $result_rules = $db->query($query_rules) or die(print_r($db->errorInfo(), true));
        while ($row_rule = $result_rules->fetch(PDO::FETCH_ASSOC)) {
            $dongle['Rules'][] = $row_rule;
        }
        
        $Dongles[] = $dongle;
    }

    return $Dongles;
}

function Get_OutgoingRules() {
    $db = DB::getInstance();
    $query = "
		SELECT
			*
		FROM
			OutgoingRules
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $OutgoingRules = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $OutgoingRules[] = $row;
    }

    return $OutgoingRules;
}

function Get_Ext_SipPhones() {
    $db = DB::getInstance();
    $query = "
		SELECT
			Ext_SipPhones.PK_Extension AS PK_Extension,
			Extension,
			Extensions.Name,
			Password,
			Email,
			PhonePassword,
			NATTypes.Name AS NATType,
			DTMFModes.Name AS DTMFMode
		FROM
			Ext_SipPhones
			INNER JOIN Extensions ON Ext_SipPhones.PK_Extension = Extensions.PK_Extension
			INNER JOIN NATTypes  ON FK_NATType  = PK_NATType
			INNER JOIN DTMFModes ON FK_DTMFMode = PK_DTMFMode
		ORDER BY
			Extension
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $Extensions = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $exten = $row;

        // Get allowed codecs
        $codecs = array();
        $query_codecs = "
			SELECT
				Name
			FROM
				Codecs
				INNER JOIN Ext_SipPhones_Codecs ON PK_Codec = FK_Codec
			WHERE
				FK_Extension = {$exten['PK_Extension']}
		";
        $result_codecs = $db->query($query_codecs) or die(print_r($db->errorInfo(), true));
        while ($row_codec = $result_codecs->fetch(PDO::FETCH_ASSOC)) {
            $codecs[] = $row_codec['Name'];
        }
        $exten['Codecs'] = implode(',', $codecs);

        // Get available features
        $exten['Features'] = array();
        $query_features = "
			SELECT
				ShortName
			FROM
				Features
				INNER JOIN Ext_SipPhones_Features ON PK_Feature = FK_Feature
			WHERE
				FK_Extension = {$exten['PK_Extension']}
		";
        $result_features = $db->query($query_features) or die(print_r($db->errorInfo(), true));
        while ($row_feature = $result_features->fetch(PDO::FETCH_ASSOC)) {
            $exten['Features'][] = $row_feature['ShortName'];
        }

        $exten['Rules'] = array();
        $query_rules = "
			SELECT
				OutgoingRules.*
			FROM
				Extension_Rules JOIN OutgoingRules ON (PK_OutgoingRule = FK_OutgoingRule)
			WHERE
				FK_Extension = {$exten['PK_Extension']} ORDER BY RuleOrder
		";
        $result_rules = $db->query($query_rules) or die(print_r($db->errorInfo(), true));
        while ($row_rule = $result_rules->fetch(PDO::FETCH_ASSOC)) {
            $exten['Rules'][] = $row_rule;
        }
        
        $Extensions[] = $exten;
    }

//            var_dump($Extensions);
//        die();

    return $Extensions;
}

function Get_Ext_Queues() {
    $db = DB::getInstance();
    $query = "
		SELECT
			Extensions.PK_Extension,
            Extension,
			Ext_Queues.*,
			RingStrategies.Name AS RingStrategy,
			A.Filename          AS Sound_PickupAnnouncement,
			B.Filename          AS Sound_YouAreNext,
			C.Filename          AS Sound_ThereAre,
			D.Filename          AS Sound_CallsWaiting,
			E.Filename          AS Sound_HoldTime,
			F.Filename          AS Sound_Minutes,
			G.Filename          AS Sound_ThankYou
		FROM
			Ext_Queues
			INNER JOIN Extensions      ON Ext_Queues.PK_Extension = Extensions.PK_Extension
			INNER JOIN RingStrategies  ON FK_RingStrategy   = PK_RingStrategy
			LEFT JOIN SoundFiles     A ON A.PK_SoundFile    = FK_Sound_PickupAnnouncement
			LEFT JOIN SoundFiles     B ON B.PK_SoundFile    = FK_Sound_YouAreNext
			LEFT JOIN SoundFiles     C ON C.PK_SoundFile    = FK_Sound_ThereAre
			LEFT JOIN SoundFiles     D ON D.PK_SoundFile    = FK_Sound_CallsWaiting
			LEFT JOIN SoundFiles     E ON E.PK_SoundFile    = FK_Sound_HoldTime
			LEFT JOIN SoundFiles     F ON F.PK_SoundFile    = FK_Sound_Minutes
			LEFT JOIN SoundFiles     G ON G.PK_SoundFile    = FK_Sound_ThankYou
		ORDER BY
			Extension
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    $Queues = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $queue = $row;

        $query = "
			SELECT
				Ext_Queue_Members.*,
				Extensions.*
			FROM
				Ext_Queue_Members
				JOIN Extensions ON FK_Extension_Member = PK_Extension
			WHERE
				FK_Extension = {$queue['PK_Extension']}
		";
        $result = $db->query($query) or die(print_r($db->errorInfo(), true));
        while ($member = $result->fetch(PDO::FETCH_ASSOC)) {
            $queue['Members'][] = $member;
        }

        foreach (array_keys($queue) as $field) {
            if (preg_match('/^Sound_.*/', $field)) {
                $queue[$field] = preg_replace('/^(.*)\.[^.]*$/', '$1', $queue[$field]);
            }
        }

        $Queues[] = $queue;
    }
    return $Queues;
}

function Get_Ext_ConfCenter_Rooms() {
    $db = DB::getInstance();
    $query = "
		SELECT
			*
		FROM
			Ext_ConfCenter_Rooms
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $Rooms[] = $row;
    }

    return $Rooms;
}

function Get_Moh_Groups() {
    $db = DB::getInstance();
    global $conf;
    
    $query = "
		SELECT
			*
		FROM
			Moh_Groups
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    	$row['Folder'] = $conf['dirs']['moh'] . "/group_" . str_pad($row['PK_Group'], 10, '0', STR_PAD_LEFT);
        if ($row['Volume']>=100) {
            $row['Gain'] = (($row['Volume']-100)*18)/100;
        } else {
            $row['Gain'] = ($row['Volume']*50)/100 - 50;
        }

        $Groups[] = $row;
    }

    return $Groups;
}

function Get_Settings() {
    $db = DB::getInstance();
    $query = "
		SELECT
			*
		FROM
			Settings
	";
    $result = $db->query($query) or die(print_r($db->errorInfo(), true));

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        if (in_array($row['Name'], array('Network_Additional_LAN', 'Network_Interfaces_LAN'))) {
            $Settings[$row['Name']] = explode(';', $row['Value']);
        } else {
            $Settings[$row['Name']] = $row['Value'];
        }
    }

    return $Settings;
}

?>