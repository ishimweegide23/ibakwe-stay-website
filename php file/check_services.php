<?php
// Check XAMPP Services Status
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>XAMPP Services Check</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .step { margin: 20px 0; padding: 10px; border-left: 4px solid #ccc; }
    .step h3 { margin-top: 0; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    .solution { background: #e8f4f8; padding: 15px; border-radius: 5px; margin: 10px 0; }
</style>";

echo "<div class='step'>";
echo "<h3>Step 1: Check if MySQL Port is Available</h3>";

// Check if port 3306 is in use
$connection = @fsockopen('localhost', 3306, $errno, $errstr, 5);
if ($connection) {
    echo "<p class='success'>✓ Port 3306 is open and MySQL appears to be running</p>";
    fclose($connection);
} else {
    echo "<p class='error'>✗ Port 3306 is not accessible. MySQL may not be running.</p>";
    echo "<div class='solution'>";
    echo "<h4>Solution:</h4>";
    echo "<ol>";
    echo "<li>Open XAMPP Control Panel</li>";
    echo "<li>Click 'Start' next to MySQL</li>";
    echo "<li>Wait for the status to turn green</li>";
    echo "<li>If it fails, check the logs by clicking 'Logs' button</li>";
    echo "</ol>";
    echo "</div>";
}

echo "</div>";

echo "<div class='step'>";
echo "<h3>Step 2: Check Apache Status</h3>";

// Check if Apache is running
$connection = @fsockopen('localhost', 80, $errno, $errstr, 5);
if ($connection) {
    echo "<p class='success'>✓ Apache is running on port 80</p>";
    fclose($connection);
} else {
    echo "<p class='error'>✗ Apache is not running on port 80</p>";
    echo "<div class='solution'>";
    echo "<h4>Solution:</h4>";
    echo "<ol>";
    echo "<li>Open XAMPP Control Panel</li>";
    echo "<li>Click 'Start' next to Apache</li>";
    echo "<li>Wait for the status to turn green</li>";
    echo "</ol>";
    echo "</div>";
}

echo "</div>";

echo "<div class='step'>";
echo "<h3>Step 3: Manual MySQL Connection Test</h3>";

// Try different connection methods
$connection_methods = [
    ['method' => 'PDO with localhost', 'dsn' => 'mysql:host=localhost'],
    ['method' => 'PDO with 127.0.0.1', 'dsn' => 'mysql:host=127.0.0.1'],
    ['method' => 'PDO with socket', 'dsn' => 'mysql:unix_socket=/tmp/mysql.sock'],
    ['method' => 'PDO with socket (Windows)', 'dsn' => 'mysql:unix_socket=C:/xampp/mysql/mysql.sock']
];

foreach ($connection_methods as $method) {
    try {
        $pdo = new PDO($method['dsn'], 'root', '');
        echo "<p class='success'>✓ {$method['method']} - Connection successful!</p>";
        break;
    } catch (PDOException $e) {
        echo "<p class='error'>✗ {$method['method']} - " . $e->getMessage() . "</p>";
    }
}

echo "</div>";

echo "<div class='step'>";
echo "<h3>Step 4: XAMPP Control Panel Instructions</h3>";

echo "<div class='solution'>";
echo "<h4>How to Start XAMPP Services:</h4>";
echo "<ol>";
echo "<li><strong>Open XAMPP Control Panel</strong></li>";
echo "<li><strong>Start Apache:</strong> Click the 'Start' button next to Apache</li>";
echo "<li><strong>Start MySQL:</strong> Click the 'Start' button next to MySQL</li>";
echo "<li><strong>Wait for both services to turn green</strong></li>";
echo "<li><strong>If MySQL fails to start:</strong></li>";
echo "<ul>";
echo "<li>Click 'Logs' button next to MySQL</li>";
echo "<li>Look for error messages</li>";
echo "<li>Common issues: port 3306 already in use, or MySQL data directory issues</li>";
echo "</ul>";
echo "</ol>";
echo "</div>";

echo "<div class='solution'>";
echo "<h4>If MySQL Still Won't Start:</h4>";
echo "<ol>";
echo "<li><strong>Check if another MySQL is running:</strong></li>";
echo "<ul>";
echo "<li>Open Task Manager (Ctrl+Shift+Esc)</li>";
echo "<li>Look for 'mysqld.exe' or 'mysql.exe' processes</li>";
echo "<li>End them if found</li>";
echo "</ul>";
echo "<li><strong>Check port 3306:</strong></li>";
echo "<ul>";
echo "<li>Open Command Prompt as Administrator</li>";
echo "<li>Run: <code>netstat -ano | findstr :3306</code></li>";
echo "<li>If something is using port 3306, stop that service</li>";
echo "</ul>";
echo "<li><strong>Reset MySQL:</strong></li>";
echo "<ul>";
echo "<li>In XAMPP Control Panel, click 'Stop' for MySQL</li>";
echo "<li>Click 'Config' → 'my.ini'</li>";
echo "<li>Look for any custom settings that might cause issues</li>";
echo "</ul>";
echo "</ol>";
echo "</div>";

echo "</div>";

echo "<div class='step'>";
echo "<h3>Step 5: Alternative Solutions</h3>";

echo "<div class='solution'>";
echo "<h4>If XAMPP MySQL won't work:</h4>";
echo "<ol>";
echo "<li><strong>Use a different database:</strong> SQLite (no server needed)</li>";
echo "<li><strong>Install MySQL separately:</strong> Download MySQL Community Server</li>";
echo "<li><strong>Use a cloud database:</strong> Like PlanetScale, Railway, or similar</li>";
echo "</ol>";
echo "</div>";

echo "</div>";

echo "<div class='step'>";
echo "<h3>Next Steps</h3>";
echo "<p class='info'>1. First, make sure XAMPP Control Panel is open and both Apache and MySQL are running (green status)</p>";
echo "<p class='info'>2. If MySQL won't start, follow the troubleshooting steps above</p>";
echo "<p class='info'>3. Once MySQL is running, go back to: <a href='database_setup.php'>database_setup.php</a></p>";
echo "<p class='info'>4. If you continue having issues, we can switch to SQLite database (no MySQL needed)</p>";
echo "</div>";
?> 