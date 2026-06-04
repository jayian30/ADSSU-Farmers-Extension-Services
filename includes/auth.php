<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $base_url = (getenv('PORT') !== false || getenv('RAILWAY_STATIC_URL') !== false) ? "" : "/ADSSU Farmers Extension Services";
        header("Location: " . $base_url . "/login.php");
        exit();
    }
}

function hasRole($role) {
    if (!isLoggedIn()) return false;
    if (is_array($role)) {
        return in_array($_SESSION['role'], $role);
    }
    return $_SESSION['role'] === $role;
}

function requireRole($role) {
    if (!hasRole($role)) {
        // Log unauthorized access attempt here if needed
        http_response_code(403);
        die("403 Forbidden: You don't have permission to access this page.");
    }
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verifyCsrfToken($token) {
    return hash_equals($_SESSION['csrf_token'], $token);
}

function logActivity($pdo, $user_id, $action) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $action, $ip]);
}

function addNotification($pdo, $user_id, $title, $message) {
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $title, $message]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
