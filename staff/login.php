<?php
/**
 * LEYECO III Forms Management System
 * Staff Login Page
 */

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/AuthController.php';

$auth = new AuthController();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: ' . STAFF_URL . '/dashboard.php');
    exit;
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);
    $csrfToken = $_POST['csrf_token'] ?? '';

    // Validate CSRF token
    if (!$auth->validateCsrfToken($csrfToken)) {
        $error = 'Invalid request. Please try again.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $result = $auth->login($username, $password, $rememberMe);
        
        if ($result['success']) {
            // Redirect to intended page or dashboard
            $redirectUrl = $_SESSION['intended_url'] ?? STAFF_URL . '/dashboard.php';
            unset($_SESSION['intended_url']);
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            $error = $result['error'];
        }
    }
}

// Generate CSRF token
$csrfToken = $auth->generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - LEYECO III Forms Management System</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>/assets/images/leyecoicon_fav.svg">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Login Stylesheet -->
    <link rel="stylesheet" href="<?php echo STAFF_URL; ?>/assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="login-logo">
                    <img src="<?php echo BASE_URL; ?>/assets/images/logoL3iii.webp" alt="LEYECO III Logo">
                </div>
                <h1>Staff Portal</h1>
                <p>LEYECO III Forms Management System</p>
            </div>

            <!-- Body -->
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i>
                            Username or Email
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-user"></i>
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                class="form-control" 
                                placeholder="Enter your username or email"
                                value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Password
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-control" 
                                placeholder="Enter your password"
                                required
                            >
                            <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                        </div>
                    </div>

                    <div class="form-options">
                        <div class="checkbox-wrapper">
                            <input 
                                type="checkbox" 
                                id="remember_me" 
                                name="remember_me"
                                <?php echo isset($_POST['remember_me']) ? 'checked' : ''; ?>
                            >
                            <label for="remember_me">Remember me</label>
                        </div>
                        <!-- <a href="#" class="forgot-password">Forgot password?</a> -->
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>
            </div>

            <!-- Footer -->
            <div class="login-footer">
                <p>Need help? Contact your system administrator</p>
                <a href="<?php echo BASE_URL; ?>/public/index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Back to Public Site
                </a>
            </div>
        </div>
    </div>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // toggle the eye slash icon
            this.classList.toggle('fa-eye-slash');
            
            // trigger animation
            this.classList.remove('animate');
            void this.offsetWidth; // trigger reflow
            this.classList.add('animate');
        });
    </script>
</body>
</html>
