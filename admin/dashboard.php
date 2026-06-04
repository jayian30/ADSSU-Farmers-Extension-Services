<?php
// admin/dashboard.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
requireRole('admin');

$db = new Database();
$pdo = $db->getConnection();

// Fetch statistics
$stats = [
    'total_farmers' => $pdo->query("SELECT COUNT(*) FROM farmers")->fetchColumn(),
    'active_programs' => $pdo->query("SELECT COUNT(*) FROM agricultural_programs WHERE status='active'")->fetchColumn(),
    'upcoming_trainings' => $pdo->query("SELECT COUNT(*) FROM trainings WHERE status='upcoming'")->fetchColumn(),
    'recent_visits' => $pdo->query("SELECT COUNT(*) FROM field_visits WHERE visit_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn()
];

// Fetch recent announcements
$recent_announcements = $pdo->query("SELECT title, created_at FROM announcements ORDER BY created_at DESC LIMIT 5")->fetchAll();

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold" style="color: var(--dark-green);">Admin Dashboard</h2>
            <p class="text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>. Here's what's happening today.</p>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="glass-card p-4 d-flex align-items-center">
                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="fas fa-users fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Total Farmers</h6>
                    <h3 class="fw-bold mb-0"><?php echo number_format($stats['total_farmers']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 d-flex align-items-center">
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background-color: #f59e0b;">
                    <i class="fas fa-seedling fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Active Programs</h6>
                    <h3 class="fw-bold mb-0"><?php echo number_format($stats['active_programs']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 d-flex align-items-center">
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background-color: #3b82f6;">
                    <i class="fas fa-chalkboard-teacher fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Upcoming Trainings</h6>
                    <h3 class="fw-bold mb-0"><?php echo number_format($stats['upcoming_trainings']); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-card p-4 d-flex align-items-center">
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; background-color: #8b5cf6;">
                    <i class="fas fa-map-marked-alt fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Visits (30 Days)</h6>
                    <h3 class="fw-bold mb-0"><?php echo number_format($stats['recent_visits']); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold mb-4">Farmer Registration Trend</h5>
                <canvas id="registrationChart" height="100"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="glass-card p-4 h-100">
                <h5 class="fw-bold mb-4">Recent Announcements</h5>
                <?php if(count($recent_announcements) > 0): ?>
                    <ul class="list-group list-group-flush bg-transparent">
                        <?php foreach($recent_announcements as $announcement): ?>
                        <li class="list-group-item bg-transparent px-0 border-light">
                            <h6 class="mb-1 text-dark"><?php echo htmlspecialchars($announcement['title']); ?></h6>
                            <small class="text-muted"><i class="far fa-clock me-1"></i><?php echo date('M d, Y', strtotime($announcement['created_at'])); ?></small>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">No recent announcements.</p>
                <?php endif; ?>
                <a href="announcements.php" class="btn btn-sm btn-outline-success mt-3 w-100">View All</a>
            </div>
        </div>
    </div>
</div>

<script>
// Chart.js implementation for demo
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('registrationChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'New Registrations',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: '#22C55E',
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
