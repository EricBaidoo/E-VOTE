-- E-VOTE System Database Setup
-- Run this script in MySQL Workbench

-- Create database
DROP DATABASE IF EXISTS evote_db;
CREATE DATABASE evote_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE evote_db;

-- Create voters table
CREATE TABLE voters (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) DEFAULT '',
    phone VARCHAR(50) DEFAULT '',
    INDEX idx_name (name),
    INDEX idx_email (email),
    INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create admins table
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    pin VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create votes table
CREATE TABLE votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    voter_session_id VARCHAR(50) NOT NULL,
    position VARCHAR(255) NOT NULL,
    candidate_id INT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_voter (voter_session_id),
    INDEX idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin
INSERT INTO admins (name, pin) VALUES ('admin', 'admin123');

-- Verify tables created
SELECT 'Database setup complete!' as Status;
SELECT COUNT(*) as admin_count FROM admins;
