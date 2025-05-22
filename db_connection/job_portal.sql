-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 22, 2025 at 05:13 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
CREATE DATABASE IF NOT EXISTS `job_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `job_portal`;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `resume_link` varchar(255) NOT NULL,
  `status` enum('pending','reviewed','accepted','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `job_id`, `job_seeker_id`, `resume_link`, `status`, `applied_at`) VALUES
(68, 145, 20, 'uploads/resumes/resume_68205106158e85.43120966.pdf', 'accepted', '2025-05-11 01:25:58');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_name` varchar(255) NOT NULL,
  `skills` varchar(100) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `employer_id`, `title`, `description`, `category`, `salary`, `location`, `status`, `created_at`, `company_name`, `skills`, `education`) VALUES
(145, 20, 'PHP Developer', 'Build web applications using PHP and Laravel framework.', 'Web Development', 48000.00, 'Pasig', 'approved', '2025-05-11 05:31:02', 'JOHN MARC GABUYO', 'PHP, Laravel, MySQL', 'BS in Information Systems'),
(147, 20, 'JavaScript Developer', 'Develop dynamic interfaces using modern JavaScript frameworks.', 'IT', 60000.00, 'Region IV-A, Biñan', 'approved', '2025-05-11 01:01:54', 'JOHN MARC GABUYO', 'JavaScript, Vue.js, API Integration', 'BS in Computer Engineering'),
(148, 20, 'React Developer', 'Build scalable front-end applications using React.', 'Web Development', 55000.00, 'Mandaluyong', 'approved', '2025-05-11 05:31:02', 'JOHN MARC GABUYO', 'React, Redux, TypeScript', 'BS in Software Engineering'),
(150, 20, 'Junior Web Developer', 'Assist in building and maintaining websites under senior supervision.', 'Web Development', 30000.00, 'Davao', 'approved', '2025-05-11 05:31:02', 'JOHN MARC GABUYO', 'HTML, CSS, JavaScript', 'BS in IT or related'),
(153, 20, 'QA Tester - Web Applications', 'Test and ensure quality of web applications.', 'Quality Assurance', 35000.00, 'Pasig', 'approved', '2025-05-11 05:31:02', 'JOHN MARC GABUYO', 'Selenium, Manual Testing, Bug Tracking', 'BS in Computer Science'),
(154, 20, 'DevOps Engineer', 'Manage CI/CD pipelines and deployment automation.', 'Infrastructure', 60000.00, 'Taguig', 'approved', '2025-05-11 05:31:02', 'JOHN MARC GABUYO', 'Docker, Jenkins, AWS', 'BS in Computer Engineering'),
(155, 20, 'Mobile Web Developer', 'Create mobile-optimized websites and hybrid apps.', 'Web Development', 47000.00, 'Manila', 'approved', '2025-05-11 05:31:02', 'JOHN MARC GABUYO', 'Ionic, React Native, HTML5', 'BS in Software Engineering'),
(156, 20, 'Technical SEO Specialist', 'Improve website performance and search engine visibility.', 'Digital Marketing', 39000.00, 'Cebu', 'approved', '2025-05-11 05:31:02', 'JOHN MARC GABUYO', 'SEO, Google Analytics, HTML', 'BS in Marketing or IT'),
(157, 20, 'Web Content Manager', 'Oversee and update web content across company sites.', 'Content Management', 37000.00, 'Davao', 'approved', '2025-05-11 05:31:02', 'JOHN MARC GABUYO', 'CMS, WordPress, HTML', 'BS in Communications or IT'),
(158, 20, 'API Integration Developer', 'Integrate third-party APIs and ensure secure communication.', 'Software Development', 51000.00, 'Quezon City', 'approved', '2025-05-11 05:31:02', 'JOHN MARC GABUYO', 'REST, JSON, OAuth', 'BS in Computer Science'),
(160, 20, 'Manghihilot', 'Hello po', 'IT', 1500.00, 'Region IV-A, Biñan', 'approved', '2025-05-13 03:40:03', 'JOHN MARC GABUYO', 'magaling', 'KAHIT ANO NA');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_type` enum('admin','employer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_name` varchar(255) DEFAULT NULL,
  `company_tagline` varchar(255) DEFAULT NULL,
  `company_image` varchar(255) DEFAULT NULL,
  `company_description` text DEFAULT NULL,
  `company_cover` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `user_type`, `created_at`, `company_name`, `company_tagline`, `company_image`, `company_description`, `company_cover`) VALUES
(1, 'ADMIN', '123', 'admin@example.com', 'admin', '2025-03-24 01:40:57', NULL, NULL, NULL, NULL, NULL),
(20, 'SANTANDER', '123', 'santanderjoseph13@gmail.com', 'employer', '2025-05-10 09:44:41', 'JOHN MARC GABUYO', '\"BUSOG ANG SAYA SAYA DIBA\"', 'uploads/company_img/job.png', 'TechSolutions Inc. is a dynamic and innovative technology company specializing in custom software development, web and mobile solutions, and IT consulting services. Since its establishment in 2015, the company has built a strong reputation for delivering high-quality, scalable, and user-centric digital products tailored to the unique needs of businesses across various industries. With a team of experienced developers, designers, and project managers, TechSolutions Inc. embraces agile methodologies and cutting-edge technologies to ensure timely and effective results for its clients.\n\nDriven by a passion for excellence and a commitment to continuous improvement, TechSolutions Inc. aims to empower businesses through smart digital transformation. The company fosters a collaborative and forward-thinking environment where creativity and innovation thrive. Whether it\'s building e-commerce platforms, enterprise-level systems, or interactive web applications, TechSolutions Inc. stands as a trusted technology partner for organizations looking to stay ahead in the ever-evolving digital landscape.', 'uploads/company_cover/kk.webp'),
(21, 'JOSEPH', '$2y$10$WQCpKajMiaXYsPSLKtt.DeZ.tSOjnCP/CCRo0vAB.JwTHS6Sj4Le6', 'josephsantander911@gmail.com', 'employer', '2025-05-11 07:30:05', 'FUJI XIROX', 'PUYO', 'uploads/company_img/education.png', 'No overview provided.', 'uploads/company_cover/kk.webp'),
(25, 'TAGARRO', '$2y$10$3Js/Mlt//xUPamZdotfVCuoHzMW9xJ3o2Sg6ShGgt/mw.Io66MlWy', 'carlitotagarro0@gmail.com', 'employer', '2025-05-22 14:03:58', NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `job_seeker_id` (`job_seeker_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=161;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`job_seeker_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
