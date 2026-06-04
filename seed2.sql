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
