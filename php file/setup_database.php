<?php
// Database Setup Script for Kigali Housing Platform
// This script creates all necessary tables for the platform

// Database connection settings
$host = 'localhost';
$username = 'root';
$password = ''; // Empty password for XAMPP default
$database = 'kigali_housing';

try {
    // Create connection without database first
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$database' created successfully or already exists.<br>";
    
    // Select the database
    $pdo->exec("USE `$database`");
    
    // Drop existing tables if they exist (to avoid conflicts)
    $pdo->exec("DROP TABLE IF EXISTS `contact_messages`");
    $pdo->exec("DROP TABLE IF EXISTS `property_inquiries`");
    $pdo->exec("DROP TABLE IF EXISTS `users`");
    $pdo->exec("DROP TABLE IF EXISTS `properties`");
    
    // Create contact_messages table
    $sql = "CREATE TABLE `contact_messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `phone` varchar(20) DEFAULT NULL,
        `subject` varchar(200) DEFAULT NULL,
        `message` text NOT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'contact_messages' created successfully.<br>";
    
    // Create users table for registration and login
    $sql = "CREATE TABLE `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `full_name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `phone` varchar(20) DEFAULT NULL,
        `password` varchar(255) NOT NULL,
        `user_type` enum('client', 'agent', 'admin') DEFAULT 'client',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'users' created successfully.<br>";
    
    // Create property_inquiries table for all property inquiry forms
    $sql = "CREATE TABLE `property_inquiries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `email` varchar(100) NOT NULL,
        `phone` varchar(20) NOT NULL,
        `message` text NOT NULL,
        `property_type` varchar(50) DEFAULT NULL,
        `property_id` varchar(50) DEFAULT NULL,
        `property_name` varchar(200) DEFAULT NULL,
        `inquiry_type` enum('rent', 'sale', 'short_stay', 'apartment') DEFAULT 'rent',
        `status` enum('new', 'contacted', 'viewing_scheduled', 'closed') DEFAULT 'new',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'property_inquiries' created successfully.<br>";
    
    // Create properties table to store property information
    $sql = "CREATE TABLE `properties` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `property_name` varchar(200) NOT NULL,
        `property_type` enum('apartment', 'house', 'villa', 'commercial') NOT NULL,
        `category` enum('for_rent', 'for_sale', 'short_stay') NOT NULL,
        `location` varchar(200) NOT NULL,
        `price` decimal(10,2) NOT NULL,
        `currency` varchar(3) DEFAULT 'USD',
        `bedrooms` int(11) DEFAULT NULL,
        `bathrooms` int(11) DEFAULT NULL,
        `size` varchar(50) DEFAULT NULL,
        `description` text,
        `amenities` text,
        `images` text,
        `status` enum('available', 'rented', 'sold', 'maintenance') DEFAULT 'available',
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "Table 'properties' created successfully.<br>";
    
    // Insert sample properties data
    $sampleProperties = [
        [
            'property_name' => 'Nyarutarama Luxury Residence',
            'property_type' => 'apartment',
            'category' => 'for_rent',
            'location' => 'Nyarutarama, Kigali',
            'price' => 1200.00,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'size' => '150 m²',
            'description' => 'Luxurious 3-bedroom apartment with modern amenities and beautiful views.',
            'amenities' => 'Swimming pool, Gym, Security, Parking, High-speed internet'
        ],
        [
            'property_name' => 'Kiyovu Modern Flat',
            'property_type' => 'apartment',
            'category' => 'for_rent',
            'location' => 'Kiyovu, Kigali',
            'price' => 850.00,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'size' => '120 m²',
            'description' => 'Stylish 2-bedroom flat in the heart of Kiyovu.',
            'amenities' => 'High-speed internet, 24/7 security, Shared garden'
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO properties (property_name, property_type, category, location, price, bedrooms, bathrooms, size, description, amenities) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($sampleProperties as $property) {
        $stmt->execute([
            $property['property_name'],
            $property['property_type'],
            $property['category'],
            $property['location'],
            $property['price'],
            $property['bedrooms'],
            $property['bathrooms'],
            $property['size'],
            $property['description'],
            $property['amenities']
        ]);
    }
    echo "Sample properties inserted successfully.<br>";
    
    echo "<br><strong>Database setup completed successfully!</strong><br>";
    echo "All tables have been created and are ready to use.<br>";
    echo "<a href='contact.html'>Go to Contact Page</a> | <a href='index.html'>Go to Homepage</a>";
    
} catch (PDOException $e) {
    echo "Database setup failed: " . $e->getMessage();
}
?> 