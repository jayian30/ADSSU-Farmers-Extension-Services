<?php
require 'config/database.php';
$db = new Database();
$pdo = $db->getConnection();
$stmt = $pdo->query('SELECT v.*, f.full_name as farmer_name FROM field_visits v JOIN farmers f ON v.farmer_id = f.id WHERE v.worker_id = 7');
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
