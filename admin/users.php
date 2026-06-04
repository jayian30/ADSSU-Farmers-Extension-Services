<?php
// admin/users.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
requireRole('admin');

$db = new Database();
$pdo = $db->getConnection();

$page_title = "User Management";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800">User Management</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Users</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetForm()">
        <i class="fas fa-plus me-2"></i>Add New User
    </button>
</div>

<div class="card shadow-sm border-0 glass-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Email</th>
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

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="userModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="user_id" name="id">
                    <input type="hidden" id="action" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger" id="password_req">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="text-muted" id="password_help" style="display:none;">Leave blank to keep current password.</small>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="extension_worker">Extension Worker</option>
                                <option value="farmer">Farmer</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3" id="statusGroup" style="display:none;">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Save User</button>
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
                Are you sure you want to delete this user?
            </div>
            <div class="modal-footer border-0">
                <input type="hidden" id="delete_user_id">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
let usersList = [];

$(document).ready(function() {
    loadUsers();
});

function loadUsers() {
    $.ajax({
        url: '../ajax/users.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            usersList = response;
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>';
            } else {
                response.forEach(u => {
                    let statusClass = u.status === 'active' ? 'bg-success text-success' : 'bg-danger text-danger';
                    let roleClass = u.role === 'admin' ? 'bg-danger' : (u.role === 'extension_worker' ? 'bg-primary' : 'bg-info');
                    
                    html += `
                        <tr>
                            <td class="fw-bold text-dark">${u.full_name}</td>
                            <td>${u.username}</td>
                            <td><span class="badge ${roleClass} bg-opacity-10 text-${roleClass.replace('bg-','')} px-2 py-1 rounded-pill text-capitalize">${u.role.replace('_', ' ')}</span></td>
                            <td>${u.email || '-'}</td>
                            <td><span class="badge ${statusClass.split(' ')[0]} bg-opacity-10 ${statusClass.split(' ')[1]} px-2 py-1 rounded-pill text-capitalize">${u.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-light text-primary me-1" onclick="editUser(${u.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger" onclick="deleteUser(${u.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#usersTable tbody').html(html);
        }
    });
}

function resetForm() {
    $('#userForm')[0].reset();
    $('#user_id').val('');
    $('#action').val('add');
    $('#userModalLabel').text('Add New User');
    $('#statusGroup').hide();
    $('#password').prop('required', true);
    $('#password_req').show();
    $('#password_help').hide();
}

function editUser(id) {
    const u = usersList.find(user => user.id == id);
    if (!u) return;
    resetForm();
    $('#user_id').val(u.id);
    $('#action').val('edit');
    $('#full_name').val(u.full_name);
    $('#username').val(u.username);
    $('#email').val(u.email);
    $('#role').val(u.role);
    $('#status').val(u.status);
    
    $('#userModalLabel').text('Edit User');
    $('#statusGroup').show();
    $('#password').prop('required', false);
    $('#password_req').hide();
    $('#password_help').show();
    $('#userModal').modal('show');
}

function saveUser() {
    if(!$('#userForm')[0].checkValidity()) {
        $('#userForm')[0].reportValidity();
        return;
    }
    
    $.ajax({
        url: '../ajax/users.php',
        type: 'POST',
        data: $('#userForm').serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#userModal').modal('hide');
                loadUsers();
            } else {
                alert(response.message || 'Error saving user.');
            }
        }
    });
}

function deleteUser(id) {
    $('#delete_user_id').val(id);
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    $.ajax({
        url: '../ajax/users.php',
        type: 'POST',
        data: { action: 'delete', id: $('#delete_user_id').val() },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#deleteModal').modal('hide');
                loadUsers();
            } else {
                alert(response.message || 'Error deleting user.');
            }
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
