<?php
/**
 * Admin Reviews Management
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

// Handle Actions
if (isset($_GET['action'], $_GET['id']) && in_array($_GET['action'], ['approve', 'hide', 'delete'])) {
    $revId = (int)$_GET['id'];
    $act = $_GET['action'];

    if ($act === 'approve') {
        $stmt = $pdo->prepare("UPDATE reviews SET status = 'approved' WHERE id = ?");
        $stmt->execute([$revId]);
        setFlash('success', 'Review approved and published.');
    } elseif ($act === 'hide') {
        $stmt = $pdo->prepare("UPDATE reviews SET status = 'hidden' WHERE id = ?");
        $stmt->execute([$revId]);
        setFlash('warning', 'Review hidden from public.');
    } elseif ($act === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$revId]);
        setFlash('danger', 'Review deleted permanently.');
    }
    header("Location: " . BASE_URL . "admin/reviews.php");
    exit;
}

// Fetch all reviews
$reviews = $pdo->query("SELECT r.*, u.name AS user_name, u.email AS user_email, f.name AS food_name 
                        FROM reviews r 
                        JOIN users u ON r.user_id = u.id 
                        LEFT JOIN foods f ON r.food_id = f.id 
                        ORDER BY r.id DESC")->fetchAll();

$pageTitle = "Manage Diner Reviews";
include __DIR__ . '/header.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <h6 class="admin-card-title"><i class="fa-solid fa-star text-warning me-2"></i>Customer Reviews &amp; Ratings (<?= count($reviews) ?>)</h6>
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Rating</th>
          <th>Customer</th>
          <th>Dish Tagged</th>
          <th>Review Content</th>
          <th>Date</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($reviews)): ?>
          <?php foreach ($reviews as $rev): ?>
            <tr>
              <td>
                <div class="text-warning small text-nowrap">
                  <?php for ($s = 1; $s <= 5; $s++): ?>
                    <i class="fa-<?= ($s <= $rev['rating']) ? 'solid' : 'regular' ?> fa-star"></i>
                  <?php endfor; ?>
                </div>
              </td>
              <td>
                <div class="fw-bold small"><?= htmlspecialchars($rev['user_name']) ?></div>
                <div class="small text-muted"><?= htmlspecialchars($rev['user_email']) ?></div>
              </td>
              <td class="small">
                <?= !empty($rev['food_name']) ? '<span class="badge bg-light text-dark border">' . htmlspecialchars($rev['food_name']) . '</span>' : '<span class="text-muted">General</span>' ?>
              </td>
              <td class="small text-muted" style="max-width: 300px;">
                "<?= htmlspecialchars($rev['review']) ?>"
              </td>
              <td class="small text-muted"><?= date('M d, Y', strtotime($rev['created_at'])) ?></td>
              <td>
                <span class="badge-status <?= ($rev['status'] === 'approved') ? 'badge-delivered' : (($rev['status'] === 'hidden') ? 'badge-inactive' : 'badge-pending') ?>">
                  <?= $rev['status'] ?>
                </span>
              </td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-1">
                  <?php if ($rev['status'] !== 'approved'): ?>
                    <a href="<?= BASE_URL ?>admin/reviews.php?action=approve&id=<?= $rev['id'] ?>" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1" title="Approve">
                      <i class="fa-solid fa-check"></i>
                    </a>
                  <?php endif; ?>
                  <?php if ($rev['status'] !== 'hidden'): ?>
                    <a href="<?= BASE_URL ?>admin/reviews.php?action=hide&id=<?= $rev['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" title="Hide">
                      <i class="fa-solid fa-eye-slash"></i>
                    </a>
                  <?php endif; ?>
                  <a href="<?= BASE_URL ?>admin/reviews.php?action=delete&id=<?= $rev['id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete" onclick="return confirm('Delete review?');">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center py-4 text-muted">No reviews submitted yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
