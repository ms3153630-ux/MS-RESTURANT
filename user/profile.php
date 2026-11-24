<?php
/**
 * Customer Profile Management
 */
require_once __DIR__ . '/../includes/auth.php';

$userId = $_SESSION['user']['id'];
$errors = [];
$successMsg = '';

// Handle Profile Details Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'update_profile') {
    $name    = sanitize($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');

    if (empty($name)) $errors[] = "Name cannot be empty.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email address is required.";
    if (empty($phone)) $errors[] = "Phone number is required.";

    if (empty($errors)) {
        // Check if email taken by someone else
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $checkStmt->execute([$email, $userId]);
        if ($checkStmt->fetch()) {
            $errors[] = "This email is already in use by another user.";
        } else {
            $updateSql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?";
            $stmt = $pdo->prepare($updateSql);
            $stmt->execute([$name, $email, $phone, $address, $userId]);

            // Update session data
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['phone'] = $phone;
            $_SESSION['user']['address'] = $address;

            setFlash('success', 'Profile details updated successfully!');
            header("Location: " . BASE_URL . "user/profile.php");
            exit;
        }
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_type']) && $_POST['action_type'] === 'change_password') {
    $currentPass = $_POST['current_password'] ?? '';
    $newPass     = $_POST['new_password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    // Verify current password from database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $dbUser = $stmt->fetch();

    if (!$dbUser || !password_verify($currentPass, $dbUser['password'])) {
        $errors[] = "Your current password is incorrect.";
    } elseif (strlen($newPass) < 6) {
        $errors[] = "New password must be at least 6 characters long.";
    } elseif ($newPass !== $confirmPass) {
        $errors[] = "New password and confirmation do not match.";
    } else {
        $newHash = password_hash($newPass, PASSWORD_BCRYPT);
        $upStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $upStmt->execute([$newHash, $userId]);

        setFlash('success', 'Password successfully changed! Please use your new password next time.');
        header("Location: " . BASE_URL . "user/profile.php");
        exit;
    }
}

// Fetch Fresh User Data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch();

$pageTitle = "My Customer Profile";
include __DIR__ . '/../includes/header.php';
?>

<div class="py-4 bg-light">
  <div class="container">
    <h1 class="display-5 fw-bold font-serif mb-1">Customer Account Dashboard</h1>
    <p class="text-muted mb-0">Manage your dining profile, addresses, and account credentials.</p>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <div class="row g-4">
      <!-- Profile Sidebar -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center mb-4">
          <div class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-circle mx-auto mb-3 fw-bold display-5" style="width: 90px; height: 90px;">
            <?= strtoupper(substr($userData['name'], 0, 1)) ?>
          </div>
          <h4 class="fw-bold font-serif mb-1"><?= htmlspecialchars($userData['name']) ?></h4>
          <p class="text-muted small mb-3"><?= htmlspecialchars($userData['email']) ?></p>
          <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">Active Diner</span>
        </div>

        <div class="list-group border-0 shadow-sm rounded-4 overflow-hidden">
          <a href="<?= BASE_URL ?>user/profile.php" class="list-group-item list-group-item-action active fw-semibold py-3">
            <i class="fa-solid fa-user me-2"></i> Profile &amp; Settings
          </a>
          <a href="<?= BASE_URL ?>user/orders.php" class="list-group-item list-group-item-action fw-semibold py-3">
            <i class="fa-solid fa-bag-shopping me-2 text-warning"></i> My Order History
          </a>
          <a href="<?= BASE_URL ?>reservation.php" class="list-group-item list-group-item-action fw-semibold py-3">
            <i class="fa-solid fa-calendar-check me-2 text-info"></i> Table Reservations
          </a>
          <a href="<?= BASE_URL ?>user/logout.php" class="list-group-item list-group-item-action text-danger fw-semibold py-3">
            <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Log Out
          </a>
        </div>
      </div>

      <!-- Main Forms Column -->
      <div class="col-lg-8">
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger shadow-sm mb-4">
            <ul class="mb-0 ps-3">
              <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Form 1: Profile Information -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
          <h4 class="fw-bold font-serif mb-4 border-bottom pb-3"><i class="fa-solid fa-id-card text-warning me-2"></i>Update Personal Details</h4>

          <form method="POST" action="<?= BASE_URL ?>user/profile.php">
            <input type="hidden" name="action_type" value="update_profile">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold small">Full Name</label>
                <input type="text" name="name" class="form-control rounded-3" value="<?= htmlspecialchars($userData['name']) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Email Address</label>
                <input type="email" name="email" class="form-control rounded-3" value="<?= htmlspecialchars($userData['email']) ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Phone / Mobile</label>
                <input type="text" name="phone" class="form-control rounded-3" value="<?= htmlspecialchars($userData['phone'] ?? '') ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Member Since</label>
                <input type="text" class="form-control rounded-3 bg-light" value="<?= formatDate($userData['created_at']) ?>" readonly>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold small">Default Delivery Address</label>
                <textarea name="address" rows="3" class="form-control rounded-3"><?= htmlspecialchars($userData['address'] ?? '') ?></textarea>
              </div>
            </div>

            <button type="submit" class="btn btn-warning rounded-pill px-4 py-2 fw-semibold mt-4">
              <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
            </button>
          </form>
        </div>

        <!-- Form 2: Password Change -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
          <h4 class="fw-bold font-serif mb-4 border-bottom pb-3"><i class="fa-solid fa-key text-warning me-2"></i>Change Security Password</h4>

          <form method="POST" action="<?= BASE_URL ?>user/profile.php">
            <input type="hidden" name="action_type" value="change_password">

            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold small">Current Password</label>
                <input type="password" name="current_password" class="form-control rounded-3" placeholder="Enter current password" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">New Password (Min 6 chars)</label>
                <input type="password" name="new_password" class="form-control rounded-3" placeholder="••••••••" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control rounded-3" placeholder="••••••••" required>
              </div>
            </div>

            <button type="submit" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-semibold mt-4">
              <i class="fa-solid fa-lock me-1"></i> Update Password
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
