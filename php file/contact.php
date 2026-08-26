<?php
// Contact Form Handler for Kigali Housing Platform
// This file processes the contact form submission

// Include database configuration
require_once 'config.php';

// Initialize variables
$name = $email = $phone = $subject = $message = '';
$errors = [];
$success = false;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    // Validate required fields
    if (empty($name)) {
        $errors['name'] = 'Full name is required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email address is required';
    } elseif (!validateEmail($email)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (empty($message)) {
        $errors['message'] = 'Message is required';
    }
    
    // If no errors, proceed with database insertion
    if (empty($errors)) {
        try {
            $pdo = getDatabaseConnection();
            
            // Prepare and execute SQL statement
            $stmt = $pdo->prepare("
                INSERT INTO contact_messages 
                (name, email, phone, subject, message, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            
            // Set success flag
            $success = true;
            
            // Clear form fields
            $name = $email = $phone = $subject = $message = '';
            
        } catch (Exception $e) {
            $errors['database'] = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form | Kigali Housing Platform</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2D5D7C;
            --secondary: #F9A825;
            --accent: #E63946;
            --dark: #1A1A2E;
            --light: #F8F9FA;
            --success: #2E8B57;
            --error: #E63946;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        
        h1 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 2rem;
        }
        
        .success-message {
            color: var(--success);
            font-size: 1.2rem;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #f0f8f0;
            border-radius: 8px;
            border-left: 4px solid var(--success);
        }
        
        .error-message {
            color: var(--error);
            font-size: 1.1rem;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #fff5f5;
            border-radius: 8px;
            border-left: 4px solid var(--error);
        }
        
        .errors-list {
            text-align: left;
            margin: 20px 0;
            padding-left: 20px;
        }
        
        .errors-list li {
            margin-bottom: 8px;
            color: var(--error);
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background-color: var(--secondary);
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn:hover {
            background-color: #e69500;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(249, 168, 37, 0.4);
        }
        
        .icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .success-icon {
            color: var(--success);
        }
        
        .error-icon {
            color: var(--error);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($success): ?>
            <i class="fas fa-check-circle icon success-icon"></i>
            <h1>Thank You!</h1>
            <div class="success-message">
                Your message has been successfully sent. We'll get back to you soon.
            </div>
            <a href="contact.html" class="btn">Back to Contact Page</a>
        <?php else: ?>
            <i class="fas fa-exclamation-triangle icon error-icon"></i>
            <h1>Oops!</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    There were some issues with your submission:
                </div>
                <ul class="errors-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="error-message">
                    There was an unexpected error processing your request.
                </div>
            <?php endif; ?>
            
            <a href="contact.html" class="btn">Please try again</a>
        <?php endif; ?>
    </div>
</body>
</html>