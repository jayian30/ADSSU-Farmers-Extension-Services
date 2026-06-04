<?php
// admin/trainings.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
requireRole('admin');

$db = new Database();
$pdo = $db->getConnection();

// Fetch farmers for attendance dropdown
$farmers = $pdo->query("SELECT id, full_name, rsbsa_number FROM farmers WHERE status = 'active' ORDER BY full_name ASC")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "Trainings & Seminars";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800">Trainings & Seminars</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Trainings</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#trainingModal" onclick="resetForm()">
        <i class="fas fa-plus me-2"></i>Schedule Training
    </button>
</div>

<!-- Trainings Table Card -->
<div class="card shadow-sm border-0 glass-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="trainingsTable">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Schedule</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Populated by AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Training Modal -->
<div class="modal fade" id="trainingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="trainingModalLabel">Schedule Training</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="trainingForm">
                    <input type="hidden" id="training_id" name="id">
                    <input type="hidden" id="action" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Training Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="schedule_date" class="form-label">Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="schedule_date" name="schedule_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                    </div>

                    <div class="mb-3" id="statusGroup" style="display:none;">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveTraining()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this training?
            </div>
            <div class="modal-footer border-0">
                <input type="hidden" id="delete_training_id">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Attendance Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="attendanceModalLabel">Manage Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="attendance_training_id">
                
                <!-- Add Farmer to Training Form -->
                <form id="addAttendanceForm" class="row g-3 align-items-end mb-4 border-bottom pb-4">
                    <div class="col-md-6">
                        <label for="attendee_farmer_id" class="form-label small fw-bold text-muted">Select Farmer</label>
                        <select class="form-select" id="attendee_farmer_id" required>
                            <option value="">Choose Farmer...</option>
                            <?php foreach ($farmers as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['full_name']) ?> (<?= htmlspecialchars($f['rsbsa_number'] ?? 'No RSBSA') ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="attendee_status" class="form-label small fw-bold text-muted">Status</label>
                        <select class="form-select" id="attendee_status" required>
                            <option value="registered">Registered</option>
                            <option value="attended">Attended</option>
                            <option value="absent">Absent</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success w-100" onclick="addFarmerToTraining()">Add</button>
                    </div>
                </form>

                <!-- Attendance Table -->
                <div class="table-responsive" style="max-height: 350px;">
                    <table class="table table-sm table-hover align-middle" id="attendanceTable">
                        <thead class="table-light">
                            <tr>
                                <th>Farmer Name</th>
                                <th>RSBSA No.</th>
                                <th style="width: 150px;">Status</th>
                                <th style="width: 50px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populated by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let trainingsList = [];

$(document).ready(function() {
    loadTrainings();
});

function loadTrainings() {
    $.ajax({
        url: '../ajax/trainings.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            trainingsList = response;
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="5" class="text-center text-muted py-4">No trainings found.</td></tr>';
            } else {
                response.forEach(t => {
                    let statusClass = 'bg-primary';
                    if(t.status === 'ongoing') statusClass = 'bg-warning text-dark';
                    else if(t.status === 'completed') statusClass = 'bg-success';
                    else if(t.status === 'cancelled') statusClass = 'bg-danger';
                    
                    let statusBadge = `<span class="badge ${statusClass} bg-opacity-10 text-${statusClass.includes('text-dark') ? 'dark' : statusClass.replace('bg-','')} px-2 py-1 rounded-pill text-capitalize">${t.status}</span>`;
                    
                    let date = new Date(t.schedule_date).toLocaleString();
                    
                    html += `
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">${t.title}</div>
                                <div class="text-muted small text-truncate" style="max-width: 250px;">${t.description || ''}</div>
                            </td>
                            <td>${date}</td>
                            <td>${t.location}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <button class="btn btn-sm btn-light text-success me-1" onclick="manageAttendance(${t.id}, '${t.title.replace(/'/g, "\\'")}')" title="Manage Attendance">
                                    <i class="fas fa-users"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-primary me-1" onclick="editTraining(${t.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger" onclick="deleteTraining(${t.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#trainingsTable tbody').html(html);
        }
    });
}

function resetForm() {
    $('#trainingForm')[0].reset();
    $('#training_id').val('');
    $('#action').val('add');
    $('#trainingModalLabel').text('Schedule Training');
    $('#statusGroup').hide();
}

function editTraining(id) {
    const t = trainingsList.find(item => item.id == id);
    if (!t) return;
    resetForm();
    $('#training_id').val(t.id);
    $('#action').val('edit');
    $('#title').val(t.title);
    $('#description').val(t.description);
    $('#schedule_date').val(t.schedule_date.replace(' ', 'T'));
    $('#location').val(t.location);
    $('#status').val(t.status);
    
    $('#trainingModalLabel').text('Edit Training');
    $('#statusGroup').show();
    $('#trainingModal').modal('show');
}

function saveTraining() {
    if(!$('#trainingForm')[0].checkValidity()) {
        $('#trainingForm')[0].reportValidity();
        return;
    }
    
    $.ajax({
        url: '../ajax/trainings.php',
        type: 'POST',
        data: $('#trainingForm').serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#trainingModal').modal('hide');
                loadTrainings();
            } else {
                alert(response.message || 'Error saving training.');
            }
        }
    });
}

function deleteTraining(id) {
    $('#delete_training_id').val(id);
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    $.ajax({
        url: '../ajax/trainings.php',
        type: 'POST',
        data: { action: 'delete', id: $('#delete_training_id').val() },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#deleteModal').modal('hide');
                loadTrainings();
            } else {
                alert(response.message || 'Error deleting training.');
            }
        }
    });
}

function manageAttendance(trainingId, title) {
    $('#attendance_training_id').val(trainingId);
    $('#attendanceModalLabel').text('Manage Attendance: ' + title);
    $('#addAttendanceForm')[0].reset();
    loadAttendance(trainingId);
    $('#attendanceModal').modal('show');
}

function loadAttendance(trainingId) {
    $.ajax({
        url: '../ajax/training_attendance.php',
        type: 'GET',
        data: { action: 'list', training_id: trainingId },
        dataType: 'json',
        success: function(response) {
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="4" class="text-center text-muted py-3">No farmers registered in this training.</td></tr>';
            } else {
                response.forEach(row => {
                    let regSelected = row.status === 'registered' ? 'selected' : '';
                    let attSelected = row.status === 'attended' ? 'selected' : '';
                    let absSelected = row.status === 'absent' ? 'selected' : '';
                    
                    html += `
                        <tr>
                            <td class="fw-semibold">${row.full_name}</td>
                            <td>${row.rsbsa_number || 'N/A'}</td>
                            <td>
                                <select class="form-select form-select-sm" onchange="updateAttendanceStatus(${row.id}, this.value)">
                                    <option value="registered" ${regSelected}>Registered</option>
                                    <option value="attended" ${attSelected}>Attended</option>
                                    <option value="absent" ${absSelected}>Absent</option>
                                </select>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light text-danger" onclick="removeFarmerFromTraining(${row.id})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#attendanceTable tbody').html(html);
        }
    });
}

function addFarmerToTraining() {
    let trainingId = $('#attendance_training_id').val();
    let farmerId = $('#attendee_farmer_id').val();
    let status = $('#attendee_status').val();

    if(!farmerId) {
        alert("Please select a farmer.");
        return;
    }

    $.ajax({
        url: '../ajax/training_attendance.php',
        type: 'POST',
        data: {
            action: 'add',
            training_id: trainingId,
            farmer_id: farmerId,
            status: status
        },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#attendee_farmer_id').val('');
                loadAttendance(trainingId);
                loadTrainings();
            } else {
                alert(response.message || "Error adding farmer.");
            }
        }
    });
}

function updateAttendanceStatus(id, status) {
    $.ajax({
        url: '../ajax/training_attendance.php',
        type: 'POST',
        data: { action: 'update', id: id, status: status },
        dataType: 'json',
        success: function(response) {
            if(!response.success) {
                alert(response.message || "Error updating status.");
            }
        }
    });
}

function removeFarmerFromTraining(id) {
    if(confirm("Are you sure you want to remove this farmer from the training?")) {
        let trainingId = $('#attendance_training_id').val();
        $.ajax({
            url: '../ajax/training_attendance.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    loadAttendance(trainingId);
                } else {
                    alert(response.message || "Error removing farmer.");
                }
            }
        });
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
