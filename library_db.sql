-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2025 at 10:55 AM
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
(2, 'admin2', '$2y$10$hn7IVHG38Q7z9GZkqXM9n.T5nCuxWEFPp37ZY9gyQRCqy6Zs8yjcC', '2025-08-18 06:33:38');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `isbn`, `title`, `author`, `publisher`, `year_pub`, `category`, `copies_total`, `copies_available`, `shelf_location`, `status`, `created_at`, `updated_at`) VALUES
(1, '9780553386790', 'The Martian', 'Andy Weir', 'Crown', '2014', 'Fiction', 5, 4, 'FIC-WEI', 'available', '2025-08-18 06:07:16', '2025-08-18 07:09:47'),
(2, '9780131103627', 'The C Programming Language', 'Kernighan & Ritchie', 'Prentice Hall', '1988', 'Computers', 3, 3, 'COMP-KR', 'available', '2025-08-18 06:07:16', '2025-08-18 07:11:03'),
(3, '9780262033848', 'Introduction to Algorithms', 'Cormen et al.', 'MIT Press', '2009', 'Computers', 2, 2, 'COMP-CLRS', 'available', '2025-08-18 06:07:16', '2025-08-18 06:35:38');

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
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_transactions`
--

INSERT INTO `borrow_transactions` (`id`, `borrow_code`, `student_id`, `book_id`, `qty`, `status`, `requested_at`, `issued_at`, `due_at`, `returned_at`, `notes`) VALUES
(1, '9KWKNAJW', 1, 3, 1, 'returned', '2025-08-18 06:12:04', '2025-08-18 08:34:36', '2025-08-25 08:34:36', '2025-08-18 08:35:38', ''),
(2, '5ZJFHTYP', 1, 3, 1, 'returned', '2025-08-18 06:29:00', '2025-08-18 08:34:17', '2025-08-25 08:34:17', '2025-08-18 08:35:26', ''),
(3, 'BE3SLP9R', 2, 2, 1, 'returned', '2025-08-18 06:37:52', '2025-08-18 08:38:09', '2025-08-25 08:38:09', '2025-08-18 08:38:36', ''),
(4, 'X4AF6JKW', 1, 2, 1, 'returned', '2025-08-18 07:01:17', '2025-08-18 09:02:17', '2025-08-25 09:02:17', '2025-08-18 09:04:59', ''),
(5, '6SVLXQVZ', 1, 1, 1, 'overdue', '2025-08-18 07:05:17', '2025-08-18 09:05:31', '2025-08-25 09:05:31', NULL, ''),
(6, '663HFSEW', 1, 2, 1, 'returned', '2025-08-18 07:07:51', '2025-08-18 15:08:06', '2025-08-25 15:08:06', '2025-08-18 09:08:38', ''),
(7, 'GF349UMG', 1, 1, 1, 'returned', '2025-08-18 07:08:53', '2025-08-18 09:09:06', '2025-08-25 09:09:06', '2025-08-18 15:09:47', ''),
(8, 'R8Y8BA8B', 2, 2, 1, 'returned', '2025-08-18 07:10:05', '2025-08-18 15:10:24', '2025-08-25 15:10:24', '2025-08-18 15:11:03', '');

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
(2, '202200967', 'Neil', 'BSINT', '3rd', 'john@gmail.com', '0121828128', 'active', '2025-08-18 06:36:58');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `borrow_transactions`
--
ALTER TABLE `borrow_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
