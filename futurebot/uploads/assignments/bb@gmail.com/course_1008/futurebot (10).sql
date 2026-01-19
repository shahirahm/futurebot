-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 12:33 PM
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
-- Database: `futurebot`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointed_list`
--

CREATE TABLE `appointed_list` (
  `id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `mentor_email` varchar(255) DEFAULT NULL,
  `student_name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `institute` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `status` enum('accepted','rejected') NOT NULL,
  `action_by` varchar(255) DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointed_list`
--

INSERT INTO `appointed_list` (`id`, `request_id`, `mentor_email`, `student_name`, `location`, `institute`, `subject`, `contact`, `status`, `action_by`, `action_time`) VALUES
(32, 0, 'mahi@gmail.com', 'maisha', 'lakecity', 'Uiu', 'C++', '01738915382', 'accepted', 'rafi', '2025-07-23 16:05:29');

-- --------------------------------------------------------

--
-- Table structure for table `careerpaths`
--

CREATE TABLE `careerpaths` (
  `path_id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `market_demand_score` int(11) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `education_level_required` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `career_milestones`
--

CREATE TABLE `career_milestones` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `recommended_for_skills` text DEFAULT NULL,
  `milestone_type` varchar(50) DEFAULT NULL,
  `link` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_profiles`
--

CREATE TABLE `company_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `started_year` varchar(10) DEFAULT NULL,
  `rating` varchar(10) DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_profiles`
--

INSERT INTO `company_profiles` (`id`, `user_id`, `company_name`, `started_year`, `rating`, `facilities`, `bio`, `location`, `photo`) VALUES
(1, 63, 'mmmmm', '2010', '4', ',,,oo', '...mm', 'Badda', 'uploads/company_photos/1753110665_WhatsApp Image 2025-07-10 at 12.22.07 AM.jpeg'),
(2, 65, 'samiha', '2010', 'm', 'mm', 'mm', 'mm', 'uploads/company_photos/1753113639_WhatsApp Image 2025-07-10 at 12.22.07 AM.jpeg'),
(3, 68, 'samiha', '2010', '4', 'mm', ',,,,,', ',,,', 'uploads/company_photos/1753193593_WhatsApp Image 2025-07-09 at 11.55.17 PM.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `connections`
--

CREATE TABLE `connections` (
  `connection_id` int(11) NOT NULL,
  `user_one` int(11) NOT NULL,
  `user_two` int(11) NOT NULL,
  `status` enum('pending','accepted','rejected','blocked') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accepted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `recommended_for_skills` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `duration` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `recommended_for_skills`, `link`, `created_at`, `duration`) VALUES
(1, 'Introduction to Python', 'A beginner-friendly course to get started with Python and logical problem-solving.', 'Python,Programming,Logic', NULL, '2025-07-23 11:05:29', NULL),
(2, 'Web Development Bootcamp', 'Covers HTML, CSS, JavaScript and modern frontend tools.', 'Web Development,HTML,CSS,JavaScript', NULL, '2025-07-23 11:05:29', NULL),
(3, 'Machine Learning Basics', 'Intro to ML concepts, models, and real-life applications.', 'AI & Machine Learning,Data Science,Python', NULL, '2025-07-23 11:05:29', NULL),
(4, 'Cybersecurity Fundamentals', 'Understand the basics of digital security and ethical hacking.', 'Cybersecurity,Networking,IT Security', NULL, '2025-07-23 11:05:29', NULL),
(5, 'Digital Marketing Essentials', 'Learn SEO, SEM, and social media strategies.', 'Digital Marketing,SEO,Marketing', NULL, '2025-07-23 11:05:29', NULL),
(6, 'Python for Beginners', 'Learn Python from scratch, ideal for beginners.', 'Python,Programming', NULL, '2025-07-23 11:05:29', NULL),
(7, 'Advanced Python', 'Deep dive into advanced Python concepts.', 'Python,Programming', NULL, '2025-07-23 11:05:29', NULL),
(8, 'Web Development Bootcamp', 'Covers HTML, CSS, JavaScript and modern frontend tools.', 'Web Development,HTML,CSS,JavaScript', NULL, '2025-07-23 11:05:29', NULL),
(9, 'Data Science Essentials', 'Fundamentals of Data Science and Analysis.', 'Data Science,Python,Machine Learning', NULL, '2025-07-23 11:05:29', NULL),
(10, 'Machine Learning Basics', 'Introduction to machine learning algorithms.', 'Machine Learning,Python', NULL, '2025-07-23 11:05:29', NULL),
(11, 'React for Beginners', 'Learn React JS and build dynamic web apps.', 'Web Development,React,JavaScript', NULL, '2025-07-23 11:05:29', NULL),
(12, 'Cybersecurity Fundamentals', 'Basics of cybersecurity and ethical hacking.', 'Cybersecurity,Networking', NULL, '2025-07-23 11:05:29', NULL),
(13, 'AWS Cloud Practitioner', 'Introduction to AWS Cloud services.', 'Cloud Computing,AWS', NULL, '2025-07-23 11:05:29', NULL),
(14, 'Mobile App Development with Kotlin', 'Build Android apps using Kotlin.', 'Mobile Development,Kotlin', NULL, '2025-07-23 11:05:29', NULL),
(15, 'Docker & Kubernetes', 'Containerize applications and orchestrate them.', 'DevOps,Docker,Kubernetes', NULL, '2025-07-23 11:05:29', NULL),
(16, 'SQL for Beginners', 'Learn SQL querying and database management.', 'Databases,SQL', NULL, '2025-07-23 11:05:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `progress` int(11) DEFAULT 0,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_enrollments`
--

INSERT INTO `course_enrollments` (`id`, `user_email`, `course_id`, `progress`, `enrolled_at`) VALUES
(1, 'ayaadd@gmail.com', 2, 0, '2025-07-23 10:50:09'),
(2, 'ayaadd@gmail.com', 11, 0, '2025-07-23 10:54:36'),
(3, 'ayaadd@gmail.com', 8, 0, '2025-07-23 10:54:40'),
(4, 'ayaadd@gmail.com', 7, 0, '2025-07-23 11:27:25'),
(5, 'ayaadd@gmail.com', 9, 0, '2025-07-23 11:27:35'),
(6, 'ayaadd@gmail.com', 3, 0, '2025-07-23 11:31:50'),
(7, 'ayaadd@gmail.com', 6, 0, '2025-07-23 11:38:06'),
(8, 'ayaadd@gmail.com', 1, 0, '2025-07-23 11:41:47');

-- --------------------------------------------------------

--
-- Table structure for table `course_feedback`
--

CREATE TABLE `course_feedback` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_lessons`
--

CREATE TABLE `course_lessons` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lesson_title` varchar(255) NOT NULL,
  `lesson_content` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `lesson_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_lessons`
--

INSERT INTO `course_lessons` (`id`, `course_id`, `lesson_title`, `lesson_content`, `video_url`, `lesson_order`) VALUES
(1, 1, 'Introduction to Python', 'Welcome to Python programming. In this lesson, you will learn the basics.', 'https://youtu.be/_uQrJ0TkZlc', 1),
(2, 1, 'Variables and Data Types', 'Learn about variables, data types, and how to use them.', '', 2),
(3, 1, 'Control Flow', 'Conditional statements and loops in Python.', 'https://youtu.be/6iF8Xb7Z3wQ', 3);

-- --------------------------------------------------------

--
-- Table structure for table `enrolled_courses`
--

CREATE TABLE `enrolled_courses` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `progress` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hire_requests`
--

CREATE TABLE `hire_requests` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ignored_mentors`
--

CREATE TABLE `ignored_mentors` (
  `user_id` int(11) NOT NULL,
  `ignored_mentor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ignored_mentors`
--

INSERT INTO `ignored_mentors` (`user_id`, `ignored_mentor_id`) VALUES
(67, 14),
(67, 15),
(67, 16),
(67, 17),
(67, 18),
(67, 20),
(67, 22),
(67, 25),
(67, 32),
(67, 33),
(67, 35),
(67, 39),
(67, 41),
(67, 42),
(67, 44),
(67, 45),
(67, 47),
(67, 50),
(67, 51),
(67, 66),
(67, 67);

-- --------------------------------------------------------

--
-- Table structure for table `interview_answers`
--

CREATE TABLE `interview_answers` (
  `answer_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text DEFAULT NULL,
  `score` decimal(3,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interview_questions`
--

CREATE TABLE `interview_questions` (
  `question_id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('behavioral','technical','scenario') DEFAULT 'technical'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interview_questions`
--

INSERT INTO `interview_questions` (`question_id`, `category`, `question_text`, `question_type`) VALUES
(1, 'Software Engineering', 'Tell me about a time you handled a conflict in a team project.', 'behavioral'),
(2, 'Software Engineering', 'Explain the difference between abstraction and encapsulation.', 'technical'),
(3, 'Marketing', 'How would you handle a campaign that failed to meet expectations?', 'scenario');

-- --------------------------------------------------------

--
-- Table structure for table `interview_sessions`
--

CREATE TABLE `interview_sessions` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mentor_id` int(11) DEFAULT NULL,
  `started_at` datetime DEFAULT current_timestamp(),
  `ended_at` datetime DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobapplications`
--

CREATE TABLE `jobapplications` (
  `application_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobapplications`
--

INSERT INTO `jobapplications` (`application_id`, `job_id`, `applicant_id`, `cover_letter`, `status`, `applied_at`) VALUES
(1, 10, 5, NULL, 'pending', '2025-07-20 20:53:44');

-- --------------------------------------------------------

--
-- Table structure for table `jobposts`
--

CREATE TABLE `jobposts` (
  `job_id` int(11) NOT NULL,
  `poster_id` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `company` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `application_link` varchar(255) DEFAULT NULL,
  `job_type` enum('Full-time','Part-time','Internship','Freelance') DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `salary_range` varchar(50) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL,
  `required_skills` text DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `salary` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobposts`
--

INSERT INTO `jobposts` (`job_id`, `poster_id`, `title`, `company`, `description`, `application_link`, `job_type`, `location`, `salary_range`, `company_email`, `required_skills`, `deadline`, `created_at`, `salary`) VALUES
(7, 1, 'Junior Web Developer', 'Tech Solutions', 'Entry level web developer role working with React and Node.js.', NULL, 'Full-time', 'New York', '$40,000 - $50,000', NULL, 'Web Development,JavaScript,React', '2025-12-31', '2025-07-19 20:16:56', NULL),
(8, 1, 'Data Analyst Intern', 'DataCorp', 'Internship role for data analysis with Python and SQL skills.', NULL, 'Internship', 'Remote', '$15/hr', NULL, 'Python,Data Analysis,SQL', '2025-11-30', '2025-07-19 20:16:56', NULL),
(9, 1, 'Cloud Support Engineer', 'CloudX', 'Support engineer role in cloud infrastructure with AWS.', NULL, 'Full-time', 'San Francisco', '$60,000 - $70,000', NULL, 'AWS,Cloud Computing,DevOps', '2025-10-31', '2025-07-19 20:16:56', NULL),
(10, 1, 'Junior Web Developer', 'Tech Solutions', 'Entry level web developer role working with React and Node.js.', NULL, 'Full-time', 'New York', '$40,000 - $50,000', NULL, 'Web Development,JavaScript,React', '2025-12-31', '2025-07-19 20:18:17', NULL),
(11, 1, 'Data Analyst Intern', 'DataCorp', 'Internship role for data analysis with Python and SQL skills.', NULL, 'Internship', 'Remote', '$15/hr', NULL, 'Python,Data Analysis,SQL', '2025-11-30', '2025-07-19 20:18:17', NULL),
(12, 1, 'Cloud Support Engineer', 'CloudX', 'Support engineer role in cloud infrastructure with AWS.', NULL, 'Full-time', 'San Francisco', '$60,000 - $70,000', NULL, 'AWS,Cloud Computing,DevOps', '2025-10-31', '2025-07-19 20:18:17', NULL),
(14, 4, 'Junior Web Developer', 'TechWave Ltd.', 'Looking for a passionate junior developer to join our web team.', NULL, 'Full-time', 'Dhaka', '15,000-25,000 BDT', 'samihamaisha231@gmail.com', 'HTML, CSS, JavaScript, PHP', '2025-08-31', '2025-07-20 08:04:32', NULL),
(15, 3, 'Junior Web Developer', 'TechWave Ltd.', 'Looking for a passionate junior developer to join our web team.', NULL, 'Full-time', 'Dhaka', '15,000-25,000 BDT', 'samihamaisha231@gmail.com', 'HTML, CSS, JavaScript, PHP', '2025-08-31', '2025-07-20 08:06:32', NULL),
(16, 4, 'Junior Web Developer', 'TechWave Ltd.', 'Looking for a passionate junior developer to join our web team.', NULL, '', 'Dhaka', '15,000-25,000 BDT', 'samihamaisha231@gmail.com', 'HTML, CSS, JavaScript, PHP', '2025-08-31', '2025-07-20 08:08:50', NULL),
(19, 18, 'WebDevoloping', 'WebDevelop', 'Provide best service to customers', 'nmsk', 'Full-time', 'Badda.Dhaka', '5000000', 'contact@futurebot.com', 'WebDevoloping', '2025-02-22', '2025-07-20 05:52:41', NULL),
(20, 18, 'customer service', 'WebDevelop', 'Provide best service to customers', 'nmsk', 'Part-time', 'Dhaka', '10000-15000', 'contact@futurebot.com', 'English', '2025-02-22', '2025-07-20 05:59:23', NULL),
(23, 33, 'WebDevoloping', 'WebDevelop', 'vv', 'nmsk', 'Full-time', 'Badda.Dhaka', '10000-15000', 'rcifiirmmnmm@gmail.com', 'English', '0000-00-00', '2025-07-20 14:10:37', NULL),
(24, 1, 'Frontend Developer', '', 'Develop UI components', NULL, NULL, 'Dhaka', NULL, NULL, NULL, NULL, '2025-07-20 20:51:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_posts`
--

CREATE TABLE `job_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `skills` text DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_posts`
--

INSERT INTO `job_posts` (`id`, `user_id`, `job_title`, `description`, `location`, `requirements`, `created_at`, `skills`, `deadline`, `title`) VALUES
(67, 68, ',,,,', ',,,', ',,,', NULL, '2025-07-22 14:47:53', ',,,', '0888-08-08', ''),
(68, 68, ',,,,', 'm', ',,,', NULL, '2025-07-22 14:48:02', ',,,', '0888-08-08', ''),
(69, 68, ',,,,', ',,,', ',,,', NULL, '2025-07-22 14:52:55', ',,,', '0888-08-08', ''),
(70, 68, 'MyProject', ',,', ',,,,,,mmm', NULL, '2025-07-22 14:53:52', 'programming', '0000-00-00', ''),
(71, 68, 'MyProject', 'bbmmnnbbvvvvvv', ',,,,,,mmm', NULL, '2025-07-22 15:02:10', 'programming', '3455-01-01', ''),
(72, 68, 'MyProject', 'mm', ',,,,,,mmm', NULL, '2025-07-22 15:11:00', 'programming', '3455-01-01', ''),
(74, 73, '', 'okokokok', 'Badda.Dhaka', ',,,,,', '2025-07-23 16:04:44', NULL, NULL, 'CustomerService');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentors`
--

CREATE TABLE `mentors` (
  `mentor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `expertise_skills` text NOT NULL,
  `bio` text DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `profile_image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentors`
--

INSERT INTO `mentors` (`mentor_id`, `name`, `expertise_skills`, `bio`, `contact_info`, `profile_image_url`) VALUES
(1, 'Alice Johnson', 'Web Development,JavaScript,React,Node.js', 'I guide beginners in web development. Start by mastering HTML, CSS, and JS.', 'alice@example.com', NULL),
(2, 'Bob Smith', 'Python,Machine Learning,Data Analysis', 'I help students get into AI and data science careers. Build strong Python skills and practice projects.', 'bob@example.com', NULL),
(3, 'Cathy Lee', 'Cloud Computing,AWS,DevOps', 'I advise on cloud careers. Start with AWS fundamentals and certifications.', 'cathy@example.com', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mentor_availability`
--

CREATE TABLE `mentor_availability` (
  `availability_id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentor_details`
--

CREATE TABLE `mentor_details` (
  `mentor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `started_year` year(4) NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentor_details`
--

INSERT INTO `mentor_details` (`mentor_id`, `user_id`, `company_name`, `started_year`, `rating`, `facilities`, `bio`, `location`, `profile_pic`, `created_at`, `updated_at`) VALUES
(1, 18, 'WebDevelop', '2010', NULL, 'Everthing', NULL, 'Badda.Dhaka', NULL, '2025-07-20 09:24:57', '2025-07-20 09:42:26'),
(2, 33, 'WebDevelop', '2010', NULL, 'mm', NULL, 'Badda.Dhaka', NULL, '2025-07-20 17:35:47', '2025-07-20 17:35:47'),
(3, 42, 'samiha', '2010', 5.0, 'mmm', 'mmm', 'Badda.Dhaka', NULL, '2025-07-20 22:01:55', '2025-07-20 22:01:55'),
(4, 42, 'samiha', '2010', 5.0, 'mm', 'mm', 'Badda.Dhaka', NULL, '2025-07-20 22:04:58', '2025-07-20 22:04:58'),
(5, 42, 'samiha', '2010', 5.0, 'm', 'm', 'Badda.Dhaka', NULL, '2025-07-20 22:43:41', '2025-07-20 22:43:41'),
(6, 51, 'samiha', '2010', 5.0, 'mm', 'mm', 'Badda.Dhaka', NULL, '2025-07-21 08:34:25', '2025-07-21 08:34:25'),
(7, 51, 'samiha', '2010', 5.0, 'mm', 'mm', 'Badda.Dhaka', NULL, '2025-07-21 09:04:18', '2025-07-21 09:04:18'),
(8, 51, 'samiha', '2010', 5.0, 'mm', 'mm', 'Badda.Dhaka', NULL, '2025-07-21 09:04:43', '2025-07-21 11:49:32'),
(9, 51, 'samiha', '2010', 5.0, 'mm', 'mm', 'Badda.Dhaka', NULL, '2025-07-21 09:14:18', '2025-07-21 09:14:18'),
(10, 51, 'samiha', '2010', 5.0, 'mm', 'mm', 'Badda.Dhaka', NULL, '2025-07-21 09:25:55', '2025-07-21 09:25:55'),
(11, 66, 'samiha', '2010', 5.0, ',,,', ',,', 'Badda.Dhaka', NULL, '2025-07-21 16:42:24', '2025-07-21 16:42:24'),
(12, 67, 'samiha', '2010', 5.0, 'Everything as demand', 'Most renewed company with 5 star review', 'Badda.Dhaka', NULL, '2025-07-22 09:35:03', '2025-07-22 09:35:03');

-- --------------------------------------------------------

--
-- Table structure for table `mentor_hires`
--

CREATE TABLE `mentor_hires` (
  `hire_id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `hire_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentor_profiles`
--

CREATE TABLE `mentor_profiles` (
  `mentor_id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `started_year` year(4) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `rating` float DEFAULT 0,
  `facilities` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `trade_license` varchar(100) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentor_requests`
--

CREATE TABLE `mentor_requests` (
  `id` int(11) NOT NULL,
  `mentor_email` varchar(255) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `institute` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentor_requests`
--

INSERT INTO `mentor_requests` (`id`, `mentor_email`, `student_name`, `location`, `institute`, `subject`, `contact`, `created_at`) VALUES
(26, 'sm11@gmail.com', 'maisha', 'lakecity', 'Uiu', 'C++', '01738915382', '2025-07-22 13:12:16'),
(27, 'mahi@gmail.com', 'maisha', 'lakecity', 'Uiu', 'C++', '01738915382', '2025-07-23 16:05:04');

-- --------------------------------------------------------

--
-- Table structure for table `mentor_student_chats`
--

CREATE TABLE `mentor_student_chats` (
  `chat_id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sender_role` enum('mentor','student','bot') NOT NULL,
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentor_student_relationships`
--

CREATE TABLE `mentor_student_relationships` (
  `relationship_id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `started_at` datetime DEFAULT current_timestamp(),
  `ended_at` datetime DEFAULT NULL,
  `status` enum('active','completed','terminated') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentor_student_sessions`
--

CREATE TABLE `mentor_student_sessions` (
  `session_id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `scheduled_time` datetime NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentor_suggestions`
--

CREATE TABLE `mentor_suggestions` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `rating` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentor_suggestions`
--

INSERT INTO `mentor_suggestions` (`id`, `email`, `company_name`, `location`, `rating`) VALUES
(1, 'tarriiqgb@gmail.com', 'samiha', 'Badda.Dhaka', '5'),
(2, 'tarriiqgb@gmail.com', 'samiha', 'Badda.Dhaka', '5'),
(3, 'ak34@gmail.com', 'samiha', 'Badda.Dhaka', '5'),
(4, 'ak34@gmail.com', 'samiha', 'Badda.Dhaka', '5'),
(5, 'ak34@gmail.com', 'samiha', 'Badda.Dhaka', '5'),
(6, 'ak34@gmail.com', 'samiha', 'Badda.Dhaka', '5'),
(7, 'ak34@gmail.com', 'samiha', 'Badda.Dhaka', '5'),
(8, 'sampg9110@gmail.com', 'samiha', 'Badda.Dhaka', '5'),
(9, 'sm11@gmail.com', 'samiha', 'Badda.Dhaka', '5');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `skill_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`skill_id`, `skill_name`) VALUES
(20, 'Agile Methodology'),
(10, 'Artificial Intelligence'),
(41, 'AWS'),
(42, 'Azure'),
(14, 'Big Data'),
(12, 'Blockchain Development'),
(27, 'C Programming'),
(26, 'C++ Programming'),
(6, 'Cloud Computing'),
(13, 'Computer Vision'),
(18, 'Content Writing'),
(5, 'Cybersecurity'),
(3, 'Data Analysis'),
(15, 'Data Visualization'),
(22, 'Database Management'),
(9, 'DevOps'),
(16, 'Digital Marketing'),
(35, 'Django'),
(40, 'Docker'),
(47, 'Ethical Hacking'),
(32, 'Express.js'),
(44, 'Firebase'),
(36, 'Flask'),
(45, 'Git & GitHub'),
(43, 'Google Cloud Platform'),
(25, 'Java Programming'),
(28, 'JavaScript'),
(39, 'Kubernetes'),
(34, 'Laravel'),
(2, 'Machine Learning'),
(7, 'Mobile App Development'),
(11, 'Natural Language Processing'),
(46, 'Networking'),
(31, 'Node.js'),
(24, 'NoSQL'),
(48, 'Penetration Testing'),
(33, 'PHP'),
(19, 'Project Management'),
(51, 'Public Speaking'),
(1, 'Python Programming'),
(38, 'PyTorch'),
(29, 'React JS'),
(21, 'Scrum'),
(17, 'SEO'),
(50, 'Soft Skills'),
(23, 'SQL'),
(49, 'Technical Writing'),
(37, 'TensorFlow'),
(8, 'UI/UX Design'),
(30, 'Vue JS'),
(4, 'Web Development');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `institution_name` varchar(100) DEFAULT NULL,
  `gpa` float DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `selected_careers` text DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suggestions`
--

CREATE TABLE `suggestions` (
  `suggestion_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `path_id` int(11) NOT NULL,
  `suggestion_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('student','mentor','admin','guest') NOT NULL DEFAULT 'student',
  `bio` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `profile_visibility` enum('public','friends_only','private') DEFAULT 'public',
  `company_name` varchar(150) DEFAULT NULL,
  `started_year` varchar(10) DEFAULT NULL,
  `rating` varchar(5) DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `full_name`, `skills`, `institution`, `gpa`, `email`, `password_hash`, `role`, `bio`, `profile_pic`, `registered_at`, `last_login`, `is_active`, `is_deleted`, `verification_status`, `profile_visibility`, `company_name`, `started_year`, `rating`, `facilities`, `location`) VALUES
(1, 'rifat', NULL, NULL, NULL, NULL, 'samihamaisha232@gmail.com', '$2y$10$quM6yRJy/uO6vZ1nAuZUve5wOJoiFdrgfgG04pzBokYnCEGkQtSJG', 'student', NULL, NULL, '2025-07-19 17:37:04', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(3, 'samiha', NULL, NULL, NULL, NULL, 'samihamaisha231@gmail.com', '$2y$10$c/ucyQX98cJN4PSIqaUfResZoQkzmABSfqw4GOLyG0eUbBXY4fRFO', 'student', NULL, NULL, '2025-07-19 19:20:13', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(4, 'sm', 'Samiha Akter Maisha', 'programming', 'Uiu', 3.00, 'testuser@gmail.com', '$2y$10$Z5Xx7Hu9jzm2GVMBH0yc7uhWD./wJBf/WyIpb1haKY.G5KHF8tCCO', 'student', NULL, NULL, '2025-07-19 19:39:06', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(5, 'ssmm', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, PHP', 'Uiu', 3.00, 'testuser1@gmail.com', '$2y$10$3qALFMHZTVwo/dECmtj6keAopabvavUc3y34cZgeMpuYsDjmc1dRu', 'student', NULL, NULL, '2025-07-19 19:46:05', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(6, 'sm44', NULL, NULL, NULL, NULL, 'testuser44@gmail.com', '$2y$10$/qy2P4ZP50GnItqAboiVe.p5Dint1bhIBJ43hwux548S8p4FjVVXK', 'student', NULL, NULL, '2025-07-19 20:04:03', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(7, 'sm444', NULL, NULL, NULL, NULL, 'testuser444@gmail.com', '$2y$10$vYH9hj8vZkG4.H5mLTjOwuQB8Idcy21KRna9aXBxEx6RCjjN1.6g6', 'student', NULL, NULL, '2025-07-19 20:05:18', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(8, 'sm4449', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, SQL', 'Uiu', 3.00, 'testuser4444@gmail.com', '$2y$10$RRi54vuowhmrT/IWAf3K4.PSNfoWMZ781NvgfGxb2fj6iyZIdRvKG', 'student', NULL, NULL, '2025-07-19 20:06:28', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(9, 'sm44490', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Java, C++, JavaScript, React, PHP, SQL, Machine Learning, Deep Learning', 'Uiu', 3.00, 'testuser44044@gmail.com', '$2y$10$BR54QFvUXdUu.7lpbQ3NWOxJkxQ2umdKcsQWAzU9/Cik77yQTSi3W', 'student', NULL, NULL, '2025-07-19 20:19:59', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(10, 'jisha', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, React, Node.js, SQL, NoSQL', 'Uiu', 3.00, 'jisha@gmail.com', '$2y$10$vhgVpHFdW8oGYcBEQzBXeuo2lA/gGPXAt69xKBYusoZMoUImem8NC', 'student', NULL, NULL, '2025-07-19 20:34:16', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(11, 'jisha1', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Java, C++, JavaScript, React, PHP, SQL, Machine Learning, Deep Learning', 'Uiu', 3.00, 'jisha1@gmail.com', '$2y$10$K0SPfb.D0kWe6qL5V611Jexh/a8sLpfcfFKgBDhx/eob1oZZcbIHi', 'student', NULL, NULL, '2025-07-19 20:47:04', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(12, 'jisha111', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Java, C++, JavaScript, PHP, SQL, Machine Learning, Deep Learning, UI/UX Design', 'Uiu', 3.00, 'jisha11@gmail.com', '$2y$10$4ZTiwbs1nOxArZYc3zpVPu0soGojI4Q91HiE0cVkoDDUcgvuN7iu.', 'student', NULL, NULL, '2025-07-19 21:27:06', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(13, 'jisha1111', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, PHP', 'Uiu', 3.00, 'jisha111@gmail.com', '$2y$10$YVs6i5ibgo2XfZ0WMfYRxe1e/HrJjFU./gSmbM53qMUxNmMwQ8bXS', 'student', NULL, NULL, '2025-07-20 08:16:48', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(14, 'jisha11111', NULL, NULL, NULL, NULL, 'jisha1111@gmail.com', '$2y$10$ce7VfN65OrQtuzqmCGnfoOfcZ0bWI00OyKB8Acj8btBm1NGGk3zZm', 'mentor', NULL, NULL, '2025-07-20 08:51:19', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(15, 'mai1', NULL, NULL, NULL, NULL, 'tariq34@gmail.com', '$2y$10$K0SX6tChNDmAfJ9cWk7FJ.RBOhW06pcOrdcQCxkd88avq.FyPZt1W', 'mentor', NULL, NULL, '2025-07-20 09:01:07', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(16, 'jisha11118', NULL, NULL, NULL, NULL, 'jisha1181@gmail.com', '$2y$10$M6pBvPDQG9KwUKZnoOeaV.KyDkR3x5Or.JvUrkXHZ1QpgEDv4h6AK', 'mentor', NULL, NULL, '2025-07-20 09:06:09', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(17, 'Samiha00', NULL, NULL, NULL, NULL, 'testuser00@gmail.com', '$2y$10$OSP2m3GQjhlIcVUe2dC.IeLmfZ6BamTYBOa2rjnfzLvTVoF8ghChO', 'mentor', NULL, NULL, '2025-07-20 09:18:42', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(18, 'k', 'Samiha Akter Maisha', NULL, NULL, NULL, 'tariq7@gmail.com', '$2y$10$.fcg9AGm0t1f.EGzs1mST.oWVTAMzPLz3D8ydXlCrN0nIaMlVXi2S', 'mentor', 'We provide the best services', 'uploads/profile_pics/18_1753004145.jpeg', '2025-07-20 09:22:45', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka'),
(19, 'rifat000@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, PHP', 'Uiu', 3.00, 'samihamaisha2320@gmail.com', '$2y$10$M9vo0nCWl98ijtLvNbulzum/uyBg5QcMxi4Lbotfdt4PYL0Nm9aoK', 'student', NULL, NULL, '2025-07-20 09:40:27', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(20, 'Samihap', NULL, NULL, NULL, NULL, 'testuserm@gmail.com', '$2y$10$zmSAPqRZUsQlheiyuTKD6.zbsXM56ZokkHf9ia9Gzor49CpN/5/XO', 'mentor', 'mm', NULL, '2025-07-20 09:41:53', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka'),
(21, 'rifatm@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, PHP', 'Uiu', 3.00, 'samihamais0@gmail.com', '$2y$10$eTPipCNcjlVoT2BRKrYZu.54SU9oZGe9dv.J1dC2CeFaL8k3fhWq6', 'student', NULL, NULL, '2025-07-20 09:44:05', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(22, 'rifatms@gmail.com', NULL, NULL, NULL, NULL, 'samihamaiss0@gmail.com', '$2y$10$x4z7NIl3GCwAvTCqujQ5G.9d.n3Z4R4019i6GTuxrapQTaO.6M1cG', 'mentor', 'nn', NULL, '2025-07-20 09:51:59', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka'),
(23, 'rifatmsn@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, PHP', 'Uiu', 3.00, 'samihamaissp0@gmail.com', '$2y$10$AdflNttsMRANHqyMXu2C3.2/7PjG0Lkg.DYgfdiyRSIfReRuiAe5G', 'student', NULL, NULL, '2025-07-20 09:53:04', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(24, 'rcifat@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, NoSQL', 'Uiu', 3.00, 'ssamihamais0@gmail.com', '$2y$10$kTI6V9Raen8HjMrKibi9Z.A2FTRfoBrpkTSr901ylZjdpFfdvSSDC', 'student', NULL, NULL, '2025-07-20 09:57:27', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(25, 'rcifatn@gmail.com', NULL, NULL, NULL, NULL, 'ssamiihamais0@gmail.com', '$2y$10$jQX/noIrjun/37lwfQSr3ujZY8cfSPIhlsxy83KyLxxtsqYZOAeay', 'mentor', 'm', NULL, '2025-07-20 09:58:45', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka'),
(26, 'rcifatvn@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, SQL', 'Uiu', 3.00, 'ssamiihamvais0@gmail.com', '$2y$10$kVa8CyYYEOy7Sto6E/C7q.HeoVrEImEA3XTTWKNYbQLswchpq1zMS', 'student', NULL, NULL, '2025-07-20 09:59:57', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(27, 'rcifatnmm@gmail.com', NULL, NULL, NULL, NULL, 'ssamniihamais0@gmail.com', '$2y$10$k2uIl/UD8dxgA0wVvNaE/u0rT7vc0f5HXY2ENCeuN.RhzY/nTIvSK', 'student', NULL, NULL, '2025-07-20 12:15:11', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(28, 'rcifratnmm@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, SQL', 'Uiu', 3.00, 'ssarmniihamais0@gmail.com', '$2y$10$gwb/HdF13tsXosRCgQFOFOI7DE0GQZRN7MRDnQ/4dN.cwgSjt3GAO', 'student', NULL, NULL, '2025-07-20 12:18:15', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(29, 'rcifrmm@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, Cloud Computing', 'Uiu', 3.00, 's0@gmail.com', '$2y$10$wVxLu0n84.YBFqJTvYa30uM3.ZKTf2sMHDyYTWVqYWwU9Pen1xgSu', 'student', NULL, NULL, '2025-07-20 12:53:04', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(30, 'rcifrmmm@gmail.com', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 's00@gmail.com', '$2y$10$fVBCdMj1IoNTaxhV3RInQu3SfM2HQ1rVnJrG9CsHBFIqP023J0zkm', 'student', NULL, NULL, '2025-07-20 14:12:43', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(31, 'rcifrmnmm@gmail.com', NULL, NULL, NULL, NULL, 's000@gmail.com', '$2y$10$bSUq6bjtDwFtsvF6eLZKrOgv0ajcb27rWCxN/mq2JfT6isbKEfMMi', 'student', NULL, NULL, '2025-07-20 14:51:22', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(32, 'rcifrmmnmm@gmail.com', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 's100@gmail.com', '$2y$10$.Lg073iJVAbj406KNGe6AeBv4map6XBhETmEZU6fA0BsBN/y9CTJe', 'mentor', 'nh', NULL, '2025-07-20 14:51:53', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka'),
(33, 'rcifiirmmnmm@gmail.com', 'Samiha Akter Maisha', NULL, NULL, NULL, 's11100@gmail.com', '$2y$10$KOq1HGIjowRaYDtIjX4SauozXhBX2xgWJ0Ulm.y3oHVTTz2cd5Why', 'mentor', 'nnn', 'uploads/profile_pics/33_1753032947.jpeg', '2025-07-20 15:42:25', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka'),
(34, 'rifatx@gmail.com', 'Samiha Akter Maisha', 'Java, C#', 'Uiu', 3.00, 'tariqx@gmail.com', '$2y$10$Vjs/pgeL2EkW5BuTi33oy.qiGFixDNCZF7yFOU51pEH7pxgYfkagu', 'student', NULL, NULL, '2025-07-20 16:01:25', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(35, 'rrifatx@gmail.com', NULL, NULL, NULL, NULL, 'ttariqx@gmail.com', '$2y$10$C/afeTHUsEtSIAmrMZPTAu7vuXdcyN9H4kTwoycQBznnU.7S44k.u', 'mentor', 'vv', NULL, '2025-07-20 16:04:11', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka'),
(36, 'scamiha', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'samihamais01@gmail.com', '$2y$10$AH6r3WW0rdGXj6FOupRGR.iraZuWbwg2PSWL39ujuzQ8U6vm6xpaK', 'student', NULL, NULL, '2025-07-20 16:06:56', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(37, 'samiham', 'Samiha Akter Maisha', 'JavaScript', 'Uiu', 3.00, 'samihamas0@gmail.com', '$2y$10$aCb1ypaNr5IOnqLPWtNT/OE4E1RJyrgWpYNGESyi4/3wnst1CQZ1G', 'student', NULL, NULL, '2025-07-20 16:14:57', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(38, 'rrrrifatx@gmail.com', 'Samiha Akter Maisha', 'Mobile App Development, Python, Leadership', 'Uiu', 3.00, 'tttntariqx@gmail.com', '$2y$10$3GUjcxaZ65U8Xix8Xmxrz.3zRlUmeC9QWdMXuajP3LDdxLw3/7mPq', 'student', NULL, NULL, '2025-07-20 17:07:53', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(39, 'samihamm', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'tariqg@gmail.com', '$2y$10$vz5NNgAVIiVYQT.om.b1He4Vf3F.TNuPFpL/GkiCK5YPDq9QW5Alu', 'mentor', 'nn', NULL, '2025-07-20 17:27:59', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka'),
(40, 'sam', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'tarriqg@gmail.com', '$2y$10$Z0KCV.hZXGbLBqqP6BVuqOniu2kTlLWekhfmu5RcBQjLdHeFSg1.m', 'student', NULL, NULL, '2025-07-20 20:36:28', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(41, 'Sami', NULL, NULL, NULL, NULL, 'testuserz@gmail.com', '$2y$10$Zw0tgZq3oytwBILEekhbju4X1wk2fZzOzlSqk.OfGjFDNB.Q2yoE2', 'mentor', 'bb', NULL, '2025-07-20 21:08:54', NULL, 1, 0, 'pending', 'public', 'WebDevelop', '2010', '5', 'everything', 'Dhaka'),
(42, 'sammm', NULL, NULL, NULL, NULL, 'tarriiqgb@gmail.com', '$2y$10$IYlVZuI1j0Vq.zLY1NwaT.wufpjZSdaAkcuCPB79kVFj1wA6WuNSa', 'mentor', 'bb', NULL, '2025-07-20 21:13:12', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka'),
(43, 'sammmm', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'tarriiqgbg@gmail.com', '$2y$10$W7WLtN7JM/1BbXvob.6vVOv5vyCy2gROfHKeIqCsdZKAqeUv9isSu', 'student', NULL, NULL, '2025-07-20 21:58:39', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(44, 's', NULL, NULL, NULL, NULL, 'sa@gmail.com', '$2y$10$6e50/agz/En5p4yrN.kRzONIUjA9fZ98VcVmGlyR/0ZFZVjdQkfRu', 'mentor', NULL, NULL, '2025-07-20 22:01:35', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(45, 'sa', NULL, NULL, NULL, NULL, 'saa@gmail.com', '$2y$10$NH9rBNUHRnwtxnBmVcR//ekXDvwnsnzFDrBzjc/ii5oiOsDOmjKFG', 'mentor', NULL, NULL, '2025-07-20 22:04:44', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(46, 'ma', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'ma1@mail.com', '$2y$10$Y2YuN8YepSBeyk/jYmnTMeZKgB3ULQs5zxy.4rQ/uPYYdUwoJ3D8W', 'student', NULL, NULL, '2025-07-20 22:55:22', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(47, 'maa', NULL, NULL, NULL, NULL, 'maa1@mail.com', '$2y$10$co0aLwVSH8zhxVVWERe.x.ndO01v1DkKvC3dMSVOwJnLTEuSlJxpy', 'mentor', NULL, NULL, '2025-07-20 23:15:36', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(48, 'maaa', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, C++', 'Uiu', 3.00, 'maaa1@mail.com', '$2y$10$cfmh3AoO/kVtlMjAG3w3aOqDWzbaLyXXnOWHtCb/Kmc35vdkpDR8K', 'student', NULL, NULL, '2025-07-20 23:23:32', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(49, 'maaaa', NULL, NULL, NULL, NULL, 'maaa12@mail.com', '$2y$10$2qMR9GLJamaGNbAD/HuSa.zMymN4L1AU.Zp0bOOskj1mPt/P3RJi.', 'student', NULL, NULL, '2025-07-21 07:32:55', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(50, 'maaaai', NULL, NULL, NULL, NULL, 'maaai12@mail.com', '$2y$10$PzLFJqh8Z0MCxs9jH7963OEKGGHV24FU9bcrMMz4hy7.ABed6oZFa', 'mentor', NULL, NULL, '2025-07-21 07:34:39', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(51, 'akter', 'Samiha Akter Maisha', NULL, NULL, NULL, 'ak34@gmail.com', '$2y$10$yMRb2lydZx6TXuAFMpuWweiPp/kMayDTNj2jBB/epUS4ojMgyZyuu', 'mentor', 'nn', 'uploads/profile_pics/51_1753098572.jpeg', '2025-07-21 07:43:18', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(52, 'akterr', NULL, NULL, NULL, NULL, 'akt34@gmail.com', '$2y$10$Y2KwCr5CBarW79HrD0/pu.hQC34e7imDrQKDB.BaNb8mOodm2eB0q', 'student', NULL, NULL, '2025-07-21 09:06:18', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(53, 'akterrrr', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'ak344@gmail.com', '$2y$10$YOisISHh8blQ9NxAJMOzc.JcTRFBHnOWPZ6C6T1NrUT31EQ0Qs422', 'student', NULL, NULL, '2025-07-21 09:06:56', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(54, 'akterro', NULL, NULL, NULL, NULL, 'akt348@gmail.com', '$2y$10$amqm0X6YQvc7Ox6NOYvou.t2c2.wTSDwYMueIb9vPC6s6h2wCs3ma', '', NULL, NULL, '2025-07-21 13:46:41', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(55, 'a', NULL, NULL, NULL, NULL, 'aak344@gmail.com', '$2y$10$Gxf6a5lTeSnL/iMFnEBfu.RM42Xn5Os8UwOmNsE0tuJQuaAAkAhEK', '', NULL, NULL, '2025-07-21 13:49:02', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(56, 'akterroo', NULL, NULL, NULL, NULL, 'akt3488@gmail.com', '$2y$10$CiIudaWajwuyT0L7Oppo8epucvP0864s389or6eI4Dv5XKLYe/nEC', '', NULL, NULL, '2025-07-21 13:54:16', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(57, 'akkterroo', NULL, NULL, NULL, NULL, 'aktt3488@gmail.com', '$2y$10$pUWTVlX.vA1OGWdu9YyjyeROjAyJ1gjWZzzqBPpbUTso/gZ3dy6Wq', '', NULL, NULL, '2025-07-21 13:58:33', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(58, 'att3488@gmail.com', NULL, NULL, NULL, NULL, 'tariqul@gmail.com', '$2y$10$leAroyVca2sPdKHcKh62Le1iKcChrjHClu6G1HsKclVAE1RPzIIU2', '', NULL, NULL, '2025-07-21 13:59:54', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(59, 'samihac', NULL, NULL, NULL, NULL, 'samihamai0@gmail.com', '$2y$10$vuQiowRoXO83Ja2XpJNRTO8ylNLwfI5Y0nN3dlYNcXifU.O4rWxOe', '', NULL, NULL, '2025-07-21 14:02:16', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(60, 'akkterroox', NULL, NULL, NULL, NULL, 'akttx3488@gmail.com', '$2y$10$2ruY9tkvO3FZc9iVvO31..NwiQV/Ujl8XhLm2HFWIb2EkDYd35Jey', '', NULL, NULL, '2025-07-21 14:04:28', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(61, 'rif', NULL, NULL, NULL, NULL, 'testr@gmail.com', '$2y$10$3MPupDsdgM4tG03IJPJHfOy4mba4Z1moFonWL36hcO0e8qKrY8cXi', '', NULL, NULL, '2025-07-21 14:08:42', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(62, 'samp', NULL, NULL, NULL, NULL, 'samp1010@gmail.com', '$2y$10$o0bxd36sZf0P6D3pBqG5..BTpazGqHIm/8rFCw7fYLTFDOn1le6fa', '', NULL, NULL, '2025-07-21 14:12:10', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(63, 'sampp', NULL, NULL, NULL, NULL, 'samp110@gmail.com', '$2y$10$UJ6QK9nNoGW88LTXnai3o.vflSVbq7feKL55XQTmEa8CWPaCgzjuG', '', NULL, NULL, '2025-07-21 14:16:01', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(64, 'samppp', NULL, NULL, NULL, NULL, 'samp9110@gmail.com', '$2y$10$gHEQzOuS6OJcOnNk5K1iXuOztaTDqg0S2ZraMOvaEqwRwTuh6Ueia', '', NULL, NULL, '2025-07-21 15:55:35', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(65, 'riff', NULL, NULL, NULL, NULL, 'ctestr@gmail.com', '$2y$10$YhoG97G8mKUOGvbbyVYrxuMiz01rPiikovaIhXntw5p2JswaaiVwm', '', NULL, NULL, '2025-07-21 15:56:35', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(66, 'samg', NULL, NULL, NULL, NULL, 'sampg9110@gmail.com', '$2y$10$cS.gejXkgA5KWOgbrDk9zezt6A.hW240IjBXWMwMH2CXKqRfWorA2', 'mentor', NULL, NULL, '2025-07-21 16:42:13', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(67, 'smm', 'Samiha Maisha', NULL, NULL, NULL, 'sm11@gmail.com', '$2y$10$2QiVpAh5j7hrPX.XzXj/DecF6pTgkMPtWo9unz4s9XbSHW0ricN/6', 'mentor', 'Best company', NULL, '2025-07-22 09:33:33', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(68, 'ayad', NULL, NULL, NULL, NULL, 'ayad@gmail.com', '$2y$10$sIy55zHY4goQYwGPpn8Q1./3IKEx7go4rO6xr7rIP/s4PbCWB.vcm', '', NULL, NULL, '2025-07-22 14:12:03', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(69, 'ayadd', 'Samiha Akter Maisha', 'Python', 'Uiu', 3.00, 'ayadd@gmail.com', '$2y$10$iGBH/38wqx0UteCdzTTVBe4MlXHhg7VhRn7owA2Tii8FOrF6Dkgla', 'student', NULL, NULL, '2025-07-22 15:12:40', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(70, 'ayaadd', 'smm', 'Web Development', 'Uiu', 3.00, 'ayaadd@gmail.com', '$2y$10$cqEdJ8WOTVsuNUlAGNM.D.AhFH7fS1O3OY.84o8g/TtPAtv9p0mba', 'student', NULL, NULL, '2025-07-23 08:53:34', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(71, 'samihammmmmm', NULL, NULL, NULL, NULL, 'ayaamdd@gmail.com', '$2y$10$6CahQ1GEMEWO7caL8W80V.SCGhe/g5FrxGwAQcCSFXtg84OWG8uEu', 'mentor', NULL, NULL, '2025-07-23 15:53:53', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(72, 'bb', 'smm', 'Python', 'Uiu', 3.00, 'ayabamdd@gmail.com', '$2y$10$1UndGmXMgnlv6.dZC0bY0ONtVZf/fsyGhmARME2oW2rFE1WFhFnr6', 'student', NULL, NULL, '2025-07-23 15:56:33', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(73, 'rafi', NULL, NULL, NULL, NULL, 'mahi@gmail.com', '$2y$10$OkPt6VvAVEIZy43Cot.iru8z6Syi4pGiYmsCdAEOLiEilGaCUACY2', 'mentor', NULL, NULL, '2025-07-23 15:59:48', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL),
(74, 'rafii', 'Samiha Akter Maisha', 'Python, C++', 'Uiu', 3.00, 'mahii@gmail.com', '$2y$10$auUNdHifG3LPpb7TPvHkHOH5XUWp/vHW2hTeyWZg6mw7CSGCMGVTa', 'student', NULL, NULL, '2025-07-23 16:06:44', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `userskills`
--

CREATE TABLE `userskills` (
  `user_skill_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `proficiency` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_enrollments`
--

CREATE TABLE `user_enrollments` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrolled_at` datetime DEFAULT current_timestamp(),
  `progress` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_milestones`
--

CREATE TABLE `user_milestones` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `milestone_id` int(11) NOT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_skill_progress`
--

CREATE TABLE `user_skill_progress` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `skill` varchar(100) NOT NULL,
  `milestone_index` int(11) NOT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_skill_progress`
--

INSERT INTO `user_skill_progress` (`id`, `user_email`, `skill`, `milestone_index`, `completed`, `completed_at`) VALUES
(1, 'ayadd@gmail.com', 'Python', 0, 0, NULL),
(2, 'ayadd@gmail.com', 'Python', 2, 0, NULL),
(3, 'ayadd@gmail.com', 'Python', 1, 0, NULL),
(4, 'ayaadd@gmail.com', 'Java', 2, 0, NULL),
(5, 'ayaadd@gmail.com', 'Java', 0, 0, NULL),
(6, 'ayaadd@gmail.com', 'Java', 1, 0, NULL),
(7, 'ayaadd@gmail.com', 'Python', 1, 0, NULL),
(8, 'ayaadd@gmail.com', 'Python', 2, 0, NULL),
(9, 'ayaadd@gmail.com', 'Python', 0, 0, NULL),
(10, 'mahii@gmail.com', 'Java', 0, 0, NULL),
(11, 'mahii@gmail.com', 'Java', 1, 0, NULL),
(12, 'mahii@gmail.com', 'Java', 2, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointed_list`
--
ALTER TABLE `appointed_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `careerpaths`
--
ALTER TABLE `careerpaths`
  ADD PRIMARY KEY (`path_id`);

--
-- Indexes for table `career_milestones`
--
ALTER TABLE `career_milestones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `connections`
--
ALTER TABLE `connections`
  ADD PRIMARY KEY (`connection_id`),
  ADD UNIQUE KEY `user_one` (`user_one`,`user_two`),
  ADD KEY `user_two` (`user_two`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `course_feedback`
--
ALTER TABLE `course_feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `enrolled_courses`
--
ALTER TABLE `enrolled_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`user_email`,`course_id`);

--
-- Indexes for table `hire_requests`
--
ALTER TABLE `hire_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `ignored_mentors`
--
ALTER TABLE `ignored_mentors`
  ADD PRIMARY KEY (`user_id`,`ignored_mentor_id`);

--
-- Indexes for table `interview_answers`
--
ALTER TABLE `interview_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `interview_questions`
--
ALTER TABLE `interview_questions`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `interview_sessions`
--
ALTER TABLE `interview_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `mentor_id` (`mentor_id`);

--
-- Indexes for table `jobapplications`
--
ALTER TABLE `jobapplications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `applicant_id` (`applicant_id`);

--
-- Indexes for table `jobposts`
--
ALTER TABLE `jobposts`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `poster_id` (`poster_id`);

--
-- Indexes for table `job_posts`
--
ALTER TABLE `job_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_progress` (`user_email`,`lesson_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `mentors`
--
ALTER TABLE `mentors`
  ADD PRIMARY KEY (`mentor_id`);

--
-- Indexes for table `mentor_availability`
--
ALTER TABLE `mentor_availability`
  ADD PRIMARY KEY (`availability_id`),
  ADD KEY `mentor_id` (`mentor_id`);

--
-- Indexes for table `mentor_details`
--
ALTER TABLE `mentor_details`
  ADD PRIMARY KEY (`mentor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `mentor_hires`
--
ALTER TABLE `mentor_hires`
  ADD PRIMARY KEY (`hire_id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `mentor_profiles`
--
ALTER TABLE `mentor_profiles`
  ADD PRIMARY KEY (`mentor_id`);

--
-- Indexes for table `mentor_requests`
--
ALTER TABLE `mentor_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mentor_student_chats`
--
ALTER TABLE `mentor_student_chats`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `mentor_student_relationships`
--
ALTER TABLE `mentor_student_relationships`
  ADD PRIMARY KEY (`relationship_id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `mentor_student_sessions`
--
ALTER TABLE `mentor_student_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `mentor_suggestions`
--
ALTER TABLE `mentor_suggestions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`skill_id`),
  ADD UNIQUE KEY `skill_name` (`skill_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `suggestions`
--
ALTER TABLE `suggestions`
  ADD PRIMARY KEY (`suggestion_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `path_id` (`path_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `userskills`
--
ALTER TABLE `userskills`
  ADD PRIMARY KEY (`user_skill_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`skill_id`),
  ADD KEY `skill_id` (`skill_id`);

--
-- Indexes for table `user_enrollments`
--
ALTER TABLE `user_enrollments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_milestones`
--
ALTER TABLE `user_milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `milestone_id` (`milestone_id`);

--
-- Indexes for table `user_skill_progress`
--
ALTER TABLE `user_skill_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_progress` (`user_email`,`skill`,`milestone_index`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointed_list`
--
ALTER TABLE `appointed_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `careerpaths`
--
ALTER TABLE `careerpaths`
  MODIFY `path_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `career_milestones`
--
ALTER TABLE `career_milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_profiles`
--
ALTER TABLE `company_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `connections`
--
ALTER TABLE `connections`
  MODIFY `connection_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `course_feedback`
--
ALTER TABLE `course_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_lessons`
--
ALTER TABLE `course_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enrolled_courses`
--
ALTER TABLE `enrolled_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hire_requests`
--
ALTER TABLE `hire_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interview_answers`
--
ALTER TABLE `interview_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interview_questions`
--
ALTER TABLE `interview_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `interview_sessions`
--
ALTER TABLE `interview_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobapplications`
--
ALTER TABLE `jobapplications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobposts`
--
ALTER TABLE `jobposts`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `job_posts`
--
ALTER TABLE `job_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentors`
--
ALTER TABLE `mentors`
  MODIFY `mentor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mentor_availability`
--
ALTER TABLE `mentor_availability`
  MODIFY `availability_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentor_details`
--
ALTER TABLE `mentor_details`
  MODIFY `mentor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `mentor_hires`
--
ALTER TABLE `mentor_hires`
  MODIFY `hire_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentor_requests`
--
ALTER TABLE `mentor_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `mentor_student_chats`
--
ALTER TABLE `mentor_student_chats`
  MODIFY `chat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentor_student_relationships`
--
ALTER TABLE `mentor_student_relationships`
  MODIFY `relationship_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentor_student_sessions`
--
ALTER TABLE `mentor_student_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentor_suggestions`
--
ALTER TABLE `mentor_suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suggestions`
--
ALTER TABLE `suggestions`
  MODIFY `suggestion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `userskills`
--
ALTER TABLE `userskills`
  MODIFY `user_skill_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_enrollments`
--
ALTER TABLE `user_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_milestones`
--
ALTER TABLE `user_milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_skill_progress`
--
ALTER TABLE `user_skill_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD CONSTRAINT `company_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `connections`
--
ALTER TABLE `connections`
  ADD CONSTRAINT `connections_ibfk_1` FOREIGN KEY (`user_one`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `connections_ibfk_2` FOREIGN KEY (`user_two`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `course_enrollments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD CONSTRAINT `course_lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `hire_requests`
--
ALTER TABLE `hire_requests`
  ADD CONSTRAINT `hire_requests_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hire_requests_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `interview_answers`
--
ALTER TABLE `interview_answers`
  ADD CONSTRAINT `interview_answers_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `interview_sessions` (`session_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interview_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `interview_questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `interview_sessions`
--
ALTER TABLE `interview_sessions`
  ADD CONSTRAINT `interview_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interview_sessions_ibfk_2` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `jobapplications`
--
ALTER TABLE `jobapplications`
  ADD CONSTRAINT `jobapplications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobposts` (`job_id`),
  ADD CONSTRAINT `jobapplications_ibfk_2` FOREIGN KEY (`applicant_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `jobposts`
--
ALTER TABLE `jobposts`
  ADD CONSTRAINT `fk_jobposts_poster_id` FOREIGN KEY (`poster_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_poster_id` FOREIGN KEY (`poster_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobposts_ibfk_1` FOREIGN KEY (`poster_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `job_posts`
--
ALTER TABLE `job_posts`
  ADD CONSTRAINT `job_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD CONSTRAINT `lesson_progress_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `course_lessons` (`id`);

--
-- Constraints for table `mentor_availability`
--
ALTER TABLE `mentor_availability`
  ADD CONSTRAINT `mentor_availability_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `mentor_details`
--
ALTER TABLE `mentor_details`
  ADD CONSTRAINT `mentor_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `mentor_hires`
--
ALTER TABLE `mentor_hires`
  ADD CONSTRAINT `mentor_hires_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `mentor_details` (`mentor_id`),
  ADD CONSTRAINT `mentor_hires_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `mentor_profiles`
--
ALTER TABLE `mentor_profiles`
  ADD CONSTRAINT `mentor_profiles_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `mentor_student_chats`
--
ALTER TABLE `mentor_student_chats`
  ADD CONSTRAINT `mentor_student_chats_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentor_student_chats_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `mentor_student_relationships`
--
ALTER TABLE `mentor_student_relationships`
  ADD CONSTRAINT `mentor_student_relationships_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentor_student_relationships_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentor_student_relationships_ibfk_3` FOREIGN KEY (`job_id`) REFERENCES `jobposts` (`job_id`) ON DELETE SET NULL;

--
-- Constraints for table `mentor_student_sessions`
--
ALTER TABLE `mentor_student_sessions`
  ADD CONSTRAINT `mentor_student_sessions_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mentor_student_sessions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `suggestions`
--
ALTER TABLE `suggestions`
  ADD CONSTRAINT `suggestions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `suggestions_ibfk_2` FOREIGN KEY (`path_id`) REFERENCES `careerpaths` (`path_id`);

--
-- Constraints for table `userskills`
--
ALTER TABLE `userskills`
  ADD CONSTRAINT `userskills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `userskills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`skill_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_milestones`
--
ALTER TABLE `user_milestones`
  ADD CONSTRAINT `user_milestones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_milestones_ibfk_2` FOREIGN KEY (`milestone_id`) REFERENCES `career_milestones` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
