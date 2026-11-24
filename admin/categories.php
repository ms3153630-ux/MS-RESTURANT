<?php
/**
 * Admin Categories Management
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$errors = [];
$editCategory = null;

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $catId = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$catId]);
    setFlash('warning', 'Category deleted successfully.');
    header("Location: " . BASE_URL . "admin/categories.php");
    exit;
}

// Handle Status Toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $catId = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE categories SET status = IF(status='active', 'inactive', 'active') WHERE id = ?");
    $stmt->execute([$catId]);
    setFlash('info', 'Category status updated.');
    header("Location: " . BASE_URL . "admin/categories.php");
    exit;
}

// Handle Edit Mode
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $catId = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$catId]);
    $editCategory = $stmt->fetch();
}

// Handle Form Submission (Add or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catId       = (int)($_POST['category_id'] ?? 0);
    $name        = sanitize($_POST['name'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $status      = ($_POST['status'] ?? 'active') === 'active' ? 'active' : 'inactive';
    $imageName   = $_POST['current_image'] ?? 'cat-burgers.jpg';

    if (empty($name)) {
        $errors[] = "Category name is required.";
    }

    // Handle Image Upload
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            $errors[] = "Allowed image types: JPG, PNG, WEBP.";
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $errors[] = "Image size exceeds 5MB limit.";
        } else {
            $newFileName = 'cat_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $uploadTarget = __DIR__ . '/../assets/uploads/' . $newFileName;
            $imagesTarget = __DIR__ . '/../assets/images/' . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $uploadTarget)) {
                @copy($uploadTarget, $imagesTarget);
                $imageName = $newFileName;
            } else {
                $errors[] = "Failed to upload category image.";
            }
        }
    }

    if (empty($errors)) {
        if ($catId > 0) {
            // Update
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ?, image = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $description, $imageName, $status, $catId]);
            setFlash('success', "Category '{$name}' updated successfully.");
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO categories (name, description, image, status, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $description, $imageName, $status]);
            setFlash('success', "Category '{$name}' created successfully.");
        }
        header("Location: " . BASE_URL . "admin/categories.php");
        exit;
    }
}

// Fetch all categories with food count
$categories = $pdo->query("SELECT c.*, COUNT(f.id) AS total_foods 
                           FROM categories c 
                           LEFT JOIN foods f ON c.id = f.category_id 
                           GROUP BY c.id 
                           ORDER BY c.id ASC")->fetchAll();

$pageTitle = "Manage Categories";
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

<div class="row g-4">
  <!-- Add / Edit Form Column -->
  <div class="col-lg-4">
    <div class="admin-card">
      <div class="admin-card-header">
        <h6 class="admin-card-title">
          <i class="fa-solid fa-<?= $editCategory ? 'pen-to-square' : 'circle-plus' ?> text-warning me-2"></i>
          <?= $editCategory ? 'Edit Category' : 'Create Category' ?>
        </h6>
        <?php if ($editCategory): ?>
          <a href="<?= BASE_URL ?>admin/categories.php" class="btn btn-sm btn-outline-secondary">Cancel</a>
        <?php endif; ?>
      </div>
      <div class="admin-card-body">
        <form method="POST" action="<?= BASE_URL ?>admin/categories.php" enctype="multipart/form-data">
          <input type="hidden" name="category_id" value="<?= $editCategory['id'] ?? 0 ?>">
          <input type="hidden" name="current_image" value="<?= $editCategory['image'] ?? 'cat-burgers.jpg' ?>">

          <div class="mb-3">
            <label class="form-label fw-semibold small">Category Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Seafood, Steaks, Drinks" value="<?= htmlspecialchars($_POST['name'] ?? ($editCategory['name'] ?? '')) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Short Description</label>
            <textarea name="description" rows="3" class="form-control rounded-3" placeholder="Appetizing category summary..."><?= htmlspecialchars($_POST['description'] ?? ($editCategory['description'] ?? '')) ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Category Photo</label>
            <input type="file" name="image" class="form-control rounded-3 image-preview-input" data-preview-target="catImgPreview" accept="image/*">
            <?php if (!empty($editCategory['image'])): ?>
              <div class="mt-2 text-center">
                <img id="catImgPreview" src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($editCategory['image']) ?>" alt="Preview" class="rounded-3 border" style="width: 100px; height: 100px; object-fit: cover;">
              </div>
            <?php else: ?>
              <div class="mt-2 text-center">
                <img id="catImgPreview" src="" alt="Preview" class="rounded-3 border" style="width: 100px; height: 100px; object-fit: cover; display: none;">
              </div>
            <?php endif; ?>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold small">Status</label>
            <select name="status" class="form-select rounded-3">
              <option value="active" <?= (($editCategory['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active (Visible on Menu)</option>
              <option value="inactive" <?= (($editCategory['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive (Hidden)</option>
            </select>
          </div>

          <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold">
            <i class="fa-solid fa-save me-1"></i> <?= $editCategory ? 'Update Category' : 'Save Category' ?>
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Categories Table Column -->
  <div class="col-lg-8">
    <div class="admin-card">
      <div class="admin-card-header">
        <h6 class="admin-card-title"><i class="fa-solid fa-list text-primary me-2"></i>All Categories (<?= count($categories) ?>)</h6>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Image</th>
              <th>Name</th>
              <th>Dishes</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($categories)): ?>
              <?php foreach ($categories as $cat): ?>
                <tr>
                  <td>
                    <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($cat['image'] ?? 'cat-burgers.jpg') ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="rounded-2 border" style="width: 50px; height: 50px; object-fit: cover;">
                  </td>
                  <td>
                    <div class="fw-bold"><?= htmlspecialchars($cat['name']) ?></div>
                    <small class="text-muted d-inline-block text-truncate" style="max-width: 250px;"><?= htmlspecialchars($cat['description'] ?? '') ?></small>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border fw-bold"><?= $cat['total_foods'] ?> Items</span>
                  </td>
                  <td>
                    <a href="<?= BASE_URL ?>admin/categories.php?action=toggle&id=<?= $cat['id'] ?>" class="text-decoration-none">
                      <span class="badge-status <?= ($cat['status'] === 'active') ? 'badge-active' : 'badge-inactive' ?>">
                        <?= $cat['status'] ?>
                      </span>
                    </a>
                  </td>
                  <td class="text-end">
                    <div class="d-flex justify-content-end gap-2">
                      <a href="<?= BASE_URL ?>admin/categories.php?action=edit&id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary rounded-circle p-2" title="Edit">
                        <i class="fa-solid fa-pen"></i>
                      </a>
                      <a href="<?= BASE_URL ?>admin/categories.php?action=delete&id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete" onclick="return confirm('Deleting category will also remove all associated food dishes. Continue?');">
                        <i class="fa-solid fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" class="text-center py-4 text-muted">No categories created yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
