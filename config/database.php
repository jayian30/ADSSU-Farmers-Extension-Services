<?php
// config/database.php

// Read environment variables (supports Railway and other host environments), or fallback to local XAMPP settings
// Railway uses MYSQLHOST, MYSQLPORT, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE
// Some Railway deployments use MYSQL_HOST, MYSQL_PORT, etc. — we check both.
$db_host = getenv('MYSQLHOST') !== false ? getenv('MYSQLHOST') 
         : (getenv('MYSQL_HOST') !== false ? getenv('MYSQL_HOST') : 'localhost');
$db_user = getenv('MYSQLUSER') !== false ? getenv('MYSQLUSER')
         : (getenv('MYSQL_USER') !== false ? getenv('MYSQL_USER') : 'root');
$db_pass = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD')
         : (getenv('MYSQL_PASSWORD') !== false ? getenv('MYSQL_PASSWORD') : '');
$db_name = getenv('MYSQLDATABASE') !== false ? getenv('MYSQLDATABASE')
         : (getenv('MYSQL_DATABASE') !== false ? getenv('MYSQL_DATABASE') : 'adssu_farmers_db');
// Railway MySQL uses port 3306; local XAMPP uses 3307
$db_port = getenv('MYSQLPORT') !== false ? getenv('MYSQLPORT')
         : (getenv('MYSQL_PORT') !== false ? getenv('MYSQL_PORT')
         : (getenv('MYSQLHOST') !== false ? '3306' : '3307'));

define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);
define('DB_PORT', $db_port);

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private $port = DB_PORT;
    private $dbh;
    private $error;

    public function __construct() {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );

        // Create PDO instance
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            $this->checkForMigrations();
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die("Database Connection Error: " . $this->error);
        }
    }

    private function checkForMigrations() {
        try {
            // Check if users table exists
            $tablesExist = $this->dbh->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
            $hasUsers = false;
            if ($tablesExist) {
                $hasUsers = $this->dbh->query("SELECT COUNT(*) FROM users")->fetchColumn() > 0;
            }

            if (!$tablesExist || !$hasUsers) {
                // Remove error file if it exists from previous attempts
                $errorFile = dirname(__DIR__) . '/migration_error.txt';
                if (file_exists($errorFile)) {
                    @unlink($errorFile);
                }

                // If tables exist but have no users, drop all to avoid constraint conflicts on recreation
                if ($tablesExist) {
                    $this->dbh->exec("SET FOREIGN_KEY_CHECKS = 0;");
                    $tables = ['training_attendance', 'field_visits', 'assistance_records', 'notifications', 'activity_logs', 'announcements', 'trainings', 'agricultural_programs', 'farmers', 'extension_workers', 'users'];
                    foreach ($tables as $table) {
                        $this->dbh->exec("DROP TABLE IF EXISTS `$table`Grid;"); // Safeguard
                        $this->dbh->exec("DROP TABLE IF EXISTS `$table` ;");
                    }
                    $this->dbh->exec("SET FOREIGN_KEY_CHECKS = 1;");
                }

                // Database is empty or broken, run setup.sql
                $setupSqlFile = dirname(__DIR__) . '/database/setup.sql';
                if (file_exists($setupSqlFile)) {
                    $sql = file_get_contents($setupSqlFile);
                    $this->dbh->exec($sql);
                }
                
                // Then run seed2.sql to populate initial values
                $seedSqlFile = dirname(__DIR__) . '/seed2.sql';
                if (file_exists($seedSqlFile)) {
                    $sql = file_get_contents($seedSqlFile);
                    $this->dbh->exec($sql);
                }
            }
        } catch (Exception $e) {
            // Log the migration error to a file in root so it can be inspected
            file_put_contents(dirname(__DIR__) . '/migration_error.txt', $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    // Prepare statement with query
    public function query($sql) {
        return $this->dbh->prepare($sql);
    }
    
    // Get raw PDO instance if needed
    public function getConnection() {
        return $this->dbh;
    }
}
