<?php
// ajax/trainings.php
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
            $stmt = $pdo->query("SELECT * FROM trainings ORDER BY schedule_date DESC");
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
            $sql = "INSERT INTO trainings (title, description, schedule_date, location, organizer_id) 
                    VALUES (:title, :desc, :schedule, :location, :organizer_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $_POST['title'],
                ':desc' => $_POST['description'],
                ':schedule' => $_POST['schedule_date'],
                ':location' => $_POST['location'],
                ':organizer_id' => $_SESSION['user_id']
            ]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'edit') {
        try {
            $sql = "UPDATE trainings SET 
                    title = :title, 
                    description = :desc, 
                    schedule_date = :schedule, 
                    location = :location,
                    status = :status 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $_POST['title'],
                ':desc' => $_POST['description'],
                ':schedule' => $_POST['schedule_date'],
                ':location' => $_POST['location'],
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
            $stmt = $pdo->prepare("DELETE FROM trainings WHERE id = :id");
            $stmt->execute([':id' => $_POST['id']]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete training due to existing attendance records.']);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
