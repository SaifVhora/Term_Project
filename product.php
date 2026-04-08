<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, u.name AS seller_name FROM products p JOIN users u ON p.seller_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) { header("Location: /marketplace/products.php"); exit(); }

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check->execute([$_SESSION['user_id'], $product['id']]);
    $existing = $check->fetch();
    if ($existing) {
        $upd = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
        $upd->execute([$qty, $existing['id']]);
    } else {
        $ins = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $ins->execute([$_SESSION['user_id'], $product['id'], $qty]);
    }
    $message = 'success';
}

require_once 'includes/header.php';
?>

<?php if ($message === 'success'): ?>
  <div class="alert alert-success alert-dismissible fade show">
    Item added to your cart! <a href="/marketplace/cart.php" class="alert-link">View Cart</a>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row g-4">
  <div class="col-md-5">
    <img src="<?= htmlspecialchars($product['image_url'] ?: '/marketplace/images/placeholder.png') ?>"
         class="img-fluid rounded shadow" alt="<?= htmlspecialchars($product['name']) ?>"
         onerror="this.src='/marketplace/images/placeholder.png'">
  </div>
  <div class="col-md-7">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <p class="text-muted"><?= htmlspecialchars($product['category']) ?> &bull; Condition: <strong><?= htmlspecialchars($product['condition']) ?></strong></p>
    <h3 class="text-success">$<?= number_format($product['price'], 2) ?></h3>
    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
    <p class="text-muted small">Sold by: <?= htmlspecialchars($product['seller_name']) ?></p>
    <p class="text-muted small">Stock: <?= $product['stock'] > 0 ? $product['stock'] . ' available' : '<span class="text-danger">Out of stock</span>' ?></p>

    <?php if (isLoggedIn() && $product['stock'] > 0): ?>
      <form method="POST" class="d-flex gap-2 align-items-center">
        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control" style="width:80px">
        <button type="submit" class="btn btn-dark">Add to Cart</button>
      </form>
    <?php elseif (!isLoggedIn()): ?>
      <a href="/marketplace/login.php" class="btn btn-outline-dark">Login to Buy</a>
    <?php else: ?>
      <button class="btn btn-secondary" disabled>Out of Stock</button>
    <?php endif; ?>

    <div class="mt-3">
      <a href="/marketplace/products.php" class="text-muted small">&larr; Back to listings</a>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
