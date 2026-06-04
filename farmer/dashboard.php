<?php
// farmer/dashboard.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
if ($_SESSION['role'] !== 'farmer') {
    header("Location: ../index.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$user_id = $_SESSION['user_id'];

// Get farmer record
$stmt = $pdo->prepare("SELECT id, full_name, rsbsa_number, status FROM farmers WHERE user_id = :uid");
$stmt->execute([':uid' => $user_id]);
$farmer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$farmer) {
    // If not linked yet, maybe show a message
    $farmer = ['id' => 0, 'full_name' => $_SESSION['full_name'], 'rsbsa_number' => 'Not linked yet', 'status' => 'pending'];
}

// Stats
$farmer_id = $farmer['id'];
$stats = [
    'services' => $pdo->query("SELECT COUNT(*) FROM assistance_records WHERE farmer_id = $farmer_id")->fetchColumn(),
    'trainings' => $pdo->query("SELECT COUNT(*) FROM training_attendance WHERE farmer_id = $farmer_id")->fetchColumn(),
    'visits' => $pdo->query("SELECT COUNT(*) FROM field_visits WHERE farmer_id = $farmer_id")->fetchColumn()
];

$page_title = "Farmer Dashboard";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-header mb-4">
    <h2 class="h3 mb-0 text-gray-800">Welcome, <?= htmlspecialchars($farmer['full_name']) ?>!</h2>
    <p class="text-muted">RSBSA No: <?= htmlspecialchars($farmer['rsbsa_number']) ?></p>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card stat-card glass-card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Assistance Received</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['services']) ?></div>
                    </div>
                    <div class="icon-circle bg-success bg-opacity-10 text-success">
                        <i class="fas fa-seedling fa-2x"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Trainings Attended</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['trainings']) ?></div>
                    </div>
                    <div class="icon-circle bg-info bg-opacity-10 text-info">
                        <i class="fas fa-certificate fa-2x"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Field Visits Logged</div>
                        <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['visits']) ?></div>
                    </div>
                    <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-map-marked-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Announcements -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 glass-card h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h6 class="m-0 font-weight-bold text-primary">Recent Announcements</h6>
            </div>
            <div class="card-body">
                <?php
                $announcements = $pdo->query("SELECT * FROM announcements WHERE target_role IN ('all', 'farmer') ORDER BY created_at DESC LIMIT 3")->fetchAll();
                if(count($announcements) > 0):
                    foreach($announcements as $a):
                ?>
                <div class="mb-3 border-bottom pb-2">
                    <div class="fw-bold"><?= htmlspecialchars($a['title']) ?></div>
                    <div class="small text-muted mb-1"><?= date('M d, Y h:i A', strtotime($a['created_at'])) ?></div>
                    <div class="small text-dark"><?= nl2br(htmlspecialchars($a['content'])) ?></div>
                </div>
                <?php endforeach; else: ?>
                <div class="text-center text-muted py-3">No recent announcements.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Trainings -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm border-0 glass-card h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h6 class="m-0 font-weight-bold text-success">Upcoming Trainings</h6>
            </div>
            <div class="card-body">
                <?php
                $upcoming = $pdo->query("SELECT * FROM trainings WHERE status = 'upcoming' ORDER BY schedule_date ASC LIMIT 3")->fetchAll();
                if(count($upcoming) > 0):
                    foreach($upcoming as $t):
                ?>
                <div class="mb-3 border-bottom pb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold"><?= htmlspecialchars($t['title']) ?></div>
                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">Upcoming</span>
                    </div>
                    <div class="small text-muted"><i class="fas fa-calendar-alt me-1"></i> <?= date('M d, Y h:i A', strtotime($t['schedule_date'])) ?></div>
                    <div class="small text-muted"><i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($t['location']) ?></div>
                </div>
                <?php endforeach; else: ?>
                <div class="text-center text-muted py-3">No upcoming trainings.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
