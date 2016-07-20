/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


/*Table structure for table `Admins` */

DROP TABLE IF EXISTS `Admins`;

CREATE TABLE `Admins` (
  `PK_User` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`PK_User`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Backups` */

DROP TABLE IF EXISTS `Backups`;

CREATE TABLE `Backups` (
  `PK_Backup` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `Optionals` varchar(100) NOT NULL,
  `Size` varchar(5) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PK_Backup`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `CDR` */

DROP TABLE IF EXISTS `CDR`;

CREATE TABLE `CDR` (
  `calldate` datetime NOT NULL,
  `clid` varchar(80) NOT NULL DEFAULT '',
  `src` varchar(80) NOT NULL DEFAULT '',
  `dst` varchar(80) NOT NULL DEFAULT '',
  `dcontext` varchar(80) NOT NULL DEFAULT '',
  `channel` varchar(80) NOT NULL DEFAULT '',
  `dstchannel` varchar(80) NOT NULL DEFAULT '',
  `lastapp` varchar(80) NOT NULL DEFAULT '',
  `lastdata` varchar(80) NOT NULL DEFAULT '',
  `duration` int(11) NOT NULL DEFAULT '0',
  `billsec` int(11) NOT NULL DEFAULT '0',
  `disposition` varchar(45) NOT NULL DEFAULT '',
  `amaflags` int(11) NOT NULL DEFAULT '0',
  `accountcode` varchar(20) NOT NULL DEFAULT '',
  `userfield` varchar(255) NOT NULL DEFAULT '',
  `uniqueid` varchar(32) NOT NULL DEFAULT '',
  KEY `dst` (`dst`),
  KEY `accountcode` (`accountcode`),
  KEY `calldate` (`calldate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `CEL` */

DROP TABLE IF EXISTS `CEL`;

CREATE TABLE `CEL` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `eventtype` varchar(30) NOT NULL,
  `eventtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userdeftype` varchar(255) NOT NULL,
  `cid_name` varchar(80) NOT NULL,
  `cid_num` varchar(80) NOT NULL,
  `cid_ani` varchar(80) NOT NULL,
  `cid_rdnis` varchar(80) NOT NULL,
  `cid_dnid` varchar(80) NOT NULL,
  `exten` varchar(80) NOT NULL,
  `context` varchar(80) NOT NULL,
  `channame` varchar(80) NOT NULL,
  `appname` varchar(80) NOT NULL,
  `appdata` varchar(80) NOT NULL,
  `amaflags` int(11) NOT NULL,
  `accountcode` varchar(20) NOT NULL,
  `peeraccount` varchar(20) NOT NULL,
  `uniqueid` varchar(150) NOT NULL,
  `linkedid` varchar(150) NOT NULL,
  `userfield` varchar(255) NOT NULL,
  `peer` varchar(80) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `CallLog` */

DROP TABLE IF EXISTS `CallLog`;

CREATE TABLE `CallLog` (
  `PK_CallLog` varchar(50) NOT NULL,
  `FK_CallLog_Parent` varchar(50) NOT NULL,
  `CallType` enum('LOCAL','OUT','IN') NOT NULL,
  `StartDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `CallerID` int(11) NOT NULL,
  `CallerType` varchar(50) NOT NULL,
  `CallerName` varchar(100) NOT NULL,
  `CallerNumber` varchar(100) NOT NULL,
  `CalledID` int(10) unsigned NOT NULL,
  `CalledType` varchar(50) NOT NULL,
  `CalledName` varchar(100) NOT NULL,
  `CalledNumber` varchar(100) NOT NULL,
  `BillSec` int(10) unsigned NOT NULL,
  `Duration` int(11) NOT NULL,
  PRIMARY KEY (`PK_CallLog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `CallLog_Details` */

DROP TABLE IF EXISTS `CallLog_Details`;

CREATE TABLE `CallLog_Details` (
  `PK_Details` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FK_CallLog` varchar(50) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Event` varchar(15) NOT NULL,
  `Data` varchar(255) NOT NULL,
  PRIMARY KEY (`PK_Details`),
  KEY `FK_CallLog` (`FK_CallLog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Codecs` */

DROP TABLE IF EXISTS `Codecs`;

CREATE TABLE `Codecs` (
  `PK_Codec` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(10) NOT NULL,
  `Description` varchar(100) NOT NULL,
  `Recomended` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PK_Codec`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `DTMFModes` */

DROP TABLE IF EXISTS `DTMFModes`;

CREATE TABLE `DTMFModes` (
  `PK_DTMFMode` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(20) NOT NULL,
  `Description` varchar(100) NOT NULL,
  PRIMARY KEY (`PK_DTMFMode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Dongle_Rules` */

DROP TABLE IF EXISTS `Dongle_Rules`;

CREATE TABLE `Dongle_Rules` (
  `FK_Dongle` int(10) unsigned NOT NULL,
  `FK_OutgoingRule` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Dongle`,`FK_OutgoingRule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `Dongle_Status` */

DROP TABLE IF EXISTS `Dongle_Status`;

CREATE TABLE `Dongle_Status` (
  `FK_Dongle` int(10) unsigned NOT NULL,
  `RSSI` int(10) NOT NULL,
  `Status` varchar(50) NOT NULL,
  `Provider` varchar(50) DEFAULT NULL,
  `Mode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`FK_Dongle`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

/*Table structure for table `Dongles` */

DROP TABLE IF EXISTS `Dongles`;

CREATE TABLE `Dongles` (
  `PK_Dongle` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) NOT NULL,
  `IMEI` varchar(32) NOT NULL,
  `IMSI` varchar(32) NOT NULL,
  `MSISDN` varchar(32) NOT NULL,
  `CallbackExtension` varchar(15) DEFAULT NULL,
  `ApplyIncomingRules` tinyint(1) DEFAULT '1',
  `EnableSMS` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`PK_Dongle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `EmergencyNumbers` */

DROP TABLE IF EXISTS `EmergencyNumbers`;

CREATE TABLE `EmergencyNumbers` (
  `PK_EmergencyNumber` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `EmergencyNumber` varchar(20) DEFAULT NULL,
  `Enabled` tinyint(1) DEFAULT '0',
  `Protected` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`PK_EmergencyNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_Agent` */

DROP TABLE IF EXISTS `Ext_Agent`;

CREATE TABLE `Ext_Agent` (
  `PK_Extension` int(10) unsigned NOT NULL,
  `Name_Editable` tinyint(1) NOT NULL,
  `Password` varchar(10) NOT NULL,
  `Password_Editable` tinyint(1) NOT NULL,
  `PhonePassword` varchar(50) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Email_Editable` tinyint(1) NOT NULL,
  PRIMARY KEY (`PK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_AgentLogin` */

DROP TABLE IF EXISTS `Ext_AgentLogin`;

CREATE TABLE `Ext_AgentLogin` (
  `PK_Extension` int(10) unsigned NOT NULL,
  `RequirePassword` tinyint(1) NOT NULL DEFAULT '1',
  `LoginToggle` tinyint(1) NOT NULL DEFAULT '0',
  `EnterExtension` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_Agent_Features` */

DROP TABLE IF EXISTS `Ext_Agent_Features`;

CREATE TABLE `Ext_Agent_Features` (
  `FK_Extension` int(10) unsigned NOT NULL,
  `FK_Feature` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`,`FK_Feature`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_ConfCenter` */

DROP TABLE IF EXISTS `Ext_ConfCenter`;

CREATE TABLE `Ext_ConfCenter` (
  `PK_Extension` int(255) unsigned NOT NULL,
  `Invalid` int(1) NOT NULL,
  `TransferExt` int(5) NOT NULL,
  PRIMARY KEY (`PK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_ConfCenter_Admins` */

DROP TABLE IF EXISTS `Ext_ConfCenter_Admins`;

CREATE TABLE `Ext_ConfCenter_Admins` (
  `FK_Room` int(10) unsigned NOT NULL,
  `FK_Extension` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Room`,`FK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_ConfCenter_Rooms` */

DROP TABLE IF EXISTS `Ext_ConfCenter_Rooms`;

CREATE TABLE `Ext_ConfCenter_Rooms` (
  `PK_Room` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FK_Extension_Owner` int(10) unsigned NOT NULL,
  `Number` char(5) NOT NULL,
  `PlayEnterSound` tinyint(1) NOT NULL,
  `PlayMOH` tinyint(1) NOT NULL,
  `Operator` varchar(10) NOT NULL,
  `OnlyAdminTalk` tinyint(1) NOT NULL,
  `HangupIfNoAdmin` tinyint(1) NOT NULL,
  `NoTalkTillAdmin` tinyint(1) NOT NULL,
  PRIMARY KEY (`PK_Room`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_DialTone` */

DROP TABLE IF EXISTS `Ext_DialTone`;

CREATE TABLE `Ext_DialTone` (
  `PK_Extension` int(10) unsigned NOT NULL,
  `Password` varchar(10) NOT NULL,
  PRIMARY KEY (`PK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_GroupPickup` */

DROP TABLE IF EXISTS `Ext_GroupPickup`;

CREATE TABLE `Ext_GroupPickup` (
  `PK_Extension` int(10) unsigned NOT NULL,
  `Use_Members_ByAccount` tinyint(1) NOT NULL,
  `Use_Admins_ByAccount` tinyint(1) NOT NULL,
  PRIMARY KEY (`PK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_GroupPickup_Admins` */

DROP TABLE IF EXISTS `Ext_GroupPickup_Admins`;

CREATE TABLE `Ext_GroupPickup_Admins` (
  `FK_Extension` int(255) unsigned NOT NULL,
  `FK_Ext_Admin` int(255) unsigned NOT NULL,
  `FK_Ext_Group` int(255) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`,`FK_Ext_Admin`,`FK_Ext_Group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_GroupPickup_Members` */

DROP TABLE IF EXISTS `Ext_GroupPickup_Members`;

CREATE TABLE `Ext_GroupPickup_Members` (
  `FK_Extension` int(255) unsigned NOT NULL,
  `FK_Ext_Member` int(255) unsigned NOT NULL,
  `FK_Ext_Group` int(255) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`,`FK_Ext_Member`,`FK_Ext_Group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_IVR` */

DROP TABLE IF EXISTS `Ext_IVR`;

CREATE TABLE `Ext_IVR` (
  `PK_Extension` int(10) unsigned NOT NULL,
  `FK_Menu` int(10) unsigned NOT NULL,
  `FK_Action` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_Queue_Members` */

DROP TABLE IF EXISTS `Ext_Queue_Members`;

CREATE TABLE `Ext_Queue_Members` (
  `FK_Extension` int(10) unsigned NOT NULL,
  `FK_Extension_Member` int(10) unsigned NOT NULL,
  `LoginRequired` tinyint(1) NOT NULL,
  `QueueOrder` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`,`FK_Extension_Member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_Queue_Members_Status` */

DROP TABLE IF EXISTS `Ext_Queue_Members_Status`;

CREATE TABLE `Ext_Queue_Members_Status` (
  `FK_Extension` int(10) unsigned NOT NULL,
  `From` varchar(255) NOT NULL,
  `LoginDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`FK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_Queues` */

DROP TABLE IF EXISTS `Ext_Queues`;

CREATE TABLE `Ext_Queues` (
  `PK_Extension` int(10) unsigned NOT NULL,
  `FK_RingStrategy` int(10) unsigned NOT NULL,
  `RingInUse` enum('yes','no') NOT NULL DEFAULT 'no',
  `FK_MohGroup` int(10) unsigned NOT NULL,
  `PlayMohInQueue` tinyint(1) NOT NULL DEFAULT '1',
  `MemberRingTime` varchar(5) NOT NULL,
  `NextWaitTime` varchar(5) NOT NULL,
  `WrapUpTime` varchar(5) NOT NULL,
  `AnnounceFrequency` int(11) NOT NULL DEFAULT '0',
  `AnnouncePosition` enum('yes','no') NOT NULL DEFAULT 'yes',
  `AnnounceHoldtime` enum('yes','no') NOT NULL DEFAULT 'no',
  `Timeout` int(11) NOT NULL DEFAULT '0',
  `TimeoutExtension` varchar(20) NOT NULL,
  `JoinEmptyExtension` varchar(20) NOT NULL,
  `MaxLen` int(11) NOT NULL DEFAULT '0',
  `MaxLenExtension` varchar(20) NOT NULL,
  `Cycles` int(10) unsigned NOT NULL DEFAULT '0',
  `CyclesExtension` varchar(20) NOT NULL,
  `OperatorExtension` varchar(20) NOT NULL,
  `FK_Sound_PickupAnnouncement` int(10) unsigned NOT NULL,
  `FK_Sound_YouAreNext` int(10) unsigned NOT NULL,
  `FK_Sound_ThereAre` int(10) unsigned NOT NULL,
  `FK_Sound_CallsWaiting` int(10) unsigned NOT NULL,
  `FK_Sound_HoldTime` int(10) unsigned NOT NULL,
  `FK_Sound_Minutes` int(10) unsigned NOT NULL,
  `FK_Sound_ThankYou` int(10) unsigned NOT NULL,
  `AckCall` tinyint(4) NOT NULL,
  `MissedCallsAllowed` varchar(30) NOT NULL,
  PRIMARY KEY (`PK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_SipPhones` */

DROP TABLE IF EXISTS `Ext_SipPhones`;

CREATE TABLE `Ext_SipPhones` (
  `PK_Extension` int(10) unsigned NOT NULL,
  `Name_Editable` tinyint(1) NOT NULL,
  `Password` varchar(10) NOT NULL,
  `Password_Editable` tinyint(1) NOT NULL,
  `PhonePassword` varchar(50) NOT NULL,
  `FK_NATType` int(10) unsigned NOT NULL,
  `FK_DTMFMode` int(10) unsigned NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Email_Editable` tinyint(1) NOT NULL,
  PRIMARY KEY (`PK_Extension`),
  KEY `FK_NATType` (`FK_NATType`),
  KEY `FK_DTMFMode` (`FK_DTMFMode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_SipPhones_Codecs` */

DROP TABLE IF EXISTS `Ext_SipPhones_Codecs`;

CREATE TABLE `Ext_SipPhones_Codecs` (
  `FK_Extension` int(10) unsigned NOT NULL,
  `FK_Codec` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`,`FK_Codec`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_SipPhones_Features` */

DROP TABLE IF EXISTS `Ext_SipPhones_Features`;

CREATE TABLE `Ext_SipPhones_Features` (
  `FK_Extension` int(10) unsigned NOT NULL,
  `FK_Feature` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`,`FK_Feature`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_SipPhones_Status` */

DROP TABLE IF EXISTS `Ext_SipPhones_Status`;

CREATE TABLE `Ext_SipPhones_Status` (
  `Extension` varchar(10) NOT NULL,
  `UserAgent` varchar(255) NOT NULL,
  `IPAddress` varchar(50) NOT NULL,
  `Status` varchar(50) NOT NULL,
  PRIMARY KEY (`Extension`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_Virtual` */

DROP TABLE IF EXISTS `Ext_Virtual`;

CREATE TABLE `Ext_Virtual` (
  `PK_Extension` int(10) unsigned NOT NULL,
  `IsInternal` tinyint(1) NOT NULL DEFAULT '1',
  `TargetExtension` varchar(10) DEFAULT NULL,
  `TargetNumber` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`PK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Ext_Voicemail` */

DROP TABLE IF EXISTS `Ext_Voicemail`;

CREATE TABLE `Ext_Voicemail` (
  `PK_Extension` int(10) unsigned NOT NULL,
  `RequirePassword` tinyint(1) NOT NULL,
  PRIMARY KEY (`PK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Extension_Groups` */

DROP TABLE IF EXISTS `Extension_Groups`;

CREATE TABLE `Extension_Groups` (
  `FK_Extension` int(10) unsigned NOT NULL,
  `FK_Group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`,`FK_Group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Extension_Rules` */

DROP TABLE IF EXISTS `Extension_Rules`;

CREATE TABLE `Extension_Rules` (
  `FK_Extension` int(10) unsigned NOT NULL,
  `FK_OutgoingRule` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`,`FK_OutgoingRule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Extensions` */

DROP TABLE IF EXISTS `Extensions`;

CREATE TABLE `Extensions` (
  `PK_Extension` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Extension` varchar(10) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Type` varchar(20) NOT NULL,
  `Feature` tinyint(1) NOT NULL DEFAULT '0',
  `IVRDial` tinyint(1) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PK_Extension`),
  UNIQUE KEY `ExtensionNumber` (`Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `FC_CallMonitor` */

DROP TABLE IF EXISTS `FC_CallMonitor`;

CREATE TABLE `FC_CallMonitor` (
  `FK_Extension` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `FC_CallMonitor_Admins` */

DROP TABLE IF EXISTS `FC_CallMonitor_Admins`;

CREATE TABLE `FC_CallMonitor_Admins` (
  `FK_Extension` int(255) unsigned NOT NULL,
  `ConnectionID` char(13) NOT NULL,
  `FK_Ext_Admin` int(255) unsigned NOT NULL,
  `FK_Ext_Group` int(255) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `FC_CallMonitor_Members` */

DROP TABLE IF EXISTS `FC_CallMonitor_Members`;

CREATE TABLE `FC_CallMonitor_Members` (
  `FK_Extension` int(255) unsigned NOT NULL,
  `ConnectionID` char(13) NOT NULL,
  `FK_Ext_Member` int(255) unsigned NOT NULL,
  `FK_Ext_Group` int(255) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `FC_DirectedPickup` */

DROP TABLE IF EXISTS `FC_DirectedPickup`;

CREATE TABLE `FC_DirectedPickup` (
  `FK_Extension` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `FC_DirectedPickup_Admins` */

DROP TABLE IF EXISTS `FC_DirectedPickup_Admins`;

CREATE TABLE `FC_DirectedPickup_Admins` (
  `FK_Extension` int(255) unsigned NOT NULL,
  `ConnectionID` char(13) NOT NULL,
  `FK_Ext_Admin` int(255) unsigned NOT NULL,
  `FK_Ext_Group` int(255) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `FC_DirectedPickup_Members` */

DROP TABLE IF EXISTS `FC_DirectedPickup_Members`;

CREATE TABLE `FC_DirectedPickup_Members` (
  `FK_Extension` int(255) unsigned NOT NULL,
  `ConnectionID` char(13) NOT NULL,
  `FK_Ext_Member` int(255) unsigned NOT NULL,
  `FK_Ext_Group` int(255) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `FC_Voicemail` */

DROP TABLE IF EXISTS `FC_Voicemail`;

CREATE TABLE `FC_Voicemail` (
  `FK_Extension` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Features` */

DROP TABLE IF EXISTS `Features`;

CREATE TABLE `Features` (
  `PK_Feature` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `ShortName` varchar(50) NOT NULL,
  `Recomended` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PK_Feature`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Groups` */

DROP TABLE IF EXISTS `Groups`;

CREATE TABLE `Groups` (
  `PK_Group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PK_Group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `IVR_Action_Params` */

DROP TABLE IF EXISTS `IVR_Action_Params`;

CREATE TABLE `IVR_Action_Params` (
  `PK_Param` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FK_Action` int(10) unsigned NOT NULL,
  `Name` varchar(30) NOT NULL,
  `Value` varchar(255) NOT NULL,
  `Variable` varchar(50) NOT NULL,
  PRIMARY KEY (`PK_Param`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `IVR_Actions` */

DROP TABLE IF EXISTS `IVR_Actions`;

CREATE TABLE `IVR_Actions` (
  `PK_Action` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FK_Menu` int(10) unsigned NOT NULL,
  `Order` int(10) unsigned NOT NULL,
  `Type` varchar(100) NOT NULL,
  PRIMARY KEY (`PK_Action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `IVR_Menus` */

DROP TABLE IF EXISTS `IVR_Menus`;

CREATE TABLE `IVR_Menus` (
  `PK_Menu` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `Description` text NOT NULL,
  `ExtensionDialing` tinyint(1) NOT NULL DEFAULT '0',
  `FK_SoundLanguage_Invalid` int(10) unsigned NOT NULL,
  `FK_SoundEntry_Invalid` int(10) unsigned NOT NULL,
  `FK_Menu_Invalid` int(10) unsigned NOT NULL,
  `FK_Action_Invalid` int(10) unsigned NOT NULL,
  `Timeout` smallint(5) unsigned NOT NULL,
  `FK_SoundLanguage_Timeout` int(10) unsigned NOT NULL,
  `FK_SoundEntry_Timeout` int(10) unsigned NOT NULL,
  `FK_Menu_Timeout` int(10) unsigned NOT NULL,
  `FK_Action_Timeout` int(10) unsigned NOT NULL,
  `Retry` smallint(5) unsigned NOT NULL,
  `FK_SoundLanguage_Retry` int(10) unsigned NOT NULL,
  `FK_SoundEntry_Retry` int(10) unsigned NOT NULL,
  `FK_Menu_Retry` int(10) unsigned NOT NULL,
  `FK_Action_Retry` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PK_Menu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `IVR_Options` */

DROP TABLE IF EXISTS `IVR_Options`;

CREATE TABLE `IVR_Options` (
  `PK_Option` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Key` smallint(5) unsigned NOT NULL,
  `FK_Menu` int(10) unsigned NOT NULL,
  `FK_Menu_Entry` int(10) unsigned NOT NULL,
  `FK_Action_Entry` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PK_Option`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `IncomingRoutes` */

DROP TABLE IF EXISTS `IncomingRoutes`;

CREATE TABLE `IncomingRoutes` (
  `PK_IncomingRoute` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RouteType` enum('single','multiple') NOT NULL DEFAULT 'single',
  `ProviderType` enum('ANY','SIP','DONGLE') NOT NULL DEFAULT 'ANY',
  `ProviderID` int(10) unsigned NOT NULL,
  `StartNumber` varchar(20) NOT NULL,
  `EndNumber` varchar(20) DEFAULT NULL,
  `TrimFront` smallint(6) DEFAULT '0',
  `Add` int(11) DEFAULT '0',
  `Extension` varchar(20) DEFAULT NULL,
  `RouteOrder` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PK_IncomingRoute`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `IncomingRules` */

DROP TABLE IF EXISTS `IncomingRules`;

CREATE TABLE `IncomingRules` (
  `PK_IncomingRule` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RuleOrder` int(11) NOT NULL,
  `RuleType` enum('transfer','block') NOT NULL,
  `Subject` enum('prefix','phone') NOT NULL,
  `Digits` varchar(20) NOT NULL,
  `Extension` varchar(10) NOT NULL,
  `BlockType` enum('busy','congestion','hangup') NOT NULL,
  `FK_Timeframe` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PK_IncomingRule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Moh_Files` */

DROP TABLE IF EXISTS `Moh_Files`;

CREATE TABLE `Moh_Files` (
  `PK_File` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Filename` varchar(255) NOT NULL,
  `Fileext` varchar(10) NOT NULL,
  `FK_Group` int(10) unsigned NOT NULL,
  `Order` int(10) unsigned NOT NULL,
  `DateCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PK_File`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Moh_Groups` */

DROP TABLE IF EXISTS `Moh_Groups`;

CREATE TABLE `Moh_Groups` (
  `PK_Group` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(15) NOT NULL,
  `Description` text NOT NULL,
  `Volume` int(10) unsigned NOT NULL DEFAULT '100',
  `Ordered` tinyint(1) NOT NULL DEFAULT '0',
  `Protected` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PK_Group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `NATTypes` */

DROP TABLE IF EXISTS `NATTypes`;

CREATE TABLE `NATTypes` (
  `PK_NATType` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Description` varchar(100) NOT NULL,
  PRIMARY KEY (`PK_NATType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `OutgoingCIDRules` */

DROP TABLE IF EXISTS `OutgoingCIDRules`;

CREATE TABLE `OutgoingCIDRules` (
  `PK_OutgoingCIDRule` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Type` enum('Single','Multiple') NOT NULL,
  `ExtensionStart` mediumint(8) unsigned NOT NULL,
  `ExtensionEnd` mediumint(8) unsigned NOT NULL,
  `FK_OutgoingRule` int(10) unsigned NOT NULL,
  `Add` int(10) unsigned NOT NULL,
  `PrependDigits` varchar(10) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Number` varchar(100) NOT NULL,
  PRIMARY KEY (`PK_OutgoingCIDRule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `OutgoingRules` */

DROP TABLE IF EXISTS `OutgoingRules`;

CREATE TABLE `OutgoingRules` (
  `PK_OutgoingRule` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RuleOrder` int(10) unsigned NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Final` tinyint(1) NOT NULL,
  `BeginWith` varchar(30) NOT NULL,
  `RestBetweenLow` smallint(5) unsigned NOT NULL DEFAULT '0',
  `RestBetweenHigh` smallint(5) unsigned NOT NULL DEFAULT '0',
  `TrimFront` smallint(5) unsigned NOT NULL DEFAULT '0',
  `PrependDigits` varchar(30) NOT NULL,
  `ProviderType` enum('INTERNAL','SIP','GROUP','DONGLE') NOT NULL,
  `ProviderID` int(10) unsigned NOT NULL,
  `Protected` tinyint(1) NOT NULL,
  PRIMARY KEY (`PK_OutgoingRule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `RecordingLog` */

DROP TABLE IF EXISTS `RecordingLog`;

CREATE TABLE `RecordingLog` (
  `PK_RecordingLog` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FK_Rule` int(10) unsigned NOT NULL,
  `FK_CallLog` varchar(50) NOT NULL,
  `Label` varchar(100) NOT NULL,
  `RecordedID` int(10) unsigned NOT NULL,
  `RecordedType` varchar(50) NOT NULL,
  `RecordedName` varchar(100) NOT NULL,
  `RecordedNumber` varchar(100) NOT NULL,
  `StartDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `CallerID` int(10) unsigned NOT NULL,
  `CallerType` varchar(50) NOT NULL,
  `CallerName` varchar(100) NOT NULL,
  `CallerNumber` varchar(100) NOT NULL,
  `CalledID` int(10) unsigned NOT NULL,
  `CalledType` varchar(50) NOT NULL,
  `CalledName` varchar(100) NOT NULL,
  `CalledNumber` varchar(100) NOT NULL,
  `Duration` int(10) unsigned NOT NULL,
  `Size` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PK_RecordingLog`),
  KEY `StartDate` (`StartDate`),
  KEY `FK_Rule` (`FK_Rule`),
  KEY `FK_CallLog` (`FK_CallLog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `RecordingRules` */

DROP TABLE IF EXISTS `RecordingRules`;

CREATE TABLE `RecordingRules` (
  `PK_Rule` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Type` varchar(25) NOT NULL,
  `Call_Incoming` tinyint(1) NOT NULL DEFAULT '0',
  `Call_Outgoing` tinyint(1) NOT NULL DEFAULT '0',
  `Call_Queue` tinyint(1) NOT NULL DEFAULT '0',
  `EndCount` int(10) unsigned DEFAULT NULL,
  `EndDate` timestamp NULL DEFAULT NULL,
  `KeepCount` int(10) unsigned DEFAULT NULL,
  `KeepSize` int(10) unsigned DEFAULT NULL,
  `Backup` tinyint(1) NOT NULL,
  `MinLength` int(11) DEFAULT NULL,
  `Label` varchar(50) NOT NULL,
  PRIMARY KEY (`PK_Rule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `RecordingRules_Extensions` */

DROP TABLE IF EXISTS `RecordingRules_Extensions`;

CREATE TABLE `RecordingRules_Extensions` (
  `FK_Rule` int(10) unsigned NOT NULL,
  `FK_Extension` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Rule`,`FK_Extension`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `RecordingRules_Groups` */

DROP TABLE IF EXISTS `RecordingRules_Groups`;

CREATE TABLE `RecordingRules_Groups` (
  `FK_Rule` int(10) unsigned NOT NULL,
  `FK_Group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Rule`,`FK_Group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `RingStrategies` */

DROP TABLE IF EXISTS `RingStrategies`;

CREATE TABLE `RingStrategies` (
  `PK_RingStrategy` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) NOT NULL,
  `Description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`PK_RingStrategy`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Settings` */

DROP TABLE IF EXISTS `Settings`;

CREATE TABLE `Settings` (
  `Name` varchar(40) NOT NULL,
  `Value` text NOT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `SipProvider_Codecs` */

DROP TABLE IF EXISTS `SipProvider_Codecs`;

CREATE TABLE `SipProvider_Codecs` (
  `FK_SipProvider` int(10) unsigned NOT NULL,
  `FK_Codec` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_SipProvider`,`FK_Codec`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `SipProvider_Rules` */

DROP TABLE IF EXISTS `SipProvider_Rules`;

CREATE TABLE `SipProvider_Rules` (
  `FK_SipProvider` int(10) unsigned NOT NULL,
  `FK_OutgoingRule` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_SipProvider`,`FK_OutgoingRule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `SipProvider_Status` */

DROP TABLE IF EXISTS `SipProvider_Status`;

CREATE TABLE `SipProvider_Status` (
  `FK_SipProvider` int(10) unsigned NOT NULL,
  `Latency` varchar(10) NOT NULL,
  `Status` varchar(50) NOT NULL,
  PRIMARY KEY (`FK_SipProvider`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

/*Table structure for table `SipProviders` */

DROP TABLE IF EXISTS `SipProviders`;

CREATE TABLE `SipProviders` (
  `PK_SipProvider` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) NOT NULL,
  `AccountID` varchar(32) NOT NULL,
  `Password` varchar(32) NOT NULL,
  `Host` varchar(64) NOT NULL,
  `CallbackExtension` varchar(15) DEFAULT NULL,
  `FK_DTMFMode` int(10) unsigned NOT NULL,
  `HostType` enum('Provider','Peer') NOT NULL DEFAULT 'Provider',
  `CallerIDChange` tinyint(1) NOT NULL DEFAULT '0',
  `CallerIDName` varchar(32) DEFAULT NULL,
  `CallerIDNumber` varchar(32) DEFAULT NULL,
  `CallerIDMethod` enum('FromHeader','P-Asserted-Identity','Remote-Party-ID') NOT NULL DEFAULT 'FromHeader',
  `SipPort` int(11) NOT NULL DEFAULT '5060',
  `SipExpiry` int(11) NOT NULL DEFAULT '120',
  `ProxyHost` varchar(64) DEFAULT NULL,
  `AuthUser` varchar(32) DEFAULT NULL,
  `AlwaysTrust` tinyint(1) NOT NULL DEFAULT '1',
  `SendEarlyMedia` tinyint(1) NOT NULL DEFAULT '0',
  `ApplyIncomingRules` tinyint(1) NOT NULL DEFAULT '1',
  `Qualify` tinyint(1) NOT NULL DEFAULT '0',
  `UserEqPhone` tinyint(1) NOT NULL DEFAULT '0',
  `LocalAddrFrom` tinyint(1) NOT NULL DEFAULT '0',
  `DTMFDial` tinyint(1) NOT NULL DEFAULT '0',
  `LocalUser` tinyint(1) NOT NULL,
  `Jitterbuffer` enum('never','yes','always','fixed','alwaysfixed') NOT NULL DEFAULT 'never',
  `Reinvite` enum('no','yes','nonat','update','nonat,update') NOT NULL DEFAULT 'no',
  `Voicepulse` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PK_SipProvider`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `SoundEntries` */

DROP TABLE IF EXISTS `SoundEntries`;

CREATE TABLE `SoundEntries` (
  `PK_SoundEntry` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Type` enum('User','System') NOT NULL DEFAULT 'User',
  `FK_SoundFolder` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PK_SoundEntry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `SoundFiles` */

DROP TABLE IF EXISTS `SoundFiles`;

CREATE TABLE `SoundFiles` (
  `PK_SoundFile` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FK_SoundEntry` int(10) unsigned NOT NULL,
  `FK_SoundPack` int(10) unsigned NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Description` text NOT NULL,
  `Filename` varchar(255) NOT NULL,
  `FK_SoundLanguage` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PK_SoundFile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `SoundFolders` */

DROP TABLE IF EXISTS `SoundFolders`;

CREATE TABLE `SoundFolders` (
  `PK_SoundFolder` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Description` text NOT NULL,
  `Type` enum('User','System') NOT NULL DEFAULT 'User',
  PRIMARY KEY (`PK_SoundFolder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `SoundLanguages` */

DROP TABLE IF EXISTS `SoundLanguages`;

CREATE TABLE `SoundLanguages` (
  `PK_SoundLanguage` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Type` enum('User','System') NOT NULL DEFAULT 'User',
  `Default` tinyint(1) NOT NULL,
  PRIMARY KEY (`PK_SoundLanguage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `SoundPacks` */

DROP TABLE IF EXISTS `SoundPacks`;

CREATE TABLE `SoundPacks` (
  `PK_SoundPack` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  PRIMARY KEY (`PK_SoundPack`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Template_Codecs` */

DROP TABLE IF EXISTS `Template_Codecs`;

CREATE TABLE `Template_Codecs` (
  `FK_Template` int(10) unsigned NOT NULL,
  `FK_Codec` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Codec`,`FK_Template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `Template_Features` */

DROP TABLE IF EXISTS `Template_Features`;

CREATE TABLE `Template_Features` (
  `FK_Template` int(10) unsigned NOT NULL,
  `FK_Feature` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Feature`,`FK_Template`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `Template_Groups` */

DROP TABLE IF EXISTS `Template_Groups`;

CREATE TABLE `Template_Groups` (
  `FK_Group` int(10) unsigned NOT NULL,
  `FK_Template` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Template`,`FK_Group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `Template_Rules` */

DROP TABLE IF EXISTS `Template_Rules`;

CREATE TABLE `Template_Rules` (
  `FK_Template` int(10) unsigned NOT NULL,
  `FK_OutgoingRule` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FK_Template`,`FK_OutgoingRule`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `Templates` */

DROP TABLE IF EXISTS `Templates`;

CREATE TABLE `Templates` (
  `PK_Template` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) NOT NULL,
  `Name_Editable` tinyint(1) NOT NULL,
  `Password_Editable` tinyint(1) NOT NULL,
  `Email_Editable` tinyint(1) NOT NULL,
  `FK_NATType` int(10) unsigned NOT NULL DEFAULT '1',
  `FK_DTMFMode` int(10) unsigned NOT NULL DEFAULT '1',
  `IVRDial` tinyint(1) NOT NULL DEFAULT '1',
  `Protected` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PK_Template`),
  KEY `FK_NATType` (`FK_NATType`),
  KEY `FK_DTMFMode` (`FK_DTMFMode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Timeframe_Intervals` */

DROP TABLE IF EXISTS `Timeframe_Intervals`;

CREATE TABLE `Timeframe_Intervals` (
  `PK_Interval` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FK_Timeframe` int(10) unsigned NOT NULL,
  `StartDate` date DEFAULT NULL,
  `EndDate` date DEFAULT NULL,
  `StartDay` mediumint(9) DEFAULT NULL,
  `EndDay` mediumint(9) DEFAULT NULL,
  `StartTime` varchar(50) NOT NULL,
  `StartTimeMode` enum('AM','PM') NOT NULL,
  `EndTime` varchar(50) NOT NULL,
  `EndTimeMode` enum('AM','PM') NOT NULL,
  `OrderDummy` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`PK_Interval`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Timeframes` */

DROP TABLE IF EXISTS `Timeframes`;

CREATE TABLE `Timeframes` (
  `PK_Timeframe` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `FK_Extension` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`PK_Timeframe`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `Version` */

DROP TABLE IF EXISTS `Version`;

CREATE TABLE `Version` (
  `Name` varchar(40) NOT NULL,
  `Value` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/* Function  structure for function  `ValidTimeInterval` */

/*!50003 DROP FUNCTION IF EXISTS `ValidTimeInterval` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `ValidTimeInterval`(id_interval INT) RETURNS int(11)
    DETERMINISTIC
BEGIN
 
 DECLARE date_match INT DEFAULT 0;
 DECLARE time_match INT DEFAULT 0;
 DECLARE day_match  INT DEFAULT 0;

 DECLARE sec_start  INT DEFAULT 0;
 DECLARE sec_end    INT DEFAULT 0;
 DECLARE sec_now    INT DEFAULT 0;

 SELECT 
  COUNT(PK_Interval)
 INTO
  date_match
 FROM
  Timeframe_Intervals
 WHERE
  (ISNULL(StartDate) OR StartDate = '00-00-0000' OR StartDate <= CURDATE())
  AND
  (ISNULL(EndDate)   OR EndDate   = '00-00-0000' OR EndDate   >= CURDATE())
  AND
  PK_Interval=id_interval
 LIMIT 1;

 
 SELECT
  TIME_TO_SEC(STR_TO_DATE(CONCAT('1970-01-01 ',StartTime,' ',StartTimeMode),'%Y-%m-%d %l:%i %p')),
  TIME_TO_SEC(STR_TO_DATE(CONCAT('1970-01-01 ',EndTime  ,' ',EndTimeMode  ),'%Y-%m-%d %l:%i %p')),
  TIME_TO_SEC(DATE_FORMAT(NOW(),'%H:%i'))
 INTO
  sec_start,
  sec_end,
  sec_now
 FROM
  Timeframe_Intervals 
 WHERE 
  PK_Interval = id_interval 
 LIMIT 1;
 
 
 SELECT
  COUNT(PK_Interval)
 INTO
  time_match
 FROM
  Timeframe_Intervals
 WHERE
  (
   (
    (ISNULL(StartTime) OR StartTime = '') 
    AND
    (ISNULL(EndTime)   OR EndTime   = '')
   ) 
   OR
   (sec_start < sec_end  AND sec_now >= sec_start AND sec_now <= sec_end  )
   OR
   (sec_start >= sec_end AND (sec_now <= sec_end OR sec_now >= sec_start))
  )
  AND
  PK_Interval = id_interval
 LIMIT 1;

 
 SELECT
  COUNT(PK_Interval)
 INTO
  day_match
 FROM
  Timeframe_Intervals
 WHERE
  (
   (StartDay = 0 AND EndDay = 0)
   OR
   (StartDay < EndDay AND MOD(DAYOFWEEK(NOW())+5,7)+1 >= StartDay AND MOD(DAYOFWEEK(NOW())+5,7)+1 <= EndDay)
   OR
   (StartDay >= EndDay AND (MOD(DAYOFWEEK(NOW())+5,7)+1 <= EndDay OR MOD(DAYOFWEEK(NOW())+5,7)+1 >= StartDay))
  )
  AND
  PK_Interval = id_interval
 LIMIT 1;
 
 
 RETURN time_match * date_match * day_match;
END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
