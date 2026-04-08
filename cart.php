<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireLogin();

if (isset($_GET['remove'])) {
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([(int)$_GET['remove'], $_SESSION['user_id']]);
    header("Location: /marketplace/cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['qty'] as $cartId => $qty) {
        $qty = max(1, (int)$qty);
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$qty, (int)$cartId, $_SESSION['user_id']]);
    }
    header("Location: /marketplace/cart.php");
    exit();
}

$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.image_url, p.stock
    FROM cart c JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$items = $stmt->fetchAll();
$total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));

require_once 'includes/header.php';
?>

<h4 class="mb-4">Your Cart</h4>

<?php if (empty($items)): ?>
  <div class="alert alert-info">Your cart is empty. <a href="/marketplace/products.php">Browse listings</a>.</div>
<?php else: ?>
  <form method="POST">
    <input type="hidden" name="update" value="1">
    <table class="table align-middle">
      <thead class="table-dark">
        <tr><th>Item</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <img src="<?= htmlspecialchars($item['image_url'] ?: '/marketplace/images/placeholder.png') ?>"
                     width="60" class="rounded" onerror="this.src='/marketplace/images/placeholder.png'">
                <a href="/marketplace/product.php?id=<?= $item['product_id'] ?>"><?= htmlspecialchars($item['name']) ?></a>
              </div>
            </td>
            <td>$<?= number_format($item['price'], 2) ?></td>
            <td>
              <input type="number" name="qty[<?= $item['cart_id'] ?>]" value="<?= $item['quantity'] ?>"
                     min="1" max="<?= $item['stock'] ?>" class="form-control" style="width:70px">
            </td>
            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            <td>
              <a href="/marketplace/cart.php?remove=<?= $item['cart_id'] ?>" class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Remove this item?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center mt-3">
      <button type="submit" class="btn btn-outline-secondary">Update Cart</button>
      <div class="text-end">
        <h5>Total: <strong class="text-success">$<?= number_format($total, 2) ?></strong></h5>
        <a href="/marketplace/checkout.php" class="btn btn-dark btn-lg mt-2">Proceed to Checkout</a>
      </div>
    </div>
  </form>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
