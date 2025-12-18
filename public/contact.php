<?php
/**
 * Contact Page - LEYECO III Forms Management System
 * Contact information and inquiry form
 */

// Page configuration
$page_title = 'Contact Us';
$additional_css = [
    '/assets/css/contact.css',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
];
$additional_js = [
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
    '/assets/js/contact-map.js'
];

// Include header
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <div class="hero-icon">
            <i class="fas fa-envelope"></i>
        </div>
        <h1>Contact Us</h1>
        <p class="subtitle">We're here to help and answer any questions you might have</p>
    </div>
</section>

<!-- Contact Information Section -->
<section class="contact-info-section">
    <div class="container">
        <h2 class="section-title">Get in Touch</h2>
        <p class="section-subtitle">Reach out to us through any of the following channels</p>
        
        <div class="contact-grid">
            <!-- Address -->
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3>Office Address</h3>
                <p>Brgy. San Roque, Tunga, Leyte</p>
                <p>Philippines</p>
            </div>

            <!-- Phone -->
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <h3>Hotline Numbers</h3>
                <p><strong>SMART:</strong> <a href="tel:+639285104998">+639285104998</a></p>
                <p><strong>GLOBE:</strong> <a href="tel:+639173049794">+639173049794</a></p>
            </div>

            <!-- Email -->
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>Email Address</h3>
                <p><a href="mailto:leyteiiie@yahoo.com">leyteiiie@yahoo.com</a></p>
            </div>

            <!-- Website -->
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <h3>Website</h3>
                <p><a href="https://www.leyeco3.com/" target="_blank">www.leyeco3.com</a></p>
            </div>

            <!-- Office Hours -->
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>Office Hours</h3>
                <p>Monday - Friday</p>
                <p>8:00 AM - 5:00 PM</p>
            </div>

            <!-- Social Media -->
            <div class="contact-card">
                <div class="contact-icon">
                    <i class="fas fa-share-alt"></i>
                </div>
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="https://www.facebook.com/leyeco3" target="_blank" title="Facebook">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://www.leyeco3.com/" target="_blank" title="Website">
                        <i class="fas fa-globe"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container">
        <h2 class="section-title">Find Us</h2>
        <p class="section-subtitle">Visit our office at Brgy. San Roque, Tunga, Leyte</p>
        
        <div class="map-container">
            <div id="contact-map" style="height: 350px; width: 100%; border-radius: 8px;"></div>
        </div>
    </div>
</section>

<!-- Quick Links Section -->
<section class="quick-links-section">
    <div class="container">
        <h2 class="section-title">Quick Actions</h2>
        <p class="section-subtitle">Need to submit a form or request?</p>
        
        <div class="quick-links-grid">
            <a href="<?php echo BASE_URL; ?>/forms/request_form.php" class="quick-link-card">
                <div class="quick-link-icon">
                    <i class="fas fa-file-text"></i>
                </div>
                <h3>Service Request</h3>
                <p>Submit a new service request</p>
            </a>

            <a href="<?php echo BASE_URL; ?>/forms/reconnection_form.php" class="quick-link-card">
                <div class="quick-link-icon">
                    <i class="fas fa-plug"></i>
                </div>
                <h3>Reconnection</h3>
                <p>Request reconnection service</p>
            </a>

            <a href="<?php echo BASE_URL; ?>/forms/complaints_form.php" class="quick-link-card">
                <div class="quick-link-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <h3>File Complaint</h3>
                <p>Report service issues</p>
            </a>

            <a href="<?php echo BASE_URL; ?>/forms/meter_replacement_form.php" class="quick-link-card">
                <div class="quick-link-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Meter Replacement</h3>
                <p>Request meter replacement</p>
            </a>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        
        <div class="faq-grid">
            <div class="faq-item">
                <h3><i class="fas fa-question-circle"></i> How do I submit a service request?</h3>
                <p>You can submit a service request by clicking on the appropriate form from our homepage or the Quick Actions section above. Fill out the required information and submit the form online.</p>
            </div>

            <div class="faq-item">
                <h3><i class="fas fa-question-circle"></i> How long does it take to process my request?</h3>
                <p>Processing time varies depending on the type of request. Typically, requests are processed within 24-48 hours. You will receive a reference number to track your request status.</p>
            </div>

            <div class="faq-item">
                <h3><i class="fas fa-question-circle"></i> What are your office hours?</h3>
                <p>Our office is open Monday through Friday, from 8:00 AM to 5:00 PM. We are closed on weekends and public holidays.</p>
            </div>

            <div class="faq-item">
                <h3><i class="fas fa-question-circle"></i> How can I report a power outage?</h3>
                <p>You can report power outages by calling our hotline numbers: SMART +639285104998 or GLOBE +639173049794. You can also file a complaint through our online form.</p>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>
