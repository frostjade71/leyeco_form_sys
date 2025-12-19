-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Dec 19, 2025 at 01:08 PM
-- Server version: 8.0.44
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `leyeco_forms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `id` int NOT NULL,
  `requisition_id` int NOT NULL,
  `approval_level` int NOT NULL,
  `approver_role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approver_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `approvals`
--

INSERT INTO `approvals` (`id`, `requisition_id`, `approval_level`, `approver_role`, `approver_name`, `status`, `remarks`, `approved_at`, `created_at`) VALUES
(1, 1, 1, 'Recommending Approval - Section Head/Div. Head/Department Head', 'ENGR. Richard A. Forsuelo', 'approved', 'okay', '2025-12-17 14:28:18', '2025-12-17 13:13:57'),
(2, 1, 2, 'Inventory Checked - Warehouse Section Head', 'Glen C. Arpon', 'approved', 'we have on stock po', '2025-12-17 14:33:17', '2025-12-17 13:13:57'),
(3, 1, 3, 'Budget Approval - Div. Supervisor/Budget Officer', 'Abraham M. Abarca Jr.', 'approved', '', '2025-12-17 14:40:32', '2025-12-17 13:13:57'),
(4, 1, 4, 'Checked By - Internal Auditor', 'Renee A. Ong CPA.', 'approved', 'Done', '2025-12-17 14:46:23', '2025-12-17 13:13:57'),
(5, 1, 5, 'Approved By - General Manager', 'Allan L. Laniba MPA, MM', 'approved', '', '2025-12-17 14:47:32', '2025-12-17 13:13:57'),
(6, 2, 1, 'Recommending Approval - Section Head/Div. Head/Department Head', 'Jeric', 'approved', 'you may proceed', '2025-12-17 23:37:53', '2025-12-17 23:37:03'),
(7, 2, 2, 'Inventory Checked - Warehouse Section Head', 'Glen C. Arpon', 'approved', 'yes stock', '2025-12-17 23:39:44', '2025-12-17 23:37:03'),
(8, 2, 3, 'Budget Approval - Div. Supervisor/Budget Officer', 'Abraham M. Abarca Jr.', 'approved', 'k.', '2025-12-17 23:41:11', '2025-12-17 23:37:03'),
(9, 2, 4, 'Checked By - Internal Auditor', 'Renee A. Ong CPA.', 'approved', 'goodluck nlang kay manager', '2025-12-17 23:42:25', '2025-12-17 23:37:03'),
(10, 2, 5, 'Approved By - General Manager', 'Allan L. Laniba MPA, MM', 'approved', 'rent nla kamon speaker diay', '2025-12-17 23:44:09', '2025-12-17 23:37:03'),
(26, 6, 1, 'Noted by - Department Head', NULL, 'pending', NULL, NULL, '2025-12-19 03:47:32'),
(27, 6, 2, 'Checked by - Warehouse Section Head', NULL, 'pending', NULL, NULL, '2025-12-19 03:47:32'),
(28, 6, 3, 'Reviewed by - Budget Officer', NULL, 'pending', NULL, NULL, '2025-12-19 03:47:32'),
(29, 6, 4, 'Checked By - Internal Auditor', NULL, 'pending', NULL, NULL, '2025-12-19 03:47:32'),
(30, 6, 5, 'Approved By - General Manager', NULL, 'pending', NULL, NULL, '2025-12-19 03:47:32');

-- --------------------------------------------------------

--
-- Table structure for table `approvers`
--

CREATE TABLE `approvers` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval_level` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `approvers`
--

INSERT INTO `approvers` (`id`, `name`, `role`, `approval_level`, `email`, `password`, `is_admin`, `created_at`, `updated_at`) VALUES
(2, 'ENGR. Richard A. Forsuelo', 'Recommending Approval - Section Head/Div. Head/Department Head', 1, 'richard@leyeco3.com', '$2y$10$26Qk4/aCkF.wG623Ag7LmeNOan7RLa.tmUgN5Rqq4P8AGNgp6crVm', 0, '2025-12-17 13:23:39', '2025-12-17 13:23:39'),
(3, 'Glen C. Arpon', 'Inventory Checked - Warehouse Section Head', 2, 'glen@leyeco3.com', '$2y$10$B0J1A8.HFErSU6jhBQGhtuc6AF8O5fbBkYkLdJYwf/KekTIrzqiW.', 0, '2025-12-17 13:25:13', '2025-12-17 13:25:13'),
(4, 'Abraham M. Abarca Jr.', 'Budget Approval - Div. Supervisor/Budget Officer', 3, 'abarca@leyeco3.com', '$2y$10$ABA/cEb8lsQxoAjc6lWKT.TvBULmcHy4xV461fPglEfRrZYMzfqPS', 0, '2025-12-17 13:26:37', '2025-12-17 13:26:37'),
(5, 'Renee A. Ong CPA.', 'Checked By - Internal Auditor', 4, 'renee@leyeco3.com', '$2y$10$6WUYrNAV5Lm/llt.Zg5nDeIJQlsZNt9JqLmwyF0Bcsw1uN5tMwALa', 0, '2025-12-17 13:27:54', '2025-12-17 13:27:54'),
(6, 'Allan L. Laniba MPA, MM', 'Approved By - General Manager', 5, 'allan@leyeco3.com', '$2y$10$rsdJrSrEW9H1UckuR.RQtedXrlF8KQD0T9Fuv429PnzlbqjvXMYwW', 0, '2025-12-17 13:28:45', '2025-12-17 13:28:45'),
(7, 'Jeric', 'Recommending Approval - Section Head/Div. Head/Department Head', 1, '0', '$2y$10$NDG36PODicpNieTG/YE0COo9m/AFqZRCCCLiNyjlPtUfk53IXWxKq', 0, '2025-12-17 23:35:17', '2025-12-17 23:35:33'),
(8, 'Jeric', 'Recommending Approval - Section Head/Div. Head/Department Head', 1, 'jeric@gmail.com', '$2y$10$KkBkEGRTDV2T2imoRznDI.H4jAv5uDfVJZqXZQZhp4IctvaQiLcn.', 0, '2025-12-17 23:35:40', '2025-12-17 23:35:40');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int NOT NULL,
  `reference_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reporter_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `municipality` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barangay` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `lon` decimal(11,8) DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'NEW',
  `assigned_to` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dispatch_to` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dispatcher name (no account needed)',
  `dispatch_mode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mode: Handcarried, Radio/SMS/Chat/E-mail, Others',
  `dispatch_by` int DEFAULT NULL COMMENT 'Staff ID who dispatched',
  `dispatch_date` timestamp NULL DEFAULT NULL COMMENT 'Date and time of dispatch',
  `action_taken` text COLLATE utf8mb4_unicode_ci COMMENT 'Action taken by concerned personnel',
  `acknowledged_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Person who acknowledged',
  `date_settled` timestamp NULL DEFAULT NULL COMMENT 'Date settled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `reference_code`, `reporter_name`, `contact`, `description`, `type`, `municipality`, `barangay`, `address`, `lat`, `lon`, `photo_path`, `status`, `assigned_to`, `created_at`, `updated_at`, `dispatch_to`, `dispatch_mode`, `dispatch_by`, `dispatch_date`, `action_taken`, `acknowledged_by`, `date_settled`) VALUES
(8, 'CLN20251219-0001', 'Jaderby Peñaranda', 'jaderzkiepenaranda@gmail.com', 'no bill', 'NO_POWER_BILL', 'Barugo', 'Cuta', 'avenue', 11.31564100, 124.75556400, 'assets/uploads/complaint_6944ebee9973d4.03928019.jpg', 'NEW', NULL, '2025-12-19 06:08:46', '2025-12-19 06:46:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaint_audit_logs`
--

CREATE TABLE `complaint_audit_logs` (
  `id` int NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `user_id` int DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaint_audit_logs`
--

INSERT INTO `complaint_audit_logs` (`id`, `action`, `details`, `user_id`, `ip_address`, `created_at`) VALUES
(1, 'COMPLAINT_CREATED', 'Complaint LEY-20251211-0001 created', NULL, '172.22.0.1', '2025-12-11 06:05:06'),
(2, 'COMPLAINT_CREATED', 'Complaint LEY-20251211-0002 created', NULL, '172.22.0.1', '2025-12-11 06:43:49'),
(3, 'COMPLAINT_CREATED', 'Complaint CMPF32C63C8 created', NULL, '172.22.0.1', '2025-12-12 02:34:26'),
(4, 'COMPLAINT_CREATED', 'Complaint LEY-20251212-0001 created', NULL, '172.22.0.1', '2025-12-12 02:38:18'),
(5, 'COMPLAINT_STATUS_UPDATED', 'Complaint LEY-20251212-0001 status changed to INVESTIGATING', 1, '172.22.0.1', '2025-12-12 02:52:20'),
(6, 'COMPLAINT_CREATED', 'Complaint LEY-20251212-0001 created', NULL, '172.22.0.1', '2025-12-12 03:05:50'),
(7, 'COMPLAINT_CREATED', 'Complaint LEY-20251212-0001 created', NULL, '172.22.0.1', '2025-12-12 03:12:37'),
(8, 'COMPLAINT_STATUS_UPDATED', 'Complaint LEY-20251212-0001 status changed to INVESTIGATING', 1, '172.22.0.1', '2025-12-15 00:47:59'),
(9, 'COMPLAINT_CREATED', 'Complaint CLN20251215-0001 created', NULL, '172.22.0.1', '2025-12-15 01:08:46'),
(10, 'COMPLAINT_DISPATCH_UPDATED', 'Complaint CLN20251215-0001 dispatch details updated', 1, '172.22.0.1', '2025-12-15 04:46:23'),
(11, 'COMPLAINT_DISPATCH_UPDATED', 'Complaint CLN20251215-0001 dispatch details updated', 1, '172.22.0.1', '2025-12-15 04:47:08'),
(12, 'COMPLAINT_DISPATCH_UPDATED', 'Complaint CLN20251215-0001 dispatch details updated', 1, '172.22.0.1', '2025-12-15 04:48:59'),
(13, 'COMPLAINT_DISPATCH_UPDATED', 'Complaint CLN20251215-0001 dispatch details updated', 1, '172.22.0.1', '2025-12-15 04:52:02'),
(14, 'COMPLAINT_STATUS_UPDATED', 'Complaint CLN20251215-0001 status changed to INVESTIGATING', 1, '172.22.0.1', '2025-12-15 05:09:27'),
(15, 'COMPLAINT_STATUS_UPDATED', 'Complaint CLN20251215-0001 status changed to RESOLVED', 7, '172.22.0.1', '2025-12-17 23:44:42'),
(16, 'COMPLAINT_CREATED', 'Complaint CLN20251219-0001 created', NULL, '172.22.0.1', '2025-12-19 06:08:46'),
(17, 'COMPLAINT_STATUS_UPDATED', 'Complaint CLN20251219-0001 status changed to RESOLVED', 1, '172.22.0.1', '2025-12-19 06:46:06'),
(18, 'COMPLAINT_STATUS_UPDATED', 'Complaint CLN20251219-0001 status changed to CLOSED', 1, '172.22.0.1', '2025-12-19 06:46:34'),
(19, 'COMPLAINT_STATUS_UPDATED', 'Complaint CLN20251219-0001 status changed to NEW', 1, '172.22.0.1', '2025-12-19 06:46:51');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_comments`
--

CREATE TABLE `complaint_comments` (
  `id` int NOT NULL,
  `complaint_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `complaint_comments`
--

INSERT INTO `complaint_comments` (`id`, `complaint_id`, `user_id`, `message`, `created_at`) VALUES
(19, 8, NULL, 'Complaint submitted', '2025-12-19 06:08:46'),
(20, 8, 1, 'Status changed to: RESOLVED', '2025-12-19 06:46:06'),
(21, 8, 1, 'Status changed to: CLOSED', '2025-12-19 06:46:34'),
(22, 8, 1, 'Status changed to: NEW', '2025-12-19 06:46:51');

-- --------------------------------------------------------

--
-- Table structure for table `requisition_items`
--

CREATE TABLE `requisition_items` (
  `id` int NOT NULL,
  `requisition_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_inventory` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance_for_purchase` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requisition_items`
--

INSERT INTO `requisition_items` (`id`, `requisition_id`, `quantity`, `unit`, `description`, `warehouse_inventory`, `balance_for_purchase`, `remarks`, `created_at`) VALUES
(1, 1, 10, 'meters', 'Cable Cords', '200', 'no stock', 'Verified', '2025-12-17 13:13:57'),
(2, 1, 1, 'liters', 'diet coke', '300', '233', 'Verified', '2025-12-17 13:13:57'),
(3, 1, 2, 'boxes', 'Tape', '299', '323', 'Verified', '2025-12-17 13:13:57'),
(4, 2, 1, 'pcs', 'Tarpaulin', '23', '₱323', 'Verified', '2025-12-17 23:37:03'),
(5, 2, 2, 'pcs', 'Amplifier TI -  12065', '23', '₱5000', 'Verified', '2025-12-17 23:37:03'),
(6, 2, 10, 'pcs', 'Speaker Loud base', '2', 'No budget sorry', 'Incomplete', '2025-12-17 23:37:03'),
(10, 6, 33, 'pcs', 'bruh', NULL, NULL, NULL, '2025-12-19 03:47:32');

-- --------------------------------------------------------

--
-- Table structure for table `requisition_requests`
--

CREATE TABLE `requisition_requests` (
  `id` int NOT NULL,
  `rf_control_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requester_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purpose` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `current_approval_level` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requisition_requests`
--

INSERT INTO `requisition_requests` (`id`, `rf_control_number`, `requester_name`, `department`, `purpose`, `status`, `current_approval_level`, `created_at`, `updated_at`) VALUES
(1, 'RF-20251217-0001', 'Jade', 'Technical Services Department', 'For Embedded Action', 'approved', 5, '2025-12-17 13:13:57', '2025-12-17 14:47:32'),
(2, 'RF-20251218-0001', 'Jeric', 'Technical Services Department', 'For AGMA August event', 'approved', 5, '2025-12-17 23:37:03', '2025-12-17 23:44:09'),
(6, 'RF-20251219-0001', 'Loren', 'Institutional Services Department', 'dfdfgd', 'pending', 1, '2025-12-19 03:47:32', '2025-12-19 03:47:32');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `session_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `expires_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `session_token`, `ip_address`, `user_agent`, `expires_at`, `created_at`) VALUES
(79, 1, 'a6bd6b17a5678865f1af7aa1cd88d19bbb9c63fdab2b9c54dedf2c9259bb5c03', '172.22.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-19 16:07:37', '2025-12-19 08:07:37'),
(81, 1, '950a946074307579ee7ce640decbb3458f78581ac667ed2476076d7d437addc3', '172.22.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-19 20:50:26', '2025-12-19 12:50:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Jaderby', '$2y$10$yXeJjfjSfJopCd.5GjmdMOHf7AaLViUm1pZLQjuLWyRf6IB/NaKei', 'jaderzkiepenaranda@gmail.com', 'Jaderby Peñaranda', 'admin', 1, '2025-12-19 12:50:26', '2025-12-11 03:31:15', '2025-12-19 12:50:26'),
(2, 'Jeric', '$2y$10$A8WJiGscF3PKzctaTcxN/OaQ1PAQFUZI/mNaYPp1YnGRzenBcBr92', 'jeric@gmail.com', 'Jeric', 'staff,approver', 1, '2025-12-17 23:37:27', '2025-12-11 03:31:15', '2025-12-17 23:37:27'),
(3, 'Richard', '$2y$10$26Qk4/aCkF.wG623Ag7LmeNOan7RLa.tmUgN5Rqq4P8AGNgp6crVm', 'richard@leyeco3.com', 'ENGR. Richard A. Forsuelo', 'staff,approver', 1, '2025-12-18 03:03:01', '2025-12-17 13:23:39', '2025-12-18 03:03:01'),
(4, 'Glen', '$2y$10$B0J1A8.HFErSU6jhBQGhtuc6AF8O5fbBkYkLdJYwf/KekTIrzqiW.', 'glen@leyeco3.com', 'Glen C. Arpon', 'staff,approver', 1, '2025-12-17 23:38:23', '2025-12-17 13:25:13', '2025-12-17 23:38:23'),
(5, 'Abraham', '$2y$10$ABA/cEb8lsQxoAjc6lWKT.TvBULmcHy4xV461fPglEfRrZYMzfqPS', 'abarca@leyeco3.com', 'Abraham M. Abarca Jr.', 'staff,approver', 1, '2025-12-17 23:40:09', '2025-12-17 13:26:37', '2025-12-17 23:40:09'),
(6, 'Renee', '$2y$10$6WUYrNAV5Lm/llt.Zg5nDeIJQlsZNt9JqLmwyF0Bcsw1uN5tMwALa', 'renee@leyeco3.com', 'Renee A. Ong CPA.', 'staff,approver', 1, '2025-12-17 23:41:33', '2025-12-17 13:27:54', '2025-12-17 23:41:33'),
(7, 'Allan', '$2y$10$rsdJrSrEW9H1UckuR.RQtedXrlF8KQD0T9Fuv429PnzlbqjvXMYwW', 'allan@leyeco3.com', 'Allan L. Laniba MPA, MM', 'staff,approver', 1, '2025-12-17 23:43:10', '2025-12-17 13:28:45', '2025-12-17 23:43:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_requisition_level` (`requisition_id`,`approval_level`),
  ADD KEY `idx_requisition_id` (`requisition_id`),
  ADD KEY `idx_approval_level` (`approval_level`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `approvers`
--
ALTER TABLE `approvers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_approval_level` (`approval_level`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_code` (`reference_code`),
  ADD KEY `idx_reference_code` (`reference_code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_municipality` (`municipality`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_barangay` (`barangay`),
  ADD KEY `idx_dispatch_by` (`dispatch_by`);

--
-- Indexes for table `complaint_audit_logs`
--
ALTER TABLE `complaint_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `complaint_comments`
--
ALTER TABLE `complaint_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_complaint_id` (`complaint_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `requisition_items`
--
ALTER TABLE `requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_requisition_id` (`requisition_id`);

--
-- Indexes for table `requisition_requests`
--
ALTER TABLE `requisition_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rf_control_number` (`rf_control_number`),
  ADD KEY `idx_rf_control_number` (`rf_control_number`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_current_approval_level` (`current_approval_level`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `idx_session_token` (`session_token`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_users_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approvers`
--
ALTER TABLE `approvers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `complaint_audit_logs`
--
ALTER TABLE `complaint_audit_logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `complaint_comments`
--
ALTER TABLE `complaint_comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `requisition_items`
--
ALTER TABLE `requisition_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `requisition_requests`
--
ALTER TABLE `requisition_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_ibfk_1` FOREIGN KEY (`requisition_id`) REFERENCES `requisition_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaint_comments`
--
ALTER TABLE `complaint_comments`
  ADD CONSTRAINT `complaint_comments_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requisition_items`
--
ALTER TABLE `requisition_items`
  ADD CONSTRAINT `requisition_items_ibfk_1` FOREIGN KEY (`requisition_id`) REFERENCES `requisition_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
