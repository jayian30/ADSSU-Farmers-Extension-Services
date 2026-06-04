<?php
// extension-worker/dashboard.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
if ($_SESSION['role'] !== 'extension_worker') {
    header("Location: ../index.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

// Fetch statistics
$worker_id = $_SESSION['user_id'];
$stats = [
    'my_farmers' => $pdo->query("SELECT COUNT(*) FROM farmers")->fetchColumn(), // Could filter by worker if needed
    'my_visits' => $pdo->query("SELECT COUNT(*) FROM field_visits WHERE worker_id = $worker_id")->fetchColumn(),
    'trainings' => $pdo->query("SELECT COUNT(*) FROM trainings WHERE status IN ('upcoming', 'ongoing')")->fetchColumn()
];

$page_title = "Extension Worker Dashboard";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-header mb-4">
    <h2 class="h3 mb-0 text-gray-800">Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?>!</h2>
    <p class="text-muted">Here's your field activity overview.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card glass-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Registered Farmers</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['my_farmers']) ?></div>
                    </div>
                    <div class="icon-circle bg-success bg-opacity-10 text-success">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card glass-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">My Field Visits</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['my_visits']) ?></div>
                    </div>
                    <div class="icon-circle bg-info bg-opacity-10 text-info">
                        <i class="fas fa-map-marked-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6">
        <div class="card stat-card glass-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Upcoming Trainings</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['trainings']) ?></div>
                    </div>
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 glass-card">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h6 class="m-0 font-weight-bold text-primary">Recent Field Visits</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Farmer</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recentVisits = $pdo->query("SELECT v.*, f.full_name FROM field_visits v JOIN farmers f ON v.farmer_id = f.id WHERE v.worker_id = $worker_id ORDER BY v.visit_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
                            if(count($recentVisits) > 0):
                                foreach($recentVisits as $v):
                            ?>
                            <tr>
                                <td><?= date('M d, Y h:i A', strtotime($v['visit_date'])) ?></td>
                                <td><?= htmlspecialchars($v['full_name']) ?></td>
                                <td><?= htmlspecialchars($v['purpose']) ?></td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">No recent visits.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
