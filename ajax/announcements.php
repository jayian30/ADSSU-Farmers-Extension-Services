<?php
// ajax/announcements.php
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
            $stmt = $pdo->query("
                SELECT a.*, u.full_name as author_name 
                FROM announcements a 
                LEFT JOIN users u ON a.author_id = u.id 
                ORDER BY a.created_at DESC
            ");
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
            $sql = "INSERT INTO announcements (title, content, author_id, target_role) 
                    VALUES (:title, :content, :author_id, :target_role)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $_POST['title'],
                ':content' => $_POST['content'],
                ':author_id' => $_SESSION['user_id'],
                ':target_role' => $_POST['target_role'] ?? 'all'
            ]);
            $announcementId = $pdo->lastInsertId();

            $target_role = $_POST['target_role'] ?? 'all';
            if ($target_role === 'all') {
                $ustmt = $pdo->query("SELECT id FROM users");
            } else {
                $ustmt = $pdo->prepare("SELECT id FROM users WHERE role = ?");
                $ustmt->execute([$target_role]);
            }
            $targetUsers = $ustmt->fetchAll(PDO::FETCH_COLUMN);

            foreach ($targetUsers as $uid) {
                if ($uid != $_SESSION['user_id']) {
                    addNotification($pdo, $uid, "New Announcement: " . $_POST['title'], $_POST['content']);
                }
            }

            echo json_encode(['success' => true, 'id' => $announcementId]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'edit') {
        try {
            $sql = "UPDATE announcements SET 
                    title = :title, 
                    content = :content, 
                    target_role = :target_role 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title' => $_POST['title'],
                ':content' => $_POST['content'],
                ':target_role' => $_POST['target_role'] ?? 'all',
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
            $stmt = $pdo->prepare("DELETE FROM announcements WHERE id = :id");
            $stmt->execute([':id' => $_POST['id']]);
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
