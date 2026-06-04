<?php
// ajax/training_attendance.php
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
        $training_id = $_GET['training_id'] ?? 0;
        try {
            $stmt = $pdo->prepare("
                SELECT ta.id, ta.farmer_id, f.full_name, f.rsbsa_number, ta.status 
                FROM training_attendance ta 
                JOIN farmers f ON ta.farmer_id = f.id 
                WHERE ta.training_id = ?
                ORDER BY f.full_name ASC
            ");
            $stmt->execute([$training_id]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            echo json_encode([]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only Admin can manage attendance lists, or Farmer registering themselves
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $training_id = $_POST['training_id'] ?? 0;
        $farmer_id = $_POST['farmer_id'] ?? 0;
        $status = $_POST['status'] ?? 'registered';

        if (empty($training_id) || empty($farmer_id)) {
            echo json_encode(['success' => false, 'message' => 'Missing training or farmer ID.']);
            exit;
        }

        try {
            // Check duplicate
            $check = $pdo->prepare("SELECT id FROM training_attendance WHERE training_id = ? AND farmer_id = ?");
            $check->execute([$training_id, $farmer_id]);
            if ($check->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Farmer is already registered in this training.']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO training_attendance (training_id, farmer_id, status) VALUES (?, ?, ?)");
            $stmt->execute([$training_id, $farmer_id, $status]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'register_self') {
        // Farmer registering themselves
        if ($_SESSION['role'] !== 'farmer') {
            echo json_encode(['success' => false, 'message' => 'Only farmers can register themselves.']);
            exit;
        }

        $training_id = $_POST['training_id'] ?? 0;
        $user_id = $_SESSION['user_id'];

        // Get farmer ID
        $fstmt = $pdo->prepare("SELECT id FROM farmers WHERE user_id = ?");
        $fstmt->execute([$user_id]);
        $farmer_id = $fstmt->fetchColumn();

        if (!$farmer_id) {
            echo json_encode(['success' => false, 'message' => 'Farmer profile not found.']);
            exit;
        }

        try {
            // Check duplicate
            $check = $pdo->prepare("SELECT id FROM training_attendance WHERE training_id = ? AND farmer_id = ?");
            $check->execute([$training_id, $farmer_id]);
            if ($check->fetch()) {
                echo json_encode(['success' => false, 'message' => 'You are already registered for this training.']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO training_attendance (training_id, farmer_id, status) VALUES (?, ?, 'registered')");
            $stmt->execute([$training_id, $farmer_id]);
            
            // We will hook notifications here later.
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'update') {
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? 'registered';

        try {
            $stmt = $pdo->prepare("UPDATE training_attendance SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'delete') {
        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $id = $_POST['id'] ?? 0;

        try {
            $stmt = $pdo->prepare("DELETE FROM training_attendance WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
