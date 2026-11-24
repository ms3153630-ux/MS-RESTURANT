<?php
/**
 * Checkout Page - Customer Details, Payment Method & Order Placement
 */
require_once __DIR__ . '/config/config.php';

$cart = getCartDetails($pdo);
if (empty($cart['items'])) {
    setFlash('warning', 'Your basket is empty. Please add food items before checking out.');
    header("Location: " . BASE_URL . "menu.php");
    exit;
}

$user = currentUser();
$settings = getSettings($pdo);
$errors = [];

// Form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName    = sanitize($_POST['customer_name'] ?? '');
    $customerEmail   = sanitize($_POST['customer_email'] ?? '');
    $customerPhone   = sanitize($_POST['customer_phone'] ?? '');
    $city            = sanitize($_POST['city'] ?? '');
    $deliveryAddress = sanitize($_POST['delivery_address'] ?? '');
    $notes           = sanitize($_POST['notes'] ?? '');
    $paymentMethod   = sanitize($_POST['payment_method'] ?? 'Cash on Delivery');

    // Validation
    if (empty($customerName)) $errors[] = "Please provide your full name.";
    if (empty($customerEmail) || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) $errors[] = "Please provide a valid email address.";
    if (empty($customerPhone)) $errors[] = "Please provide your active contact phone number.";
    if (empty($city)) $errors[] = "Please specify your delivery city.";
    if (empty($deliveryAddress)) $errors[] = "Please enter your complete delivery street address.";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
            $userId = $user ? $user['id'] : null;
            $totalAmount = $cart['grand_total'];
            $deliveryFee = $cart['delivery_fee'];
            $orderStatus = 'Pending';
            $paymentStatus = ($paymentMethod === 'Online Payment') ? 'paid' : 'pending';

            $orderSql = "INSERT INTO orders (order_number, user_id, customer_name, customer_email, customer_phone, delivery_address, city, notes, total_amount, delivery_fee, payment_method, payment_status, order_status, created_at)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($orderSql);
            $stmt->execute([
                $orderNumber,
                $userId,
                $customerName,
                $customerEmail,
                $customerPhone,
                $deliveryAddress,
                $city,
                $notes,
                $totalAmount,
                $deliveryFee,
                $paymentMethod,
                $paymentStatus,
                $orderStatus
            ]);
            $orderId = $pdo->lastInsertId();

            // Insert Order Items
            $itemSql = "INSERT INTO order_items (order_id, food_id, food_name, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?, ?)";
            $itemStmt = $pdo->prepare($itemSql);
            foreach ($cart['items'] as $item) {
                $itemStmt->execute([
                    $orderId,
                    $item['id'],
                    $item['name'],
                    $item['quantity'],
                    $item['price'],
                    $item['subtotal']
                ]);
            }

            $pdo->commit();

            // Clear session cart
            clearCart();

            setFlash('success', 'Your order #' . $orderNumber . ' was placed successfully!');
            header("Location: " . BASE_URL . "order-success.php?order=" . urlencode($orderNumber));
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Order placement failed: " . $e->getMessage();
        }
    }
}

// Default field values
$defName    = $user['name'] ?? '';
$defEmail   = $user['email'] ?? '';
$defPhone   = $user['phone'] ?? '';
$defAddress = $user['address'] ?? '';
$defCity    = 'Karachi';

$pageTitle = "Complete Checkout";
include __DIR__ . '/includes/header.php';
?>

<div class="py-4 bg-light">
  <div class="container">
    <h1 class="display-5 fw-bold font-serif mb-1">Checkout &amp; Place Order</h1>
    <p class="text-muted mb-0">Fill in your delivery details to receive your freshly prepared meal.</p>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger shadow-sm mb-4">
        <ul class="mb-0 ps-3">
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>checkout.php" class="row g-4">
      <!-- Left Column: Delivery Details -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
          <h4 class="fw-bold font-serif mb-3 border-bottom pb-3"><i class="fa-solid fa-truck-fast text-warning me-2"></i>1. Delivery Information</h4>
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control rounded-3" value="<?= htmlspecialchars($_POST['customer_name'] ?? $defName) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold small">Phone / Mobile <span class="text-danger">*</span></label>
              <input type="text" name="customer_phone" class="form-control rounded-3" placeholder="03001234567" value="<?= htmlspecialchars($_POST['customer_phone'] ?? $defPhone) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
              <input type="email" name="customer_email" class="form-control rounded-3" placeholder="ahmed@example.com" value="<?= htmlspecialchars($_POST['customer_email'] ?? $defEmail) ?>" required>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold small">City <span class="text-danger">*</span></label>
              <input type="text" name="city" class="form-control rounded-3" value="<?= htmlspecialchars($_POST['city'] ?? $defCity) ?>" required>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold small">Complete Street Delivery Address <span class="text-danger">*</span></label>
              <textarea name="delivery_address" rows="3" class="form-control rounded-3" placeholder="House/Flat number, street name, block/sector, landmark..." required><?= htmlspecialchars($_POST['delivery_address'] ?? $defAddress) ?></textarea>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold small">Special Delivery Notes / Instructions (Optional)</label>
              <input type="text" name="notes" class="form-control rounded-3" placeholder="e.g. Please ring bell twice, extra napkins, cutlery required..." value="<?= htmlspecialchars($_POST['notes'] ?? '') ?>">
            </div>
          </div>
        </div>

        <!-- Payment Method Selection -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
          <h4 class="fw-bold font-serif mb-3 border-bottom pb-3"><i class="fa-solid fa-credit-card text-warning me-2"></i>2. Payment Method</h4>
          
          <div class="form-check p-3 border rounded-3 mb-3 bg-light">
            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payCod" value="Cash on Delivery" checked>
            <label class="form-check-label fw-bold d-block" for="payCod">
              <i class="fa-solid fa-money-bill-wave text-success me-2"></i> Cash on Delivery (COD)
              <span class="d-block fw-normal text-muted small mt-1">Pay with cash to our delivery rider upon receipt of your warm food.</span>
            </label>
          </div>

          <div class="form-check p-3 border rounded-3 bg-light">
            <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="payOnline" value="Online Payment">
            <label class="form-check-label fw-bold d-block" for="payOnline">
              <i class="fa-solid fa-credit-card text-primary me-2"></i> Online Payment / Digital Wallet (Placeholder)
              <span class="d-block fw-normal text-muted small mt-1">Direct debit, Credit Card, or Mobile Wallet simulation.</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Right Column: Order Summary Review -->
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 90px;">
          <h4 class="fw-bold font-serif mb-3 border-bottom pb-3">Review Your Items</h4>

          <div class="cart-items-preview mb-3" style="max-height: 260px; overflow-y: auto;">
            <?php foreach ($cart['items'] as $item): ?>
              <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                  <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="rounded-2" style="width: 48px; height: 48px; object-fit: cover;">
                  <div>
                    <h6 class="mb-0 fw-bold small"><?= htmlspecialchars($item['name']) ?></h6>
                    <small class="text-muted"><?= formatPrice($item['price']) ?> &times; <?= $item['quantity'] ?></small>
                  </div>
                </div>
                <span class="fw-bold text-dark small"><?= formatPrice($item['subtotal']) ?></span>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Dishes Subtotal:</span>
            <span class="fw-bold"><?= formatPrice($cart['subtotal']) ?></span>
          </div>

          <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">Delivery Fee:</span>
            <span class="fw-bold text-success"><?= formatPrice($cart['delivery_fee']) ?></span>
          </div>

          <div class="border-top pt-3 mb-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Total Payable:</h5>
            <h4 class="fw-bold text-primary mb-0"><?= formatPrice($cart['grand_total']) ?></h4>
          </div>

          <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold fs-5 mb-2 shadow">
            <i class="fa-solid fa-lock me-2"></i>Confirm &amp; Place Order
          </button>

          <a href="<?= BASE_URL ?>cart.php" class="btn btn-outline-secondary w-100 rounded-pill py-2 small">
            <i class="fa-solid fa-pen-to-square me-1"></i>Edit Basket Items
          </a>
        </div>
      </div>
    </form>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
