<?php
// Database Connection Test Script
// This script tests the database connection and shows table information

// Include database configuration
require_once 'config.php';

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Database Connection Test | Kigali Housing</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { color: #28a745; background-color: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo ".error { color: #dc3545; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo ".info { color: #0c5460; background-color: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo "table { width: 100%; border-collapse: collapse; margin: 20px 0; }";
echo "th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }";
echo "th { background-color: #f2f2f2; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";
echo "<h1>Database Connection Test</h1>";

// Test database connection
$connection_result = testDatabaseConnection();

if ($connection_result['success']) {
    echo "<div class='success'>✓ " . $connection_result['message'] . "</div>";
    
    try {
        $pdo = getDatabaseConnection();
        
        // Get database information
        echo "<h2>Database Information</h2>";
        echo "<div class='info'>";
        echo "<strong>Database Name:</strong> kigali_housing<br>";
        echo "<strong>Host:</strong> localhost<br>";
        echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
        echo "<strong>MySQL Version:</strong> " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "<br>";
        echo "</div>";
        
        // Check tables
        echo "<h2>Database Tables</h2>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "<div class='error'>No tables found in the database. Please run setup_database.php first.</div>";
        } else {
            echo "<table>";
            echo "<tr><th>Table Name</th><th>Status</th></tr>";
            
            $expected_tables = ['contact_messages', 'users', 'property_inquiries', 'properties'];
            
            foreach ($expected_tables as $expected_table) {
                if (in_array($expected_table, $tables)) {
                    echo "<tr><td>$expected_table</td><td style='color: #28a745;'>✓ Exists</td></tr>";
                } else {
                    echo "<tr><td>$expected_table</td><td style='color: #dc3545;'>✗ Missing</td></tr>";
                }
            }
            echo "</table>";
        }
        
        // Show sample data if available
        echo "<h2>Sample Data</h2>";
        
        // Check contact_messages
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages");
        $contact_count = $stmt->fetch()['count'];
        echo "<div class='info'>Contact Messages: $contact_count records</div>";
        
        // Check users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $users_count = $stmt->fetch()['count'];
        echo "<div class='info'>Users: $users_count records</div>";
        
        // Check property_inquiries
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM property_inquiries");
        $inquiries_count = $stmt->fetch()['count'];
        echo "<div class='info'>Property Inquiries: $inquiries_count records</div>";
        
        // Check properties
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM properties");
        $properties_count = $stmt->fetch()['count'];
        echo "<div class='info'>Properties: $properties_count records</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>Error accessing database: " . $e->getMessage() . "</div>";
    }
    
} else {
    echo "<div class='error'>✗ " . $connection_result['message'] . "</div>";
    echo "<div class='info'>Please make sure:";
    echo "<ul>";
    echo "<li>XAMPP is running (Apache and MySQL)</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>Database 'kigali_housing' exists</li>";
    echo "<li>Username and password are correct</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<h2>Quick Links</h2>";
echo "<div class='info'>";
echo "<a href='setup_database.php' style='color: #007bff; text-decoration: none; margin-right: 20px;'>Setup Database</a>";
echo "<a href='contact.html' style='color: #007bff; text-decoration: none; margin-right: 20px;'>Contact Form</a>";
echo "<a href='register.html' style='color: #007bff; text-decoration: none; margin-right: 20px;'>Register</a>";
echo "<a href='login.html' style='color: #007bff; text-decoration: none;'>Login</a>";
echo "</div>";

echo "</div>";
echo "</body>";
echo "</html>";
?> 