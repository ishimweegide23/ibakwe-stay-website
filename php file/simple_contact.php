<?php
// Simple Contact Form Handler - No Database Required!

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Simple validation
    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill in all required fields.";
    } else {
        // Create message content
        $message_content = "=== NEW MESSAGE ===\n";
        $message_content .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $message_content .= "Name: " . $name . "\n";
        $message_content .= "Email: " . $email . "\n";
        $message_content .= "Phone: " . $phone . "\n";
        $message_content .= "Subject: " . $subject . "\n";
        $message_content .= "Message: " . $message . "\n";
        $message_content .= "==================\n\n";
        
        // Save to file
        $file = 'contact_messages.txt';
        if (file_put_contents($file, $message_content, FILE_APPEND | LOCK_EX)) {
            $success = true;
        } else {
            $error = "Could not save message. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Sent | Kigali Housing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        .success {
            color: #28a745;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .error {
            color: #dc3545;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($success)): ?>
            <div class="success">✓ Message Sent Successfully!</div>
            <p>Thank you for your message. We'll get back to you soon.</p>
            <a href="contact.html" class="btn">Back to Contact Page</a>
        <?php elseif (isset($error)): ?>
            <div class="error">✗ <?php echo $error; ?></div>
            <a href="contact.html" class="btn">Try Again</a>
        <?php else: ?>
            <div class="error">No form data received.</div>
            <a href="contact.html" class="btn">Go to Contact Page</a>
        <?php endif; ?>
    </div>
</body>
</html> 