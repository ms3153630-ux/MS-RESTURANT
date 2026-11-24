<?php
/**
 * Home Page - Saffron & Spice Gourmet Restaurant
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = "Welcome to Saffron & Spice Gourmet Restaurant";
$pageDesc = "Experience culinary excellence with gourmet burgers, charcoal BBQ, artisan pizza, authentic Pakistani curries, and rapid doorstep delivery.";

// Fetch Featured Foods
$stmt = $pdo->query("SELECT f.*, c.name AS category_name FROM foods f JOIN categories c ON f.category_id = c.id WHERE f.is_featured = 1 AND f.status = 'active' ORDER BY f.rating DESC LIMIT 6");
$featuredFoods = $stmt->fetchAll();

// Fetch Popular Dishes
$stmt = $pdo->query("SELECT f.*, c.name AS category_name FROM foods f JOIN categories c ON f.category_id = c.id WHERE f.is_popular = 1 AND f.status = 'active' ORDER BY f.id DESC LIMIT 4");
$popularDishes = $stmt->fetchAll();

// Fetch Special Offers
$stmt = $pdo->query("SELECT f.*, c.name AS category_name FROM foods f JOIN categories c ON f.category_id = c.id WHERE f.is_special = 1 AND f.status = 'active' ORDER BY f.id DESC LIMIT 3");
$specialOffers = $stmt->fetchAll();

// Fetch Customer Approved Reviews
$stmt = $pdo->query("SELECT r.*, u.name AS user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.status = 'approved' ORDER BY r.id DESC LIMIT 3");
$customerReviews = $stmt->fetchAll();

// Fetch Categories for quick browse
$stmt = $pdo->query("SELECT * FROM categories WHERE status = 'active' ORDER BY id ASC");
$allCategories = $stmt->fetchAll();

$settings = getSettings($pdo);
$heroWaMsg = "Hello! I am viewing your website and would like to browse the specials and place an order.";
$heroWaUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $heroWaMsg);

include __DIR__ . '/includes/header.php';
?>

<!-- 1. Hero Section -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <span class="hero-tagline"><i class="fa-solid fa-crown me-2"></i>Artisanal Flavors &amp; Heritage Spices</span>
        <h1 class="hero-title">A Symphony of <span>Exquisite Taste</span> &amp; Gourmet Passion</h1>
        <p class="hero-desc">
          Immerse yourself in authentic royal curries, juicy smashed burgers, wood-fired pizzas, and smokey charcoal BBQ grills crafted from farm-fresh ingredients.
        </p>
        <div class="hero-btns">
          <a href="<?= BASE_URL ?>menu.php" class="btn btn-primary-custom">
            <i class="fa-solid fa-book-open me-2"></i>View Full Menu
          </a>
          <a href="<?= BASE_URL ?>menu.php" class="btn btn-outline-custom">
            <i class="fa-solid fa-cart-shopping me-2"></i>Order Now
          </a>
          <a href="<?= $heroWaUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp-custom">
            <i class="fa-brands fa-whatsapp me-2"></i>Order on WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 2. Restaurant Introduction -->
<section class="py-5 bg-white">
  <div class="container py-4">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="position-relative">
          <img src="<?= BASE_URL ?>assets/images/about-img.jpg" alt="Restaurant Ambiance" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 440px; object-fit: cover;">
          <div class="position-absolute bottom-0 end-0 bg-warning text-dark p-3 rounded-3 shadow m-3 text-center fw-bold">
            <span class="fs-3 d-block">15+</span>
            <small>Years of Culinary Glory</small>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <span class="section-subtitle">Our Heritage</span>
        <h2 class="section-title text-start mb-3">Authentic Flavors Cooked with Royal Passion</h2>
        <p class="text-muted leading-relaxed mb-4">
          At <strong><?= htmlspecialchars($settings['restaurant_name']) ?></strong>, dining is an art form. Every spice blend is hand-ground, every cut of meat is chosen with meticulous care, and every flatbread is freshly baked inside our intense clay tandoors.
        </p>
        <div class="row g-3 mb-4">
          <div class="col-sm-6">
            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
              <i class="fa-solid fa-seedling text-success fs-3"></i>
              <div>
                <h6 class="mb-0 fw-bold">100% Fresh &amp; Halal</h6>
                <small class="text-muted">Direct from organic farms</small>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
              <i class="fa-solid fa-motorcycle text-warning fs-3"></i>
              <div>
                <h6 class="mb-0 fw-bold">Swift Hot Delivery</h6>
                <small class="text-muted">Under 35 mins doorstep</small>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex gap-3">
          <a href="<?= BASE_URL ?>about.php" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold">Learn More About Us</a>
          <a href="<?= BASE_URL ?>reservation.php" class="btn btn-warning rounded-pill px-4 py-2 fw-semibold">Reserve a Table</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 3. Categories Highlights -->
<section class="py-5 bg-light">
  <div class="container py-3">
    <div class="section-header">
      <span class="section-subtitle">Explore Categories</span>
      <h2 class="section-title">Discover Our Specialties</h2>
      <p class="section-desc">From sizzling hot BBQ grills to gourmet loaded pizzas and refreshing coolers, explore a diverse menu crafted for every craving.</p>
    </div>

    <div class="row g-4">
      <?php foreach (array_slice($allCategories, 0, 8) as $cat): ?>
        <div class="col-6 col-md-4 col-lg-3">
          <a href="<?= BASE_URL ?>menu.php?cat=<?= $cat['id'] ?>" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden text-center hover-lift transition">
              <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($cat['image'] ?? 'cat-burgers.jpg') ?>" class="card-img-top" alt="<?= htmlspecialchars($cat['name']) ?>" style="height: 160px; object-fit: cover;">
              <div class="card-body p-3">
                <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($cat['name']) ?></h6>
                <small class="text-muted">Explore Dishes <i class="fa-solid fa-arrow-right text-warning ms-1"></i></small>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 4. Featured Food Items -->
<section class="py-5 bg-white">
  <div class="container py-3">
    <div class="section-header">
      <span class="section-subtitle">Chef's Recommendations</span>
      <h2 class="section-title">Featured Delicacies</h2>
      <p class="section-desc">Hand-picked showstoppers loved by our loyal food connoisseurs.</p>
    </div>

    <div class="row g-4">
      <?php foreach ($featuredFoods as $food): ?>
        <?php
          $singleWaMsg = formatWhatsAppFoodItemMessage($food['name'], $food['price']);
          $singleWaUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $singleWaMsg);
        ?>
        <div class="col-md-6 col-lg-4">
          <div class="food-card">
            <div class="food-card-img-wrapper">
              <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($food['image']) ?>" alt="<?= htmlspecialchars($food['name']) ?>" class="food-card-img">
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
                <a href="<?= $singleWaUrl ?>" target="_blank" rel="noopener noreferrer" class="btn-card-wa" title="Order on WhatsApp">
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

    <div class="text-center mt-5">
      <a href="<?= BASE_URL ?>menu.php" class="btn btn-outline-dark rounded-pill px-5 py-3 fw-semibold">
        View Entire Menu <i class="fa-solid fa-arrow-right ms-2"></i>
      </a>
    </div>
  </div>
</section>

<!-- 5. Popular Dishes & Special Offers -->
<section class="py-5 bg-light">
  <div class="container py-3">
    <div class="row g-5">
      <!-- Popular Dishes -->
      <div class="col-lg-6">
        <div class="mb-4">
          <span class="section-subtitle">Top Sellers</span>
          <h3 class="section-title text-start mb-2">Most Popular Dishes</h3>
          <p class="text-muted">Frequently reordered by our valued patrons.</p>
        </div>

        <div class="d-flex flex-column gap-3">
          <?php foreach ($popularDishes as $pFood): ?>
            <?php
              $pWaMsg = formatWhatsAppFoodItemMessage($pFood['name'], $pFood['price']);
              $pWaUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $pWaMsg);
            ?>
            <div class="p-3 bg-white rounded-3 shadow-sm d-flex align-items-center gap-3 border">
              <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($pFood['image']) ?>" alt="<?= htmlspecialchars($pFood['name']) ?>" class="rounded-3" style="width: 85px; height: 85px; object-fit: cover;">
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                  <h6 class="mb-1 fw-bold"><a href="<?= BASE_URL ?>food-details.php?id=<?= $pFood['id'] ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($pFood['name']) ?></a></h6>
                  <span class="text-warning fw-bold small"><i class="fa-solid fa-star"></i> <?= number_format($pFood['rating'], 1) ?></span>
                </div>
                <p class="text-muted small mb-2 text-truncate" style="max-width: 250px;"><?= htmlspecialchars($pFood['description']) ?></p>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fw-bold text-primary"><?= formatPrice($pFood['price']) ?></span>
                  <div class="d-flex gap-2">
                    <a href="<?= BASE_URL ?>cart.php?action=add&id=<?= $pFood['id'] ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 fw-semibold">
                      <i class="fa-solid fa-plus me-1"></i> Add
                    </a>
                    <a href="<?= $pWaUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1" title="WhatsApp Order">
                      <i class="fa-brands fa-whatsapp"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Special Offers & Combos -->
      <div class="col-lg-6">
        <div class="mb-4">
          <span class="section-subtitle">Limited Time</span>
          <h3 class="section-title text-start mb-2">Chef Special Offers</h3>
          <p class="text-muted">Exclusive culinary creations featuring premium selections.</p>
        </div>

        <div class="d-flex flex-column gap-3">
          <?php foreach ($specialOffers as $sFood): ?>
            <div class="card border-0 rounded-4 overflow-hidden text-white shadow-sm position-relative" style="background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.85)), url('<?= BASE_URL ?>assets/images/<?= htmlspecialchars($sFood['image']) ?>') center/cover;">
              <div class="card-body p-4">
                <span class="badge bg-danger rounded-pill px-3 py-2 mb-2 text-uppercase letter-spacing-1">Chef's Special</span>
                <h4 class="fw-bold font-serif mb-2 text-white"><?= htmlspecialchars($sFood['name']) ?></h4>
                <p class="small text-white-50 mb-3" style="max-width: 380px;"><?= htmlspecialchars($sFood['description']) ?></p>
                <div class="d-flex align-items-center justify-content-between">
                  <span class="fs-4 fw-bold text-warning"><?= formatPrice($sFood['price']) ?></span>
                  <div class="d-flex gap-2">
                    <a href="<?= BASE_URL ?>cart.php?action=add&id=<?= $sFood['id'] ?>" class="btn btn-warning rounded-pill px-3 py-2 fw-semibold">
                      <i class="fa-solid fa-cart-shopping me-1"></i> Order Now
                    </a>
                    <?php
                      $sWaMsg = formatWhatsAppFoodItemMessage($sFood['name'], $sFood['price']);
                      $sWaUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $sWaMsg);
                    ?>
                    <a href="<?= $sWaUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success rounded-pill px-3 py-2">
                      <i class="fa-brands fa-whatsapp"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 6. Why Choose Us -->
<section class="py-5 bg-white">
  <div class="container py-3">
    <div class="section-header">
      <span class="section-subtitle">Our Commitments</span>
      <h2 class="section-title">Why Customers Choose Us</h2>
      <p class="section-desc">We guarantee culinary craftsmanship, pristine hygiene, and memorable dining moments every single day.</p>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="feature-box">
          <div class="feature-icon"><i class="fa-solid fa-fire-burner"></i></div>
          <h5 class="fw-bold mb-2 font-serif">Original Recipes</h5>
          <p class="text-muted small">Secret spice combinations and slow-cooking dum techniques preserved across generations.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="feature-box">
          <div class="feature-icon"><i class="fa-solid fa-hand-holding-heart"></i></div>
          <h5 class="fw-bold mb-2 font-serif">Fresh Ingredients</h5>
          <p class="text-muted small">Finest organic produce, prime cuts of meat, and farm fresh dairy inspected daily.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="feature-box">
          <div class="feature-icon"><i class="fa-solid fa-truck-fast"></i></div>
          <h5 class="fw-bold mb-2 font-serif">Lightning Delivery</h5>
          <p class="text-muted small">Insulated thermal food packaging ensuring piping-hot meals right at your doorstep.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="feature-box">
          <div class="feature-icon"><i class="fa-solid fa-comment-dots"></i></div>
          <h5 class="fw-bold mb-2 font-serif">WhatsApp Ordering</h5>
          <p class="text-muted small">Instant direct one-tap ordering on WhatsApp with live customer care coordination.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 7. Customer Reviews -->
<section class="py-5 bg-light">
  <div class="container py-3">
    <div class="section-header">
      <span class="section-subtitle">Testimonials</span>
      <h2 class="section-title">Words From Our Diners</h2>
      <p class="section-desc">Real verified reviews from food lovers who dined with us or ordered at home.</p>
    </div>

    <div class="row g-4">
      <?php if (!empty($customerReviews)): ?>
        <?php foreach ($customerReviews as $rev): ?>
          <div class="col-md-4">
            <div class="review-card">
              <div class="review-stars">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                  <i class="fa-<?= ($s <= $rev['rating']) ? 'solid' : 'regular' ?> fa-star"></i>
                <?php endfor; ?>
              </div>
              <p class="review-text">"<?= htmlspecialchars($rev['review']) ?>"</p>
              <div class="reviewer-info">
                <div class="reviewer-avatar">
                  <?= strtoupper(substr($rev['user_name'], 0, 1)) ?>
                </div>
                <div>
                  <h6 class="mb-0 fw-bold"><?= htmlspecialchars($rev['user_name']) ?></h6>
                  <small class="text-muted"><i class="fa-solid fa-circle-check text-success me-1"></i>Verified Patron</small>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center text-muted">No reviews yet. Be the first to leave a review!</p>
      <?php endif; ?>
    </div>

    <div class="text-center mt-4">
      <a href="<?= BASE_URL ?>reviews.php" class="btn btn-outline-warning rounded-pill px-4 py-2 fw-semibold">
        View All Reviews or Share Feedback
      </a>
    </div>
  </div>
</section>

<!-- 8. Table Reservation & WhatsApp CTA Banners -->
<section class="py-5" style="background: linear-gradient(135deg, #1b1c20 0%, #2b2c34 100%);">
  <div class="container py-4">
    <div class="row align-items-center g-4">
      <div class="col-lg-7 text-white">
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">Dine-in Reservations</span>
        <h2 class="font-serif display-6 fw-bold mb-3">Planning a Special Celebration or Family Dinner?</h2>
        <p class="text-white-50 lead mb-4">
          Reserve your favorite table in advance. We offer candlelit intimate booths, private family halls, and outdoor starlit rooftop seating.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <a href="<?= BASE_URL ?>reservation.php" class="btn btn-warning rounded-pill px-4 py-3 fw-bold">
            <i class="fa-solid fa-calendar-check me-2"></i>Book Table Online
          </a>
          <?php
            $reserveWaMsg = "Hello! I would like to reserve a table at Saffron & Spice. Please advise available timings.";
            $reserveWaUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $reserveWaMsg);
          ?>
          <a href="<?= $reserveWaUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success rounded-pill px-4 py-3 fw-bold">
            <i class="fa-brands fa-whatsapp me-2"></i>Book via WhatsApp
          </a>
        </div>
      </div>
      <div class="col-lg-5 text-center text-lg-end">
        <img src="<?= BASE_URL ?>assets/images/about-chef.jpg" alt="Chef preparing meals" class="img-fluid rounded-4 shadow-lg border border-secondary" style="max-height: 320px; object-fit: cover;">
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
