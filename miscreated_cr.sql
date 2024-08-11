-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 11, 2024 at 04:10 AM
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
-- Database: `miscreated_cr`
--

-- --------------------------------------------------------

--
-- Table structure for table `change_requests`
--

DROP TABLE IF EXISTS `change_requests`;
CREATE TABLE IF NOT EXISTS `change_requests` (
  `RequestID` int(11) NOT NULL AUTO_INCREMENT,
  `RequestDateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `RequestType` varchar(128) NOT NULL,
  `RequestItem` varchar(128) NOT NULL,
  `RequestShortDescription` varchar(64) NOT NULL,
  `RequestDescription` text NOT NULL,
  `Requestor` varchar(64) NOT NULL,
  `Status` varchar(64) NOT NULL DEFAULT '"Initial"',
  `Completed` enum('Y','N') NOT NULL,
  `CompletedVersion` varchar(16) DEFAULT NULL,
  `CompletedDateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`RequestID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `change_request_notes`
--

DROP TABLE IF EXISTS `change_request_notes`;
CREATE TABLE IF NOT EXISTS `change_request_notes` (
  `RequestNoteID` int(11) NOT NULL AUTO_INCREMENT,
  `RequestID` int(11) NOT NULL,
  `NoteAuthor` varchar(128) NOT NULL,
  `NoteDescription` text NOT NULL,
  `NoteDateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`RequestNoteID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
