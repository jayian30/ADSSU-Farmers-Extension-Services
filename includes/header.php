<?php
// includes/header.php
require_once __DIR__ . '/auth.php';
requireLogin();

// Base URL detection
$base_url = (getenv('PORT') !== false || getenv('RAILWAY_STATIC_URL') !== false) ? "" : "/ADSSU Farmers Extension Services";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ADSSU FESMS</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <style>
        .no-caret::after {
            display: none !important;
        }
        .notification-item:hover {
            background-color: #f8fafc !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateUnreadCount();
            setInterval(updateUnreadCount, 30000);
        });

        function updateUnreadCount() {
            const badge = document.getElementById('notificationBadge');
            if (!badge) return;
            
            let url = 'ajax/notifications.php?action=unread_count';
            if (window.location.pathname.includes('/admin/') || window.location.pathname.includes('/extension-worker/') || window.location.pathname.includes('/farmer/')) {
                url = '../' + url;
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data && data.count > 0) {
                        badge.innerText = data.count;
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }
                })
                .catch(err => console.error(err));
        }

        function loadNotificationsList() {
            const container = document.getElementById('notificationsContainer');
            if (!container) return;
            
            container.innerHTML = '<div class="text-center p-3 text-muted small"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</div>';
            
            let url = 'ajax/notifications.php?action=list';
            if (window.location.pathname.includes('/admin/') || window.location.pathname.includes('/extension-worker/') || window.location.pathname.includes('/farmer/')) {
                url = '../' + url;
            }
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    if (!data || data.length === 0) {
                        html = '<div class="text-center p-3 text-muted small">No new notifications.</div>';
                    } else {
                        data.forEach(item => {
                            const readClass = item.is_read ? 'text-muted bg-white' : 'fw-bold bg-light';
                            const titleColor = item.is_read ? 'text-secondary' : 'text-success';
                            const time = new Date(item.created_at).toLocaleDateString() + ' ' + new Date(item.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                            
                            html += `
                                <div class="p-3 border-bottom notification-item ${readClass}" style="cursor: pointer;" onclick="markNotificationRead(${item.id}, event)">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small ${titleColor}">${escapeHtml(item.title)}</span>
                                        <span class="text-muted" style="font-size: 10px;">${time}</span>
                                    </div>
                                    <p class="mb-0 text-dark small" style="font-size: 12px; line-height:1.4;">${escapeHtml(item.message)}</p>
                                </div>
                            `;
                        });
                    }
                    container.innerHTML = html;
                })
                .catch(err => {
                    container.innerHTML = '<div class="text-center p-3 text-danger small">Error loading notifications.</div>';
                });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function markNotificationRead(id, event) {
            event.stopPropagation();
            let url = 'ajax/notifications.php';
            if (window.location.pathname.includes('/admin/') || window.location.pathname.includes('/extension-worker/') || window.location.pathname.includes('/farmer/')) {
                url = '../' + url;
            }
            
            let formData = new FormData();
            formData.append('action', 'mark_read');
            formData.append('id', id);
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateUnreadCount();
                    loadNotificationsList();
                }
            });
        }

        function markAllNotificationsRead(event) {
            event.preventDefault();
            event.stopPropagation();
            let url = 'ajax/notifications.php';
            if (window.location.pathname.includes('/admin/') || window.location.pathname.includes('/extension-worker/') || window.location.pathname.includes('/farmer/')) {
                url = '../' + url;
            }
            
            let formData = new FormData();
            formData.append('action', 'mark_read');
            formData.append('id', 'all');
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateUnreadCount();
                    loadNotificationsList();
                }
            });
        }
    </script>
</head>
<body>
    
<?php include 'sidebar.php'; ?>

<section class="main-content">
    <nav class="top-navbar">
        <div class="sidebar-toggle">
            <i class="fas fa-bars" id="btn-toggle"></i>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Notifications Dropdown -->
            <div class="dropdown" id="notificationDropdownContainer">
                <a href="#" class="text-dark position-relative dropdown-toggle no-caret" id="dropdownNotifications" data-bs-toggle="dropdown" aria-expanded="false" onclick="loadNotificationsList()">
                    <i class="fas fa-bell fs-4"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notificationBadge" style="font-size: 9px; padding: 3px 6px;">
                        0
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow py-0" aria-labelledby="dropdownNotifications" style="width: 300px; max-height: 400px; overflow-y: auto; z-index: 1050;">
                    <div class="p-2 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <span class="fw-bold small">Notifications</span>
                        <a href="#" class="text-success small text-decoration-none fw-bold" onclick="markAllNotificationsRead(event)">Mark all as read</a>
                    </div>
                    <div id="notificationsContainer">
                        <!-- Loaded dynamically -->
                    </div>
                </ul>
            </div>

            <!-- User profile -->
            <div class="user-profile dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name']); ?>&background=22C55E&color=fff" alt="mdo" width="32" height="32" class="rounded-circle me-2">
                    <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item" href="<?php echo $base_url; ?>/profile.php">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo $base_url; ?>/logout.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="content-wrapper">
