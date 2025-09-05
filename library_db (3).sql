-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 22, 2025 at 10:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$lf2ac4jzV.Gn5Vx0x1a6wOJQq9aXv9hOr17lIsfGVB68SxP7P1Fdm', '2025-08-18 06:27:27'),
(2, 'admin2', '$2y$10$hn7IVHG38Q7z9GZkqXM9n.T5nCuxWEFPp37ZY9gyQRCqy6Zs8yjcC', '2025-08-18 06:33:38'),
(3, 'neil1@gmail.com', '$2y$10$KcI0xr78kEozYBiK66EZ1O4ylNPSV8JFOO75GcoS2ZwixUnAi1PX6', '2025-08-19 02:18:04');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `author` varchar(200) DEFAULT NULL,
  `publisher` varchar(200) DEFAULT NULL,
  `year_pub` varchar(10) DEFAULT NULL,
  `category` varchar(120) DEFAULT NULL,
  `copies_total` int(11) NOT NULL DEFAULT 1,
  `copies_available` int(11) NOT NULL DEFAULT 1,
  `shelf_location` varchar(60) DEFAULT NULL,
  `status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `isbn`, `title`, `author`, `publisher`, `year_pub`, `category`, `copies_total`, `copies_available`, `shelf_location`, `status`, `created_at`, `updated_at`, `image`) VALUES
(5, '1', 'King Arthur', 'Roger Lancelyn Green', 'Simon &amp;amp;amp;amp; Schuster', '1485', '10', 10, 5, '1', 'available', '2025-08-20 00:22:28', '2025-08-20 06:43:37', 'uploads/books/book_68a517c1901d51.19982871.jpg'),
(6, '2', 'The Martian Novel', 'Andy Weir', 'Andy Weir', '2011', '10', 10, 8, '1', 'available', '2025-08-20 00:27:23', '2025-08-20 06:44:17', 'uploads/books/book_68a5166b4194e1.17015558.jpg'),
(7, '3', 'Introduction to Algorithms', 'Ronald Rivest', 'Ronald Rivest', '1989', '10', 10, 6, '1', 'available', '2025-08-20 00:34:54', '2025-08-22 02:49:36', 'uploads/books/book_68a5182e10ba14.18541794.webp'),
(8, '4', 'The C Programming Language', 'Brian W. Kernighan', 'Brian W. Kernighan', '2017', '10', 10, 8, '0', 'available', '2025-08-20 08:15:34', '2025-08-20 08:18:23', 'uploads/books/book_68a5842690c3f2.72864513.png');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_transactions`
--

CREATE TABLE `borrow_transactions` (
  `id` int(11) NOT NULL,
  `borrow_code` varchar(16) NOT NULL,
  `student_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `status` enum('requested','issued','returned','denied','overdue') NOT NULL DEFAULT 'requested',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `issued_at` datetime DEFAULT NULL,
  `due_at` datetime DEFAULT NULL,
  `returned_at` datetime DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `notes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_transactions`
--

INSERT INTO `borrow_transactions` (`id`, `borrow_code`, `student_id`, `book_id`, `qty`, `status`, `requested_at`, `issued_at`, `due_at`, `returned_at`, `image`, `notes`) VALUES
(9, '2B8KYU43', 1, 7, 2, 'returned', '2025-08-20 03:52:09', '2025-08-20 11:52:28', '2025-08-27 11:52:28', '2025-08-20 12:02:10', '', ''),
(10, 'GSQJT8DG', 3, 7, 1, 'returned', '2025-08-20 03:53:12', '2025-08-20 11:54:03', '2025-08-27 11:54:03', '2025-08-20 12:00:31', '', ''),
(11, 'AP9SDR5N', 1, 5, 1, 'issued', '2025-08-20 06:39:13', '2025-08-20 14:43:37', '2025-08-27 14:43:37', NULL, NULL, ''),
(12, 'KW5GMH29', 1, 6, 1, 'issued', '2025-08-20 06:44:11', '2025-08-20 14:44:17', '2025-08-27 14:44:17', NULL, NULL, ''),
(13, 'J3DNWTNM', 3, 8, 1, 'issued', '2025-08-20 08:18:02', '2025-08-20 16:18:23', '2025-08-27 16:18:23', NULL, NULL, ''),
(14, 'EW85VWXF', 1, 7, 1, 'requested', '2025-08-22 02:49:36', NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `student_no` varchar(50) NOT NULL,
  `rating_value` tinyint(4) NOT NULL CHECK (`rating_value` between 1 and 5),
  `review_comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `book_id`, `student_no`, `rating_value`, `review_comment`, `created_at`) VALUES
(1, 7, '202200968', 3, 'sas', '2025-08-22 07:48:39'),
(2, 5, '202200968', 3, 'goods', '2025-08-22 03:39:12'),
(3, 7, '202100769', 5, 'wowers', '2025-08-22 07:34:04'),
(4, 8, '202200968', 3, 'neil', '2025-08-22 07:49:04');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_no` varchar(30) NOT NULL,
  `name` varchar(120) NOT NULL,
  `course` varchar(80) DEFAULT NULL,
  `year_level` varchar(20) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_no`, `name`, `course`, `year_level`, `email`, `phone`, `status`, `created_at`) VALUES
(1, '202200968', 'John Mark M. ALCORIZA', 'BSINT', '4TH', 'alcorizatata@gmail.com', '09984973039', 'active', '2025-08-18 06:11:52'),
(2, '202200967', 'Neil', 'BSINT', '3rd', 'john@gmail.com', '0121828128', 'active', '2025-08-18 06:36:58'),
(3, '202100769', 'Neil', 'bsint', '4th year', 'amparadoneilabundo@gmail.com', '095536546654', 'active', '2025-08-20 03:13:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrow_transactions`
--
ALTER TABLE `borrow_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `borrow_code` (`borrow_code`),
  ADD KEY `status` (`status`),
  ADD KEY `fk_borrow_student` (`student_id`),
  ADD KEY `fk_borrow_book` (`book_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_no` (`student_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `borrow_transactions`
--
ALTER TABLE `borrow_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_transactions`
--
ALTER TABLE `borrow_transactions`
  ADD CONSTRAINT `fk_borrow_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_borrow_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
