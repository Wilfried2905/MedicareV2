<?php
// Configuration générale de l'application MediCare+
define('APP_NAME', 'MediCare+');
define('APP_VERSION', '2.0.0');
define('APP_URL', 'http://localhost/medicare-plus');

// Configuration de sécurité
define('SESSION_TIMEOUT', 3600); // 1 heure
define('PASSWORD_MIN_LENGTH', 6);
define('MAX_LOGIN_ATTEMPTS', 3);

// Configuration des applications
define('TENSIOCARE_NAME', 'TensioCare');
define('DIABETOCARE_NAME', 'DiabetoCare');
define('CONSULTATIONS_NAME', 'Consultations');

// Données de démonstration - Utilisateurs
$demo_users = [
    // TensioCare
    'tensiocare' => [
        'patient' => ['username' => 'patient', 'password' => 'patient', 'role' => 'patient', 'app' => 'tensiocare'],
        'medecin' => ['username' => 'medecin', 'password' => 'medecin', 'role' => 'doctor', 'app' => 'tensiocare'],
        'admin' => ['username' => 'admin', 'password' => 'admin', 'role' => 'admin', 'app' => 'tensiocare']
    ],
    // DiabetoCare  
    'diabetocare' => [
        'patient' => ['username' => 'patient', 'password' => 'patient', 'role' => 'patient', 'app' => 'diabetocare'],
        'medecin' => ['username' => 'medecin', 'password' => 'medecin', 'role' => 'doctor', 'app' => 'diabetocare'],
        'admin' => ['username' => 'admin', 'password' => 'admin', 'role' => 'admin', 'app' => 'diabetocare']
    ],
    // Consultations
    'consultations' => [
        'patient' => ['username' => 'patient', 'password' => 'patient', 'role' => 'patient', 'app' => 'consultations'],
        'medecin' => ['username' => 'medecin', 'password' => 'medecin', 'role' => 'doctor', 'app' => 'consultations'],
        'admin' => ['username' => 'admin', 'password' => 'admin', 'role' => 'admin', 'app' => 'consultations']
    ]
];

// Classification OMS 2023 - Tension Artérielle
$blood_pressure_classification = [
    'optimal' => ['systolic' => [0, 120], 'diastolic' => [0, 80], 'color' => 'green', 'label' => 'Optimale'],
    'normal' => ['systolic' => [120, 130], 'diastolic' => [80, 85], 'color' => 'green', 'label' => 'Normale'],
    'high_normal' => ['systolic' => [130, 140], 'diastolic' => [85, 90], 'color' => 'yellow', 'label' => 'Normale-Haute'],
    'grade1' => ['systolic' => [140, 160], 'diastolic' => [90, 100], 'color' => 'orange', 'label' => 'HTA Grade 1'],
    'grade2' => ['systolic' => [160, 180], 'diastolic' => [100, 110], 'color' => 'red', 'label' => 'HTA Grade 2'],
    'grade3' => ['systolic' => [180, 999], 'diastolic' => [110, 999], 'color' => 'red', 'label' => 'HTA Grade 3'],
    'isolated_systolic' => ['systolic' => [140, 999], 'diastolic' => [0, 90], 'color' => 'orange', 'label' => 'HTA Systolique Isolée']
];

// Classification ADA 2025 - Glycémie  
$glucose_classification = [
    'normal' => ['min' => 70, 'max' => 100, 'color' => 'green', 'label' => 'Normal'],
    'prediabetes' => ['min' => 100, 'max' => 126, 'color' => 'yellow', 'label' => 'Prédiabète'],
    'diabetes' => ['min' => 126, 'max' => 999, 'color' => 'red', 'label' => 'Diabète']
];

// Méthodes de paiement Mobile Money
$payment_methods = [
    'orange' => ['name' => 'Orange Money', 'color' => 'orange', 'prefix' => '+225'],
    'mtn' => ['name' => 'MTN Mobile Money', 'color' => 'yellow', 'prefix' => '+225'],
    'moov' => ['name' => 'Moov Money', 'color' => 'blue', 'prefix' => '+225']
];

// Fonction pour démarrer une session sécurisée
function start_secure_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
        
        // Vérification du timeout de session
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
            session_unset();
            session_destroy();
            session_start();
        }
        $_SESSION['last_activity'] = time();
    }
}

// Fonction pour vérifier l'authentification
function is_authenticated($app = null) {
    start_secure_session();
    if ($app) {
        return isset($_SESSION['user']) && $_SESSION['app'] === $app;
    }
    return isset($_SESSION['user']);
}

// Fonction pour obtenir l'utilisateur connecté
function get_current_user() {
    start_secure_session();
    return $_SESSION['user'] ?? null;
}

// Fonction pour se déconnecter
function logout() {
    start_secure_session();
    session_unset();
    session_destroy();
}

// Fonction pour classifier la tension artérielle
function classify_blood_pressure($systolic, $diastolic) {
    global $blood_pressure_classification;
    
    // HTA Systolique Isolée
    if ($systolic >= 140 && $diastolic < 90) {
        return $blood_pressure_classification['isolated_systolic'];
    }
    
    // Classification par grade
    foreach ($blood_pressure_classification as $key => $class) {
        if ($key === 'isolated_systolic') continue;
        
        if ($systolic >= $class['systolic'][0] && $systolic < $class['systolic'][1] &&
            $diastolic >= $class['diastolic'][0] && $diastolic < $class['diastolic'][1]) {
            return $class;
        }
    }
    
    return $blood_pressure_classification['optimal'];
}

// Fonction pour classifier la glycémie
function classify_glucose($glucose) {
    global $glucose_classification;
    
    foreach ($glucose_classification as $key => $class) {
        if ($glucose >= $class['min'] && $glucose < $class['max']) {
            return $class;
        }
    }
    
    return $glucose_classification['normal'];
}

// Fonction pour formater la date
function format_date($date) {
    return date('d/m/Y H:i', strtotime($date));
}

// Fonction pour générer un token CSRF
function generate_csrf_token() {
    start_secure_session();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fonction pour vérifier le token CSRF
function verify_csrf_token($token) {
    start_secure_session();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>