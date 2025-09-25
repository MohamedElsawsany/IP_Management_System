-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 25, 2025 at 04:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ip_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

-- INSERT INTO `branches` (`id`, `name`, `created_at`) VALUES
-- (1, 'Korba', '2025-09-25 12:28:34'),
-- (2, 'Alex', '2025-09-25 12:28:34'),
-- (3, 'Obouar', '2025-09-25 12:28:34');

-- --------------------------------------------------------

--
-- Table structure for table `device_types`
--

CREATE TABLE `device_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `device_types`
--

INSERT INTO `device_types` (`id`, `name`, `created_at`) VALUES
(1, 'Router', '2025-09-25 12:28:34'),
(2, 'Switch', '2025-09-25 12:28:34'),
(3, 'Firewall', '2025-09-25 12:28:34'),
(4, 'Access Point', '2025-09-25 12:28:34'),
(5, 'Server', '2025-09-25 12:28:34'),
(6, 'Workstation', '2025-09-25 12:28:34'),
(7, 'Printer', '2025-09-25 12:28:34'),
(8, 'Camera', '2025-09-25 12:28:34'),
(9, 'Phone', '2025-09-25 12:28:34');

-- --------------------------------------------------------

--
-- Table structure for table `ips`
--

CREATE TABLE `ips` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `device_name` varchar(100) NOT NULL,
  `device_type_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ips`
--

-- INSERT INTO `ips` (`id`, `ip_address`, `device_name`, `device_type_id`, `branch_id`, `description`, `created_at`, `updated_at`) VALUES
-- (13, '172.16.7.1', 'Alex-IRAM-Router', 1, 2, 'Alex-IRAM-Router', '2025-09-25 12:31:36', '2025-09-25 12:31:36'),
-- (14, '192.168.1.100', 'Mohamed Hassan', 6, 2, 'Alex-IRAM-Router', '2025-09-25 12:33:07', '2025-09-25 12:33:07'),
-- (16, '172.16.7.2', 'Korba-Core-Router', 1, 1, 'Main router for Korba branch', '2025-09-25 10:00:00', '2025-09-25 10:00:00'),
-- (17, '192.168.1.101', 'Obouar-Switch-01', 2, 3, 'Core switch for Obouar network', '2025-09-25 10:05:00', '2025-09-25 10:05:00'),
-- (18, '172.16.7.10', 'Alex-Firewall', 3, 2, 'Primary firewall for Alex branch', '2025-09-25 10:10:00', '2025-09-25 10:10:00'),
-- (19, '192.168.1.102', 'Korba-AP-01', 4, 1, 'Wireless access point for Korba office', '2025-09-25 10:15:00', '2025-09-25 10:15:00'),
-- (20, '172.16.7.20', 'Alex-Server-01', 5, 2, 'File server for Alex branch', '2025-09-25 10:20:00', '2025-09-25 10:20:00'),
-- (21, '192.168.1.103', 'Obouar-Workstation-01', 6, 3, 'Employee workstation in Obouar', '2025-09-25 10:25:00', '2025-09-25 10:25:00'),
-- (22, '172.16.7.30', 'Korba-Printer-01', 7, 1, 'Network printer for Korba branch', '2025-09-25 10:30:00', '2025-09-25 10:30:00'),
-- (23, '192.168.1.104', 'Alex-Camera-01', 8, 2, 'Security camera in Alex office', '2025-09-25 10:35:00', '2025-09-25 10:35:00'),
-- (24, '172.16.7.40', 'Obouar-Phone-01', 9, 3, 'VoIP phone for Obouar branch', '2025-09-25 10:40:00', '2025-09-25 10:40:00'),
-- (25, '192.168.1.105', 'Korba-Server-01', 5, 1, 'Backup server for Korba branch', '2025-09-25 10:45:00', '2025-09-25 10:45:00'),
-- (26, '172.16.7.3', 'Korba-Switch-01', 2, 1, 'Secondary switch for Korba branch', '2025-09-25 11:00:00', '2025-09-25 11:00:00'),
-- (27, '192.168.1.106', 'Alex-AP-01', 4, 2, 'Wireless access point for Alex office', '2025-09-25 11:05:00', '2025-09-25 11:05:00'),
-- (28, '172.16.7.11', 'Obouar-Firewall-01', 3, 3, 'Firewall for Obouar network security', '2025-09-25 11:10:00', '2025-09-25 11:10:00'),
-- (29, '192.168.1.107', 'Korba-Workstation-02', 6, 1, 'Employee workstation in Korba', '2025-09-25 11:15:00', '2025-09-25 11:15:00'),
-- (30, '172.16.7.21', 'Alex-Server-02', 5, 2, 'Database server for Alex branch', '2025-09-25 11:20:00', '2025-09-25 11:20:00'),
-- (31, '192.168.1.108', 'Obouar-Printer-01', 7, 3, 'Network printer for Obouar branch', '2025-09-25 11:25:00', '2025-09-25 11:25:00'),
-- (32, '172.16.7.31', 'Korba-Camera-01', 8, 1, 'Security camera for Korba office', '2025-09-25 11:30:00', '2025-09-25 11:30:00'),
-- (33, '192.168.1.109', 'Alex-Phone-01', 9, 2, 'VoIP phone for Alex branch', '2025-09-25 11:35:00', '2025-09-25 11:35:00'),
-- (34, '172.16.7.41', 'Obouar-Router-01', 1, 3, 'Main router for Obouar branch', '2025-09-25 11:40:00', '2025-09-25 11:40:00'),
-- (35, '192.168.1.110', 'Korba-AP-02', 4, 1, 'Secondary access point for Korba office', '2025-09-25 11:45:00', '2025-09-25 11:45:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device_types`
--
ALTER TABLE `device_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ips`
--
ALTER TABLE `ips`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`),
  ADD KEY `idx_branch_id` (`branch_id`),
  ADD KEY `idx_device_type_id` (`device_type_id`),
  ADD KEY `idx_ip_address` (`ip_address`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `device_types`
--
ALTER TABLE `device_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ips`
--
ALTER TABLE `ips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ips`
--
ALTER TABLE `ips`
  ADD CONSTRAINT `ips_ibfk_1` FOREIGN KEY (`device_type_id`) REFERENCES `device_types` (`id`),
  ADD CONSTRAINT `ips_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
