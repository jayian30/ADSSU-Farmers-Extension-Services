<?php
// ajax/visits.php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn() || ($_SESSION['role'] !== 'extension_worker' && $_SESSION['role'] !== 'admin')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'list') {
        try {
            $sql = "SELECT v.*, f.full_name as farmer_name 
                    FROM field_visits v 
                    JOIN farmers f ON v.farmer_id = f.id";
            
            // If it's an extension worker, optionally filter by their own visits
            // For now, let's just let them see all, or filter by worker_id:
            if ($_SESSION['role'] === 'extension_worker') {
                $sql .= " WHERE v.worker_id = " . $_SESSION['user_id'];
            }
            
            $sql .= " ORDER BY v.visit_date DESC";
            
            $stmt = $pdo->query($sql);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
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
            $sql = "INSERT INTO field_visits (worker_id, farmer_id, visit_date, purpose, notes, farmer_concerns, gps_latitude, gps_longitude) 
                    VALUES (:worker_id, :farmer_id, :visit_date, :purpose, :notes, :concerns, :lat, :lng)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':worker_id' => $_SESSION['user_id'],
                ':farmer_id' => $_POST['farmer_id'],
                ':visit_date' => $_POST['visit_date'],
                ':purpose' => $_POST['purpose'],
                ':notes' => $_POST['notes'],
                ':concerns' => $_POST['farmer_concerns'],
                ':lat' => $_POST['gps_latitude'] ?: null,
                ':lng' => $_POST['gps_longitude'] ?: null
            ]);
            // Notify targeted farmer
            $f_stmt = $pdo->prepare("SELECT user_id FROM farmers WHERE id = ?");
            $f_stmt->execute([$_POST['farmer_id']]);
            $farmer_uid = $f_stmt->fetchColumn();
            if ($farmer_uid) {
                $msg = "An extension worker logged a field visit to your farm on " . date('M d, Y', strtotime($_POST['visit_date'])) . " for the purpose of: " . $_POST['purpose'] . ".";
                addNotification($pdo, $farmer_uid, "Field Visit Logged", $msg);
            }

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'edit') {
        try {
            $sql = "UPDATE field_visits SET 
                    farmer_id = :farmer_id, 
                    visit_date = :visit_date, 
                    purpose = :purpose, 
                    notes = :notes,
                    farmer_concerns = :concerns,
                    gps_latitude = :lat,
                    gps_longitude = :lng
                    WHERE id = :id";
            
            // Extra security: ensure worker owns this record, unless admin
            if ($_SESSION['role'] === 'extension_worker') {
                $sql .= " AND worker_id = " . $_SESSION['user_id'];
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':farmer_id' => $_POST['farmer_id'],
                ':visit_date' => $_POST['visit_date'],
                ':purpose' => $_POST['purpose'],
                ':notes' => $_POST['notes'],
                ':concerns' => $_POST['farmer_concerns'],
                ':lat' => $_POST['gps_latitude'] ?: null,
                ':lng' => $_POST['gps_longitude'] ?: null,
                ':id' => $_POST['id']
            ]);
            
            if($stmt->rowCount() > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Record not found or unauthorized']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'delete') {
        try {
            $sql = "DELETE FROM field_visits WHERE id = :id";
            if ($_SESSION['role'] === 'extension_worker') {
                $sql .= " AND worker_id = " . $_SESSION['user_id'];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $_POST['id']]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error deleting visit']);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
