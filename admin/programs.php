<?php
// admin/programs.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
requireRole('admin');

$db = new Database();
$pdo = $db->getConnection();

$page_title = "Agricultural Programs";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800">Agricultural Programs</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Programs</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#programModal" onclick="resetForm()">
        <i class="fas fa-plus me-2"></i>Add New Program
    </button>
</div>

<!-- Programs Table Card -->
<div class="card shadow-sm border-0 glass-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="programsTable">
                <thead class="table-light">
                    <tr>
                        <th>Program Name</th>
                        <th>Duration</th>
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

<!-- Add/Edit Program Modal -->
<div class="modal fade" id="programModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="programModalLabel">Add New Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="programForm">
                    <input type="hidden" id="program_id" name="id">
                    <input type="hidden" id="action" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="program_name" class="form-label">Program Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="program_name" name="program_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                        </div>
                    </div>

                    <div class="mb-3" id="statusGroup" style="display:none;">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="planned">Planned</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveProgram()">Save Program</button>
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
                Are you sure you want to delete this program?
            </div>
            <div class="modal-footer border-0">
                <input type="hidden" id="delete_program_id">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
let programsList = [];

$(document).ready(function() {
    loadPrograms();
});

function loadPrograms() {
    $.ajax({
        url: '../ajax/programs.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            programsList = response;
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="4" class="text-center text-muted py-4">No programs found.</td></tr>';
            } else {
                response.forEach(p => {
                    let statusClass = 'bg-secondary';
                    if(p.status === 'active') statusClass = 'bg-success';
                    else if(p.status === 'completed') statusClass = 'bg-primary';
                    
                    let statusBadge = `<span class="badge ${statusClass} bg-opacity-10 text-${statusClass.replace('bg-','')} px-2 py-1 rounded-pill text-capitalize">${p.status}</span>`;
                    
                    let duration = (p.start_date || 'TBD') + ' to ' + (p.end_date || 'TBD');
                    
                    html += `
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">${p.program_name}</div>
                                <div class="text-muted small text-truncate" style="max-width: 300px;">${p.description || ''}</div>
                            </td>
                            <td>${duration}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <button class="btn btn-sm btn-light text-primary me-1" onclick="editProgram(${p.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger" onclick="deleteProgram(${p.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#programsTable tbody').html(html);
        }
    });
}

function resetForm() {
    $('#programForm')[0].reset();
    $('#program_id').val('');
    $('#action').val('add');
    $('#programModalLabel').text('Add New Program');
    $('#statusGroup').hide();
}

function editProgram(id) {
    const p = programsList.find(item => item.id == id);
    if (!p) return;
    resetForm();
    $('#program_id').val(p.id);
    $('#action').val('edit');
    $('#program_name').val(p.program_name);
    $('#description').val(p.description);
    $('#start_date').val(p.start_date);
    $('#end_date').val(p.end_date);
    $('#status').val(p.status);
    
    $('#programModalLabel').text('Edit Program');
    $('#statusGroup').show();
    $('#programModal').modal('show');
}

function saveProgram() {
    if(!$('#programForm')[0].checkValidity()) {
        $('#programForm')[0].reportValidity();
        return;
    }
    
    $.ajax({
        url: '../ajax/programs.php',
        type: 'POST',
        data: $('#programForm').serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#programModal').modal('hide');
                loadPrograms();
            } else {
                alert(response.message || 'Error saving program.');
            }
        }
    });
}

function deleteProgram(id) {
    $('#delete_program_id').val(id);
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    $.ajax({
        url: '../ajax/programs.php',
        type: 'POST',
        data: { action: 'delete', id: $('#delete_program_id').val() },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#deleteModal').modal('hide');
                loadPrograms();
            } else {
                alert(response.message || 'Error deleting program.');
            }
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
