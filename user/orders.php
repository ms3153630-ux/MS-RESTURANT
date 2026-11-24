<?php
/**
 * Customer Order History
 */
require_once __DIR__ . '/../includes/auth.php';

$userId = $_SESSION['user']['id'];
$settings = getSettings($pdo);

// Fetch all orders for current user
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll();

$pageTitle = "My Food Orders";
include __DIR__ . '/../includes/header.php';
?>

<div class="py-4 bg-light">
  <div class="container">
    <h1 class="display-5 fw-bold font-serif mb-1">My Food Orders</h1>
    <p class="text-muted mb-0">Track real-time delivery status and view past dining orders.</p>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <!-- Profile Navigation Sidebar -->
      <div class="col-lg-3">
        <div class="list-group border-0 shadow-sm rounded-4 overflow-hidden mb-4">
          <a href="<?= BASE_URL ?>user/profile.php" class="list-group-item list-group-item-action fw-semibold py-3">
            <i class="fa-solid fa-user me-2 text-muted"></i> Profile &amp; Settings
          </a>
          <a href="<?= BASE_URL ?>user/orders.php" class="list-group-item list-group-item-action active fw-semibold py-3">
            <i class="fa-solid fa-bag-shopping me-2"></i> My Order History
          </a>
          <a href="<?= BASE_URL ?>reservation.php" class="list-group-item list-group-item-action fw-semibold py-3">
            <i class="fa-solid fa-calendar-check me-2 text-info"></i> Table Reservations
          </a>
          <a href="<?= BASE_URL ?>user/logout.php" class="list-group-item list-group-item-action text-danger fw-semibold py-3">
            <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Log Out
          </a>
        </div>
      </div>

      <!-- Orders List Column -->
      <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
          <h4 class="fw-bold font-serif mb-4 border-bottom pb-3">Past &amp; Active Orders</h4>

          <?php if (!empty($orders)): ?>
            <div class="table-responsive">
              <table class="table align-middle">
                <thead class="table-light">
                  <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $ord): ?>
                    <?php
                      $statusClass = match($ord['order_status']) {
                          'Delivered'        => 'bg-success text-white',
                          'Cancelled'        => 'bg-danger text-white',
                          'Out for Delivery' => 'bg-info text-dark',
                          'Preparing'        => 'bg-primary text-white',
                          'Confirmed'        => 'bg-secondary text-white',
                          default            => 'bg-warning text-dark'
                      };
                    ?>
                    <tr>
                      <td class="fw-bold">
                        <a href="<?= BASE_URL ?>user/order-details.php?id=<?= $ord['id'] ?>" class="text-dark text-decoration-none">
                          <?= htmlspecialchars($ord['order_number']) ?>
                        </a>
                      </td>
                      <td class="small text-muted"><?= formatDate($ord['created_at']) ?></td>
                      <td class="fw-bold text-primary"><?= formatPrice($ord['total_amount']) ?></td>
                      <td class="small"><?= htmlspecialchars($ord['payment_method']) ?></td>
                      <td>
                        <span class="badge <?= $statusClass ?> rounded-pill px-3 py-2 small">
                          <?= htmlspecialchars($ord['order_status']) ?>
                        </span>
                      </td>
                      <td class="text-end">
                        <a href="<?= BASE_URL ?>user/order-details.php?id=<?= $ord['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1">
                          View Details
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="text-center py-5">
              <div class="display-3 text-muted mb-3"><i class="fa-solid fa-bag-shopping"></i></div>
              <h5 class="fw-bold">No Orders Placed Yet</h5>
              <p class="text-muted">You haven't ordered any delicious food yet. Check out our culinary menu!</p>
              <a href="<?= BASE_URL ?>menu.php" class="btn btn-warning rounded-pill px-4 py-2 fw-semibold">
                Explore Menu
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
