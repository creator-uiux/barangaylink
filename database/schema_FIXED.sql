-- BarangayLink Database Schema
-- FIXED FOR barangaylink_db DATABASE
-- Run this in phpMyAdmin to fix foreign key errors!

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+08:00";

-- Drop existing database and recreate
DROP DATABASE IF EXISTS `barangaylink_db`;
CREATE DATABASE IF NOT EXISTS `barangaylink_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `barangaylink_db`;

-- =====================================================
-- TABLE: users (CREATE FIRST - Referenced by others)
-- =====================================================

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('resident','official','admin') NOT NULL DEFAULT 'resident',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- INSERT ADMIN USER FIRST (ID will be 1)
INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `address`, `phone`, `role`, `is_active`) VALUES
(1, 'Admin', NULL, 'User', 'admin@barangaylink.gov.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Barangay Hall', '+63 912 345 6789', 'admin', 1);
-- Password: password

-- INSERT TEST USER (ID will be 2)
INSERT INTO `users` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `address`, `phone`, `role`, `is_active`) VALUES
(2, 'Juan', 'Santos', 'Dela Cruz', 'juan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '123 Main Street', '+63 912 345 6780', 'resident', 1);
-- Password: password

-- INSERT MORE TEST USERS
INSERT INTO `users` (`first_name`, `middle_name`, `last_name`, `email`, `password`, `address`, `phone`, `role`, `is_active`) VALUES
('Maria', 'Garcia', 'Santos', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '456 Sunset Blvd', '+63 912 345 6781', 'resident', 1),
('Pedro', 'Lopez', 'Reyes', 'pedro@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '789 River Road', '+63 912 345 6782', 'resident', 1),
('Rosa', 'Mendoza', 'Cruz', 'rosa@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '321 Hill Street', '+63 912 345 6783', 'official', 1);

-- =====================================================
-- TABLE: documents
-- =====================================================

CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `purpose` text NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `status` enum('pending','processing','approved','rejected','ready','claimed') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  KEY `processed_by` (`processed_by`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: concerns
-- =====================================================

CREATE TABLE `concerns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('pending','in-progress','resolved','rejected','closed') NOT NULL DEFAULT 'pending',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `admin_response` text DEFAULT NULL,
  `resolved_by` int(11) DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `category` (`category`),
  KEY `priority` (`priority`),
  KEY `created_at` (`created_at`),
  KEY `resolved_by` (`resolved_by`),
  CONSTRAINT `concerns_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `concerns_ibfk_2` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: announcements
-- =====================================================

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(100) DEFAULT 'General',
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_at` (`created_at`),
  KEY `is_active` (`is_active`),
  KEY `priority` (`priority`),
  KEY `category` (`category`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: events
-- =====================================================

CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `category` varchar(100) DEFAULT 'Community',
  `max_participants` int(11) DEFAULT NULL,
  `current_participants` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `event_date` (`event_date`),
  KEY `created_at` (`created_at`),
  KEY `is_active` (`is_active`),
  KEY `category` (`category`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: emergency_alerts
-- =====================================================

CREATE TABLE `emergency_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `severity` enum('info','warning','danger','critical') NOT NULL DEFAULT 'info',
  `alert_type` varchar(100) DEFAULT 'General',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `severity` (`severity`),
  KEY `is_active` (`is_active`),
  KEY `created_at` (`created_at`),
  KEY `expires_at` (`expires_at`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `emergency_alerts_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: notifications
-- =====================================================

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `related_type` varchar(50) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`),
  KEY `type` (`type`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: barangay_officials
-- =====================================================

CREATE TABLE `barangay_officials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `position_order` int(11) NOT NULL DEFAULT 0,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `position_order` (`position_order`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SAMPLE DATA
-- =====================================================

-- Barangay Officials
INSERT INTO `barangay_officials` (`name`, `position`, `position_order`, `email`, `phone`, `description`, `is_active`) VALUES
('Hon. Juan Dela Cruz', 'Barangay Captain', 1, 'captain@barangay.gov.ph', '+63 912 345 6789', 'Serving the community with dedication', 1),
('Maria Santos', 'Barangay Kagawad - Health', 2, 'health@barangay.gov.ph', '+63 912 345 6780', 'Health Committee Head', 1),
('Pedro Reyes', 'Barangay Kagawad - Peace & Order', 3, 'peace@barangay.gov.ph', '+63 912 345 6781', 'Peace Committee Chairman', 1),
('Rosa Garcia', 'Barangay Secretary', 4, 'secretary@barangay.gov.ph', '+63 912 345 6782', 'Administrative services', 1);

-- Emergency Alerts
INSERT INTO `emergency_alerts` (`title`, `message`, `severity`, `alert_type`, `is_active`, `created_by`, `expires_at`) VALUES
('Weather Advisory', 'Heavy rainfall expected in the next 24-48 hours. Stay safe!', 'warning', 'Weather', 1, 1, DATE_ADD(NOW(), INTERVAL 2 DAY)),
('Health Program', 'Free vaccination this Saturday at Barangay Hall, 8AM-12PM', 'info', 'Health', 1, 1, DATE_ADD(NOW(), INTERVAL 7 DAY));

-- Announcements
INSERT INTO `announcements` (`title`, `content`, `category`, `priority`, `is_active`, `created_by`) VALUES
('Community Assembly', 'Quarterly barangay assembly on November 20, 2025 at 5PM', 'Community', 'high', 1, 1),
('Garbage Schedule Update', 'New garbage collection: Mon/Thu biodegradable, Wed/Sat non-biodegradable', 'Environment', 'normal', 1, 1),
('Basketball League', 'Registration open for Annual Basketball League. Sign up until Nov 25!', 'Sports', 'normal', 1, 1);

-- Events
INSERT INTO `events` (`title`, `description`, `location`, `event_date`, `event_time`, `category`, `max_participants`, `is_active`, `created_by`) VALUES
('Community Clean-Up', 'Join our cleanup drive. Free snacks provided!', 'Barangay Hall', '2025-11-20', '07:00:00', 'Environment', 100, 1, 1),
('Free Medical Mission', 'Free consultation and medicine for seniors', 'Health Center', '2025-11-22', '08:00:00', 'Health', 150, 1, 1),
('Christmas Party 2025', 'Annual Christmas celebration with games and prizes', 'Barangay Hall', '2025-12-20', '17:00:00', 'Festival', 500, 1, 1);

-- Performance Indexes
ALTER TABLE `documents` ADD INDEX `user_status_idx` (`user_id`, `status`);
ALTER TABLE `concerns` ADD INDEX `user_status_idx` (`user_id`, `status`);
ALTER TABLE `notifications` ADD INDEX `user_read_idx` (`user_id`, `is_read`);

COMMIT;

-- =====================================================
-- VERIFY DATA
-- =====================================================
SELECT 'Database created successfully!' as status;
SELECT 'Users table:' as info, COUNT(*) as count FROM users;
SELECT 'Documents table:' as info, COUNT(*) as count FROM documents;
SELECT 'Concerns table:' as info, COUNT(*) as count FROM concerns;
SELECT 'Announcements:' as info, COUNT(*) as count FROM announcements;
SELECT 'Events:' as info, COUNT(*) as count FROM events;
SELECT 'Officials:' as info, COUNT(*) as count FROM barangay_officials;

-- Show all users
SELECT id, first_name, last_name, email, role FROM users;
