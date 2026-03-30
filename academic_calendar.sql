-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 30, 2026 at 12:26 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `academic_calendar`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$etEGhMcKEFRZQBrTm54xIeU0F7twmA0zCAsFeZIpm/z17rRlMEcRa');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `type`, `event_date`, `description`) VALUES
(1, 'Orientation Program', 'Academic', '2025-08-05', 'Orientation for new students'),
(2, 'Independence Day Holiday', 'Holiday', '2025-08-15', 'University closed for Independence Day'),
(3, 'Mid Semester Examination Begins', 'Exam', '2025-09-20', 'Start of mid semester exams'),
(4, 'Mid Semester Examination Ends', 'Exam', '2025-09-30', 'End of mid semester exams'),
(5, 'Durga Puja Holiday', 'Holiday', '2025-10-08', 'University closed for Durga Puja'),
(6, 'Project Submission Deadline', 'Academic', '2025-10-20', 'Final year project submission'),
(7, 'Diwali Holiday', 'Holiday', '2025-11-01', 'University closed for Diwali'),
(8, 'End Semester Exams Begin', 'Exam', '2025-11-20', 'Start of end semester examinations'),
(9, 'End Semester Exams End', 'Exam', '2025-12-05', 'End of end semester examinations'),
(10, 'Winter Break Begins', 'Holiday', '2025-12-20', 'Start of winter vacation'),
(11, 'Winter Break Ends', 'Holiday', '2026-01-05', 'Classes resume after winter break'),
(12, 'Republic Day Holiday', 'Holiday', '2026-01-26', 'University closed for Republic Day'),
(13, 'Spring Semester Begins', 'Academic', '2026-02-01', 'Start of spring semester'),
(14, 'Sports Week', 'Academic', '2026-03-10', 'University sports competitions'),
(15, 'Annual Cultural Fest', 'Academic', '2026-03-25', 'Annual cultural festival of the university');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
