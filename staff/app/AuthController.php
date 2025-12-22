<?php
/**
 * LEYECO III Forms Management System
 * Authentication Controller
 */

require_once __DIR__ . '/config.php';

class AuthController {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Authenticate user with username and password
     */
    public function login($username, $password, $rememberMe = false) {
        try {
            $stmt = $this->conn->prepare("
                SELECT id, username, password, email, full_name, role, is_active 
                FROM users 
                WHERE username = ? OR email = ?
            ");
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (!$user) {
                return ['success' => false, 'error' => 'Invalid username or password'];
            }

            if (!$user['is_active']) {
                return ['success' => false, 'error' => 'Account is deactivated'];
            }

            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'error' => 'Invalid username or password'];
            }

            // Create session
            $this->createSession($user, $rememberMe);

            // Update last login
            $updateStmt = $this->conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();

            return ['success' => true, 'user' => $user];
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'error' => 'An error occurred during login'];
        }
    }

    /**
     * Create user session
     */
    private function createSession($user, $rememberMe = false) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();

        // Generate CSRF token
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }

        // Create session token in database
        $sessionToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + SESSION_LIFETIME);
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $stmt = $this->conn->prepare("
            INSERT INTO sessions (user_id, session_token, ip_address, user_agent, expires_at, last_activity)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("issss", $user['id'], $sessionToken, $ipAddress, $userAgent, $expiresAt);
        $stmt->execute();

        $_SESSION['session_token'] = $sessionToken;

        // Set remember me cookie if requested
        if ($rememberMe) {
            setcookie('remember_token', $sessionToken, time() + (86400 * 30), '/', '', true, true);
        }
    }

    /**
     * Logout user
     */
    public function logout() {
        if (isset($_SESSION['session_token'])) {
            // Delete session from database
            $stmt = $this->conn->prepare("DELETE FROM sessions WHERE session_token = ?");
            $stmt->bind_param("s", $_SESSION['session_token']);
            $stmt->execute();
        }

        // Clear remember me cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        }

        // Destroy session
        session_unset();
        session_destroy();
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            return false;
        }

        // Check session timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_LIFETIME)) {
            $this->logout();
            return false;
        }

        // Update last activity in PHP session
        $_SESSION['last_activity'] = time();
        
        // Update last_activity in database for this session
        if (isset($_SESSION['session_token'])) {
            try {
                $stmt = $this->conn->prepare("UPDATE sessions SET last_activity = NOW() WHERE session_token = ?");
                $stmt->bind_param("s", $_SESSION['session_token']);
                $stmt->execute();
            } catch (Exception $e) {
                error_log("Failed to update session activity: " . $e->getMessage());
            }
        }

        return true;
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin() {
        return $this->hasRole('admin');
    }

    /**
     * Get current user data
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'role' => $_SESSION['role'] ?? null
        ];
    }

    /**
     * Generate CSRF token
     */
    public function generateCsrfToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    /**
     * Validate CSRF token
     */
    public function validateCsrfToken($token) {
        return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }

    /**
     * Clean expired sessions (by expiration time or inactivity)
     */
    public function cleanExpiredSessions() {
        // Delete sessions that have expired by time
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE expires_at < NOW()");
        $stmt->execute();
        
        // Delete sessions that have been inactive for too long
        $this->cleanInactiveSessions();
    }
    
    /**
     * Clean inactive sessions (no activity for INACTIVITY_TIMEOUT period)
     */
    public function cleanInactiveSessions() {
        $inactivityThreshold = date('Y-m-d H:i:s', time() - INACTIVITY_TIMEOUT);
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE last_activity < ?");
        $stmt->bind_param("s", $inactivityThreshold);
        $stmt->execute();
    }
}
?>
