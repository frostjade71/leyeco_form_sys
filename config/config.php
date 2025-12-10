<?php
/**
 * System Configuration
 * LEYECO III Forms Management System
 * 
 * This file contains system-wide constants and configuration settings.
 */

// Start session for user tracking
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL Configuration
// Adjust this based on your server setup
// For Docker: Empty string (assets are relative)
// For XAMPP: /leyeco_form_sys
define('BASE_URL', getenv('BASE_URL') ?: '');

// Site Information
define('SITE_NAME', 'LEYECO III Forms Management System');
define('SITE_TAGLINE', 'Lighting Houses, Lighting Homes, Lighting Hopes');
define('SYSTEM_VERSION', '1.0.0');

// Timezone Configuration (Philippine Time)
date_default_timezone_set('Asia/Manila');

// Color Scheme Constants (for reference in PHP)
define('PRIMARY_RED', '#DC2626');
define('PRIMARY_RED_DARK', '#991B1B');
define('ACCENT_YELLOW', '#FBBF24');
define('WHITE', '#FFFFFF');
define('OFF_WHITE', '#FAFAFA');
define('LIGHT_GRAY', '#F3F4F6');
define('TEXT_DARK', '#1F2937');
define('TEXT_GRAY', '#6B7280');
define('BORDER_LIGHT', '#E5E7EB');

// Form Types Configuration
define('FORM_TYPES', [
    'request' => [
        'name' => 'Service Request Form',
        'slug' => 'request_form',
        'icon' => 'fa-file-text',
        'description' => 'Submit new service requests and connection applications'
    ],
    'reconnection' => [
        'name' => 'Reconnection Form',
        'slug' => 'reconnection_form',
        'icon' => 'fa-plug',
        'description' => 'Request reconnection of electric service'
    ],
    'complaints' => [
        'name' => 'Complaints Form',
        'slug' => 'complaints_form',
        'icon' => 'fa-exclamation-circle',
        'description' => 'Report service issues or file complaints'
    ],
    'meter_replacement' => [
        'name' => 'Meter Replacement Form',
        'slug' => 'meter_replacement_form',
        'icon' => 'fa-bolt',
        'description' => 'Request electric meter replacement or upgrade'
    ]
]);

// Include database configuration
require_once __DIR__ . '/database.php';

// Error Handling Configuration
// Set to false in production
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Security: XSS Protection Helper Function
function clean_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Helper function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Helper function to get current user
function get_logged_in_user() {
    if (is_logged_in()) {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? null
        ];
    }
    return null;
}
?>
