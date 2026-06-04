<?php
// ajax/farmers.php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'list') {
        try {
            $stmt = $pdo->query("SELECT * FROM farmers ORDER BY created_at DESC");
            $farmers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($farmers);
        } catch (PDOException $e) {
            echo json_encode([]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        try {
            $sql = "INSERT INTO farmers (rsbsa_number, full_name, address, barangay, contact_number, farm_type, crop_type, farm_size, registered_by) 
                    VALUES (:rsbsa, :name, :address, :barangay, :contact, :farm_type, :crop_type, :farm_size, :user_id)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':rsbsa' => $_POST['rsbsa_number'] ?: null,
                ':name' => $_POST['full_name'],
                ':address' => $_POST['address'],
                ':barangay' => $_POST['barangay'],
                ':contact' => $_POST['contact_number'],
                ':farm_type' => $_POST['farm_type'],
                ':crop_type' => $_POST['crop_type'],
                ':farm_size' => $_POST['farm_size'] ?: null,
                ':user_id' => $_SESSION['user_id']
            ]);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'edit') {
        try {
            $sql = "UPDATE farmers SET 
                    rsbsa_number = :rsbsa, 
                    full_name = :name, 
                    address = :address, 
                    barangay = :barangay, 
                    contact_number = :contact, 
                    farm_type = :farm_type, 
                    crop_type = :crop_type, 
                    farm_size = :farm_size,
                    status = :status 
                    WHERE id = :id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':rsbsa' => $_POST['rsbsa_number'] ?: null,
                ':name' => $_POST['full_name'],
                ':address' => $_POST['address'],
                ':barangay' => $_POST['barangay'],
                ':contact' => $_POST['contact_number'],
                ':farm_type' => $_POST['farm_type'],
                ':crop_type' => $_POST['crop_type'],
                ':farm_size' => $_POST['farm_size'] ?: null,
                ':status' => $_POST['status'] ?? 'active',
                ':id' => $_POST['id']
            ]);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM farmers WHERE id = :id");
            $stmt->execute([':id' => $_POST['id']]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete farmer as they are linked to other records.']);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
