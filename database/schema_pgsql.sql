-- BarangayLink Database Schema - PostgreSQL Version
-- COMPLETE PostgreSQL COMPATIBLE VERSION
-- Run this file to create the entire database schema

-- =====================================================
-- EXTENSIONS (if needed)
-- =====================================================

-- CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- =====================================================
-- TABLE: users
-- Stores all user accounts (residents, officials, admin)
-- =====================================================

DROP TABLE IF EXISTS users CASCADE;

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  middle_name VARCHAR(100),
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  address TEXT,
  phone VARCHAR(20),
  role TEXT NOT NULL DEFAULT 'resident' CHECK (role IN ('resident', 'official', 'admin')),
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP
);

-- Indexes for users table
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_is_active ON users(is_active);
CREATE INDEX idx_users_email ON users(email);

-- =====================================================
-- TABLE: documents
-- Stores document requests from residents
-- =====================================================

DROP TABLE IF EXISTS documents CASCADE;

CREATE TABLE documents (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL,
  document_type VARCHAR(255) NOT NULL,
  purpose TEXT NOT NULL,
  quantity INTEGER NOT NULL DEFAULT 1,
  notes TEXT,
  status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'processing', 'approved', 'rejected', 'ready', 'claimed')),
  admin_notes TEXT,
  processed_by INTEGER,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Indexes for documents table
CREATE INDEX idx_documents_user_id ON documents(user_id);
CREATE INDEX idx_documents_status ON documents(status);
CREATE INDEX idx_documents_created_at ON documents(created_at);
CREATE INDEX idx_documents_processed_by ON documents(processed_by);
CREATE INDEX idx_documents_user_status ON documents(user_id, status);

-- =====================================================
-- TABLE: concerns
-- Stores community concerns and reports
-- =====================================================

DROP TABLE IF EXISTS concerns CASCADE;

CREATE TABLE concerns (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL,
  category VARCHAR(100) NOT NULL,
  subject VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  location VARCHAR(255),
  status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'in-progress', 'resolved', 'rejected', 'closed')),
  priority TEXT NOT NULL DEFAULT 'normal' CHECK (priority IN ('low', 'normal', 'high', 'urgent')),
  admin_response TEXT,
  resolved_by INTEGER,
  resolved_at TIMESTAMP,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Indexes for concerns table
CREATE INDEX idx_concerns_user_id ON concerns(user_id);
CREATE INDEX idx_concerns_status ON concerns(status);
CREATE INDEX idx_concerns_category ON concerns(category);
CREATE INDEX idx_concerns_priority ON concerns(priority);
CREATE INDEX idx_concerns_created_at ON concerns(created_at);
CREATE INDEX idx_concerns_resolved_by ON concerns(resolved_by);
CREATE INDEX idx_concerns_user_status ON concerns(user_id, status);

-- =====================================================
-- TABLE: announcements
-- Stores barangay announcements
-- =====================================================

DROP TABLE IF EXISTS announcements CASCADE;

CREATE TABLE announcements (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  category VARCHAR(100) DEFAULT 'General',
  priority TEXT NOT NULL DEFAULT 'normal' CHECK (priority IN ('low', 'normal', 'high', 'urgent')),
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_by INTEGER,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Indexes for announcements table
CREATE INDEX idx_announcements_created_at ON announcements(created_at);
CREATE INDEX idx_announcements_is_active ON announcements(is_active);
CREATE INDEX idx_announcements_priority ON announcements(priority);
CREATE INDEX idx_announcements_category ON announcements(category);
CREATE INDEX idx_announcements_created_by ON announcements(created_by);

-- =====================================================
-- TABLE: events
-- Stores barangay events and activities
-- =====================================================

DROP TABLE IF EXISTS events CASCADE;

CREATE TABLE events (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  location VARCHAR(255),
  event_date DATE NOT NULL,
  event_time TIME,
  end_date DATE,
  end_time TIME,
  category VARCHAR(100) DEFAULT 'Community',
  max_participants INTEGER,
  current_participants INTEGER NOT NULL DEFAULT 0,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_by INTEGER,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Indexes for events table
CREATE INDEX idx_events_event_date ON events(event_date);
CREATE INDEX idx_events_created_at ON events(created_at);
CREATE INDEX idx_events_is_active ON events(is_active);
CREATE INDEX idx_events_category ON events(category);
CREATE INDEX idx_events_created_by ON events(created_by);

-- =====================================================
-- TABLE: emergency_alerts
-- Stores emergency alerts and warnings
-- =====================================================

DROP TABLE IF EXISTS emergency_alerts CASCADE;

CREATE TABLE emergency_alerts (
  id SERIAL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  severity TEXT NOT NULL DEFAULT 'info' CHECK (severity IN ('info', 'warning', 'danger', 'critical')),
  alert_type VARCHAR(100) DEFAULT 'General',
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_by INTEGER,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP,
  expires_at TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Indexes for emergency_alerts table
CREATE INDEX idx_emergency_alerts_severity ON emergency_alerts(severity);
CREATE INDEX idx_emergency_alerts_is_active ON emergency_alerts(is_active);
CREATE INDEX idx_emergency_alerts_created_at ON emergency_alerts(created_at);
CREATE INDEX idx_emergency_alerts_expires_at ON emergency_alerts(expires_at);
CREATE INDEX idx_emergency_alerts_created_by ON emergency_alerts(created_by);

-- =====================================================
-- TABLE: notifications
-- Stores user notifications
-- =====================================================

DROP TABLE IF EXISTS notifications CASCADE;

CREATE TABLE notifications (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL,
  type VARCHAR(50) NOT NULL DEFAULT 'info',
  title VARCHAR(255) NOT NULL,
  message TEXT NOT NULL,
  is_read BOOLEAN NOT NULL DEFAULT FALSE,
  related_type VARCHAR(50),
  related_id INTEGER,
  action_url VARCHAR(255),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Indexes for notifications table
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
CREATE INDEX idx_notifications_is_read ON notifications(is_read);
CREATE INDEX idx_notifications_created_at ON notifications(created_at);
CREATE INDEX idx_notifications_type ON notifications(type);
CREATE INDEX idx_notifications_user_read ON notifications(user_id, is_read);

-- =====================================================
-- TABLE: barangay_officials
-- Stores barangay officials directory
-- =====================================================

DROP TABLE IF EXISTS barangay_officials CASCADE;

CREATE TABLE barangay_officials (
  id SERIAL PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  position VARCHAR(100) NOT NULL,
  position_order INTEGER NOT NULL DEFAULT 0,
  email VARCHAR(255),
  phone VARCHAR(20),
  photo_url VARCHAR(255),
  description TEXT,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP
);

-- Indexes for barangay_officials table
CREATE INDEX idx_barangay_officials_position_order ON barangay_officials(position_order);
CREATE INDEX idx_barangay_officials_is_active ON barangay_officials(is_active);

-- =====================================================
-- SAMPLE DATA: Default Admin Account
-- =====================================================

INSERT INTO users (first_name, middle_name, last_name, email, password, address, phone, role, is_active) VALUES
('Admin', NULL, 'User', 'admin@barangay.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Barangay Hall, Main Street', '+63 912 345 6789', 'admin', TRUE);
-- Password: password

-- =====================================================
-- SAMPLE DATA: Barangay Officials
-- =====================================================

INSERT INTO barangay_officials (name, position, position_order, email, phone, description, is_active) VALUES
('Hon. Juan Dela Cruz', 'Barangay Captain', 1, 'captain@barangay.gov.ph', '+63 912 345 6789', 'Serving the community with dedication for over 10 years. Committed to transparency and progress.', TRUE),
('Maria Santos', 'Barangay Kagawad - Health & Sanitation', 2, 'health@barangay.gov.ph', '+63 912 345 6780', 'Head of Health and Sanitation Committee. Licensed nurse with 15 years of community health experience.', TRUE),
('Pedro Reyes', 'Barangay Kagawad - Peace & Order', 3, 'peace@barangay.gov.ph', '+63 912 345 6781', 'Peace and Order Committee Chairman. Former police officer ensuring community safety.', TRUE),
('Rosa Garcia', 'Barangay Kagawad - Education', 4, 'education@barangay.gov.ph', '+63 912 345 6782', 'Education Committee Head. Former teacher advocating for youth development.', TRUE),
('Jose Mendoza', 'Barangay Kagawad - Infrastructure', 5, 'infrastructure@barangay.gov.ph', '+63 912 345 6783', 'Infrastructure Committee Chairman. Civil engineer overseeing barangay projects.', TRUE),
('Ana Lopez', 'Barangay Secretary', 6, 'secretary@barangay.gov.ph', '+63 912 345 6784', 'Handles all administrative services and document processing.', TRUE),
('Carlos Rivera', 'Barangay Treasurer', 7, 'treasurer@barangay.gov.ph', '+63 912 345 6785', 'Manages barangay finances and budget allocation.', TRUE),
('Linda Cruz', 'SK Chairperson', 8, 'sk@barangay.gov.ph', '+63 912 345 6786', 'Sangguniang Kabataan Chairperson. Youth leader and student advocate.', TRUE);

-- =====================================================
-- SAMPLE DATA: Emergency Alerts
-- =====================================================

INSERT INTO emergency_alerts (title, message, severity, alert_type, is_active, expires_at) VALUES
('Heavy Rainfall Warning', 'Weather bureau advises heavy rainfall expected in the next 24-48 hours. Residents in low-lying areas should take precautionary measures. Prepare emergency kits and stay updated.', 'warning', 'Weather', TRUE, CURRENT_TIMESTAMP + INTERVAL '2 days'),
('Community Health Alert', 'Free health checkup and vaccination program this Saturday at the Barangay Hall from 8:00 AM to 12:00 PM. Bring your health cards. First come, first served.', 'info', 'Health', TRUE, CURRENT_TIMESTAMP + INTERVAL '7 days'),
('Road Closure Advisory', 'Main Street will be temporarily closed on November 15-16 for road repairs. Please use alternative routes. We apologize for the inconvenience.', 'warning', 'Infrastructure', TRUE, CURRENT_TIMESTAMP + INTERVAL '5 days');

-- =====================================================
-- SAMPLE DATA: Announcements
-- =====================================================

INSERT INTO announcements (title, content, category, priority, is_active) VALUES
('Quarterly Barangay Assembly', 'All residents are cordially invited to attend the quarterly barangay assembly on November 20, 2025, at 5:00 PM at the Barangay Hall. Your participation is highly encouraged as we discuss important community matters and upcoming projects.', 'Community', 'high', TRUE),
('Updated Garbage Collection Schedule', 'Effective next week, garbage collection schedule will be updated. Monday and Thursday for biodegradable waste, Wednesday and Saturday for non-biodegradable waste. Please segregate your waste properly to help keep our community clean.', 'Environment', 'normal', TRUE),
('Basketball League Registration Open', 'Registration for the Annual Barangay Basketball League is now open! Sign up at the Barangay Hall until November 25, 2025. Open to all male residents aged 18-40. Championship game will be held in December.', 'Sports', 'normal', TRUE),
('Senior Citizens Benefits', 'Senior citizens are reminded to claim your quarterly benefits on November 18, 2025. Bring your senior citizen ID and barangay clearance. Distribution will be from 8:00 AM to 3:00 PM at the Barangay Hall.', 'Social Services', 'high', TRUE),
('Barangay Cleanup Drive', 'Join us for a community cleanup drive this Saturday, November 16, 2025, starting at 6:00 AM. Meeting point at the Barangay Hall. Bring your own cleaning materials. Snacks and refreshments will be provided. Let''s keep our barangay clean and green!', 'Environment', 'normal', TRUE);

-- =====================================================
-- SAMPLE DATA: Events
-- =====================================================

INSERT INTO events (title, description, location, event_date, event_time, category, max_participants, is_active) VALUES
('Community Clean-Up Drive', 'Join us for a community-wide clean-up drive. Bring your own cleaning materials. Snacks and refreshments will be provided. Let''s work together to keep our barangay clean and beautiful!', 'Barangay Covered Court', '2025-11-20', '07:00:00', 'Environment', 100, TRUE),
('Free Medical Mission', 'Free medical consultation, blood pressure monitoring, blood sugar testing, and medicine distribution for senior citizens and PWDs. Bring your health cards and IDs. Limited slots available.', 'Barangay Health Center', '2025-11-22', '08:00:00', 'Health', 150, TRUE),
('Christmas Party 2025', 'Annual barangay Christmas party with games, prizes, raffle draws, and special performances. All residents are welcome! Free food and giveaways. Come and celebrate with your community!', 'Barangay Hall', '2025-12-20', '17:00:00', 'Festival', 500, TRUE),
('Disaster Preparedness Seminar', 'Learn essential disaster preparedness and first aid skills. Resource speakers from the local disaster risk reduction office. Free certificates will be given to attendees.', 'Barangay Function Hall', '2025-11-25', '13:00:00', 'Education', 80, TRUE),
('Youth Sports Fest', 'Inter-purok sports competition for the youth. Events include basketball, volleyball, badminton, and table tennis. Register your team now! Winning teams will receive trophies and medals.', 'Barangay Sports Complex', '2025-12-05', '08:00:00', 'Sports', 200, TRUE);

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Uncomment these to verify the schema was created correctly:
-- SELECT COUNT(*) as total_users FROM users;
-- SELECT COUNT(*) as total_officials FROM barangay_officials;
-- SELECT COUNT(*) as total_announcements FROM announcements;
-- SELECT COUNT(*) as total_events FROM events;
-- SELECT COUNT(*) as total_alerts FROM emergency_alerts;

-- =====================================================
-- SCHEMA CREATION COMPLETE!
-- =====================================================
