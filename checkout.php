<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireLogin();

$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.stock
    FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();

if (empty($items)) { header("Location: /marketplace/cart.php"); exit(); }

$total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($items as $item) {
        if ($item['quantity'] > $item['stock'])
            $errors[] = "{$item['name']} only has {$item['stock']} in stock.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $orderId = $pdo->lastInsertId();

        foreach ($items as $item) {
            $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)")
                ->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")
                ->execute([$item['quantity'], $item['product_id']]);
        }

        $pdo->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$_SESSION['user_id']]);
        header("Location: /marketplace/orders.php?success=1");
        exit();
    }
}

require_once 'includes/header.php';
?>

<h4 class="mb-4">Checkout</h4>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <?php foreach ($errors as $e): ?><p class="mb-0"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-md-7">
    <h5>Order Summary</h5>
    <table class="table">
      <thead><tr><th>Item</th><th>Qty</th><th>Subtotal</th></tr></thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr><th colspan="2">Total</th><th class="text-success">$<?= number_format($total, 2) ?></th></tr>
      </tfoot>
    </table>
  </div>
  <div class="col-md-5">
    <div class="card p-4 shadow-sm">
      <h5>Place Order</h5>
      <p class="text-muted small">This is a demo marketplace. No real payment is processed.</p>
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['name']) ?>" readonly>
        </div>
        <button type="submit" class="btn btn-dark w-100">Confirm Order</button>
      </form>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
