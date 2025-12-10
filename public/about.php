<?php
/**
 * About Page - LEYECO III Forms Management System
 * Information about LEYECO III
 */

// Page configuration
$page_title = 'About Us';
$additional_css = ['/assets/css/about.css'];

// Include header
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <div class="hero-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <h1>About LEYECO III</h1>
        <p class="subtitle">Lighting Houses, Lighting Homes, Lighting Hopes</p>
    </div>
</section>

<!-- ISO Certification Section -->
<section class="iso-section">
    <div class="container">
        <h2 class="iso-title">The First ISO 9001:2015 QMS Electric Coop in the Visayas</h2>
        
        <div class="iso-content">
            <!-- Certificate Image -->
            <div class="iso-certificate">
                <img src="/assets/images/NQA.avif" alt="ISO 9001:2015 Certificate" class="certificate-image">
            </div>
            
            <!-- Description Text -->
            <div class="iso-description">
                <div class="iso-badge">
                    <i class="fas fa-certificate"></i>
                </div>
                <p class="lead-text">
                    LEYECO III is the first electric cooperative in the Visayas and second in the whole country 
                    that acquired and sealed with ISO 9001:2015 QMS with zero Major Non-Conformance and zero 
                    minor Non-Conformance. It adheres not only to the national standard in quality management 
                    system but more so in the international standards.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Vision, Mission, Quality Policy Section -->
<section class="vmq-section">
    <div class="container">
        <div class="vmq-grid">
            <!-- Vision -->
            <div class="vmq-card">
                <div class="vmq-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>Vision</h3>
                <p>The top performing distribution utility in the Visayas by 2030.</p>
            </div>

            <!-- Mission -->
            <div class="vmq-card">
                <div class="vmq-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3>Mission</h3>
                <p>Total electrification and efficient delivery of power at reasonable costs and responsive to the needs of Member-Consumers.</p>
            </div>

            <!-- Slogan -->
            <div class="vmq-card">
                <div class="vmq-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3>Slogan</h3>
                <p class="slogan">
                    Lighting Houses,<br>
                    Lighting Homes,<br>
                    Lighting Hopes
                </p>
            </div>
        </div>

        <!-- Quality Policy -->
        <div class="quality-policy">
            <h3><i class="fas fa-award"></i> Quality Policy</h3>
            <p>
                We, the vanguards of light in LEYTE III ELECTRIC COOPERATIVE, INC. are committed to provide 
                efficient and reliable electric service to all our Member-Consumer-Owners (MCOs) at all times. 
                We are dedicated to satisfy customers' demand and consistently comply with all the statutory 
                and regulatory requirements. We aim to continuously improve and develop our Quality Management 
                System in order to be the:
            </p>
            <div class="leyeco-acronym">
                <p><strong>L</strong>eading and top performing</p>
                <p><strong>E</strong>lectric Cooperative in every</p>
                <p><strong>Y</strong>ear by sustaining</p>
                <p><strong>E</strong>xcellence and</p>
                <p><strong>C</strong>onstantly employing quality service to</p>
                <p><strong>O</strong>utstand among other competitors</p>
            </div>
        </div>
    </div>
</section>

<!-- History Section -->
<section class="history-section">
    <div class="container">
        <h2><i class="fas fa-history"></i> Brief History</h2>
        
        <div class="history-content">
            <p>
                The Leyte III Electric Cooperative, Inc. (LEYECO III) was duly organized on <strong>October 30, 1975</strong>. 
                It was during this time when the Articles of Incorporation was made and signed at Jaro Elementary School, 
                Jaro, Leyte. Upon its registration and incorporation with the National Electrification Administration (NEA) 
                an initial loan was granted amounting to 14.6 million to finance the construction and operation of LEYECO III.
            </p>

            <p>
                Its service started on <strong>April 1, 1977</strong> with only 372 residential, 9 commercial, 1 industrial, 
                and 6 public building consumers at its rented temporary office in Alangalang, Leyte. It initially served the 
                two (2) taken over municipalities of Pastrana and Sta. Fe from DORELCO while constructing the backbone system 
                from Sta. Fe down to other municipalities.
            </p>

            <h3>Energization Timeline</h3>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-date">June 17, 1977</div>
                    <div class="timeline-content">First energization at municipality of Alangalang</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">September 28, 1977</div>
                    <div class="timeline-content">Energization of San Miguel, Leyte</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">September 19, 1979</div>
                    <div class="timeline-content">Energization of Jaro, Leyte</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">November 14, 1979</div>
                    <div class="timeline-content">Energization of Tunga, Leyte</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">December 22, 1979</div>
                    <div class="timeline-content">Energization of Carigara, Leyte</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">May 18, 1980</div>
                    <div class="timeline-content">Energization of Barugo, Leyte</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">June 4, 1980</div>
                    <div class="timeline-content">Energization of Capoocan, Leyte and completion of backbone line construction</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">April 8, 1982</div>
                    <div class="timeline-content">LEYECO III tapped to NPC Power Barge at Isabel, Leyte</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">March 8, 1983</div>
                    <div class="timeline-content">Tapped to NPC Tongonan Geothermal Plant, Kananga, Leyte</div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">February 25, 1978</div>
                    <div class="timeline-content">First Annual General Membership Assembly (AGMA) at Agro-Industrial School, Alangalang</div>
                </div>
            </div>

            <p class="history-conclusion">
                Amidst the desolation, setbacks and transition phases, LEYECO III has stood strong, and has expanded 
                its perspective of lighting houses, lighting homes, and lighting hopes. Energizing the nine (9) municipalities, 
                two-hundred eighty-five (285) barangays and forty-thousand one hundred sixty (40,160) households. With the 
                selfless and unending support and care of its member-consumers, employees and staff, LEYECO III will even 
                soar higher and will remain true to its objectives of giving light to each and every home.
            </p>
        </div>
    </div>
</section>

<!-- Coverage Section -->
<section class="coverage-section">
    <div class="container">
        <h2><i class="fas fa-map-marked-alt"></i> Service Coverage</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">9</div>
                <div class="stat-label">Municipalities</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">285</div>
                <div class="stat-label">Barangays</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">40,160</div>
                <div class="stat-label">Households</div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
require_once __DIR__ . '/../includes/footer.php';
?>
