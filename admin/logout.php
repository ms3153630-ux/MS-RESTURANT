<?php
/**
 * Admin Logout
 */
require_once __DIR__ . '/../config/config.php';

unset($_SESSION['user']);
setFlash('info', 'Administrator logged out successfully.');
header("Location: " . BASE_URL . "admin/login.php");
exit;
