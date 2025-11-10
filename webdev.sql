-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2025 at 10:09 AM
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
-- Database: `webdev`
--

-- --------------------------------------------------------

--
-- Table structure for table `course_section`
--

CREATE TABLE `course_section` (
  `id` int(11) NOT NULL,
  `module_title` varchar(100) DEFAULT NULL,
  `section_title` varchar(100) DEFAULT NULL,
  `assessment_type` enum('quiz','profile') DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lectures`
--

CREATE TABLE `lectures` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `sequence` int(11) NOT NULL,
  `has_quiz` tinyint(1) DEFAULT 1,
  `has_practical` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`id`, `section_id`, `title`, `description`, `sequence`, `has_quiz`, `has_practical`) VALUES
(1, 1, 'Developer Environment Setup', 'Set up your web development environment for the first time. You will install VS Code, configure your preferred web browser, and learn how to use essential tools like Live Server and Git. This setup process is critical for a smooth coding journey.', 1, 1, 1),
(2, 1, 'Version Control Introduction', NULL, 2, 1, 0),
(3, 2, 'Introduction to HTML', NULL, 1, 1, 0),
(4, 2, 'Foundation and Semantics', NULL, 2, 1, 0),
(5, 2, 'Media Embeds', NULL, 3, 1, 0),
(6, 2, 'Tables Data', NULL, 4, 1, 0),
(7, 2, 'Forms Interactivity', NULL, 5, 1, 0),
(8, 2, 'Advanced HTML', NULL, 6, 1, 0),
(9, 3, 'Introduction to CSS', NULL, 1, 1, 0),
(10, 3, 'CSS Fundamentals', NULL, 2, 1, 0),
(11, 3, 'Layout Mastery', NULL, 3, 1, 0),
(12, 3, 'Responsive Design', NULL, 4, 1, 0),
(13, 3, 'Visual Effects and Animations', NULL, 5, 1, 0),
(14, 3, 'Modern CSS Features', NULL, 6, 1, 0),
(15, 3, 'Advanced Techniques', NULL, 7, 1, 0),
(16, 3, 'Real-World Projects', NULL, 8, 1, 0),
(17, 3, 'Browser DevTools', NULL, 9, 1, 0),
(18, 4, 'JS Fundamentals', NULL, 1, 1, 0),
(19, 4, 'Control Data Structures', NULL, 2, 1, 0),
(20, 4, 'Array Objects', NULL, 3, 1, 0),
(21, 4, 'The DOM Document Object Model', NULL, 4, 1, 0),
(22, 4, 'Local Storage and JSON', NULL, 5, 1, 0),
(23, 4, 'Debugging and Best Practices', NULL, 6, 1, 0),
(24, 5, 'Projects', NULL, 1, 1, 0),
(25, 5, 'Portfolio', NULL, 2, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `practical_submissions`
--

CREATE TABLE `practical_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `github_link` varchar(255) NOT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `scored` tinyint(1) DEFAULT 0,
  `score` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `practical_submissions`
--

INSERT INTO `practical_submissions` (`id`, `user_id`, `lecture_id`, `github_link`, `submitted_at`, `scored`, `score`, `feedback`) VALUES
(1, 1, 1, 'https://github.com', '2025-11-09 22:25:24', 1, 85, 'No Feedback');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `user_choice` char(1) NOT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `answered_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `optiona` text NOT NULL,
  `optionb` text NOT NULL,
  `optionc` text DEFAULT NULL,
  `optiond` text DEFAULT NULL,
  `answer` char(1) NOT NULL,
  `explanation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `lecture_id`, `question`, `optiona`, `optionb`, `optionc`, `optiond`, `answer`, `explanation`) VALUES
(1, 1, 'What is a code editor primarily used for?', 'PLaying music', 'Writing and editing code', 'Browsing and internet', NULL, 'B', 'A code editor lets you write programs. Music and browsing are not its features');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_submissions`
--

CREATE TABLE `quiz_submissions` (
  `user_id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `submission_time` datetime DEFAULT current_timestamp(),
  `answers` text NOT NULL,
  `score` int(11) DEFAULT 0,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `lecture_id` int(11) NOT NULL,
  `type` enum('pdf','video','link') NOT NULL,
  `icon` varchar(32) DEFAULT 'fa-file-alt',
  `title` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `link` varchar(255) NOT NULL,
  `action` varchar(32) DEFAULT 'download'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `lecture_id`, `type`, `icon`, `title`, `description`, `link`, `action`) VALUES
(1, 1, 'pdf', 'fa-file-alt', 'Setup Guide - VS Code & Browser', 'Step-by-step PDF for installing, configuring VS \'Code and\' Chrome', 'https://yourdomain.com/resources/dev-env-setup.pdf', 'Download Guide'),
(2, 1, 'video', 'fa-youtube', 'Setup walkthrough video', 'Watch a video on editor and browser configuration', 'https://youtube.com/watch?', 'Watch Video');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `sequence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `title`, `sequence`) VALUES
(1, 'Web Fundamentals', 1),
(2, 'HTML5: The Structure of the Web', 2),
(3, 'CSS: The Design of the Web', 3),
(4, 'JavaScript Basics: Logic and Interactivity', 4),
(5, 'Integrating Skills', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(1, 'Mukhiteee', 'mukhiteee@gmail.com', '$2y$10$G6NHGSJ59d9LUMOEH7P2xOjiokzrkxLxCaCwf4zwjeTs78H105wj2'),
(2, 'Sadiq Ibrahim', 'Sadiqabdulhamid@gmail.com', '$2y$10$GrtdL2HatIjxbdzEMRGyZeqhNYgXrWvFLt5.BVo9sihAUyuBnYVA6'),
(3, 'Abdulhamid Rodiya', 'diyah@gmail.com', '$2y$10$/tYPpd5nCISCakhx6CRWIukKcqWYihiNTRCGKLZSqMuJpPzopZ1Cy');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course_section`
--
ALTER TABLE `course_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `practical_submissions`
--
ALTER TABLE `practical_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lecture_id` (`lecture_id`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture_id` (`lecture_id`);

--
-- Indexes for table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lecture_id` (`lecture_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecture_id` (`lecture_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course_section`
--
ALTER TABLE `course_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `practical_submissions`
--
ALTER TABLE `practical_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lectures`
--
ALTER TABLE `lectures`
  ADD CONSTRAINT `lectures_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`);

--
-- Constraints for table `practical_submissions`
--
ALTER TABLE `practical_submissions`
  ADD CONSTRAINT `practical_submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `practical_submissions_ibfk_2` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`);

--
-- Constraints for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `quiz_submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_submissions`
--
ALTER TABLE `quiz_submissions`
  ADD CONSTRAINT `quiz_submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `quiz_submissions_ibfk_2` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`);

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lectures` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
