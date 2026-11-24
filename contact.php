<?php
/**
 * Contact Us Page
 */
require_once __DIR__ . '/config/config.php';

$user = currentUser();
$settings = getSettings($pdo);
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitize($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name)) $errors[] = "Please provide your name.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please provide a valid email address.";
    if (empty($subject)) $errors[] = "Please provide a subject for your inquiry.";
    if (empty($message)) $errors[] = "Please write your message.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, 'unread', NOW())");
        $stmt->execute([$name, $email, $phone, $subject, $message]);

        $success = true;
        setFlash('success', 'Thank you! Your message has been sent to our restaurant management. We will respond shortly.');
    }
}

$contactWaMsg = "Hello! I am reaching out through your restaurant contact page with an inquiry.";
$contactWaUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $contactWaMsg);

$pageTitle = "Contact Saffron & Spice Gourmet Restaurant";
$pageDesc = "Get in touch with us for inquiries, private event catering, table bookings or feedback.";
include __DIR__ . '/includes/header.php';
?>

<!-- Header Banner -->
<div class="py-5 bg-dark text-white text-center position-relative" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?= BASE_URL ?>assets/images/hero-bg.jpg') center/cover;">
  <div class="container py-4">
    <h1 class="display-4 fw-bold font-serif mb-2">Get In Touch</h1>
    <p class="text-white-50 lead mb-0">We are always delighted to hear from our patrons and event planners.</p>
  </div>
</div>

<section class="py-5 bg-light">
  <div class="container py-3">
    <div class="row g-5">
      <!-- Contact Info & Map Column -->
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
          <h4 class="fw-bold font-serif mb-4 border-bottom pb-3">Restaurant Information</h4>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="feature-icon m-0" style="width: 50px; height: 50px; line-height: 50px; font-size: 20px;">
              <i class="fa-solid fa-location-dot"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-1">Our Location</h6>
              <p class="text-muted small mb-0"><?= htmlspecialchars($settings['address'] ?? 'Plot 42, Food Street, Clifton, Karachi') ?></p>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="feature-icon m-0 bg-success-subtle text-success" style="width: 50px; height: 50px; line-height: 50px; font-size: 20px;">
              <i class="fa-solid fa-phone"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-1">Telephone &amp; Mobile</h6>
              <p class="text-muted small mb-0"><?= htmlspecialchars($settings['phone'] ?? '+92 300 1234567') ?></p>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="feature-icon m-0 bg-primary-subtle text-primary" style="width: 50px; height: 50px; line-height: 50px; font-size: 20px;">
              <i class="fa-solid fa-envelope"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-1">Email Dispatch</h6>
              <p class="text-muted small mb-0"><?= htmlspecialchars($settings['email'] ?? 'info@saffronspice.com') ?></p>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="feature-icon m-0 bg-warning-subtle text-warning" style="width: 50px; height: 50px; line-height: 50px; font-size: 20px;">
              <i class="fa-solid fa-clock"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-1">Service Timings</h6>
              <p class="text-muted small mb-0"><?= htmlspecialchars($settings['opening_hours'] ?? '12:00 PM - 01:00 AM Daily') ?></p>
            </div>
          </div>

          <a href="<?= $contactWaUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp-custom w-100 justify-content-center py-3 fw-bold">
            <i class="fa-brands fa-whatsapp fs-5"></i> Instant Chat on WhatsApp
          </a>
        </div>

        <!-- Google Maps Placeholder / Embed -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
          <div class="ratio ratio-16x9">
            <iframe src="https://maps.google.com/maps?q=Clifton%20Karachi&t=&z=13&ie=UTF8&iwloc=&output=embed" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
          </div>
        </div>
      </div>

      <!-- Contact Message Form Column -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
          <h3 class="fw-bold font-serif mb-2">Send Us a Direct Message</h3>
          <p class="text-muted small mb-4">Got questions about private catering, allergens, or business inquiries? Write to us.</p>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger shadow-sm mb-4">
              <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                  <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if ($success): ?>
            <div class="alert alert-success p-4 rounded-3 mb-4 text-center">
              <i class="fa-solid fa-circle-check fs-2 text-success mb-2 d-block"></i>
              <h5 class="fw-bold">Your message was received!</h5>
              <p class="mb-0">A representative from our hospitality team will reach out via email or phone within 24 hours.</p>
            </div>
          <?php endif; ?>

          <form method="POST" action="<?= BASE_URL ?>contact.php">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold small">Your Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control rounded-3" value="<?= htmlspecialchars($_POST['name'] ?? ($user['name'] ?? '')) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control rounded-3" value="<?= htmlspecialchars($_POST['email'] ?? ($user['email'] ?? '')) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Phone Number (Optional)</label>
                <input type="text" name="phone" class="form-control rounded-3" placeholder="03001234567" value="<?= htmlspecialchars($_POST['phone'] ?? ($user['phone'] ?? '')) ?>">
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Subject <span class="text-danger">*</span></label>
                <input type="text" name="subject" class="form-control rounded-3" placeholder="e.g. Catering Request, Feedback, Reservation query" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold small">Message Content <span class="text-danger">*</span></label>
                <textarea name="message" rows="5" class="form-control rounded-3" placeholder="Type your detailed message here..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
              </div>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mt-4 shadow">
              <i class="fa-solid fa-paper-plane me-2"></i> Send Message Now
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
