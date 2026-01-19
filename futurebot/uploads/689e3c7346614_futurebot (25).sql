-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2025 at 06:41 PM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_job_approvals`
--

CREATE TABLE `admin_job_approvals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('job','internship') NOT NULL,
  `description` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `availability` varchar(255) DEFAULT NULL,
  `fee` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `mentor_email` varchar(255) NOT NULL,
  `answer_text` text NOT NULL,
  `votes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(34, 0, 'sm11@gmail.com', 'maisha', 'lakecity', 'Uiu', 'C++', '01738915382', 'rejected', 'samiha sami', '2025-07-29 12:19:32'),
(35, 0, '', 'mmm', 'Badda.Dhaka', 'mmm', 'kkk', 'nnnnnnnnnnn', 'accepted', 'olv', '2025-08-07 17:37:04'),
(36, 0, 'bkm112202@gmail.com', 'mmm', 'Badda.Dhaka', 'mmm', 'kkk', 'nnnnnnnnnnn', 'accepted', 'samiha10', '2025-08-14 10:58:41'),
(37, 0, 'bkm112202@gmail.com', 'mmm', 'Badda.Dhaka', 'mmm', 'kkk', 'nnnnnnnnnnn', 'accepted', 'samiha10', '2025-08-14 10:58:43'),
(38, 0, 'bkm112202@gmail.com', 'mmm', 'Badda.Dhaka', 'mmm', 'kkk', 'nnnnnnnnnnn', 'accepted', 'samiha10', '2025-08-14 10:58:48');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `skill` varchar(100) NOT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `pdf` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `description`, `skill`, `price`, `image`, `rating`, `created_at`, `pdf`) VALUES
(4, 'Java', ',,', 'JAVA', 100.00, 'uploads/books/book_6893dd36be9d5.jpg', NULL, '2025-08-06 22:54:46', 'uploads/books/pdfs/book_pdf_6893dd36bf220.pdf');

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
-- Table structure for table `chat_history`
--

CREATE TABLE `chat_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question` text DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `start_year` int(11) DEFAULT NULL,
  `trade_license` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_approved` tinyint(1) DEFAULT 0,
  `otp` varchar(10) DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `start_year`, `trade_license`, `email`, `phone`, `password`, `address`, `document_path`, `status`, `created_at`, `is_approved`, `otp`, `otp_expires_at`, `otp_expiry`, `company_name`) VALUES
(1, 'Tariq', 2010, '1010010', 'tish@gmail.com', NULL, '$2y$10$EGpVCu.5HJKkGhoM2XPykuo6HpwfNbYMuQFgkOcviwPvWsn3FjLEa', 'Dhaka,Bangladesh', 'uploads/futurebot (14).sql', 'approved', '2025-08-02 11:25:48', 1, NULL, NULL, NULL, NULL),
(3, 'Tariq', 2010, '1010010', 'tisha@gmail.com', NULL, '$2y$10$Uw58JYZaOJG7u7..Z0ooVuAH96xS.WidP/xyA45J/4d/T7wM2TxE2', 'Dhaka,Bangladesh', 'uploads/ffffff.pdf', 'approved', '2025-08-02 12:42:27', 0, NULL, NULL, NULL, NULL),
(4, 'Tariq', 2010, '1010010', 'tishaa@gmail.com', NULL, '$2y$10$T7Lr5W5wm90q6XW9BMPpJ.HxOlLClOaeuxwfkfNqpMLKMow8nxeWy', 'Dhaka,Bangladesh', 'uploads/ffffff.pdf', 'approved', '2025-08-02 12:46:16', 0, NULL, NULL, NULL, NULL),
(5, 'Tariq', 2010, '1010010', 'tishaaa@gmail.com', NULL, '$2y$10$s8RIkGKoqe47SILccDUHlOW7oi.RWCeOgi8AQPu3s2jnqgUiPy1Qe', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/ffffff.pdf', 'approved', '2025-08-02 12:53:57', 0, NULL, NULL, NULL, NULL),
(6, 'Tariq', 2010, '1010010', 'irtezaa@gmail.com', NULL, '$2y$10$qEeIg2GEkoryhXVYxMGnUu2WyhyB4wprGL.sWwj26NbWjpqRR2qCW', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (12).sql', 'approved', '2025-08-02 12:59:53', 0, NULL, NULL, NULL, NULL),
(7, 'Tariq', 2010, '1010010', 'ir@gmail.com', NULL, '$2y$10$ZewqCMSFzK5pUhtHJ105rO/yIpugStGZz/FKEznxdoA/fts0er9Pm', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/ffffff.pdf', 'approved', '2025-08-02 13:02:57', 0, NULL, NULL, NULL, NULL),
(8, 'Tariq', 2010, '1010010', 'a@gmail.com', NULL, '$2y$10$Jn1uILBGGV2oM/tsMSt3n.I2DGtoIskr0WBejTJI/zKAzcmNY1hwq', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/PHPMailer-master (2).zip', 'approved', '2025-08-02 13:14:39', 0, NULL, NULL, NULL, NULL),
(9, 'Tariq', 2010, '1010010', 'ab@gmail.com', NULL, '$2y$10$w5DUo2ITaasyqUnX0uM5L.fXeaYAYkQwo9pJAQQVvWXFwtbd7hIBi', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/PHPMailer-master (2).zip', 'approved', '2025-08-02 13:26:30', 0, NULL, NULL, NULL, NULL),
(10, 'Tariq', 2010, '1010010', 'irc@gmail.com', NULL, '$2y$10$1U.nopPoMe5nk2OuOoSspOevpt2wUszf/.4Y8Az8VJqCkoxZz/bNm', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/PHPMailer-master (2).zip', 'approved', '2025-08-02 13:30:42', 0, NULL, NULL, NULL, NULL),
(11, 'Tariq', 2010, '1010010', 'irce@gmail.com', NULL, '$2y$10$Ay.ODWjmJgBL/8VZ86/la.GWfUn9gq6TvLrUeQy9yby5GQWijc8Fu', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/PHPMailer-master (2).zip', 'approved', '2025-08-02 13:31:00', 0, NULL, NULL, NULL, NULL),
(12, 'Tariq', 2010, '1010010', 'irceb@gmail.com', NULL, '$2y$10$ZWzCa0uJ.FPW119NUmUvbuadLPF4oVxa19SnSszC06BDinYtBRhNi', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (14) (1).zip', 'approved', '2025-08-02 13:43:56', 0, NULL, NULL, NULL, NULL),
(13, 'Tariq', 2010, '1010010', 'm@gmail.com', NULL, '$2y$10$oTDyEtJpzFqSnXsMr/6ywOtShqbkYMn/0meI/q2dqZxu5n2MHVFfe', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (14) (1).zip', 'approved', '2025-08-02 13:44:22', 0, NULL, NULL, NULL, NULL),
(14, 'Tariq', 2010, '1010010', 'mm@gmail.com', NULL, '$2y$10$OdsG8HsCln9qenUk8FvHqeAV0jzlIoz6YQlMrCsGKAq2o7YELHdkq', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (15).sql', 'approved', '2025-08-02 14:21:16', 0, NULL, NULL, NULL, NULL),
(15, 'Tariq', 2010, '1010010', 'mbm@gmail.com', NULL, '$2y$10$1scu6qE40bKVGVZfHVPRPehf6Wka0oBpY5WnKZ3bI8Hvznucsgkyy', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (14) (1).zip', 'approved', '2025-08-02 14:21:34', 0, NULL, NULL, NULL, NULL),
(16, 'Tariq', 2010, '1010010', 'mbbm@gmail.com', NULL, '$2y$10$7KXC79P0oA8z125yEcg2U.Onq1cdrL0k.z9wFhWO0ooOQtV8QmkJ6', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (14) (1).zip', 'approved', '2025-08-02 14:21:51', 0, NULL, NULL, NULL, NULL),
(17, 'Tariq', 2010, '1010010', 'mmm@gmail.com', NULL, '$2y$10$GW.vOdyOdzZbuj5MHgu4zuG0SFMY6eUksldFnWwyeWawAM9euEyKC', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (14) (1).zip', 'approved', '2025-08-02 14:45:50', 0, NULL, NULL, NULL, NULL),
(18, 'Tariq', 2010, '1010010', 'mmmb@gmail.com', NULL, '$2y$10$Agklr.e0etxtSGipxeTM3.NbpDerg5DKcGcapiFj9brgXVknhKdkW', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (15).sql', 'approved', '2025-08-02 14:49:26', 0, NULL, NULL, NULL, NULL),
(19, 'Tariq', 2010, '1010010', 'ms@gmail.com', NULL, '$2y$10$zQZOABOBH/qulOxUziJ.3eyDyiGqlWwUgx/zz04bimR5B8FGRmHyi', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (14) (1).zip', 'approved', '2025-08-02 14:52:06', 0, NULL, NULL, NULL, NULL),
(20, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$FnBIqi1Hi9EmwO8Ue8RSi.D/lju0m220aOMomqlitcywBhE6ZH.J6', 'Dhaka,Bangladesh', 'uploads/futurebot (16).sql', 'approved', '2025-08-03 12:58:36', 0, '655818', NULL, '2025-08-03 17:18:35', 'WebDeveloper'),
(21, 'WebDeveloper', 2010, '1010010', 'tamannaorpa25@gmail.com', NULL, '$2y$10$cV/O5sI/d2pLImlnL3V.b.IHzXlZdiSalXdFpzNN7Ltp5gNPolawe', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16).sql', 'approved', '2025-08-03 13:19:21', 0, NULL, NULL, NULL, NULL),
(23, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$auZLYHM9rr.U47ilt3yH4eHuOCOqOjsbEgoE327NcYg3Jn85M/Ktq', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16).sql', 'approved', '2025-08-03 13:44:56', 0, '655818', NULL, '2025-08-03 17:18:35', NULL),
(24, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$GeZd17vp6tOAPXx0Mp/j7uiqeEvGdArlC3DyzW51aCOaRWuM5mkpO', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16).sql', 'approved', '2025-08-03 13:45:15', 0, '655818', '2025-08-03 16:13:18', '2025-08-03 17:18:35', NULL),
(25, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$h.FBd6SSC5aIkJIk2LsEaeBAzQVyFBVLSbxW0tGdpfShqibtCeUWa', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16).sql', 'approved', '2025-08-03 14:30:52', 0, '655818', '2025-08-03 16:46:00', '2025-08-03 17:18:35', NULL),
(26, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$A6CjsQhceZxI1J1RhomZQuqmx.KH7z8sV4q2jk3wzx9ufzFShPJEy', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (15).sql', 'approved', '2025-08-03 14:54:06', 0, '655818', '2025-08-03 17:09:14', '2025-08-03 17:18:35', NULL),
(27, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$Qy/8osE/IcezZfMA2AHDMeCoXNNOyu0PYkraqCvF6AzrQVsmgBAr6', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16).sql', 'approved', '2025-08-03 15:08:55', 0, '655818', '2025-08-03 17:24:09', '2025-08-03 17:18:35', NULL),
(28, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$pSW9yGQgAZCAyhEiKbs1BurLg0LeM10ydMq58EJqYFiexU7lS8UIC', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/PHPMailer-master (2).zip', 'approved', '2025-08-03 15:15:01', 0, '840375', '2025-08-03 17:30:49', NULL, NULL),
(29, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$ZYfOAL6NcwGdRkzt9hXF5.la6go4Ew1wPR9gW.W/0poejmZdqu09u', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16).sql', 'approved', '2025-08-03 15:23:43', 0, '214589', '2025-08-03 17:38:59', NULL, NULL),
(30, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$cpNB./ImwIn7kTlQCPKrnu4rAnN1Nu4O9LWjU/nMToQcVbG29n0Eu', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16).sql', 'approved', '2025-08-03 15:53:40', 0, '789325', '2025-08-03 18:11:02', NULL, NULL),
(31, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$SaVl49CaZLbGnAol28j4MOKaI6rmcD9Of5vVJBgNXtG.OQC5l/Aga', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/PHPMailer-master (2).zip', 'approved', '2025-08-03 15:53:54', 0, '852937', '2025-08-03 18:17:38', NULL, NULL),
(32, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$jibH05MfSurvipFbk62UleQxGs9vKnV7Crrccz4jExfQePWKmcyaG', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/CSE3411_Mid_241.pdf', 'approved', '2025-08-03 15:54:08', 0, '431720', '2025-08-03 20:31:42', NULL, NULL),
(33, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$iBeC7wzdkp.rGU4zAZLUCu5YAm0tT4XRrGak9p9WL0QhKcviEZZb6', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16).sql', 'approved', '2025-08-03 15:54:24', 0, '928145', '2025-08-03 20:31:57', NULL, NULL),
(34, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$wJknitkk1ARj9S/riH1sGuscInERFzh6OPxigDKuCxx1jzzoiCESe', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16) (1).sql', 'approved', '2025-08-03 18:23:45', 0, '359716', '2025-08-03 20:38:52', NULL, NULL),
(35, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$yFrUjyUQwNU2/vab8l0yhut.NxEX62fiFOjvVsb5jmxC2PcAWOPC6', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (17).sql', 'approved', '2025-08-03 18:25:41', 0, '208463', '2025-08-03 20:40:48', NULL, NULL),
(36, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$uV6MatMsY83sNJfxHhZE3uVJ3ARg1l.EgKev6VREUaaeoLz0N8Oym', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (17).sql', 'approved', '2025-08-03 18:31:03', 0, '489035', '2025-08-03 20:46:22', NULL, NULL),
(37, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$P/MiaceP.IL6g/tNN8b.yOSgK01C.cSDmYZ4O4Hj2nuMvU1VIzZ4O', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16) (1).sql', 'approved', '2025-08-03 18:33:06', 0, '092853', '2025-08-03 20:48:37', NULL, NULL),
(38, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$hrT1hR75P59n.tN1rtSzz..OwFtagbYrP5zj3Lo50zqOhrhSf0lsG', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (17).sql', 'approved', '2025-08-03 18:33:17', 0, '127640', '2025-08-03 21:01:23', NULL, NULL),
(39, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$.oTfgpKg2c2VPXuIQs6B/OyGdpMx1EdvFtQCjBqD8TlWFqFPw6Vqq', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (17).sql', 'approved', '2025-08-03 18:33:29', 0, '491675', '2025-08-03 21:04:33', NULL, NULL),
(40, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$ThS9E.qbgILPR6iRgd4EnuNA1GwqrVF19KI/w1TyiBSUWNmO99xOi', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (17).sql', 'approved', '2025-08-03 18:53:06', 0, '409538', '2025-08-03 21:15:03', NULL, NULL),
(41, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$DzEVQcqTyiE0fBE9coFT4.yGC9IT9moZM41ic3NqwNm7TOV4gYkLa', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (16) (1).sql', 'approved', '2025-08-03 18:53:18', 0, '279530', '2025-08-03 21:08:35', NULL, NULL),
(42, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$u/vZ5q5nakExz7vb7bTJI.4PqJ0KdIch7IgIIcJ8wkg.koU2GNm5u', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (17).sql', 'approved', '2025-08-03 19:03:18', 0, '526817', '2025-08-03 21:18:26', NULL, NULL),
(43, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$1fL6DE6eURoTodeO5QrxHuMLQUxn4x8vXD7CcqFwywoBWaIRL.ec.', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/futurebot (17).sql', 'approved', '2025-08-03 19:20:31', 0, '093721', '2025-08-03 21:35:39', NULL, NULL),
(44, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$KSUEvFz3ajiIGEFSTiem8uSjqws55HfAkv7dNi2HXcTS9o.jO5IIa', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/688fb998cb5bc_futurebot (17).sql', 'approved', '2025-08-03 19:33:44', 0, '075294', '2025-08-03 21:48:57', NULL, NULL),
(45, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$sXHDyrLNoJbRLe6n3gf3ruMf5TWgN4X9vtFlrU1WS4HsPvdg88T5i', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/68909a5fcc9db_PHPMailer-master (1).zip', 'approved', '2025-08-04 11:32:47', 0, NULL, NULL, NULL, NULL),
(46, 'Samiha Akter Maisha', 2010, '100010', 'samihamaisha231@gmail.com', NULL, '$2y$10$99LQlbLUSZM6XnOYastPW.a7s3IYIHjujb3MXnalIUFhCE9evMJoa', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/6890a25e0a7b2_Screenshot (17).png', 'approved', '2025-08-04 12:06:54', 0, NULL, NULL, NULL, NULL),
(47, 'Samiha Akter Maisha', 2010, '100010', 'samihamaisha231@gmail.com', NULL, '$2y$10$seDhv2PYcd6aeSrNDWNLUOip4B1cwLfGTjxBxdu3Sm65thqpOKPNO', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/6890a8fa57c10_Screenshot (16).png', 'approved', '2025-08-04 12:35:06', 0, NULL, NULL, NULL, NULL),
(48, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$PpUPBxtJTvjQACj491To4.ZkRwFVNN//v2BFXERZ1Kc6bVGYjHfGm', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/6891f21e19445_enroll_course.php', 'approved', '2025-08-05 11:59:26', 0, NULL, NULL, NULL, NULL),
(49, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$CW4jIwl381hDxOIaL8F/sua4KI8upv6Vzmp8rDqBPuw7HnJ508ey2', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/6891f8e8b58fd_futurebot (18).sql', 'approved', '2025-08-05 12:28:24', 0, NULL, NULL, NULL, NULL),
(50, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$szbg7Djc.0hJ3LNUX/0qbuziLXX/CPkMJyxEcu6jVECOGKPS4Q1jy', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/6891fabc718c9_enroll_course.php', 'rejected', '2025-08-05 12:36:12', 0, NULL, NULL, NULL, NULL),
(51, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$G5HUfUFRCP0PDGFzDlaAFOyUdoAvtz.ezysw7fWqDC3aIeKunXcLy', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/6892d7abec950_PHPMailer-master (1).zip', 'approved', '2025-08-06 04:18:51', 0, NULL, NULL, NULL, NULL),
(52, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$RgwHip6Fm74f74HjV/mY3OvNb0CKH0Ck245cHr3u9wPIfCO/C/DOO', 'bbb', 'uploads/68949824c112e_futurebot (22).sql', 'approved', '2025-08-07 12:12:20', 0, NULL, NULL, NULL, NULL),
(53, NULL, 2020, 'ABC12345', 'contact@example.com', NULL, '$2y$10$7CWq31u3ek9o7iaeLCVZJ.Tedx3BK7t7evpAC.tWE4qCGRgQsrvkW', '123 Example St, City', NULL, 'approved', '2025-08-07 14:46:21', 0, NULL, NULL, NULL, 'Example Company'),
(54, NULL, 2010, 'bb', 'samihamaisha231@gmail.com', NULL, '$2y$10$R586vwVNZXLNWV8hvvuW/u1VwIH70d4cU2UskCRrtSYxQYX0k8NOK', 'Lake City Concord,Khilkhet,Dhaka', NULL, 'approved', '2025-08-07 14:49:21', 0, NULL, NULL, NULL, 'mmmmm'),
(55, NULL, 2010, 'bb', 'samihamaisha231@gmail.com', NULL, '$2y$10$g.a8XwxaEaXkCOMFSQheLujpBp/jg4kNv/3gCqMC8EGFYzYpypBhi', 'Lake City Concord,Khilkhet,Dhaka', NULL, 'approved', '2025-08-07 14:52:32', 0, NULL, NULL, NULL, 'mmmmm'),
(56, NULL, 2010, '100010', 'samihamaisha231@gmail.com', NULL, '$2y$10$7xI3bzpQtnCiDcPYtpFyk.UmvxvmyC7dCwEN5e02mdv4xSNhcSIiW', 'Lake City Concord,Khilkhet,Dhaka', NULL, 'approved', '2025-08-07 15:26:39', 0, NULL, NULL, NULL, 'FutureBot'),
(57, NULL, NULL, NULL, 'samihamaisha231@gmail.com', NULL, '123456', NULL, NULL, 'approved', '2025-08-07 15:58:15', 0, NULL, NULL, NULL, 'Future Company'),
(58, NULL, 2010, '100010', 'samihamaisha231@gmail.com', NULL, '$2y$10$I40c3Xaf6zQuhbEkJXgJBe/Hd8soJis9mXpeSdW/AxFwBK1X1xRe6', 'Lake City Concord,Khilkhet,Dhaka', NULL, 'approved', '2025-08-07 16:12:09', 0, NULL, NULL, NULL, 'FutureBot'),
(59, NULL, 2010, '100010', 'samihamaisha231@gmail.com', NULL, '$2y$10$EMXpiwnqrxrXvL2K2tF3Teck27tZMivyxjr4OzTx3F2IyY5SnN34e', 'Lake City Concord,Khilkhet,Dhaka', NULL, 'approved', '2025-08-07 16:13:00', 0, NULL, NULL, NULL, 'FutureBot'),
(60, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$FV9QVQhFCeSFCb9wyw1ChuhIrvgEE0KEkmKQdU08TGTu2Q/1s9Prq', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/6894f78c9f0e0_futurebot (22).sql', 'approved', '2025-08-07 18:59:24', 0, NULL, NULL, NULL, NULL),
(61, 'WebDeveloper', 2010, '1010010', 'samihamaisha231@gmail.com', NULL, '$2y$10$XUaNpl61azak1NZmq4KPaO1bYcwtUYaDgMnp43o7vU6FwmvRfsTqS', 'Lake City Concord,Khilkhet,Dhaka', 'uploads/689a0bfcab074_FundMid1_0.1.pdf', 'approved', '2025-08-11 15:27:56', 0, NULL, NULL, NULL, NULL),
(62, NULL, 2010, '100010', 'samihamaisha231@gmail.com', NULL, '$2y$10$hyLQXpoSW8q1/u/mJXW0e.WF7GQd8dgA7b5Cq4sP5lWFQEu1/AIOy', 'Lake City Concord,Khilkhet,Dhaka', NULL, 'pending', '2025-08-11 15:30:01', 0, NULL, NULL, NULL, 'FutureBot');

-- --------------------------------------------------------

--
-- Table structure for table `company_otps`
--

CREATE TABLE `company_otps` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(10) NOT NULL,
  `otp_expiry` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_posts`
--

CREATE TABLE `company_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_profiles`
--

CREATE TABLE `company_profiles` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `started_year` year(4) DEFAULT NULL,
  `rating` float DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `starting_year` int(11) DEFAULT NULL,
  `trade_license` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_profiles`
--

INSERT INTO `company_profiles` (`id`, `company_id`, `company_name`, `started_year`, `rating`, `facilities`, `bio`, `location`, `logo_path`, `starting_year`, `trade_license`, `photo`, `created_at`) VALUES
(3, 53, NULL, NULL, 5, 'vv', 'v', 'iiii', NULL, NULL, NULL, NULL, '2025-08-07 14:46:21'),
(4, 54, NULL, NULL, 5, 'vv', ',,,,,,', 'iiii', 'uploads/logos/logo_54.jpeg', NULL, NULL, NULL, '2025-08-07 14:49:21'),
(5, 55, NULL, NULL, 5, 'vv', ',,,,,,', 'iiii', 'uploads/logos/logo_55.jpeg', NULL, NULL, NULL, '2025-08-07 14:52:32'),
(6, 56, NULL, NULL, 5, ',,,,,,,,,,,,,,,,,,,,,,,', 'Best Service Provider', 'Dhaka', 'uploads/logos/logo_56.jpg', NULL, NULL, NULL, '2025-08-07 15:26:39'),
(7, 58, NULL, NULL, 5, ',,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,', ',,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,', 'Dhaka', 'uploads/logos/logo_58.jpeg', NULL, NULL, NULL, '2025-08-07 16:12:09'),
(8, 59, NULL, NULL, 4, ',,,,', ',,,,,,,,,,,,,,,,,,,,,,,,,,', 'Dhaka', 'uploads/logos/logo_59.jpeg', NULL, NULL, NULL, '2025-08-07 16:13:00'),
(9, 20, NULL, NULL, 4, '...................', '..................................', 'Badda.Dhaka', 'uploads/logos/logo_20.jpeg', NULL, NULL, NULL, '2025-08-07 16:30:00'),
(10, 62, NULL, NULL, 5, '.............', '...............', 'Dhaka', 'uploads/logos/logo_62.jpeg', NULL, NULL, NULL, '2025-08-11 15:30:01');

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
-- Table structure for table `course_milestones`
--

CREATE TABLE `course_milestones` (
  `id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `milestone_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_milestones`
--

INSERT INTO `course_milestones` (`id`, `course_id`, `milestone_text`) VALUES
(1, 1, 'Watch Introduction Video'),
(2, 1, 'Complete Module 1 Quiz'),
(3, 1, 'Submit Assignment 1'),
(4, 2, 'Read Chapter 1'),
(5, 2, 'Participate in Discussion'),
(6, 2, 'Pass Final Test');

-- --------------------------------------------------------

--
-- Table structure for table `course_payments`
--

CREATE TABLE `course_payments` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_type` varchar(20) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `remaining_amount` decimal(10,2) DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL,
  `sender_info` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_payments`
--

INSERT INTO `course_payments` (`id`, `user_email`, `course_name`, `amount_paid`, `payment_type`, `total_price`, `remaining_amount`, `payment_date`, `payment_method`, `sender_info`, `transaction_id`, `created_at`) VALUES
(5, 'bkm1122@gmail.com', 'Intro to Python Programming', 80.00, 'Installment', 100.00, 20.00, '2025-08-05 11:15:34', NULL, NULL, NULL, '2025-08-05 12:53:26'),
(6, 'bkm1122@gmail.com', 'Intro to Python Programming', 89.00, 'Installment', 100.00, 11.00, '2025-08-05 11:18:20', NULL, NULL, NULL, '2025-08-05 12:53:26'),
(7, 'bkm1122@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-05 11:29:55', 'Bkash', NULL, NULL, '2025-08-05 12:53:26'),
(8, 'bkm1122@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-05 11:33:40', 'Bkash', NULL, NULL, '2025-08-05 12:53:26'),
(9, 'bkm1122@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-05 11:35:03', 'Bkash', NULL, NULL, '2025-08-05 12:53:26'),
(10, 'bkm1122@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-05 11:36:19', 'Bkash', NULL, NULL, '2025-08-05 12:53:26'),
(11, 'bkm1122@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-05 11:38:15', 'Bkash', NULL, NULL, '2025-08-05 12:53:26'),
(12, 'bkm1122@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-05 11:38:51', 'Bkash', NULL, NULL, '2025-08-05 12:53:26'),
(17, 'bkm1122@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-05 12:58:59', 'Bkash', NULL, NULL, '2025-08-05 12:58:59'),
(18, 'bkm11222@gmail.com', 'Intro to Python Programming', 9.00, 'Installment', 100.00, 91.00, '2025-08-05 13:03:00', 'Bkash', NULL, NULL, '2025-08-05 13:03:00'),
(19, 'bkm11222@gmail.com', 'Intro to Python Programming', 9.00, 'Installment', 100.00, 91.00, '2025-08-05 13:05:25', 'Bkash', NULL, NULL, '2025-08-05 13:05:25'),
(20, 'bkm1122029@gmail.com', 'Intro to Python Programming', 9.00, 'Installment', 100.00, 91.00, '2025-08-05 17:35:18', 'Bkash', NULL, NULL, '2025-08-05 17:35:18'),
(21, 'bkm11220289@gmail.com', 'Intro to Python Programming', 8.00, 'Installment', 100.00, 92.00, '2025-08-06 05:47:15', 'Bkash', NULL, NULL, '2025-08-06 05:47:15'),
(22, 'bkm11220289@gmail.com', 'Intro to Python Programming', 80.00, 'Installment', 100.00, 20.00, '2025-08-06 05:51:17', 'Bkash', NULL, NULL, '2025-08-06 05:51:17'),
(23, 'olvia@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-08 15:28:07', 'Bkash', NULL, NULL, '2025-08-08 15:28:07'),
(24, 'olvia@gmail.com', 'Intro to Python Programming', 90.00, 'Installment', 100.00, 10.00, '2025-08-08 15:40:39', 'Bkash', NULL, NULL, '2025-08-08 15:40:39'),
(25, 'olvia@gmail.com', 'Intro to Python Programming', 90.00, 'Installment', 100.00, 10.00, '2025-08-08 15:41:41', 'Bkash', NULL, NULL, '2025-08-08 15:41:41'),
(26, 'olvia@gmail.com', 'Intro to Python Programming', 90.00, 'Installment', 100.00, 10.00, '2025-08-08 15:42:05', 'Bkash', NULL, NULL, '2025-08-08 15:42:05'),
(27, 'olvia@gmail.com', 'Intro to Python Programming', 90.00, 'Installment', 100.00, 10.00, '2025-08-08 15:42:26', 'Bkash', NULL, NULL, '2025-08-08 15:42:26'),
(28, 'olvia@gmail.com', 'Intro to Python Programming', 90.00, 'Installment', 100.00, 10.00, '2025-08-08 15:42:50', 'Bkash', NULL, NULL, '2025-08-08 15:42:50'),
(29, 'olvia@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-08 15:50:55', 'Bkash', NULL, NULL, '2025-08-08 15:50:55'),
(30, 'olvia@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-08 15:51:17', 'Bkash', NULL, NULL, '2025-08-08 15:51:17'),
(31, 'olvia@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-08 15:52:43', 'Bkash', NULL, NULL, '2025-08-08 15:52:43'),
(32, 'samihamaisha25531@gmail.com', 'Intro to Python Programming', 100.00, 'Full', 100.00, 0.00, '2025-08-11 15:24:00', 'Bkash', NULL, NULL, '2025-08-11 15:24:00');

-- --------------------------------------------------------

--
-- Table structure for table `email_opens`
--

CREATE TABLE `email_opens` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `opened_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

--
-- Dumping data for table `enrolled_courses`
--

INSERT INTO `enrolled_courses` (`id`, `user_email`, `course_id`, `progress`) VALUES
(1, 'mahiiii@gmail.com', 11, 0);

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
-- Table structure for table `internships`
--

CREATE TABLE `internships` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `internship_applications`
--

CREATE TABLE `internship_applications` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `internship_id` int(11) NOT NULL,
  `applied_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `internship_applications`
--

INSERT INTO `internship_applications` (`id`, `user_email`, `internship_id`, `applied_at`) VALUES
(1, 'bkm1122029@gmail.com', 2, '2025-08-05 23:47:08'),
(2, 'bkm1122029@gmail.com', 2, '2025-08-05 23:48:46'),
(3, 'bkm1122029@gmail.com', 2, '2025-08-05 23:48:57'),
(4, 'bkm1122029@gmail.com', 2, '2025-08-05 23:49:10');

-- --------------------------------------------------------

--
-- Table structure for table `internship_requests`
--

CREATE TABLE `internship_requests` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `internship_title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `title` varchar(255) NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `posted_by` varchar(255) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'job',
  `experience` text DEFAULT NULL,
  `availability` text DEFAULT NULL,
  `fee` varchar(50) DEFAULT NULL,
  `demo_link` varchar(255) DEFAULT NULL,
  `demo_schedule` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_posts`
--

INSERT INTO `job_posts` (`id`, `user_id`, `job_title`, `description`, `location`, `requirements`, `created_at`, `skills`, `deadline`, `title`, `approved`, `posted_by`, `type`, `experience`, `availability`, `fee`, `demo_link`, `demo_schedule`, `bio`) VALUES
(112, 110, '', 'I am a programer since 2010', 'Dhaka', NULL, '2025-08-14 14:38:56', 'Java,c++', NULL, 'Mentorship', 1, NULL, 'job', 'CSE', 'MON-FRI', '1000', NULL, NULL, ',,,,');

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `university` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `recent_profession` varchar(255) DEFAULT NULL,
  `demo_link` varchar(255) DEFAULT NULL,
  `demo_schedule` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentor_details`
--

INSERT INTO `mentor_details` (`mentor_id`, `user_id`, `company_name`, `started_year`, `rating`, `facilities`, `bio`, `location`, `profile_pic`, `created_at`, `updated_at`, `university`, `subject`, `recent_profession`, `demo_link`, `demo_schedule`) VALUES
(24, 120, '', '0000', NULL, NULL, ',,,,,,,,', 'Badda.Dhaka', NULL, '2025-08-14 10:25:32', '2025-08-14 10:25:32', 'United International University,Dhaka', 'Computer Science & Engineering', 'Professor', NULL, NULL),
(25, 110, '', '0000', NULL, NULL, NULL, 'Badda.Dhaka', NULL, '2025-08-14 13:09:21', '2025-08-14 13:09:21', 'United International University,Dhaka', 'Computer Science & Engineering', 'Professor', 'https://meet.google.com/abc-defg-hij', '0000-00-00 00:00:00');

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
-- Table structure for table `mentor_posts`
--

CREATE TABLE `mentor_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
(35, 'tarriiqgb@gmail.com', 'Samiha Maisha', 'Badda.Dhaka', 'Uiu', 'Python', '+8801738915383', '2025-08-14 11:11:17');

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
  `rating` varchar(10) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `university` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `recent_profession` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mentor_suggestions`
--

INSERT INTO `mentor_suggestions` (`id`, `email`, `company_name`, `location`, `rating`, `full_name`, `phone`, `university`, `subject`, `recent_profession`, `bio`) VALUES
(1, 'tarriiqgb@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(2, 'tarriiqgb@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(3, 'ak34@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(4, 'ak34@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(5, 'ak34@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(6, 'ak34@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(7, 'ak34@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(8, 'sampg9110@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(9, 'sm11@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(10, 'bbbbn@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(11, 'sakibaa@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(12, 'sakibaa@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(13, 'saki@gmail.com', 'Samiha sami', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(14, 'bkm0@gmail.com', 'samiha', 'lakecity', '5', '', NULL, '', '', NULL, NULL),
(15, 'oasss@gmail.com', 'WebDevelop', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(16, 'samiha10110@gmail.com', 'samiha', 'Badda.Dhaka', '5', '', NULL, '', '', NULL, NULL),
(17, 'rifat1232@gmail.com', NULL, 'Badda.Dhaka', NULL, 'Dr. Rifat Ahmed', '01738915382', 'United International University,Dhaka', 'Computer Science & Engineering', 'Professor', '.................'),
(18, 'rifat010@gmail.com', NULL, 'Badda.Dhaka', NULL, 'Dr. Rifat Ahmed', '01738915382', 'United International University,Dhaka', 'Computer Science & Engineering', 'Professor', '................'),
(19, 'rifatahmed12@gmail.com', NULL, 'Badda.Dhaka', NULL, 'Dr. Rifat Ahmed', '01738915382', 'United International University,Dhaka', 'Computer Science & Engineering', 'Professor', ',,,,,,,,');

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
-- Table structure for table `micro_internships`
--

CREATE TABLE `micro_internships` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `duration_hours` int(11) NOT NULL,
  `skills_required` varchar(255) NOT NULL,
  `location_type` enum('Remote','On-site','Hybrid') DEFAULT 'Remote',
  `company_name` varchar(255) NOT NULL,
  `application_link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `micro_internships`
--

INSERT INTO `micro_internships` (`id`, `title`, `description`, `duration_hours`, `skills_required`, `location_type`, `company_name`, `application_link`) VALUES
(1, 'Python Data Analysis Mini Project', 'Analyze a small dataset and provide insights.', 4, 'Python,Data Analysis,Pandas', 'Remote', 'DataCorp', 'https://datacorp.com/apply/123'),
(2, 'Frontend React Mini Build', 'Build a small React component for a web app.', 6, 'React,JavaScript,HTML,CSS', 'Remote', 'WebWorks', 'https://webworks.com/apply/456'),
(3, 'SEO Basics Micro Internship', 'Perform basic SEO audit for a website.', 3, 'SEO,Digital Marketing,Google Analytics', 'Remote', 'MarketPros', 'https://marketpros.com/apply/789'),
(4, 'Arduino Quick Setup Gig', 'Assemble and test Arduino circuits.', 5, 'Arduino,Electronics,Circuit Design', 'On-site', 'TechLabs', 'https://techlabs.com/apply/101'),
(5, 'Copywriting Short Task', 'Write short promotional content.', 2, 'Copywriting,Writing,Marketing', 'Remote', 'AdAgency', 'https://adagency.com/apply/202');

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
-- Table structure for table `opportunities`
--

CREATE TABLE `opportunities` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `skills_required` varchar(255) NOT NULL,
  `type` enum('Volunteer','Internship','Freelance') NOT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `opportunities`
--

INSERT INTO `opportunities` (`id`, `title`, `description`, `skills_required`, `type`, `link`) VALUES
(1, 'Web Dev Volunteer', 'Help non-profit build their website using HTML, CSS, and JavaScript.', 'Web Development,JavaScript,HTML,CSS', 'Volunteer', 'https://example.com/apply1'),
(2, 'Data Science Intern', 'Analyze datasets for a startup.', 'Data Science,Python', 'Internship', 'https://example.com/apply2'),
(3, 'Freelance PHP Developer', 'Build small web apps for clients.', 'PHP,MySQL', 'Freelance', 'https://example.com/apply3');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `bkash_txn_id` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `payment_intent_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_email`, `book_id`, `amount`, `payment_status`, `payment_method`, `transaction_id`, `bkash_txn_id`, `status`, `payment_intent_id`, `created_at`) VALUES
(1, 'oa@gmail.com', 4, NULL, NULL, 'bkash', NULL, NULL, 'completed', NULL, '2025-08-07 00:10:01'),
(2, 'oa@gmail.com', 4, NULL, NULL, 'bkash', NULL, NULL, 'completed', NULL, '2025-08-07 00:10:08'),
(3, 'oa@gmail.com', 4, NULL, NULL, 'bkash', NULL, 'Unknown', 'completed', NULL, '2025-08-07 00:11:35'),
(4, 'oa@gmail.com', 4, NULL, NULL, 'bkash', NULL, 'Unknown', 'completed', NULL, '2025-08-07 00:12:10'),
(5, 'oa@gmail.com', 4, NULL, NULL, 'bkash', NULL, 'Unknown', 'completed', NULL, '2025-08-07 00:12:15'),
(6, 'oa@gmail.com', 4, NULL, NULL, 'bkash', NULL, 'Unknown', 'completed', NULL, '2025-08-07 00:13:04'),
(7, 'oa@gmail.com', 4, NULL, NULL, 'bkash', NULL, 'Unknown', 'completed', NULL, '2025-08-07 00:13:12'),
(8, 'oa@gmail.com', 4, NULL, NULL, 'bkash', NULL, 'Unknown', 'completed', NULL, '2025-08-07 00:14:50'),
(9, 'oa@gmail.com', 4, NULL, NULL, 'bkash', NULL, 'Unknown', 'completed', NULL, '2025-08-07 00:16:48'),
(10, 'oa@gmail.com', 4, NULL, NULL, 'bkash', '', NULL, 'completed', NULL, '2025-08-07 00:17:58'),
(11, 'oa@gmail.com', 4, 100.00, NULL, 'bkash', 'N/A', NULL, 'completed', NULL, '2025-08-07 00:19:42'),
(12, 'oas@gmail.com', 4, 100.00, NULL, 'bkash', 'N/A', NULL, 'completed', NULL, '2025-08-07 10:14:11');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `creator_email` varchar(255) DEFAULT NULL,
  `required_skills` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'ongoing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) DEFAULT 0,
  `posted_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `creator_email`, `required_skills`, `status`, `created_at`, `approved`, `posted_by`) VALUES
(1, 'WebDevoloping', ',', 'bkm1122029@gmail.com', ',', 'ongoing', '2025-08-05 19:02:34', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `project_members`
--

CREATE TABLE `project_members` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `member_email` varchar(255) DEFAULT NULL,
  `role` varchar(100) DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_updates`
--

CREATE TABLE `project_updates` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `member_email` varchar(255) DEFAULT NULL,
  `update_text` text DEFAULT NULL,
  `update_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `skill` varchar(100) NOT NULL,
  `question_text` text NOT NULL,
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
-- Table structure for table `student_projects`
--

CREATE TABLE `student_projects` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `skills_used` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_projects`
--

INSERT INTO `student_projects` (`id`, `user_email`, `title`, `description`, `skills_used`, `image_path`, `created_at`, `approved`) VALUES
(5, 'ol@gmail.com', 'FutureBot', 'mmm', 'programming', 'project_uploads/1754576143_Dbms_report_1010.pdf', '2025-08-07 14:15:43', 0),
(9, 'samihaaa707@gmail.com', 'SkillHub – Personalized Learning Platform', 'SkillHub is a web application designed to recommend courses, projects, and mentors to users based on their skills and interests. Users can create profiles, select their skills, explore relevant learning resources, submit projects, and connect with mentors for guidance. The system uses a dynamic dashboard to show personalized recommendations and tracks progress in courses and skill-building activities.', 'Tools & Technologies:  Frontend: HTML5, CSS3, JavaScript, Bootstrap 5  Backend: PHP, MySQL  File Handling: PHP $_FILES for project uploads  Version Control: Git/GitHub  Optional: jQuery, AJAX for dynamic updates  Deployment: XAMPP / Local Server', 'project_uploads/1755187711_ffffff.pdf', '2025-08-14 16:08:31', 1);

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
  `phone` varchar(20) DEFAULT NULL,
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
  `location` varchar(100) DEFAULT NULL,
  `saved_degrees` text DEFAULT NULL,
  `registration_source` varchar(50) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `availability` varchar(255) DEFAULT NULL,
  `fee` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `trade_license` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `full_name`, `skills`, `institution`, `gpa`, `email`, `phone`, `password_hash`, `role`, `bio`, `profile_pic`, `registered_at`, `last_login`, `is_active`, `is_deleted`, `verification_status`, `profile_visibility`, `company_name`, `started_year`, `rating`, `facilities`, `location`, `saved_degrees`, `registration_source`, `experience`, `availability`, `fee`, `photo`, `trade_license`) VALUES
(1, 'rifat', NULL, NULL, NULL, NULL, 'samihamaisha232@gmail.com', NULL, '$2y$10$quM6yRJy/uO6vZ1nAuZUve5wOJoiFdrgfgG04pzBokYnCEGkQtSJG', 'student', NULL, NULL, '2025-07-19 17:37:04', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'samiha', NULL, NULL, NULL, NULL, 'samihamaisha231@gmail.com', NULL, '$2y$10$c/ucyQX98cJN4PSIqaUfResZoQkzmABSfqw4GOLyG0eUbBXY4fRFO', 'student', NULL, NULL, '2025-07-19 19:20:13', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'sm', 'Samiha Akter Maisha', 'programming', 'Uiu', 3.00, 'testuser@gmail.com', NULL, '$2y$10$Z5Xx7Hu9jzm2GVMBH0yc7uhWD./wJBf/WyIpb1haKY.G5KHF8tCCO', 'student', NULL, NULL, '2025-07-19 19:39:06', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'ssmm', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, PHP', 'Uiu', 3.00, 'testuser1@gmail.com', NULL, '$2y$10$3qALFMHZTVwo/dECmtj6keAopabvavUc3y34cZgeMpuYsDjmc1dRu', 'student', NULL, NULL, '2025-07-19 19:46:05', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'sm44', NULL, NULL, NULL, NULL, 'testuser44@gmail.com', NULL, '$2y$10$/qy2P4ZP50GnItqAboiVe.p5Dint1bhIBJ43hwux548S8p4FjVVXK', 'student', NULL, NULL, '2025-07-19 20:04:03', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'sm444', NULL, NULL, NULL, NULL, 'testuser444@gmail.com', NULL, '$2y$10$vYH9hj8vZkG4.H5mLTjOwuQB8Idcy21KRna9aXBxEx6RCjjN1.6g6', 'student', NULL, NULL, '2025-07-19 20:05:18', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'sm4449', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, SQL', 'Uiu', 3.00, 'testuser4444@gmail.com', NULL, '$2y$10$RRi54vuowhmrT/IWAf3K4.PSNfoWMZ781NvgfGxb2fj6iyZIdRvKG', 'student', NULL, NULL, '2025-07-19 20:06:28', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'sm44490', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Java, C++, JavaScript, React, PHP, SQL, Machine Learning, Deep Learning', 'Uiu', 3.00, 'testuser44044@gmail.com', NULL, '$2y$10$BR54QFvUXdUu.7lpbQ3NWOxJkxQ2umdKcsQWAzU9/Cik77yQTSi3W', 'student', NULL, NULL, '2025-07-19 20:19:59', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'jisha', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, React, Node.js, SQL, NoSQL', 'Uiu', 3.00, 'jisha@gmail.com', NULL, '$2y$10$vhgVpHFdW8oGYcBEQzBXeuo2lA/gGPXAt69xKBYusoZMoUImem8NC', 'student', NULL, NULL, '2025-07-19 20:34:16', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'jisha1', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Java, C++, JavaScript, React, PHP, SQL, Machine Learning, Deep Learning', 'Uiu', 3.00, 'jisha1@gmail.com', NULL, '$2y$10$K0SPfb.D0kWe6qL5V611Jexh/a8sLpfcfFKgBDhx/eob1oZZcbIHi', 'student', NULL, NULL, '2025-07-19 20:47:04', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'jisha111', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Java, C++, JavaScript, PHP, SQL, Machine Learning, Deep Learning, UI/UX Design', 'Uiu', 3.00, 'jisha11@gmail.com', NULL, '$2y$10$4ZTiwbs1nOxArZYc3zpVPu0soGojI4Q91HiE0cVkoDDUcgvuN7iu.', 'student', NULL, NULL, '2025-07-19 21:27:06', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'jisha1111', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, PHP', 'Uiu', 3.00, 'jisha111@gmail.com', NULL, '$2y$10$YVs6i5ibgo2XfZ0WMfYRxe1e/HrJjFU./gSmbM53qMUxNmMwQ8bXS', 'student', NULL, NULL, '2025-07-20 08:16:48', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'jisha11111', NULL, NULL, NULL, NULL, 'jisha1111@gmail.com', NULL, '$2y$10$ce7VfN65OrQtuzqmCGnfoOfcZ0bWI00OyKB8Acj8btBm1NGGk3zZm', 'mentor', NULL, NULL, '2025-07-20 08:51:19', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'mai1', NULL, NULL, NULL, NULL, 'tariq34@gmail.com', NULL, '$2y$10$K0SX6tChNDmAfJ9cWk7FJ.RBOhW06pcOrdcQCxkd88avq.FyPZt1W', 'mentor', NULL, NULL, '2025-07-20 09:01:07', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'jisha11118', NULL, NULL, NULL, NULL, 'jisha1181@gmail.com', NULL, '$2y$10$M6pBvPDQG9KwUKZnoOeaV.KyDkR3x5Or.JvUrkXHZ1QpgEDv4h6AK', 'mentor', NULL, NULL, '2025-07-20 09:06:09', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'Samiha00', NULL, NULL, NULL, NULL, 'testuser00@gmail.com', NULL, '$2y$10$OSP2m3GQjhlIcVUe2dC.IeLmfZ6BamTYBOa2rjnfzLvTVoF8ghChO', 'mentor', NULL, NULL, '2025-07-20 09:18:42', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'k', 'Samiha Akter Maisha', NULL, NULL, NULL, 'tariq7@gmail.com', NULL, '$2y$10$.fcg9AGm0t1f.EGzs1mST.oWVTAMzPLz3D8ydXlCrN0nIaMlVXi2S', 'mentor', 'We provide the best services', 'uploads/profile_pics/18_1753004145.jpeg', '2025-07-20 09:22:45', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'rifat000@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, PHP', 'Uiu', 3.00, 'samihamaisha2320@gmail.com', NULL, '$2y$10$M9vo0nCWl98ijtLvNbulzum/uyBg5QcMxi4Lbotfdt4PYL0Nm9aoK', 'student', NULL, NULL, '2025-07-20 09:40:27', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Samihap', NULL, NULL, NULL, NULL, 'testuserm@gmail.com', NULL, '$2y$10$zmSAPqRZUsQlheiyuTKD6.zbsXM56ZokkHf9ia9Gzor49CpN/5/XO', 'mentor', 'mm', NULL, '2025-07-20 09:41:53', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'rifatm@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, PHP', 'Uiu', 3.00, 'samihamais0@gmail.com', NULL, '$2y$10$eTPipCNcjlVoT2BRKrYZu.54SU9oZGe9dv.J1dC2CeFaL8k3fhWq6', 'student', NULL, NULL, '2025-07-20 09:44:05', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'rifatms@gmail.com', NULL, NULL, NULL, NULL, 'samihamaiss0@gmail.com', NULL, '$2y$10$x4z7NIl3GCwAvTCqujQ5G.9d.n3Z4R4019i6GTuxrapQTaO.6M1cG', 'mentor', 'nn', NULL, '2025-07-20 09:51:59', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'rifatmsn@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, PHP', 'Uiu', 3.00, 'samihamaissp0@gmail.com', NULL, '$2y$10$AdflNttsMRANHqyMXu2C3.2/7PjG0Lkg.DYgfdiyRSIfReRuiAe5G', 'student', NULL, NULL, '2025-07-20 09:53:04', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'rcifat@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, NoSQL', 'Uiu', 3.00, 'ssamihamais0@gmail.com', NULL, '$2y$10$kTI6V9Raen8HjMrKibi9Z.A2FTRfoBrpkTSr901ylZjdpFfdvSSDC', 'student', NULL, NULL, '2025-07-20 09:57:27', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'rcifatn@gmail.com', NULL, NULL, NULL, NULL, 'ssamiihamais0@gmail.com', NULL, '$2y$10$jQX/noIrjun/37lwfQSr3ujZY8cfSPIhlsxy83KyLxxtsqYZOAeay', 'mentor', 'm', NULL, '2025-07-20 09:58:45', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'rcifatvn@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, SQL', 'Uiu', 3.00, 'ssamiihamvais0@gmail.com', NULL, '$2y$10$kVa8CyYYEOy7Sto6E/C7q.HeoVrEImEA3XTTWKNYbQLswchpq1zMS', 'student', NULL, NULL, '2025-07-20 09:59:57', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'rcifatnmm@gmail.com', NULL, NULL, NULL, NULL, 'ssamniihamais0@gmail.com', NULL, '$2y$10$k2uIl/UD8dxgA0wVvNaE/u0rT7vc0f5HXY2ENCeuN.RhzY/nTIvSK', 'student', NULL, NULL, '2025-07-20 12:15:11', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'rcifratnmm@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, SQL', 'Uiu', 3.00, 'ssarmniihamais0@gmail.com', NULL, '$2y$10$gwb/HdF13tsXosRCgQFOFOI7DE0GQZRN7MRDnQ/4dN.cwgSjt3GAO', 'student', NULL, NULL, '2025-07-20 12:18:15', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'rcifrmm@gmail.com', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, Java, C++, C#, JavaScript, React, Node.js, Cloud Computing', 'Uiu', 3.00, 's0@gmail.com', NULL, '$2y$10$wVxLu0n84.YBFqJTvYa30uM3.ZKTf2sMHDyYTWVqYWwU9Pen1xgSu', 'student', NULL, NULL, '2025-07-20 12:53:04', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'rcifrmmm@gmail.com', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 's00@gmail.com', NULL, '$2y$10$fVBCdMj1IoNTaxhV3RInQu3SfM2HQ1rVnJrG9CsHBFIqP023J0zkm', 'student', NULL, NULL, '2025-07-20 14:12:43', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'rcifrmnmm@gmail.com', NULL, NULL, NULL, NULL, 's000@gmail.com', NULL, '$2y$10$bSUq6bjtDwFtsvF6eLZKrOgv0ajcb27rWCxN/mq2JfT6isbKEfMMi', 'student', NULL, NULL, '2025-07-20 14:51:22', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'rcifrmmnmm@gmail.com', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 's100@gmail.com', NULL, '$2y$10$.Lg073iJVAbj406KNGe6AeBv4map6XBhETmEZU6fA0BsBN/y9CTJe', 'mentor', 'nh', NULL, '2025-07-20 14:51:53', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'rcifiirmmnmm@gmail.com', 'Samiha Akter Maisha', NULL, NULL, NULL, 's11100@gmail.com', NULL, '$2y$10$KOq1HGIjowRaYDtIjX4SauozXhBX2xgWJ0Ulm.y3oHVTTz2cd5Why', 'mentor', 'nnn', 'uploads/profile_pics/33_1753032947.jpeg', '2025-07-20 15:42:25', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'rifatx@gmail.com', 'Samiha Akter Maisha', 'Java, C#', 'Uiu', 3.00, 'tariqx@gmail.com', NULL, '$2y$10$Vjs/pgeL2EkW5BuTi33oy.qiGFixDNCZF7yFOU51pEH7pxgYfkagu', 'student', NULL, NULL, '2025-07-20 16:01:25', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'rrifatx@gmail.com', NULL, NULL, NULL, NULL, 'ttariqx@gmail.com', NULL, '$2y$10$C/afeTHUsEtSIAmrMZPTAu7vuXdcyN9H4kTwoycQBznnU.7S44k.u', 'mentor', 'vv', NULL, '2025-07-20 16:04:11', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'scamiha', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'samihamais01@gmail.com', NULL, '$2y$10$AH6r3WW0rdGXj6FOupRGR.iraZuWbwg2PSWL39ujuzQ8U6vm6xpaK', 'student', NULL, NULL, '2025-07-20 16:06:56', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'samiham', 'Samiha Akter Maisha', 'JavaScript', 'Uiu', 3.00, 'samihamas0@gmail.com', NULL, '$2y$10$aCb1ypaNr5IOnqLPWtNT/OE4E1RJyrgWpYNGESyi4/3wnst1CQZ1G', 'student', NULL, NULL, '2025-07-20 16:14:57', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'rrrrifatx@gmail.com', 'Samiha Akter Maisha', 'Mobile App Development, Python, Leadership', 'Uiu', 3.00, 'tttntariqx@gmail.com', NULL, '$2y$10$3GUjcxaZ65U8Xix8Xmxrz.3zRlUmeC9QWdMXuajP3LDdxLw3/7mPq', 'student', NULL, NULL, '2025-07-20 17:07:53', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'samihamm', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'tariqg@gmail.com', NULL, '$2y$10$vz5NNgAVIiVYQT.om.b1He4Vf3F.TNuPFpL/GkiCK5YPDq9QW5Alu', 'mentor', 'nn', NULL, '2025-07-20 17:27:59', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 'sam', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'tarriqg@gmail.com', NULL, '$2y$10$Z0KCV.hZXGbLBqqP6BVuqOniu2kTlLWekhfmu5RcBQjLdHeFSg1.m', 'student', NULL, NULL, '2025-07-20 20:36:28', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 'Sami', NULL, NULL, NULL, NULL, 'testuserz@gmail.com', NULL, '$2y$10$Zw0tgZq3oytwBILEekhbju4X1wk2fZzOzlSqk.OfGjFDNB.Q2yoE2', 'mentor', 'bb', NULL, '2025-07-20 21:08:54', NULL, 1, 0, 'pending', 'public', 'WebDevelop', '2010', '5', 'everything', 'Dhaka', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'sammm', NULL, NULL, NULL, NULL, 'tarriiqgb@gmail.com', NULL, '$2y$10$IYlVZuI1j0Vq.zLY1NwaT.wufpjZSdaAkcuCPB79kVFj1wA6WuNSa', 'mentor', 'bb', NULL, '2025-07-20 21:13:12', NULL, 1, 0, 'pending', 'public', 'samiha', '2010', '5', 'everything', 'Dhaka', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'sammmm', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'tarriiqgbg@gmail.com', NULL, '$2y$10$W7WLtN7JM/1BbXvob.6vVOv5vyCy2gROfHKeIqCsdZKAqeUv9isSu', 'student', NULL, NULL, '2025-07-20 21:58:39', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 's', NULL, NULL, NULL, NULL, 'sa@gmail.com', NULL, '$2y$10$6e50/agz/En5p4yrN.kRzONIUjA9fZ98VcVmGlyR/0ZFZVjdQkfRu', 'mentor', NULL, NULL, '2025-07-20 22:01:35', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'sa', NULL, NULL, NULL, NULL, 'saa@gmail.com', NULL, '$2y$10$NH9rBNUHRnwtxnBmVcR//ekXDvwnsnzFDrBzjc/ii5oiOsDOmjKFG', 'mentor', NULL, NULL, '2025-07-20 22:04:44', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'ma', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'ma1@mail.com', NULL, '$2y$10$Y2YuN8YepSBeyk/jYmnTMeZKgB3ULQs5zxy.4rQ/uPYYdUwoJ3D8W', 'student', NULL, NULL, '2025-07-20 22:55:22', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'maa', NULL, NULL, NULL, NULL, 'maa1@mail.com', NULL, '$2y$10$co0aLwVSH8zhxVVWERe.x.ndO01v1DkKvC3dMSVOwJnLTEuSlJxpy', 'mentor', NULL, NULL, '2025-07-20 23:15:36', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'maaa', 'Samiha Akter Maisha', 'Web Development, Mobile App Development, Python, C++', 'Uiu', 3.00, 'maaa1@mail.com', NULL, '$2y$10$cfmh3AoO/kVtlMjAG3w3aOqDWzbaLyXXnOWHtCb/Kmc35vdkpDR8K', 'student', NULL, NULL, '2025-07-20 23:23:32', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'maaaa', NULL, NULL, NULL, NULL, 'maaa12@mail.com', NULL, '$2y$10$2qMR9GLJamaGNbAD/HuSa.zMymN4L1AU.Zp0bOOskj1mPt/P3RJi.', 'student', NULL, NULL, '2025-07-21 07:32:55', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'maaaai', NULL, NULL, NULL, NULL, 'maaai12@mail.com', NULL, '$2y$10$PzLFJqh8Z0MCxs9jH7963OEKGGHV24FU9bcrMMz4hy7.ABed6oZFa', 'mentor', NULL, NULL, '2025-07-21 07:34:39', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'akter', 'Samiha Akter Maisha', NULL, NULL, NULL, 'ak34@gmail.com', NULL, '$2y$10$yMRb2lydZx6TXuAFMpuWweiPp/kMayDTNj2jBB/epUS4ojMgyZyuu', 'mentor', 'nn', 'uploads/profile_pics/51_1753098572.jpeg', '2025-07-21 07:43:18', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 'akterr', NULL, NULL, NULL, NULL, 'akt34@gmail.com', NULL, '$2y$10$Y2KwCr5CBarW79HrD0/pu.hQC34e7imDrQKDB.BaNb8mOodm2eB0q', 'student', NULL, NULL, '2025-07-21 09:06:18', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'akterrrr', 'Samiha Akter Maisha', 'Web Development', 'Uiu', 3.00, 'ak344@gmail.com', NULL, '$2y$10$YOisISHh8blQ9NxAJMOzc.JcTRFBHnOWPZ6C6T1NrUT31EQ0Qs422', 'student', NULL, NULL, '2025-07-21 09:06:56', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'akterro', NULL, NULL, NULL, NULL, 'akt348@gmail.com', NULL, '$2y$10$amqm0X6YQvc7Ox6NOYvou.t2c2.wTSDwYMueIb9vPC6s6h2wCs3ma', '', NULL, NULL, '2025-07-21 13:46:41', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 'a', NULL, NULL, NULL, NULL, 'aak344@gmail.com', NULL, '$2y$10$Gxf6a5lTeSnL/iMFnEBfu.RM42Xn5Os8UwOmNsE0tuJQuaAAkAhEK', '', NULL, NULL, '2025-07-21 13:49:02', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'akterroo', NULL, NULL, NULL, NULL, 'akt3488@gmail.com', NULL, '$2y$10$CiIudaWajwuyT0L7Oppo8epucvP0864s389or6eI4Dv5XKLYe/nEC', '', NULL, NULL, '2025-07-21 13:54:16', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'akkterroo', NULL, NULL, NULL, NULL, 'aktt3488@gmail.com', NULL, '$2y$10$pUWTVlX.vA1OGWdu9YyjyeROjAyJ1gjWZzzqBPpbUTso/gZ3dy6Wq', '', NULL, NULL, '2025-07-21 13:58:33', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 'att3488@gmail.com', NULL, NULL, NULL, NULL, 'tariqul@gmail.com', NULL, '$2y$10$leAroyVca2sPdKHcKh62Le1iKcChrjHClu6G1HsKclVAE1RPzIIU2', '', NULL, NULL, '2025-07-21 13:59:54', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 'samihac', NULL, NULL, NULL, NULL, 'samihamai0@gmail.com', NULL, '$2y$10$vuQiowRoXO83Ja2XpJNRTO8ylNLwfI5Y0nN3dlYNcXifU.O4rWxOe', '', NULL, NULL, '2025-07-21 14:02:16', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 'akkterroox', NULL, NULL, NULL, NULL, 'akttx3488@gmail.com', NULL, '$2y$10$2ruY9tkvO3FZc9iVvO31..NwiQV/Ujl8XhLm2HFWIb2EkDYd35Jey', '', NULL, NULL, '2025-07-21 14:04:28', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 'rif', NULL, NULL, NULL, NULL, 'testr@gmail.com', NULL, '$2y$10$3MPupDsdgM4tG03IJPJHfOy4mba4Z1moFonWL36hcO0e8qKrY8cXi', '', NULL, NULL, '2025-07-21 14:08:42', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 'samp', NULL, NULL, NULL, NULL, 'samp1010@gmail.com', NULL, '$2y$10$o0bxd36sZf0P6D3pBqG5..BTpazGqHIm/8rFCw7fYLTFDOn1le6fa', '', NULL, NULL, '2025-07-21 14:12:10', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 'sampp', NULL, NULL, NULL, NULL, 'samp110@gmail.com', NULL, '$2y$10$UJ6QK9nNoGW88LTXnai3o.vflSVbq7feKL55XQTmEa8CWPaCgzjuG', '', NULL, NULL, '2025-07-21 14:16:01', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 'samppp', NULL, NULL, NULL, NULL, 'samp9110@gmail.com', NULL, '$2y$10$gHEQzOuS6OJcOnNk5K1iXuOztaTDqg0S2ZraMOvaEqwRwTuh6Ueia', '', NULL, NULL, '2025-07-21 15:55:35', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 'riff', NULL, NULL, NULL, NULL, 'ctestr@gmail.com', NULL, '$2y$10$YhoG97G8mKUOGvbbyVYrxuMiz01rPiikovaIhXntw5p2JswaaiVwm', '', NULL, NULL, '2025-07-21 15:56:35', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 'samg', NULL, NULL, NULL, NULL, 'sampg9110@gmail.com', NULL, '$2y$10$cS.gejXkgA5KWOgbrDk9zezt6A.hW240IjBXWMwMH2CXKqRfWorA2', 'mentor', NULL, NULL, '2025-07-21 16:42:13', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 'smm', 'Samiha Maisha', NULL, NULL, NULL, 'sm11@gmail.com', NULL, '$2y$10$2QiVpAh5j7hrPX.XzXj/DecF6pTgkMPtWo9unz4s9XbSHW0ricN/6', 'mentor', 'Best company', NULL, '2025-07-22 09:33:33', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 'ayad', NULL, NULL, NULL, NULL, 'ayad@gmail.com', NULL, '$2y$10$sIy55zHY4goQYwGPpn8Q1./3IKEx7go4rO6xr7rIP/s4PbCWB.vcm', '', NULL, NULL, '2025-07-22 14:12:03', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 'ayadd', 'Samiha Akter Maisha', 'Python', 'Uiu', 3.00, 'ayadd@gmail.com', NULL, '$2y$10$iGBH/38wqx0UteCdzTTVBe4MlXHhg7VhRn7owA2Tii8FOrF6Dkgla', 'student', NULL, NULL, '2025-07-22 15:12:40', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 'ayaadd', 'smm', 'Web Development', 'Uiu', 3.00, 'ayaadd@gmail.com', NULL, '$2y$10$cqEdJ8WOTVsuNUlAGNM.D.AhFH7fS1O3OY.84o8g/TtPAtv9p0mba', 'student', NULL, NULL, '2025-07-23 08:53:34', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 'samihammmmmm', NULL, NULL, NULL, NULL, 'ayaamdd@gmail.com', NULL, '$2y$10$6CahQ1GEMEWO7caL8W80V.SCGhe/g5FrxGwAQcCSFXtg84OWG8uEu', 'mentor', NULL, NULL, '2025-07-23 15:53:53', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 'bb', 'smm', 'Python', 'Uiu', 3.00, 'ayabamdd@gmail.com', NULL, '$2y$10$1UndGmXMgnlv6.dZC0bY0ONtVZf/fsyGhmARME2oW2rFE1WFhFnr6', 'student', NULL, NULL, '2025-07-23 15:56:33', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 'rafi', NULL, NULL, NULL, NULL, 'mahi@gmail.com', NULL, '$2y$10$OkPt6VvAVEIZy43Cot.iru8z6Syi4pGiYmsCdAEOLiEilGaCUACY2', 'mentor', NULL, NULL, '2025-07-23 15:59:48', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 'rafii', 'Samiha Akter Maisha', 'Python, C++', 'Uiu', 3.00, 'mahii@gmail.com', NULL, '$2y$10$auUNdHifG3LPpb7TPvHkHOH5XUWp/vHW2hTeyWZg6mw7CSGCMGVTa', 'student', NULL, NULL, '2025-07-23 16:06:44', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 'rafiii', 'Samiha Akter Maisha', 'Python', 'Uiu', 3.00, 'mahiii@gmail.com', NULL, '$2y$10$SzX8uTYwGsfLdt263GDsJOvVl8c9qJn.N0.k9ZUHObY8jaXLi/8xC', 'student', NULL, NULL, '2025-07-24 10:38:44', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 'rafiiii', 'Samiha Akter Maisha', 'Python, JavaScript', 'Uiu', 3.00, 'mahiiii@gmail.com', NULL, '$2y$10$.P2Cw.cu8FxMznUnDMXGM.5mwtQYpuV8pFQuQhgSPlnXmFw6Wc1fu', 'student', NULL, NULL, '2025-07-24 11:42:32', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 'Rahim', 'Samiha Akter Maisha', 'Python', 'Uiu', 3.00, 'rahim@gmail.com', NULL, '$2y$10$t191I6mklinFUcRu.sPOuO4HRBOEeg5TFepjGKxgVwqCuzUK8eDGi', 'student', NULL, NULL, '2025-07-24 15:56:32', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 'Rahimm', 'Samiha Akter Maisha', 'Python, Java, C++, PHP, SQL, Machine Learning, Deep Learning', 'Uiu', 3.00, 'rahimm@gmail.com', NULL, '$2y$10$38GheO8NL5fjNglS1Cnnve..3/EJCrnncdf9bfHLB20GbfGk0RRPO', 'student', NULL, NULL, '2025-07-24 17:05:38', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 'Rahimmm', 'Samiha Akter Maisha', 'Python', 'Uiu', 3.00, 'rahimmm@gmail.com', NULL, '$2y$10$5UqTcCQ8f6pBw9jP6tIche4QBN06RF1KAjpZbgHkNulfLKVSP7tUu', 'student', NULL, NULL, '2025-07-25 16:49:01', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 'bbbb', 'Samiha Akter Maisha', 'Python, Java, C++', 'Uiu', 3.00, 'bb@gmail.com', NULL, '$2y$10$yXyP8gIy9jdLugExRCOewuJZ3fdSQj0BpeRLmCZyiOzlQlMOwoxxW', 'student', NULL, NULL, '2025-07-26 04:10:01', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 'irteza yeasmin', 'Irteza Yeasmin', 'Python', 'Uiu', 3.00, 'irteza@gmail.com', NULL, '$2y$10$8b16k7peuNZlo1am1edRTeSBC6rmzIe.PAE.JYFW9HzAuj0.dDxP6', 'student', NULL, NULL, '2025-07-26 06:53:51', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 'bnb', 'Irteza Yeasmin', 'Python', 'Uiu', 3.00, 'bbn@gmail.com', NULL, '$2y$10$..9vshRhH5GKPYxUT6OzWeNaN5y7yzoCUKvT3C/01/UJDGiavKoOC', 'student', NULL, NULL, '2025-07-28 14:19:03', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 'bnbb', 'Irteza Yeasmin', 'Python', 'Uiu', 3.00, 'bbbn@gmail.com', NULL, '$2y$10$eHyXNWdHXq3PZaPWJ93eiucBs2uVC0/1mYf.4l4rUX72eudmhTmPe', 'student', NULL, NULL, '2025-07-29 05:03:47', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 'bnbbb', 'Samiha Akter Maisha', NULL, NULL, NULL, 'bbbbn@gmail.com', NULL, '$2y$10$6lgEb4sMwW9khApvo./a7e2Df40KpHuYXgd.UqhT6Rgbsrpz1WWgG', 'mentor', 'mm', 'uploads/profile_pics/84_1753765764.png', '2025-07-29 05:08:43', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 'riiifat@gmail.com', NULL, NULL, NULL, NULL, 'samihamaisha2341@gmail.com', NULL, '$2y$10$XDmLFCxHr1GtU1baJVwRNuBWdXj7bF5usJK4JcI6NNCHp5qLbq0em', '', NULL, NULL, '2025-07-29 05:14:43', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 'sakiba', 'Irteza Yeasmin', 'Python', 'Uiu', 3.00, 'sakiba@gmail.com', NULL, '$2y$10$nVgU1/82dWsfw1qd4YBHfeQmUFG/LiV5GNklFOgMuAhMnOAvkjQyi', 'student', NULL, NULL, '2025-07-29 06:19:12', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 'sakibaa', NULL, NULL, NULL, NULL, 'sakibaa@gmail.com', NULL, '$2y$10$cwt/CIr4kVE5fUBYVR9qYuVQZ09QkyTjCCgNCKzfIEZg3kAqUqzy2', 'mentor', NULL, NULL, '2025-07-29 06:35:49', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 'samiha sami', 'Samiha Akter Maisha', NULL, NULL, NULL, 'saki@gmail.com', NULL, '$2y$10$Aa8Wo/0hwKl8E41fFgUJk.6tQhIZLlCYireEib07SDbNl2Oc3/dbS', 'mentor', 'Programmer', 'uploads/profile_pics/88_1753782043.jpeg', '2025-07-29 09:39:36', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 'n', 'Irteza Yeasmin', 'Python', 'Uiu', 3.00, 'n@gmail.com', NULL, '$2y$10$RbhUorQLIwShms1xGT9eouap5FVzAuU3MrFpyJHkjMqLQbxoa11eO', 'student', NULL, NULL, '2025-07-29 12:30:14', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 'tisha', NULL, NULL, NULL, NULL, 'tisha@gmail.com', NULL, '$2y$10$CNiUvUeLiqHyRYW3IxMHVOc5BtnefvUQ7eH9Mx7Kw2pZcy1e67wXm', 'student', NULL, NULL, '2025-07-29 13:59:58', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 'tish', 'Irteza Yeasmin', 'Python', 'Uiu', 3.00, 'tish@gmail.com', NULL, '$2y$10$3cnFp6KsUKKO9wkiU9hTlOa6kkOCwJB/4/wKsTfJzchimj4/vOlT6', 'student', NULL, NULL, '2025-07-31 13:31:13', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 'cvb', 'Irteza Yeasmin', 'Web Development, Python', 'Uiu', 3.00, 'ak304@gmail.com', NULL, '$2y$10$1qHqGPz5ZTYWDQ.r3ajcpu.KQaKhBdpcYH.HEJ4xe7ZUL93lx.T5e', 'student', NULL, NULL, '2025-08-03 14:13:05', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 'bkmm', 'Samiha Akter Maisha', NULL, NULL, NULL, 'bkm0@gmail.com', NULL, '$2y$10$.bFvmw2q/X8/RfBmMcaTruJhx/tcpdDuaqB8Z5dykkPT2CbrI/eBa', 'mentor', 'mm', 'uploads/profile_pics/93_1754231143.jpeg', '2025-08-03 14:24:53', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 'cvbb', NULL, NULL, NULL, NULL, 'ak3094@gmail.com', NULL, '$2y$10$1Ia2eIgPHzT9uHm/jEFiXuqQg.tch6QHtNtnHWxON4eLgk61zbs2y', 'mentor', NULL, NULL, '2025-08-03 18:43:09', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 'kik', NULL, NULL, NULL, NULL, 'kik@gmail.com', NULL, '$2y$10$oUs34QgxnTRio.B01N/lW.U622KR3RsL7h3sx/L7qoVc3dzExx6Ka', 'student', NULL, NULL, '2025-08-04 12:22:00', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 'bkmmm', 'Samiha Akter Maishamm', 'Python', 'Uiu', 3.00, 'bkm122@gmail.com', NULL, '$2y$10$3/PDmlxHdANtRkE7s/niOOViNm3gXD6r1/jBhnsbc4V.xU3PvHFve', 'student', NULL, NULL, '2025-08-04 14:26:57', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 'bkmmmm', 'Samiha Akter Maishamm', 'Python', 'Uiu', 3.00, 'bkm1122@gmail.com', NULL, '$2y$10$.D/j4XFLj3HYFWWwBGPjVOZn5v44OReloLKfO0x6zrMrKfmconJ3S', 'student', NULL, NULL, '2025-08-05 09:02:15', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 'sm10', 'Samiha Akter Maishamm', 'Python', 'Uiu', 3.00, 'bkm11222@gmail.com', NULL, '$2y$10$aKIgay9otiX4hAzVXLm8Kud5wGvmlj7CWFNCh9mtjJIuiWAnvTv.2', 'student', NULL, NULL, '2025-08-05 13:02:30', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 'sm100', 'Samiha Akter Maisha', NULL, NULL, NULL, 'bkm112202@gmail.com', NULL, '$2y$10$05EZWzRX20/5LOkvQliWJeAk0cUbxGNKQZ36akx8rDADt6MYQCIjq', 'mentor', 'I am a instructor', 'uploads/profile_pics/99_1754402835.jpeg', '2025-08-05 14:05:48', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 'sm1009', 'Samiha Akter Maishamm', 'Java', 'Uiu', 3.00, 'bkm1122029@gmail.com', NULL, '$2y$10$bS1uIrDrrgsNZTSu6cbiPuUkhdswr7kiSHcpjX9NOdbVkT38AKjpK', 'student', NULL, NULL, '2025-08-05 14:32:41', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL),
(101, 'sm10090', 'Samiha Akter Maishamm', 'Java', 'Uiu', 3.00, 'bkm11220289@gmail.com', NULL, '$2y$10$RiU.zDrWhl3SXvCnBtocLufuvSazrp7QnLAMOPU63TNDzIGPMNTzK', 'student', NULL, NULL, '2025-08-06 04:11:35', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 'samihax', 'Samiha Akter Maishamm', 'Python', 'Uiu', 3.00, 'mbxm@gmail.com', NULL, '$2y$10$/qDYuAKDDFvC6fPYvT/qNOb5q7lg3QkgXb2enq1KGae50b1.tt/kW', 'student', NULL, NULL, '2025-08-06 06:55:13', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 'orpa', 'Samiha Akter Maishamm', 'Python', 'Uiu', 3.00, 'o@gmail.com', NULL, '$2y$10$FURuGe8GQ6ukAjwH02V5v.UI9qODrudbS7B9OD5xP9l0vjRp1P6G.', 'student', NULL, NULL, '2025-08-06 18:32:50', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 'orpaa', 'Samiha Akter Maishamm', 'Python', 'Uiu', 3.00, 'oa@gmail.com', NULL, '$2y$10$vZ6R0MJOlSvFaKB9irA2vu5z.JHM9JEpAJ4dM2VLIxuIiPS185UQe', 'student', NULL, NULL, '2025-08-06 22:22:09', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 'os', 'Samiha Akter Maishamm', 'Python', 'Uiu', 3.00, 'oas@gmail.com', NULL, '$2y$10$C0Z9Mg40/.8oU9Oo/.tEHev7YR0wpmVlbNeF9EVN.IvWi/TVAejNi', 'student', NULL, NULL, '2025-08-07 10:07:20', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 'oss', NULL, NULL, NULL, NULL, 'oass@gmail.com', NULL, '$2y$10$mrOLmk.gLaM8TuEhxTcYd.yxLWCT46V6pKG1RqViqROp.xQNo5hJG', 'mentor', NULL, NULL, '2025-08-07 11:35:46', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 'osss', 'Samiha Akter Maisha', 'Python', 'Uiu', 3.00, 'oasss@gmail.com', NULL, '$2y$10$oXW6SQOk/0JRgkZPUNZTRuerHp5WWxVnU65L0xLcGWmwlT15ehmsq', 'mentor', '....', 'uploads/profile_pics/107_1754569340.jpeg', '2025-08-07 11:46:18', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 'ossss', 'Samiha Akter Maishamm', 'Python', 'Uiu', 3.00, 'oassss@gmail.com', NULL, '$2y$10$tfIryG5wdhRzMJH2lmN.o.JOMQNYvjAV0ihZqff/do5V1xON0Gddy', 'student', NULL, NULL, '2025-08-07 13:36:04', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 'ol', 'Samiha Akter Maisha', 'Python', 'Uiu', 3.00, 'ol@gmail.com', NULL, '$2y$10$thJwKMKttSKv2hpH9b232eUd48hTGeSQPZbFAs4qCp0.GLuy2TQN2', 'student', NULL, NULL, '2025-08-07 13:50:26', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 'olv', 'Dr. Rifat Ahmed', NULL, NULL, NULL, 'olv@gmail.com', '01738915382', '$2y$10$qRETK7v2UGvA1iXCoXekN.B/KAuNLodWnfK1a1IDSTKSLy7iS.Um2', 'mentor', '.........', 'uploads/profile_pics/110_1755176982.jpeg', '2025-08-07 17:36:39', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 'olvia', 'Samiha Akter Maisha', 'Python, Java', 'Uiu', 3.00, 'olvia@gmail.com', NULL, '$2y$10$gp5btG2TO5iZYvavjhCZxeaW3eQ6tw8CaGLxk9PTDRDaT3iFJN2r.', 'student', NULL, NULL, '2025-08-08 12:48:45', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 'olviaa', 'Samiha Akter Maisha', 'Python, Java', 'Uiu', 3.00, 'olviaa@gmail.com', NULL, '$2y$10$W0H4Jcxdp.GnZZbE.aHhuub4SFdYd/LMOBXxOxmgNN.Kx8X/DjQGK', 'student', NULL, NULL, '2025-08-09 05:14:14', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 'olvi', NULL, NULL, NULL, NULL, 'olvi@gmail.com', NULL, '$2y$10$OY3GJUtcNXMHWWB.Y23tuOD4G/4KL2cyePHlr99sZTIBCVa/.exqq', 'student', NULL, NULL, '2025-08-10 17:45:32', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 'olviiiiiiii', NULL, NULL, NULL, NULL, 'olviiiii@gmail.com', NULL, '$2y$10$reMb2gW3TOEMZXw3wFUOeOjqRIowCEM5vhl8UD2.Z4pj5qGpG7ICW', 'student', NULL, NULL, '2025-08-10 17:45:55', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 'mai123', 'Samiha Akter Maisha', 'Python, Java', 'Uiu', 3.00, 'moi@gmail.com', NULL, '$2y$10$3eMe8c0ihqd6hrYAHfaOd.l4qtQtOlLglwydOd4KftDQIzQG.ezkW', 'student', NULL, NULL, '2025-08-10 17:47:52', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(116, 'adip', NULL, NULL, NULL, NULL, 'adip@gmail.com', NULL, '$2y$10$r/52JuuaQc1vrba.b1zYNuUbCFa3m7i1FXH.emM8BXvN5YFA8z7QG', 'student', NULL, NULL, '2025-08-11 15:20:28', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 'samihamaisha0', 'Samiha Akter Maisha', 'Python, Java', 'Uiu', 3.00, 'samihamaisha25531@gmail.com', NULL, '$2y$10$dTJZBWCXVLaDyMZnBZjvS.JhU58d.UjV.8WhQ.wT1XCpHMYQmfiSy', 'student', NULL, NULL, '2025-08-11 15:21:10', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 'samihamaisha0000', NULL, NULL, NULL, NULL, 'samihamaisha200@gmail.com', NULL, '$2y$10$UdnMt6luCBJ77NpGddgXy.EdnDKpYRYbZZ9pAtXK.MZtIF9RkCh/y', 'mentor', NULL, NULL, '2025-08-11 15:33:08', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 'samiha209', NULL, NULL, NULL, NULL, 'samiha7200@gmail.com', NULL, '$2y$10$DWc6jN/UQxLNtAiEC2Q/pOdEj.AWI1RFMvYAT4whtY5RuOpb7/VJq', 'student', NULL, NULL, '2025-08-12 16:28:32', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 'samiha10', 'Dr. Rifat Ahmed', 'Web Development, Python, Java', 'Uiu', 3.00, 'rifatahmed12@gmail.com', '01738915382', '$2y$10$oj.dInaZN3DtDbeNSZYzkOy2oBOAeo9ScjriIHIN/LAGnNJoRrQ9m', 'mentor', '...................', 'uploads/profile_pics/120_1755166413.jpeg', '2025-08-12 16:30:02', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 'samii', 'Samiha Akter Maisha', 'Web Development, Python, Java', 'Uiu', 3.00, 'samihaaa7200@gmail.com', NULL, '$2y$10$voq9IpB2er4GPNAzxqSpA.AJoN80AZFkkWYTwEqUbLeaRvMTUCLue', 'student', NULL, NULL, '2025-08-12 18:03:13', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 'Samiha_Akter_Maisha', 'Samiha Akter Maisha', 'Python, Java', 'Uiu', 3.00, 'samihaaa707@gmail.com', NULL, '$2y$10$2yEhsi2ZEis.gy00fKXzEO56XJEYxCF4l/T6Q33gDadJbqFK6J34.', 'student', NULL, NULL, '2025-08-14 15:57:06', NULL, 1, 0, 'pending', 'public', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
-- Table structure for table `user_courses`
--

CREATE TABLE `user_courses` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_courses`
--

INSERT INTO `user_courses` (`id`, `user_email`, `course_id`, `enrolled_at`) VALUES
(1, 'mahiiii@gmail.com', 11, '2025-07-24 12:48:15'),
(5, 'mahiiii@gmail.com', 2, '2025-07-24 12:52:16'),
(16, 'mahiiii@gmail.com', 16, '2025-07-24 14:04:49'),
(17, 'mahiiii@gmail.com', 7, '2025-07-24 14:09:14'),
(18, 'mahiiii@gmail.com', 3, '2025-07-24 14:14:22');

-- --------------------------------------------------------

--
-- Table structure for table `user_course_milestones`
--

CREATE TABLE `user_course_milestones` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `milestone_text` varchar(255) DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_course_milestones`
--

INSERT INTO `user_course_milestones` (`id`, `user_email`, `course_id`, `milestone_text`, `is_completed`) VALUES
(1, 'mahiiii@gmail.com', 2, 'Read Chapter 1', 0),
(2, 'mahiiii@gmail.com', 2, 'Participate in Discussion', 0),
(3, 'mahiiii@gmail.com', 2, 'Pass Final Test', 0);

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
  `completed_at` datetime DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `skill` varchar(255) NOT NULL,
  `milestone_index` int(11) NOT NULL
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
(12, 'mahii@gmail.com', 'Java', 2, 0, NULL),
(13, 'mahiiii@gmail.com', 'Python', 0, 0, NULL),
(14, 'mahiiii@gmail.com', 'Python', 1, 0, NULL),
(15, 'mahiiii@gmail.com', 'Python', 2, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `book_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_email`, `book_id`, `created_at`, `added_at`) VALUES
(3, 'oa@gmail.com', 4, '2025-08-06 23:26:34', '2025-08-07 05:26:34'),
(4, 'oassss@gmail.com', 4, '2025-08-07 13:46:00', '2025-08-07 19:46:00');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `book_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_job_approvals`
--
ALTER TABLE `admin_job_approvals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `appointed_list`
--
ALTER TABLE `appointed_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
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
-- Indexes for table `chat_history`
--
ALTER TABLE `chat_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_otps`
--
ALTER TABLE `company_otps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `company_posts`
--
ALTER TABLE `company_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

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
-- Indexes for table `course_milestones`
--
ALTER TABLE `course_milestones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_payments`
--
ALTER TABLE `course_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_opens`
--
ALTER TABLE `email_opens`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `internships`
--
ALTER TABLE `internships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `internship_applications`
--
ALTER TABLE `internship_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_email` (`user_email`),
  ADD KEY `internship_id` (`internship_id`);

--
-- Indexes for table `internship_requests`
--
ALTER TABLE `internship_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

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
-- Indexes for table `mentor_posts`
--
ALTER TABLE `mentor_posts`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `micro_internships`
--
ALTER TABLE `micro_internships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `opportunities`
--
ALTER TABLE `opportunities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_updates`
--
ALTER TABLE `project_updates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `student_projects`
--
ALTER TABLE `student_projects`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `user_courses`
--
ALTER TABLE `user_courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enroll` (`user_email`,`course_id`);

--
-- Indexes for table `user_course_milestones`
--
ALTER TABLE `user_course_milestones`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_email`,`book_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_job_approvals`
--
ALTER TABLE `admin_job_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointed_list`
--
ALTER TABLE `appointed_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `chat_history`
--
ALTER TABLE `chat_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `company_otps`
--
ALTER TABLE `company_otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_posts`
--
ALTER TABLE `company_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_profiles`
--
ALTER TABLE `company_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- AUTO_INCREMENT for table `course_milestones`
--
ALTER TABLE `course_milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `course_payments`
--
ALTER TABLE `course_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `email_opens`
--
ALTER TABLE `email_opens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrolled_courses`
--
ALTER TABLE `enrolled_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hire_requests`
--
ALTER TABLE `hire_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `internships`
--
ALTER TABLE `internships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `internship_applications`
--
ALTER TABLE `internship_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `internship_requests`
--
ALTER TABLE `internship_requests`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

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
  MODIFY `mentor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `mentor_hires`
--
ALTER TABLE `mentor_hires`
  MODIFY `hire_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentor_posts`
--
ALTER TABLE `mentor_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mentor_requests`
--
ALTER TABLE `mentor_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `micro_internships`
--
ALTER TABLE `micro_internships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opportunities`
--
ALTER TABLE `opportunities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_members`
--
ALTER TABLE `project_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_updates`
--
ALTER TABLE `project_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `student_projects`
--
ALTER TABLE `student_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `suggestions`
--
ALTER TABLE `suggestions`
  MODIFY `suggestion_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `userskills`
--
ALTER TABLE `userskills`
  MODIFY `user_skill_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_courses`
--
ALTER TABLE `user_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `user_course_milestones`
--
ALTER TABLE `user_course_milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_enrollments`
--
ALTER TABLE `user_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_milestones`
--
ALTER TABLE `user_milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `user_skill_progress`
--
ALTER TABLE `user_skill_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_history`
--
ALTER TABLE `chat_history`
  ADD CONSTRAINT `chat_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `company_profiles`
--
ALTER TABLE `company_profiles`
  ADD CONSTRAINT `company_profiles_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `internships`
--
ALTER TABLE `internships`
  ADD CONSTRAINT `internships_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `internship_requests`
--
ALTER TABLE `internship_requests`
  ADD CONSTRAINT `internship_requests_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company_profiles` (`id`) ON DELETE CASCADE;

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

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
