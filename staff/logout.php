<?php
/**
 * LEYECO III Forms Management System
 * Staff Logout
 */

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/AuthController.php';

$auth = new AuthController();
$auth->logout();

// Redirect to login with success message
header('Location: ' . STAFF_URL . '/login.php?logout=success');
exit;
?>
