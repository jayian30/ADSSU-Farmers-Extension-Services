<?php
// farmer/trainings.php
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

$page_title = "My Trainings";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-header mb-4">
    <h2 class="h3 mb-0 text-gray-800">My Trainings & Seminars</h2>
    <p class="text-muted">Track your registered and attended trainings.</p>
</div>

<ul class="nav nav-tabs mb-4" id="trainingTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold text-success" id="my-trainings-tab" data-bs-toggle="tab" data-bs-target="#my-trainings" type="button" role="tab" aria-selected="true">My Scheduled & Attended Trainings</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-success" id="available-trainings-tab" data-bs-toggle="tab" data-bs-target="#available-trainings" type="button" role="tab" aria-selected="false">Available Trainings</button>
    </li>
</ul>

<div class="tab-content" id="trainingTabsContent">
    <!-- Tab 1: My Trainings -->
    <div class="tab-pane fade show active" id="my-trainings" role="tabpanel" aria-labelledby="my-trainings-tab">
        <div class="card shadow-sm border-0 glass-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Training Title</th>
                                <th>Schedule</th>
                                <th>Location</th>
                                <th>My Status</th>
                                <th>Training Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($farmer_id) {
                                $trainingsStmt = $pdo->query("SELECT t.title, t.schedule_date, t.location, t.status as training_status, 
                                    a.status as attendance_status 
                                    FROM training_attendance a 
                                    JOIN trainings t ON a.training_id = t.id 
                                    WHERE a.farmer_id = $farmer_id 
                                    ORDER BY t.schedule_date DESC");
                                $trainings = $trainingsStmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if(count($trainings) > 0):
                                    foreach($trainings as $t):
                                        $attStatusClass = 'bg-secondary';
                                        if($t['attendance_status'] === 'registered') $attStatusClass = 'bg-primary';
                                        if($t['attendance_status'] === 'attended') $attStatusClass = 'bg-success';
                                        if($t['attendance_status'] === 'absent') $attStatusClass = 'bg-danger';

                                        $tStatusClass = 'bg-secondary';
                                        if($t['training_status'] === 'upcoming') $tStatusClass = 'bg-info';
                                        if($t['training_status'] === 'ongoing') $tStatusClass = 'bg-warning text-dark';
                                        if($t['training_status'] === 'completed') $tStatusClass = 'bg-success';
                                        if($t['training_status'] === 'cancelled') $tStatusClass = 'bg-danger';
                            ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($t['title']) ?></td>
                                <td><?= date('M d, Y h:i A', strtotime($t['schedule_date'])) ?></td>
                                <td><?= htmlspecialchars($t['location']) ?></td>
                                <td><span class="badge <?= $attStatusClass ?> bg-opacity-10 text-<?= str_replace('bg-', '', $attStatusClass) ?> px-2 py-1 rounded-pill text-capitalize"><?= $t['attendance_status'] ?></span></td>
                                <td><span class="badge <?= $tStatusClass ?> bg-opacity-10 text-<?= str_replace('bg-', '', str_replace(' text-dark', '', $tStatusClass)) ?> px-2 py-1 rounded-pill text-capitalize"><?= $t['training_status'] ?></span></td>
                            </tr>
                            <?php 
                                    endforeach; 
                                else: 
                            ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">No training records found.</td></tr>
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

    <!-- Tab 2: Available Trainings -->
    <div class="tab-pane fade" id="available-trainings" role="tabpanel" aria-labelledby="available-trainings-tab">
        <div class="card shadow-sm border-0 glass-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Training Title</th>
                                <th>Schedule</th>
                                <th>Location</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($farmer_id) {
                                $availableStmt = $pdo->query("SELECT * FROM trainings 
                                    WHERE status = 'upcoming' 
                                    AND id NOT IN (SELECT training_id FROM training_attendance WHERE farmer_id = $farmer_id) 
                                    ORDER BY schedule_date ASC");
                                $available = $availableStmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                if(count($available) > 0):
                                    foreach($available as $at):
                            ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($at['title']) ?></td>
                                <td><?= date('M d, Y h:i A', strtotime($at['schedule_date'])) ?></td>
                                <td><?= htmlspecialchars($at['location']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-success rounded-pill px-3" onclick="registerTraining(<?= $at['id'] ?>)">Register Now</button>
                                </td>
                            </tr>
                            <?php 
                                    endforeach; 
                                else: 
                            ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">No new upcoming trainings available.</td></tr>
                            <?php 
                                endif; 
                            } else { 
                            ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Your account is not linked to a farmer profile yet.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function registerTraining(trainingId) {
    Swal.fire({
        title: 'Confirm Registration',
        text: 'Are you sure you want to register for this training?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22C55E',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Register'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../ajax/training_attendance.php',
                type: 'POST',
                data: { action: 'register_self', training_id: trainingId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', 'Registered successfully!', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Error registering.', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Server error occurred.', 'error');
                }
            });
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
