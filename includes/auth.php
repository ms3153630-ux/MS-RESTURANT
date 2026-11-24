<?php
/**
 * Customer Authentication Guard
 * Protects customer pages (profile, my-orders, submit review)
 */

require_once __DIR__ . '/../config/config.php';

if (!isLoggedIn()) {
    setFlash('warning', 'Please login to access your account.');
    $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '');
    header("Location: " . BASE_URL . "user/login.php?redirect=" . $redirect);
    exit;
}
