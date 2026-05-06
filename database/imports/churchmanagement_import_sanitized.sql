-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 03:09 AM
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
-- Database: `churchmanagement`
--

DELIMITER $$
--
-- Procedures
--
CREATE PROCEDURE `AddAttendance` (IN `p_member_id` BIGINT, IN `p_event_id` BIGINT, IN `p_administrator_id` BIGINT, IN `p_status` VARCHAR(50))   BEGIN
    INSERT INTO attendances (
        member_id,
        event_id,
        administrator_id,
        attended_at,
        status,
        created_at,
        updated_at
    )
    VALUES (
        p_member_id,
        p_event_id,
        p_administrator_id,
        NOW(),
        p_status,
        NOW(),
        NOW()
    );
END$$

CREATE PROCEDURE `GetAttendanceByEvent` (IN `eventId` INT)   BEGIN
    SELECT 
        e.event_name,
        s.attendance_name,
        s.attendance_date,
        m.member_lname AS member_name,
        a.status,
        a.time_in
    FROM attendances a
    JOIN members m ON a.member_id = m.member_id
    JOIN events e ON a.event_id = e.event_id
    JOIN attendance_sessions s 
        ON a.attendance_session_id = s.attendance_session_id
    WHERE e.event_id = eventId
    ORDER BY s.attendance_date, m.member_lname;
END$$

--
-- Functions
--
CREATE FUNCTION `GetEventAttendanceStatus` (`eventId` INT) RETURNS VARCHAR(20) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE total INT;

    SELECT COUNT(*) INTO total
    FROM attendances
    WHERE event_id = eventId;

    IF total >= 50 THEN
        RETURN 'High Attendance';
    ELSEIF total >= 20 THEN
        RETURN 'Moderate Attendance';
    ELSE
        RETURN 'Low Attendance';
    END IF;
END$$

CREATE FUNCTION `GetMemberFullName` (`p_member_id` BIGINT) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
    DECLARE full_name VARCHAR(255);

    SELECT CONCAT(member_fname, ' ', COALESCE(member_mname, ''), ' ', member_lname)
    INTO full_name
    FROM members
    WHERE member_id = p_member_id;

    RETURN TRIM(full_name);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `administrators`
--

CREATE TABLE `administrators` (
  `administrator_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin') NOT NULL DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `administrators`
--

INSERT INTO `administrators` (`administrator_id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', '$2y$12$VqSaljn6l88IgQI4wpI1YuBlLu0uweUaJUxk2sPbRblfy4PRxZWOi', 'super_admin', '2026-04-29 16:08:18', '2026-04-29 16:15:33'),
(2, 'admin', '$2y$12$M3zwhu/SsTZLpE9ZvAR39uwNEtJ.IVfztZhcKthqRZP8c2piL4sra', 'admin', '2026-04-29 16:08:18', '2026-04-29 16:15:34');

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `attendance_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `administrator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attendance_session_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attended_at` timestamp NULL DEFAULT NULL,
  `time_in` timestamp NULL DEFAULT NULL,
  `time_out` timestamp NULL DEFAULT NULL,
  `status` enum('Pending','Present','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`attendance_id`, `member_id`, `event_id`, `administrator_id`, `attendance_session_id`, `attended_at`, `time_in`, `time_out`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 7, '2026-04-29 16:08:19', NULL, NULL, 'Present', '2026-04-29 16:08:19', '2026-04-29 16:08:19'),
(2, 2, 2, 1, 8, '2026-04-29 16:08:19', NULL, NULL, 'Present', '2026-04-29 16:08:19', '2026-04-29 16:08:19');

-- --------------------------------------------------------

--
-- Table structure for table `attendance_sessions`
--

CREATE TABLE `attendance_sessions` (
  `attendance_session_id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `administrator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attendance_name` varchar(255) NOT NULL,
  `attendance_date` date NOT NULL,
  `time_in_start` time DEFAULT NULL,
  `time_out_end` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_sessions`
--

INSERT INTO `attendance_sessions` (`attendance_session_id`, `event_id`, `administrator_id`, `attendance_name`, `attendance_date`, `time_in_start`, `time_out_end`, `created_at`, `updated_at`) VALUES
(7, 1, 1, 'Morning Attendance', '2026-05-05', '08:00:00', '10:00:00', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(8, 2, 1, 'Evening Attendance', '2026-05-07', '18:00:00', '19:30:00', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(10, 6, 1, 'Whole Day Attendance', '2026-04-30', '05:30:00', '21:30:00', '2026-04-29 16:24:00', '2026-04-29 16:24:00'),
(11, 7, 1, 'Morning Attendance', '2026-04-30', '06:30:00', '11:30:00', '2026-04-29 16:25:36', '2026-04-29 16:54:02'),
(13, 7, 1, 'Whole Day Attendance', '2026-04-30', '00:40:00', '15:40:00', '2026-04-29 16:40:58', '2026-04-29 16:40:58');

-- --------------------------------------------------------

--
-- Stand-in structure for view `attendance_session_summary`
-- (See below for the actual view)
--
CREATE TABLE `attendance_session_summary` (
`attendance_session_id` bigint(20) unsigned
,`attendance_name` varchar(255)
,`event_name` varchar(255)
,`attendance_date` date
,`time_in_start` time
,`time_out_end` time
,`total_attendance` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('upcoming','ongoing','finished') NOT NULL DEFAULT 'upcoming',
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `administrator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `start_date`, `end_date`, `start_time`, `end_time`, `description`, `status`, `type_id`, `administrator_id`, `created_at`, `updated_at`) VALUES
(1, 'Sunday Worship Service', '2026-05-05', '2026-05-05', '00:00:00', '10:00:00', 'Weekly worship service', 'upcoming', 1, 1, '2026-04-29 16:08:18', '2026-04-29 16:40:06'),
(2, 'Prayer Meeting', '2026-05-07', '2026-05-07', '18:00:00', '19:30:00', 'Evening prayer meeting', 'upcoming', 2, 1, '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(4, 'Bible Study Session', '2026-04-05', '2026-05-12', '00:00:00', '18:30:00', 'Bible sharing and discussion', 'ongoing', 5, 1, '2026-04-29 16:08:18', '2026-04-29 16:40:13'),
(5, 'Community Outreach', '2026-05-15', '2026-05-15', '00:00:00', '12:00:00', 'Barangay outreach program', 'upcoming', 6, 2, '2026-04-29 16:08:18', '2026-04-29 16:39:56'),
(6, 'Thanks Giving by Mina', '2026-04-30', '2026-06-01', '06:30:00', '12:00:00', NULL, 'upcoming', 4, 2, '2026-04-29 16:16:50', '2026-04-29 16:54:35'),
(7, 'Youth Ni Galon', '2026-04-30', '2026-05-14', '00:18:00', '12:18:00', NULL, 'ongoing', 3, 2, '2026-04-29 16:18:12', '2026-04-29 16:18:12');

-- --------------------------------------------------------

--
-- Stand-in structure for view `event_attendance_summary`
-- (See below for the actual view)
--
CREATE TABLE `event_attendance_summary` (
`event_id` bigint(20) unsigned
,`event_name` varchar(255)
,`type_name` varchar(255)
,`status` enum('upcoming','ongoing','finished')
,`total_attendance` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `member_fname` varchar(255) NOT NULL,
  `member_mname` varchar(255) DEFAULT NULL,
  `member_lname` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `member_fname`, `member_mname`, `member_lname`, `gender`, `birth_date`, `email`, `phone_number`, `province`, `city`, `street`, `is_archived`, `archived_at`, `created_at`, `updated_at`) VALUES
(1, 'Andy Andrade II', 'A', 'Mina', 'Male', '2005-04-12', 'mina@gmail.com', '0123456789', 'Davao Del Sur', 'Davao', 'Matina', 0, NULL, '2026-04-29 16:08:18', '2026-04-29 16:43:44'),
(2, 'Claudee', 'A', 'Galon', 'Male', '2005-04-01', 'claudee@gmail.com', '0912387654', 'Davao Del Sur', 'Davao', 'Mandug', 0, NULL, '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(3, 'Geoff Patrick', 'G', 'Granada', 'Male', '2005-05-04', 'Granada@gmail.com', '987654321', 'Davao del sur', 'Matina', 'Grenade', 0, NULL, '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(4, 'Joen', 'R', 'Reloba', 'Male', '2005-09-27', 'Reloba@gmail.com', '214365879', 'Davao del sur', 'Toril', 'Jomaine', 0, NULL, '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(5, 'Jhonn Crisler', 'L', 'Lazar', 'Male', '2000-03-14', 'Lazar@gmail.com', '543216789', 'Davao del sur', 'Gravahan', 'Traydor', 0, NULL, '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(6, 'Geonathan Niel', 'P', 'Penafiel', 'Male', '2006-06-01', 'Geonathan@gmail.com', '786591234', 'Davao del sur', 'Wala ko Balo', 'Armlet God', 0, NULL, '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(7, 'Francis Laurence', 'C', 'Calla', 'Male', '2004-02-21', 'Calla@gmail.com', '918273645', 'Davao del sur', 'Pastil', 'Barato', 0, NULL, '2026-04-29 16:08:18', '2026-04-29 16:08:18');

-- --------------------------------------------------------

--
-- Table structure for table `members_ministries`
--

CREATE TABLE `members_ministries` (
  `members_ministry_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `ministry_id` bigint(20) UNSIGNED NOT NULL,
  `role_in_ministry` varchar(255) DEFAULT NULL,
  `date_joined` date DEFAULT NULL,
  `status` enum('active','inactive','left') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_08_000000_members', 1),
(5, '2026_04_08_000005_admin', 1),
(6, '2026_04_08_000006_type', 1),
(7, '2026_04_08_000007_ministry', 1),
(8, '2026_04_08_000008_membersministry', 1),
(9, '2026_04_08_000009_events', 1),
(10, '2026_04_08_104020_attendance_sessions', 1),
(11, '2026_04_08_133025_attendance', 1),
(12, '2026_04_16_071310_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ministries`
--

CREATE TABLE `ministries` (
  `ministry_id` bigint(20) UNSIGNED NOT NULL,
  `ministry_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ministries`
--

INSERT INTO `ministries` (`ministry_id`, `ministry_name`, `created_at`, `updated_at`) VALUES
(1, 'Youth', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(2, 'Choir', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(3, 'Usher', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(4, 'Prayer Team', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(5, 'Media', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(6, 'Children', '2026-04-29 16:08:18', '2026-04-29 16:08:18');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('vXY2Ml6yqwoWKyk5nmj7bUcjuSM8j2ZJgjXYZjpZ', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJuTFJBSGI5OERGc0t0TmNUbGIwV2g2WTVUMEtKa1IxdTdKMDNxOVloIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL21lbWJlcnMiLCJyb3V0ZSI6Im1lbWJlcnMuaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9', 1777481690);

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE `types` (
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`type_id`, `type_name`, `created_at`, `updated_at`) VALUES
(1, 'Worship Service', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(2, 'Prayer Meeting', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(3, 'Youth Fellowship', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(4, 'Thanks Giving', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(5, 'Bible Study', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(6, 'Community Outreach', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(7, 'Leadership Meeting', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(8, 'Church Anniversary', '2026-04-29 16:08:18', '2026-04-29 16:08:18'),
(9, 'Youth Fellowship', '2026-04-29 16:08:18', '2026-04-29 16:08:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `attendance_session_summary`
--
DROP TABLE IF EXISTS `attendance_session_summary`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `attendance_session_summary`  AS SELECT `s`.`attendance_session_id` AS `attendance_session_id`, `s`.`attendance_name` AS `attendance_name`, `e`.`event_name` AS `event_name`, `s`.`attendance_date` AS `attendance_date`, `s`.`time_in_start` AS `time_in_start`, `s`.`time_out_end` AS `time_out_end`, count(`a`.`attendance_id`) AS `total_attendance` FROM ((`attendance_sessions` `s` join `events` `e` on(`s`.`event_id` = `e`.`event_id`)) left join `attendances` `a` on(`s`.`attendance_session_id` = `a`.`attendance_session_id`)) GROUP BY `s`.`attendance_session_id`, `s`.`attendance_name`, `e`.`event_name`, `s`.`attendance_date`, `s`.`time_in_start`, `s`.`time_out_end` ;

-- --------------------------------------------------------

--
-- Structure for view `event_attendance_summary`
--
DROP TABLE IF EXISTS `event_attendance_summary`;

CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `event_attendance_summary`  AS SELECT `e`.`event_id` AS `event_id`, `e`.`event_name` AS `event_name`, `t`.`type_name` AS `type_name`, `e`.`status` AS `status`, count(`a`.`attendance_id`) AS `total_attendance` FROM ((`events` `e` join `types` `t` on(`e`.`type_id` = `t`.`type_id`)) left join `attendances` `a` on(`e`.`event_id` = `a`.`event_id`)) GROUP BY `e`.`event_id`, `e`.`event_name`, `t`.`type_name`, `e`.`status` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`administrator_id`),
  ADD UNIQUE KEY `administrators_username_unique` (`username`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `attendances_member_id_attendance_session_id_unique` (`member_id`,`attendance_session_id`),
  ADD KEY `attendances_attendance_session_id_foreign` (`attendance_session_id`),
  ADD KEY `attendances_event_id_foreign` (`event_id`),
  ADD KEY `attendances_administrator_id_foreign` (`administrator_id`);

--
-- Indexes for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD PRIMARY KEY (`attendance_session_id`),
  ADD KEY `attendance_sessions_event_id_foreign` (`event_id`),
  ADD KEY `attendance_sessions_administrator_id_foreign` (`administrator_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `events_type_id_foreign` (`type_id`),
  ADD KEY `events_administrator_id_foreign` (`administrator_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `members_email_unique` (`email`),
  ADD UNIQUE KEY `members_phone_number_unique` (`phone_number`);

--
-- Indexes for table `members_ministries`
--
ALTER TABLE `members_ministries`
  ADD PRIMARY KEY (`members_ministry_id`),
  ADD UNIQUE KEY `members_ministries_member_id_ministry_id_unique` (`member_id`,`ministry_id`),
  ADD KEY `members_ministries_ministry_id_foreign` (`ministry_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ministries`
--
ALTER TABLE `ministries`
  ADD PRIMARY KEY (`ministry_id`),
  ADD UNIQUE KEY `ministries_ministry_name_unique` (`ministry_name`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `types_type_name_unique` (`type_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrators`
--
ALTER TABLE `administrators`
  MODIFY `administrator_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `attendance_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  MODIFY `attendance_session_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `members_ministries`
--
ALTER TABLE `members_ministries`
  MODIFY `members_ministry_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ministries`
--
ALTER TABLE `ministries`
  MODIFY `ministry_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `type_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_administrator_id_foreign` FOREIGN KEY (`administrator_id`) REFERENCES `administrators` (`administrator_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendances_attendance_session_id_foreign` FOREIGN KEY (`attendance_session_id`) REFERENCES `attendance_sessions` (`attendance_session_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `attendance_sessions`
--
ALTER TABLE `attendance_sessions`
  ADD CONSTRAINT `attendance_sessions_administrator_id_foreign` FOREIGN KEY (`administrator_id`) REFERENCES `administrators` (`administrator_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_sessions_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_administrator_id_foreign` FOREIGN KEY (`administrator_id`) REFERENCES `administrators` (`administrator_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `events_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `types` (`type_id`) ON DELETE CASCADE;

--
-- Constraints for table `members_ministries`
--
ALTER TABLE `members_ministries`
  ADD CONSTRAINT `members_ministries_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `members_ministries_ministry_id_foreign` FOREIGN KEY (`ministry_id`) REFERENCES `ministries` (`ministry_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

