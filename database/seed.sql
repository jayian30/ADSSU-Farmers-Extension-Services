USE adssu_farmers_db;

-- Insert default extension worker (Password is 'admin123')
INSERT IGNORE INTO users (username, password, full_name, role, email) 
VALUES ('worker', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Extension Worker 1', 'extension_worker', 'worker@adssu.edu.ph');

INSERT IGNORE INTO extension_workers (user_id, contact_number, assigned_barangay) 
SELECT id, '09123456789', 'Brgy. Central' FROM users WHERE username = 'worker';

-- Insert default farmer (Password is 'admin123')
INSERT IGNORE INTO users (username, password, full_name, role, email) 
VALUES ('farmer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan Farmer', 'farmer', 'farmer@adssu.edu.ph');

INSERT IGNORE INTO farmers (user_id, rsbsa_number, full_name, address, barangay, contact_number, farm_type, crop_type, farm_size) 
SELECT id, 'RSBSA-12345', 'Juan Farmer', '123 Agri St.', 'Brgy. Central', '09987654321', 'Crop', 'Rice', 1.5 FROM users WHERE username = 'farmer';
