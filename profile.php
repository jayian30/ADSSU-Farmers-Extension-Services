<?php
// profile.php
require_once 'config/database.php';
require_once 'includes/auth.php';

requireLogin();

$db = new Database();
$pdo = $db->getConnection();
$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT username, full_name, role, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If farmer, fetch farmer data too
$farmer = null;
if ($_SESSION['role'] === 'farmer') {
    $fstmt = $pdo->prepare("SELECT * FROM farmers WHERE user_id = ?");
    $fstmt->execute([$user_id]);
    $farmer = $fstmt->fetch(PDO::FETCH_ASSOC);
}

$page_title = "My Profile";
// We need to adjust base_url logic for header/sidebar if they are included from root
// Normally we include them from subdirectories, so we need to set a flag or define path
$in_root = true; 
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
?>

<div class="content-header mb-4">
    <h2 class="h3 mb-0 text-gray-800">My Profile</h2>
    <p class="text-muted">Manage your personal information and account settings.</p>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 glass-card h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h6 class="m-0 font-weight-bold text-primary">Account Details</h6>
            </div>
            <div class="card-body">
                <form id="profileForm">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Role</label>
                        <input type="text" class="form-control bg-light text-capitalize" value="<?= str_replace('_', ' ', $user['role']) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label text-muted small fw-bold">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label text-muted small fw-bold">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label text-muted small fw-bold">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 glass-card h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h6 class="m-0 font-weight-bold text-danger">Change Password</h6>
            </div>
            <div class="card-body">
                <form id="passwordForm">
                    <input type="hidden" name="action" value="change_password">
                    <div class="mb-3">
                        <label for="current_password" class="form-label text-muted small fw-bold">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label text-muted small fw-bold">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label text-muted small fw-bold">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-danger">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax/profile.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    Swal.fire('Success', 'Profile updated successfully!', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });

    $('#passwordForm').on('submit', function(e) {
        e.preventDefault();
        if ($('#new_password').val() !== $('#confirm_password').val()) {
            Swal.fire('Error', 'New passwords do not match!', 'error');
            return;
        }
        $.ajax({
            url: 'ajax/profile.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    Swal.fire('Success', 'Password changed successfully!', 'success').then(() => {
                        $('#passwordForm')[0].reset();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
