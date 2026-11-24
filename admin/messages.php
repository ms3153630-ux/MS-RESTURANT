<?php
/**
 * Admin Contact Messages Management
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/admin_auth.php';

// Handle Actions
if (isset($_GET['action'], $_GET['id']) && in_array($_GET['action'], ['mark_read', 'mark_unread', 'delete'])) {
    $msgId = (int)$_GET['id'];
    $act = $_GET['action'];

    if ($act === 'mark_read') {
        $stmt = $pdo->prepare("UPDATE contacts SET status = 'read' WHERE id = ?");
        $stmt->execute([$msgId]);
        setFlash('info', 'Message marked as read.');
    } elseif ($act === 'mark_unread') {
        $stmt = $pdo->prepare("UPDATE contacts SET status = 'unread' WHERE id = ?");
        $stmt->execute([$msgId]);
        setFlash('info', 'Message marked as unread.');
    } elseif ($act === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
        $stmt->execute([$msgId]);
        setFlash('danger', 'Message deleted.');
    }
    header("Location: " . BASE_URL . "admin/messages.php");
    exit;
}

// Fetch all messages
$messages = $pdo->query("SELECT * FROM contacts ORDER BY id DESC")->fetchAll();

$pageTitle = "Contact Inquiries";
include __DIR__ . '/header.php';
?>

<div class="admin-card">
  <div class="admin-card-header">
    <h6 class="admin-card-title"><i class="fa-solid fa-envelope text-info me-2"></i>Inquiries &amp; Messages (<?= count($messages) ?>)</h6>
  </div>
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Sender</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Received</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($messages)): ?>
          <?php foreach ($messages as $m): ?>
            <tr class="<?= ($m['status'] === 'unread') ? 'table-warning bg-opacity-10' : '' ?>">
              <td>
                <div class="fw-bold"><?= htmlspecialchars($m['name']) ?></div>
                <div class="small text-muted"><?= htmlspecialchars($m['email']) ?></div>
                <?php if (!empty($m['phone'])): ?>
                  <div class="small text-muted"><?= htmlspecialchars($m['phone']) ?></div>
                <?php endif; ?>
              </td>
              <td class="fw-semibold small">
                <?= htmlspecialchars($m['subject']) ?>
              </td>
              <td class="small text-muted" style="max-width: 320px;">
                <?= nl2br(htmlspecialchars($m['message'])) ?>
              </td>
              <td class="small text-muted"><?= date('M d, H:i', strtotime($m['created_at'])) ?></td>
              <td>
                <span class="badge-status <?= ($m['status'] === 'unread') ? 'badge-unread' : 'badge-read' ?>">
                  <?= $m['status'] ?>
                </span>
              </td>
              <td class="text-end">
                <div class="d-flex justify-content-end gap-1">
                  <?php if ($m['status'] === 'unread'): ?>
                    <a href="<?= BASE_URL ?>admin/messages.php?action=mark_read&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1" title="Mark as Read">
                      <i class="fa-solid fa-envelope-open"></i>
                    </a>
                  <?php else: ?>
                    <a href="<?= BASE_URL ?>admin/messages.php?action=mark_unread&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" title="Mark as Unread">
                      <i class="fa-solid fa-envelope"></i>
                    </a>
                  <?php endif; ?>
                  <a href="<?= BASE_URL ?>admin/messages.php?action=delete&id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-danger rounded-circle p-2" title="Delete" onclick="return confirm('Delete message?');">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center py-4 text-muted">No messages received yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
