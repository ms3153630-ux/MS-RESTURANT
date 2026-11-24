<?php
/**
 * Table Reservation Page
 */
require_once __DIR__ . '/config/config.php';

$user = currentUser();
$settings = getSettings($pdo);
$errors = [];
$success = false;
$reservationData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName   = sanitize($_POST['customer_name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $phone          = sanitize($_POST['phone'] ?? '');
    $resDate        = sanitize($_POST['reservation_date'] ?? '');
    $resTime        = sanitize($_POST['reservation_time'] ?? '');
    $guests         = (int)($_POST['guests'] ?? 2);
    $specialRequest = sanitize($_POST['special_request'] ?? '');

    if (empty($customerName)) $errors[] = "Please provide your full name.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please provide a valid email address.";
    if (empty($phone)) $errors[] = "Please provide a reachable phone number.";
    if (empty($resDate) || strtotime($resDate) < strtotime(date('Y-m-d'))) $errors[] = "Please select a valid upcoming reservation date.";
    if (empty($resTime)) $errors[] = "Please select your preferred dining time.";
    if ($guests < 1 || $guests > 50) $errors[] = "Number of guests must be between 1 and 50.";

    if (empty($errors)) {
        $userId = $user ? $user['id'] : null;
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, customer_name, email, phone, reservation_date, reservation_time, guests, special_request, status, created_at)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending', NOW())");
        $stmt->execute([
            $userId,
            $customerName,
            $email,
            $phone,
            $resDate,
            $resTime,
            $guests,
            $specialRequest
        ]);

        $resId = $pdo->lastInsertId();
        $success = true;
        $reservationData = [
            'id'    => $resId,
            'name'  => $customerName,
            'phone' => $phone,
            'date'  => $resDate,
            'time'  => $resTime,
            'guests'=> $guests,
            'notes' => $specialRequest
        ];
        setFlash('success', 'Your table reservation request has been received! Our host will confirm promptly.');
    }
}

// Fetch user reservations if logged in
$myReservations = [];
if ($user) {
    $myStmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ? ORDER BY id DESC LIMIT 5");
    $myStmt->execute([$user['id']]);
    $myReservations = $myStmt->fetchAll();
}

$pageTitle = "Book a Table Reservation";
$pageDesc = "Reserve an intimate table or private family banquet at Saffron & Spice Gourmet Restaurant.";
include __DIR__ . '/includes/header.php';
?>

<!-- Header Banner -->
<div class="py-5 bg-dark text-white text-center position-relative" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?= BASE_URL ?>assets/images/about-img.jpg') center/cover;">
  <div class="container py-4">
    <h1 class="display-4 fw-bold font-serif mb-2">Reserve Your Table</h1>
    <p class="text-white-50 lead mb-0">Experience fine dining, impeccable hospitality, and royal ambiance.</p>
  </div>
</div>

<section class="py-5 bg-light">
  <div class="container py-3">
    <div class="row g-5">
      <!-- Reservation Form Column -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
          <h3 class="fw-bold font-serif mb-2">Table Booking Form</h3>
          <p class="text-muted small mb-4">Please submit your dining details. For gatherings of more than 15 guests, you can also ping us on WhatsApp directly.</p>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger shadow-sm mb-4">
              <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                  <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if ($success && $reservationData): ?>
            <div class="alert alert-success p-4 rounded-3 mb-4 text-center">
              <h4 class="fw-bold text-success mb-2"><i class="fa-solid fa-circle-check me-2"></i>Reservation Requested!</h4>
              <p class="mb-3">Table for <strong><?= $reservationData['guests'] ?> Guests</strong> on <strong><?= date('M d, Y', strtotime($reservationData['date'])) ?></strong> at <strong><?= date('h:i A', strtotime($reservationData['time'])) ?></strong>.</p>
              <?php
                $waMsg = "Hello! I placed a table reservation request at Saffron & Spice.\n\nName: {$reservationData['name']}\nPhone: {$reservationData['phone']}\nDate: {$reservationData['date']}\nTime: {$reservationData['time']}\nGuests: {$reservationData['guests']}\nSpecial Request: {$reservationData['notes']}\n\nPlease confirm availability.";
                $waUrl = buildWhatsAppUrl($settings['whatsapp'] ?? '923001234567', $waMsg);
              ?>
              <a href="<?= $waUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp-custom fw-bold px-4 py-2">
                <i class="fa-brands fa-whatsapp fs-5"></i> Confirm via WhatsApp
              </a>
            </div>
          <?php endif; ?>

          <form method="POST" action="<?= BASE_URL ?>reservation.php">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="customer_name" class="form-control rounded-3" value="<?= htmlspecialchars($_POST['customer_name'] ?? ($user['name'] ?? '')) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Phone / Mobile <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control rounded-3" placeholder="03001234567" value="<?= htmlspecialchars($_POST['phone'] ?? ($user['phone'] ?? '')) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control rounded-3" placeholder="ahmed@example.com" value="<?= htmlspecialchars($_POST['email'] ?? ($user['email'] ?? '')) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Number of Guests <span class="text-danger">*</span></label>
                <select name="guests" class="form-select rounded-3" required>
                  <?php for ($g = 1; $g <= 20; $g++): ?>
                    <option value="<?= $g ?>" <?= ($g == 2) ? 'selected' : '' ?>><?= $g ?> <?= ($g == 1) ? 'Guest (Solo)' : 'Guests' ?></option>
                  <?php endfor; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Reservation Date <span class="text-danger">*</span></label>
                <input type="date" name="reservation_date" class="form-control rounded-3" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_POST['reservation_date'] ?? date('Y-m-d')) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Reservation Time <span class="text-danger">*</span></label>
                <input type="time" name="reservation_time" class="form-control rounded-3" value="<?= htmlspecialchars($_POST['reservation_time'] ?? '20:00') ?>" required>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold small">Special Requests / Seating Preference</label>
                <textarea name="special_request" rows="3" class="form-control rounded-3" placeholder="e.g. Birthday anniversary, quiet corner table, high chair for infant..."><?= htmlspecialchars($_POST['special_request'] ?? '') ?></textarea>
              </div>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mt-4 shadow">
              <i class="fa-solid fa-calendar-check me-2"></i> Submit Table Reservation
            </button>
          </form>
        </div>
      </div>

      <!-- Info & Policies Column -->
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
          <h4 class="fw-bold font-serif mb-3 border-bottom pb-3"><i class="fa-solid fa-clock text-warning me-2"></i>Operating Hours</h4>
          <p class="text-muted small mb-2"><i class="fa-solid fa-circle-check text-success me-2"></i><strong>Lunch &amp; Dinner:</strong> 12:00 PM - 01:00 AM Daily</p>
          <p class="text-muted small mb-3"><i class="fa-solid fa-circle-check text-success me-2"></i><strong>Peak Hours:</strong> 08:00 PM - 11:00 PM</p>
          <div class="alert alert-warning border-0 small mb-0">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> Tables are held for up to 15 minutes past your booked time before being released to walk-ins.
          </div>
        </div>

        <!-- My Recent Reservations if logged in -->
        <?php if (!empty($myReservations)): ?>
          <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h5 class="fw-bold font-serif mb-3 border-bottom pb-2">Your Recent Bookings</h5>
            <div class="list-group list-group-flush">
              <?php foreach ($myReservations as $r): ?>
                <div class="list-group-item px-0 py-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <strong><?= date('M d, Y', strtotime($r['reservation_date'])) ?></strong> at <?= date('h:i A', strtotime($r['reservation_time'])) ?>
                      <div class="small text-muted"><?= $r['guests'] ?> Guests</div>
                    </div>
                    <span class="badge bg-<?= ($r['status'] == 'Confirmed') ? 'success' : (($r['status'] == 'Cancelled') ? 'danger' : 'warning text-dark') ?> rounded-pill">
                      <?= $r['status'] ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
