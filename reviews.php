<?php
/**
 * Customer Reviews Page
 */
require_once __DIR__ . '/config/config.php';

$user = currentUser();
$errors = [];
$settings = getSettings($pdo);

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        setFlash('warning', 'Please login to submit a review.');
        header("Location: " . BASE_URL . "user/login.php?redirect=" . urlencode(BASE_URL . 'reviews.php'));
        exit;
    }

    $rating  = (int)($_POST['rating'] ?? 5);
    $review  = sanitize($_POST['review'] ?? '');
    $foodId  = !empty($_POST['food_id']) ? (int)$_POST['food_id'] : null;

    if ($rating < 1 || $rating > 5) $errors[] = "Rating must be between 1 and 5 stars.";
    if (strlen($review) < 10) $errors[] = "Please provide a detailed review with at least 10 characters.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, food_id, rating, review, status, created_at) VALUES (?, ?, ?, ?, 'approved', NOW())");
        $stmt->execute([$user['id'], $foodId, $rating, $review]);

        // If food_id was provided, recalculate that food's average rating
        if ($foodId) {
            $avgStmt = $pdo->prepare("SELECT AVG(rating) AS avg_rate FROM reviews WHERE food_id = ? AND status = 'approved'");
            $avgStmt->execute([$foodId]);
            $newRate = round((float)$avgStmt->fetchColumn(), 1);
            if ($newRate > 0) {
                $upFood = $pdo->prepare("UPDATE foods SET rating = ? WHERE id = ?");
                $upFood->execute([$newRate, $foodId]);
            }
        }

        setFlash('success', 'Thank you! Your dining review has been posted successfully.');
        header("Location: " . BASE_URL . "reviews.php");
        exit;
    }
}

// Fetch all active foods for the dropdown
$foodStmt = $pdo->query("SELECT id, name FROM foods WHERE status = 'active' ORDER BY name ASC");
$allFoods = $foodStmt->fetchAll();

// Fetch approved reviews
$stmt = $pdo->query("SELECT r.*, u.name AS user_name, f.name AS food_name 
                     FROM reviews r 
                     JOIN users u ON r.user_id = u.id 
                     LEFT JOIN foods f ON r.food_id = f.id 
                     WHERE r.status = 'approved' 
                     ORDER BY r.id DESC");
$reviews = $stmt->fetchAll();

$pageTitle = "Customer Dining Reviews & Ratings";
$pageDesc = "Read testimonials and reviews from genuine diners at Saffron & Spice Gourmet Restaurant.";
include __DIR__ . '/includes/header.php';
?>

<!-- Header Banner -->
<div class="py-5 bg-dark text-white text-center position-relative" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?= BASE_URL ?>assets/images/hero-bg.jpg') center/cover;">
  <div class="container py-4">
    <h1 class="display-4 fw-bold font-serif mb-2">Customer Testimonials</h1>
    <p class="text-white-50 lead mb-0">Genuine feedback, culinary impressions, and dining experiences.</p>
  </div>
</div>

<section class="py-5 bg-light">
  <div class="container py-3">
    <div class="row g-5">
      <!-- Reviews List Column -->
      <div class="col-lg-7">
        <h3 class="fw-bold font-serif mb-4">What Diners Are Saying (<?= count($reviews) ?> Reviews)</h3>

        <?php if (!empty($reviews)): ?>
          <div class="d-flex flex-column gap-4">
            <?php foreach ($reviews as $rev): ?>
              <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="reviewer-info">
                    <div class="reviewer-avatar">
                      <?= strtoupper(substr($rev['user_name'], 0, 1)) ?>
                    </div>
                    <div>
                      <h6 class="mb-0 fw-bold"><?= htmlspecialchars($rev['user_name']) ?></h6>
                      <small class="text-muted"><?= formatDate($rev['created_at']) ?></small>
                    </div>
                  </div>
                  <div class="review-stars fs-6">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                      <i class="fa-<?= ($s <= $rev['rating']) ? 'solid' : 'regular' ?> fa-star"></i>
                    <?php endfor; ?>
                  </div>
                </div>

                <?php if (!empty($rev['food_name'])): ?>
                  <div class="mb-2">
                    <span class="badge bg-light text-dark border">
                      <i class="fa-solid fa-utensils text-warning me-1"></i> Dish: <?= htmlspecialchars($rev['food_name']) ?>
                    </span>
                  </div>
                <?php endif; ?>

                <p class="text-muted mb-0 leading-relaxed">
                  "<?= htmlspecialchars($rev['review']) ?>"
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="card border-0 shadow-sm rounded-4 p-5 bg-white text-center">
            <h5 class="fw-bold">No Reviews Yet</h5>
            <p class="text-muted mb-0">Be the very first diner to share your culinary thoughts!</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Write Review Column -->
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white sticky-top" style="top: 90px;">
          <h4 class="fw-bold font-serif mb-2">Share Your Experience</h4>
          <p class="text-muted small mb-4">We deeply value your thoughts and use them to elevate our hospitality.</p>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger shadow-sm mb-4">
              <ul class="mb-0 ps-3">
                <?php foreach ($errors as $err): ?>
                  <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (isLoggedIn()): ?>
            <form method="POST" action="<?= BASE_URL ?>reviews.php">
              <div class="mb-3">
                <label class="form-label fw-semibold small">Star Rating (1 - 5)</label>
                <select name="rating" class="form-select rounded-3 text-warning fw-bold" required>
                  <option value="5" selected>&#9733;&#9733;&#9733;&#9733;&#9733; (5 Stars - Exceptional)</option>
                  <option value="4">&#9733;&#9733;&#9733;&#9733; (4 Stars - Very Good)</option>
                  <option value="3">&#9733;&#9733;&#9733; (3 Stars - Average)</option>
                  <option value="2">&#9733;&#9733; (2 Stars - Mediocre)</option>
                  <option value="1">&#9733; (1 Star - Poor)</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold small">Dish Reviewed (Optional)</label>
                <select name="food_id" class="form-select rounded-3">
                  <option value="">General Restaurant Experience</option>
                  <?php foreach ($allFoods as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= (isset($_GET['food_id']) && $_GET['food_id'] == $f['id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($f['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-4">
                <label class="form-label fw-semibold small">Your Review</label>
                <textarea name="review" rows="4" class="form-control rounded-3" placeholder="Tell us how you enjoyed the taste, ambiance, spices, or delivery..." required><?= htmlspecialchars($_POST['review'] ?? '') ?></textarea>
              </div>

              <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-pill shadow">
                <i class="fa-solid fa-paper-plane me-2"></i> Submit My Review
              </button>
            </form>
          <?php else: ?>
            <div class="p-4 bg-light rounded-3 text-center border">
              <i class="fa-solid fa-lock text-warning display-6 mb-2"></i>
              <h6 class="fw-bold mb-2">Login Required</h6>
              <p class="text-muted small mb-3">To ensure all dining reviews are genuine and authentic, please log in with your customer account.</p>
              <div class="d-grid gap-2">
                <a href="<?= BASE_URL ?>user/login.php?redirect=<?= urlencode(BASE_URL . 'reviews.php') ?>" class="btn btn-primary-custom py-2">
                  Log In to Review
                </a>
                <a href="<?= BASE_URL ?>user/register.php" class="btn btn-outline-dark rounded-pill py-2 small">
                  Create Free Account
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
