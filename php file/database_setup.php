<?php
// Comprehensive Database Setup and Troubleshooting Script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Setup and Troubleshooting</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
    .warning { color: orange; }
    .info { color: blue; }
    .step { margin: 20px 0; padding: 10px; border-left: 4px solid #ccc; }
    .step h3 { margin-top: 0; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>";

// Step 1: Test basic MySQL connection without database
echo "<div class='step'>";
echo "<h3>Step 1: Testing Basic MySQL Connection</h3>";

$connection_tests = [
    ['host' => 'localhost', 'user' => 'root', 'pass' => ''],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => ''],
    ['host' => 'localhost', 'user' => 'root', 'pass' => 'root'],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => 'root']
];

$working_connection = null;

foreach ($connection_tests as $test) {
    try {
        $pdo = new PDO("mysql:host={$test['host']}", $test['user'], $test['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
        echo "<p class='success'>✓ Connection successful with: {$test['host']}, user: {$test['user']}, password: " . (empty($test['pass']) ? 'empty' : 'set') . "</p>";
        $working_connection = $test;
        break;
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Connection failed with: {$test['host']}, user: {$test['user']}, password: " . (empty($test['pass']) ? 'empty' : 'set') . " - " . $e->getMessage() . "</p>";
    }
}

if (!$working_connection) {
    echo "<p class='error'>No working connection found. Please check your MySQL settings.</p>";
    echo "<h4>Common Solutions:</h4>";
    echo "<ul>";
    echo "<li>Make sure XAMPP/MySQL is running</li>";
    echo "<li>Check if MySQL service is started</li>";
    echo "<li>Try setting a password for root user in phpMyAdmin</li>";
    echo "<li>Check if port 3306 is available</li>";
    echo "</ul>";
    exit;
}

echo "</div>";

// Step 2: Create database if it doesn't exist
echo "<div class='step'>";
echo "<h3>Step 2: Creating Database</h3>";

$dbname = 'contact_messages';

try {
    $pdo = new PDO("mysql:host={$working_connection['host']}", $working_connection['user'], $working_connection['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '$dbname'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='success'>✓ Database '$dbname' already exists</p>";
    } else {
        // Create database
        $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p class='success'>✓ Database '$dbname' created successfully</p>";
    }
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error creating database: " . $e->getMessage() . "</p>";
    exit;
}

echo "</div>";

// Step 3: Create table
echo "<div class='step'>";
echo "<h3>Step 3: Creating Contact Messages Table</h3>";

try {
    $pdo = new PDO("mysql:host={$working_connection['host']};dbname=$dbname", $working_connection['user'], $working_connection['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    
    // Check if table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='success'>✓ Table 'contact_messages' already exists</p>";
    } else {
        // Create table
        $sql = "CREATE TABLE contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            subject VARCHAR(100),
            message TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "<p class='success'>✓ Table 'contact_messages' created successfully</p>";
    }
    
    // Show table structure
    $stmt = $pdo->query("DESCRIBE contact_messages");
    echo "<h4>Table Structure:</h4>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error creating table: " . $e->getMessage() . "</p>";
    exit;
}

echo "</div>";

// Step 4: Update configuration files
echo "<div class='step'>";
echo "<h3>Step 4: Updating Configuration Files</h3>";

// Update config.php
$config_content = "<?php
// Database Configuration - Auto-generated by database_setup.php
\$host = '{$working_connection['host']}';
\$username = '{$working_connection['user']}';
\$password = '{$working_connection['pass']}';
\$dbname = '$dbname';

function getDatabaseConnection() {
    global \$host, \$dbname, \$username, \$password;
    
    try {
        \$pdo = new PDO(\"mysql:host=\$host;dbname=\$dbname;charset=utf8\", \$username, \$password);
        \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
        return \$pdo;
    } catch (PDOException \$e) {
        throw new Exception(\"Database connection failed: \" . \$e->getMessage());
    }
}
?>";

if (file_put_contents('config.php', $config_content)) {
    echo "<p class='success'>✓ Configuration file 'config.php' updated</p>";
} else {
    echo "<p class='error'>✗ Could not update config.php</p>";
}

echo "</div>";

// Step 5: Test final connection
echo "<div class='step'>";
echo "<h3>Step 5: Final Connection Test</h3>";

try {
    $pdo = new PDO("mysql:host={$working_connection['host']};dbname=$dbname", $working_connection['user'], $working_connection['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    
    // Test inserting a sample record
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Test User', 'test@example.com', '123456789', 'Test Subject', 'This is a test message']);
    
    echo "<p class='success'>✓ Database connection and table operations working correctly!</p>";
    echo "<p class='info'>Your contact form should now work properly.</p>";
    
    // Count records
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM contact_messages");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<p class='info'>Total messages in database: <strong>$count</strong></p>";
    
} catch (PDOException $e) {
    echo "<p class='error'>✗ Final test failed: " . $e->getMessage() . "</p>";
}

echo "</div>";

echo "<div class='step'>";
echo "<h3>Next Steps</h3>";
echo "<p class='info'>1. Your contact form should now work. Try submitting a message from your contact page.</p>";
echo "<p class='info'>2. If you still have issues, check the error logs in your XAMPP/Apache directory.</p>";
echo "<p class='info'>3. You can view your messages in phpMyAdmin at: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></p>";
echo "</div>";
?> 