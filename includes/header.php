<?php
/**
 * Header Component
 * LEYECO III Forms Management System
 */
require_once __DIR__ . '/../config/config.php';

// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LEYECO III Forms Management System - Submit and manage your service requests online">
    <meta name="keywords" content="LEYECO III, electric cooperative, forms, service request, reconnection, complaints, meter replacement">
    <meta name="author" content="LEYECO III">
    
    <title><?php echo isset($page_title) ? clean_output($page_title) . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Page-specific CSS -->
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay"></div>
    
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <!-- Logo/Brand -->
            <a href="/public/index.php" class="navbar-brand">
                <img src="/assets/images/logoL3iii.webp" alt="LEYECO III Logo" class="logo-icon">
                <img src="/assets/images/logo_leyeco3.webp" alt="LEYECO III" class="logo-text">
            </a>
            
            <!-- Mobile Toggle -->
            <div class="navbar-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
            
            <!-- Navigation Menu -->
            <div class="navbar-menu">
                <ul class="navbar-nav">
                    <li>
                        <a href="/public/index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li>
                        <a href="/public/about.php" class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>">
                            <i class="fas fa-info-circle"></i> About
                        </a>
                    </li>
                    <li>
                        <a href="/public/contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">
                            <i class="fas fa-envelope"></i> Contact
                        </a>
                    </li>
                    <li>
                        <a href="/staff/login.php" class="btn btn-primary">
                            <i class="fas fa-user-lock"></i> Staff Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Wrapper -->
    <main class="main-content">
