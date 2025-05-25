-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 25, 2025 at 10:22 PM
-- Server version: 10.4.32-MariaDB-log
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pili_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_id` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `action` enum('INSERT','UPDATE','DELETE') NOT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `changed_by` int(11) NOT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`audit_id`, `table_name`, `record_id`, `action`, `old_data`, `new_data`, `changed_by`, `changed_at`) VALUES
(1, 'users', 9, 'INSERT', NULL, '{\"user_name\":\"Pythonsss\",\"user_email\":\"sadjasisss@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 9, '2025-05-17 15:05:27'),
(2, 'users', 9, 'DELETE', '{\"user_name\":\"Pythonsss\",\"user_email\":\"sadjasisss@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', NULL, 9, '2025-05-17 21:34:43'),
(3, 'users', 7, 'DELETE', '{\"user_name\":\"Pythonss\",\"user_email\":\"sadjasiss@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', NULL, 7, '2025-05-18 10:41:34'),
(4, 'users', 3, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 3, '2025-05-18 14:08:22'),
(5, 'users', 3, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 3, '2025-05-18 14:08:24'),
(6, 'users', 3, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 3, '2025-05-18 14:08:30'),
(7, 'users', 3, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 3, '2025-05-18 14:15:08'),
(8, 'users', 3, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 3, '2025-05-18 14:20:04'),
(9, 'users', 10, 'INSERT', NULL, '{\"user_name\":\"Pythonsss\",\"user_email\":\"sadjasiss@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 10, '2025-05-19 09:15:03'),
(10, 'users', 3, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 3, '2025-05-19 15:35:56'),
(11, 'users', 3, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 3, '2025-05-19 15:36:59'),
(12, 'users', 3, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 3, '2025-05-19 15:37:38'),
(13, 'users', 11, 'INSERT', NULL, '{\"user_name\":\"Pythonss\",\"user_email\":\"sadjasissss@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 11, '2025-05-19 15:58:19'),
(14, 'users', 3, 'DELETE', '{\"user_name\":\"Python\",\"user_email\":\"sadjasi@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', NULL, 3, '2025-05-19 16:08:57'),
(15, 'users', 12, 'INSERT', NULL, '{\"user_name\":\"xzit\",\"user_email\":\"james@gmail.com\",\"user_role\":\"Admin\",\"user_phone\":\"09917240521\"}', 12, '2025-05-22 13:18:52'),
(16, 'users', 13, 'INSERT', NULL, '{\"user_name\":\"xzitt\",\"user_email\":\"jamess@gmail.com\",\"user_role\":\"Admin\",\"user_phone\":\"09917240521\"}', 13, '2025-05-23 17:06:28'),
(17, 'users', 13, 'DELETE', '{\"user_name\":\"xzitt\",\"user_email\":\"jamess@gmail.com\",\"user_role\":\"Admin\",\"user_phone\":\"09917240521\"}', NULL, 13, '2025-05-23 17:06:59'),
(18, 'users', 14, 'INSERT', NULL, '{\"user_name\":\"xzitt\",\"user_email\":\"sadjasisss@gmail.com\",\"user_role\":\"Admin\",\"user_phone\":\"09917240521\"}', 14, '2025-05-23 17:27:16'),
(19, 'users', 11, 'DELETE', '{\"user_name\":\"Pythonss\",\"user_email\":\"sadjasissss@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', NULL, 11, '2025-05-24 01:21:38'),
(20, 'users', 15, 'INSERT', NULL, '{\"user_name\":\"xzitt\",\"user_email\":\"jame@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678383\"}', 15, '2025-05-24 01:32:03'),
(21, 'users', 15, 'UPDATE', '{\"user_name\":\"xzitt\",\"user_email\":\"jame@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678383\"}', '{\"user_name\":\"xzitt\",\"user_email\":\"jame@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678384\"}', 15, '2025-05-24 01:32:41'),
(22, 'users', 16, 'INSERT', NULL, '{\"user_name\":\"xzitt\",\"user_email\":\"jam@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240522\"}', 16, '2025-05-24 01:51:19'),
(23, 'users', 16, 'DELETE', '{\"user_name\":\"xzitt\",\"user_email\":\"jam@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240522\"}', NULL, 16, '2025-05-24 01:51:25'),
(24, 'users', 15, 'DELETE', '{\"user_name\":\"xzitt\",\"user_email\":\"jame@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678384\"}', NULL, 15, '2025-05-24 22:21:39'),
(25, 'users', 17, 'INSERT', NULL, '{\"user_name\":\"xa\",\"user_email\":\"jame@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678383\"}', 17, '2025-05-24 22:53:32'),
(26, 'users', 17, 'DELETE', '{\"user_name\":\"xa\",\"user_email\":\"jame@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678383\"}', NULL, 17, '2025-05-24 22:53:36'),
(27, 'users', 18, 'INSERT', NULL, '{\"user_name\":\"xa\",\"user_email\":\"jamme@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678383\"}', 18, '2025-05-25 10:16:46'),
(28, 'users', 18, 'UPDATE', '{\"user_name\":\"xa\",\"user_email\":\"jamme@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678383\"}', '{\"user_name\":\"xaaa\",\"user_email\":\"jamme@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678383\"}', 18, '2025-05-25 10:16:53'),
(29, 'users', 18, 'DELETE', '{\"user_name\":\"xaaa\",\"user_email\":\"jamme@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678383\"}', NULL, 18, '2025-05-25 10:17:41'),
(30, 'users', 14, 'UPDATE', '{\"user_name\":\"xzitt\",\"user_email\":\"sadjasisss@gmail.com\",\"user_role\":\"Admin\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"xzitta\",\"user_email\":\"sadjasisss@gmail.com\",\"user_role\":\"Admin\",\"user_phone\":\"09917240521\"}', 14, '2025-05-25 11:30:01'),
(31, 'users', 19, 'INSERT', NULL, '{\"user_name\":\"xas\",\"user_email\":\"jaaame@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240522\"}', 19, '2025-05-25 11:31:42'),
(32, 'users', 19, 'DELETE', '{\"user_name\":\"xas\",\"user_email\":\"jaaame@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240522\"}', NULL, 19, '2025-05-25 11:33:25'),
(33, 'users', 20, 'INSERT', NULL, '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:47:48'),
(34, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:52:39'),
(35, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:52:43'),
(36, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:52:47'),
(37, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:53:13'),
(38, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:53:47'),
(39, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:53:53'),
(40, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:54:08'),
(41, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:54:11'),
(42, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.coms\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:54:15'),
(43, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:54:27'),
(44, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:54:40'),
(45, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:54:49'),
(46, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:54:54'),
(47, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:54:55'),
(48, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:56:43'),
(49, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 20, '2025-05-25 11:56:59'),
(50, 'users', 20, 'UPDATE', '{\"user_name\":\"Python\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Pythons\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 0, '2025-05-25 12:09:03'),
(51, 'users', 20, 'UPDATE', '{\"user_name\":\"Pythons\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Pythonss\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 12, '2025-05-25 12:11:19'),
(52, 'users', 21, 'INSERT', NULL, '{\"user_name\":\"xaa\",\"user_email\":\"jaamme@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678383\"}', 12, '2025-05-25 13:16:13'),
(53, 'users', 21, 'DELETE', '{\"user_name\":\"xaa\",\"user_email\":\"jaamme@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09124678383\"}', NULL, 12, '2025-05-25 13:16:41'),
(54, 'users', 20, 'UPDATE', '{\"user_name\":\"Pythonss\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Pythonss\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 12, '2025-05-25 13:36:09'),
(55, 'users', 20, 'UPDATE', '{\"user_name\":\"Pythonss\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Pythonss\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 12, '2025-05-25 13:38:43'),
(56, 'bookings', 8, 'UPDATE', 'user_id: 2, room_id: 5, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 11, booking_status: pending', 'user_id: 2, room_id: 1, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 112, booking_status: pending', 2, '2025-05-25 14:17:01'),
(57, 'bookings', 8, 'UPDATE', 'user_id: 2, room_id: 1, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 112, booking_status: pending', 'user_id: 2, room_id: 2, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 113, booking_status: pending', 12, '2025-05-25 14:18:49'),
(58, 'bookings', 8, 'UPDATE', 'user_id: 2, room_id: 2, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 113, booking_status: pending', 'user_id: 2, room_id: 1, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 114, booking_status: pending', 12, '2025-05-25 14:56:49'),
(59, 'bookings', 8, 'UPDATE', 'user_id: 2, room_id: 1, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 114, booking_status: pending', 'user_id: 2, room_id: 20, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 116, booking_status: confirmed', 12, '2025-05-25 15:00:58'),
(60, 'bookings', 3, 'UPDATE', 'user_id: 1, room_id: 6, booking_check_in: 2025-05-25, booking_check_out: 2025-05-26, booking_total_price: 3000, booking_status: Confirmed', 'user_id: 1, room_id: 2, booking_check_in: 2025-05-25, booking_check_out: 2025-05-26, booking_total_price: 3000, booking_status: Checked-in', 12, '2025-05-25 15:50:16'),
(61, 'bookings', 3, 'UPDATE', 'user_id: 1, room_id: 2, booking_check_in: 2025-05-25, booking_check_out: 2025-05-26, booking_total_price: 3000, booking_status: Checked-in', 'user_id: 1, room_id: 1, booking_check_in: 2025-05-25, booking_check_out: 2025-05-26, booking_total_price: 3000, booking_status: Checked-out', 12, '2025-05-25 15:50:26'),
(62, 'bookings', 3, 'UPDATE', 'user_id: 1, room_id: 1, booking_check_in: 2025-05-25, booking_check_out: 2025-05-26, booking_total_price: 3000, booking_status: Checked-out', 'user_id: 1, room_id: 1, booking_check_in: 2025-05-25, booking_check_out: 2025-05-28, booking_total_price: 3000, booking_status: Confirmed', 12, '2025-05-25 15:57:36'),
(63, 'payments', 1, 'UPDATE', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=paid', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=failed', 12, '2025-05-25 16:16:26'),
(64, 'payments', 1, 'UPDATE', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=failed', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=failed', 12, '2025-05-25 16:16:29'),
(65, 'payments', 1, 'UPDATE', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=failed', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=pending', 12, '2025-05-25 16:16:31'),
(66, 'users', 20, 'UPDATE', '{\"user_name\":\"Pythonss\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Pythonssz\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 12, '2025-05-25 16:16:59'),
(67, 'payments', 3, 'INSERT', NULL, 'payment_id=3, booking_id=8, payment_amount=116, payment_date=2025-05-25, payment_status=paid', 12, '2025-05-25 16:17:51'),
(68, 'payments', 1, 'UPDATE', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=pending', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=paid', 12, '2025-05-25 16:17:58'),
(69, 'payments', 3, 'DELETE', 'payment_id=3, booking_id=8, payment_amount=116, payment_date=2025-05-25, payment_status=paid', NULL, 12, '2025-05-25 16:18:01'),
(70, 'payments', 1, 'UPDATE', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=paid', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=failed', 12, '2025-05-25 16:28:58'),
(71, 'payments', 1, 'UPDATE', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=failed', 'payment_id=1, booking_id=3, payment_amount=3000, payment_date=2025-05-16, payment_status=paid', 12, '2025-05-25 16:29:03'),
(72, 'payments', 4, 'INSERT', NULL, 'payment_id=4, booking_id=8, payment_amount=116, payment_date=2025-05-25, payment_status=failed', 12, '2025-05-25 16:29:46'),
(73, 'payments', 4, 'UPDATE', 'payment_id=4, booking_id=8, payment_amount=116, payment_date=2025-05-25, payment_status=failed', 'payment_id=4, booking_id=8, payment_amount=116, payment_date=2025-05-25, payment_status=pending', 12, '2025-05-25 16:39:13'),
(74, 'users', 20, 'UPDATE', '{\"user_name\":\"Pythonssz\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Pythonsszs\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 12, '2025-05-25 16:39:20'),
(75, 'bookings', 8, 'UPDATE', 'user_id: 2, room_id: 20, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 116, booking_status: Confirmed', 'user_id: 2, room_id: 20, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 117, booking_status: Confirmed', 12, '2025-05-25 16:39:38'),
(76, 'payments', 4, 'UPDATE', 'payment_id=4, booking_id=8, payment_amount=116, payment_date=2025-05-25, payment_status=pending', 'payment_id=4, booking_id=8, payment_amount=116, payment_date=2025-05-25, payment_status=paid', 12, '2025-05-25 16:48:03'),
(77, 'bookings', 8, 'UPDATE', 'user_id: 2, room_id: 20, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 117, booking_status: Confirmed', 'user_id: 2, room_id: 20, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 117, booking_status: Confirmed', 12, '2025-05-25 17:00:04'),
(78, 'bookings', 8, 'UPDATE', 'user_id: 2, room_id: 20, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 117, booking_status: Confirmed', 'user_id: 2, room_id: 20, booking_check_in: 2025-05-25, booking_check_out: 2025-05-31, booking_total_price: 110, booking_status: Confirmed', 12, '2025-05-25 17:00:12'),
(79, 'payments', 4, 'UPDATE', 'payment_id: 4, booking_id: 8, payment_amount: 116, payment_date: 2025-05-25, payment_status: paid', 'payment_id: 4, booking_id: 8, payment_amount: 116, payment_date: 2025-05-25, payment_status: failed', 12, '2025-05-25 17:08:16'),
(80, 'payments', 4, 'UPDATE', 'payment_id: 4, booking_id: 8, payment_amount: 116, payment_date: 2025-05-25, payment_status: failed', 'payment_id: 4, booking_id: 8, payment_amount: 116, payment_date: 2025-05-25, payment_status: pending', 12, '2025-05-25 17:11:36'),
(81, 'payments', 5, 'INSERT', NULL, 'payment_id: 5, booking_id: 6, payment_amount: 1, payment_date: 2025-05-29, payment_status: failed', 12, '2025-05-25 17:11:48'),
(82, 'payments', 5, 'DELETE', 'payment_id: 5, booking_id: 6, payment_amount: 1, payment_date: 2025-05-29, payment_status: failed', NULL, 12, '2025-05-25 17:12:05'),
(83, 'users', 20, 'UPDATE', '{\"user_name\":\"Pythonsszs\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Pythonsszss\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', 12, '2025-05-25 17:50:09'),
(84, 'users', 20, 'UPDATE', '{\"user_name\":\"Pythonsszss\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240520\"}', '{\"user_name\":\"Pythonsszss\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 12, '2025-05-25 20:59:24'),
(85, 'users', 20, 'UPDATE', '{\"user_name\":\"Pythonsszss\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"Pythonsszs\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 12, '2025-05-25 22:09:03'),
(86, 'users', 20, 'UPDATE', '{\"user_name\":\"Pythonsszs\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"aPythonsszs\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 12, '2025-05-25 22:15:20'),
(87, 'users', 20, 'UPDATE', '{\"user_name\":\"aPythonsszs\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', '{\"user_name\":\"Pythonsszs\",\"user_email\":\"a@gmail.com\",\"user_role\":\"Guest\",\"user_phone\":\"09917240521\"}', 12, '2025-05-25 22:20:49');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `room_id` int(10) NOT NULL,
  `booking_check_in` date NOT NULL,
  `booking_check_out` date NOT NULL,
  `booking_total_price` int(50) NOT NULL,
  `booking_status` enum('Checked-in','Checked-out','Pending','Confirmed','Canceled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `booking_check_in`, `booking_check_out`, `booking_total_price`, `booking_status`) VALUES
(3, 1, 1, '2025-05-25', '2025-05-28', 3000, 'Confirmed'),
(6, 6, 9, '2025-05-24', '2025-05-26', 3998, 'Pending');

--
-- Triggers `bookings`
--
DELIMITER $$
CREATE TRIGGER `trg_bookings_delete` AFTER DELETE ON `bookings` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, changed_by)
  VALUES (
    'bookings',
    OLD.booking_id,
    'DELETE',
    CONCAT(
      'user_id: ', OLD.user_id, ', ',
      'room_id: ', OLD.room_id, ', ',
      'booking_check_in: ', OLD.booking_check_in, ', ',
      'booking_check_out: ', OLD.booking_check_out, ', ',
      'booking_total_price: ', OLD.booking_total_price, ', ',
      'booking_status: ', OLD.booking_status
    ),
    NULL,
    @current_user_id  
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_bookings_insert` AFTER INSERT ON `bookings` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, changed_by)
  VALUES (
    'bookings',
    NEW.booking_id,
    'INSERT',
    NULL,
    CONCAT(
      'user_id: ', NEW.user_id, ', ',
      'room_id: ', NEW.room_id, ', ',
      'check_in: ', NEW.booking_check_in, ', ',
      'check_out: ', NEW.booking_check_out, ', ',
      'total_price: ', NEW.booking_total_price, ', ',
      'booking_status: ', NEW.booking_status
    ),
    @current_user_id  
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_bookings_update` AFTER UPDATE ON `bookings` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, changed_by)
  VALUES (
    'bookings',
    NEW.booking_id,
    'UPDATE',
    CONCAT(
      'user_id: ', OLD.user_id, ', ',
      'room_id: ', OLD.room_id, ', ',
      'booking_check_in: ', OLD.booking_check_in, ', ',
      'booking_check_out: ', OLD.booking_check_out, ', ',
      'booking_total_price: ', OLD.booking_total_price, ', ',
      'booking_status: ', OLD.booking_status
    ),
    CONCAT(
      'user_id: ', NEW.user_id, ', ',
      'room_id: ', NEW.room_id, ', ',
      'booking_check_in: ', NEW.booking_check_in, ', ',
      'booking_check_out: ', NEW.booking_check_out, ', ',
      'booking_total_price: ', NEW.booking_total_price, ', ',
      'booking_status: ', NEW.booking_status
    ),
    @current_user_id  
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `room_id` int(10) NOT NULL,
  `feedback_rating` int(10) NOT NULL,
  `feedback_comments` varchar(255) NOT NULL,
  `feedback_date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `room_id`, `feedback_rating`, `feedback_comments`, `feedback_date_created`) VALUES
(1, 1, 1, 5, 'i love it very much', '2025-05-16'),
(2, 2, 2, 4, 'will come back again', '2025-05-17'),
(3, 2, 2, 5, '', '2025-05-17'),
(4, 2, 2, 5, '', '2025-05-17'),
(5, 2, 2, 5, '', '2025-05-17'),
(6, 2, 2, 5, '', '2025-05-17'),
(7, 10, 2, 5, '', '2025-05-19'),
(13, 10, 2, 5, '', '2025-05-19'),
(14, 10, 2, 5, 'ggg', '2025-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(10) NOT NULL,
  `invoice_total_amount` int(50) NOT NULL,
  `invoice_status` enum('paid','unpaid','canceled') NOT NULL,
  `invoice_date_created` date NOT NULL,
  `payment_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `invoice_total_amount`, `invoice_status`, `invoice_date_created`, `payment_id`) VALUES
(1, 3000, 'paid', '2025-05-16', 1);

--
-- Triggers `invoice`
--
DELIMITER $$
CREATE TRIGGER `trg_invoice_delete` AFTER DELETE ON `invoice` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (
    table_name, 
    record_id, 
    action, 
    old_data, 
    new_data, 
    changed_by
  )
  VALUES (
    'invoice',
    OLD.invoice_id,
    'DELETE',
    CONCAT(
      'invoice_id: ', OLD.invoice_id, ', ',
      'invoice_total_amount: ', OLD.invoice_total_amount, ', ',
      'invoice_status: ', OLD.invoice_status, ', ',
      'invoice_date_created: ', OLD.invoice_date_created, ', ',
      'payment_id: ', OLD.payment_id
    ),
    NULL,
    @current_user_id
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_invoice_insert` AFTER INSERT ON `invoice` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (
    table_name, 
    record_id, 
    action, 
    old_data, 
    new_data, 
    changed_by
  )
  VALUES (
    'invoice',
    NEW.invoice_id,
    'INSERT',
    NULL,
    CONCAT(
      'invoice_id: ', NEW.invoice_id, ', ',
      'invoice_total_amount: ', NEW.invoice_total_amount, ', ',
      'invoice_status: ', NEW.invoice_status, ', ',
      'invoice_date_created: ', NEW.invoice_date_created, ', ',
      'payment_id: ', NEW.payment_id
    ),
    @current_user_id
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_invoice_update` AFTER UPDATE ON `invoice` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (
    table_name, 
    record_id, 
    action, 
    old_data, 
    new_data, 
    changed_by
  )
  VALUES (
    'invoice',
    NEW.invoice_id,
    'UPDATE',
    CONCAT(
      'invoice_id: ', OLD.invoice_id, ', ',
      'invoice_total_amount: ', OLD.invoice_total_amount, ', ',
      'invoice_status: ', OLD.invoice_status, ', ',
      'invoice_date_created: ', OLD.invoice_date_created, ', ',
      'payment_id: ', OLD.payment_id
    ),
    CONCAT(
      'invoice_id: ', NEW.invoice_id, ', ',
      'invoice_total_amount: ', NEW.invoice_total_amount, ', ',
      'invoice_status: ', NEW.invoice_status, ', ',
      'invoice_date_created: ', NEW.invoice_date_created, ', ',
      'payment_id: ', NEW.payment_id
    ),
    @current_user_id
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(10) NOT NULL,
  `booking_id` int(10) NOT NULL,
  `payment_amount` int(50) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_status` enum('paid','pending','failed') NOT NULL,
  `payment_proof` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `payment_amount`, `payment_date`, `payment_status`, `payment_proof`) VALUES
(1, 3, 3000, '2025-05-16', 'paid', '');

--
-- Triggers `payments`
--
DELIMITER $$
CREATE TRIGGER `trg_payments_delete` AFTER DELETE ON `payments` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, changed_by)
  VALUES (
    'payments',
    OLD.payment_id,
    'DELETE',
    CONCAT(
      'payment_id: ', OLD.payment_id, ', ',
      'booking_id: ', OLD.booking_id, ', ',
      'payment_amount: ', OLD.payment_amount, ', ',
      'payment_date: ', OLD.payment_date, ', ',
      'payment_status: ', OLD.payment_status
    ),
    NULL,
    @current_user_id
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_payments_insert` AFTER INSERT ON `payments` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, changed_by)
  VALUES (
    'payments',
    NEW.payment_id,
    'INSERT',
    NULL,
    CONCAT(
      'payment_id: ', NEW.payment_id, ', ',
      'booking_id: ', NEW.booking_id, ', ',
      'payment_amount: ', NEW.payment_amount, ', ',
      'payment_date: ', NEW.payment_date, ', ',
      'payment_status: ', NEW.payment_status
    ),
    @current_user_id
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_payments_update` AFTER UPDATE ON `payments` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (table_name, record_id, action, old_data, new_data, changed_by)
  VALUES (
    'payments',
    NEW.payment_id,
    'UPDATE',
    CONCAT(
      'payment_id: ', OLD.payment_id, ', ',
      'booking_id: ', OLD.booking_id, ', ',
      'payment_amount: ', OLD.payment_amount, ', ',
      'payment_date: ', OLD.payment_date, ', ',
      'payment_status: ', OLD.payment_status
    ),
    CONCAT(
      'payment_id: ', NEW.payment_id, ', ',
      'booking_id: ', NEW.booking_id, ', ',
      'payment_amount: ', NEW.payment_amount, ', ',
      'payment_date: ', NEW.payment_date, ', ',
      'payment_status: ', NEW.payment_status
    ),
    @current_user_id
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(10) NOT NULL,
  `room_number` int(50) NOT NULL,
  `room_type` enum('Deluxe Room','Standard Room','Single Room','Double Room') NOT NULL,
  `room_price` int(50) NOT NULL,
  `room_capacity` int(10) NOT NULL,
  `room_status` enum('available','booked','maintenance') NOT NULL,
  `room_image` varchar(255) DEFAULT NULL,
  `room_description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_type`, `room_price`, `room_capacity`, `room_status`, `room_image`, `room_description`) VALUES
(1, 1001, 'Deluxe Room', 3000, 7, 'available', 'r1.jpg', ''),
(2, 1002, 'Double Room', 1999, 6, 'available', 'r2.jpg', ''),
(3, 1003, 'Deluxe Room', 3000, 7, 'maintenance', 'r3.jpg', ''),
(4, 1004, 'Single Room', 1399, 4, 'maintenance', 'r4.jpg', ''),
(5, 1005, 'Double Room', 1999, 5, 'available', 'r5.jpg', ''),
(6, 1006, 'Standard Room', 2499, 6, 'available', 'r6.jpg', ''),
(7, 1007, 'Standard Room', 2499, 6, 'booked', 'r7.jpg', ''),
(8, 1008, 'Deluxe Room', 3000, 7, 'booked', 'r8.jpg', ''),
(9, 1009, 'Double Room', 1999, 5, 'available', 'r9.jpg', ''),
(10, 1002, 'Double Room', 1999, 6, 'available', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_role` enum('Guest','Admin') NOT NULL DEFAULT 'Guest',
  `user_phone` varchar(255) NOT NULL,
  `user_date_created` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_role`, `user_phone`, `user_date_created`) VALUES
(1, 'james andrew', 'jamesandrew.ocuaman@wvsu.edu.ph', 'aquaman', 'Admin', '09124678382', '2025-05-16'),
(2, 'luis ronatay', 'luisgabriel.ronatay@wvsu.edu.ph', 'luispogi', 'Guest', '09460824512', '2025-05-16'),
(6, 'Pythons', 'sadjasis@gmail.com', '$2y$10$rHFMUa35mJrq5vyFbryAFOau7KGX22bEIjU596OE4ucwiy0IeXvlK', 'Guest', '09917240520', '2025-05-17'),
(10, 'Pythonsss', 'sadjasiss@gmail.com', '$2y$10$3ywZ6HRSRNeKGpIMmpskI.1ApjhfZIjtHx/cg8.vShiOJ7b.jOEJG', 'Guest', '09917240520', '2025-05-19'),
(12, 'xzit', 'james@gmail.com', '$2y$10$B695KEqECFlqPh.xJVcQe.sIHjxgBuG33mS6zDGKI3UEoOWq11uEC', 'Admin', '09917240521', '2025-05-22'),
(14, 'xzitta', 'sadjasisss@gmail.com', '$2y$10$.BG9NM84O.3jeRjkW1KHLuXR/fFHlJURR9lAVGdro5vMFzR73xS0S', 'Admin', '09917240521', '2025-05-23'),
(20, 'Pythonsszs', 'a@gmail.com', '$2y$10$9HHRSMk8zhfW8GamLdgHde/7.e6t/JQqBvXFctxoXwHsUNKOL5goC', 'Guest', '09917240521', '2025-05-25');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `users_after_delete` AFTER DELETE ON `users` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (
    table_name, record_id, action, old_data, changed_by
  )
  VALUES (
    'users',
    OLD.user_id,
    'DELETE',
    CONCAT(
      '{"user_name":"', OLD.user_name,
      '","user_email":"', OLD.user_email,
      '","user_role":"', OLD.user_role,
      '","user_phone":"', OLD.user_phone, '"}'
    ),
    @current_user_id    
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_after_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (
    table_name, record_id, action, new_data, changed_by
  ) VALUES (
    'users',
    NEW.user_id,
    'INSERT',
    CONCAT(
      '{"user_name":"', NEW.user_name,
      '","user_email":"', NEW.user_email,
      '","user_role":"', NEW.user_role,
      '","user_phone":"', NEW.user_phone, '"}'
    ),
    @current_user_id   
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `users_after_update` AFTER UPDATE ON `users` FOR EACH ROW BEGIN
  INSERT INTO audit_logs (
    table_name, record_id, action, old_data, new_data, changed_by
  )
  VALUES (
    'users',
    OLD.user_id,
    'UPDATE',
    CONCAT(
      '{"user_name":"', OLD.user_name,
      '","user_email":"', OLD.user_email,
      '","user_role":"', OLD.user_role,
      '","user_phone":"', OLD.user_phone, '"}'
    ),
    CONCAT(
      '{"user_name":"', NEW.user_name,
      '","user_email":"', NEW.user_email,
      '","user_role":"', NEW.user_role,
      '","user_phone":"', NEW.user_phone, '"}'
    ),
    @current_user_id   
  );
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `fk_invoice_payments` (`payment_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_bookiing_id` (`booking_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `room_id` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`),
  ADD CONSTRAINT `fk_feedback_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `fk_invoice_payments` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `bookiing_id` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
