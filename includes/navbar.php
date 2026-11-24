<?php
/**
 * Navigation Bar Component
 */
$cartCount = getCartCount();
$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
  <div class="container">
    <a class="navbar-brand" href="<?= BASE_URL ?>index.php">
      <i class="fa-solid fa-utensils text-warning"></i>
      <span>Saffron</span> &amp; Spice
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#restaurantNavbar" aria-controls="restaurantNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="restaurantNavbar">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'index.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'about.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>about.php">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'menu.php' || $currentPage == 'food-details.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>menu.php">Menu</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'reservation.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>reservation.php">Reservations</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'reviews.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>reviews.php">Reviews</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'contact.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>contact.php">Contact</a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-3">
        <!-- Cart Icon -->
        <a href="<?= BASE_URL ?>cart.php" class="btn btn-outline-light position-relative rounded-pill px-3 py-2 cart-badge-icon" title="View Cart">
          <i class="fa-solid fa-cart-shopping me-1"></i>
          <span class="d-none d-sm-inline">Cart</span>
          <span class="badge" id="cartCountBadge"><?= $cartCount ?></span>
        </a>

        <!-- User Authentication Links -->
        <?php if ($user): ?>
          <div class="dropdown">
            <button class="btn btn-warning dropdown-toggle rounded-pill px-3 py-2 fw-semibold" type="button" id="userMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fa-solid fa-user-circle me-1"></i> <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenuBtn">
              <?php if (isAdmin()): ?>
                <li>
                  <a class="dropdown-item fw-bold text-danger" href="<?= BASE_URL ?>admin/dashboard.php">
                    <i class="fa-solid fa-gauge-high me-2"></i> Admin Panel
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>user/profile.php"><i class="fa-solid fa-id-card me-2 text-muted"></i> My Profile</a></li>
              <li><a class="dropdown-item" href="<?= BASE_URL ?>user/orders.php"><i class="fa-solid fa-bag-shopping me-2 text-muted"></i> My Orders</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>user/logout.php"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>user/login.php" class="btn btn-outline-light rounded-pill px-3 py-2">
              <i class="fa-solid fa-right-to-bracket me-1"></i> Login
            </a>
            <a href="<?= BASE_URL ?>user/register.php" class="btn btn-warning rounded-pill px-3 py-2 fw-semibold d-none d-sm-inline-block">
              Register
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
