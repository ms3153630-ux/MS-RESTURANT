<?php
/**
 * About Us Page
 */
require_once __DIR__ . '/config/config.php';

$pageTitle = "About Our Culinary Heritage";
$pageDesc = "Discover the story, mission, culinary vision, and passion behind Saffron & Spice Gourmet Restaurant.";

$settings = getSettings($pdo);
include __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb Header -->
<div class="py-5 bg-dark text-white text-center position-relative" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?= BASE_URL ?>assets/images/hero-bg.jpg') center/cover;">
  <div class="container py-4">
    <h1 class="display-4 fw-bold font-serif mb-2">Our Culinary Heritage</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-center mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php" class="text-warning text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-white-50" aria-current="page">About Us</li>
      </ol>
    </nav>
  </div>
</div>

<!-- Story Section -->
<section class="py-5 bg-white">
  <div class="container py-4">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="position-relative">
          <img src="<?= BASE_URL ?>assets/images/about-img.jpg" alt="Restaurant Interior" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 480px; object-fit: cover;">
          <div class="position-absolute -bottom-4 -end-4 d-none d-md-block p-4 bg-white rounded-4 shadow border" style="bottom: 20px; right: 20px;">
            <div class="d-flex align-items-center gap-3">
              <i class="fa-solid fa-award text-warning display-5"></i>
              <div>
                <h5 class="fw-bold mb-0">Award Winning Taste</h5>
                <small class="text-muted">Best Culinary Experience 2024</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <span class="section-subtitle">Since 2010</span>
        <h2 class="section-title text-start mb-3">Crafting Extraordinary Dishes With Love &amp; Soul</h2>
        <p class="text-muted lead mb-3">
          Founded on a deep-rooted devotion to royal culinary traditions, <strong><?= htmlspecialchars($settings['restaurant_name']) ?></strong> began as a boutique kitchen with a burning desire: to redefine fine dining with uncompromised authenticity.
        </p>
        <p class="text-muted mb-4">
          Over more than a decade, our master chefs have curated signature recipes spanning the sizzling charcoal skewers of Peshawar, the rich dum biryanis of Hyderabad, artisanal stone-baked pizzas, and contemporary smashed beef burgers. Every single dish that leaves our kitchen carries our commitment to hygiene, pure ghee, and unadulterated spices.
        </p>

        <div class="d-flex gap-3 pt-2">
          <a href="<?= BASE_URL ?>menu.php" class="btn btn-warning rounded-pill px-4 py-3 fw-bold">Explore Our Menu</a>
          <a href="<?= BASE_URL ?>reservation.php" class="btn btn-outline-dark rounded-pill px-4 py-3 fw-bold">Reserve a Table</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Mission & Vision -->
<section class="py-5 bg-light">
  <div class="container py-4">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="p-5 bg-white rounded-4 shadow-sm h-100 border-top border-4 border-warning">
          <div class="d-flex align-items-center gap-3 mb-3">
            <span class="feature-icon m-0" style="width: 50px; height: 50px; line-height: 50px; font-size: 22px;">
              <i class="fa-solid fa-bullseye"></i>
            </span>
            <h3 class="font-serif fw-bold mb-0">Our Mission</h3>
          </div>
          <p class="text-muted mb-0 leading-relaxed">
            To provide every guest with an unforgettable dining experience through extraordinary culinary flavors, impeccable hygiene, warm oriental hospitality, and swift home delivery services that bring celebration right to your dining table.
          </p>
        </div>
      </div>

      <div class="col-md-6">
        <div class="p-5 bg-white rounded-4 shadow-sm h-100 border-top border-4 border-danger">
          <div class="d-flex align-items-center gap-3 mb-3">
            <span class="feature-icon m-0 bg-danger-subtle text-danger" style="width: 50px; height: 50px; line-height: 50px; font-size: 22px;">
              <i class="fa-solid fa-eye"></i>
            </span>
            <h3 class="font-serif fw-bold mb-0">Our Vision</h3>
          </div>
          <p class="text-muted mb-0 leading-relaxed">
            To establish Saffron &amp; Spice as the benchmark for gastronomic excellence, inspiring food lovers through creative dishes that honor centuries-old recipes while embracing modern global culinary techniques.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Master Chefs Section -->
<section class="py-5 bg-white">
  <div class="container py-4">
    <div class="section-header">
      <span class="section-subtitle">Meet The Masters</span>
      <h2 class="section-title">Our Executive Culinary Team</h2>
      <p class="section-desc">Guided by internationally celebrated chefs specializing in authentic barbecue, royal gravies, and pastry craft.</p>
    </div>

    <div class="row g-4 justify-content-center">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-center">
          <img src="<?= BASE_URL ?>assets/images/about-chef.jpg" alt="Chef Tariq" class="card-img-top" style="height: 320px; object-fit: cover;">
          <div class="card-body p-4">
            <h5 class="fw-bold font-serif mb-1">Chef Tariq Al-Hashmi</h5>
            <span class="badge bg-warning text-dark px-3 py-1 rounded-pill mb-3">Head Executive Chef</span>
            <p class="text-muted small">Specialist in charcoal smoke infusions, royal Awadhi gravies, and slow-braised shank karahis.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
