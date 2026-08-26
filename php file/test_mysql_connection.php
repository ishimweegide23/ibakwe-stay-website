<?php
// MySQL Connection Test Script
// This script tests different password combinations for XAMPP MySQL

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>MySQL Connection Test | Kigali Housing</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }";
echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }";
echo ".success { color: #28a745; background-color: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo ".error { color: #dc3545; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo ".info { color: #0c5460; background-color: #d1ecf1; padding: 10px; border-radius: 4px; margin: 10px 0; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";

echo "<h1>MySQL Connection Test</h1>";
echo "<div class='info'>Testing different password combinations for XAMPP MySQL...</div>";

$host = 'localhost';
$username = 'root';
$passwords = ['', 'root', 'password', 'admin', 'xampp'];

$connected = false;
$working_password = '';

foreach ($passwords as $password) {
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='success'>✓ Connected successfully with password: '" . ($password === '' ? 'empty' : $password) . "'</div>";
        $connected = true;
        $working_password = $password;
        break;
        
    } catch (PDOException $e) {
        echo "<div class='error'>✗ Failed with password '" . ($password === '' ? 'empty' : $password) . "': " . $e->getMessage() . "</div>";
    }
}

if ($connected) {
    echo "<div class='success'>";
    echo "<h2>Connection Successful!</h2>";
    echo "<p>Working password: <strong>" . ($working_password === '' ? 'empty' : $working_password) . "</strong></p>";
    echo "<p>Please update your config.php file with this password.</p>";
    
    // Test if database exists
    try {
        $pdo->exec("USE kigali_housing");
        echo "<p>✓ Database 'kigali_housing' exists and is accessible.</p>";
    } catch (PDOException $e) {
        echo "<p>⚠️ Database 'kigali_housing' does not exist. Please run setup_database.php first.</p>";
    }
    
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Update config.php with the working password</li>";
    echo "<li>Run <a href='setup_database.php'>setup_database.php</a> to create tables</li>";
    echo "<li>Test <a href='register.html'>registration form</a></li>";
    echo "</ol>";
    echo "</div>";
    
} else {
    echo "<div class='error'>";
    echo "<h2>Connection Failed!</h2>";
    echo "<p>None of the tested passwords worked. Please check:</p>";
    echo "<ul>";
    echo "<li>XAMPP is running (Apache and MySQL)</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>MySQL root password is set correctly</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ol>";
    echo "<li>Open XAMPP Control Panel</li>";
    echo "<li>Stop MySQL service</li>";
    echo "<li>Click 'Config' → 'my.ini'</li>";
    echo "<li>Look for password settings</li>";
    echo "<li>Restart MySQL service</li>";
    echo "</ol>";
    echo "</div>";
}

echo "</div>";
echo "</body>";
echo "</html>";
?> 