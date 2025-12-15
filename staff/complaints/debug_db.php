<?php
// Debug script for complaints
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug Complaints</h1>";

// Mimic dashboard.php includes
// FORCE LOCALHOST for debugging
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Try empty password for XAMPP default, or 'root' as user config suggested?
// User config said: define('DB_PASS', getenv('DB_PASS') ?: 'leyeco_pass');
// But forms/complaints/app/config.php said: define('DB_PASS', 'root');
// Let's try to assume common defaults. If user has 'leyeco_user' setup, I should use that if possible.
// Wait, config/database.php defaults to 'leyeco_user' / 'leyeco_pass'.
// forms/complaints/app/config.php defaults to 'root' / 'root'.

// Let's use the values from forms/complaints/app/config.php as a fallback if they are what's being used there.
// But better: let's NOT define them and see what happens if I fix the include order or path behavior.
// actually, if I can't connect, I can't debug.

require_once __DIR__ . '/../app/config.php'; // Load config for DB constants
require_once __DIR__ . '/../../forms/complaints/app/ComplaintController.php';

echo "<h2>Database Connection</h2>";
// Check if $conn exists (from config/database.php included via staff/app/config.php)
// Note: staff/app/config.php includes ../../config/database.php
global $conn;
if (isset($conn)) {
    echo "Global \$conn is set.<br>";
    if ($conn->connect_error) {
        echo "Connection Error: " . $conn->connect_error . "<br>";
    } else {
        echo "Connection Status: Connected<br>";
        echo "Host info: " . $conn->host_info . "<br>";
    }
} else {
    echo "Global \$conn is NOT set. Trying to create it manually...<br>";
    // Try manual connection if not set
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "Manual connection successful.<br>";
}

echo "<h2>Controller Test</h2>";
$controller = new ComplaintController();

echo "<h3>Statistics</h3>";
$stats = $controller->getStatistics();
echo "<pre>";
print_r($stats);
echo "</pre>";

echo "<h3>GetAll (Limit 5)</h3>";
$filters = [];
$page = 1;
$perPage = 5;
$result = $controller->getAll($filters, $page, $perPage);

echo "Total from getAll: " . ($result['total'] ?? 'undefined') . "<br>";
echo "Count of complaints: " . count($result['complaints'] ?? []) . "<br>";

if (empty($result['complaints'])) {
    echo "No complaints found in getAll.<br>";
} else {
    echo "<pre>";
    print_r($result['complaints']);
    echo "</pre>";
}

echo "<h3>Direct Query Test</h3>";
$sql = "SELECT * FROM complaints LIMIT 1";
$res = $conn->query($sql);
if ($res) {
    echo "Direct query successful. Rows: " . $res->num_rows . "<br>";
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        echo "First row ref: " . $row['reference_code'] . "<br>";
    }
} else {
    echo "Direct query failed: " . $conn->error . "<br>";
}

?>
