<?php
// ajax/profile.php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$db = new Database();
$pdo = $db->getConnection();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        try {
            $username = trim($_POST['username']);
            $full_name = trim($_POST['full_name']);
            $email = trim($_POST['email']);

            // Check if username is taken by someone else
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $user_id]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Username is already taken.']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, email = ? WHERE id = ?");
            $stmt->execute([$username, $full_name, $email, $user_id]);

            // Update session
            $_SESSION['username'] = $username;
            $_SESSION['full_name'] = $full_name;

            // Also update farmer table if role is farmer
            if ($_SESSION['role'] === 'farmer') {
                $fstmt = $pdo->prepare("UPDATE farmers SET full_name = ? WHERE user_id = ?");
                $fstmt->execute([$full_name, $user_id]);
            }

            logActivity($pdo, $user_id, 'Updated profile information');
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error updating profile.']);
        }
        exit;
    }

    if ($action === 'change_password') {
        try {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];

            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            if (!password_verify($current_password, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Incorrect current password.']);
                exit;
            }

            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $user_id]);

            logActivity($pdo, $user_id, 'Changed password');
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error changing password.']);
        }
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
