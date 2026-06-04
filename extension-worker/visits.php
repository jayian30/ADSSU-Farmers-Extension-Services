<?php
// extension-worker/visits.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
if ($_SESSION['role'] !== 'extension_worker' && $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$page_title = "Field Visits";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';

// Fetch farmers for dropdown
$farmersStmt = $pdo->query("SELECT id, full_name, rsbsa_number FROM farmers ORDER BY full_name ASC");
$farmers = $farmersStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800">Field Visits</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Field Visits</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#visitModal" onclick="resetForm()">
        <i class="fas fa-plus me-2"></i>Log Visit
    </button>
</div>

<div class="card shadow-sm border-0 glass-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="visitsTable">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Farmer</th>
                        <th>Purpose</th>
                        <th>Notes</th>
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

<!-- Add/Edit Visit Modal -->
<div class="modal fade" id="visitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="visitModalLabel">Log Field Visit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="visitForm">
                    <input type="hidden" id="visit_id" name="id">
                    <input type="hidden" id="action" name="action" value="add">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="farmer_id" class="form-label">Farmer <span class="text-danger">*</span></label>
                            <select class="form-select" id="farmer_id" name="farmer_id" required>
                                <option value="">Select Farmer</option>
                                <?php foreach ($farmers as $f): ?>
                                    <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['full_name']) ?> (<?= htmlspecialchars($f['rsbsa_number'] ?? 'No RSBSA') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="visit_date" class="form-label">Visit Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="visit_date" name="visit_date" required>
                        </div>
                        
                        <div class="col-12">
                            <label for="purpose" class="form-label">Purpose of Visit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="purpose" name="purpose" placeholder="e.g., Crop Inspection, Pest Assessment" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="notes" class="form-label">Observations / Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="farmer_concerns" class="form-label">Farmer's Concerns</label>
                            <textarea class="form-control" id="farmer_concerns" name="farmer_concerns" rows="4"></textarea>
                        </div>

                        <!-- Optional GPS data if they allow location access -->
                        <div class="col-md-6">
                            <label class="form-label">GPS Location</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="gps_latitude" name="gps_latitude" placeholder="Latitude" readonly>
                                <input type="text" class="form-control" id="gps_longitude" name="gps_longitude" placeholder="Longitude" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="getLocation()"><i class="fas fa-map-marker-alt"></i></button>
                            </div>
                            <small class="text-muted">Click the marker icon to get current location.</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveVisit()">Save Visit</button>
            </div>
        </div>
    </div>
</div>

<script>
let visitsList = [];

$(document).ready(function() {
    loadVisits();
});

function loadVisits() {
    $.ajax({
        url: '../ajax/visits.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            if (response && response.success === false) {
                $('#visitsTable tbody').html('<tr><td colspan="5" class="text-danger">' + response.message + '</td></tr>');
                return;
            }
            visitsList = response;
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="5" class="text-center text-muted py-4">No visits recorded.</td></tr>';
            } else {
                response.forEach(v => {
                    let date = new Date(v.visit_date).toLocaleString();
                    html += `
                        <tr>
                            <td class="text-muted">${date}</td>
                            <td class="fw-bold">${v.farmer_name}</td>
                            <td>${v.purpose}</td>
                            <td>
                                <div class="text-truncate" style="max-width: 250px;" title="${v.notes}">${v.notes || '-'}</div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light text-primary me-1" onclick="editVisit(${v.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger" onclick="deleteVisit(${v.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#visitsTable tbody').html(html);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            $('#visitsTable tbody').html('<tr><td colspan="5" class="text-danger">Error loading data: ' + error + '<br>' + xhr.responseText + '</td></tr>');
        }
    });
}

function resetForm() {
    $('#visitForm')[0].reset();
    $('#visit_id').val('');
    $('#action').val('add');
    $('#visitModalLabel').text('Log Field Visit');
}

function editVisit(id) {
    const v = visitsList.find(item => item.id == id);
    if (!v) return;
    resetForm();
    $('#visit_id').val(v.id);
    $('#action').val('edit');
    $('#farmer_id').val(v.farmer_id);
    $('#visit_date').val(v.visit_date.replace(' ', 'T'));
    $('#purpose').val(v.purpose);
    $('#notes').val(v.notes);
    $('#farmer_concerns').val(v.farmer_concerns);
    $('#gps_latitude').val(v.gps_latitude);
    $('#gps_longitude').val(v.gps_longitude);
    
    $('#visitModalLabel').text('Edit Field Visit');
    $('#visitModal').modal('show');
}

function saveVisit() {
    if(!$('#visitForm')[0].checkValidity()) {
        $('#visitForm')[0].reportValidity();
        return;
    }
    
    $.ajax({
        url: '../ajax/visits.php',
        type: 'POST',
        data: $('#visitForm').serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#visitModal').modal('hide');
                loadVisits();
            } else {
                alert(response.message || 'Error saving visit.');
            }
        }
    });
}

function deleteVisit(id) {
    if(confirm('Are you sure you want to delete this record?')) {
        $.ajax({
            url: '../ajax/visits.php',
            type: 'POST',
            data: { action: 'delete', id: id },
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    loadVisits();
                } else {
                    alert(response.message || 'Error deleting visit.');
                }
            }
        });
    }
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            $('#gps_latitude').val(position.coords.latitude);
            $('#gps_longitude').val(position.coords.longitude);
        }, function(error) {
            alert("Error getting location: " + error.message);
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
