-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2021 at 04:25 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smart_school`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `f_name` varchar(20) NOT NULL,
  `l_name` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `comment` text NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `f_name`, `l_name`, `email`, `phone`, `comment`, `date`) VALUES
(4, 'Mahmoud', 'Reda', 'mahmodreda219@gmail.com', '01093668025', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni eos assumenda dolor soluta iste quos, at, deleniti, repudiandae modi placeat deserunt! Minima quisquam corporis dolor eaque velit eius. Ipsa, cupiditate!', '2021-03-22 20:04:47'),
(5, 'Mohamed', 'Osama', 'mo_osama@hotmail.com', '0402441788', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni eos assumenda dolor soluta iste quos, at, deleniti, repudiandae modi placeat deserunt! Minima quisquam corporis dolor eaque velit eius. Ipsa, cupiditate!', '2021-03-22 20:05:11'),
(6, 'Hana', 'Elsayed', 'hana22_s@gmail.com', '01274448798', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni eos assumenda dolor soluta iste quos, at, deleniti, repudiandae modi placeat deserunt! Minima quisquam corporis dolor eaque velit eius. Ipsa, cupiditate!', '2021-03-22 20:05:46');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `teacher` int(11) NOT NULL,
  `image` varchar(250) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `grade`, `teacher`, `image`, `title`, `description`, `date`) VALUES
(6, 8, 2, '15578_Add Course.png', 'Gramer Course', 'Arabic Grammer Explaination', '2021-04-02'),
(9, 9, 3, '8321_a.jpg', 'Hello Test', 'Testing Number NULL', '2021-04-17');

-- --------------------------------------------------------

--
-- Table structure for table `courses_videos`
--

CREATE TABLE `courses_videos` (
  `id` int(11) NOT NULL,
  `link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `course` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `courses_videos`
--

INSERT INTO `courses_videos` (`id`, `link`, `course`, `title`, `description`, `date`) VALUES
(1, 'https://www.youtube.com/embed/H6ymYOM8gzU', 6, 'First Video', 'Hello First Video', '2021-05-06'),
(2, 'https://www.youtube.com/embed/QStpeLp25A4', 9, 'Second Video', 'Hello Second Video', '2021-05-01'),
(3, 'https://www.youtube.com/embed/hKq0YIKZju0', 6, 'End Of The Course', 'The Ending', '2021-05-06');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `at` datetime NOT NULL,
  `duration` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` int(11) NOT NULL,
  `teacher` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `name`, `at`, `duration`, `grade`, `subject`, `teacher`, `date`) VALUES
(1, 'Grammer Exam', '2021-04-21 07:00:00', '60', '8', 1, 2, '2021-04-15');

-- --------------------------------------------------------

--
-- Table structure for table `marks`
--

CREATE TABLE `marks` (
  `id` int(11) NOT NULL,
  `student` int(11) NOT NULL,
  `mark` float NOT NULL,
  `full_mark` float NOT NULL,
  `subject` int(11) NOT NULL,
  `teacher` int(11) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `marks`
--

INSERT INTO `marks` (`id`, `student`, `mark`, `full_mark`, `subject`, `teacher`, `date`) VALUES
(1, 5, 90, 100, 2, 4, '2021-04-09'),
(5, 5, 40, 50, 1, 4, '2021-05-07'),
(6, 5, 20, 100, 1, 4, '2021-04-21'),
(7, 4, 45, 100, 4, 4, '2021-04-21');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `image` varchar(200) NOT NULL,
  `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `image`, `title`, `description`, `date`) VALUES
(3, '44020_blog_1.jpg', 'New Year Has gone', 'Hello, New Years', '2021-04-08');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(80) CHARACTER SET utf8 NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `grade` varchar(2) NOT NULL,
  `password` varchar(150) NOT NULL,
  `birth_date` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 is activate account',
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `student_id`, `email`, `grade`, `password`, `birth_date`, `status`, `date`) VALUES
(1, 'Ashraf Amr', '221', 'ashraf55@gmail.com', '7', '63abb446cc2c3d7332fce71f513d860b656de555', '2021-12-31', 1, '2021-04-02'),
(3, 'Menna Elsoht', '223', 'menna55@gmail.com', '9', 'bb8d916b93288c24137a20c5d99da8cc861bd348', '2005-09-04', 1, '2021-04-02'),
(4, 'Mohamed Osama', '224', 'mohamed@gmail.com', '7', '0436b2b49085eb8f93a4180557859e7b4aa35a92', '2008-05-05', 0, '2021-04-02'),
(5, 'Mahmoud Reda', '044', 'mm22@mm.com', '8', '0375c0ba89621a0a7552eedf34e67f5df3de2f99', '2021-12-31', 0, '2021-04-19'),
(6, 'Ahmed Saleh', '445', 'ahmedsaleh22@gmail.com', '7', 'a9d28c502c3933142266af4cb8685ce66ca10254', '2008-02-28', 0, '2021-04-21'),
(7, 'mahmoud reda', '998', 'mahmoud99@gmail.com', '7', '0375c0ba89621a0a7552eedf34e67f5df3de2f99', '2021-01-01', 1, '2021-05-04');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject`) VALUES
(1, 'Arabic'),
(2, 'Maths'),
(3, 'Programming'),
(4, 'Chemistry');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` int(11) DEFAULT NULL,
  `phone` varchar(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `fb_link` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 is teacher 1 is admin',
  `visibility` tinyint(4) NOT NULL DEFAULT 1 COMMENT ' 0 is invisible, 1 is visible',
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `image`, `subject`, `phone`, `email`, `password`, `gender`, `fb_link`, `position`, `visibility`, `date`) VALUES
(2, 'Ahmed Saleh', '76864_avatar-01.jpg', 1, '01274445858', 'ahmedsaleh219@gmil.com', '3fff862ca2197989ab069e166a86e94b35a8fe89', 0, 'https://facebook.com/ahmedsaleh55', 0, 1, '2021-03-30'),
(3, 'Mohamed Farag', '', 2, '01244747899', 'hossam22@gmail.com', '0375c0ba89621a0a7552eedf34e67f5df3de2f99', 0, '', 0, 1, '2021-04-02'),
(4, 'Mahmoud Reda', '60049_a.jpg', NULL, '01093668025', 'mahmodreda219@gmail.com', '0375c0ba89621a0a7552eedf34e67f5df3de2f99', 0, 'https://facebook.com/mahmoudreda66', 1, 0, '2021-04-04'),
(5, 'Ahmed Hesham', '', 2, '01244747789', 'ahmed@gmail.com', '3fff862ca2197989ab069e166a86e94b35a8fe89', 0, '', 0, 1, '2021-04-14'),
(9, 'Teacher Name', '46911_a.jpg', 2, '01244557897', 'teacher@school.com', '0375c0ba89621a0a7552eedf34e67f5df3de2f99', 1, 'https://facebook.com/teacher', 0, 1, '2021-04-14'),
(10, 'Hothayfa Suliman', '', NULL, '020441145', 'hothayfa@gmail.com', 'dd94709528bb1c83d08f3088d4043f4742891f4f', 0, '', 1, 0, '2021-04-24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `f_name` (`f_name`,`l_name`,`email`,`phone`,`comment`) USING HASH;

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher` (`teacher`);

--
-- Indexes for table `courses_videos`
--
ALTER TABLE `courses_videos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `link` (`link`,`course`,`title`,`description`) USING HASH,
  ADD KEY `course` (`course`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject`),
  ADD KEY `teacher` (`teacher`);

--
-- Indexes for table `marks`
--
ALTER TABLE `marks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student` (`student`,`mark`,`subject`,`teacher`),
  ADD KEY `teacher` (`teacher`),
  ADD KEY `subject` (`subject`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `image` (`image`,`title`,`description`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`,`student_id`,`email`,`birth_date`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `name` (`name`,`subject`,`phone`,`email`),
  ADD KEY `subject` (`subject`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `courses_videos`
--
ALTER TABLE `courses_videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `marks`
--
ALTER TABLE `marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `courses_videos`
--
ALTER TABLE `courses_videos`
  ADD CONSTRAINT `courses_videos_ibfk_1` FOREIGN KEY (`course`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`subject`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exams_ibfk_2` FOREIGN KEY (`teacher`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `marks`
--
ALTER TABLE `marks`
  ADD CONSTRAINT `marks_ibfk_1` FOREIGN KEY (`student`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `marks_ibfk_2` FOREIGN KEY (`teacher`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `marks_ibfk_3` FOREIGN KEY (`subject`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`subject`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
