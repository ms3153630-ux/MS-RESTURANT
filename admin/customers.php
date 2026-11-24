<?php
/**
 * Admin Customers Management
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

// Toggle Status
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE users SET status = IF(status='active', 'inactive', 'active') WHERE id = ? AND role = 'customer'");
    $stmt->execute([$userId]);
    setFlash('info', 'Customer status updated.');
    header("Location: " . BASE_URL . "admin/customers.php");
    exit;
}

// Fetch all customers with their order counts & spent amount
$customers = $pdo->query("SELECT u.*, 
                                 COUNT(o.id) AS total_orders, 
                                 COALESCE(SUM(o.total_amount), 0) AS total_spent 
                          FROM users u 
                          LEFT JOIN orders o ON u.id = o.user_id 
                          WHERE u.role = 'customer' 
                          GROUP BY u.id 
                          ORDER BY u.id DESC")->fetchAll();

$pageTitle = "Manage Registered Customers";
include __DIR__ . '/header.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <h6 class="admin-card-title"><i class="fa-solid fa-users text-primary me-2"></i>Registered Customer Accounts (<?= count($customers) ?>)</h6>
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Contact</th>
          <th>Delivery Address</th>
          <th>Orders</th>
          <th>Total Spent</th>
          <th>Joined</th>
          <th>Status</th>
          <th class="text-end">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($customers)): ?>
          <?php foreach ($customers as $c): ?>
            <tr>
              <td class="text-muted fw-bold">#<?= $c['id'] ?></td>
              <td>
                <div class="fw-bold"><?= htmlspecialchars($c['name']) ?></div>
              </td>
              <td>
                <div class="small fw-semibold"><?= htmlspecialchars($c['email']) ?></div>
                <div class="small text-muted"><?= htmlspecialchars($c['phone'] ?? 'N/A') ?></div>
              </td>
              <td class="small text-truncate" style="max-width: 220px;" title="<?= htmlspecialchars($c['address'] ?? '') ?>">
                <?= htmlspecialchars($c['address'] ?? 'Not specified') ?>
              </td>
              <td>
                <span class="badge bg-light text-dark border"><?= $c['total_orders'] ?> Orders</span>
              </td>
              <td class="fw-bold text-success small">
                <?= formatPrice($c['total_spent']) ?>
              </td>
              <td class="small text-muted"><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
              <td>
                <a href="<?= BASE_URL ?>admin/customers.php?action=toggle&id=<?= $c['id'] ?>" class="text-decoration-none">
                  <span class="badge-status <?= ($c['status'] === 'active') ? 'badge-active' : 'badge-inactive' ?>">
                    <?= $c['status'] ?>
                  </span>
                </a>
              </td>
              <td class="text-end">
                <a href="<?= BASE_URL ?>admin/customers.php?action=toggle&id=<?= $c['id'] ?>" class="btn btn-sm <?= ($c['status'] === 'active') ? 'btn-outline-danger' : 'btn-outline-success' ?> rounded-pill px-3 py-1">
                  <?= ($c['status'] === 'active') ? 'Deactivate' : 'Activate' ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="9" class="text-center py-4 text-muted">No customers registered yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
