-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2021 at 05:55 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.1

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
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(1, 9, 1, '35955_Recent News.png', 'Programming Course', 'Introduction To PHP And MYSQL', '2021-03-30'),
(3, 7, 2, '39196_Edit Student.png', 'Html, Css Courses', 'Introduction To Front End Pathing', '2021-03-31'),
(6, 8, 2, '15578_Add Course.png', 'Gramer Course', 'Arabic Grammer Explaination', '2021-04-02'),
(7, 7, 1, '51604_All Teachers.png', 'Ai Course', 'Artifficial Intelligence Course', '2021-04-02');

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
(1, '77436_Students.png', 'Hello, World', 'Finally, The Website Has Done\r\nThanks God.', '2021-04-02');

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
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `student_id`, `email`, `grade`, `password`, `birth_date`, `date`) VALUES
(1, 'Ashraf Amr', '221', 'ashraf55@gmail.com', '7', '63abb446cc2c3d7332fce71f513d860b656de555', '2021-12-31', '2021-04-02'),
(2, 'Ashraf Elsaket', '222', 'ashraf22@gmail.com', '8', '10a51906d5e08f212aa06b190df4d30c8124ed78', '2004-06-06', '2021-04-02'),
(3, 'Menna Elsoht', '223', 'menna55@gmail.com', '9', 'bb8d916b93288c24137a20c5d99da8cc861bd348', '2005-09-04', '2021-04-02'),
(4, 'Mohamed Osama', '224', 'mohamed@gmail.com', '7', '0436b2b49085eb8f93a4180557859e7b4aa35a92', '2008-05-05', '2021-04-02');

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
(2, 'English'),
(3, 'Maths'),
(4, 'French'),
(5, 'Programming');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` int(11) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(150) NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `fb_link` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `name`, `image`, `subject`, `phone`, `email`, `password`, `gender`, `fb_link`, `date`) VALUES
(1, 'Mahmoud Reda', '46560_testi_02.png', 5, '01093668888', 'mahmodreda219@gmail.com', '0375c0ba89621a0a7552eedf34e67f5df3de2f99', 0, 'https://facebook.com/mahmodreda66', '2021-03-30'),
(2, 'Ahmed Saleh', '76864_avatar-01.jpg', 1, '01274445858', 'ahmedsaleh219@gmil.com', '3fff862ca2197989ab069e166a86e94b35a8fe89', 0, 'https://facebook.com/ahmedsaleh55', '2021-03-30'),
(3, 'Mohamed Farag', '', 2, '01244747899', 'hossam22arag@gmail.com', 'c08673fd85f9b213afb87151264120c84316302e', 0, '', '2021-04-02');

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
  ADD UNIQUE KEY `name` (`name`,`student_id`,`email`,`birth_date`);

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
  ADD UNIQUE KEY `name` (`name`,`subject`,`phone`,`email`),
  ADD KEY `subject` (`subject`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`teacher`) REFERENCES `teachers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`subject`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
