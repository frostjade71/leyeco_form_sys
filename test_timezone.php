<?php
/**
 * Test timezone configuration
 */

require_once __DIR__ . '/config/database.php';

echo "<h2>Timezone Configuration Test</h2>";
echo "<style>body { font-family: Arial; padding: 20px; } .success { color: green; } .info { color: blue; }</style>";

// Check PHP timezone
echo "<h3>PHP Timezone</h3>";
echo "<p class='info'>Configured timezone: <strong>" . date_default_timezone_get() . "</strong></p>";
echo "<p class='info'>Current PHP time: <strong>" . date('Y-m-d H:i:s') . "</strong></p>";

// Check database timezone
echo "<h3>Database Timezone</h3>";
$result = $conn->query("SELECT @@session.time_zone as session_tz, NOW() as db_time");
$row = $result->fetch_assoc();
echo "<p class='info'>Database session timezone: <strong>" . $row['session_tz'] . "</strong></p>";
echo "<p class='info'>Database current time: <strong>" . $row['db_time'] . "</strong></p>";

// Check if they match
echo "<h3>Verification</h3>";
$phpTime = new DateTime('now', new DateTimeZone('Asia/Manila'));
$dbTime = new DateTime($row['db_time']);
$diff = $phpTime->getTimestamp() - $dbTime->getTimestamp();

if (abs($diff) < 60) {
    echo "<p class='success'>✓ PHP and Database times are synchronized (within 1 minute)</p>";
} else {
    echo "<p style='color: red;'>✗ PHP and Database times differ by " . abs($diff) . " seconds</p>";
}

// Test complaint creation timestamp
echo "<h3>Sample Complaint Timestamp</h3>";
$result = $conn->query("SELECT reference_code, created_at FROM complaints ORDER BY created_at DESC LIMIT 1");
if ($result && $result->num_rows > 0) {
    $complaint = $result->fetch_assoc();
    echo "<p class='info'>Latest complaint: <strong>" . $complaint['reference_code'] . "</strong></p>";
    echo "<p class='info'>Created at: <strong>" . $complaint['created_at'] . "</strong></p>";
    
    $createdTime = new DateTime($complaint['created_at']);
    echo "<p class='info'>Formatted: <strong>" . $createdTime->format('F d, Y h:i:s A') . "</strong></p>";
} else {
    echo "<p>No complaints found</p>";
}
?>
