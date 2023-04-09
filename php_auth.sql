-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2023 at 05:53 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `php_auth`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `post_id` int(11) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `names` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`post_id`, `comment`, `names`, `timestamp`, `user_id`) VALUES
(16, '     j jn kn', 'douglas', '2023-03-30 23:04:37', 4),
(11, 'fireflies', 'dave', '2023-03-28 01:10:19', 3),
(13, 'I\'m taking one right now', 'daisy', '2023-03-28 23:01:34', 5),
(1, 'test', 'dave', '2023-03-27 21:25:09', 3),
(3, 'test', 'mary jane', '2023-03-27 22:53:10', 1),
(2, 'yoh', 'douglas', '2023-03-31 08:26:24', 4);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `names` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `body`, `timestamp`, `names`) VALUES
(1, 1, 'learning to program in php', 'php is a backend scripting language that is used in developing web applications and secure websites.', '2023-03-21 21:57:25', 'mary jane'),
(2, 1, 'today is 22', 'this a date test', '2023-03-21 21:57:39', 'mary jane'),
(3, 1, 'Tech Job', 'A tech job is one that deals with computer programs, hardware, software, networking, and maintaining systems.', '2023-03-27 21:57:53', 'mary jane'),
(13, 5, 'Sipping Lemonade', 'I take lemonade twice a week', '2023-03-28 23:00:10', 'daisy'),
(16, 4, 'Song', 'Listening to Forever by Labrinth', '2023-03-30 13:42:23', 'douglas'),
(30, 8, 'Presentation', 'I\'m doing my presentation via zoom', '2023-03-31 07:14:13', 'johnson');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `names` varchar(30) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(80) DEFAULT NULL,
  `profilePhoto` varchar(80) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `lastLogin` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `names`, `email`, `phone`, `password`, `profilePhoto`, `createdAt`, `lastLogin`) VALUES
(1, 'mary jane', 'maryjane@example.com', '+254797925090', '$2y$10$rQVnnx/j2ES2s1jHjkSC2OMU1bNRQC4eiD7PVlokem.d37h1yJv8C', NULL, '2023-03-31 08:32:07', '2023-03-31 08:32:07'),
(2, 'john doe', 'johndoe@example.com', '+254114000307', '$2y$10$DX1jjDfCJT9Oij/ykSx2yeF6n6XeCmaVYP2RFcCdj3RoJY8.r7HYG', NULL, '2023-03-22 09:24:35', '2023-03-22 09:24:35'),
(3, 'dave', 'dave@example.com', '+254114000322', '$2y$10$PaejOv5PC4AXwBNccv9nV.B4Bk71GMPU1XnQHr0i0ApAiO246jl3C', NULL, '2023-03-28 08:50:27', '2023-03-28 08:50:27'),
(4, 'douglas', 'douglas@example.com', '+254114000321', '$2y$10$Hv7qRTjs3w9q.ywPvp4TVuT2qr.Ul7duFRfPnGHe1RrKn2.vP.dS2', NULL, '2023-03-31 08:24:05', '2023-03-31 08:24:05'),
(5, 'daisy', 'daisy@example.com', '+254114000422', '$2y$10$HP366/kXYxX8euKdcf1PD.O1vmb5PYHInWfpFOk7Y1.WFl0NyhpWW', NULL, '2023-03-28 23:11:52', '2023-03-28 23:11:52'),
(7, 'rick sanchez', 'ricksanchez@gmail.com', '+254737371083', '$2y$10$TjQJ6ZdbfWlpkf7P48iC0erqsh4stL7UYmeB4AbVZJnvCe.yKp2Vq', NULL, '2023-03-29 14:36:22', '2023-03-29 14:36:22'),
(8, 'johnson', 'johnson@example.com', '+254114000302', '$2y$10$CEwwjMUvG4MOCHpOEqDYcuh.DhEizPt3S4s72MQkV.Lh9aLbYetC6', NULL, '2023-03-31 07:13:03', '2023-03-31 07:13:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment`,`timestamp`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
