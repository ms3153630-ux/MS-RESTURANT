<?php
/**
 * Admin Panel Header & Navigation Sidebar
 */
require_once __DIR__ . '/../includes/admin_auth.php';

$settings = getSettings($pdo);
$currentAdminPage = basename($_SERVER['PHP_SELF']);

// Quick counts for sidebar badges
$pendingOrdersCount = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();
$pendingReservationsCount = (int)$pdo->query("SELECT COUNT(*) FROM reservations WHERE status = 'Pending'")->fetchColumn();
$unreadMessagesCount = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'unread'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . " | " : "" ?>Admin Panel | <?= htmlspecialchars($settings['restaurant_name']) ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>
<body class="admin-body">

  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <a href="<?= BASE_URL ?>admin/dashboard.php" class="admin-sidebar-brand">
      <i class="fa-solid fa-utensils text-warning"></i>
      <span>Admin</span>Portal
    </a>

    <ul class="admin-nav">
      <li class="admin-nav-item">
        <a href="<?= BASE_URL ?>admin/dashboard.php" class="admin-nav-link <?= ($currentAdminPage == 'dashboard.php') ? 'active' : '' ?>">
          <i class="fa-solid fa-gauge-high"></i> Dashboard
        </a>
      </li>

      <li class="admin-nav-section-title">Menu &amp; Catalog</li>

      <li class="admin-nav-item">
        <a href="<?= BASE_URL ?>admin/categories.php" class="admin-nav-link <?= ($currentAdminPage == 'categories.php') ? 'active' : '' ?>">
          <i class="fa-solid fa-layer-group"></i> Categories
        </a>
      </li>

      <li class="admin-nav-item">
        <a href="<?= BASE_URL ?>admin/foods.php" class="admin-nav-link <?= ($currentAdminPage == 'foods.php') ? 'active' : '' ?>">
          <i class="fa-solid fa-bowl-food"></i> Food Items
        </a>
      </li>

      <li class="admin-nav-section-title">Operations</li>

      <li class="admin-nav-item">
        <a href="<?= BASE_URL ?>admin/orders.php" class="admin-nav-link <?= ($currentAdminPage == 'orders.php') ? 'active' : '' ?>">
          <i class="fa-solid fa-cart-shopping"></i> Orders
          <?php if ($pendingOrdersCount > 0): ?>
            <span class="badge bg-danger rounded-pill ms-auto"><?= $pendingOrdersCount ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li class="admin-nav-item">
        <a href="<?= BASE_URL ?>admin/reservations.php" class="admin-nav-link <?= ($currentAdminPage == 'reservations.php') ? 'active' : '' ?>">
          <i class="fa-solid fa-calendar-check"></i> Reservations
          <?php if ($pendingReservationsCount > 0): ?>
            <span class="badge bg-warning text-dark rounded-pill ms-auto"><?= $pendingReservationsCount ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li class="admin-nav-item">
        <a href="<?= BASE_URL ?>admin/customers.php" class="admin-nav-link <?= ($currentAdminPage == 'customers.php') ? 'active' : '' ?>">
          <i class="fa-solid fa-users"></i> Customers
        </a>
      </li>

      <li class="admin-nav-section-title">Engagement</li>

      <li class="admin-nav-item">
        <a href="<?= BASE_URL ?>admin/reviews.php" class="admin-nav-link <?= ($currentAdminPage == 'reviews.php') ? 'active' : '' ?>">
          <i class="fa-solid fa-star"></i> Reviews
        </a>
      </li>

      <li class="admin-nav-item">
        <a href="<?= BASE_URL ?>admin/messages.php" class="admin-nav-link <?= ($currentAdminPage == 'messages.php') ? 'active' : '' ?>">
          <i class="fa-solid fa-envelope"></i> Messages
          <?php if ($unreadMessagesCount > 0): ?>
            <span class="badge bg-info text-dark rounded-pill ms-auto"><?= $unreadMessagesCount ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li class="admin-nav-section-title">Configuration</li>

      <li class="admin-nav-item">
        <a href="<?= BASE_URL ?>admin/settings.php" class="admin-nav-link <?= ($currentAdminPage == 'settings.php') ? 'active' : '' ?>">
          <i class="fa-solid fa-sliders"></i> Restaurant Settings
        </a>
      </li>

      <li class="admin-nav-item mt-3">
        <a href="<?= BASE_URL ?>admin/logout.php" class="admin-nav-link text-danger">
          <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
        </a>
      </li>
    </ul>
  </aside>

  <!-- Main Wrapper -->
  <div class="admin-main-wrapper">
    <!-- Topbar -->
    <header class="admin-topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-secondary btn-sm d-lg-none" type="button" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
          <i class="fa-solid fa-bars"></i>
        </button>
        <h5 class="admin-page-title"><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : "Management Dashboard" ?></h5>
      </div>

      <div class="d-flex align-items-center gap-3">
        <a href="<?= BASE_URL ?>index.php" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
          <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Live Website
        </a>

        <div class="dropdown">
          <button class="btn btn-light rounded-pill px-3 py-1 dropdown-toggle border" type="button" id="adminDropdown" data-bs-toggle="dropdown">
            <i class="fa-solid fa-user-gear me-1 text-primary"></i> <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Admin') ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow border-0">
            <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/settings.php"><i class="fa-solid fa-gear me-2 text-muted"></i> Settings</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>admin/logout.php"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </header>

    <div class="admin-content">
      <?php displayFlash(); ?>
