<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

$userCount    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenue      = $pdo->query("SELECT SUM(total_price) FROM orders WHERE status != 'Cancelled'")->fetchColumn();

$recentOrders = $pdo->query("
    SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC LIMIT 10
")->fetchAll();

require_once '../includes/header.php';
?>

<h4 class="mb-4"><i class="bi bi-shield-lock"></i> Admin Dashboard</h4>

<div class="row g-4 mb-4">
  <div class="col-md-3">
    <div class="card text-white bg-dark shadow-sm text-center p-3">
      <h2><?= $userCount ?></h2><p class="mb-0">Users</p>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-secondary shadow-sm text-center p-3">
      <h2><?= $productCount ?></h2><p class="mb-0">Listings</p>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-success shadow-sm text-center p-3">
      <h2><?= $orderCount ?></h2><p class="mb-0">Orders</p>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-primary shadow-sm text-center p-3">
      <h2>$<?= number_format($revenue ?? 0, 2) ?></h2><p class="mb-0">Revenue</p>
    </div>
  </div>
</div>

<div class="mb-4 d-flex gap-2">
  <a href="/marketplace/admin/products.php" class="btn btn-outline-dark">Manage Listings</a>
  <a href="/marketplace/admin/orders.php" class="btn btn-outline-dark">Manage Orders</a>
  <a href="/marketplace/admin/users.php" class="btn btn-outline-dark">Manage Users</a>
</div>

<h5>Recent Orders</h5>
<table class="table table-hover table-bordered">
  <thead class="table-dark">
    <tr><th>#</th><th>User</th><th>Total</th><th>Status</th><th>Date</th></tr>
  </thead>
  <tbody>
    <?php foreach ($recentOrders as $o): ?>
      <tr>
        <td><?= $o['id'] ?></td>
        <td><?= htmlspecialchars($o['user_name']) ?></td>
        <td>$<?= number_format($o['total_price'], 2) ?></td>
        <td>
          <span class="badge bg-<?= $o['status'] === 'Completed' ? 'success' : ($o['status'] === 'Cancelled' ? 'danger' : 'secondary') ?>">
            <?= $o['status'] ?>
          </span>
        </td>
        <td><?= date('M d, Y', strtotime($o['order_date'])) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
