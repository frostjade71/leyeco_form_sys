<?php
/**
 * LEYECO III Forms Management System
 * Authentication Middleware
 * 
 * Include this file at the top of any protected staff page
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/AuthController.php';

$auth = new AuthController();

// Clean expired sessions periodically (1% chance)
if (rand(1, 100) === 1) {
    $auth->cleanExpiredSessions();
}

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    // Store intended destination
    $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to login
    header('Location: ' . STAFF_URL . '/login.php');
    exit;
}

// Make auth and current user available globally
$currentUser = $auth->getCurrentUser();

/**
 * Require admin role
 */
function requireAdmin() {
    global $auth;
    if (!$auth->isAdmin()) {
        http_response_code(403);
        die('Access denied. Admin privileges required.');
    }
}

/**
 * Require specific role
 */
function requireRole($role) {
    global $auth;
    if (!$auth->hasRole($role)) {
        http_response_code(403);
        die('Access denied. Insufficient privileges.');
    }
}
?>
