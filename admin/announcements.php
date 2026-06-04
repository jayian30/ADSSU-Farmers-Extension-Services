<?php
// admin/announcements.php
require_once '../config/database.php';
require_once '../includes/auth.php';

requireLogin();
requireRole('admin');

$db = new Database();
$pdo = $db->getConnection();

$page_title = "Announcements Management";
require_once '../includes/header.php';
require_once '../includes/sidebar.php';
?>

<div class="content-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-0 text-gray-800">Announcements Management</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Announcements</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#announcementModal" onclick="resetForm()">
        <i class="fas fa-plus me-2"></i>New Announcement
    </button>
</div>

<!-- Announcements Table Card -->
<div class="card shadow-sm border-0 glass-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="announcementsTable">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Target Audience</th>
                        <th>Author</th>
                        <th>Date Posted</th>
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

<!-- Add/Edit Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title" id="announcementModalLabel">Post New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="announcementForm">
                    <input type="hidden" id="announcement_id" name="id">
                    <input type="hidden" id="action" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required placeholder="Announcement Title">
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Announcement Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="6" required placeholder="Type the announcement details here..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="target_role" class="form-label">Target Audience <span class="text-danger">*</span></label>
                        <select class="form-select" id="target_role" name="target_role" required>
                            <option value="all">All Users</option>
                            <option value="farmer">Farmers Only</option>
                            <option value="extension_worker">Extension Workers Only</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAnnouncement()">Post Announcement</button>
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
                Are you sure you want to delete this announcement?
            </div>
            <div class="modal-footer border-0">
                <input type="hidden" id="delete_announcement_id">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
let announcementsList = [];

$(document).ready(function() {
    loadAnnouncements();
});

function loadAnnouncements() {
    $.ajax({
        url: '../ajax/announcements.php',
        type: 'GET',
        data: { action: 'list' },
        dataType: 'json',
        success: function(response) {
            announcementsList = response;
            let html = '';
            if(response.length === 0) {
                html = '<tr><td colspan="5" class="text-center text-muted py-4">No announcements found.</td></tr>';
            } else {
                response.forEach(a => {
                    let targetBadge = 'bg-primary';
                    if (a.target_role === 'farmer') targetBadge = 'bg-info';
                    else if (a.target_role === 'extension_worker') targetBadge = 'bg-warning text-dark';
                    
                    let badgeText = a.target_role === 'all' ? 'All Users' : (a.target_role === 'farmer' ? 'Farmers' : 'Workers');
                    
                    let date = new Date(a.created_at).toLocaleString();
                    
                    html += `
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">${a.title}</div>
                                <div class="text-muted small text-truncate" style="max-width: 350px;">${a.content}</div>
                            </td>
                            <td><span class="badge ${targetBadge} bg-opacity-10 text-${targetBadge.includes('text-dark') ? 'dark' : targetBadge.replace('bg-','')} px-2 py-1 rounded-pill text-capitalize">${badgeText}</span></td>
                            <td>${a.author_name || 'System'}</td>
                            <td>${date}</td>
                            <td>
                                <button class="btn btn-sm btn-light text-primary me-1" onclick="editAnnouncement(${a.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger" onclick="deleteAnnouncement(${a.id})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            }
            $('#announcementsTable tbody').html(html);
        }
    });
}

function resetForm() {
    $('#announcementForm')[0].reset();
    $('#announcement_id').val('');
    $('#action').val('add');
    $('#announcementModalLabel').text('Post New Announcement');
}

function editAnnouncement(id) {
    const a = announcementsList.find(item => item.id == id);
    if (!a) return;
    resetForm();
    $('#announcement_id').val(a.id);
    $('#action').val('edit');
    $('#title').val(a.title);
    $('#content').val(a.content);
    $('#target_role').val(a.target_role);
    
    $('#announcementModalLabel').text('Edit Announcement');
    $('#announcementModal').modal('show');
}

function saveAnnouncement() {
    if(!$('#announcementForm')[0].checkValidity()) {
        $('#announcementForm')[0].reportValidity();
        return;
    }
    
    $.ajax({
        url: '../ajax/announcements.php',
        type: 'POST',
        data: $('#announcementForm').serialize(),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#announcementModal').modal('hide');
                loadAnnouncements();
            } else {
                alert(response.message || 'Error saving announcement.');
            }
        }
    });
}

function deleteAnnouncement(id) {
    $('#delete_announcement_id').val(id);
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    $.ajax({
        url: '../ajax/announcements.php',
        type: 'POST',
        data: { action: 'delete', id: $('#delete_announcement_id').val() },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                $('#deleteModal').modal('hide');
                loadAnnouncements();
            } else {
                alert(response.message || 'Error deleting announcement.');
            }
        }
    });
}
</script>

<?php require_once '../includes/footer.php'; ?>
