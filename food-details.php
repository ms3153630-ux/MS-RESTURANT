<?php
/**
 * Food Details Page - Detailed View, Ingredients & Single Item Review
 */
require_once __DIR__ . '/config/config.php';

$foodId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($foodId <= 0) {
    header("Location: " . BASE_URL . "menu.php");
    exit;
}

// Fetch Food Item
$stmt = $pdo->prepare("SELECT f.*, c.name AS category_name FROM foods f JOIN categories c ON f.category_id = c.id WHERE f.id = ? AND f.status = 'active'");
$stmt->execute([$foodId]);
$food = $stmt->fetch();

if (!$food) {
    setFlash('danger', 'Food item not found or currently unavailable.');
    header("Location: " . BASE_URL . "menu.php");
    exit;
}

$pageTitle = $food['name'] . " - Details & Ordering";
$pageDesc = substr(strip_tags($food['description']), 0, 150);

// Fetch Related Foods in same category
$relStmt = $pdo->prepare("SELECT f.*, c.name AS category_name FROM foods f JOIN categories c ON f.category_id = c.id WHERE f.category_id = ? AND f.id != ? AND f.status = 'active' LIMIT 3");
$relStmt->execute([$food['category_id'], $foodId]);
$relatedFoods = $relStmt->fetchAll();

// Fetch Food Reviews
$revStmt = $pdo->prepare("SELECT r.*, u.name AS user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE (r.food_id = ? OR r.food_id IS NULL) AND r.status = 'approved' ORDER BY r.id DESC LIMIT 5");
$revStmt->execute([$foodId]);
$reviews = $revStmt->fetchAll();

$settings = getSettings($pdo);
$waOrderMsg = formatWhatsAppFoodItemMessage($food['name'], $food['price']);
$waOrderUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $waOrderMsg);

include __DIR__ . '/includes/header.php';
?>

<div class="py-4 bg-light">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php" class="text-warning text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>menu.php" class="text-warning text-decoration-none">Menu</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>menu.php?cat=<?= $food['category_id'] ?>" class="text-warning text-decoration-none"><?= htmlspecialchars($food['category_name']) ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($food['name']) ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="py-5 bg-white">
  <div class="container py-3">
    <div class="row g-5 align-items-center">
      <!-- Image Column -->
      <div class="col-lg-6">
        <div class="position-relative overflow-hidden rounded-4 shadow-lg border">
          <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($food['image']) ?>" alt="<?= htmlspecialchars($food['name']) ?>" class="img-fluid w-100" style="max-height: 480px; object-fit: cover;">
          <span class="position-absolute top-0 start-0 bg-dark text-white fw-bold px-3 py-2 m-3 rounded-pill small">
            <i class="fa-solid fa-utensils me-1 text-warning"></i> <?= htmlspecialchars($food['category_name']) ?>
          </span>
        </div>
      </div>

      <!-- Food Info Column -->
      <div class="col-lg-6">
        <div class="d-flex align-items-center gap-2 mb-2">
          <div class="text-warning fs-5">
            <?php for ($s = 1; $s <= 5; $s++): ?>
              <i class="fa-<?= ($s <= round($food['rating'])) ? 'solid' : 'regular' ?> fa-star"></i>
            <?php endfor; ?>
          </div>
          <span class="fw-bold fs-6 text-dark ms-1"><?= number_format($food['rating'], 1) ?> / 5.0</span>
          <span class="text-muted small">(<?= count($reviews) ?> reviews)</span>
        </div>

        <h1 class="display-5 fw-bold font-serif mb-3 text-dark"><?= htmlspecialchars($food['name']) ?></h1>
        <div class="display-6 fw-bold text-primary mb-4"><?= formatPrice($food['price']) ?></div>

        <p class="text-muted lead fs-6 mb-4 leading-relaxed">
          <?= nl2br(htmlspecialchars($food['description'])) ?>
        </p>

        <!-- Ingredients Showcase -->
        <?php if (!empty($food['ingredients'])): ?>
          <div class="p-3 bg-light rounded-3 mb-4 border">
            <h6 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-kitchen-set text-warning me-2"></i>Fresh Ingredients:</h6>
            <div class="d-flex flex-wrap gap-2">
              <?php foreach (explode(',', $food['ingredients']) as $ing): ?>
                <span class="badge bg-white text-dark border px-3 py-2 rounded-pill font-monospace small">
                  <?= htmlspecialchars(trim($ing)) ?>
                </span>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Add To Cart Form -->
        <form action="<?= BASE_URL ?>cart.php" method="GET" class="d-flex flex-wrap align-items-center gap-3 mb-4">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="id" value="<?= $food['id'] ?>">

          <div class="input-group" style="width: 140px;">
            <button class="btn btn-outline-secondary qty-btn qty-minus" type="button"><i class="fa-solid fa-minus"></i></button>
            <input type="number" name="quantity" class="form-control text-center qty-input fw-bold" value="1" min="1" max="50">
            <button class="btn btn-outline-secondary qty-btn qty-plus" type="button"><i class="fa-solid fa-plus"></i></button>
          </div>

          <button type="submit" class="btn btn-primary-custom px-4 py-2">
            <i class="fa-solid fa-cart-shopping me-2"></i> Add to Cart
          </button>

          <a href="<?= $waOrderUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp-custom px-4 py-2">
            <i class="fa-brands fa-whatsapp me-2"></i> Order on WhatsApp
          </a>
        </form>

        <div class="pt-3 border-top d-flex gap-4 text-muted small">
          <span><i class="fa-solid fa-truck text-success me-1"></i> Fast Doorstep Delivery</span>
          <span><i class="fa-solid fa-shield-halved text-primary me-1"></i> Fresh &amp; Hygienic</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Related Food Delicacies -->
<?php if (!empty($relatedFoods)): ?>
<section class="py-5 bg-light">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <span class="section-subtitle">More From <?= htmlspecialchars($food['category_name']) ?></span>
        <h3 class="section-title text-start mb-0">You Might Also Relish</h3>
      </div>
      <a href="<?= BASE_URL ?>menu.php?cat=<?= $food['category_id'] ?>" class="btn btn-outline-dark rounded-pill px-3 py-2 small fw-semibold">
        View Category <i class="fa-solid fa-arrow-right ms-1"></i>
      </a>
    </div>

    <div class="row g-4">
      <?php foreach ($relatedFoods as $rel): ?>
        <?php
          $relWaMsg = formatWhatsAppFoodItemMessage($rel['name'], $rel['price']);
          $relWaUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $relWaMsg);
        ?>
        <div class="col-md-4">
          <div class="food-card">
            <div class="food-card-img-wrapper" style="height: 180px;">
              <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($rel['image']) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" class="food-card-img">
              <span class="food-badge-rating"><i class="fa-solid fa-star"></i> <?= number_format($rel['rating'], 1) ?></span>
            </div>
            <div class="food-card-body">
              <h5 class="food-card-title">
                <a href="<?= BASE_URL ?>food-details.php?id=<?= $rel['id'] ?>"><?= htmlspecialchars($rel['name']) ?></a>
              </h5>
              <div class="food-card-price"><?= formatPrice($rel['price']) ?></div>
              <div class="food-card-actions">
                <a href="<?= BASE_URL ?>cart.php?action=add&id=<?= $rel['id'] ?>" class="btn-card-add">
                  <i class="fa-solid fa-cart-plus"></i> Add
                </a>
                <a href="<?= $relWaUrl ?>" target="_blank" rel="noopener noreferrer" class="btn-card-wa">
                  <i class="fa-brands fa-whatsapp"></i>
                </a>
                <a href="<?= BASE_URL ?>food-details.php?id=<?= $rel['id'] ?>" class="btn-card-detail">
                  <i class="fa-solid fa-eye"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
