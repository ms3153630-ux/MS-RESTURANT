<?php
/**
 * Admin Restaurant Settings Management
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$errors = [];

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $restaurantName = sanitize($_POST['restaurant_name'] ?? '');
    $phone          = sanitize($_POST['phone'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $whatsapp       = sanitize($_POST['whatsapp'] ?? '');
    $address        = sanitize($_POST['address'] ?? '');
    $openingHours   = sanitize($_POST['opening_hours'] ?? '');
    $deliveryFee    = (float)($_POST['delivery_fee'] ?? 150.00);
    $facebook       = sanitize($_POST['facebook'] ?? '');
    $instagram      = sanitize($_POST['instagram'] ?? '');
    $website        = sanitize($_POST['website'] ?? '');
    $currency       = sanitize($_POST['currency_symbol'] ?? 'Rs. ');
    $logoName       = $_POST['current_logo'] ?? 'logo.png';

    if (empty($restaurantName)) $errors[] = "Restaurant name is required.";
    if (empty($whatsapp)) $errors[] = "WhatsApp contact number is required for instant ordering.";

    // Handle Logo Upload if present
    if (!empty($_FILES['logo']['name'])) {
        $file = $_FILES['logo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];

        if (!in_array($ext, $allowed)) {
            $errors[] = "Allowed logo formats: JPG, PNG, SVG, WEBP.";
        } else {
            $newLogo = 'logo_' . time() . '.' . $ext;
            $targetPath1 = __DIR__ . '/../assets/images/' . $newLogo;
            $targetPath2 = __DIR__ . '/../assets/uploads/' . $newLogo;
            if (move_uploaded_file($file['tmp_name'], $targetPath1)) {
                @copy($targetPath1, $targetPath2);
                $logoName = $newLogo;
            }
        }
    }

    if (empty($errors)) {
        // Update settings in database
        $updateSql = "UPDATE settings SET 
                        restaurant_name = ?, 
                        logo = ?, 
                        phone = ?, 
                        email = ?, 
                        whatsapp = ?, 
                        address = ?, 
                        opening_hours = ?, 
                        delivery_fee = ?, 
                        facebook = ?, 
                        instagram = ?, 
                        website = ?, 
                        currency_symbol = ? 
                      WHERE id = 1";
        $stmt = $pdo->prepare($updateSql);
        $stmt->execute([
            $restaurantName,
            $logoName,
            $phone,
            $email,
            $whatsapp,
            $address,
            $openingHours,
            $deliveryFee,
            $facebook,
            $instagram,
            $website,
            $currency
        ]);

        setFlash('success', 'Restaurant settings saved successfully.');
        header("Location: " . BASE_URL . "admin/settings.php");
        exit;
    }
}

// Fetch Current Settings
$currentSettings = getSettings($pdo);

$pageTitle = "Restaurant Profile & System Settings";
include __DIR__ . '/header.php';
?>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger shadow-sm mb-4">
    <ul class="mb-0 ps-3">
      <?php foreach ($errors as $err): ?>
        <li><?= htmlspecialchars($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-lg-9">
    <div class="admin-card">
      <div class="admin-card-header">
        <h6 class="admin-card-title"><i class="fa-solid fa-sliders text-warning me-2"></i>Global Restaurant Parameters</h6>
      </div>
      <div class="admin-card-body">
        <form method="POST" action="<?= BASE_URL ?>admin/settings.php" enctype="multipart/form-data">
          <input type="hidden" name="current_logo" value="<?= htmlspecialchars($currentSettings['logo'] ?? 'logo.png') ?>">

          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold small">Restaurant Legal / Brand Name <span class="text-danger">*</span></label>
              <input type="text" name="restaurant_name" class="form-control rounded-3" value="<?= htmlspecialchars($currentSettings['restaurant_name'] ?? '') ?>" required>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Delivery Fee (PKR)</label>
              <div class="input-group">
                <span class="input-group-text bg-light fw-bold">Rs.</span>
                <input type="number" step="0.01" name="delivery_fee" class="form-control" value="<?= htmlspecialchars($currentSettings['delivery_fee'] ?? '150.00') ?>" required>
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Order WhatsApp Hotline <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-success text-white"><i class="fa-brands fa-whatsapp"></i></span>
                <input type="text" name="whatsapp" class="form-control" placeholder="923001234567" value="<?= htmlspecialchars($currentSettings['whatsapp'] ?? '') ?>" required>
              </div>
              <small class="text-muted">International format (e.g. 923001234567)</small>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Public Phone / Landline</label>
              <input type="text" name="phone" class="form-control rounded-3" value="<?= htmlspecialchars($currentSettings['phone'] ?? '') ?>">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Official Email</label>
              <input type="email" name="email" class="form-control rounded-3" value="<?= htmlspecialchars($currentSettings['email'] ?? '') ?>">
            </div>

            <div class="col-md-8">
              <label class="form-label fw-semibold small">Physical Address</label>
              <input type="text" name="address" class="form-control rounded-3" value="<?= htmlspecialchars($currentSettings['address'] ?? '') ?>">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Service Hours</label>
              <input type="text" name="opening_hours" class="form-control rounded-3" value="<?= htmlspecialchars($currentSettings['opening_hours'] ?? '') ?>">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Facebook Page URL</label>
              <input type="url" name="facebook" class="form-control rounded-3" value="<?= htmlspecialchars($currentSettings['facebook'] ?? '') ?>">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Instagram URL</label>
              <input type="url" name="instagram" class="form-control rounded-3" value="<?= htmlspecialchars($currentSettings['instagram'] ?? '') ?>">
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold small">Currency Symbol</label>
              <input type="text" name="currency_symbol" class="form-control rounded-3" value="<?= htmlspecialchars($currentSettings['currency_symbol'] ?? 'Rs. ') ?>">
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold small">Restaurant Logo</label>
              <input type="file" name="logo" class="form-control rounded-3" accept="image/*">
            </div>
          </div>

          <button type="submit" class="btn btn-warning px-5 py-3 fw-bold rounded-pill shadow mt-4">
            <i class="fa-solid fa-floppy-disk me-2"></i> Save Configuration
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
