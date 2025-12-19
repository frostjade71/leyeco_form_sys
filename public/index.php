<?php
/**
 * Homepage - LEYECO III Forms Management System
 * Main landing page with form selection cards
 */

// Page configuration
$page_title = 'Home';
$additional_css = ['/assets/css/homepage.css'];
$additional_js = ['/assets/js/homepage.js'];

// Track page visit for analytics
try {
    require_once __DIR__ . '/../config/config.php';
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $stmt = $conn->prepare("INSERT INTO page_visits (page, ip_address, user_agent) VALUES (?, ?, ?)");
    $page_name = 'public/index.php';
    $stmt->bind_param('sss', $page_name, $ip_address, $user_agent);
    $stmt->execute();
    $stmt->close();
} catch (Exception $e) {
    // Silently fail - don't break the page if tracking fails
    error_log("Page visit tracking error: " . $e->getMessage());
}

// Include header
require_once __DIR__ . '/../includes/header.php';
?>


<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <main class="main">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-branding">
                <img src="<?php echo BASE_URL; ?>/assets/images/Leyecoicon.png" alt="LEYECO III Logo" class="hero-logo">
                <div class="hero-text">
                    <h2>LEYECO III</h2>
                    <p class="hero-subtitle">Forms Management System</p>
                </div>
            </div>
            <p class="hero-description">Welcome to the LEYECO III online forms portal. Select the appropriate form below to submit your request. All submissions are processed securely and you will receive a confirmation upon successful submission.</p>
            
            <div class="hero-actions">
                <a href="#select-forms" class="btn btn-primary btn-large">
                    📋 Select Forms
                </a>
                <a href="#instructions-section" class="btn btn-secondary btn-large">
                    📖 How It Works
                </a>
            </div>
        </section>

        <!-- Forms Selection Section -->
        <section id="select-forms" class="forms-selection">
            <h3>Select a Form</h3>
            <p class="section-description">Choose the form that matches your service request</p>
            
            <div class="forms-grid">
                <?php
                // Loop through form types from config
                $form_configs = [
                    [
                        'name' => 'Complaints Form',
                        'icon' => 'fa-exclamation-circle',
                        'description' => 'Report service issues or file complaints',
                        'url' => BASE_URL . '/forms/complaints_form.php',
                        'button_text' => 'File Complaint'
                    ],
                    [
                        'name' => 'Requisition Form',
                        'icon' => 'fa-file-alt',
                        'description' => 'Request for requisition of materials and such and such',
                        'url' => BASE_URL . '/forms/requisition_form.php',
                        'button_text' => 'Request Requisition'
                    ],
                    [
                        'name' => 'Service Request Form',
                        'icon' => 'fa-file-text',
                        'description' => 'Submit new service requests and connection applications',
                        'url' => BASE_URL . '/forms/request_form.php',
                        'button_text' => 'Start Request'
                    ],
                    [
                        'name' => 'Reconnection Form',
                        'icon' => 'fa-plug',
                        'description' => 'Request reconnection of electric service',
                        'url' => BASE_URL . '/forms/reconnection_form.php',
                        'button_text' => 'Request Reconnection'
                    ],
                    [
                        'name' => 'Meter Replacement Form',
                        'icon' => 'fa-bolt',
                        'description' => 'Request electric meter replacement or upgrade',
                        'url' => BASE_URL . '/forms/meter_replacement_form.php',
                        'button_text' => 'Request Replacement'
                    ]
                ];
                
                foreach ($form_configs as $form):
                ?>
                    <div class="form-card">
                        <div class="form-card-icon">
                            <i class="fas <?php echo clean_output($form['icon']); ?>"></i>
                        </div>
                        <h4><?php echo clean_output($form['name']); ?></h4>
                        <p><?php echo clean_output($form['description']); ?></p>
                        <a href="<?php echo clean_output($form['url']); ?>" class="btn btn-primary">
                            <?php echo clean_output($form['button_text']); ?> <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Instructions Section -->
        <section id="instructions-section" class="info">
            <h3>How to Submit a Form</h3>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Select Form Type</h4>
                    <p>Choose the appropriate form from the options above that matches your service request.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Fill Out Details</h4>
                    <p>Complete all required fields with accurate information. Ensure all details are correct.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Submit Request</h4>
                    <p>Review your information and submit. You'll receive a reference number for tracking.</p>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <h4>Track Progress</h4>
                    <p>Use your reference number to track the status of your request online anytime.</p>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats">
            <h3>System Statistics</h3>
            <p class="section-description">Real-time overview of our service</p>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">1,247</div>
                    <div class="stat-label">Forms Submitted</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">72</div>
                    <div class="stat-label">Average Processing Hours</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-number">40</div>
                    <div class="stat-label">Active Requests</div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <p style="color: var(--text-gray); font-size: 15px;">
                    <i class="fas fa-info-circle"></i> Need assistance? Contact us at +639173049794 or visit our office in Tunga, Leyte.
                </p>
            </div>
        </section>
    </main>
</div>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>

