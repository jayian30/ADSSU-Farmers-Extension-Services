<?php
// ajax/assistance.php
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
            $sql = "SELECT a.*, f.full_name as farmer_name, p.program_name 
                    FROM assistance_records a 
                    JOIN farmers f ON a.farmer_id = f.id
                    JOIN agricultural_programs p ON a.program_id = p.id";
            
            // If extension worker, could filter by their own distributed records, or show all for transparency
            if ($_SESSION['role'] === 'extension_worker') {
                $sql .= " WHERE a.distributed_by = " . $_SESSION['user_id'];
            }
            
            $sql .= " ORDER BY a.date_received DESC";
            
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
            $sql = "INSERT INTO assistance_records (farmer_id, program_id, assistance_type, quantity, unit, date_received, distributed_by, notes) 
                    VALUES (:farmer_id, :program_id, :assistance_type, :quantity, :unit, :date_received, :distributed_by, :notes)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':farmer_id' => $_POST['farmer_id'],
                ':program_id' => $_POST['program_id'],
                ':assistance_type' => $_POST['assistance_type'],
                ':quantity' => $_POST['quantity'] ?: null,
                ':unit' => $_POST['unit'] ?: null,
                ':date_received' => $_POST['date_received'],
                ':distributed_by' => $_SESSION['user_id'],
                ':notes' => $_POST['notes']
            ]);
            // Notify targeted farmer
            $f_stmt = $pdo->prepare("SELECT user_id FROM farmers WHERE id = ?");
            $f_stmt->execute([$_POST['farmer_id']]);
            $farmer_uid = $f_stmt->fetchColumn();
            if ($farmer_uid) {
                $qty_desc = $_POST['quantity'] ? $_POST['quantity'] . ' ' . ($_POST['unit'] ?? '') : '';
                $msg = "You have been recorded to receive " . trim($qty_desc . " of " . $_POST['assistance_type']) . ".";
                addNotification($pdo, $farmer_uid, "Assistance Distributed", $msg);
            }

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'edit') {
        try {
            $sql = "UPDATE assistance_records SET 
                    farmer_id = :farmer_id, 
                    program_id = :program_id, 
                    assistance_type = :assistance_type, 
                    quantity = :quantity,
                    unit = :unit,
                    date_received = :date_received,
                    notes = :notes
                    WHERE id = :id";
            
            if ($_SESSION['role'] === 'extension_worker') {
                $sql .= " AND distributed_by = " . $_SESSION['user_id'];
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':farmer_id' => $_POST['farmer_id'],
                ':program_id' => $_POST['program_id'],
                ':assistance_type' => $_POST['assistance_type'],
                ':quantity' => $_POST['quantity'] ?: null,
                ':unit' => $_POST['unit'] ?: null,
                ':date_received' => $_POST['date_received'],
                ':notes' => $_POST['notes'],
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
            $sql = "DELETE FROM assistance_records WHERE id = :id";
            if ($_SESSION['role'] === 'extension_worker') {
                $sql .= " AND distributed_by = " . $_SESSION['user_id'];
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $_POST['id']]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error deleting assistance record.']);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
