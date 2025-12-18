<?php
/**
 * LEYECO III Complaints System - Homepage
 */

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include main configuration for BASE_URL and other constants
require_once __DIR__ . '/../../../config/config.php';

// Include the complaints system configuration first
$configPath = realpath(__DIR__ . '/../app/config.php');
if (!file_exists($configPath)) {
    die('Complaints system configuration file not found. Please check your installation.');
}
require_once $configPath;

// Include the main database configuration
$dbPath = realpath(__DIR__ . '/../../../config/database.php');
if (!file_exists($dbPath)) {
    die('Database configuration file not found. Please check your installation.');
}
require_once $dbPath;

// Then include the controller
$controllerPath = realpath(__DIR__ . '/../app/ComplaintController.php');
if (!file_exists($controllerPath)) {
    die('ComplaintController not found. Please check your installation.');
}
require_once $controllerPath;

// Initialize variables
$stats = [
    'total' => 0,
    'by_status' => [
        'NEW' => 0,
        'INVESTIGATING' => 0,
        'RESOLVED' => 0
    ]
];

try {
    // Create controller instance
    $complaintController = new ComplaintController();
    
    // Get statistics
    $stats = $complaintController->getStatistics();
    
    // Ensure stats array has required keys
    if (!isset($stats['by_status'])) {
        $stats['by_status'] = [
            'NEW' => 0,
            'INVESTIGATING' => 0,
            'RESOLVED' => 0
        ];
    }
} catch (Exception $e) {
    error_log("Error in homepage.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    // Continue with default stats if there's an error
}
$flashMessage = getFlashMessage();

// Page configuration for main header
$page_title = 'Complaints System';
$additional_css = ['/forms/complaints/public/homepage.css'];

// Include main header
require_once __DIR__ . '/../../../includes/header.php';
?>

<?php if ($flashMessage): ?>
    <div class="alert alert-<?php echo e($flashMessage['type']); ?>">
        <?php echo e($flashMessage['message']); ?>
    </div>
<?php endif; ?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <main class="main">
        <section class="hero">
            <h2>File a Complaint</h2>
            <p>We value your feedback. Report service issues, billing concerns, or any complaints to help us serve you better.</p>
            
            <div class="hero-actions">
                <a href="submit_complaint.php" class="btn btn-primary btn-large">
                    📝 Submit New Complaint
                </a>
                <a href="#view-complaint" class="btn btn-secondary btn-large">
                    🔍 Track Your Complaint
                </a>
            </div>
        </section>

        <section class="stats">
            <h3>System Statistics</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total'] ?? 0); ?></div>
                    <div class="stat-label">Total Complaints</div>
                </div>
                <div class="stat-card stat-new">
                    <div class="stat-number"><?php echo number_format($stats['by_status']['NEW'] ?? 0); ?></div>
                    <div class="stat-label">New Complaints</div>
                </div>
                <div class="stat-card stat-investigating">
                    <div class="stat-number"><?php echo number_format($stats['by_status']['INVESTIGATING'] ?? 0); ?></div>
                    <div class="stat-label">Under Investigation</div>
                </div>
                <div class="stat-card stat-resolved">
                    <div class="stat-number"><?php echo number_format($stats['by_status']['RESOLVED'] ?? 0); ?></div>
                    <div class="stat-label">Resolved</div>
                </div>
            </div>
        </section>

        <section class="view-complaint" id="view-complaint">
            <h3>Track Your Complaint</h3>
            <p>Enter your reference code to view the status of your complaint</p>
            
            <form action="view_complaint.php" method="GET" class="search-form">
                <input 
                    type="text" 
                    name="ref" 
                    placeholder="Enter reference code (e.g., CLN20251211-0001)" 
                    required
                    pattern="CLN\d{8}-\d{4}"
                    class="search-input"
                >
                <button type="submit" class="btn btn-primary">View Complaint</button>
            </form>
        </section>

        <section class="info">
            <h3>How It Works</h3>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Submit Complaint</h4>
                    <p>Fill out the form with details about your complaint</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Get Reference Code</h4>
                    <p>Receive a unique tracking code for your complaint</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Track Progress</h4>
                    <p>Use your code to check the status anytime</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h4>Issue Resolved</h4>
                    <p>Our team works to resolve your complaint quickly</p>
                </div>
            </div>
        </section>
    </main>
</div>

<?php
// Include main footer
require_once __DIR__ . '/../../../includes/footer.php';
?>
