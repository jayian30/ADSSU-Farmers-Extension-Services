<?php
// config/database.php

// Read environment variables (supports Railway and other host environments), or fallback to local XAMPP settings
$db_host = getenv('MYSQLHOST') !== false ? getenv('MYSQLHOST') : 'localhost';
$db_user = getenv('MYSQLUSER') !== false ? getenv('MYSQLUSER') : 'root';
$db_pass = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : '';
$db_name = getenv('MYSQLDATABASE') !== false ? getenv('MYSQLDATABASE') : 'adssu_farmers_db';
$db_port = getenv('MYSQLPORT') !== false ? getenv('MYSQLPORT') : '3307'; // Local XAMPP uses 3307

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
            // Check if 'users' table exists by running a query
            $stmt = $this->dbh->query("SHOW TABLES LIKE 'users'");
            if ($stmt->rowCount() == 0) {
                // Database is empty, run setup.sql
                $setupSqlFile = dirname(__DIR__) . '/database/setup.sql';
                if (file_exists($setupSqlFile)) {
                    $sql = file_get_contents($setupSqlFile);
                    // Remove CREATE DATABASE and USE statements to avoid permissions/isolation issues on hosting platforms
                    $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS\s+\w+;/i', '', $sql);
                    $sql = preg_replace('/USE\s+\w+;/i', '', $sql);
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
            // Log or ignore to prevent app crash if schema query fails
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
