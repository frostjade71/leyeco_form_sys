<?php
/**
 * Debug BASE_URL Detection
 * Temporary file to check what BASE_URL is being detected
 */

require_once __DIR__ . '/config/config.php';

echo "<h1>BASE_URL Debug Information</h1>";
echo "<pre>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'NOT SET') . "\n";
echo "\n";
echo "Detected BASE_URL: '" . BASE_URL . "'\n";
echo "STAFF_URL: " . (defined('STAFF_URL') ? STAFF_URL : 'NOT DEFINED') . "\n";
echo "</pre>";

echo "<h2>Test Links</h2>";
echo "<ul>";
echo "<li><a href='" . BASE_URL . "/'>Home</a></li>";
echo "<li><a href='" . BASE_URL . "/staff/dashboard.php'>Staff Dashboard</a></li>";
echo "<li><a href='" . BASE_URL . "/staff/requisition/dashboard.php'>Requisition Dashboard</a></li>";
echo "</ul>";
?>
