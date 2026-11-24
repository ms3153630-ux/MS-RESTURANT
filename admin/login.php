<?php
/**
 * Administrator Secure Login Page
 */
require_once __DIR__ . '/../config/config.php';

if (isAdmin()) {
    header("Location: " . BASE_URL . "admin/dashboard.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid administrator email.";
    }
    if (empty($password)) {
        $errors[] = "Please enter your password.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            if ($admin['status'] !== 'active') {
                $errors[] = "Your admin account has been suspended.";
            } else {
                $_SESSION['user'] = [
                    'id'      => $admin['id'],
                    'name'    => $admin['name'],
                    'email'   => $admin['email'],
                    'phone'   => $admin['phone'],
                    'address' => $admin['address'],
                    'role'    => 'admin'
                ];
                setFlash('success', 'Logged in as Administrator successfully!');
                header("Location: " . BASE_URL . "admin/dashboard.php");
                exit;
            }
        } else {
            $errors[] = "Invalid administrator credentials.";
        }
    }
}

$settings = getSettings($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Portal Login | <?= htmlspecialchars($settings['restaurant_name']) ?></title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="min-height: 100vh; background: radial-gradient(circle at center, #1e222b 0%, #0d0f13 100%);">

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5" style="background: rgba(255, 255, 255, 0.98);">
          <div class="text-center mb-4">
            <span class="d-inline-flex align-items-center justify-content-center bg-danger text-white rounded-circle shadow mb-3" style="width: 60px; height: 60px; font-size: 24px;">
              <i class="fa-solid fa-lock"></i>
            </span>
            <h3 class="fw-bold font-serif mb-1 text-dark">Admin Portal</h3>
            <p class="text-muted small">Sign in to manage food catalog, orders &amp; reservations</p>
          </div>

          <?php displayFlash(); ?>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger shadow-sm mb-4">
              <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                  <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <form method="POST" action="<?= BASE_URL ?>admin/login.php">
            <div class="mb-3">
              <label class="form-label fw-semibold small text-muted">Administrator Email</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-user-shield text-muted"></i></span>
                <input type="email" name="email" class="form-control" placeholder="admin@restaurant.com" value="<?= htmlspecialchars($_POST['email'] ?? 'admin@restaurant.com') ?>" required>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold small text-muted">Password</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-key text-muted"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" value="admin123" required>
              </div>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mb-3 shadow">
              <i class="fa-solid fa-right-to-bracket me-2"></i> Access Dashboard
            </button>

            <div class="text-center">
              <a href="<?= BASE_URL ?>index.php" class="text-decoration-none small text-muted">
                <i class="fa-solid fa-arrow-left me-1"></i> Return to Main Website
              </a>
            </div>

            <div class="border-top mt-4 pt-3 text-center">
              <small class="text-muted d-block">Preloaded Admin Credentials:</small>
              <small class="text-secondary font-monospace">admin@restaurant.com / admin123</small>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
