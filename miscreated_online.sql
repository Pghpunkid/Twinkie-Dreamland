-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 11, 2024 at 04:09 AM
-- Server version: 5.7.28
-- PHP Version: 7.4.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `miscreated_online`
--

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

DROP TABLE IF EXISTS `characters`;
CREATE TABLE IF NOT EXISTS `characters` (
  `DBCharacterID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `CharacterID` int(11) NOT NULL,
  `GameServerID` int(11) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `PosX` double NOT NULL,
  `PosY` double NOT NULL,
  `PosZ` double NOT NULL,
  `RotZ` double NOT NULL,
  `Health` double NOT NULL,
  `Food` double NOT NULL,
  `Water` double NOT NULL,
  `Radiation` double NOT NULL,
  `Temperature` double NOT NULL,
  `CreationDate` int(11) NOT NULL,
  `SelectedSlot` text COLLATE utf8_unicode_ci NOT NULL,
  `MapName` text COLLATE utf8_unicode_ci NOT NULL,
  `Gender` int(11) NOT NULL,
  `Data` text COLLATE utf8_unicode_ci NOT NULL,
  `CharacterGUID` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `DBCharacterID` (`DBCharacterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clanmembers`
--

DROP TABLE IF EXISTS `clanmembers`;
CREATE TABLE IF NOT EXISTS `clanmembers` (
  `DBClanMemberID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `ClanMemberID` int(11) NOT NULL,
  `ClanID` int(11) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `MemberName` text COLLATE utf8_unicode_ci NOT NULL,
  `IsAdmin` int(11) NOT NULL,
  `CanAlterMembers` double NOT NULL,
  `CanAlterParts` double NOT NULL,
  `CanAlterLocks` double NOT NULL,
  `CanAlterPower` double NOT NULL,
  PRIMARY KEY (`DBClanMemberID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clans`
--

DROP TABLE IF EXISTS `clans`;
CREATE TABLE IF NOT EXISTS `clans` (
  `DBClanID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `ClanID` int(11) NOT NULL,
  `GameServerID` int(11) NOT NULL,
  `OwnerAccountID` int(11) NOT NULL,
  `ClanName` text COLLATE utf8_unicode_ci NOT NULL,
  `CreationDate` int(11) NOT NULL,
  PRIMARY KEY (`DBClanID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_backuphistory`
--

DROP TABLE IF EXISTS `db_backuphistory`;
CREATE TABLE IF NOT EXISTS `db_backuphistory` (
  `BackupID` int(11) NOT NULL AUTO_INCREMENT,
  `GUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `BackupDateTime` datetime NOT NULL,
  PRIMARY KEY (`BackupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_steamnames`
--

DROP TABLE IF EXISTS `db_steamnames`;
CREATE TABLE IF NOT EXISTS `db_steamnames` (
  `SteamID` bigint(20) NOT NULL,
  `SteamID3` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `AccountID` int(11) NOT NULL,
  `Name` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `LastUpdate` datetime NOT NULL,
  `ServerAdminLevel` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `SteamID` (`SteamID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_structureparttypes`
--

DROP TABLE IF EXISTS `db_structureparttypes`;
CREATE TABLE IF NOT EXISTS `db_structureparttypes` (
  `PartTypeID` int(11) NOT NULL,
  `ClassName` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `MaxHealth` int(11) DEFAULT NULL,
  `Towable` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `IconURL` text COLLATE utf8_unicode_ci,
  `IconURLLarge` text COLLATE utf8_unicode_ci,
  `EnglishName` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`PartTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Classes and Descriptions of Structure Parts.';

-- --------------------------------------------------------

--
-- Table structure for table `db_systemvars`
--

DROP TABLE IF EXISTS `db_systemvars`;
CREATE TABLE IF NOT EXISTS `db_systemvars` (
  `LastUpdate` datetime NOT NULL,
  `MaintenanceMode` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entities`
--

DROP TABLE IF EXISTS `entities`;
CREATE TABLE IF NOT EXISTS `entities` (
  `DBEntityID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `EntityID` int(11) NOT NULL,
  `GameServerID` int(11) NOT NULL,
  `MapName` text COLLATE utf8_unicode_ci NOT NULL,
  `ClassName` text COLLATE utf8_unicode_ci NOT NULL,
  `PosX` double NOT NULL,
  `PosY` double NOT NULL,
  `PosZ` double NOT NULL,
  `RotZ` double NOT NULL,
  `Data` text COLLATE utf8_unicode_ci NOT NULL,
  `EntityGUID` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`DBEntityID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `DBItemID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `ItemID` int(11) NOT NULL,
  `Slot` text COLLATE utf8_unicode_ci NOT NULL,
  `ClassName` text COLLATE utf8_unicode_ci NOT NULL,
  `Data` text COLLATE utf8_unicode_ci NOT NULL,
  `ItemGUID` text COLLATE utf8_unicode_ci NOT NULL,
  `ParentGUID` text COLLATE utf8_unicode_ci NOT NULL,
  `OwnerGUID` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`DBItemID`),
  KEY `OwnerGUID` (`OwnerGUID`(36)),
  KEY `DBBackupGUID` (`DBBackupGUID`(36))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `serveraccountdata`
--

DROP TABLE IF EXISTS `serveraccountdata`;
CREATE TABLE IF NOT EXISTS `serveraccountdata` (
  `DBServerAccountDataID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `ServerAccountDataID` int(11) NOT NULL,
  `GameServerID` int(11) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `Guide00` int(11) NOT NULL,
  `Guide01` int(11) NOT NULL,
  `Guide02` int(11) NOT NULL,
  `Guide03` int(11) NOT NULL,
  `ClanID` int(11) NOT NULL,
  `IsPendingClanInvite` int(11) NOT NULL,
  `IgnoreClanInvites` int(11) NOT NULL,
  `HadTasksAssigned` int(11) NOT NULL,
  PRIMARY KEY (`DBServerAccountDataID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sqlite_sequence`
--

DROP TABLE IF EXISTS `sqlite_sequence`;
CREATE TABLE IF NOT EXISTS `sqlite_sequence` (
  `DBsqlite_sequenceID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `seq` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`DBsqlite_sequenceID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `structureparts`
--

DROP TABLE IF EXISTS `structureparts`;
CREATE TABLE IF NOT EXISTS `structureparts` (
  `DBStructurePartID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `StructurePartID` int(11) NOT NULL,
  `PartTypeID` int(11) NOT NULL,
  `PosX` double NOT NULL,
  `PosY` double NOT NULL,
  `PosZ` double NOT NULL,
  `RotZ` double NOT NULL,
  `StructurePartGUID` text COLLATE utf8_unicode_ci NOT NULL,
  `Data` text COLLATE utf8_unicode_ci NOT NULL,
  `StructureGUID` text COLLATE utf8_unicode_ci NOT NULL,
  `ParentStructurePartGUIDs` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`DBStructurePartID`),
  KEY `StructureID` (`StructureGUID`(36)),
  KEY `DBBackupGUID` (`DBBackupGUID`(36))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `structures`
--

DROP TABLE IF EXISTS `structures`;
CREATE TABLE IF NOT EXISTS `structures` (
  `DBStructuresID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `StructureID` int(11) NOT NULL,
  `GameServerID` int(11) NOT NULL,
  `MapName` text COLLATE utf8_unicode_ci NOT NULL,
  `AccountID` int(11) NOT NULL,
  `ClassName` text COLLATE utf8_unicode_ci NOT NULL,
  `PosX` double NOT NULL,
  `PosY` double NOT NULL,
  `PosZ` double NOT NULL,
  `RotX` double NOT NULL,
  `RotY` double NOT NULL,
  `RotZ` double NOT NULL,
  `AbandonTimer` int(11) NOT NULL,
  `Data` text COLLATE utf8_unicode_ci NOT NULL,
  `StructureGUID` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`DBStructuresID`),
  KEY `DBBackupGUID` (`DBBackupGUID`(36))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE IF NOT EXISTS `tasks` (
  `DBTaskID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `GameServerID` int(11) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `TaskCRC` int(11) NOT NULL,
  `TaskType` int(11) NOT NULL,
  `Amount` int(11) NOT NULL,
  PRIMARY KEY (`DBTaskID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
CREATE TABLE IF NOT EXISTS `vehicles` (
  `DBVehicleID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `VehicleID` int(11) NOT NULL,
  `GameServerID` int(11) NOT NULL,
  `MapName` text COLLATE utf8_unicode_ci NOT NULL,
  `Category` text COLLATE utf8_unicode_ci NOT NULL,
  `ClassName` text COLLATE utf8_unicode_ci NOT NULL,
  `PosX` double NOT NULL,
  `PosY` double NOT NULL,
  `PosZ` double NOT NULL,
  `RotX` double NOT NULL,
  `RotY` double NOT NULL,
  `RotZ` double NOT NULL,
  `AbandonTimer` int(11) NOT NULL,
  `Data` text COLLATE utf8_unicode_ci NOT NULL,
  `VehicleGUID` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`DBVehicleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `version`
--

DROP TABLE IF EXISTS `version`;
CREATE TABLE IF NOT EXISTS `version` (
  `DBVersionID` int(11) NOT NULL AUTO_INCREMENT,
  `DBBackupGUID` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
  `Version` int(11) DEFAULT NULL,
  PRIMARY KEY (`DBVersionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
