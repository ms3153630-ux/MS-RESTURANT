<?php
/**
 * Admin Food Catalog Management (CRUD)
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$errors = [];
$editFood = null;

// Handle Delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $foodId = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM foods WHERE id = ?");
    $stmt->execute([$foodId]);
    setFlash('warning', 'Dish removed from catalog.');
    header("Location: " . BASE_URL . "admin/foods.php");
    exit;
}

// Handle Status Toggle
if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_GET['id'])) {
    $foodId = (int)$_GET['id'];
    $stmt = $pdo->prepare("UPDATE foods SET status = IF(status='active', 'inactive', 'active') WHERE id = ?");
    $stmt->execute([$foodId]);
    setFlash('info', 'Dish availability updated.');
    header("Location: " . BASE_URL . "admin/foods.php");
    exit;
}

// Handle Edit Mode
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $foodId = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM foods WHERE id = ?");
    $stmt->execute([$foodId]);
    $editFood = $stmt->fetch();
}

// Handle Add / Edit Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foodId      = (int)($_POST['food_id'] ?? 0);
    $categoryId  = (int)($_POST['category_id'] ?? 0);
    $name        = sanitize($_POST['name'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $ingredients = sanitize($_POST['ingredients'] ?? '');
    $status      = ($_POST['status'] ?? 'active') === 'active' ? 'active' : 'inactive';
    $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;
    $isPopular   = isset($_POST['is_popular']) ? 1 : 0;
    $isSpecial   = isset($_POST['is_special']) ? 1 : 0;
    $imageName   = $_POST['current_image'] ?? 'smash-burger.jpg';

    if (empty($name)) $errors[] = "Food item name is required.";
    if ($categoryId <= 0) $errors[] = "Please select a valid category.";
    if ($price <= 0) $errors[] = "Price must be greater than zero.";

    // Handle Image Upload
    if (!empty($_FILES['image']['name'])) {
        $file = $_FILES['image'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            $errors[] = "Allowed image extensions: JPG, PNG, WEBP.";
        } elseif ($file['size'] > 6 * 1024 * 1024) {
            $errors[] = "Image file is too large (max 6MB).";
        } else {
            $newFileName = 'dish_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $uploadTarget = __DIR__ . '/../assets/uploads/' . $newFileName;
            $imagesTarget = __DIR__ . '/../assets/images/' . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $uploadTarget)) {
                @copy($uploadTarget, $imagesTarget);
                $imageName = $newFileName;
            } else {
                $errors[] = "Failed to upload food dish image.";
            }
        }
    }

    if (empty($errors)) {
        if ($foodId > 0) {
            $updateSql = "UPDATE foods SET category_id = ?, name = ?, description = ?, ingredients = ?, price = ?, image = ?, is_featured = ?, is_popular = ?, is_special = ?, status = ? WHERE id = ?";
            $stmt = $pdo->prepare($updateSql);
            $stmt->execute([$categoryId, $name, $description, $ingredients, $price, $imageName, $isFeatured, $isPopular, $isSpecial, $status, $foodId]);
            setFlash('success', "Food item '{$name}' updated successfully.");
        } else {
            $insertSql = "INSERT INTO foods (category_id, name, description, ingredients, price, image, rating, is_featured, is_popular, is_special, status, created_at)
                          VALUES (?, ?, ?, ?, ?, ?, 4.8, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($insertSql);
            $stmt->execute([$categoryId, $name, $description, $ingredients, $price, $imageName, $isFeatured, $isPopular, $isSpecial, $status]);
            setFlash('success', "New dish '{$name}' added to menu catalog.");
        }
        header("Location: " . BASE_URL . "admin/foods.php");
        exit;
    }
}

// Fetch Categories for Dropdown
$catStmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll();

// Fetch All Foods with Category Info
$foods = $pdo->query("SELECT f.*, c.name AS category_name 
                      FROM foods f 
                      JOIN categories c ON f.category_id = c.id 
                      ORDER BY f.id DESC")->fetchAll();

$pageTitle = "Manage Food Items";
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
  <!-- Add/Edit Form Column -->
  <div class="col-lg-4">
    <div class="admin-card">
      <div class="admin-card-header">
        <h6 class="admin-card-title">
          <i class="fa-solid fa-<?= $editFood ? 'pen-to-square' : 'circle-plus' ?> text-warning me-2"></i>
          <?= $editFood ? 'Edit Dish Details' : 'Add New Food Item' ?>
        </h6>
        <?php if ($editFood): ?>
          <a href="<?= BASE_URL ?>admin/foods.php" class="btn btn-sm btn-outline-secondary">Cancel</a>
        <?php endif; ?>
      </div>
      <div class="admin-card-body">
        <form method="POST" action="<?= BASE_URL ?>admin/foods.php" enctype="multipart/form-data">
          <input type="hidden" name="food_id" value="<?= $editFood['id'] ?? 0 ?>">
          <input type="hidden" name="current_image" value="<?= $editFood['image'] ?? 'smash-burger.jpg' ?>">

          <div class="mb-3">
            <label class="form-label fw-semibold small">Dish Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Malai Boti, Truffle Burger" value="<?= htmlspecialchars($_POST['name'] ?? ($editFood['name'] ?? '')) ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select rounded-3" required>
              <option value="">Select Category...</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (($editFood['category_id'] ?? 0) == $cat['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Price (PKR) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light fw-bold">Rs.</span>
              <input type="number" step="0.01" name="price" class="form-control" placeholder="1200" value="<?= htmlspecialchars($_POST['price'] ?? ($editFood['price'] ?? '')) ?>" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Description</label>
            <textarea name="description" rows="3" class="form-control rounded-3" placeholder="Rich description of preparation, flavors..."><?= htmlspecialchars($_POST['description'] ?? ($editFood['description'] ?? '')) ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Ingredients (Comma Separated)</label>
            <input type="text" name="ingredients" class="form-control rounded-3" placeholder="Prime beef, cheddar, brioche bun, herbs..." value="<?= htmlspecialchars($_POST['ingredients'] ?? ($editFood['ingredients'] ?? '')) ?>">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small">Dish Photo</label>
            <input type="file" name="image" class="form-control rounded-3 image-preview-input" data-preview-target="foodImgPreview" accept="image/*">
            <?php if (!empty($editFood['image'])): ?>
              <div class="mt-2 text-center">
                <img id="foodImgPreview" src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($editFood['image']) ?>" alt="Preview" class="rounded-3 border" style="width: 100px; height: 100px; object-fit: cover;">
              </div>
            <?php else: ?>
              <div class="mt-2 text-center">
                <img id="foodImgPreview" src="" alt="Preview" class="rounded-3 border" style="width: 100px; height: 100px; object-fit: cover; display: none;">
              </div>
            <?php endif; ?>
          </div>

          <!-- Promotion Badges -->
          <div class="mb-3 p-3 bg-light rounded-3 border">
            <label class="form-label fw-bold small text-muted d-block mb-2">Display Badges</label>
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" name="is_featured" id="featCheck" <?= (!empty($editFood['is_featured'])) ? 'checked' : '' ?>>
              <label class="form-check-label small" for="featCheck">Featured on Homepage</label>
            </div>
            <div class="form-check form-switch mb-1">
              <input class="form-check-input" type="checkbox" name="is_popular" id="popCheck" <?= (!empty($editFood['is_popular'])) ? 'checked' : '' ?>>
              <label class="form-check-label small" for="popCheck">Popular Dish</label>
            </div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="is_special" id="specCheck" <?= (!empty($editFood['is_special'])) ? 'checked' : '' ?>>
              <label class="form-check-label small" for="specCheck">Chef Special Offer</label>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold small">Status</label>
            <select name="status" class="form-select rounded-3">
              <option value="active" <?= (($editFood['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active (In Stock)</option>
              <option value="inactive" <?= (($editFood['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive (Out of Stock)</option>
            </select>
          </div>

          <button type="submit" class="btn btn-warning w-100 py-2 fw-semibold">
            <i class="fa-solid fa-floppy-disk me-1"></i> <?= $editFood ? 'Update Food Item' : 'Publish Food Item' ?>
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- Food Items Table Column -->
  <div class="col-lg-8">
    <div class="admin-card">
      <div class="admin-card-header">
        <h6 class="admin-card-title"><i class="fa-solid fa-utensils text-primary me-2"></i>All Menu Items (<?= count($foods) ?>)</h6>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Dish</th>
              <th>Category</th>
              <th>Price</th>
              <th>Rating</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($foods)): ?>
              <?php foreach ($foods as $f): ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <img src="<?= BASE_URL ?>assets/images/<?= htmlspecialchars($f['image']) ?>" alt="<?= htmlspecialchars($f['name']) ?>" class="rounded-2 border" style="width: 50px; height: 50px; object-fit: cover;">
                      <div>
                        <div class="fw-bold small"><?= htmlspecialchars($f['name']) ?></div>
                        <div class="d-flex gap-1 mt-1">
                          <?php if ($f['is_featured']): ?><span class="badge bg-warning text-dark" style="font-size:0.65rem;">Featured</span><?php endif; ?>
                          <?php if ($f['is_popular']): ?><span class="badge bg-info text-dark" style="font-size:0.65rem;">Popular</span><?php endif; ?>
                          <?php if ($f['is_special']): ?><span class="badge bg-danger text-white" style="font-size:0.65rem;">Special</span><?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border"><?= htmlspecialchars($f['category_name']) ?></span>
                  </td>
                  <td class="fw-bold text-primary small">
                    <?= formatPrice($f['price']) ?>
                  </td>
                  <td class="small">
                    <span class="text-warning"><i class="fa-solid fa-star"></i></span> <?= number_format($f['rating'], 1) ?>
                  </td>
                  <td>
                    <a href="<?= BASE_URL ?>admin/foods.php?action=toggle&id=<?= $f['id'] ?>" class="text-decoration-none">
                      <span class="badge-status <?= ($f['status'] === 'active') ? 'badge-active' : 'badge-inactive' ?>">
                        <?= $f['status'] ?>
                      </span>
                    </a>
                  </td>
                  <td class="text-end">
                    <div class="d-flex justify-content-end gap-2">
                      <a href="<?= BASE_URL ?>food-details.php?id=<?= $f['id'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-circle p-2" title="Preview on Website">
                        <i class="fa-solid fa-eye"></i>
                      </a>
                      <a href="<?= BASE_URL ?>admin/foods.php?action=edit&id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary rounded-circle p-2" title="Edit">
                        <i class="fa-solid fa-pen"></i>
                      </a>
                      <a href="<?= BASE_URL ?>admin/foods.php?action=delete&id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete" onclick="return confirm('Permanently delete this dish?');">
                        <i class="fa-solid fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center py-4 text-muted">No dishes currently in the catalog.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
