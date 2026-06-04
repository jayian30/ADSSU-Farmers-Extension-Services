<?php
// extension-worker/farmers.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
if ($_SESSION['role'] !== 'extension_worker') {
    header("Location: ../index.php");
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$page_title = "My Farmers";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800">My Registered Farmers</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Farmers</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#farmerModal" onclick="resetForm()">
        <i class="fas fa-plus me-2"></i>Register Farmer
    </button>
</div>

<!-- Farmers Table Card -->
<div class="card shadow-sm border-0 glass-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="farmersTable">
                <thead class="table-light">
                    <tr>
                        <th>RSBSA No.</th>
                        <th>Name</th>
                        <th>Barangay</th>
                        <th>Farm Details</th>
                        <th>Contact</th>
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

<!-- Add/Edit Farmer Modal -->
<div class="modal fade" id="farmerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="farmerModalLabel">Register Farmer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="farmerForm">
                    <input type="hidden" id="farmer_id" name="id">
                    <input type="hidden" id="action" name="action" value="add">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="rsbsa_number" class="form-label">RSBSA Number</label>
                            <input type="text" class="form-control" id="rsbsa_number" name="rsbsa_number" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                            <select class="form-select" id="barangay" name="barangay" required>
                                <option value="">Select Barangay</option>
                                <option value="Matina">Matina</option>
                                <option value="Bago Gallera">Bago Gallera</option>
                                <option value="Calinan">Calinan</option>
                                <option value="Mintal">Mintal</option>
                                <option value="Toril">Toril</option>
                                <!-- Add more barangays as needed -->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number">
                        </div>
                        
                        <div class="col-12">
                            <label for="address" class="form-label">Complete Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="address" name="address" rows="2" required></textarea>
                        </div>

                        <div class="col-md-4">
                            <label for="farm_type" class="form-label">Farm Type</label>
                            <input type="text" class="form-control" id="farm_type" name="farm_type" placeholder="e.g., Irrigated, Rainfed">
                        </div>
                        <div class="col-md-4">
                            <label for="crop_type" class="form-label">Crop Type</label>
                            <input type="text" class="form-control" id="crop_type" name="crop_type" placeholder="e.g., Rice, Corn, Cacao">
                        </div>
                        <div class="col-md-4">
                            <label for="farm_size" class="form-label">Farm Size (Hectares)</label>
                            <input type="number" step="0.01" class="form-control" id="farm_size" name="farm_size">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveFarmer()">Save Farmer</button>
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
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this farmer?
            </div>
            <div class="modal-footer border-0">
                <input type="hidden" id="delete_farmer_id">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
let farmersList = [];

$(document).ready(function() {
    loadFarmers();
});

function loadFarmers() {
    $.ajax({
        url: '../ajax/farmers.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            farmersList = response;
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="7" class="text-center text-muted py-4">No farmers found.</td></tr>';
            } else {
                response.forEach(farmer => {
                    let statusBadge = farmer.status === 'active' 
                        ? '<span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">Active</span>' 
                        : '<span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill">Inactive</span>';
                        
                    html += `
                        <tr>
                            <td class="text-muted fw-semibold">${farmer.rsbsa_number || 'N/A'}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        ${farmer.full_name.charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">${farmer.full_name}</div>
                                        <div class="text-muted small">${farmer.address}</div>
                                    </div>
                                </div>
                            </td>
                            <td>${farmer.barangay}</td>
                            <td>
                                <div class="small">
                                    <div><i class="fas fa-leaf text-success me-1"></i> ${farmer.crop_type || 'N/A'}</div>
                                    <div class="text-muted">${farmer.farm_size ? farmer.farm_size + ' ha' : 'N/A'}</div>
                                </div>
                            </td>
                            <td>${farmer.contact_number || 'N/A'}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <button class="btn btn-sm btn-light text-primary me-1" onclick="editFarmer(${farmer.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger" onclick="deleteFarmer(${farmer.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#farmersTable tbody').html(html);
        },
        error: function() {
            alert('Error loading farmers data.');
        }
    });
}

function resetForm() {
    $('#farmerForm')[0].reset();
    $('#farmer_id').val('');
    $('#action').val('add');
    $('#farmerModalLabel').text('Register Farmer');
}

function editFarmer(id) {
    const farmer = farmersList.find(f => f.id == id);
    if (!farmer) return;
    resetForm();
    $('#farmer_id').val(farmer.id);
    $('#action').val('edit');
    $('#rsbsa_number').val(farmer.rsbsa_number);
    $('#full_name').val(farmer.full_name);
    $('#barangay').val(farmer.barangay);
    $('#contact_number').val(farmer.contact_number);
    $('#address').val(farmer.address);
    $('#farm_type').val(farmer.farm_type);
    $('#crop_type').val(farmer.crop_type);
    $('#farm_size').val(farmer.farm_size);
    
    $('#farmerModalLabel').text('Edit Farmer');
    $('#farmerModal').modal('show');
}

function saveFarmer() {
    if(!$('#farmerForm')[0].checkValidity()) {
        $('#farmerForm')[0].reportValidity();
        return;
    }
    
    $.ajax({
        url: '../ajax/farmers.php',
        type: 'POST',
        data: $('#farmerForm').serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#farmerModal').modal('hide');
                loadFarmers();
            } else {
                alert(response.message || 'Error saving farmer.');
            }
        },
        error: function() {
            alert('Server error occurred.');
        }
    });
}

function deleteFarmer(id) {
    $('#delete_farmer_id').val(id);
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    const id = $('#delete_farmer_id').val();
    $.ajax({
        url: '../ajax/farmers.php',
        type: 'POST',
        data: { action: 'delete', id: id },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#deleteModal').modal('hide');
                loadFarmers();
            } else {
                alert(response.message || 'Error deleting farmer.');
            }
        },
        error: function() {
            alert('Server error occurred.');
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
