-- LEYECO III Complaints System
-- Database Schema

-- Complaints Table
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_code VARCHAR(20) UNIQUE NOT NULL,
    reporter_name VARCHAR(255) DEFAULT NULL,
    contact VARCHAR(255) DEFAULT NULL,
    description TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    municipality VARCHAR(100) NOT NULL,
    barangay VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    lat DECIMAL(10, 8) DEFAULT NULL,
    lon DECIMAL(11, 8) DEFAULT NULL,
    photo_path VARCHAR(255) DEFAULT NULL,
    status VARCHAR(50) DEFAULT 'NEW',
    assigned_to INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_reference_code (reference_code),
    INDEX idx_status (status),
    INDEX idx_municipality (municipality),
    INDEX idx_barangay (barangay),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Complaint Comments Table
CREATE TABLE IF NOT EXISTS complaint_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE,
    INDEX idx_complaint_id (complaint_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Complaint Audit Logs Table
CREATE TABLE IF NOT EXISTS complaint_audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(100) NOT NULL,
    details TEXT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_action (action),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
