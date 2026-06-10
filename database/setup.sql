-- database/setup.sql

-- CREATE DATABASE IF NOT EXISTS adssu_farmers_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE adssu_farmers_db;

-- 1. users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'extension_worker', 'farmer') NOT NULL,
    email VARCHAR(100) NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. extension_workers table
CREATE TABLE IF NOT EXISTS extension_workers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    contact_number VARCHAR(20) NULL,
    assigned_barangay VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. farmers table
CREATE TABLE IF NOT EXISTS farmers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL, -- Can be null if they don't have an account yet
    rsbsa_number VARCHAR(50) UNIQUE NULL,
    full_name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    barangay VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NULL,
    farm_type VARCHAR(100) NULL,
    crop_type VARCHAR(100) NULL,
    farm_size DECIMAL(10, 2) NULL, -- in hectares
    profile_photo VARCHAR(255) NULL,
    qr_code VARCHAR(255) NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    registered_by INT NULL, -- extension_worker id
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (registered_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 4. agricultural_programs table
CREATE TABLE IF NOT EXISTS agricultural_programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_name VARCHAR(150) NOT NULL,
    description TEXT,
    start_date DATE NULL,
    end_date DATE NULL,
    status ENUM('planned', 'active', 'completed') DEFAULT 'planned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. assistance_records table
CREATE TABLE IF NOT EXISTS assistance_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT NOT NULL,
    program_id INT NOT NULL,
    assistance_type VARCHAR(100) NOT NULL, -- e.g., 'Seeds', 'Fertilizer', 'Cash'
    quantity DECIMAL(10, 2) NULL,
    unit VARCHAR(20) NULL,
    date_received DATE NOT NULL,
    distributed_by INT NULL, -- user_id of worker
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES farmers(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES agricultural_programs(id) ON DELETE CASCADE,
    FOREIGN KEY (distributed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 6. trainings table
CREATE TABLE IF NOT EXISTS trainings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    schedule_date DATETIME NOT NULL,
    location VARCHAR(150) NOT NULL,
    organizer_id INT NULL, -- user_id of admin or worker
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organizer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 7. training_attendance table
CREATE TABLE IF NOT EXISTS training_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    training_id INT NOT NULL,
    farmer_id INT NOT NULL,
    status ENUM('registered', 'attended', 'absent') DEFAULT 'registered',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE,
    FOREIGN KEY (farmer_id) REFERENCES farmers(id) ON DELETE CASCADE
);

-- 8. field_visits table
CREATE TABLE IF NOT EXISTS field_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    worker_id INT NOT NULL,
    farmer_id INT NOT NULL,
    visit_date DATETIME NOT NULL,
    purpose VARCHAR(150) NOT NULL,
    notes TEXT,
    farmer_concerns TEXT,
    photo_url VARCHAR(255) NULL,
    gps_latitude DECIMAL(10, 8) NULL,
    gps_longitude DECIMAL(11, 8) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (worker_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (farmer_id) REFERENCES farmers(id) ON DELETE CASCADE
);

-- 9. announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NULL,
    target_role ENUM('all', 'farmer', 'extension_worker') DEFAULT 'all',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- 10. notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 11. activity_logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin account (Password is 'admin123')
INSERT INTO users (username, password, full_name, role, email) 
VALUES ('admin', '$2y$10$Hux4PdbTs2btSTo..sJHFe3AAzDh6t9hAnQxpqF2tnxUMzQPQQR7u', 'System Administrator', 'admin', 'admin@adssu.edu.ph');

-- Insert default extension worker (Password is 'admin123')
INSERT INTO users (username, password, full_name, role, email) 
VALUES ('worker', '$2y$10$Hux4PdbTs2btSTo..sJHFe3AAzDh6t9hAnQxpqF2tnxUMzQPQQR7u', 'Extension Worker 1', 'extension_worker', 'worker@adssu.edu.ph');
SET @worker_id = LAST_INSERT_ID();
INSERT INTO extension_workers (user_id, contact_number, assigned_barangay) VALUES (@worker_id, '09123456789', 'Brgy. Central');

-- Insert default farmer (Password is 'admin123')
INSERT INTO users (username, password, full_name, role, email) 
VALUES ('farmer', '$2y$10$Hux4PdbTs2btSTo..sJHFe3AAzDh6t9hAnQxpqF2tnxUMzQPQQR7u', 'Juan Farmer', 'farmer', 'farmer@adssu.edu.ph');
SET @farmer_id = LAST_INSERT_ID();
INSERT INTO farmers (user_id, rsbsa_number, full_name, address, barangay, contact_number, farm_type, crop_type, farm_size) 
VALUES (@farmer_id, 'RSBSA-12345', 'Juan Farmer', '123 Agri St.', 'Brgy. Central', '09987654321', 'Crop', 'Rice', 1.5);
