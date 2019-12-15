-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2019 at 06:49 PM
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

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `petID` varchar(100) NOT NULL,
  `shelterID` varchar(100) NOT NULL,
  `petType` varchar(10) DEFAULT NULL,
  `petName` varchar(20) DEFAULT NULL,
  `breed` varchar(25) DEFAULT NULL,
  `gender` varchar(8) DEFAULT NULL,
  `age` varchar(12) DEFAULT NULL,
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
(6, 'UPID-5dccb42d6ae29', 'USID-5dc090c335263', 'cat', 'Gizmo', 'American Shorthair', 'Male', '2-4 years', 'yes', 'Rabies,Feline Distemper (Panleukopenia),Feline Herpesvirus,Calicivirus,Feline Leukemia Virus (FeLV),Bordetella,', 'dXBsb2FkQ29udGVudC9wZXRJbWFnZXMvVVBJRC01ZGNjYjQyZDZhZTI5LmpwZw==', 'Gizmo is a very emotionally expressive cat. He can be Moody and cranky if he doesn\'t want to be bothered and also become cuddly or fiesty at a moment\'s notice. His hobbies include eating grass and sitting underneath the apple tree and his best friend is a German Shepard named Chico. His best quality is meowing at everything.', '2019-11-13 18:55:57'),
(7, 'UPID-5dd56f600d627', 'USID-5dc090c335263', 'dog', 'Malcom', 'Siberian Hucky', 'Male', '2-4 years', 'Yes', 'Rabies,Distemper,Parvovirus,Adenovirus Type 1,Adenovirus Type 2,Parainfluenza,Bordetella bronchiseptica (kennel cough),Lyme disease,Leptospirosis,Canine influenza,', 'dXBsb2FkQ29udGVudC9wZXRJbWFnZXMvVVBJRC01ZGQ1NmY2MDBkNjI3LmpwZw==', 'Loves to play in the snow! Likes to play with everyone.', '2019-11-20 09:52:48'),
(10, 'UPID-5de978eb589ce', 'USID-5dc090c335263', 'dog', 'Spot', 'Labrador Retriever', 'Male', '0-2 years', 'Yes', 'Rabies,Distemper,Parvovirus,Adenovirus Type 1,Adenovirus Type 2,Parainfluenza,Bordetella bronchiseptica (kennel cough),Lyme disease,Leptospirosis,Canine influenza,', 'dXBsb2FkQ29udGVudC9wZXRJbWFnZXMvVVBJRC01ZGU5NzhlYjU4OWNlLmpwZw==', 'Loves to play fetch with tennis balls. But he has still yet to learn how to bring them back to you. ', '2019-12-05 14:38:51'),
(11, 'UPID-5dee8a0e2ed9b', 'USID-5dee887c6cb54', 'dog', 'Chico ', 'German Shepard', 'Male', '8-10 years', 'No', 'Rabies,', 'dXBsb2FkQ29udGVudC9wZXRJbWFnZXMvVVBJRC01ZGVlOGEwZTJlZDliLmpwZw==', 'He likes to roll around in the grass and bark at cars.', '2019-12-09 10:53:18'),
(15, 'UPID-5df109e6e53d4', 'USID-5dee887c6cb54', 'cat', 'Tiger', 'American Shorthair', 'Female', '0-2 years', 'Yes', 'Rabies,Feline Distemper (Panleukopenia),Feline Herpesvirus,Calicivirus,Feline Leukemia Virus (FeLV),Bordetella,', 'dXBsb2FkQ29udGVudC9wZXRJbWFnZXMvVVBJRC01ZGYxMDllNmU1M2Q0LmpwZw==', 'Loves to eat grass and meow at stuff.', '2019-12-11 08:23:18'),
(16, 'UPID-5df10d4ce29e5', 'USID-5dc090c335263', 'dog', 'Bella', 'Yorkshire Terrier', 'Female', '0-2 years', 'Yes', 'Rabies,Distemper,Parvovirus,Adenovirus Type 1,Adenovirus Type 2,Parainfluenza,Bordetella bronchiseptica (kennel cough),Lyme disease,Leptospirosis,Canine influenza,', 'dXBsb2FkQ29udGVudC9wZXRJbWFnZXMvVVBJRC01ZGYxMGQ0Y2UyOWU1LmpwZw==', 'Loves to play around in the sand. She also likes to dig holes all over the place to hide her toys.', '2019-12-11 08:37:48'),
(17, 'UPID-5df122d7a66b4', 'USID-5dee887c6cb54', 'dog', 'Kira', 'Australian Shepherd', 'Female', '2-4 years', 'Yes', 'Rabies,Distemper,Parvovirus,Adenovirus Type 1,Adenovirus Type 2,Parainfluenza,Bordetella bronchiseptica (kennel cough),Lyme disease,Leptospirosis,Canine influenza,', 'dXBsb2FkQ29udGVudC9wZXRJbWFnZXMvVVBJRC01ZGYxMjJkN2E2NmI0LmpwZw==', 'Hi!!! I am kira! I love hoomans (specially ones called Sikta) and dogs. Cats... not so sure about those guys. They are fine, I guess. I like guinea pigs sometimes... ', '2019-12-11 10:09:43'),
(18, 'UPID-5df123f980787', 'USID-5dc090c335263', 'cat', 'Dondo', 'Siamese Cat', 'Male', '16 years or ', 'Yes', 'Rabies,Feline Distemper (Panleukopenia),Feline Herpesvirus,Calicivirus,Feline Leukemia Virus (FeLV),Bordetella,', 'dXBsb2FkQ29udGVudC9wZXRJbWFnZXMvVVBJRC01ZGYxMjNmOTgwNzg3LmpwZw==', 'This is Dondo. A cat with no name. He is 8ft tall and can breathe fire. He has no soul. He is half angel half devil. There is no such thing as faith in a world with no god.', '2019-12-11 10:14:33'),
(19, 'UPID-5df12d0fdb81a', 'USID-5dc090c335263', 'dog', 'Fido', 'Beagle', 'Male', '6-8 years', 'Yes', 'Adenovirus Type 2,Parainfluenza,', 'dXBsb2FkQ29udGVudC9wZXRJbWFnZXMvVVBJRC01ZGYxMmQwZmRiODFhLmpwZw==', 'Love to play in the grass!', '2019-12-11 10:53:19');

-- --------------------------------------------------------

--
-- Table structure for table `shelters`
--

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
(16, 'USID-5dc090c335263', 'demoAccount', 'ZGVtb0BubXN1LmVkdQ==', 'demoAccount', '$2y$10$cI3BNMzTgCuMFBe2wNC3oO1bb/WodW4OovCq.YMhc2yHqUnkoa7eS', '123 ABC Street', '(888) 888-8880', 'dXBsb2FkQ29udGVudC9zaGVsdGVySW1hZ2VzL1VTSUQtNWRjMDkwYzMzNTI2My5qcGc=', 'We take pride in working with our pets making sure they are well kept and enjoy life while looking for a new home.', '2019-11-04 13:57:39'),
(17, 'USID-5dee887c6cb54', 'shelterTest', 'dGVzdEBubXN1LmVkdQ==', 'shelterTest', '$2y$10$SXPrLXl0Od8wiwyUgOPD8OLE/1n9gBHI3Z1r2DxZxdI2ectISry5q', '1234 ABC Street', '(575) 646-1234', 'dXBsb2FkQ29udGVudC9zaGVsdGVySW1hZ2VzL1VTSUQtNWRlZTg4N2M2Y2I1NC5qcGc=', 'This is an amazing shelter!', '2019-12-09 10:46:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `shelters`
--
ALTER TABLE `shelters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
