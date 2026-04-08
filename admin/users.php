<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

if (isset($_GET['toggle_admin'])) {
    $targetId = (int)$_GET['toggle_admin'];
    if ($targetId !== (int)$_SESSION['user_id']) {
        $pdo->prepare("UPDATE users SET is_admin = NOT is_admin WHERE id = ?")
            ->execute([$targetId]);
    }
    header("Location: /marketplace/admin/users.php");
    exit();
}

$users = $pdo->query("
    SELECT u.*, COUNT(o.id) AS order_count
    FROM users u LEFT JOIN orders o ON u.id = o.user_id
    GROUP BY u.id ORDER BY u.created_at DESC
")->fetchAll();

require_once '../includes/header.php';
?>

<h4 class="mb-4">Manage Users</h4>

<table class="table table-hover table-bordered align-middle">
  <thead class="table-dark">
    <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Orders</th><th>Joined</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td>
          <span class="badge bg-<?= $u['is_admin'] ? 'warning text-dark' : 'secondary' ?>">
            <?= $u['is_admin'] ? 'Admin' : 'User' ?>
          </span>
        </td>
        <td><?= $u['order_count'] ?></td>
        <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
        <td>
          <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
            <a href="/marketplace/admin/users.php?toggle_admin=<?= $u['id'] ?>"
               class="btn btn-sm btn-outline-<?= $u['is_admin'] ? 'danger' : 'warning' ?>"
               onclick="return confirm('Change this user\'s role?')">
              <?= $u['is_admin'] ? 'Revoke Admin' : 'Make Admin' ?>
            </a>
          <?php else: ?>
            <span class="text-muted small">You</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
