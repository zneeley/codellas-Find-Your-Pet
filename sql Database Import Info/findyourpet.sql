-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2019 at 04:08 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `findyourpet`
--

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--
DROP TABLE IF EXISTS `pets`;

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `petID` varchar(100) NOT NULL,
  `shelterID` varchar(100) NOT NULL,
  `petType` varchar(10) DEFAULT NULL,
  `petName` varchar(20) DEFAULT NULL,
  `breed` int(11) DEFAULT NULL,
  `gender` varchar(8) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `neutered` varchar(5) DEFAULT NULL,
  `vaccinationRecords` text DEFAULT NULL,
  `petImage` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `petID`, `shelterID`, `petType`, `petName`, `breed`, `gender`, `age`, `neutered`, `vaccinationRecords`, `petImage`, `bio`, `created_at`) VALUES
(6, 'UPID-5dccb42d6ae29', 'USID-5dc090c335263', 'cat', 'Gizmo', 0, 'male', 2, 'yes', 'REPLACE WITH REAL VALUE', 'dXBsb2FkQ29udGVudC9wZXRJbWFnZXMvVVBJRC01ZGNjYjQyZDZhZTI5LmpwZw==', 'Kaylo\'s cat Gizmo!', '2019-11-13 18:55:57');

-- --------------------------------------------------------

--
-- Table structure for table `shelters`
--
DROP TABLE IF EXISTS `shelters`;

CREATE TABLE `shelters` (
  `id` int(11) NOT NULL,
  `shelterID` varchar(100) NOT NULL,
  `shelterName` varchar(50) NOT NULL,
  `email` varchar(30) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phoneNum` varchar(25) DEFAULT NULL,
  `profileImage` text NOT NULL DEFAULT 'dXBsb2FkQ29udGVudC9kZWZhdWx0UHJvZmlsZUltZy5wbmc=',
  `shelterBio` text NOT NULL DEFAULT 'This is a default shelter bio!',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shelters`
--

INSERT INTO `shelters` (`id`, `shelterID`, `shelterName`, `email`, `username`, `password`, `address`, `phoneNum`, `profileImage`, `shelterBio`, `created_at`) VALUES
(1, 'USID-5d9e170c97b6b', '', '', 'defaultShelter', '$2y$10$8UHvF0KR3CXEFFeMGKvxw.HY3Xk2e2NHqH.48BTvkoUFpbtvjrucW', NULL, NULL, '', '', '2019-10-09 11:21:16'),
(2, 'USID-5d9e17269bb3e', '', '', 'adminShelter', '$2y$10$TIoWECE4xAxTLnSCX6vnfOQ1UfWpavTsX3P2w/qQciXIP3n6xL5/i', NULL, NULL, '', '', '2019-10-09 11:21:42'),
(16, 'USID-5dc090c335263', 'demo2', 'ZGVtb0BubXN1LmVkdQ==', 'demo2', '$2y$10$cI3BNMzTgCuMFBe2wNC3oO1bb/WodW4OovCq.YMhc2yHqUnkoa7eS', '123 ABC street', '(575) 777-8888', 'dXBsb2FkQ29udGVudC9zaGVsdGVySW1hZ2VzL1VTSUQtNWRjMDkwYzMzNTI2My5wbmc=', 'This is a shelter bio.', '2019-11-04 13:57:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `userID` varchar(100) NOT NULL,
  `FirstName` varchar(30) NOT NULL,
  `LastName` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profileImage` text NOT NULL DEFAULT 'dXBsb2FkQ29udGVudC9kZWZhdWx0UHJvZmlsZUltZy5wbmc=',
  `userBio` text NOT NULL DEFAULT 'This is a default user bio!',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `userID`, `FirstName`, `LastName`, `email`, `username`, `password`, `profileImage`, `userBio`, `created_at`) VALUES
(17, 'UUID-5d9fb6de8ea6d', 'Zach', 'Neeley', 'em5lZWxleUBubXN1LmVkdQ==', 'zneeley', '$2y$10$R4JSBI4qaXJ2iq/YOR00LeO0jJFE98c4qbwtwMmU9VfUS6E2fznJC', 'dXBsb2FkQ29udGVudC91c2VySW1hZ2VzL1VVSUQtNWQ5ZmI2ZGU4ZWE2ZC5qcGc=', 'This is a real bio!', '2019-10-10 16:55:26'),
(33, 'UUID-5daf9d81099de', 'Kaylynn', 'Melendrez', 'bWtheUBubXN1LmVkdQ==', 'kaylo41', '$2y$10$Xr4FTf8MU97LA/g/VaaYkO54Z1QGRoJNzdEDAdGafnEdU8qOS4uUe', 'dXBsb2FkQ29udGVudC91c2VySW1hZ2VzL1VVSUQtNWRhZjlkODEwOTlkZS5qcGc=', 'Testing A fix.', '2019-10-22 18:23:29'),
(34, 'UUID-5db083e615ce1', 'Sikta', 'Das', 'c2lrdGFAbm1zdS5lZHU=', 'sdas', '$2y$10$6kVGxqDOpqJcxnkir4yxLeSFYImLRXaNn73EfTQ2Ays3T0qQtmsC6', 'dXBsb2FkQ29udGVudC91c2VySW1hZ2VzL1VVSUQtNWRiMDgzZTYxNWNlMS5wbmc=', 'dogs are the best', '2019-10-23 10:46:30'),
(36, 'UUID-5dc06d23b694e', 'testing', 'test', 'dGVzdEA=', 'tests', '$2y$10$/R6Q2LfMst6A376zdb530ekqbiayO/u2a64UNxwqDDfpYZKO5jU.a', 'dXBsb2FkQ29udGVudC91c2VySW1hZ2VzL1VVSUQtNWRjMDZkMjNiNjk0ZS5qcGc=', 'This is a nice bio!', '2019-11-04 11:25:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shelters`
--
ALTER TABLE `shelters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shelters`
--
ALTER TABLE `shelters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
