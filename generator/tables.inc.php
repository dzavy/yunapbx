<?php

function Get_SipProviders() {
    global $mysqli;
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
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $SipProviders = array();
    while ($row = $result->fetch_assoc()) {
        $provider = $row;

        // Get allowed codecs
        $codecs = array();
        $query_codec = "
			SELECT
				Name
			FROM
				Codecs
				INNER JOIN SipProvider_Codecs ON PK_Codec = FK_Codec
			WHERE
				FK_SipProvider = {$provider['PK_SipProvider']}
		";
        $result_codec = $mysqli->query($query_codec) or die($mysqli->error . $query_codec);
        while ($row_codec = $result_codec->fetch_assoc()) {
            $codecs[] = $row_codec['Name'];
        }
        $provider['Codecs'] = implode(',', $codecs);

        // Get hosts list
        $provider['Hosts'][] = $provider['Host'];
        $hosts = array();
        $query_hosts = "
			SELECT
				Host
			FROM
				SipProvider_Hosts
			WHERE
				FK_SipProvider = {$provider['PK_SipProvider']}
		";
        $result_hosts = $mysqli->query($query_hosts) or die($mysqli->error . $query_hosts);
        while ($row_hosts = $result_hosts->fetch_assoc()) {
            $provider['Hosts'][] = $row_hosts['Host'];
        }

        $provider['OugoingRules'] = array();
        $query_outrules = "
			SELECT
				OutgoingRules.*
			FROM
				SipProvider_Rules JOIN OutgoingRules ON (PK_OutgoingRule = FK_OutgoingRule)
			WHERE
				FK_SipProvider = {$provider['PK_SipProvider']} ORDER BY RuleOrder
		";
        $result_outrules = $mysqli->query($query_outrules) or die($mysqli->error . $query_outrules);
        while ($row_outrule = $result_outrules->fetch_assoc()) {
            $provider['OutgoingRules'][] = $row_outrule;
        }
 
        $SipProviders[] = $provider;
    }

    return $SipProviders;
}

function Get_Dongles() {
    global $mysqli;
    $query = "
		SELECT
			Dongles.*
		FROM
			Dongles
		ORDER BY
			Name
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $Dongles = array();
    while ($row = $result->fetch_assoc()) {
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
        $result_rules = $mysqli->query($query_rules) or die($mysqli->error . $query_rules);
        while ($row_rule = $result_rules->fetch_assoc()) {
            $dongle['Rules'][] = $row_rule;
        }
        
        $Dongles[] = $dongle;
    }

    return $Dongles;
}

function Get_OutgoingRules() {
    global $mysqli;
    $query = "
		SELECT
			*
		FROM
			OutgoingRules
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $OutgoingRules = array();
    while ($row = $result->fetch_assoc()) {
        $OutgoingRules[] = $row;
    }

    return $OutgoingRules;
}

function Get_Ext_SipPhones() {
    global $mysqli;
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
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $Extensions = array();
    while ($row = $result->fetch_assoc()) {
        $exten = $row;

        // Get allowed codecs
        $codecs = array();
        $query_codec = "
			SELECT
				Name
			FROM
				Codecs
				INNER JOIN Ext_SipPhones_Codecs ON PK_Codec = FK_Codec
			WHERE
				FK_Extension = {$exten['PK_Extension']}
		";
        $result_codec = $mysqli->query($query_codec) or die($mysqli->error . $query_codec);
        while ($row_codec = $result_codec->fetch_assoc()) {
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
        $result_features = $mysqli->query($query_features) or die($mysqli->error . $query_features);
        while ($row_feature = $result_features->fetch_assoc()) {
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
        $result_rules = $mysqli->query($query_rules) or die($mysqli->error . $query_rules);
        while ($row_rule = $result_rules->fetch_assoc()) {
            $exten['Rules'][] = $row_rule;
        }
        
        $Extensions[] = $exten;
    }

//            var_dump($Extensions);
//        die();

    return $Extensions;
}

function Get_Ext_Queues() {
    global $mysqli;
    $query = "
		SELECT
			Extensions.PK_Extension,
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
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    $Queues = array();
    while ($row = $result->fetch_assoc()) {
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
        $result = $mysqli->query($query) or die($mysqli->error . $query);
        while ($member = $result->fetch_assoc()) {
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
    global $mysqli;
    $query = "
		SELECT
			*
		FROM
			Ext_ConfCenter_Rooms
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    while ($row = $result->fetch_assoc()) {
        $Rooms[] = $row;
    }

    return $Rooms;
}

function Get_Moh_Groups() {
    global $mysqli;
    $query = "
		SELECT
			*
		FROM
			Moh_Groups
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    while ($row = $result->fetch_assoc()) {
        $Groups[] = $row;
    }

    return $Groups;
}

function Get_Settings() {
    global $mysqli;
    $query = "
		SELECT
			*
		FROM
			Settings
	";
    $result = $mysqli->query($query) or die($mysqli->error . $query);

    while ($row = $result->fetch_assoc()) {
        if (in_array($row['Name'], array('Network_Additional_LAN', 'Network_Interfaces_LAN'))) {
            $Settings[$row['Name']] = explode(';', $row['Value']);
        } else {
            $Settings[$row['Name']] = $row['Value'];
        }
    }

    return $Settings;
}

?>