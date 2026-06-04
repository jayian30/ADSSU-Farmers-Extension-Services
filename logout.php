<?php
// logout.php
require_once 'config/database.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    try {
        $db = new Database();
        logActivity($db->getConnection(), $_SESSION['user_id'], 'User logged out');
    } catch (Exception $e) {
        // Ignore DB error on logout
    }
}

session_unset();
session_destroy();
$base_url = (getenv('PORT') !== false || getenv('RAILWAY_STATIC_URL') !== false) ? "" : "/ADSSU Farmers Extension Services";
header("Location: " . $base_url . "/index.php");
exit();
?>
