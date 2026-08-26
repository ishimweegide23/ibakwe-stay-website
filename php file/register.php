<?php
// User Registration Handler for Kigali Housing Platform
// This file processes user registration form submissions

// Include database configuration
require_once 'config.php';

// Initialize variables
$full_name = $email = $phone = $password = $confirm_password = '';
$errors = [];
$success = false;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate required fields
    if (empty($full_name)) {
        $errors['full_name'] = 'Full name is required';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email address is required';
    } elseif (!validateEmail($email)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters long';
    }
    
    if (empty($confirm_password)) {
        $errors['confirm_password'] = 'Please confirm your password';
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    // If no errors, proceed with database insertion
    if (empty($errors)) {
        try {
            $pdo = getDatabaseConnection();
            
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $errors['email'] = 'This email address is already registered';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $pdo->prepare("
                    INSERT INTO users 
                    (full_name, email, phone, password, user_type, created_at) 
                    VALUES (?, ?, ?, ?, 'client', NOW())
                ");
                
                $stmt->execute([$full_name, $email, $phone, $hashed_password]);
                
                // Set success flag
                $success = true;
                
                // Clear form fields
                $full_name = $email = $phone = $password = $confirm_password = '';
            }
            
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
    <title>Registration | Kigali Housing Platform</title>
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
            margin: 5px;
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
            <i class="fas fa-user-check icon success-icon"></i>
            <h1>Registration Successful!</h1>
            <div class="success-message">
                Your account has been created successfully. You can now log in to your account.
            </div>
            <div>
                <a href="login.html" class="btn">Login Now</a>
                <a href="index.html" class="btn">Go to Homepage</a>
            </div>
        <?php else: ?>
            <i class="fas fa-exclamation-triangle icon error-icon"></i>
            <h1>Registration Error</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    There were some issues with your registration:
                </div>
                <ul class="errors-list">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="error-message">
                    There was an unexpected error processing your registration.
                </div>
            <?php endif; ?>
            
            <div>
                <a href="register.html" class="btn">Try Again</a>
                <a href="index.html" class="btn">Go to Homepage</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 