-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2019 at 04:44 AM
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
-- Table structure for table `shelters`
--

CREATE TABLE `shelters` (
  `id` int(11) NOT NULL,
  `shelterID` varchar(100) NOT NULL,
  `email` varchar(30) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `shelters`
--

INSERT INTO `shelters` (`id`, `shelterID`, `email`, `username`, `password`, `created_at`) VALUES
(1, 'USID-5d9e170c97b6b', '', 'defaultShelter', '$2y$10$8UHvF0KR3CXEFFeMGKvxw.HY3Xk2e2NHqH.48BTvkoUFpbtvjrucW', '2019-10-09 11:21:16'),
(2, 'USID-5d9e17269bb3e', '', 'adminShelter', '$2y$10$TIoWECE4xAxTLnSCX6vnfOQ1UfWpavTsX3P2w/qQciXIP3n6xL5/i', '2019-10-09 11:21:42'),
(3, 'USID-5d9f92a064d45', '', 'autoLoginShelter', '$2y$10$ZiFQu40q.uAamsRL9NC2TOTSsCnIp9.eGDEf3I.1SWalPF4HEHpDq', '2019-10-10 14:20:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shelters`
--
ALTER TABLE `shelters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shelters`
--
ALTER TABLE `shelters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
