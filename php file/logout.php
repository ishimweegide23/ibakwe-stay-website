<?php
// Logout Handler for Kigali Housing Platform
// This file handles user logout

// Start session
session_start();

// Destroy all session data
session_destroy();

// Redirect to homepage
header('Location: index.html');
exit();
?> 