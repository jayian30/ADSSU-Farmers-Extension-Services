<?php
// ajax/users.php
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
            $stmt = $pdo->query("SELECT id, username, full_name, role, email, status, created_at FROM users ORDER BY created_at DESC");
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
            $sql = "INSERT INTO users (username, password, full_name, role, email) 
                    VALUES (:username, :password, :full_name, :role, :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $_POST['username'],
                ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                ':full_name' => $_POST['full_name'],
                ':role' => $_POST['role'],
                ':email' => $_POST['email'] ?: null
            ]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                echo json_encode(['success' => false, 'message' => 'Username already exists.']);
            } else {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        exit;
    }

    if ($action === 'edit') {
        try {
            $updates = [
                'full_name = :full_name',
                'username = :username',
                'email = :email',
                'role = :role',
                'status = :status'
            ];
            
            $params = [
                ':full_name' => $_POST['full_name'],
                ':username' => $_POST['username'],
                ':email' => $_POST['email'] ?: null,
                ':role' => $_POST['role'],
                ':status' => $_POST['status'],
                ':id' => $_POST['id']
            ];

            if (!empty($_POST['password'])) {
                $updates[] = 'password = :password';
                $params[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { 
                echo json_encode(['success' => false, 'message' => 'Username already exists.']);
            } else {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        exit;
    }

    if ($action === 'delete') {
        try {
            if ($_POST['id'] == $_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $_POST['id']]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete user as they have associated records.']);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
