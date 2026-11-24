<?php
/**
 * Administrator Authentication Guard
 * Protects admin panel routes
 */

require_once __DIR__ . '/../config/config.php';

if (!isAdmin()) {
    setFlash('danger', 'Unauthorized access! Administrator credentials required.');
    header("Location: " . BASE_URL . "admin/login.php");
    exit;
}
