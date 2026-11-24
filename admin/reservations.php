<?php
/**
 * Admin Table Reservations Management
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

// Handle Status Updates
if (isset($_GET['action'], $_GET['id']) && in_array($_GET['action'], ['confirm', 'cancel', 'complete', 'delete'])) {
    $resId = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'confirm') {
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'Confirmed' WHERE id = ?");
        $stmt->execute([$resId]);
        setFlash('success', "Reservation #{$resId} confirmed.");
    } elseif ($action === 'cancel') {
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'Cancelled' WHERE id = ?");
        $stmt->execute([$resId]);
        setFlash('warning', "Reservation #{$resId} cancelled.");
    } elseif ($action === 'complete') {
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'Completed' WHERE id = ?");
        $stmt->execute([$resId]);
        setFlash('info', "Reservation #{$resId} marked completed.");
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $stmt->execute([$resId]);
        setFlash('danger', "Reservation #{$resId} record deleted.");
    }
    header("Location: " . BASE_URL . "admin/reservations.php");
    exit;
}

// Fetch all reservations
$filterStatus = sanitize($_GET['status'] ?? 'all');
$sql = "SELECT * FROM reservations";
$params = [];
if ($filterStatus !== 'all' && !empty($filterStatus)) {
    $sql .= " WHERE status = ?";
    $params[] = $filterStatus;
}
$sql .= " ORDER BY reservation_date DESC, reservation_time DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll();

$pageTitle = "Manage Table Reservations";
include __DIR__ . '/header.php';
?>

<!-- Filter Pills -->
<div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4">
  <div class="d-flex flex-wrap gap-2 align-items-center">
    <span class="fw-bold small text-muted me-2"><i class="fa-solid fa-filter me-1"></i>Filter Reservations:</span>
    <a href="<?= BASE_URL ?>admin/reservations.php?status=all" class="btn btn-sm <?= ($filterStatus === 'all') ? 'btn-dark' : 'btn-outline-secondary' ?> rounded-pill px-3">
      All
    </a>
    <a href="<?= BASE_URL ?>admin/reservations.php?status=Pending" class="btn btn-sm <?= ($filterStatus === 'Pending') ? 'btn-warning text-dark fw-bold' : 'btn-outline-warning' ?> rounded-pill px-3">
      Pending
    </a>
    <a href="<?= BASE_URL ?>admin/reservations.php?status=Confirmed" class="btn btn-sm <?= ($filterStatus === 'Confirmed') ? 'btn-success text-white fw-bold' : 'btn-outline-success' ?> rounded-pill px-3">
      Confirmed
    </a>
    <a href="<?= BASE_URL ?>admin/reservations.php?status=Completed" class="btn btn-sm <?= ($filterStatus === 'Completed') ? 'btn-info text-dark fw-bold' : 'btn-outline-info' ?> rounded-pill px-3">
      Completed
    </a>
    <a href="<?= BASE_URL ?>admin/reservations.php?status=Cancelled" class="btn btn-sm <?= ($filterStatus === 'Cancelled') ? 'btn-danger text-white fw-bold' : 'btn-outline-danger' ?> rounded-pill px-3">
      Cancelled
    </a>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card-header">
    <h6 class="admin-card-title"><i class="fa-solid fa-calendar-check text-warning me-2"></i>Reservations Roster (<?= count($reservations) ?>)</h6>
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Guest Name</th>
          <th>Contact</th>
          <th>Date &amp; Time</th>
          <th>Guests</th>
          <th>Special Notes</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($reservations)): ?>
          <?php foreach ($reservations as $r): ?>
            <tr>
              <td class="text-muted fw-bold">#<?= $r['id'] ?></td>
              <td>
                <div class="fw-bold"><?= htmlspecialchars($r['customer_name']) ?></div>
              </td>
              <td>
                <div class="small fw-semibold"><?= htmlspecialchars($r['phone']) ?></div>
                <div class="small text-muted"><?= htmlspecialchars($r['email']) ?></div>
              </td>
              <td>
                <strong><?= date('M d, Y', strtotime($r['reservation_date'])) ?></strong>
                <div class="small text-muted"><?= date('h:i A', strtotime($r['reservation_time'])) ?></div>
              </td>
              <td>
                <span class="badge bg-light text-dark border fw-bold"><?= $r['guests'] ?> People</span>
              </td>
              <td class="small text-muted" style="max-width: 200px;">
                <?= !empty($r['special_request']) ? htmlspecialchars($r['special_request']) : '<span class="text-secondary fst-italic">None</span>' ?>
              </td>
              <td>
                <span class="badge-status <?= ($r['status'] === 'Confirmed') ? 'badge-delivered' : (($r['status'] === 'Cancelled') ? 'badge-cancelled' : (($r['status'] === 'Completed') ? 'badge-confirmed' : 'badge-pending')) ?>">
                  <?= $r['status'] ?>
                </span>
              </td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-1">
                  <?php if ($r['status'] === 'Pending'): ?>
                    <a href="<?= BASE_URL ?>admin/reservations.php?action=confirm&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1" title="Confirm Table">
                      <i class="fa-solid fa-check"></i>
                    </a>
                    <a href="<?= BASE_URL ?>admin/reservations.php?action=cancel&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1" title="Cancel Table">
                      <i class="fa-solid fa-xmark"></i>
                    </a>
                  <?php endif; ?>
                  <?php if ($r['status'] === 'Confirmed'): ?>
                    <a href="<?= BASE_URL ?>admin/reservations.php?action=complete&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1" title="Mark Completed">
                      <i class="fa-solid fa-circle-check"></i>
                    </a>
                  <?php endif; ?>
                  <a href="<?= BASE_URL ?>admin/reservations.php?action=delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete" onclick="return confirm('Delete reservation record permanently?');">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" class="text-center py-4 text-muted">No reservations found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
