<?php
/**
 * Admin Orders Management & Status Workflow
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = sanitize($_POST['order_status']);
    $allowedStatuses = ['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'];

    if (in_array($newStatus, $allowedStatuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
        setFlash('success', "Order #{$orderId} status changed to '{$newStatus}'.");
    }
    header("Location: " . BASE_URL . "admin/orders.php" . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
    exit;
}

// Handle Order Deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $orderId = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    setFlash('warning', "Order #{$orderId} removed.");
    header("Location: " . BASE_URL . "admin/orders.php");
    exit;
}

// Filter Status
$filterStatus = sanitize($_GET['status'] ?? 'all');
$sql = "SELECT * FROM orders";
$params = [];
if ($filterStatus !== 'all' && !empty($filterStatus)) {
    $sql .= " WHERE order_status = ?";
    $params[] = $filterStatus;
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// If viewing specific order modal or drawer
$viewOrderId = (int)($_GET['view'] ?? 0);
$viewOrder = null;
$viewItems = [];
if ($viewOrderId > 0) {
    $vStmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $vStmt->execute([$viewOrderId]);
    $viewOrder = $vStmt->fetch();

    if ($viewOrder) {
        $iStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $iStmt->execute([$viewOrderId]);
        $viewItems = $iStmt->fetchAll();
    }
}

$pageTitle = "Manage Customer Orders";
include __DIR__ . '/header.php';
?>

<!-- Status Filter Tabs -->
<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
  <div class="d-flex flex-wrap gap-2 align-items-center">
    <span class="fw-bold small text-muted me-2"><i class="fa-solid fa-filter me-1"></i>Filter by Status:</span>
    <a href="<?= BASE_URL ?>admin/orders.php?status=all" class="btn btn-sm <?= ($filterStatus === 'all') ? 'btn-dark' : 'btn-outline-secondary' ?> rounded-pill px-3">
      All Orders
    </a>
    <a href="<?= BASE_URL ?>admin/orders.php?status=Pending" class="btn btn-sm <?= ($filterStatus === 'Pending') ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning' ?> rounded-pill px-3">
      Pending
    </a>
    <a href="<?= BASE_URL ?>admin/orders.php?status=Confirmed" class="btn btn-sm <?= ($filterStatus === 'Confirmed') ? 'btn-info text-dark fw-bold' : 'btn-outline-info' ?> rounded-pill px-3">
      Confirmed
    </a>
    <a href="<?= BASE_URL ?>admin/orders.php?status=Preparing" class="btn btn-sm <?= ($filterStatus === 'Preparing') ? 'btn-primary text-white fw-bold' : 'btn-outline-primary' ?> rounded-pill px-3">
      Preparing
    </a>
    <a href="<?= BASE_URL ?>admin/orders.php?status=Out+for+Delivery" class="btn btn-sm <?= ($filterStatus === 'Out for Delivery') ? 'btn-secondary text-white fw-bold' : 'btn-outline-secondary' ?> rounded-pill px-3">
      Out for Delivery
    </a>
    <a href="<?= BASE_URL ?>admin/orders.php?status=Delivered" class="btn btn-sm <?= ($filterStatus === 'Delivered') ? 'btn-success text-white fw-bold' : 'btn-outline-success' ?> rounded-pill px-3">
      Delivered
    </a>
    <a href="<?= BASE_URL ?>admin/orders.php?status=Cancelled" class="btn btn-sm <?= ($filterStatus === 'Cancelled') ? 'btn-danger text-white fw-bold' : 'btn-outline-danger' ?> rounded-pill px-3">
      Cancelled
    </a>
  </div>
</div>

<!-- Detailed Order Viewer Drawer if selected -->
<?php if ($viewOrder): ?>
  <div class="admin-card border-warning mb-4">
    <div class="admin-card-header bg-warning-subtle">
      <h6 class="admin-card-title text-dark">
        <i class="fa-solid fa-receipt me-2"></i>Order Breakdown: <strong><?= htmlspecialchars($viewOrder['order_number']) ?></strong>
      </h6>
      <a href="<?= BASE_URL ?>admin/orders.php" class="btn btn-sm btn-outline-dark">Close Detail</a>
    </div>
    <div class="admin-card-body">
      <div class="row g-4 mb-4">
        <div class="col-md-3">
          <small class="text-muted d-block">Customer Details:</small>
          <strong><?= htmlspecialchars($viewOrder['customer_name']) ?></strong>
          <div class="small text-muted"><?= htmlspecialchars($viewOrder['customer_phone']) ?></div>
          <div class="small text-muted"><?= htmlspecialchars($viewOrder['customer_email']) ?></div>
        </div>
        <div class="col-md-4">
          <small class="text-muted d-block">Delivery Address:</small>
          <div class="small fw-semibold"><?= htmlspecialchars($viewOrder['delivery_address']) ?>, <?= htmlspecialchars($viewOrder['city']) ?></div>
          <?php if (!empty($viewOrder['notes'])): ?>
            <div class="small text-muted fst-italic mt-1">"<?= htmlspecialchars($viewOrder['notes']) ?>"</div>
          <?php endif; ?>
        </div>
        <div class="col-md-3">
          <small class="text-muted d-block">Payment Method:</small>
          <strong><?= htmlspecialchars($viewOrder['payment_method']) ?></strong>
          <span class="badge bg-light text-dark border ms-1"><?= ucfirst($viewOrder['payment_status']) ?></span>
        </div>
        <div class="col-md-2">
          <small class="text-muted d-block">Change Status:</small>
          <form method="POST" action="<?= BASE_URL ?>admin/orders.php" class="mt-1">
            <input type="hidden" name="order_id" value="<?= $viewOrder['id'] ?>">
            <select name="order_status" class="form-select form-select-sm" onchange="this.form.submit()">
              <?php foreach (['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'] as $st): ?>
                <option value="<?= $st ?>" <?= ($viewOrder['order_status'] === $st) ? 'selected' : '' ?>><?= $st ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Dish</th>
              <th class="text-center">Quantity</th>
              <th class="text-end">Unit Price</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($viewItems as $vi): ?>
              <tr>
                <td><strong><?= htmlspecialchars($vi['food_name']) ?></strong></td>
                <td class="text-center"><?= $vi['quantity'] ?></td>
                <td class="text-end"><?= formatPrice($vi['price']) ?></td>
                <td class="text-end fw-bold"><?= formatPrice($vi['subtotal']) ?></td>
              </tr>
            <?php endforeach; ?>
            <tr>
              <td colspan="3" class="text-end text-muted">Delivery Charges:</td>
              <td class="text-end fw-bold text-success"><?= formatPrice($viewOrder['delivery_fee']) ?></td>
            </tr>
            <tr class="table-light">
              <td colspan="3" class="text-end fw-bold fs-6">Grand Total:</td>
              <td class="text-end fw-bold text-primary fs-6"><?= formatPrice($viewOrder['total_amount']) ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Orders Table -->
<div class="admin-card">
  <div class="admin-card-header">
    <h6 class="admin-card-title"><i class="fa-solid fa-list-check text-warning me-2"></i>Orders List (<?= count($orders) ?>)</h6>
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Order #</th>
          <th>Customer</th>
          <th>Phone</th>
          <th>Address</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($orders)): ?>
          <?php foreach ($orders as $ord): ?>
            <tr>
              <td>
                <strong>
                  <a href="<?= BASE_URL ?>admin/orders.php?view=<?= $ord['id'] ?>" class="text-dark text-decoration-none">
                    <?= htmlspecialchars($ord['order_number']) ?>
                  </a>
                </strong>
              </td>
              <td class="fw-semibold small"><?= htmlspecialchars($ord['customer_name']) ?></td>
              <td class="small text-muted"><?= htmlspecialchars($ord['customer_phone']) ?></td>
              <td class="small text-truncate" style="max-width: 180px;" title="<?= htmlspecialchars($ord['delivery_address']) ?>">
                <?= htmlspecialchars($ord['delivery_address']) ?>
              </td>
              <td class="fw-bold text-primary small"><?= formatPrice($ord['total_amount']) ?></td>
              <td>
                <form method="POST" action="<?= BASE_URL ?>admin/orders.php" class="d-inline">
                  <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                  <select name="order_status" class="form-select form-select-sm rounded-pill fw-semibold" style="width: auto; min-width: 140px;" onchange="this.form.submit()">
                    <?php foreach (['Pending', 'Confirmed', 'Preparing', 'Out for Delivery', 'Delivered', 'Cancelled'] as $st): ?>
                      <option value="<?= $st ?>" <?= ($ord['order_status'] === $st) ? 'selected' : '' ?>><?= $st ?></option>
                    <?php endforeach; ?>
                  </select>
                </form>
              </td>
              <td class="small text-muted"><?= date('M d, H:i', strtotime($ord['created_at'])) ?></td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-2">
                  <a href="<?= BASE_URL ?>admin/orders.php?view=<?= $ord['id'] ?>" class="btn btn-sm btn-outline-dark rounded-circle p-2" title="View Full Order Receipt">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                  <a href="<?= BASE_URL ?>admin/orders.php?action=delete&id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete Order Record" onclick="return confirm('Delete order record permanently?');">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center py-4 text-muted">No orders found for the selected status.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
