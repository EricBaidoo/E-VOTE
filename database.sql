-- E-Voting System Database Schema
-- This file creates all necessary tables

-- Create database
CREATE DATABASE IF NOT EXISTS evoting_system;
USE evoting_system;

-- Users Table (Admin and Voters)
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'voter') DEFAULT 'voter',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Voters Table
CREATE TABLE IF NOT EXISTS voters (
    voter_id VARCHAR(50) UNIQUE NOT NULL PRIMARY KEY,
    user_id INT,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(100),
    has_voted BOOLEAN DEFAULT FALSE,
    vote_time TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Positions Table
CREATE TABLE IF NOT EXISTS positions (
    position_id INT PRIMARY KEY AUTO_INCREMENT,
    position_name VARCHAR(100) NOT NULL,
    description TEXT,
    position_order INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Candidates Table
CREATE TABLE IF NOT EXISTS candidates (
    candidate_id INT PRIMARY KEY AUTO_INCREMENT,
    position_id INT NOT NULL,
    candidate_name VARCHAR(100) NOT NULL,
    party VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (position_id) REFERENCES positions(position_id) ON DELETE CASCADE
);

-- Votes Table
CREATE TABLE IF NOT EXISTS votes (
    vote_id INT PRIMARY KEY AUTO_INCREMENT,
    voter_id VARCHAR(50) NOT NULL,
    position_id INT NOT NULL,
    candidate_id INT NOT NULL,
    vote_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voter_id) REFERENCES voters(voter_id) ON DELETE CASCADE,
    FOREIGN KEY (position_id) REFERENCES positions(position_id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES candidates(candidate_id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (voter_id, position_id)
);

-- Create Indexes for better performance
CREATE INDEX idx_voter_user ON voters(user_id);
CREATE INDEX idx_vote_voter ON votes(voter_id);
CREATE INDEX idx_vote_position ON votes(position_id);
CREATE INDEX idx_vote_candidate ON votes(candidate_id);
CREATE INDEX idx_candidate_position ON candidates(position_id);

-- Insert default admin user
INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@evoting.com', '$2y$10$1N9aUgNWsDVVd18iPC2CbuI8CsAnVHWBx7qc8w.H6n9LewKsiMULm', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Insert sample positions
INSERT INTO positions (position_name, description, position_order) VALUES 
('President', 'Chief Executive of the Nation', 1),
('Vice President', 'Second in Command', 2),
('Senate', 'Legislative Body', 3)
ON DUPLICATE KEY UPDATE position_name=position_name;

-- Insert sample candidates
INSERT INTO candidates (position_id, candidate_name, party) 
SELECT 1, 'John Smith', 'Democratic Party' WHERE NOT EXISTS (SELECT 1 FROM candidates WHERE candidate_name='John Smith')
UNION ALL
SELECT 1, 'Sarah Johnson', 'Republican Party' WHERE NOT EXISTS (SELECT 1 FROM candidates WHERE candidate_name='Sarah Johnson')
UNION ALL
SELECT 1, 'Mike Brown', 'Independent' WHERE NOT EXISTS (SELECT 1 FROM candidates WHERE candidate_name='Mike Brown')
UNION ALL
SELECT 2, 'Emily Davis', 'Democratic Party' WHERE NOT EXISTS (SELECT 1 FROM candidates WHERE candidate_name='Emily Davis')
UNION ALL
SELECT 2, 'Robert Wilson', 'Republican Party' WHERE NOT EXISTS (SELECT 1 FROM candidates WHERE candidate_name='Robert Wilson')
UNION ALL
SELECT 3, 'Lisa Anderson', 'Democratic Party' WHERE NOT EXISTS (SELECT 1 FROM candidates WHERE candidate_name='Lisa Anderson')
UNION ALL
SELECT 3, 'James Taylor', 'Republican Party' WHERE NOT EXISTS (SELECT 1 FROM candidates WHERE candidate_name='James Taylor');

-- Insert sample voter
INSERT INTO voters (voter_id, first_name, last_name, email) 
VALUES ('VOTER_00000001', 'Test', 'Voter', 'voter@example.com')
ON DUPLICATE KEY UPDATE voter_id=voter_id;

-- Insert sample voter user
INSERT INTO users (username, email, password, role) 
VALUES ('voter1', 'voter1@example.com', '$2y$10$yRf7jtD9I5o.fXZ2J8K5veK5N1R8L9K7M6J5H4G3F2E1D0C9B8A7', 'voter')
ON DUPLICATE KEY UPDATE username=username;
