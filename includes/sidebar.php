<?php
// includes/sidebar.php
$base_url = (getenv('PORT') !== false || getenv('RAILWAY_STATIC_URL') !== false) ? "" : "/ADSSU Farmers Extension Services";
$role = $_SESSION['role'];
$current_page = basename($_SERVER['PHP_SELF']);

$link_prefix = '';
if (isset($in_root) && $in_root) {
    if ($role === 'admin') $link_prefix = 'admin/';
    else if ($role === 'extension_worker') $link_prefix = 'extension-worker/';
    else if ($role === 'farmer') $link_prefix = 'farmer/';
}
?>
<div class="sidebar" id="sidebar">
    <div class="logo-details">
        <i class="fas fa-leaf text-success me-2"></i>
        <span class="logo-name">ADSSU FESMS</span>
    </div>
    <ul class="nav-links">
        <!-- Dashboard available to all roles, but points to different directories ideally. 
             Since we route via their respective folders, we just link to dashboard.php -->
        <li>
            <a href="<?php echo $link_prefix; ?>dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i>
                <span class="link-name">Dashboard</span>
            </a>
        </li>

        <?php if ($role === 'admin'): ?>
        <li>
            <a href="<?php echo $link_prefix; ?>farmers.php" class="<?php echo $current_page == 'farmers.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span class="link-name">Farmers</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $link_prefix; ?>programs.php" class="<?php echo $current_page == 'programs.php' ? 'active' : ''; ?>">
                <i class="fas fa-seedling"></i>
                <span class="link-name">Programs</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $link_prefix; ?>trainings.php" class="<?php echo $current_page == 'trainings.php' ? 'active' : ''; ?>">
                <i class="fas fa-chalkboard-teacher"></i>
                <span class="link-name">Trainings</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $link_prefix; ?>reports.php" class="<?php echo $current_page == 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span class="link-name">Reports</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $link_prefix; ?>users.php" class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-shield"></i>
                <span class="link-name">User Management</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($role === 'extension_worker'): ?>
        <li>
            <a href="<?php echo $link_prefix; ?>farmers.php" class="<?php echo $current_page == 'farmers.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span class="link-name">My Farmers</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $link_prefix; ?>visits.php" class="<?php echo $current_page == 'visits.php' ? 'active' : ''; ?>">
                <i class="fas fa-map-marked-alt"></i>
                <span class="link-name">Field Visits</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $link_prefix; ?>assistance.php" class="<?php echo $current_page == 'assistance.php' ? 'active' : ''; ?>">
                <i class="fas fa-hands-helping"></i>
                <span class="link-name">Assistance</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($role === 'farmer'): ?>
        <li>
            <a href="<?php echo $link_prefix; ?>history.php" class="<?php echo $current_page == 'history.php' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i>
                <span class="link-name">Service History</span>
            </a>
        </li>
        <li>
            <a href="<?php echo $link_prefix; ?>trainings.php" class="<?php echo $current_page == 'trainings.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span class="link-name">My Trainings</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>
