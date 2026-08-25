<?php
// Database Configuration for Kigali Housing Platform
// Configured for XAMPP default settings

// Database connection settings
$host = 'localhost';
$username = 'root';
$password = ''; // Empty password for XAMPP default
$database = 'kigali_housing';

// Connection function with multiple password attempts
function getDatabaseConnection() {
    global $host, $database, $username;
    
    // Try different password combinations for XAMPP
    $passwords = ['', 'root', 'password', 'admin'];
    
    foreach ($passwords as $password) {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        } catch (PDOException $e) {
            // Continue to next password if this one fails
            continue;
        }
    }
    
    // If all passwords fail, throw the last error
    throw new Exception("Database connection failed: Unable to connect with any password. Please check your XAMPP MySQL settings.");
}

// Test connection function
function testDatabaseConnection() {
    try {
        $pdo = getDatabaseConnection();
        return ['success' => true, 'message' => 'Database connection successful!'];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Sanitize input function
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email function
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?> 