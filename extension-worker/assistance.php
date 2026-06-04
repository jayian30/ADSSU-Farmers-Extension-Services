<?php
// extension-worker/assistance.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
if ($_SESSION['role'] !== 'extension_worker' && $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$page_title = "Assistance Records";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Fetch farmers for dropdown
$farmersStmt = $pdo->query("SELECT id, full_name FROM farmers ORDER BY full_name ASC");
$farmers = $farmersStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch programs for dropdown
$programsStmt = $pdo->query("SELECT id, program_name FROM agricultural_programs WHERE status != 'completed' ORDER BY program_name ASC");
$programs = $programsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800">Assistance Distribution</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Assistance</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assistanceModal" onclick="resetForm()">
        <i class="fas fa-plus me-2"></i>Log Assistance
    </button>
</div>

<div class="card shadow-sm border-0 glass-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="assistanceTable">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Farmer</th>
                        <th>Program</th>
                        <th>Assistance Given</th>
                        <th>Quantity</th>
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

<!-- Add/Edit Modal -->
<div class="modal fade" id="assistanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="assistanceModalLabel">Log Assistance Given</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assistanceForm">
                    <input type="hidden" id="record_id" name="id">
                    <input type="hidden" id="action" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="farmer_id" class="form-label">Farmer <span class="text-danger">*</span></label>
                        <select class="form-select" id="farmer_id" name="farmer_id" required>
                            <option value="">Select Farmer</option>
                            <?php foreach ($farmers as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="program_id" class="form-label">Agricultural Program <span class="text-danger">*</span></label>
                        <select class="form-select" id="program_id" name="program_id" required>
                            <option value="">Select Program</option>
                            <?php foreach ($programs as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['program_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="date_received" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date_received" name="date_received" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="assistance_type" class="form-label">Type <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="assistance_type" name="assistance_type" placeholder="e.g. Seeds, Fertilizer" required>
                        </div>
                        <div class="col-md-6">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" step="0.01" class="form-control" id="quantity" name="quantity">
                        </div>
                        <div class="col-md-6">
                            <label for="unit" class="form-label">Unit</label>
                            <input type="text" class="form-control" id="unit" name="unit" placeholder="e.g. kg, bags">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAssistance()">Save</button>
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
                Are you sure you want to delete this record?
            </div>
            <div class="modal-footer border-0">
                <input type="hidden" id="delete_record_id">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
let assistanceList = [];

$(document).ready(function() {
    loadAssistance();
});

function loadAssistance() {
    $.ajax({
        url: '../ajax/assistance.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success === false) {
                $('#assistanceTable tbody').html('<tr><td colspan="6" class="text-danger">' + response.message + '</td></tr>');
                return;
            }
            assistanceList = response;
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="6" class="text-center text-muted py-4">No assistance records found.</td></tr>';
            } else {
                response.forEach(r => {
                    let qtyDisplay = r.quantity ? `${r.quantity} ${r.unit || ''}` : 'N/A';
                    html += `
                        <tr>
                            <td>${r.date_received}</td>
                            <td class="fw-bold">${r.farmer_name}</td>
                            <td>${r.program_name}</td>
                            <td>${r.assistance_type}</td>
                            <td>${qtyDisplay}</td>
                            <td>
                                <button class="btn btn-sm btn-light text-primary me-1" onclick="editAssistance(${r.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger" onclick="deleteAssistance(${r.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#assistanceTable tbody').html(html);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            $('#assistanceTable tbody').html('<tr><td colspan="6" class="text-danger">Error loading data: ' + error + '<br>' + xhr.responseText + '</td></tr>');
        }
    });
}

function resetForm() {
    $('#assistanceForm')[0].reset();
    $('#record_id').val('');
    $('#action').val('add');
    $('#assistanceModalLabel').text('Log Assistance Given');
    $('#date_received').val(new Date().toISOString().split('T')[0]);
}

function editAssistance(id) {
    const r = assistanceList.find(item => item.id == id);
    if (!r) return;
    resetForm();
    $('#record_id').val(r.id);
    $('#action').val('edit');
    $('#farmer_id').val(r.farmer_id);
    $('#program_id').val(r.program_id);
    $('#date_received').val(r.date_received);
    $('#assistance_type').val(r.assistance_type);
    $('#quantity').val(r.quantity);
    $('#unit').val(r.unit);
    $('#notes').val(r.notes);
    
    $('#assistanceModalLabel').text('Edit Assistance Record');
    $('#assistanceModal').modal('show');
}

function saveAssistance() {
    if(!$('#assistanceForm')[0].checkValidity()) {
        $('#assistanceForm')[0].reportValidity();
        return;
    }
    
    $.ajax({
        url: '../ajax/assistance.php',
        type: 'POST',
        data: $('#assistanceForm').serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#assistanceModal').modal('hide');
                loadAssistance();
            } else {
                alert(response.message || 'Error saving record.');
            }
        }
    });
}

function deleteAssistance(id) {
    $('#delete_record_id').val(id);
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    $.ajax({
        url: '../ajax/assistance.php',
        type: 'POST',
        data: { action: 'delete', id: $('#delete_record_id').val() },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#deleteModal').modal('hide');
                loadAssistance();
            } else {
                alert(response.message || 'Error deleting record.');
            }
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
