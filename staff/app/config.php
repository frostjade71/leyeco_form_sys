<?php
/**
 * Staff Dashboard Configuration
 * LEYECO III Forms Management System
 */

// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

// Include main database configuration
require_once __DIR__ . '/../../config/database.php';

// Session Configuration
if (!defined('SESSION_LIFETIME')) {
    define('SESSION_LIFETIME', 3600 * 8); // 8 hours
}
if (!defined('SESSION_NAME')) {
    define('SESSION_NAME', 'LEYECO_STAFF_SESSION');
}

// Path Constants
if (!defined('STAFF_ROOT')) {
    define('STAFF_ROOT', __DIR__ . '/..');
}
if (!defined('STAFF_URL')) {
    define('STAFF_URL', '/staff');
}

// Pagination
if (!defined('ITEMS_PER_PAGE')) {
    define('ITEMS_PER_PAGE', 20);
}

// CSRF Token Configuration
if (!defined('CSRF_TOKEN_NAME')) {
    define('CSRF_TOKEN_NAME', 'csrf_token');
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}
?>
