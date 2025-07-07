-- Schéma de base de données MySQL pour MediCare+
-- Version 2.0.0 - PHP

-- Base de données principale
CREATE DATABASE IF NOT EXISTS medicare_plus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medicare_plus;

-- Table des utilisateurs (tous profils et applications)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('patient', 'doctor', 'admin') NOT NULL,
    app ENUM('tensiocare', 'diabetocare', 'consultations') NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    birth_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    UNIQUE KEY unique_user_app (username, app),
    INDEX idx_role_app (role, app),
    INDEX idx_username (username)
);

-- Table des mesures de tension artérielle (TensioCare)
CREATE TABLE blood_pressure_measurements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    systolic INT NOT NULL,
    diastolic INT NOT NULL,
    pulse INT,
    measurement_date DATETIME NOT NULL,
    context VARCHAR(100),
    notes TEXT,
    location VARCHAR(50),
    device_used VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, measurement_date),
    INDEX idx_measurement_date (measurement_date)
);

-- Table des mesures de glycémie (DiabetoCare)
CREATE TABLE glucose_measurements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    glucose_level DECIMAL(5,2) NOT NULL,
    measurement_type ENUM('fasting', 'postprandial', 'random', 'bedtime') NOT NULL,
    measurement_date DATETIME NOT NULL,
    context VARCHAR(100),
    notes TEXT,
    carbs_consumed INT,
    insulin_units DECIMAL(5,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, measurement_date),
    INDEX idx_measurement_date (measurement_date),
    INDEX idx_glucose_level (glucose_level)
);

-- Table des médicaments
CREATE TABLE medications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    app ENUM('tensiocare', 'diabetocare', 'consultations') NOT NULL,
    name VARCHAR(200) NOT NULL,
    dosage VARCHAR(100),
    frequency VARCHAR(100),
    instructions TEXT,
    start_date DATE,
    end_date DATE,
    prescribed_by VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_app (user_id, app),
    INDEX idx_active (is_active)
);

-- Table des consultations
CREATE TABLE consultations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    consultation_date DATETIME NOT NULL,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    type ENUM('routine', 'follow_up', 'emergency', 'specialist') NOT NULL,
    duration_minutes INT DEFAULT 30,
    notes TEXT,
    diagnosis TEXT,
    treatment_plan TEXT,
    next_appointment DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_patient_date (patient_id, consultation_date),
    INDEX idx_doctor_date (doctor_id, consultation_date),
    INDEX idx_status (status)
);

-- Table des messages (communication)
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    app ENUM('tensiocare', 'diabetocare', 'consultations') NOT NULL,
    subject VARCHAR(200),
    content TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    is_urgent BOOLEAN DEFAULT FALSE,
    message_type ENUM('general', 'appointment', 'prescription', 'emergency') DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_recipient_app (recipient_id, app),
    INDEX idx_sender_date (sender_id, created_at),
    INDEX idx_is_read (is_read)
);

-- Table des abonnements
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    app ENUM('tensiocare', 'diabetocare', 'consultations') NOT NULL,
    plan_type ENUM('free', 'basic', 'premium', 'professional') NOT NULL,
    status ENUM('active', 'trial', 'expired', 'cancelled', 'grace_period') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    trial_end_date DATE,
    grace_period_end_date DATE,
    price DECIMAL(10,2),
    currency VARCHAR(3) DEFAULT 'XOF',
    auto_renewal BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_app_subscription (user_id, app),
    INDEX idx_status_end_date (status, end_date),
    INDEX idx_app_status (app, status)
);

-- Table des paiements
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subscription_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'XOF',
    payment_method ENUM('orange_money', 'mtn_money', 'moov_money', 'visa', 'mastercard') NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    phone_number VARCHAR(20),
    payment_date DATETIME NOT NULL,
    processed_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, payment_date),
    INDEX idx_status (payment_status),
    INDEX idx_transaction_id (transaction_id)
);

-- Table des rappels/alertes
CREATE TABLE alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    app ENUM('tensiocare', 'diabetocare', 'consultations') NOT NULL,
    alert_type ENUM('measurement_reminder', 'medication_reminder', 'appointment_reminder', 'payment_due', 'critical_value') NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    alert_date DATETIME NOT NULL,
    is_sent BOOLEAN DEFAULT FALSE,
    is_read BOOLEAN DEFAULT FALSE,
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sent_at TIMESTAMP NULL,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_app (user_id, app),
    INDEX idx_alert_date (alert_date),
    INDEX idx_is_sent (is_sent),
    INDEX idx_priority (priority)
);

-- Table des prescriptions
CREATE TABLE prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    consultation_id INT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    app ENUM('tensiocare', 'diabetocare', 'consultations') NOT NULL,
    prescription_date DATE NOT NULL,
    medications JSON NOT NULL,
    instructions TEXT,
    duration_days INT,
    renewable BOOLEAN DEFAULT FALSE,
    renewal_count INT DEFAULT 0,
    max_renewals INT DEFAULT 0,
    status ENUM('active', 'completed', 'cancelled', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (consultation_id) REFERENCES consultations(id) ON DELETE SET NULL,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_patient_date (patient_id, prescription_date),
    INDEX idx_doctor_date (doctor_id, prescription_date),
    INDEX idx_status (status),
    INDEX idx_app (app)
);

-- Table de logs pour audit
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    app ENUM('tensiocare', 'diabetocare', 'consultations'),
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_date (user_id, created_at),
    INDEX idx_action (action),
    INDEX idx_table_record (table_name, record_id)
);

-- Insertion des données de démonstration
INSERT INTO users (username, password, role, app, first_name, last_name, email) VALUES 
-- TensioCare
('patient', 'patient', 'patient', 'tensiocare', 'Jean', 'Dupont', 'jean.dupont@email.com'),
('medecin', 'medecin', 'doctor', 'tensiocare', 'Dr. Marie', 'Martin', 'dr.martin@tensiocare.com'),
('admin', 'admin', 'admin', 'tensiocare', 'Admin', 'TensioCare', 'admin@tensiocare.com'),

-- DiabetoCare
('patient', 'patient', 'patient', 'diabetocare', 'Pierre', 'Bernard', 'pierre.bernard@email.com'),
('medecin', 'medecin', 'doctor', 'diabetocare', 'Dr. Sophie', 'Dubois', 'dr.dubois@diabetocare.com'),
('admin', 'admin', 'admin', 'diabetocare', 'Admin', 'DiabetoCare', 'admin@diabetocare.com'),

-- Consultations
('patient', 'patient', 'patient', 'consultations', 'Paul', 'Moreau', 'paul.moreau@email.com'),
('medecin', 'medecin', 'doctor', 'consultations', 'Dr. Julien', 'Petit', 'dr.petit@consultations.com'),
('admin', 'admin', 'admin', 'consultations', 'Admin', 'Consultations', 'admin@consultations.com');

-- Données de démonstration pour TensioCare
INSERT INTO blood_pressure_measurements (user_id, systolic, diastolic, pulse, measurement_date, context) VALUES
(1, 128, 82, 72, '2025-01-07 09:30:00', 'Matin, à jeun'),
(1, 145, 88, 78, '2025-01-06 14:15:00', 'Après repas'),
(1, 135, 85, 75, '2025-01-05 19:20:00', 'Soir, repos'),
(1, 142, 90, 80, '2025-01-04 08:45:00', 'Matin, stress'),
(1, 130, 78, 70, '2025-01-03 16:00:00', 'Après-midi');

-- Données de démonstration pour DiabetoCare
INSERT INTO glucose_measurements (user_id, glucose_level, measurement_type, measurement_date, context) VALUES
(4, 95.0, 'fasting', '2025-01-07 07:00:00', 'Matin à jeun'),
(4, 149.0, 'postprandial', '2025-01-06 14:00:00', '2h après repas'),
(4, 105.0, 'random', '2025-01-05 16:30:00', 'Contrôle afternoon'),
(4, 180.0, 'postprandial', '2025-01-04 20:00:00', 'Après dîner copieux'),
(4, 88.0, 'fasting', '2025-01-03 07:15:00', 'Matin à jeun');

-- Abonnements de démonstration
INSERT INTO subscriptions (user_id, app, plan_type, status, start_date, end_date, trial_end_date, price) VALUES
(1, 'tensiocare', 'premium', 'active', '2025-01-01', '2025-02-01', '2025-01-08', 2500.00),
(4, 'diabetocare', 'basic', 'trial', '2025-01-01', '2025-02-01', '2025-01-08', 1500.00),
(7, 'consultations', 'professional', 'active', '2025-01-01', '2025-02-01', '2025-01-08', 5000.00);

-- Paiements de démonstration
INSERT INTO payments (user_id, subscription_id, amount, payment_method, payment_status, payment_date, phone_number) VALUES
(1, 1, 2500.00, 'orange_money', 'completed', '2025-01-01 10:00:00', '+225 07 12 34 56 78'),
(7, 3, 5000.00, 'mtn_money', 'completed', '2025-01-01 11:00:00', '+225 05 98 76 54 32');

-- Index de performance
CREATE INDEX idx_bp_measurements_composite ON blood_pressure_measurements(user_id, measurement_date DESC);
CREATE INDEX idx_glucose_measurements_composite ON glucose_measurements(user_id, measurement_date DESC);
CREATE INDEX idx_consultations_composite ON consultations(patient_id, consultation_date DESC);
CREATE INDEX idx_messages_composite ON messages(recipient_id, created_at DESC);
CREATE INDEX idx_alerts_composite ON alerts(user_id, alert_date DESC);

-- Vues pour simplifier les requêtes

-- Vue des mesures récentes de tension
CREATE VIEW recent_bp_measurements AS
SELECT 
    u.username,
    u.first_name,
    u.last_name,
    bp.systolic,
    bp.diastolic,
    bp.pulse,
    bp.measurement_date,
    bp.context,
    CASE 
        WHEN bp.systolic >= 180 OR bp.diastolic >= 110 THEN 'HTA Grade 3'
        WHEN bp.systolic >= 160 OR bp.diastolic >= 100 THEN 'HTA Grade 2'
        WHEN bp.systolic >= 140 OR bp.diastolic >= 90 THEN 'HTA Grade 1'
        WHEN bp.systolic >= 130 OR bp.diastolic >= 85 THEN 'Normale-Haute'
        WHEN bp.systolic >= 120 OR bp.diastolic >= 80 THEN 'Normale'
        ELSE 'Optimale'
    END as classification
FROM blood_pressure_measurements bp
JOIN users u ON bp.user_id = u.id
WHERE bp.measurement_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY bp.measurement_date DESC;

-- Vue des mesures récentes de glycémie
CREATE VIEW recent_glucose_measurements AS
SELECT 
    u.username,
    u.first_name,
    u.last_name,
    gm.glucose_level,
    gm.measurement_type,
    gm.measurement_date,
    gm.context,
    CASE 
        WHEN gm.glucose_level >= 126 THEN 'Diabète'
        WHEN gm.glucose_level >= 100 THEN 'Prédiabète'
        ELSE 'Normal'
    END as classification
FROM glucose_measurements gm
JOIN users u ON gm.user_id = u.id
WHERE gm.measurement_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY gm.measurement_date DESC;