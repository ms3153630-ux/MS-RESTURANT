<?php
/**
 * Global Footer Component & Floating WhatsApp Button
 */
$settings = getSettings($pdo);
$floatingWhatsAppMsg = "Hello! I would like to inquire about the menu and place an order.";
$floatingWhatsAppUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $floatingWhatsAppMsg);
?>
</main>

<!-- Floating WhatsApp Button -->
<a href="<?= $floatingWhatsAppUrl ?>" target="_blank" rel="noopener noreferrer" class="floating-whatsapp" title="Chat with us on WhatsApp" aria-label="WhatsApp Contact">
  <i class="fa-brands fa-whatsapp"></i>
</a>

<!-- Main Footer -->
<footer class="footer-custom">
  <div class="container">
    <div class="row g-4">
      <!-- Col 1: About & Info -->
      <div class="col-lg-4 col-md-6">
        <h5 class="text-white"><i class="fa-solid fa-utensils text-warning me-2"></i><?= htmlspecialchars($settings['restaurant_name']) ?></h5>
        <p class="text-secondary small pe-lg-3">
          Experience gourmet dining crafted with passion, authentic spices, and royal culinary tradition. Order online for swift doorstep delivery or reserve your table today.
        </p>
        <div class="footer-social mt-3">
          <?php if (!empty($settings['facebook'])): ?>
            <a href="<?= htmlspecialchars($settings['facebook']) ?>" target="_blank" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <?php endif; ?>
          <?php if (!empty($settings['instagram'])): ?>
            <a href="<?= htmlspecialchars($settings['instagram']) ?>" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <?php endif; ?>
          <a href="<?= $floatingWhatsAppUrl ?>" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>

      <!-- Col 2: Quick Links -->
      <div class="col-lg-2 col-md-6">
        <h5>Quick Links</h5>
        <ul class="list-unstyled small">
          <li class="mb-2"><a href="<?= BASE_URL ?>index.php"><i class="fa-solid fa-angle-right me-2 text-warning"></i>Home</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>about.php"><i class="fa-solid fa-angle-right me-2 text-warning"></i>About Us</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>menu.php"><i class="fa-solid fa-angle-right me-2 text-warning"></i>Food Menu</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>reservation.php"><i class="fa-solid fa-angle-right me-2 text-warning"></i>Table Reservation</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>reviews.php"><i class="fa-solid fa-angle-right me-2 text-warning"></i>Customer Reviews</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>contact.php"><i class="fa-solid fa-angle-right me-2 text-warning"></i>Contact Us</a></li>
        </ul>
      </div>

      <!-- Col 3: Popular Categories -->
      <div class="col-lg-3 col-md-6">
        <h5>Top Categories</h5>
        <ul class="list-unstyled small">
          <li class="mb-2"><a href="<?= BASE_URL ?>menu.php?cat=2"><i class="fa-solid fa-fire-flame-curved me-2 text-warning"></i>Smoky BBQ Specials</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>menu.php?cat=3"><i class="fa-solid fa-burger me-2 text-warning"></i>Gourmet Burgers</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>menu.php?cat=4"><i class="fa-solid fa-pizza-slice me-2 text-warning"></i>Wood-Fired Pizza</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>menu.php?cat=6"><i class="fa-solid fa-bowl-rice me-2 text-warning"></i>Royal Biryani &amp; Karahi</a></li>
          <li class="mb-2"><a href="<?= BASE_URL ?>menu.php?cat=9"><i class="fa-solid fa-cake-candles me-2 text-warning"></i>Decadent Desserts</a></li>
        </ul>
      </div>

      <!-- Col 4: Contact & Hours -->
      <div class="col-lg-3 col-md-6">
        <h5>Reach Us</h5>
        <ul class="list-unstyled small text-secondary">
          <li class="mb-2 d-flex align-items-start gap-2">
            <i class="fa-solid fa-location-dot text-warning mt-1"></i>
            <span><?= htmlspecialchars($settings['address'] ?? 'Karachi, Pakistan') ?></span>
          </li>
          <li class="mb-2 d-flex align-items-center gap-2">
            <i class="fa-solid fa-phone text-warning"></i>
            <span><?= htmlspecialchars($settings['phone'] ?? '+92 300 1234567') ?></span>
          </li>
          <li class="mb-2 d-flex align-items-center gap-2">
            <i class="fa-solid fa-envelope text-warning"></i>
            <span><?= htmlspecialchars($settings['email'] ?? 'info@saffronspice.com') ?></span>
          </li>
          <li class="mb-2 d-flex align-items-start gap-2">
            <i class="fa-solid fa-clock text-warning mt-1"></i>
            <span><?= htmlspecialchars($settings['opening_hours'] ?? '12:00 PM - 01:00 AM') ?></span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Copyright -->
    <div class="footer-bottom text-center">
      <div class="row align-items-center">
        <div class="col-md-6 text-md-start mb-2 mb-md-0">
          <p class="mb-0 text-secondary">&copy; <?= date('Y') ?> <strong><?= htmlspecialchars($settings['restaurant_name']) ?></strong>. All rights reserved.</p>
        </div>
        <div class="col-md-6 text-md-end">
          <span class="text-secondary small">Built with Core PHP 8, MySQL &amp; Bootstrap 5</span>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Main Custom JS -->
<script src="<?= BASE_URL ?>assets/js/script.js"></script>
</body>
</html>
