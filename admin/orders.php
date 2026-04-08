<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $validStatuses = ['Pending','Processing','Shipped','Completed','Cancelled'];
    if (in_array($_POST['status'], $validStatuses)) {
        $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")
            ->execute([$_POST['status'], (int)$_POST['order_id']]);
    }
    header("Location: /marketplace/admin/orders.php?updated=1");
    exit();
}

$orders = $pdo->query("
    SELECT o.*, u.name AS user_name, u.email
    FROM orders o JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
")->fetchAll();

require_once '../includes/header.php';
?>

<h4 class="mb-4">Manage Orders</h4>

<?php if (isset($_GET['updated'])): ?>
  <div class="alert alert-success">Order status updated.</div>
<?php endif; ?>

<table class="table table-hover table-bordered align-middle">
  <thead class="table-dark">
    <tr><th>#</th><th>Customer</th><th>Total</th><th>Date</th><th>Status</th><th>Update</th></tr>
  </thead>
  <tbody>
    <?php foreach ($orders as $o): ?>
      <tr>
        <td><?= $o['id'] ?></td>
        <td>
          <?= htmlspecialchars($o['user_name']) ?><br>
          <small class="text-muted"><?= htmlspecialchars($o['email']) ?></small>
        </td>
        <td>$<?= number_format($o['total_price'], 2) ?></td>
        <td><?= date('M d, Y', strtotime($o['order_date'])) ?></td>
        <td>
          <span class="badge bg-<?= $o['status'] === 'Completed' ? 'success' : ($o['status'] === 'Cancelled' ? 'danger' : 'secondary') ?>">
            <?= $o['status'] ?>
          </span>
        </td>
        <td>
          <form method="POST" class="d-flex gap-1">
            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
            <select name="status" class="form-select form-select-sm" style="width:140px">
              <?php foreach (['Pending','Processing','Shipped','Completed','Cancelled'] as $s): ?>
                <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-dark">Save</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
