<?php
require_once __DIR__.'/../config/database.php';
require_once __DIR__.'/../includes/functions.php';

session_start();

// Clear remember token if exists
if (isset($_COOKIE['remember_token'])) {
    $db = (new Database())->connect();
    $stmt = $db->prepare("UPDATE users SET remember_token = NULL, remember_token_expiry = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id'] ?? 0]);
    
    setcookie('remember_token', '', time() - 3600, '/');
}

// Destroy session
$_SESSION = [];
session_destroy();

set_flash_message('success', 'You have been logged out successfully.');
redirect('/auth/login.php');