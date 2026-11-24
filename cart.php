<?php
/**
 * Shopping Cart Management & WhatsApp Direct Ordering
 */
require_once __DIR__ . '/config/config.php';

// Handle Actions (add, update, remove, clear)
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$foodId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$qty    = (int)($_GET['quantity'] ?? $_POST['quantity'] ?? 1);

if ($action === 'add' && $foodId > 0) {
    addToCart($foodId, $qty);
    setFlash('success', 'Item successfully added to your food basket!');
    header("Location: " . BASE_URL . "cart.php");
    exit;
} elseif ($action === 'update' && $foodId > 0) {
    updateCartQuantity($foodId, $qty);
    setFlash('info', 'Cart quantity updated.');
    header("Location: " . BASE_URL . "cart.php");
    exit;
} elseif ($action === 'remove' && $foodId > 0) {
    removeFromCart($foodId);
    setFlash('warning', 'Item removed from your cart.');
    header("Location: " . BASE_URL . "cart.php");
    exit;
} elseif ($action === 'clear') {
    clearCart();
    setFlash('info', 'Your shopping cart has been cleared.');
    header("Location: " . BASE_URL . "cart.php");
    exit;
}

$cart = getCartDetails($pdo);
$settings = getSettings($pdo);

// Build Order on WhatsApp message for existing cart items
$currentUser = currentUser();
$customerName = $currentUser['name'] ?? 'Guest Customer';
$customerPhone = $currentUser['phone'] ?? 'N/A';
$customerAddress = $currentUser['address'] ?? 'Delivery Address Pending';

$waCartMsg = formatWhatsAppOrderMessage(
    $customerName,
    $customerPhone,
    $customerAddress,
    $cart['items'],
    $cart['delivery_fee'],
    $cart['grand_total']
);
$waCartUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $waCartMsg);

$pageTitle = "Your Food Cart";
include __DIR__ . '/includes/header.php';
?>

<!-- Cart Header -->
<div class="py-4 bg-light">
  <div class="container">
    <h1 class="display-5 fw-bold font-serif mb-1">Your Shopping Basket</h1>
    <p class="text-muted mb-0">Review selected dishes, adjust quantities, or proceed to checkout.</p>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <?php if (!empty($cart['items'])): ?>
      <div class="row g-4">
        <!-- Cart Table Column -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th scope="col" class="ps-4">Dish</th>
                    <th scope="col">Price</th>
                    <th scope="col" style="width: 140px;">Quantity</th>
                    <th scope="col">Subtotal</th>
                    <th scope="col" class="text-end pe-4">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($cart['items'] as $item): ?>
                    <tr>
                      <!-- Dish Image & Title -->
                      <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                          <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="rounded-3 shadow-sm" style="width: 65px; height: 65px; object-fit: cover;">
                          <div>
                            <h6 class="mb-0 fw-bold">
                              <a href="<?= BASE_URL ?>food-details.php?id=<?= $item['id'] ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($item['name']) ?></a>
                            </h6>
                            <small class="badge bg-light text-muted border"><?= htmlspecialchars($item['category']) ?></small>
                          </div>
                        </div>
                      </td>

                      <!-- Price -->
                      <td class="fw-semibold text-muted">
                        <?= formatPrice($item['price']) ?>
                      </td>

                      <!-- Quantity Control -->
                      <td>
                        <form action="<?= BASE_URL ?>cart.php" method="POST" class="cart-update-form d-flex align-items-center">
                          <input type="hidden" name="action" value="update">
                          <input type="hidden" name="id" value="<?= $item['id'] ?>">
                          <div class="input-group input-group-sm">
                            <button class="btn btn-outline-secondary qty-btn qty-minus" type="button"><i class="fa-solid fa-minus"></i></button>
                            <input type="number" name="quantity" class="form-control text-center qty-input fw-bold" value="<?= $item['quantity'] ?>" min="1" max="50">
                            <button class="btn btn-outline-secondary qty-btn qty-plus" type="button"><i class="fa-solid fa-plus"></i></button>
                          </div>
                        </form>
                      </td>

                      <!-- Subtotal -->
                      <td class="fw-bold text-primary">
                        <?= formatPrice($item['subtotal']) ?>
                      </td>

                      <!-- Remove -->
                      <td class="text-end pe-4">
                        <a href="<?= BASE_URL ?>cart.php?action=remove&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Remove Dish" onclick="return confirm('Remove this dish from cart?');">
                          <i class="fa-solid fa-trash-can"></i>
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between align-items-center p-3 border-top">
              <a href="<?= BASE_URL ?>menu.php" class="btn btn-outline-dark rounded-pill px-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Continue Shopping
              </a>
              <a href="<?= BASE_URL ?>cart.php?action=clear" class="btn btn-outline-danger rounded-pill px-4" onclick="return confirm('Are you sure you want to empty your basket?');">
                <i class="fa-solid fa-trash me-1"></i>Clear Basket
              </a>
            </div>
          </div>
        </div>

        <!-- Summary Column -->
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 90px;">
            <h4 class="fw-bold font-serif mb-3 border-bottom pb-3">Order Summary</h4>
            
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Items Subtotal:</span>
              <span class="fw-bold"><?= formatPrice($cart['subtotal']) ?></span>
            </div>

            <div class="d-flex justify-content-between mb-3">
              <span class="text-muted">Delivery Charges:</span>
              <span class="fw-bold text-success"><?= formatPrice($cart['delivery_fee']) ?></span>
            </div>

            <div class="border-top pt-3 mb-4 d-flex justify-content-between align-items-center">
              <h5 class="fw-bold mb-0">Grand Total:</h5>
              <h4 class="fw-bold text-primary mb-0"><?= formatPrice($cart['grand_total']) ?></h4>
            </div>

            <!-- Checkout Action -->
            <div class="d-grid gap-2">
              <a href="<?= BASE_URL ?>checkout.php" class="btn btn-primary-custom py-3 fw-bold text-center">
                <i class="fa-solid fa-bag-shopping me-2"></i>Proceed to Checkout
              </a>
              <a href="<?= $waCartUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp-custom py-3 justify-content-center fw-bold">
                <i class="fa-brands fa-whatsapp fs-5"></i>Order on WhatsApp
              </a>
            </div>

            <div class="alert alert-light border mt-4 mb-0 small text-muted">
              <i class="fa-solid fa-shield-halved text-success me-1"></i> Cash on Delivery available across the city. Average delivery time: 35-45 minutes.
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
        <div class="display-3 text-muted mb-3"><i class="fa-solid fa-cart-shopping"></i></div>
        <h3 class="fw-bold font-serif mb-2">Your Food Basket is Empty</h3>
        <p class="text-muted mb-4">You have not added any delectable dishes to your cart yet.</p>
        <a href="<?= BASE_URL ?>menu.php" class="btn btn-warning rounded-pill px-5 py-3 fw-bold">
          <i class="fa-solid fa-utensils me-2"></i>Explore Food Menu
        </a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
