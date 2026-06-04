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
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die("Database Connection Error: " . $this->error);
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
