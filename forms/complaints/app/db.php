<?php
/**
 * LEYECO III Complaints System
 * Database Connection Handler
 */

require_once __DIR__ . '/config.php';

/**
 * Global MySQLi connection for ComplaintController
 */
$conn = null;
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        error_log("MySQLi connection error: " . $conn->connect_error);
        die("Database connection failed. Please try again later.");
    }
    
    // Set charset
    $conn->set_charset(DB_CHARSET);
} catch (Exception $e) {
    error_log("MySQLi connection error: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

/**
 * Get database connection (PDO)
 * Returns PDO instance with error mode set to exceptions
 */
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $db = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }
    
    return $db;
}

/**
 * Test database connection
 */
function testDBConnection() {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT 1");
        return true;
    } catch (Exception $e) {
        error_log("Database test failed: " . $e->getMessage());
        return false;
    }
}
