INSERT INTO users (username, password, full_name, role, email, status, created_at) VALUES 
('tboy', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Tomas Boy', 'extension_worker', 'tboy@adssu.gov.ph', 'active', NOW()),
('gmanalo', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Gina Manalo', 'extension_worker', 'gina@adssu.gov.ph', 'active', NOW()),
('jfernandez', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Jose Fernandez', 'extension_worker', 'josef@adssu.gov.ph', 'active', NOW()),
('mbautista', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Mark Bautista', 'extension_worker', 'markb@adssu.gov.ph', 'active', NOW()),
('ksantos', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Karen Santos', 'extension_worker', 'karen@adssu.gov.ph', 'active', NOW()),
('crivera', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Carlos Rivera', 'extension_worker', 'carlos@adssu.gov.ph', 'active', NOW()),
('lmartin', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Luis Martin', 'extension_worker', 'luis.m@adssu.gov.ph', 'active', NOW()),
('pcastro', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Paula Castro', 'extension_worker', 'paula@adssu.gov.ph', 'active', NOW()),
('rquizon', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Ramon Quizon', 'extension_worker', 'ramon@adssu.gov.ph', 'active', NOW()),
('etolentino', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Elena Tolentino', 'extension_worker', 'elena@adssu.gov.ph', 'active', NOW()),
('vrosario', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Victor Rosario', 'extension_worker', 'victor@adssu.gov.ph', 'active', NOW()),
('nsy', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Nina Sy', 'extension_worker', 'nina@adssu.gov.ph', 'active', NOW()),
('fmercado', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Felix Mercado', 'extension_worker', 'felix@adssu.gov.ph', 'active', NOW()),
('dnavarro', '$2a$11$0.K1f6o2N2Bf0v.T6oD6UuI8zV5.7b2M.W.2wDkP0iV5Z.P/UuU2m', 'Diana Navarro', 'extension_worker', 'diana@adssu.gov.ph', 'active', NOW())
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO farmers (rsbsa_number, full_name, address, barangay, contact_number, farm_type, crop_type, farm_size, status, created_at) VALUES
('RSBSA-2026-0011', 'Teresa Makisig', '852 Purok 11', 'San Rafael', '09271234567', 'Irrigated', 'Rice', 2.00, 'active', NOW()),
('RSBSA-2026-0012', 'Juan Luna', '963 Purok 12', 'San Juan', '09281234567', 'Rainfed', 'Corn', 1.50, 'active', NOW()),
('RSBSA-2026-0013', 'Andres Bonifacio', '159 Purok 13', 'San Miguel', '09291234567', 'Upland', 'Vegetables', 0.75, 'active', NOW()),
('RSBSA-2026-0014', 'Jose Rizal', '753 Purok 14', 'San Pedro', '09301234567', 'Irrigated', 'Rice', 3.00, 'active', NOW()),
('RSBSA-2026-0015', 'Apolinario Mabini', '258 Purok 15', 'San Roque', '09311234567', 'Rainfed', 'Corn', 2.20, 'active', NOW()),
('RSBSA-2026-0016', 'Emilio Aguinaldo', '456 Purok 16', 'San Vicente', '09321234567', 'Upland', 'Vegetables', 0.90, 'active', NOW())
ON DUPLICATE KEY UPDATE rsbsa_number=rsbsa_number;

INSERT INTO agricultural_programs (program_name, description, start_date, end_date, status, created_at) VALUES
('Mango Rejuvenation Project', 'Pruning and fertilizer support for old mango orchards.', '2026-09-01', '2027-02-28', 'planned', NOW()),
('Cacao Production Boost', 'Distribution of grafted cacao seedlings to upland farmers.', '2026-01-15', '2026-10-15', 'active', NOW()),
('Livestock Dispersal Program', 'Distribution of goats and chickens for backyard farming.', '2026-02-01', '2026-12-31', 'active', NOW()),
('Vegetable Seeds Distribution', 'Assorted vegetable seeds for community gardens.', '2026-03-01', '2026-06-30', 'completed', NOW()),
('Soil Testing and Amelioration', 'Free soil testing and distribution of agricultural lime.', '2026-05-01', '2026-11-30', 'active', NOW()),
('Fisheries Enhancement', 'Distribution of fingerlings for inland aquaculture.', '2026-07-01', '2026-12-31', 'planned', NOW())
ON DUPLICATE KEY UPDATE program_name=program_name;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000001', 'San Rafael', NOW() FROM users WHERE username = 'tboy'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000002', 'San Juan', NOW() FROM users WHERE username = 'gmanalo'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000003', 'San Miguel', NOW() FROM users WHERE username = 'jfernandez'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000004', 'San Pedro', NOW() FROM users WHERE username = 'mbautista'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000005', 'San Roque', NOW() FROM users WHERE username = 'ksantos'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000006', 'San Vicente', NOW() FROM users WHERE username = 'crivera'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000007', 'San Isidro', NOW() FROM users WHERE username = 'lmartin'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000008', 'San Antonio', NOW() FROM users WHERE username = 'pcastro'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000009', 'San Francisco', NOW() FROM users WHERE username = 'rquizon'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000010', 'San Nicolas', NOW() FROM users WHERE username = 'etolentino'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000011', 'San Rafael', NOW() FROM users WHERE username = 'vrosario'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000012', 'San Juan', NOW() FROM users WHERE username = 'nsy'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000013', 'San Miguel', NOW() FROM users WHERE username = 'fmercado'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

INSERT INTO extension_workers (user_id, contact_number, assigned_barangay, created_at) 
SELECT id, '09170000014', 'San Pedro', NOW() FROM users WHERE username = 'dnavarro'
ON DUPLICATE KEY UPDATE assigned_barangay=assigned_barangay;

-- ==========================================
-- Add mock data for Juan Farmer (RSBSA-12345)
-- ==========================================

-- Variables to link records dynamically
SET @juan_farmer_id = (SELECT id FROM farmers WHERE rsbsa_number = 'RSBSA-12345' LIMIT 1);
SET @worker_user_id = (SELECT id FROM users WHERE username = 'worker' LIMIT 1);
SET @admin_user_id = (SELECT id FROM users WHERE username = 'admin' LIMIT 1);

-- 1. Announcements
INSERT INTO announcements (title, content, author_id, target_role, created_at) VALUES
('Free Fertilizer Distribution Notice', 'Good day FESMS Farmers! Please visit the Municipal Agriculture Office starting tomorrow to receive your free fertilizer allocation under the Rice Competitiveness Enhancement Fund. Bring your RSBSA ID or QR Code.', @admin_user_id, 'farmer', NOW()),
('Upcoming Soil Health Seminar', 'We will be conducting a seminar on soil pH and health diagnostics this Friday. Details have been posted in the Training tab. Free soil kits will be given to attendees.', @admin_user_id, 'farmer', NOW()),
('Extreme Weather Advisory', 'Please prepare for heavy rains over the next 48 hours. Clear drainage channels around your crop areas to prevent waterlogging.', @admin_user_id, 'all', NOW())
ON DUPLICATE KEY UPDATE title=title;

-- 2. Trainings
INSERT INTO trainings (title, description, schedule_date, location, organizer_id, status, created_at) VALUES
('Organic Fertilizer Preparation and Application', 'Learn how to make organic compost, vermicompost, and liquid organic fertilizers using farm waste.', '2026-06-25 09:00:00', 'Brgy. Central Multi-Purpose Hall', @worker_user_id, 'upcoming', NOW()),
('Integrated Pest and Disease Management (IPDM)', 'Workshop on biological and eco-friendly control of common pests affecting rice and corn crop.', '2026-06-30 13:00:00', 'Extension Demonstration Farm', @worker_user_id, 'upcoming', NOW()),
('Intro to Modern Hydroponics and Urban Farming', 'Basic course on soil-less crop farming suited for limited space.', '2026-05-15 08:30:00', 'Agri-Tech Center Lab', @worker_user_id, 'completed', NOW()),
('Sustainable Water Management and Irrigation', 'Best practices for rainwater harvesting and drip irrigation maintenance.', '2026-05-20 10:00:00', 'Barangay Central School Ground', @worker_user_id, 'completed', NOW())
ON DUPLICATE KEY UPDATE title=title;

-- 3. Training Attendance (Juan Farmer attended two completed courses)
SET @training_hydroponics_id = (SELECT id FROM trainings WHERE title = 'Intro to Modern Hydroponics and Urban Farming' LIMIT 1);
SET @training_water_id = (SELECT id FROM trainings WHERE title = 'Sustainable Water Management and Irrigation' LIMIT 1);

INSERT INTO training_attendance (training_id, farmer_id, status, created_at) VALUES
(@training_hydroponics_id, @juan_farmer_id, 'attended', NOW()),
(@training_water_id, @juan_farmer_id, 'attended', NOW())
ON DUPLICATE KEY UPDATE training_id=training_id;

-- 4. Assistance Records (Juan Farmer received cacao seedlings, organic fertilizer, and seeds)
SET @program_cacao_id = (SELECT id FROM agricultural_programs WHERE program_name = 'Cacao Production Boost' LIMIT 1);
SET @program_seeds_id = (SELECT id FROM agricultural_programs WHERE program_name = 'Vegetable Seeds Distribution' LIMIT 1);

INSERT INTO assistance_records (farmer_id, program_id, assistance_type, quantity, unit, date_received, distributed_by, notes, created_at) VALUES
(@juan_farmer_id, @program_cacao_id, 'Cacao Seedlings', 25.00, 'pcs', '2026-03-12', @worker_user_id, 'Grafted cacao seedlings for trial planting.', NOW()),
(@juan_farmer_id, @program_seeds_id, 'Organic Fertilizers', 5.00, 'bags', '2026-04-05', @worker_user_id, 'Premium organic compost bags.', NOW()),
(@juan_farmer_id, @program_seeds_id, 'Hybrid Seeds Pack', 2.00, 'kg', '2026-04-05', @worker_user_id, 'Petchay, Tomato, and Eggplant seed variety pack.', NOW())
ON DUPLICATE KEY UPDATE farmer_id=farmer_id;

-- 5. Field Visits (Extension worker visited Juan Farmer twice)
INSERT INTO field_visits (worker_id, farmer_id, visit_date, purpose, notes, farmer_concerns, photo_url, gps_latitude, gps_longitude, created_at) VALUES
(@worker_user_id, @juan_farmer_id, '2026-05-10 10:30:00', 'Routine Crop Assessment', 'Inspected rice crop health. Crops are showing good vegetative growth. Encouraged use of organic fertilizer.', 'Reported slight leafhopper activity. Recommended monitoring.', NULL, 7.190700, 125.455700, NOW()),
(@worker_user_id, @juan_farmer_id, '2026-05-28 14:00:00', 'Soil Sample Collection', 'Collected soil samples from three points of the rice paddy for pH testing.', 'Interested in soil amelioration assistance program.', NULL, 7.190800, 125.455800, NOW())
ON DUPLICATE KEY UPDATE farmer_id=farmer_id;

