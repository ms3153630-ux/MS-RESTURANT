<?php
/**
 * Customer / Admin Logout Handler
 */
require_once __DIR__ . '/../config/config.php';

unset($_SESSION['user']);
setFlash('info', 'You have been successfully logged out. See you again soon!');
header("Location: " . BASE_URL . "user/login.php");
exit;
