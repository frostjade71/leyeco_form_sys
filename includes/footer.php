    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo" style="display: flex; align-items: center; gap: 10px; margin-bottom: 0.5rem;">
                        <img src="<?php echo BASE_URL; ?>/assets/images/logoL3iii.webp" alt="LEYECO III Logo" style="height: 30px; width: auto;">
                        <h4 style="margin: 0;">LEYECO III</h4>
                    </div>
                    <p>Leyte III Electric Cooperative, Inc.</p>
                    <p><i><?php echo SITE_TAGLINE; ?></i></p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>/public/index.php">Home</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/index.php#select-forms">Select Forms</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/contact.php#map-section">Find Us</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/public/contact.php#faq-section">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact Information</h4>
                    <p><i class="fas fa-map-marker-alt"></i> Brgy. San Roque, Tunga, Leyte</p>
                    <p><i class="fas fa-phone"></i> SMART: +639285104998</p>
                    <p><i class="fas fa-phone"></i> GLOBE: +639173049794</p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:leyteiiie@yahoo.com" style="color: var(--light-gray);">leyteiiie@yahoo.com</a></p>
                    <p><i class="fas fa-globe"></i> <a href="https://www.leyeco3.com/" target="_blank" style="color: var(--light-gray);">www.leyeco3.com</a></p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="<?php echo BASE_URL; ?>/assets/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/page-loader.js"></script>
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <?php if (strpos($js, 'http') === 0): ?>
                <script src="<?php echo $js; ?>"></script>
            <?php else: ?>
                <script src="<?php echo BASE_URL . $js; ?>"></script>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
