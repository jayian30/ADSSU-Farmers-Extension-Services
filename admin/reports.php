<?php
// admin/reports.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
requireRole('admin');

$db = new Database();
$pdo = $db->getConnection();

$page_title = "Reports & Analytics";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Prepare data for reports
$farmersByBarangay = $pdo->query("SELECT barangay, COUNT(*) as count FROM farmers GROUP BY barangay")->fetchAll(PDO::FETCH_ASSOC);
$farmersByCrop = $pdo->query("SELECT crop_type, COUNT(*) as count FROM farmers WHERE crop_type IS NOT NULL AND crop_type != '' GROUP BY crop_type ORDER BY count DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800">Reports & Analytics</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reports</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-secondary" onclick="window.print()">
        <i class="fas fa-print me-2"></i>Print Report
    </button>
</div>

<style>
    @media print {
        .sidebar, .top-header, .btn, .breadcrumb { display: none !important; }
        .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
    }
</style>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 glass-card h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h6 class="m-0 font-weight-bold text-primary">Farmers per Barangay</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Barangay</th>
                                <th>Total Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($farmersByBarangay as $fb): ?>
                            <tr>
                                <td><?= htmlspecialchars($fb['barangay']) ?></td>
                                <td><?= number_format($fb['count']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 glass-card h-100">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h6 class="m-0 font-weight-bold text-success">Top Crop Types</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Crop Type</th>
                                <th>Number of Farmers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($farmersByCrop as $fc): ?>
                            <tr>
                                <td><?= htmlspecialchars($fc['crop_type']) ?></td>
                                <td><?= number_format($fc['count']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12">
        <div class="card shadow-sm border-0 glass-card">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-info">Recent Field Visits Overview</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Extension Worker</th>
                                <th>Farmer</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $visits = $pdo->query("
                                SELECT v.visit_date, v.purpose, f.full_name as farmer, u.full_name as worker
                                FROM field_visits v
                                JOIN farmers f ON v.farmer_id = f.id
                                JOIN users u ON v.worker_id = u.id
                                ORDER BY v.visit_date DESC LIMIT 10
                            ")->fetchAll(PDO::FETCH_ASSOC);
                            
                            foreach($visits as $v):
                            ?>
                            <tr>
                                <td><?= date('Y-m-d', strtotime($v['visit_date'])) ?></td>
                                <td><?= htmlspecialchars($v['worker']) ?></td>
                                <td><?= htmlspecialchars($v['farmer']) ?></td>
                                <td><?= htmlspecialchars($v['purpose']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
