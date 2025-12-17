<?php
/**
 * Requisition System Configuration
 * System-specific constants and settings
 */

// Departments
define('REQ_DEPARTMENTS', [
    'Finance Services Department',
    'Institutional Services Department',
    'Technical Services Department',
    'Office of the General Manager'
]);

// Units of Measurement
define('REQ_UNITS', [
    'pcs',
    'kg',
    'meters',
    'liters',
    'boxes',
    'rolls',
    'sets',
    'pairs',
    'units'
]);

// Request Status
define('REQ_STATUS_PENDING', 'pending');
define('REQ_STATUS_APPROVED', 'approved');
define('REQ_STATUS_REJECTED', 'rejected');
define('REQ_STATUS_COMPLETED', 'completed');

// Approval Levels
define('REQ_APPROVAL_LEVELS', [
    1 => 'Recommending Approval - Section Head/Div. Head/Department Head',
    2 => 'Inventory Checked - Warehouse Section Head',
    3 => 'Budget Approval - Div. Supervisor/Budget Officer',
    4 => 'Checked By - Internal Auditor',
    5 => 'Approved By - General Manager'
]);

// Flash Message Helper
function setFlashMessage($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// HTML Escape Helper
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
