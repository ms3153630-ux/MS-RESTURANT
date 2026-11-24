<?php
/**
 * Customer Login Page
 */
require_once __DIR__ . '/../config/config.php';

// If already logged in, redirect
if (isLoggedIn()) {
    header("Location: " . BASE_URL . (isAdmin() ? "admin/dashboard.php" : "user/profile.php"));
    exit;
}

$errors = [];
$redirect = sanitize($_GET['redirect'] ?? $_POST['redirect'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please provide a valid registered email address.";
    }
    if (empty($password)) {
        $errors[] = "Please enter your password.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $errors[] = "Your account has been deactivated. Please contact restaurant administration.";
            } else {
                // Successful login
                $_SESSION['user'] = [
                    'id'      => $user['id'],
                    'name'    => $user['name'],
                    'email'   => $user['email'],
                    'phone'   => $user['phone'],
                    'address' => $user['address'],
                    'role'    => $user['role']
                ];

                setFlash('success', "Welcome back, " . htmlspecialchars($user['name']) . "!");

                if ($user['role'] === 'admin') {
                    header("Location: " . BASE_URL . "admin/dashboard.php");
                    exit;
                }

                if (!empty($redirect) && strpos($redirect, BASE_URL) !== false) {
                    header("Location: " . $redirect);
                } else {
                    header("Location: " . BASE_URL . "index.php");
                }
                exit;
            }
        } else {
            $errors[] = "Invalid email or password combination.";
        }
    }
}

$pageTitle = "Sign In to Your Account";
include __DIR__ . '/../includes/header.php';
?>

<section class="py-5 bg-light">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
          <div class="text-center mb-4">
            <div class="feature-icon mx-auto mb-3" style="width: 60px; height: 60px; line-height: 60px; font-size: 24px;">
              <i class="fa-solid fa-right-to-bracket"></i>
            </div>
            <h3 class="fw-bold font-serif mb-1">Customer Sign In</h3>
            <p class="text-muted small">Access your order history and manage your dining profile.</p>
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

          <form method="POST" action="<?= BASE_URL ?>user/login.php">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

            <div class="mb-3">
              <label class="form-label fw-semibold small">Email Address</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control" placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
              </div>
            </div>

            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <label class="form-label fw-semibold small">Password</label>
                <a href="javascript:void(0)" onclick="alert('Demo reset: For customer demo use password: user123. For admin demo use: admin123');" class="small text-muted text-decoration-none">Forgot password?</a>
              </div>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
              </div>
            </div>

            <div class="mb-4 form-check">
              <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
              <label class="form-check-label small text-muted" for="rememberMe">Remember me on this browser</label>
            </div>

            <button type="submit" class="btn btn-primary-custom w-100 py-3 fw-bold mb-3 shadow">
              Sign In
            </button>

            <div class="text-center text-muted small">
              Don't have an account yet? 
              <a href="<?= BASE_URL ?>user/register.php" class="text-warning fw-bold text-decoration-none ms-1">Create Account</a>
            </div>

            <div class="border-top mt-4 pt-3 text-center">
              <small class="text-muted d-block mb-1">Quick Demo Credentials:</small>
              <small class="text-secondary">Customer: <code>ahmed@example.com</code> / <code>user123</code></small>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
