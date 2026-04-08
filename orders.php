<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<h4 class="mb-4">My Orders</h4>

<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">Order placed successfully! Thank you.</div>
<?php endif; ?>

<?php if (empty($orders)): ?>
  <div class="alert alert-info">No orders yet. <a href="/marketplace/products.php">Shop now</a>.</div>
<?php else: ?>
  <?php foreach ($orders as $order): ?>
    <div class="card mb-3 shadow-sm">
      <div class="card-header d-flex justify-content-between">
        <span>Order #<?= $order['id'] ?> &mdash; <?= date('M d, Y', strtotime($order['order_date'])) ?></span>
        <span class="badge bg-<?= $order['status'] === 'Completed' ? 'success' : ($order['status'] === 'Cancelled' ? 'danger' : 'secondary') ?>">
          <?= $order['status'] ?>
        </span>
      </div>
      <div class="card-body">
        <?php
        $istmt = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $istmt->execute([$order['id']]);
        $items = $istmt->fetchAll();
        ?>
        <ul class="list-unstyled mb-0">
          <?php foreach ($items as $item): ?>
            <li><?= htmlspecialchars($item['name'] ?? '[deleted]') ?> &times; <?= $item['quantity'] ?> &mdash; $<?= number_format($item['price_at_purchase'] * $item['quantity'], 2) ?></li>
          <?php endforeach; ?>
        </ul>
        <p class="mt-2 mb-0 fw-bold">Total: $<?= number_format($order['total_price'], 2) ?></p>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
