<?php
/**
 * Homepage - LEYECO III Forms Management System
 * Main landing page with form selection cards
 */

// Page configuration
$page_title = 'Home';
$additional_css = ['/assets/css/homepage.css'];
$additional_js = ['/assets/js/homepage.js'];

// Include header
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <div class="hero-icon">
            <i class="fas fa-file-alt"></i>
        </div>
        <h1>LEYECO III Forms Management System</h1>
        <p class="subtitle">Submit and manage your service requests online</p>
        <p class="description">
            Welcome to the LEYECO III online forms portal. Select the appropriate form below to submit your request. 
            All submissions are processed securely and you will receive a confirmation upon successful submission.
        </p>
    </div>
</section>

<!-- Forms Selection Section -->
<section class="forms-section">
    <div class="container">
        <h2 class="section-title">Select a Form</h2>
        <p class="section-subtitle">Choose the form that matches your service request</p>
        
        <div class="forms-grid">
            <?php
            // Loop through form types from config
            $form_configs = [
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
                    'name' => 'Complaints Form',
                    'icon' => 'fa-exclamation-circle',
                    'description' => 'Report service issues or file complaints',
                    'url' => BASE_URL . '/forms/complaints_form.php',
                    'button_text' => 'File Complaint'
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
                    <h3><?php echo clean_output($form['name']); ?></h3>
                    <p><?php echo clean_output($form['description']); ?></p>
                    <a href="<?php echo clean_output($form['url']); ?>" class="btn">
                        <?php echo clean_output($form['button_text']); ?> <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Instructions Section -->
<section class="instructions-section">
    <div class="container">
        <h2 class="section-title">How to Submit a Form</h2>
        <p class="section-subtitle">Follow these simple steps to complete your request</p>
        
        <div class="instructions-grid">
            <div class="instruction-card">
                <div class="instruction-number">1</div>
                <h3>Select Form Type</h3>
                <p>Choose the appropriate form from the options above that matches your service request.</p>
            </div>
            
            <div class="instruction-card">
                <div class="instruction-number">2</div>
                <h3>Fill Out Details</h3>
                <p>Complete all required fields with accurate information. Ensure all details are correct.</p>
            </div>
            
            <div class="instruction-card">
                <div class="instruction-number">3</div>
                <h3>Submit Request</h3>
                <p>Review your information and submit. You'll receive a reference number for tracking.</p>
            </div>
            
            <div class="instruction-card">
                <div class="instruction-number">4</div>
                <h3>Track Progress</h3>
                <p>Use your reference number to track the status of your request online anytime.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <h2 class="section-title" style="color: var(--white);">System Statistics</h2>
        <p class="section-subtitle" style="color: rgba(255, 255, 255, 0.9);">Real-time overview of our service</p>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">1,247</div>
                <div class="stat-label">Forms Submitted</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number">24</div>
                <div class="stat-label">Average Processing Hours</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number">156</div>
                <div class="stat-label">Active Requests</div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: var(--spacing-xl);">
            <p style="color: rgba(255, 255, 255, 0.8); font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> Need assistance? Contact us at +639173049794 or visit our office in Tunga, Leyte.
            </p>
        </div>
    </div>
</section>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>
