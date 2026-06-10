<?php
// db_diagnostic.php
require_once 'includes/auth.php';
require_once 'config/database.php';

$error = null;
$connected = false;
$tables = [];
$users_count = 0;
$setup_sql_exists = file_exists('database/setup.sql');
$seed_sql_exists = file_exists('seed2.sql');
$migration_error = file_exists('migration_error.txt') ? file_get_contents('migration_error.txt') : null;

// Read settings
$db_host = getenv('MYSQLHOST') !== false ? getenv('MYSQLHOST') : 'localhost';
$db_user = getenv('MYSQLUSER') !== false ? getenv('MYSQLUSER') : 'root';
$db_pass = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : '';
$db_name = getenv('MYSQLDATABASE') !== false ? getenv('MYSQLDATABASE') : 'adssu_farmers_db';
$db_port = getenv('MYSQLPORT') !== false ? getenv('MYSQLPORT') : '3307';

try {
    $dsn = 'mysql:host=' . $db_host . ';port=' . $db_port . ';dbname=' . $db_name . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 3 // short timeout
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    $connected = true;

    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $raw_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($raw_tables as $table) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
        $count = $countStmt->fetchColumn();
        $tables[$table] = $count;
    }

    if (isset($tables['users'])) {
        $users_count = $tables['users'];
    }

} catch (PDOException $e) {
    $error = $e->getMessage();
}

$action_message = null;
$action_status = null;

// Handle manual migration request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_migration'])) {
    if (!$connected) {
        $action_message = "Cannot run migration: Database not connected.";
        $action_status = "danger";
    } else {
        try {
            // Drop existing tables
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
            $tables_to_drop = ['training_attendance', 'field_visits', 'assistance_records', 'notifications', 'activity_logs', 'announcements', 'trainings', 'agricultural_programs', 'farmers', 'extension_workers', 'users'];
            foreach ($tables_to_drop as $table) {
                $pdo->exec("DROP TABLE IF EXISTS `$table`;");
            }
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

            // Run setup
            if ($setup_sql_exists) {
                $sql = file_get_contents('database/setup.sql');
                $pdo->exec($sql);
            }
            // Run seed
            if ($seed_sql_exists) {
                $sql = file_get_contents('seed2.sql');
                $pdo->exec($sql);
            }
            
            // Clean migration error if successful
            if (file_exists('migration_error.txt')) {
                @unlink('migration_error.txt');
            }
            
            $action_message = "Database tables created and seeded successfully!";
            $action_status = "success";
            
            // Refresh variables
            $stmt = $pdo->query("SHOW TABLES");
            $raw_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $tables = [];
            foreach ($raw_tables as $table) {
                $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table`");
                $tables[$table] = $countStmt->fetchColumn();
            }
            if (isset($tables['users'])) {
                $users_count = $tables['users'];
            }
        } catch (Exception $ex) {
            $action_message = "Migration failed: " . $ex->getMessage();
            $action_status = "danger";
        }
    }
}

// Handle credential reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_credentials'])) {
    if (!$connected) {
        $action_message = "Cannot reset: Database not connected.";
        $action_status = "danger";
    } else {
        try {
            $admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
            
            // Reset default accounts
            $stmt1 = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
            $stmt1->execute([$admin_hash]);
            
            $stmt2 = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'worker'");
            $stmt2->execute([$admin_hash]);
            
            $stmt3 = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'farmer'");
            $stmt3->execute([$admin_hash]);
            
            // Confirm accounts exist, recreate if they got dropped
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = 'admin' LIMIT 1");
            $checkStmt->execute();
            if (!$checkStmt->fetch()) {
                $insertStmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role, email) VALUES ('admin', ?, 'System Administrator', 'admin', 'admin@adssu.edu.ph')");
                $insertStmt->execute([$admin_hash]);
            }
            
            $action_message = "Passwords for default accounts (admin, worker, farmer) reset successfully to 'admin123'!";
            $action_status = "success";
        } catch (Exception $ex) {
            $action_message = "Reset failed: " . $ex->getMessage();
            $action_status = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Diagnostic Utility</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #10B981;
            --primary-hover: #059669;
            --bg-dark: #0F172A;
            --card-bg: rgba(30, 41, 59, 0.7);
            --border-color: rgba(255, 255, 255, 0.1);
        }
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top, #1E293B 0%, #0F172A 100%);
            color: #E2E8F0;
            min-height: 100vh;
            padding: 40px 0;
        }
        .container {
            max-width: 900px;
        }
        .main-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            padding: 40px;
        }
        .status-badge {
            font-size: 1rem;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
        }
        .env-table th {
            color: #94A3B8;
            font-weight: 500;
        }
        .env-table td {
            font-family: monospace;
            color: #F1F5F9;
        }
        .btn-custom {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            color: white;
        }
        .btn-outline-custom {
            border: 1px solid var(--border-color);
            color: #94A3B8;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-outline-custom:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: white;
        }
        .table-custom {
            color: #E2E8F0;
        }
        .table-custom th {
            border-bottom-color: var(--border-color);
            color: #94A3B8;
        }
        .table-custom td {
            border-bottom-color: rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="main-card">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-secondary">
            <div>
                <h1 class="h2 fw-bold text-white mb-1"><i class="fas fa-database text-success me-2"></i>Database Diagnostic Utility</h1>
                <p class="text-muted mb-0">Troubleshoot & initialize your database deployment</p>
            </div>
            <div>
                <?php if ($connected): ?>
                    <span class="status-badge bg-success-subtle text-success border border-success"><i class="fas fa-check-circle me-1"></i> Connected</span>
                <?php else: ?>
                    <span class="status-badge bg-danger-subtle text-danger border border-danger"><i class="fas fa-exclamation-triangle me-1"></i> Disconnected</span>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($action_message): ?>
            <div class="alert alert-<?php echo $action_status; ?> alert-dismissible fade show" role="alert">
                <i class="fas <?php echo $action_status === 'success' ? 'fa-check-circle' : 'fa-times-circle'; ?> me-2"></i>
                <?php echo $action_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($migration_error): ?>
            <div class="alert alert-warning border border-warning-subtle" role="alert">
                <h5 class="alert-heading fw-bold"><i class="fas fa-bug me-2"></i>Last Startup Migration Error Logged:</h5>
                <pre class="mb-0 bg-dark text-warning p-3 rounded mt-2 border border-secondary" style="font-size: 0.85rem; overflow-x: auto;"><?php echo htmlspecialchars($migration_error); ?></pre>
            </div>
        <?php endif; ?>

        <!-- Database Connection Error details -->
        <?php if (!$connected): ?>
            <div class="card bg-danger-subtle border-danger text-danger-emphasis p-4 mb-4 rounded-3">
                <h4 class="fw-bold"><i class="fas fa-times-circle me-2"></i>PDO Connection Failure</h4>
                <p class="mb-2">The application could not establish a connection to your MySQL database server.</p>
                <div class="bg-dark text-danger p-3 rounded font-monospace mb-3 border border-secondary" style="font-size: 0.9rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <h6 class="fw-bold mb-2">How to fix this on Railway:</h6>
                <ol class="mb-0" style="padding-left: 20px;">
                    <li>Ensure you have added a <strong>MySQL Database</strong> service in the same project.</li>
                    <li>Verify that the environment variables <code>MYSQLHOST</code>, <code>MYSQLPORT</code>, <code>MYSQLUSER</code>, <code>MYSQLPASSWORD</code>, and <code>MYSQLDATABASE</code> are set in your web service's <strong>Variables</strong> tab. Railway usually shares them automatically.</li>
                    <li>If the variables are missing, copy them from the MySQL service variables tab to the web service variables tab.</li>
                </ol>
            </div>
        <?php endif; ?>

        <!-- Configuration Settings -->
        <h4 class="text-white mb-3 mt-4"><i class="fas fa-cog me-2 text-muted"></i>Configuration Settings</h4>
        <div class="table-responsive">
            <table class="table table-dark table-striped table-bordered env-table">
                <thead>
                    <tr>
                        <th width="35%">Environment Variable</th>
                        <th width="30%">Current Value</th>
                        <th width="35%">Fallback Value (If Not Set)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>MYSQLHOST</code></td>
                        <td><?php echo getenv('MYSQLHOST') !== false ? htmlspecialchars(getenv('MYSQLHOST')) : '<span class="text-warning">Not Set</span>'; ?></td>
                        <td><code>localhost</code></td>
                    </tr>
                    <tr>
                        <td><code>MYSQLPORT</code></td>
                        <td><?php echo getenv('MYSQLPORT') !== false ? htmlspecialchars(getenv('MYSQLPORT')) : '<span class="text-warning">Not Set</span>'; ?></td>
                        <td><code>3307</code> (local XAMPP)</td>
                    </tr>
                    <tr>
                        <td><code>MYSQLUSER</code></td>
                        <td><?php echo getenv('MYSQLUSER') !== false ? htmlspecialchars(getenv('MYSQLUSER')) : '<span class="text-warning">Not Set</span>'; ?></td>
                        <td><code>root</code></td>
                    </tr>
                    <tr>
                        <td><code>MYSQLPASSWORD</code></td>
                        <td><?php echo getenv('MYSQLPASSWORD') !== false ? '********' : '<span class="text-warning">Not Set</span>'; ?></td>
                        <td><code>(empty)</code></td>
                    </tr>
                    <tr>
                        <td><code>MYSQLDATABASE</code></td>
                        <td><?php echo getenv('MYSQLDATABASE') !== false ? htmlspecialchars(getenv('MYSQLDATABASE')) : '<span class="text-warning">Not Set</span>'; ?></td>
                        <td><code>adssu_farmers_db</code></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php if ($connected): ?>
            <!-- Table Status -->
            <h4 class="text-white mb-3 mt-5"><i class="fas fa-table me-2 text-muted"></i>Database Tables & Stats</h4>
            <?php if (empty($tables)): ?>
                <div class="alert alert-info border-info-subtle">
                    <i class="fas fa-info-circle me-2"></i>Database is empty. No tables found. Click the button below to initialize.
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-7">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <th>Table Name</th>
                                    <th class="text-end">Record Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tables as $name => $count): ?>
                                    <tr>
                                        <td><code class="text-info"><?php echo htmlspecialchars($name); ?></code></td>
                                        <td class="text-end fw-bold"><?php echo number_format($count); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-5">
                        <div class="card bg-dark border-secondary p-3 mb-3">
                            <h6 class="fw-bold text-white mb-2"><i class="fas fa-key me-2 text-warning"></i>Default Credentials</h6>
                            <p class="small text-muted">Use these default logins once the database is fully seeded:</p>
                            <ul class="small ps-3 text-light">
                                <li class="mb-2"><strong>Admin Portal:</strong><br>Username: <code>admin</code><br>Password: <code>admin123</code></li>
                                <li class="mb-2"><strong>Worker Portal:</strong><br>Username: <code>worker</code><br>Password: <code>admin123</code></li>
                                <li class="mb-2"><strong>Farmer Portal:</strong><br>Username: <code>farmer</code><br>Password: <code>admin123</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Diagnostics Actions -->
            <h4 class="text-white mb-3 mt-5"><i class="fas fa-tools me-2 text-muted"></i>Diagnostics & Maintenance Actions</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="POST" onsubmit="return confirm('WARNING: This will drop all tables and re-create them with default seed data. All existing entries will be lost. Proceed?');">
                        <button type="submit" name="run_migration" class="btn btn-custom w-100 py-3">
                            <i class="fas fa-redo me-2"></i>Re-Run Migrations & Seeds
                        </button>
                    </form>
                    <p class="small text-muted mt-2">Drops existing tables, recreates schema from <code>setup.sql</code>, and seeds initial values from <code>seed2.sql</code>.</p>
                </div>
                <div class="col-md-6">
                    <form method="POST">
                        <button type="submit" name="reset_credentials" class="btn btn-outline-custom w-100 py-3 text-white border-warning hover-bg-warning">
                            <i class="fas fa-lock-open me-2 text-warning"></i>Reset Default Passwords
                        </button>
                    </form>
                    <p class="small text-muted mt-2">Guarantees default user passwords (<code>admin</code>, <code>worker</code>, <code>farmer</code>) are set exactly to <code>admin123</code> using secure hashing.</p>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-5 pt-3 border-top border-secondary">
            <a href="login.php" class="btn btn-link text-success fw-bold text-decoration-none"><i class="fas fa-arrow-left me-2"></i>Back to Login Page</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
