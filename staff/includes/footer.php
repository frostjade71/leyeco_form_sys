            </main>
        </div>
    </div>
    
    <script>
        // Toggle sidebar on mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }
        
        // Toggle user menu
        function toggleUserMenu() {
            const userMenu = document.getElementById('userMenu');
            userMenu.classList.toggle('active');
        }
        
        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            if (!userMenu.contains(event.target)) {
                userMenu.classList.remove('active');
            }
        });
        
        // Close sidebar when clicking overlay
        document.querySelector('.sidebar-overlay')?.addEventListener('click', toggleSidebar);
        
        
        // Theme Toggle Functionality
        function toggleTheme() {
            const html = document.documentElement;
            const themeIcon = document.getElementById('themeIcon');
            const currentTheme = html.getAttribute('data-theme');
            
            if (currentTheme === 'dark') {
                html.setAttribute('data-theme', 'light');
                themeIcon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            } else {
                html.setAttribute('data-theme', 'dark');
                themeIcon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            }
        }
        
        // Update icon based on current theme (theme already applied in head)
        (function updateThemeIcon() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const themeIcon = document.getElementById('themeIcon');
            
            if (currentTheme === 'dark') {
                themeIcon.className = 'fas fa-sun';
            } else {
                themeIcon.className = 'fas fa-moon';
            }
        })();
    </script>
    
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
