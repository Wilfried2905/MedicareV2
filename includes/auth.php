<?php
require_once '../config/config.php';

class Auth {
    
    public static function authenticate($username, $password, $app) {
        global $demo_users;
        
        start_secure_session();
        
        // Vérification des tentatives de connexion
        if (self::isBlocked()) {
            return ['success' => false, 'message' => 'Trop de tentatives de connexion. Réessayez plus tard.'];
        }
        
        // Vérification des credentials
        if (isset($demo_users[$app][$username])) {
            $user = $demo_users[$app][$username];
            
            if ($user['password'] === $password) {
                // Connexion réussie
                $_SESSION['user'] = [
                    'username' => $username,
                    'role' => $user['role'],
                    'app' => $app
                ];
                $_SESSION['app'] = $app;
                $_SESSION['login_time'] = time();
                
                // Reset des tentatives
                unset($_SESSION['login_attempts']);
                unset($_SESSION['login_blocked_until']);
                
                return ['success' => true, 'user' => $_SESSION['user']];
            }
        }
        
        // Échec de connexion
        self::recordFailedAttempt();
        return ['success' => false, 'message' => 'Nom d\'utilisateur ou mot de passe incorrect.'];
    }
    
    public static function logout() {
        start_secure_session();
        session_unset();
        session_destroy();
    }
    
    public static function isLoggedIn($app = null) {
        start_secure_session();
        
        if (!isset($_SESSION['user'])) {
            return false;
        }
        
        // Vérification du timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
            self::logout();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        
        if ($app && $_SESSION['app'] !== $app) {
            return false;
        }
        
        return true;
    }
    
    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return $_SESSION['user'];
        }
        return null;
    }
    
    public static function requireAuth($app, $redirectUrl = null) {
        if (!self::isLoggedIn($app)) {
            if ($redirectUrl) {
                header("Location: $redirectUrl");
            } else {
                header("Location: login.php");
            }
            exit;
        }
    }
    
    public static function requireRole($role, $redirectUrl = 'login.php') {
        $user = self::getCurrentUser();
        if (!$user || $user['role'] !== $role) {
            header("Location: $redirectUrl");
            exit;
        }
    }
    
    private static function recordFailedAttempt() {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        
        $_SESSION['login_attempts']++;
        
        if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $_SESSION['login_blocked_until'] = time() + 900; // 15 minutes
        }
    }
    
    private static function isBlocked() {
        if (isset($_SESSION['login_blocked_until'])) {
            if (time() < $_SESSION['login_blocked_until']) {
                return true;
            } else {
                unset($_SESSION['login_blocked_until']);
                unset($_SESSION['login_attempts']);
            }
        }
        return false;
    }
    
    public static function generatePassword($length = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        return substr(str_shuffle($chars), 0, $length);
    }
    
    public static function validatePassword($password) {
        return strlen($password) >= PASSWORD_MIN_LENGTH;
    }
}

// Fonction helper pour vérifier si l'utilisateur est admin
function is_admin() {
    $user = Auth::getCurrentUser();
    return $user && $user['role'] === 'admin';
}

// Fonction helper pour vérifier si l'utilisateur est médecin
function is_doctor() {
    $user = Auth::getCurrentUser();
    return $user && $user['role'] === 'doctor';
}

// Fonction helper pour vérifier si l'utilisateur est patient
function is_patient() {
    $user = Auth::getCurrentUser();
    return $user && $user['role'] === 'patient';
}
?>