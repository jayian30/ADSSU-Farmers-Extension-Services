<?php
require_once 'config/database.php';

$db = new Database();
$pdo = $db->getConnection();

// Find the farmer ID for "Criscel Tacastacas"
$stmt = $pdo->prepare("SELECT id FROM farmers WHERE full_name LIKE '%Criscel Tacastacas%' LIMIT 1");
$stmt->execute();
$farmer_id = $stmt->fetchColumn();

if (!$farmer_id) {
    echo "Farmer Criscel Tacastacas not found.\n";
    exit;
}

echo "Found farmer ID: " . $farmer_id . "\n";

// 1. Add Assistance Records
// Get a random program ID
$stmt = $pdo->query("SELECT id FROM agricultural_programs LIMIT 1");
$program_id = $stmt->fetchColumn() ?: 1;

$stmt = $pdo->prepare("INSERT INTO assistance_records (farmer_id, program_id, assistance_type, quantity, unit, notes, date_received) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$farmer_id, $program_id, 'Fertilizer Distribution', 5, 'Sacks', 'Urea fertilizer for rice crop.', '2026-05-10']);
$stmt->execute([$farmer_id, $program_id, 'Seed Subsidy', 2, 'Bags', 'Certified inbred seeds.', '2026-05-15']);
$stmt->execute([$farmer_id, $program_id, 'Cash Assistance', 5000, 'PHP', 'Financial assistance for crop damage.', '2026-06-01']);

// 2. Add Field Visits
$stmt = $pdo->prepare("INSERT INTO field_visits (farmer_id, visit_date, purpose, notes) VALUES (?, ?, ?, ?)");
$stmt->execute([$farmer_id, '2026-04-20 09:30:00', 'Pest and Disease Monitoring', 'Observed minor stem borer damage. Advised on proper pesticide application.']);
$stmt->execute([$farmer_id, '2026-05-05 14:00:00', 'Soil Sampling', 'Collected soil samples for laboratory testing to determine fertilizer requirements.']);
$stmt->execute([$farmer_id, '2026-05-25 10:15:00', 'Harvest Inspection', 'Crop is almost ready for harvest. Expected good yield this season.']);

// 3. Add Trainings
// Create some trainings first
$pdo->exec("INSERT INTO trainings (title, description, schedule_date, location, status) VALUES 
('Modern Rice Farming Techniques', 'Learn the latest methods for maximizing rice yield.', '2026-03-15 08:00:00', 'Municipal Agriculture Office', 'completed'),
('Integrated Pest Management Seminar', 'Eco-friendly pest control strategies.', '2026-05-28 09:00:00', 'Brgy. Central Hall', 'completed'),
('Financial Literacy for Farmers', 'Budgeting and financial management for agriculture.', '2026-06-20 13:00:00', 'Community Center', 'upcoming')
ON DUPLICATE KEY UPDATE title=title;");

// Get the inserted training IDs
$stmt = $pdo->query("SELECT id FROM trainings ORDER BY id DESC LIMIT 3");
$trainings = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Add attendance
$stmt = $pdo->prepare("INSERT INTO training_attendance (training_id, farmer_id, status) VALUES (?, ?, ?)");
if(isset($trainings[0])) $stmt->execute([$trainings[0], $farmer_id, 'attended']);
if(isset($trainings[1])) $stmt->execute([$trainings[1], $farmer_id, 'attended']);
if(isset($trainings[2])) $stmt->execute([$trainings[2], $farmer_id, 'registered']);

echo "Successfully seeded data for Criscel Tacastacas.\n";
?>
