<?php
/**
 * Customer Registration Page
 */
require_once __DIR__ . '/../config/config.php';

if (isLoggedIn()) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $address  = sanitize($_POST['address'] ?? '');

    // Validation
    if (empty($name)) $errors[] = "Please provide your full name.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please provide a valid email address.";
    if (empty($phone)) $errors[] = "Please enter your phone number.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters long.";
    if ($password !== $confirm) $errors[] = "Password and confirmation password do not match.";

    if (empty($errors)) {
        // Check if email already exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetch()) {
            $errors[] = "An account with this email address already exists. Please login instead.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $insertSql = "INSERT INTO users (name, email, phone, password, address, role, status, created_at)
                          VALUES (?, ?, ?, ?, ?, 'customer', 'active', NOW())";
            $stmt = $pdo->prepare($insertSql);
            $stmt->execute([$name, $email, $phone, $hashedPassword, $address]);

            $newUserId = $pdo->lastInsertId();

            // Auto-login newly registered customer
            $_SESSION['user'] = [
                'id'      => $newUserId,
                'name'    => $name,
                'email'   => $email,
                'phone'   => $phone,
                'address' => $address,
                'role'    => 'customer'
            ];

            setFlash('success', 'Your account has been created successfully! Welcome to Saffron & Spice.');
            header("Location: " . BASE_URL . "index.php");
            exit;
        }
    }
}

$pageTitle = "Create Customer Account";
include __DIR__ . '/../includes/header.php';
?>

<section class="py-5 bg-light">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
          <div class="text-center mb-4">
            <div class="feature-icon mx-auto mb-3" style="width: 60px; height: 60px; line-height: 60px; font-size: 24px;">
              <i class="fa-solid fa-user-plus"></i>
            </div>
            <h3 class="fw-bold font-serif mb-1">Create an Account</h3>
            <p class="text-muted small">Join our gourmet family for faster checkout, order tracking, and reservations.</p>
          </div>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger shadow-sm mb-4">
              <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                  <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="POST" action="<?= BASE_URL ?>user/register.php">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Ahmed Khan" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control rounded-3" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Phone / Mobile <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control rounded-3" placeholder="03001234567" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control rounded-3" placeholder="Min. 6 characters" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold small">Confirm Password <span class="text-danger">*</span></label>
                <input type="password" name="confirm_password" class="form-control rounded-3" placeholder="Repeat password" required>
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold small">Default Delivery Address</label>
                <textarea name="address" rows="2" class="form-control rounded-3" placeholder="House/Flat number, Street, Area..."><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
              </div>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mt-4 mb-3 shadow">
              Register Account
            </button>

            <div class="text-center text-muted small">
              Already have an account? 
              <a href="<?= BASE_URL ?>user/login.php" class="text-warning fw-bold text-decoration-none ms-1">Sign In here</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
