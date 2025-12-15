<?php
/**
 * Database Configuration
 * LEYECO III Forms Management System
 * 
 * This file handles the database connection using MySQLi.
 * Modify the constants below to match your database credentials.
 */

// Set PHP timezone to Philippine Standard Time
date_default_timezone_set('Asia/Manila');

// Database Configuration Constants
// For Docker environment, use 'db' as host (service name in docker-compose)
// For local XAMPP, use 'localhost'
// Only define if not already defined
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: 'db');
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: 'leyeco_user');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('DB_PASS') ?: 'leyeco_pass');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('DB_NAME') ?: 'leyeco_forms_db');
}

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Set charset to UTF-8 for proper character encoding
$conn->set_charset("utf8mb4");

// Set database timezone to Philippine Time (+08:00)
$conn->query("SET time_zone = '+08:00'");

/**
 * Future Database Schema Structure (For Reference)
 * 
 * TABLE: users
 * - id (INT, PRIMARY KEY, AUTO_INCREMENT)
 * - username (VARCHAR(50), UNIQUE)
 * - password (VARCHAR(255), hashed)
 * - email (VARCHAR(100))
 * - full_name (VARCHAR(100))
 * - role (ENUM: 'admin', 'staff', 'user')
 * - created_at (TIMESTAMP)
 * - updated_at (TIMESTAMP)
 * 
 * TABLE: form_types
 * - id (INT, PRIMARY KEY, AUTO_INCREMENT)
 * - form_name (VARCHAR(100))
 * - form_slug (VARCHAR(50), UNIQUE)
 * - description (TEXT)
 * - is_active (BOOLEAN)
 * - created_at (TIMESTAMP)
 * 
 * TABLE: form_submissions
 * - id (INT, PRIMARY KEY, AUTO_INCREMENT)
 * - form_type_id (INT, FOREIGN KEY -> form_types.id)
 * - user_id (INT, FOREIGN KEY -> users.id, NULL for guest submissions)
 * - reference_number (VARCHAR(20), UNIQUE)
 * - status (ENUM: 'pending', 'processing', 'approved', 'rejected', 'completed')
 * - form_data (JSON, stores all form field values)
 * - submitted_at (TIMESTAMP)
 * - updated_at (TIMESTAMP)
 * - processed_by (INT, FOREIGN KEY -> users.id, NULL)
 * - notes (TEXT)
 */
?>
