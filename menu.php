<?php
/**
 * Menu Page - Saffron & Spice Gourmet Restaurant
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = "Gourmet Food Menu";
$pageDesc = "Browse our complete food menu: Starters, BBQ, Burgers, Pizza, Chinese, Pakistani, Fast Food, Drinks, and Desserts.";

// Filters
$selectedCat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$searchTerm  = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortOption  = isset($_GET['sort']) ? trim($_GET['sort']) : 'default';
$minPrice    = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$maxPrice    = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 10000;

// Fetch All Active Categories
$catStmt = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY id ASC");
$categories = $catStmt->fetchAll();

// Build Query
$sql = "SELECT f.*, c.name AS category_name 
        FROM foods f 
        JOIN categories c ON f.category_id = c.id 
        WHERE f.status = 'active'";
$params = [];

if ($selectedCat > 0) {
    $sql .= " AND f.category_id = ?";
    $params[] = $selectedCat;
}

if (!empty($searchTerm)) {
    $sql .= " AND (f.name LIKE ? OR f.description LIKE ? OR c.name LIKE ?)";
    $like = "%$searchTerm%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($minPrice > 0) {
    $sql .= " AND f.price >= ?";
    $params[] = $minPrice;
}

if ($maxPrice < 10000) {
    $sql .= " AND f.price <= ?";
    $params[] = $maxPrice;
}

// Sorting
switch ($sortOption) {
    case 'price_asc':
        $sql .= " ORDER BY f.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY f.price DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY f.rating DESC";
        break;
    case 'name':
        $sql .= " ORDER BY f.name ASC";
        break;
    default:
        $sql .= " ORDER BY f.id ASC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$foods = $stmt->fetchAll();

$settings = getSettings($pdo);

include __DIR__ . '/includes/header.php';
?>

<!-- Menu Banner -->
<div class="py-5 bg-dark text-white text-center position-relative" style="background: linear-gradient(rgba(0,0,0,0.75), rgba(0,0,0,0.75)), url('<?= BASE_URL ?>assets/images/hero-bg.jpg') center/cover;">
  <div class="container py-4">
    <h1 class="display-4 fw-bold font-serif mb-2">Our Culinary Menu</h1>
    <p class="text-white-50 lead mb-0">Prepared fresh to order with love, premium ingredients, and authentic flair.</p>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <!-- Search & Filter Controls Card -->
    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
      <form method="GET" action="<?= BASE_URL ?>menu.php" class="row g-3 align-items-end">
        <!-- Search Input -->
        <div class="col-md-4">
          <label class="form-label fw-bold small text-muted"><i class="fa-solid fa-magnifying-glass me-1"></i> Search Food Items</label>
          <input type="text" name="search" id="menuSearchInput" class="form-control rounded-pill px-3 py-2" placeholder="e.g. Burger, Karahi, Pizza..." value="<?= htmlspecialchars($searchTerm) ?>">
        </div>

        <!-- Category Dropdown -->
        <div class="col-md-3">
          <label class="form-label fw-bold small text-muted"><i class="fa-solid fa-layer-group me-1"></i> Category</label>
          <select name="cat" class="form-select rounded-pill px-3 py-2">
            <option value="0">All Categories</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($selectedCat == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Sort Option -->
        <div class="col-md-3">
          <label class="form-label fw-bold small text-muted"><i class="fa-solid fa-arrow-down-short-wide me-1"></i> Sort By</label>
          <select name="sort" class="form-select rounded-pill px-3 py-2">
            <option value="default" <?= ($sortOption == 'default') ? 'selected' : '' ?>>Default Ordering</option>
            <option value="rating" <?= ($sortOption == 'rating') ? 'selected' : '' ?>>Highest Rated</option>
            <option value="price_asc" <?= ($sortOption == 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= ($sortOption == 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="name" <?= ($sortOption == 'name') ? 'selected' : '' ?>>Name: A to Z</option>
          </select>
        </div>

        <!-- Filter Submit & Reset -->
        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-warning rounded-pill flex-grow-1 py-2 fw-semibold">
            <i class="fa-solid fa-filter me-1"></i> Filter
          </button>
          <a href="<?= BASE_URL ?>menu.php" class="btn btn-outline-secondary rounded-pill py-2" title="Reset Filters">
            <i class="fa-solid fa-rotate-left"></i>
          </a>
        </div>
      </form>
    </div>

    <!-- Category Pills Filter Bar -->
    <div class="category-filter-nav">
      <a href="<?= BASE_URL ?>menu.php" class="category-filter-btn <?= ($selectedCat == 0) ? 'active' : '' ?>">
        <i class="fa-solid fa-utensils"></i> All Dishes
      </a>
      <?php foreach ($categories as $cat): ?>
        <a href="<?= BASE_URL ?>menu.php?cat=<?= $cat['id'] ?>" class="category-filter-btn <?= ($selectedCat == $cat['id']) ? 'active' : '' ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Foods Grid -->
    <?php if (!empty($foods)): ?>
      <div class="row g-4" id="foodCardsContainer">
        <?php foreach ($foods as $food): ?>
          <?php
            $waOrderMsg = formatWhatsAppFoodItemMessage($food['name'], $food['price']);
            $waOrderUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $waOrderMsg);
          ?>
          <div class="col-md-6 col-lg-4 menu-item-card" 
               data-name="<?= htmlspecialchars($food['name']) ?>" 
               data-desc="<?= htmlspecialchars($food['description']) ?>" 
               data-category="<?= htmlspecialchars($food['category_name']) ?>">
            <div class="food-card">
              <div class="food-card-img-wrapper">
                <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($food['image']) ?>" alt="<?= htmlspecialchars($food['name']) ?>" class="food-card-img" loading="lazy">
                <span class="food-badge-category"><?= htmlspecialchars($food['category_name']) ?></span>
                <span class="food-badge-rating"><i class="fa-solid fa-star"></i> <?= number_format($food['rating'], 1) ?></span>
              </div>
              <div class="food-card-body">
                <h5 class="food-card-title">
                  <a href="<?= BASE_URL ?>food-details.php?id=<?= $food['id'] ?>"><?= htmlspecialchars($food['name']) ?></a>
                </h5>
                <p class="food-card-desc"><?= htmlspecialchars($food['description']) ?></p>
                <div class="food-card-price"><?= formatPrice($food['price']) ?></div>
                
                <div class="food-card-actions">
                  <a href="<?= BASE_URL ?>cart.php?action=add&id=<?= $food['id'] ?>" class="btn-card-add">
                    <i class="fa-solid fa-cart-plus"></i> Add to Cart
                  </a>
                  <a href="<?= $waOrderUrl ?>" target="_blank" rel="noopener noreferrer" class="btn-card-wa" title="Order on WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                  </a>
                  <a href="<?= BASE_URL ?>food-details.php?id=<?= $food['id'] ?>" class="btn-card-detail" title="View Details">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="text-center py-5 bg-white rounded-4 shadow-sm border my-4">
        <div class="display-3 text-muted mb-3"><i class="fa-solid fa-plate-wheat"></i></div>
        <h4 class="fw-bold font-serif mb-2">No Food Items Found</h4>
        <p class="text-muted">No items matched your current filters. Try resetting your search or choosing another category.</p>
        <a href="<?= BASE_URL ?>menu.php" class="btn btn-warning rounded-pill px-4 py-2 fw-semibold">View All Dishes</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
