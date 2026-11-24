<?php
/**
 * Customer Order Details Page
 */
require_once __DIR__ . '/../includes/auth.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId  = $_SESSION['user']['id'];

// Fetch order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND (user_id = ? OR ? = 'admin')");
$stmt->execute([$orderId, $userId, $_SESSION['user']['role']]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('danger', 'Order not found or permission denied.');
    header("Location: " . BASE_URL . "user/orders.php");
    exit;
}

// Fetch order items
$itemStmt = $pdo->prepare("SELECT oi.*, f.image FROM order_items oi LEFT JOIN foods f ON oi.food_id = f.id WHERE oi.order_id = ?");
$itemStmt->execute([$order['id']]);
$items = $itemStmt->fetchAll();

$settings = getSettings($pdo);

// WhatsApp status check message
$waCheckMsg = "Hello, I want to inquire about my order status.\n\nOrder ID: {$order['order_number']}\nCustomer: {$order['customer_name']}\nPhone: {$order['customer_phone']}\nTotal: Rs. " . number_format($order['total_amount'], 0);
$waCheckUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $waCheckMsg);

$pageTitle = "Order Details #" . $order['order_number'];
include __DIR__ . '/../includes/header.php';
?>

<div class="py-4 bg-light">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div>
        <h1 class="display-6 fw-bold font-serif mb-1">Order #<?= htmlspecialchars($order['order_number']) ?></h1>
        <p class="text-muted mb-0">Placed on <?= formatDate($order['created_at']) ?></p>
      </div>
      <div>
        <a href="<?= BASE_URL ?>user/orders.php" class="btn btn-outline-dark rounded-pill px-4">
          <i class="fa-solid fa-arrow-left me-1"></i> Back to Orders
        </a>
      </div>
    </div>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Ordered Items Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
          <h4 class="fw-bold font-serif mb-4 border-bottom pb-3">Dishes in this Order</h4>

          <div class="table-responsive">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th>Item</th>
                  <th class="text-center">Price</th>
                  <th class="text-center">Quantity</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <?php if (!empty($item['image'])): ?>
                          <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($item['image']) ?>" alt="" class="rounded-2" style="width: 50px; height: 50px; object-fit: cover;">
                        <?php endif; ?>
                        <div>
                          <strong><?= htmlspecialchars($item['food_name']) ?></strong>
                        </div>
                      </div>
                    </td>
                    <td class="text-center"><?= formatPrice($item['price']) ?></td>
                    <td class="text-center fw-bold">&times; <?= $item['quantity'] ?></td>
                    <td class="text-end fw-bold text-dark"><?= formatPrice($item['subtotal']) ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr>
                  <td colspan="3" class="text-end text-muted">Delivery Charges:</td>
                  <td class="text-end fw-bold text-success"><?= formatPrice($order['delivery_fee']) ?></td>
                </tr>
                <tr class="table-light">
                  <td colspan="3" class="text-end fw-bold fs-5">Total Amount:</td>
                  <td class="text-end fw-bold text-primary fs-5"><?= formatPrice($order['total_amount']) ?></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Order Status & Summary Sidebar -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
          <h4 class="fw-bold font-serif mb-3 border-bottom pb-3">Delivery &amp; Status</h4>

          <div class="mb-3">
            <label class="text-muted small d-block">Current Status:</label>
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold fs-6">
              <?= htmlspecialchars($order['order_status']) ?>
            </span>
          </div>

          <div class="mb-3">
            <label class="text-muted small d-block">Payment Method:</label>
            <strong><?= htmlspecialchars($order['payment_method']) ?></strong>
            <span class="badge bg-light text-muted border ms-1"><?= ucfirst($order['payment_status']) ?></span>
          </div>

          <div class="mb-3">
            <label class="text-muted small d-block">Contact Phone:</label>
            <strong><?= htmlspecialchars($order['customer_phone']) ?></strong>
          </div>

          <div class="mb-3">
            <label class="text-muted small d-block">Delivery Address:</label>
            <p class="mb-0 text-dark small fw-semibold"><?= htmlspecialchars($order['delivery_address']) ?>, <?= htmlspecialchars($order['city']) ?></p>
          </div>

          <?php if (!empty($order['notes'])): ?>
            <div class="mb-3">
              <label class="text-muted small d-block">Order Instructions:</label>
              <p class="mb-0 text-muted small fst-italic">"<?= htmlspecialchars($order['notes']) ?>"</p>
            </div>
          <?php endif; ?>

          <hr>

          <a href="<?= $waCheckUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp-custom w-100 justify-content-center py-2 fw-semibold">
            <i class="fa-brands fa-whatsapp fs-5"></i> Track on WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
