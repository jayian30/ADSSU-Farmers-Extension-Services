<?php
// ajax/auth.php
require_once '../config/database.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Verify CSRF
    if (empty($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token.']);
        exit;
    }

    $db = new Database();
    $pdo = $db->getConnection();

    if ($_POST['action'] === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                logActivity($pdo, $user['id'], 'User logged in');

                $redirect = '';
                if ($user['role'] === 'admin') $redirect = 'admin/dashboard.php';
                else if ($user['role'] === 'extension_worker') $redirect = 'extension-worker/dashboard.php';
                else $redirect = 'farmer/dashboard.php';

                echo json_encode(['status' => 'success', 'redirect' => $redirect]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid username or password, or account is inactive.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        }
    } 
    else if ($_POST['action'] === 'signup') {
        $full_name = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($full_name) || empty($username) || empty($password)) {
            echo json_encode(['status' => 'error', 'message' => 'Please fill in all fields.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                echo json_encode(['status' => 'error', 'message' => 'Username is already taken.']);
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, 'farmer')");
            $stmt->execute([$username, $hashedPassword, $full_name]);
            $user_id = $pdo->lastInsertId();

            // Immediately create a farmer profile so they show up in the Admin's Farmers list
            $stmt2 = $pdo->prepare("INSERT INTO farmers (user_id, full_name, address, barangay, status) VALUES (?, ?, 'Pending Update', 'Pending Update', 'active')");
            $stmt2->execute([$user_id, $full_name]);

            logActivity($pdo, $user_id, 'User registered via signup page');

            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error.']);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
