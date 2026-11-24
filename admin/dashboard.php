<?php
/**
 * Admin Dashboard - Analytics, Statistics & Recent Operations
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

// Calculate Statistics
$totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalFoods = (int)$pdo->query("SELECT COUNT(*) FROM foods")->fetchColumn();
$totalOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'")->fetchColumn();
$completedOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'Delivered'")->fetchColumn();
$totalRevenue = (float)$pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status != 'Cancelled'")->fetchColumn();
$totalReservations = (int)$pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$totalReviews = (int)$pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();

// Fetch Recent Orders (latest 5)
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();

// Fetch Recent Reservations (latest 5)
$recentReservations = $pdo->query("SELECT * FROM reservations ORDER BY id DESC LIMIT 5")->fetchAll();

$pageTitle = "Executive Dashboard";
include __DIR__ . '/header.php';
?>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <p>Total Revenue</p>
        <h3 class="text-success"><?= formatPrice($totalRevenue) ?></h3>
      </div>
      <div class="stat-icon bg-success-subtle text-success">
        <i class="fa-solid fa-coins"></i>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <p>Total Orders</p>
        <h3><?= $totalOrders ?></h3>
      </div>
      <div class="stat-icon bg-primary-subtle text-primary">
        <i class="fa-solid fa-cart-shopping"></i>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <p>Pending Orders</p>
        <h3 class="text-danger"><?= $pendingOrders ?></h3>
      </div>
      <div class="stat-icon bg-danger-subtle text-danger">
        <i class="fa-solid fa-clock"></i>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <p>Completed Orders</p>
        <h3 class="text-info"><?= $completedOrders ?></h3>
      </div>
      <div class="stat-icon bg-info-subtle text-info">
        <i class="fa-solid fa-circle-check"></i>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <p>Food Items</p>
        <h3><?= $totalFoods ?></h3>
      </div>
      <div class="stat-icon bg-warning-subtle text-warning">
        <i class="fa-solid fa-bowl-food"></i>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <p>Total Customers</p>
        <h3><?= $totalUsers ?></h3>
      </div>
      <div class="stat-icon bg-secondary-subtle text-dark">
        <i class="fa-solid fa-users"></i>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <p>Table Bookings</p>
        <h3><?= $totalReservations ?></h3>
      </div>
      <div class="stat-icon bg-purple-subtle text-primary" style="background:#ede9fe;">
        <i class="fa-solid fa-calendar-days"></i>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-info">
        <p>Diner Reviews</p>
        <h3><?= $totalReviews ?></h3>
      </div>
      <div class="stat-icon bg-warning-subtle text-warning">
        <i class="fa-solid fa-star"></i>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- Recent Orders Table -->
  <div class="col-lg-7">
    <div class="admin-card">
      <div class="admin-card-header">
        <h6 class="admin-card-title"><i class="fa-solid fa-bag-shopping text-warning me-2"></i>Recent Online Orders</h6>
        <a href="<?= BASE_URL ?>admin/orders.php" class="btn btn-sm btn-outline-dark rounded-pill px-3">View All Orders</a>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Status</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($recentOrders)): ?>
              <?php foreach ($recentOrders as $ro): ?>
                <tr>
                  <td>
                    <strong><a href="<?= BASE_URL ?>admin/orders.php?view=<?= $ro['id'] ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($ro['order_number']) ?></a></strong>
                  </td>
                  <td>
                    <div class="small fw-semibold"><?= htmlspecialchars($ro['customer_name']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($ro['customer_phone']) ?></div>
                  </td>
                  <td class="fw-bold text-primary small"><?= formatPrice($ro['total_amount']) ?></td>
                  <td>
                    <?php
                      $bClass = match($ro['order_status']) {
                        'Delivered' => 'badge-delivered',
                        'Cancelled' => 'badge-cancelled',
                        'Preparing' => 'badge-preparing',
                        'Out for Delivery' => 'badge-out-for-delivery',
                        'Confirmed' => 'badge-confirmed',
                        default => 'badge-pending'
                      };
                    ?>
                    <span class="badge-status <?= $bClass ?>"><?= $ro['order_status'] ?></span>
                  </td>
                  <td class="text-muted small"><?= date('M d, H:i', strtotime($ro['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center text-muted py-4">No recent orders.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Recent Table Bookings -->
  <div class="col-lg-5">
    <div class="admin-card">
      <div class="admin-card-header">
        <h6 class="admin-card-title"><i class="fa-solid fa-calendar-check text-info me-2"></i>Recent Reservations</h6>
        <a href="<?= BASE_URL ?>admin/reservations.php" class="btn btn-sm btn-outline-dark rounded-pill px-3">Manage</a>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Diner</th>
              <th>Date &amp; Time</th>
              <th>Guests</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($recentReservations)): ?>
              <?php foreach ($recentReservations as $rr): ?>
                <tr>
                  <td>
                    <div class="fw-semibold small"><?= htmlspecialchars($rr['customer_name']) ?></div>
                    <div class="text-muted small"><?= htmlspecialchars($rr['phone']) ?></div>
                  </td>
                  <td class="small">
                    <div><?= date('M d, Y', strtotime($rr['reservation_date'])) ?></div>
                    <div class="text-muted"><?= date('h:i A', strtotime($rr['reservation_time'])) ?></div>
                  </td>
                  <td class="small fw-bold"><?= $rr['guests'] ?></td>
                  <td>
                    <span class="badge-status <?= ($rr['status'] == 'Confirmed') ? 'badge-delivered' : (($rr['status'] == 'Cancelled') ? 'badge-cancelled' : 'badge-pending') ?>">
                      <?= $rr['status'] ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" class="text-center text-muted py-4">No reservations yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
