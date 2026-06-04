<?php
// ajax/programs.php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'list') {
        try {
            $stmt = $pdo->query("SELECT * FROM agricultural_programs ORDER BY created_at DESC");
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
            $sql = "INSERT INTO agricultural_programs (program_name, description, start_date, end_date) 
                    VALUES (:name, :desc, :start, :end)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $_POST['program_name'],
                ':desc' => $_POST['description'],
                ':start' => $_POST['start_date'] ?: null,
                ':end' => $_POST['end_date'] ?: null
            ]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'edit') {
        try {
            $sql = "UPDATE agricultural_programs SET 
                    program_name = :name, 
                    description = :desc, 
                    start_date = :start, 
                    end_date = :end,
                    status = :status 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $_POST['program_name'],
                ':desc' => $_POST['description'],
                ':start' => $_POST['start_date'] ?: null,
                ':end' => $_POST['end_date'] ?: null,
                ':status' => $_POST['status'],
                ':id' => $_POST['id']
            ]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM agricultural_programs WHERE id = :id");
            $stmt->execute([':id' => $_POST['id']]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete program as it is linked to assistance records.']);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
