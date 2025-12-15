    </main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo" style="display: flex; align-items: center; gap: 10px; margin-bottom: 0.5rem;">
                        <img src="/assets/images/logoL3iii.webp" alt="LEYECO III Logo" style="height: 30px; width: auto;">
                        <h4 style="margin: 0;">LEYECO III</h4>
                    </div>
                    <p>Leyte III Electric Cooperative, Inc.</p>
                    <p><i><?php echo SITE_TAGLINE; ?></i></p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/public/index.php">Home</a></li>
                        <li><a href="/forms/request_form.php">Service Request</a></li>
                        <li><a href="/forms/reconnection_form.php">Reconnection</a></li>
                        <li><a href="/public/contact.php">Contact Us</a></li>
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
    <script src="/assets/js/main.js"></script>
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
