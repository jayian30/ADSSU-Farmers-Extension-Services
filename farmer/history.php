<?php
// farmer/history.php
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

// Get farmer id
$stmt = $pdo->prepare("SELECT id FROM farmers WHERE user_id = :uid");
$stmt->execute([':uid' => $user_id]);
$farmer_id = $stmt->fetchColumn();

$page_title = "Service History";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-header mb-4">
    <h2 class="h3 mb-0 text-gray-800">My Service History</h2>
    <p class="text-muted">A record of assistance and field visits related to your farm.</p>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0 glass-card">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h6 class="m-0 font-weight-bold text-success">Assistance Received</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Program</th>
                                <th>Assistance Type</th>
                                <th>Quantity</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($farmer_id) {
                                $assistStmt = $pdo->query("SELECT a.*, p.program_name 
                                    FROM assistance_records a 
                                    JOIN agricultural_programs p ON a.program_id = p.id 
                                    WHERE a.farmer_id = $farmer_id 
                                    ORDER BY a.date_received DESC");
                                $assistance = $assistStmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if(count($assistance) > 0):
                                    foreach($assistance as $a):
                                        $qtyDisplay = $a['quantity'] ? $a['quantity'] . ' ' . $a['unit'] : 'N/A';
                            ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($a['date_received'])) ?></td>
                                <td><?= htmlspecialchars($a['program_name']) ?></td>
                                <td><?= htmlspecialchars($a['assistance_type']) ?></td>
                                <td><?= htmlspecialchars($qtyDisplay) ?></td>
                                <td><?= htmlspecialchars($a['notes'] ?? '-') ?></td>
                            </tr>
                            <?php 
                                    endforeach; 
                                else: 
                            ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No assistance records found.</td></tr>
                            <?php 
                                endif; 
                            } else { 
                            ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Your account is not linked to a farmer profile yet.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="card shadow-sm border-0 glass-card">
            <div class="card-header bg-transparent border-0 pt-4 pb-0">
                <h6 class="m-0 font-weight-bold text-info">Field Visits Logged</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Purpose</th>
                                <th>Notes / Observations</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($farmer_id) {
                                $visitsStmt = $pdo->query("SELECT * FROM field_visits WHERE farmer_id = $farmer_id ORDER BY visit_date DESC");
                                $visits = $visitsStmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if(count($visits) > 0):
                                    foreach($visits as $v):
                            ?>
                            <tr>
                                <td><?= date('M d, Y h:i A', strtotime($v['visit_date'])) ?></td>
                                <td><?= htmlspecialchars($v['purpose']) ?></td>
                                <td><?= nl2br(htmlspecialchars($v['notes'] ?? '-')) ?></td>
                            </tr>
                            <?php 
                                    endforeach; 
                                else: 
                            ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">No field visits recorded.</td></tr>
                            <?php 
                                endif; 
                            } else { 
                            ?>
                            <tr><td colspan="3" class="text-center text-muted py-3">Your account is not linked to a farmer profile yet.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
