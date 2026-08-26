<?php
// Simple Database Setup Script

$host = 'localhost';
$username = 'root';
$password = ''; // Try empty first, if it doesn't work, try 'root'
$database = 'contact_db';

echo "<h2>Database Setup</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .error { color: red; }
</style>";

try {
    // Connect to MySQL (without database)
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    
    echo "<p class='success'>✓ Connected to MySQL successfully</p>";
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database`");
    echo "<p class='success'>✓ Database '$database' created/verified</p>";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ATTR_ERRMODE_EXCEPTION);
    
    // Create messages table
    $sql = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        subject VARCHAR(100),
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "<p class='success'>✓ Messages table created successfully</p>";
    
    // Test insert
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Test User', 'test@example.com', '123456789', 'Test Subject', 'This is a test message']);
    
    echo "<p class='success'>✓ Test message inserted successfully</p>";
    
    // Count messages
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM messages");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<p class='success'>✓ Total messages in database: $count</p>";
    
    echo "<h3>Setup Complete!</h3>";
    echo "<p>Your database is ready. You can now:</p>";
    echo "<ul>";
    echo "<li>Submit messages from your contact form</li>";
    echo "<li>View messages in phpMyAdmin at: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></li>";
    echo "<li>Select database: <strong>$database</strong></li>";
    echo "<li>View table: <strong>messages</strong></li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<p class='error'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Make sure:</p>";
    echo "<ul>";
    echo "<li>XAMPP is running</li>";
    echo "<li>MySQL service is started</li>";
    echo "<li>Try changing password to 'root' if empty doesn't work</li>";
    echo "</ul>";
}
?> 