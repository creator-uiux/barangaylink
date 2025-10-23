-- BarangayLink Digital Governance Platform Database Schema
-- MySQL Database Setup for Real-time Data Management

CREATE DATABASE IF NOT EXISTS barangaylink_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barangaylink_db;

-- Users table with enhanced fields
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT '',
    last_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    status ENUM('active', 'inactive', 'deleted', 'suspended') NOT NULL DEFAULT 'active',
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    restored_at TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_role (role)
);

-- Notifications table for real-time notifications
CREATE TABLE IF NOT EXISTS notifications (
    id VARCHAR(50) PRIMARY KEY,
    type ENUM('success', 'warning', 'error', 'info') NOT NULL DEFAULT 'info',
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    user_id VARCHAR(255) NULL, -- NULL for general notifications
    read_status BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_read_status (read_status),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at)
);

-- Concerns table for user concerns
CREATE TABLE IF NOT EXISTS concerns (
    id VARCHAR(50) PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'resolved', 'closed') DEFAULT 'pending',
    assigned_to VARCHAR(255) NULL,
    resolution_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    INDEX idx_user_email (user_email),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_category (category),
    FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE
);

-- Document requests table
CREATE TABLE IF NOT EXISTS document_requests (
    id VARCHAR(50) PRIMARY KEY,
    user_email VARCHAR(255) NOT NULL,
    document_type VARCHAR(100) NOT NULL,
    purpose TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'ready_for_pickup', 'completed') DEFAULT 'pending',
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    INDEX idx_user_email (user_email),
    INDEX idx_status (status),
    INDEX idx_document_type (document_type),
    FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE
);

-- System settings table
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (email, password, first_name, last_name, role, status, address, phone) 
VALUES ('admin@barangaylink.com', 'admin123', 'System', 'Administrator', 'admin', 'active', 'Barangay Hall', '123-456-7890')
ON DUPLICATE KEY UPDATE email = email;

-- Insert default system settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('site_name', 'BarangayLink Digital Governance Platform', 'Website name'),
('site_description', 'Digital governance platform for barangay management', 'Website description'),
('maintenance_mode', 'false', 'Maintenance mode status'),
('registration_enabled', 'true', 'User registration status'),
('notification_enabled', 'true', 'Notification system status')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Insert sample notifications
INSERT INTO notifications (id, type, title, message, user_id, read_status) VALUES
('not_001', 'success', 'Document Request Approved', 'Your Barangay Clearance request has been approved and is ready for pickup.', 'user@email.com', FALSE),
('not_002', 'info', 'Concern Update', 'Your concern about the street light has been assigned to the maintenance team.', 'user@email.com', FALSE),
('not_003', 'warning', 'Holiday Schedule', 'Please note the modified office hours during the holiday season.', NULL, TRUE),
('not_004', 'info', 'Welcome to BarangayLink!', 'Your account has been created successfully. Welcome to our digital governance platform.', NULL, FALSE)
ON DUPLICATE KEY UPDATE id = id;

-- Create indexes for better performance
CREATE INDEX idx_users_email_status ON users(email, status);
CREATE INDEX idx_notifications_user_read ON notifications(user_id, read_status);
CREATE INDEX idx_concerns_user_status ON concerns(user_email, status);
CREATE INDEX idx_documents_user_status ON document_requests(user_email, status);

-- Create views for common queries
CREATE VIEW user_notifications AS
SELECT 
    n.*,
    u.first_name,
    u.last_name,
    u.email
FROM notifications n
LEFT JOIN users u ON n.user_id = u.email
ORDER BY n.created_at DESC;

CREATE VIEW user_concerns_summary AS
SELECT 
    c.*,
    u.first_name,
    u.last_name
FROM concerns c
JOIN users u ON c.user_email = u.email
ORDER BY c.created_at DESC;

CREATE VIEW user_documents_summary AS
SELECT 
    d.*,
    u.first_name,
    u.last_name
FROM document_requests d
JOIN users u ON d.user_email = u.email
ORDER BY d.created_at DESC;
