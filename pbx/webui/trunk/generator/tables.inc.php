<?php
function Get_SipProviders() {
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
	$result = mysql_query($query) or die(mysql_error().$query);

	$SipProviders = array();
	while ($row = mysql_fetch_assoc($result)) {
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
		$result_codec = mysql_query($query_codec) or die(mysql_error().$query_codec);
		while ($row_codec = mysql_fetch_assoc($result_codec)) {
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
		$result_hosts = mysql_query($query_hosts) or die(mysql_error().$query_hosts);
		while ($row_hosts = mysql_fetch_assoc($result_hosts)) {
			$provider['Hosts'][] = $row_hosts['Host'];
		}

		$SipProviders[] = $provider;
	}

	return $SipProviders;
}

function Get_IaxProviders() {
	$query = "
		SELECT
			IaxProviders.*
		FROM
			IaxProviders
		ORDER BY
			Name
	";
	$result = mysql_query($query) or die(mysql_error().$query);

	$IaxProviders = array();
	while ($row = mysql_fetch_assoc($result)) {
		$provider = $row;

		// Get allowed codecs
		$codecs = array();
		$query_codec = "
			SELECT
				Name
			FROM
				Codecs
				INNER JOIN IaxProvider_Codecs ON PK_Codec = FK_Codec
			WHERE
				FK_IaxProvider = {$provider['PK_IaxProvider']}
		";
		$result_codec = mysql_query($query_codec) or die(mysql_error().$query_codec);
		while ($row_codec = mysql_fetch_assoc($result_codec)) {
			$codecs[] = $row_codec['Name'];
		}
		$provider['Codecs'] = implode(',', $codecs);

		$provider['OutHosts'] = array();
		if ($provider['PrOutbHost']   != "") { $provider['OutHosts'][] = $provider['PrOutbHost'];   }
		if ($provider['SecOutbHost']  != "") { $provider['OutHosts'][] = $provider['SecOutbHost'];  }
		if ($provider['TertOutbHost'] != "") { $provider['OutHosts'][] = $provider['TertOutbHost']; }

		$IaxProviders[] = $provider;
	}

	return $IaxProviders;
}

function Get_Ext_SipPhones() {
	$query = "
		SELECT
			Ext_SipPhones.PK_Extension AS PK_Extension,
			Extension,
			FirstName,
			LastName,
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
	$result = mysql_query($query) or die(mysql_error().$query);

	$Extensions = array();
	while ($row = mysql_fetch_assoc($result)) {
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
		$result_codec = mysql_query($query_codec) or die(mysql_error().$query_codec);
		while ($row_codec = mysql_fetch_assoc($result_codec)) {
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
		$result_features = mysql_query($query_features) or die(mysql_error().$query_codec);
		while ($row_feature = mysql_fetch_assoc($result_features)) {
			$exten['Features'][] = $row_feature['ShortName'];
		}

		$Extensions[] = $exten;
	}

	return $Extensions;
}

function Get_Ext_Queues() {
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
	$result = mysql_query($query) or die(mysql_error().$query);

	$Queues = array();
	while ($row = mysql_fetch_assoc($result)) {
		$queue = $row;

		$query  = "
			SELECT
				Ext_Queue_Members.*,
				Extensions.*
			FROM
				Ext_Queue_Members
				JOIN Extensions ON FK_Extension_Member = PK_Extension
			WHERE
				FK_Extension = {$queue['PK_Extension']}
		";
		$result = mysql_query($query) or die(mysql_error().$query);
		while ($member = mysql_fetch_assoc($result)) {
			$queue['Members'][] = $member;
		}

		foreach (array_keys($queue) as $field) {
			if (preg_match('/^Sound_.*/', $field)) {
				$queue[$field] = preg_replace('/^(.*)\.[^.]*$/','$1', $queue[$field]);
			}
		}

		$Queues[] = $queue;
	}
	return $Queues;
}

function Get_Ext_Parking() {
	$query = "
		SELECT
			Extensions.Extension,
			Extensions.PK_Extension,
			Ext_ParkingLot.*
		FROM
			Ext_ParkingLot
			INNER JOIN Extensions ON Ext_ParkingLot.PK_Extension = Extensions.PK_Extension
		ORDER BY
			Extension
	";
	$result = mysql_query($query) or die(mysql_error().$query);

	$Parking = mysql_fetch_assoc($result);

	return $Parking;
}

function Get_Ext_ConfCenter_Rooms() {
	$query = "
		SELECT
			*
		FROM
			Ext_ConfCenter_Rooms
	";
	$result = mysql_query($query) or die(mysql_error().$query);

	while ($row = mysql_fetch_assoc($result)) {
		$Rooms[] = $row;
	}

	return $Rooms;
}

function Get_Moh_Groups() {
	$query = "
		SELECT
			*
		FROM
			Moh_Groups
	";
	$result = mysql_query($query) or die(mysql_error().$query);

	while ($row = mysql_fetch_assoc($result)) {
		$Groups[] = $row;
	}

	return $Groups;
}

function Get_Settings() {
	$query = "
		SELECT
			*
		FROM
			Settings
	";
	$result = mysql_query($query) or die(mysql_error().$query);

	while ($row = mysql_fetch_assoc($result)) {
		if (in_array($row['Name'],array('Network_Additional_LAN', 'Network_Interfaces_LAN'))) {
			$Settings[$row['Name']] = explode(';', $row['Value']);
		} else {
			$Settings[$row['Name']] = $row['Value'];
		}
	}

	return $Settings;
}
?>