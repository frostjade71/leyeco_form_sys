<?php
/**
 * LEYECO III Complaints System
 * Helper Functions
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Start session if not already started
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
    }
}

/**
 * Generate unique reference code
 * Format: CLN{year}{month}{day}-{4 digit number}
 */
function generateReferenceCode() {
    $db = getDB();
    $date = date('Ymd');
    $prefix = "CLN{$date}-";
    
    // Get the last reference code for today
    $stmt = $db->prepare("SELECT reference_code FROM complaints WHERE reference_code LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $result = $stmt->fetch();
    
    if ($result) {
        // Extract the sequence number and increment
        $lastCode = $result['reference_code'];
        $sequence = (int)substr($lastCode, -4) + 1;
    } else {
        $sequence = 1;
    }
    
    return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    startSession();
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken($token) {
    startSession();
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Upload photo with validation
 * Returns: ['success' => bool, 'path' => string|null, 'error' => string|null]
 */
function uploadPhoto($file) {
    // Check if file was uploaded
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => true, 'path' => null, 'error' => null];
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'path' => null, 'error' => 'File upload failed'];
    }
    
    // Validate file size
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return ['success' => false, 'path' => null, 'error' => 'File size exceeds 5MB limit'];
    }
    
    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
        return ['success' => false, 'path' => null, 'error' => 'Only JPG and PNG files are allowed'];
    }
    
    // Validate file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, UPLOAD_ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'path' => null, 'error' => 'Invalid file extension'];
    }
    
    // Create upload directory if it doesn't exist
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    // Generate unique filename
    $filename = uniqid('complaint_', true) . '.' . $extension;
    $filepath = UPLOAD_DIR . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => 'assets/uploads/' . $filename, 'error' => null];
    } else {
        return ['success' => false, 'path' => null, 'error' => 'Failed to save file'];
    }
}

/**
 * Require user to be logged in
 */
function requireLogin() {
    startSession();
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Require specific role
 */
if (!function_exists('requireRole')) {
    function requireRole($role) {
        startSession();
        requireLogin();
        
        if ($_SESSION['user_role'] !== $role && $_SESSION['user_role'] !== 'ADMIN') {
            http_response_code(403);
            die('Access denied');
        }
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

/**
 * Get current user data
 */
function getCurrentUser() {
    startSession();
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role']
    ];
}

/**
 * Log audit trail
 */
function logAudit($action, $details = null, $userId = null) {
    try {
        $db = getDB();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        
        if ($userId === null && isLoggedIn()) {
            $userId = $_SESSION['user_id'];
        }
        
        $stmt = $db->prepare("INSERT INTO complaint_audit_logs (action, details, user_id, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$action, $details, $userId, $ipAddress]);
    } catch (Exception $e) {
        error_log("Audit log error: " . $e->getMessage());
    }
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'M d, Y g:i A') {
    return date($format, strtotime($date));
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Display flash message
 */
function setFlashMessage($type, $message) {
    startSession();
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    startSession();
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Escape output for HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
