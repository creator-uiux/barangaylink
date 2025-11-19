-- BarangayLink SQLite Database Schema
-- COMPLETE FIXED VERSION - ALL ISSUES RESOLVED!
-- Run this file to create the entire SQLite database

-- =====================================================
-- DATABASE CREATION
-- =====================================================

-- =====================================================
-- TABLE: users
-- Stores all user accounts (residents, officials, admin)
-- =====================================================

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  first_name TEXT NOT NULL,
  middle_name TEXT DEFAULT NULL,
  last_name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password TEXT NOT NULL,
  address TEXT DEFAULT NULL,
  phone TEXT DEFAULT NULL,
  role TEXT NOT NULL DEFAULT 'resident' CHECK (role IN ('resident','official','admin')),
  is_active INTEGER NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL
);

-- =====================================================
-- TABLE: documents
-- Stores document requests from residents
-- =====================================================

CREATE TABLE IF NOT EXISTS documents (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  document_type TEXT NOT NULL,
  purpose TEXT NOT NULL,
  quantity INTEGER NOT NULL DEFAULT 1,
  notes TEXT DEFAULT NULL,
  status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','processing','approved','rejected','ready','claimed')),
  admin_notes TEXT DEFAULT NULL,
  processed_by INTEGER DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (processed_by) REFERENCES users (id) ON DELETE SET NULL
);

-- =====================================================
-- TABLE: concerns
-- Stores community concerns and reports
-- =====================================================

CREATE TABLE IF NOT EXISTS concerns (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  category TEXT NOT NULL,
  subject TEXT NOT NULL,
  description TEXT NOT NULL,
  location TEXT DEFAULT NULL,
  status TEXT NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','in-progress','resolved','rejected','closed')),
  priority TEXT NOT NULL DEFAULT 'normal' CHECK (priority IN ('low','normal','high','urgent')),
  admin_response TEXT DEFAULT NULL,
  resolved_by INTEGER DEFAULT NULL,
  resolved_at DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  FOREIGN KEY (resolved_by) REFERENCES users (id) ON DELETE SET NULL
);

-- =====================================================
-- TABLE: announcements
-- Stores barangay announcements
-- =====================================================

CREATE TABLE IF NOT EXISTS announcements (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  content TEXT NOT NULL,
  category TEXT DEFAULT 'General',
  priority TEXT NOT NULL DEFAULT 'normal' CHECK (priority IN ('low','normal','high','urgent')),
  is_active INTEGER NOT NULL DEFAULT 1,
  created_by INTEGER DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
);

-- =====================================================
-- TABLE: events
-- Stores barangay events and activities
-- =====================================================

CREATE TABLE IF NOT EXISTS events (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  location TEXT DEFAULT NULL,
  event_date DATE NOT NULL,
  event_time TIME DEFAULT NULL,
  end_date DATE DEFAULT NULL,
  end_time TIME DEFAULT NULL,
  category TEXT DEFAULT 'Community',
  max_participants INTEGER DEFAULT NULL,
  current_participants INTEGER NOT NULL DEFAULT 0,
  is_active INTEGER NOT NULL DEFAULT 1,
  created_by INTEGER DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
);

-- =====================================================
-- TABLE: emergency_alerts
-- Stores emergency alerts and warnings
-- =====================================================

CREATE TABLE IF NOT EXISTS emergency_alerts (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  message TEXT NOT NULL,
  severity TEXT NOT NULL DEFAULT 'info' CHECK (severity IN ('info','warning','danger','critical')),
  alert_type TEXT DEFAULT 'General',
  is_active INTEGER NOT NULL DEFAULT 1,
  created_by INTEGER DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  expires_at DATETIME DEFAULT NULL,
  FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL
);

-- =====================================================
-- TABLE: notifications
-- Stores user notifications
-- =====================================================

CREATE TABLE IF NOT EXISTS notifications (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  type TEXT NOT NULL DEFAULT 'info',
  title TEXT NOT NULL,
  message TEXT NOT NULL,
  is_read INTEGER NOT NULL DEFAULT 0,
  related_type TEXT DEFAULT NULL,
  related_id INTEGER DEFAULT NULL,
  action_url TEXT DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
);

-- =====================================================
-- TABLE: barangay_officials
-- Stores barangay officials directory
-- =====================================================

CREATE TABLE IF NOT EXISTS barangay_officials (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  position TEXT NOT NULL,
  position_order INTEGER NOT NULL DEFAULT 0,
  email TEXT DEFAULT NULL,
  phone TEXT DEFAULT NULL,
  photo_url TEXT DEFAULT NULL,
  description TEXT DEFAULT NULL,
  is_active INTEGER NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL
);

-- =====================================================
-- INDEXES FOR PERFORMANCE
-- =====================================================

CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_is_active ON users(is_active);

CREATE INDEX IF NOT EXISTS idx_documents_user_id ON documents(user_id);
CREATE INDEX IF NOT EXISTS idx_documents_status ON documents(status);
CREATE INDEX IF NOT EXISTS idx_documents_created_at ON documents(created_at);
CREATE INDEX IF NOT EXISTS idx_documents_processed_by ON documents(processed_by);
CREATE INDEX IF NOT EXISTS idx_documents_user_status ON documents(user_id, status);

CREATE INDEX IF NOT EXISTS idx_concerns_user_id ON concerns(user_id);
CREATE INDEX IF NOT EXISTS idx_concerns_status ON concerns(status);
CREATE INDEX IF NOT EXISTS idx_concerns_category ON concerns(category);
CREATE INDEX IF NOT EXISTS idx_concerns_priority ON concerns(priority);
CREATE INDEX IF NOT EXISTS idx_concerns_created_at ON concerns(created_at);
CREATE INDEX IF NOT EXISTS idx_concerns_resolved_by ON concerns(resolved_by);
CREATE INDEX IF NOT EXISTS idx_concerns_user_status ON concerns(user_id, status);

CREATE INDEX IF NOT EXISTS idx_announcements_created_at ON announcements(created_at);
CREATE INDEX IF NOT EXISTS idx_announcements_is_active ON announcements(is_active);
CREATE INDEX IF NOT EXISTS idx_announcements_priority ON announcements(priority);
CREATE INDEX IF NOT EXISTS idx_announcements_category ON announcements(category);
CREATE INDEX IF NOT EXISTS idx_announcements_created_by ON announcements(created_by);

CREATE INDEX IF NOT EXISTS idx_events_event_date ON events(event_date);
CREATE INDEX IF NOT EXISTS idx_events_created_at ON events(created_at);
CREATE INDEX IF NOT EXISTS idx_events_is_active ON events(is_active);
CREATE INDEX IF NOT EXISTS idx_events_category ON events(category);
CREATE INDEX IF NOT EXISTS idx_events_created_by ON events(created_by);

CREATE INDEX IF NOT EXISTS idx_emergency_alerts_severity ON emergency_alerts(severity);
CREATE INDEX IF NOT EXISTS idx_emergency_alerts_is_active ON emergency_alerts(is_active);
CREATE INDEX IF NOT EXISTS idx_emergency_alerts_created_at ON emergency_alerts(created_at);
CREATE INDEX IF NOT EXISTS idx_emergency_alerts_expires_at ON emergency_alerts(expires_at);
CREATE INDEX IF NOT EXISTS idx_emergency_alerts_created_by ON emergency_alerts(created_by);

CREATE INDEX IF NOT EXISTS idx_notifications_user_id ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_is_read ON notifications(is_read);
CREATE INDEX IF NOT EXISTS idx_notifications_created_at ON notifications(created_at);
CREATE INDEX IF NOT EXISTS idx_notifications_type ON notifications(type);
CREATE INDEX IF NOT EXISTS idx_notifications_user_read ON notifications(user_id, is_read);

CREATE INDEX IF NOT EXISTS idx_barangay_officials_position_order ON barangay_officials(position_order);
CREATE INDEX IF NOT EXISTS idx_barangay_officials_is_active ON barangay_officials(is_active);

-- =====================================================
-- DEFAULT ACCOUNTS
-- =====================================================

-- Admin Account (password: admin@password.com)
INSERT OR IGNORE INTO users (first_name, middle_name, last_name, email, password, address, phone, role, is_active) VALUES
('Admin', NULL, 'User', 'admin@email.com', '$2y$12$QGmKI5OXwRl6nC8rmHJd.OEdcQtrnGQtq/P.JyJ.6YliuTeW0QQfO', 'Barangay Hall, Main Street', '+63 912 345 6789', 'admin', 1);

-- Resident Account (password: resident@password.com)
INSERT OR IGNORE INTO users (first_name, middle_name, last_name, email, password, address, phone, role, is_active) VALUES
('John', 'D', 'Doe', 'resident@email.com', '$2y$12$vCwbFGWTmqlWteR1xfJaI.oisQdYfHWNbNz8kHwYsNguLTSdrRJ5S', '123 Sample Street, Barangay', '+63 987 654 3210', 'resident', 1);

-- =====================================================
-- SCHEMA CREATION COMPLETE!
-- =====================================================
