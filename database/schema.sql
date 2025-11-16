-- BarangayLink Database Schema
-- COMPLETE FIXED VERSION - ALL ISSUES RESOLVED!
-- Run this file in phpMyAdmin to create the entire database

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+08:00";

-- =====================================================
-- DATABASE CREATION
-- =====================================================

DROP DATABASE IF EXISTS `barangaylink`;
CREATE DATABASE IF NOT EXISTS `barangaylink` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `barangaylink`;

-- =====================================================
-- TABLE: users
-- Stores all user accounts (residents, officials, admin)
-- =====================================================

DROP TABLE IF EXISTS `users`;
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

-- =====================================================
-- TABLE: documents
-- Stores document requests from residents
-- =====================================================

DROP TABLE IF EXISTS `documents`;
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
-- Stores community concerns and reports
-- =====================================================

DROP TABLE IF EXISTS `concerns`;
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
-- Stores barangay announcements
-- =====================================================

DROP TABLE IF EXISTS `announcements`;
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
-- Stores barangay events and activities
-- =====================================================

DROP TABLE IF EXISTS `events`;
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
-- Stores emergency alerts and warnings
-- =====================================================

DROP TABLE IF EXISTS `emergency_alerts`;
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
-- Stores user notifications
-- =====================================================

DROP TABLE IF EXISTS `notifications`;
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
-- Stores barangay officials directory
-- =====================================================

DROP TABLE IF EXISTS `barangay_officials`;
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
-- SAMPLE DATA: Default Admin Account
-- =====================================================

INSERT INTO `users` (`first_name`, `middle_name`, `last_name`, `email`, `password`, `address`, `phone`, `role`, `is_active`) VALUES
('Admin', NULL, 'User', 'admin@barangay.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Barangay Hall, Main Street', '+63 912 345 6789', 'admin', 1);
-- Password: password

-- =====================================================
-- SAMPLE DATA: Barangay Officials
-- =====================================================

INSERT INTO `barangay_officials` (`name`, `position`, `position_order`, `email`, `phone`, `description`, `is_active`) VALUES
('Hon. Juan Dela Cruz', 'Barangay Captain', 1, 'captain@barangay.gov.ph', '+63 912 345 6789', 'Serving the community with dedication for over 10 years. Committed to transparency and progress.', 1),
('Maria Santos', 'Barangay Kagawad - Health & Sanitation', 2, 'health@barangay.gov.ph', '+63 912 345 6780', 'Head of Health and Sanitation Committee. Licensed nurse with 15 years of community health experience.', 1),
('Pedro Reyes', 'Barangay Kagawad - Peace & Order', 3, 'peace@barangay.gov.ph', '+63 912 345 6781', 'Peace and Order Committee Chairman. Former police officer ensuring community safety.', 1),
('Rosa Garcia', 'Barangay Kagawad - Education', 4, 'education@barangay.gov.ph', '+63 912 345 6782', 'Education Committee Head. Former teacher advocating for youth development.', 1),
('Jose Mendoza', 'Barangay Kagawad - Infrastructure', 5, 'infrastructure@barangay.gov.ph', '+63 912 345 6783', 'Infrastructure Committee Chairman. Civil engineer overseeing barangay projects.', 1),
('Ana Lopez', 'Barangay Secretary', 6, 'secretary@barangay.gov.ph', '+63 912 345 6784', 'Handles all administrative services and document processing.', 1),
('Carlos Rivera', 'Barangay Treasurer', 7, 'treasurer@barangay.gov.ph', '+63 912 345 6785', 'Manages barangay finances and budget allocation.', 1),
('Linda Cruz', 'SK Chairperson', 8, 'sk@barangay.gov.ph', '+63 912 345 6786', 'Sangguniang Kabataan Chairperson. Youth leader and student advocate.', 1);

-- =====================================================
-- SAMPLE DATA: Emergency Alerts
-- =====================================================

INSERT INTO `emergency_alerts` (`title`, `message`, `severity`, `alert_type`, `is_active`, `expires_at`) VALUES
('Heavy Rainfall Warning', 'Weather bureau advises heavy rainfall expected in the next 24-48 hours. Residents in low-lying areas should take precautionary measures. Prepare emergency kits and stay updated.', 'warning', 'Weather', 1, DATE_ADD(NOW(), INTERVAL 2 DAY)),
('Community Health Alert', 'Free health checkup and vaccination program this Saturday at the Barangay Hall from 8:00 AM to 12:00 PM. Bring your health cards. First come, first served.', 'info', 'Health', 1, DATE_ADD(NOW(), INTERVAL 7 DAY)),
('Road Closure Advisory', 'Main Street will be temporarily closed on November 15-16 for road repairs. Please use alternative routes. We apologize for the inconvenience.', 'warning', 'Infrastructure', 1, DATE_ADD(NOW(), INTERVAL 5 DAY));

-- =====================================================
-- SAMPLE DATA: Announcements
-- =====================================================

INSERT INTO `announcements` (`title`, `content`, `category`, `priority`, `is_active`) VALUES
('Quarterly Barangay Assembly', 'All residents are cordially invited to attend the quarterly barangay assembly on November 20, 2025, at 5:00 PM at the Barangay Hall. Your participation is highly encouraged as we discuss important community matters and upcoming projects.', 'Community', 'high', 1),
('Updated Garbage Collection Schedule', 'Effective next week, garbage collection schedule will be updated. Monday and Thursday for biodegradable waste, Wednesday and Saturday for non-biodegradable waste. Please segregate your waste properly to help keep our community clean.', 'Environment', 'normal', 1),
('Basketball League Registration Open', 'Registration for the Annual Barangay Basketball League is now open! Sign up at the Barangay Hall until November 25, 2025. Open to all male residents aged 18-40. Championship game will be held in December.', 'Sports', 'normal', 1),
('Senior Citizens Benefits', 'Senior citizens are reminded to claim your quarterly benefits on November 18, 2025. Bring your senior citizen ID and barangay clearance. Distribution will be from 8:00 AM to 3:00 PM at the Barangay Hall.', 'Social Services', 'high', 1),
('Barangay Cleanup Drive', 'Join us for a community cleanup drive this Saturday, November 16, 2025, starting at 6:00 AM. Meeting point at the Barangay Hall. Bring your own cleaning materials. Snacks and refreshments will be provided. Let\'s keep our barangay clean and green!', 'Environment', 'normal', 1);

-- =====================================================
-- SAMPLE DATA: Events
-- =====================================================

INSERT INTO `events` (`title`, `description`, `location`, `event_date`, `event_time`, `category`, `max_participants`, `is_active`) VALUES
('Community Clean-Up Drive', 'Join us for a community-wide clean-up drive. Bring your own cleaning materials. Snacks and refreshments will be provided. Let\'s work together to keep our barangay clean and beautiful!', 'Barangay Covered Court', '2025-11-20', '07:00:00', 'Environment', 100, 1),
('Free Medical Mission', 'Free medical consultation, blood pressure monitoring, blood sugar testing, and medicine distribution for senior citizens and PWDs. Bring your health cards and IDs. Limited slots available.', 'Barangay Health Center', '2025-11-22', '08:00:00', 'Health', 150, 1),
('Christmas Party 2025', 'Annual barangay Christmas party with games, prizes, raffle draws, and special performances. All residents are welcome! Free food and giveaways. Come and celebrate with your community!', 'Barangay Hall', '2025-12-20', '17:00:00', 'Festival', 500, 1),
('Disaster Preparedness Seminar', 'Learn essential disaster preparedness and first aid skills. Resource speakers from the local disaster risk reduction office. Free certificates will be given to attendees.', 'Barangay Function Hall', '2025-11-25', '13:00:00', 'Education', 80, 1),
('Youth Sports Fest', 'Inter-purok sports competition for the youth. Events include basketball, volleyball, badminton, and table tennis. Register your team now! Winning teams will receive trophies and medals.', 'Barangay Sports Complex', '2025-12-05', '08:00:00', 'Sports', 200, 1);

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

-- Additional composite indexes for better query performance
ALTER TABLE `documents` ADD INDEX `user_status_idx` (`user_id`, `status`);
ALTER TABLE `concerns` ADD INDEX `user_status_idx` (`user_id`, `status`);
ALTER TABLE `notifications` ADD INDEX `user_read_idx` (`user_id`, `is_read`);

-- =====================================================
-- COMMIT TRANSACTION
-- =====================================================

COMMIT;

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Uncomment these to verify the schema was created correctly:
-- SHOW TABLES;
-- SELECT COUNT(*) as total_users FROM users;
-- SELECT COUNT(*) as total_officials FROM barangay_officials;
-- SELECT COUNT(*) as total_announcements FROM announcements;
-- SELECT COUNT(*) as total_events FROM events;
-- SELECT COUNT(*) as total_alerts FROM emergency_alerts;

-- =====================================================
-- SCHEMA CREATION COMPLETE!
-- =====================================================
