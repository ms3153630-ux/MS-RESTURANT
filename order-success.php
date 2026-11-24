<?php
/**
 * Order Success Page - Confirmation, Details & WhatsApp Order Notification
 */
require_once __DIR__ . '/config/config.php';

$orderNumber = sanitize($_GET['order'] ?? '');
if (empty($orderNumber)) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

// Fetch Order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? LIMIT 1");
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('danger', 'Order not found.');
    header("Location: " . BASE_URL . "index.php");
    exit;
}

// Fetch Items
$itemStmt = $pdo->prepare("SELECT oi.*, f.image FROM order_items oi LEFT JOIN foods f ON oi.food_id = f.id WHERE oi.order_id = ?");
$itemStmt->execute([$order['id']]);
$items = $itemStmt->fetchAll();

$settings = getSettings($pdo);

// WhatsApp Order Notification URL
$waSuccessMsg = formatWhatsAppOrderMessage(
    $order['customer_name'],
    $order['customer_phone'],
    $order['delivery_address'] . ", " . $order['city'],
    $items,
    $order['delivery_fee'],
    $order['total_amount'],
    $order['order_number']
);
$waSuccessUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $waSuccessMsg);

$pageTitle = "Order Confirmed - " . $order['order_number'];
include __DIR__ . '/includes/header.php';
?>

<section class="py-5 bg-light">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <!-- Success Card -->
        <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center mb-4">
          <div class="mb-4">
            <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle shadow" style="width: 80px; height: 80px; font-size: 38px;">
              <i class="fa-solid fa-check"></i>
            </span>
          </div>

          <h2 class="fw-bold font-serif mb-2">Thank You For Your Order!</h2>
          <p class="text-muted lead mb-4">Your meal request has been transmitted directly to our kitchen station.</p>

          <div class="p-3 bg-light rounded-3 mb-4 text-start">
            <div class="row g-3">
              <div class="col-sm-6">
                <span class="text-muted small d-block">Order Reference:</span>
                <span class="fw-bold text-dark fs-5"><?= htmlspecialchars($order['order_number']) ?></span>
              </div>
              <div class="col-sm-6">
                <span class="text-muted small d-block">Order Status:</span>
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                  <i class="fa-solid fa-clock me-1"></i> <?= htmlspecialchars($order['order_status']) ?>
                </span>
              </div>
              <div class="col-sm-6">
                <span class="text-muted small d-block">Customer Name:</span>
                <span class="fw-semibold"><?= htmlspecialchars($order['customer_name']) ?></span>
              </div>
              <div class="col-sm-6">
                <span class="text-muted small d-block">Contact Phone:</span>
                <span class="fw-semibold"><?= htmlspecialchars($order['customer_phone']) ?></span>
              </div>
              <div class="col-12">
                <span class="text-muted small d-block">Delivery Address:</span>
                <span class="fw-semibold"><?= htmlspecialchars($order['delivery_address']) ?>, <?= htmlspecialchars($order['city']) ?></span>
              </div>
            </div>
          </div>

          <!-- Items Table -->
          <div class="table-responsive text-start mb-4">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th>Dish</th>
                  <th class="text-center">Qty</th>
                  <th class="text-end">Price</th>
                  <th class="text-end">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($items as $item): ?>
                  <tr>
                    <td>
                      <strong><?= htmlspecialchars($item['food_name']) ?></strong>
                    </td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-end"><?= formatPrice($item['price']) ?></td>
                    <td class="text-end fw-bold"><?= formatPrice($item['subtotal']) ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr>
                  <td colspan="3" class="text-end text-muted">Delivery Charges:</td>
                  <td class="text-end fw-bold text-success"><?= formatPrice($order['delivery_fee']) ?></td>
                </tr>
                <tr class="table-light">
                  <td colspan="3" class="text-end fw-bold fs-5">Grand Total:</td>
                  <td class="text-end fw-bold text-primary fs-5"><?= formatPrice($order['total_amount']) ?></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Action Buttons -->
          <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="<?= $waSuccessUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp-custom px-4 py-3 fw-bold">
              <i class="fa-brands fa-whatsapp fs-5"></i>Send Order to WhatsApp Kitchen
            </a>
            <a href="<?= BASE_URL ?>menu.php" class="btn btn-outline-dark rounded-pill px-4 py-3 fw-semibold">
              <i class="fa-solid fa-utensils me-2"></i>Continue Shopping
            </a>
            <?php if (isLoggedIn()): ?>
              <a href="<?= BASE_URL ?>user/orders.php" class="btn btn-warning rounded-pill px-4 py-3 fw-semibold">
                <i class="fa-solid fa-bag-shopping me-2"></i>View My Orders
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
