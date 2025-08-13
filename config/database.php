<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


date_default_timezone_set('Asia/Jakarta');


define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'cashflow');


$conn = null;

/**
 * Initialize database connection
 * @return mysqli Database connection object
 */
function init_database() {
    global $conn;
    
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        
        $conn->set_charset("utf8");
        
        return $conn;
        
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

/**
 * Get database connection
 * @return mysqli Database connection object
 */
function get_db_connection() {
    global $conn;
    
    if ($conn === null || $conn->ping() === false) {
        return init_database();
    }
    
    return $conn;
}

/**
 * Close database connection
 */
function close_database() {
    global $conn;
    
    if ($conn instanceof mysqli && $conn->ping()) {
        $conn->close();
        $conn = null;
    }
}


$conn = init_database();


register_shutdown_function('close_database');
?>