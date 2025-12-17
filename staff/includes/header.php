<?php
/**
 * LEYECO III Forms Management System
 * Staff Dashboard Header
 */

if (!isset($currentUser)) {
    die('Unauthorized access');
}

// Refresh current user data from database to ensure latest info is displayed
$stmt = $conn->prepare("SELECT id, username, email, full_name, role FROM users WHERE id = ?");
$stmt->bind_param("i", $currentUser['id']);
$stmt->execute();
$freshUserData = $stmt->get_result()->fetch_assoc();

// Update current user with fresh data
if ($freshUserData) {
    $currentUser = array_merge($currentUser, $freshUserData);
    // Update session with fresh data
    $_SESSION['full_name'] = $freshUserData['full_name'];
    $_SESSION['email'] = $freshUserData['email'];
    $_SESSION['role'] = $freshUserData['role'];
}

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/leyecoicon_fav.svg">
    
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - LEYECO III Staff Portal</title>
    
    <!-- Theme Initialization (Prevent Flash) -->
    <script>
        // Apply theme immediately before page renders
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Staff Stylesheet -->
    <link rel="stylesheet" href="<?php echo STAFF_URL; ?>/assets/css/staff.css?v=<?php echo time(); ?>">
    
    <!-- Page-specific CSS -->
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <?php 
        // Check if we're in a subdirectory with a custom sidebar
        $currentDir = dirname($_SERVER['SCRIPT_FILENAME']);
        $customSidebarPath = $currentDir . '/includes/sidebar.php';
        
        // If custom sidebar exists in current directory, use it
        if (file_exists($customSidebarPath)) {
            include $customSidebarPath;
        } else {
            // Use default staff sidebar
            include __DIR__ . '/sidebar.php';
        }
        ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <button class="mobile-menu-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                        <?php if (isset($breadcrumbs)): ?>
                            <div class="breadcrumb">
                                <?php foreach ($breadcrumbs as $index => $crumb): ?>
                                    <?php if ($index > 0): ?>
                                        <i class="fas fa-chevron-right"></i>
                                    <?php endif; ?>
                                    <?php if (isset($crumb['url'])): ?>
                                        <a href="<?php echo $crumb['url']; ?>"><?php echo $crumb['label']; ?></a>
                                    <?php else: ?>
                                        <span><?php echo $crumb['label']; ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="header-right">
                    <!-- Theme Toggle Button -->
                    <button class="theme-toggle" onclick="toggleTheme()" aria-label="Toggle theme">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </button>
                    
                    <div class="user-menu" id="userMenu">
                        <button class="user-menu-toggle" onclick="toggleUserMenu()">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($currentUser['full_name'], 0, 1)); ?>
                            </div>
                        </button>
                        <div class="user-menu-dropdown">
                            <a href="<?php echo STAFF_URL; ?>/profile.php">
                                <i class="fas fa-user"></i>
                                My Profile
                            </a>
                            <a href="<?php echo STAFF_URL; ?>/settings.php">
                                <i class="fas fa-cog"></i>
                                Settings
                            </a>
                            <div class="divider"></div>
                            <a href="<?php echo STAFF_URL; ?>/logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Sidebar Overlay for Mobile -->
            <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
            
            <!-- Page Content -->
            <main class="content">
