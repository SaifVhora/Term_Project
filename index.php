<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT p.*, u.name AS seller_name FROM products p JOIN users u ON p.seller_id = u.id ORDER BY p.created_at DESC LIMIT 8");
$products = $stmt->fetchAll();
?>

<div class="p-5 mb-4 bg-dark text-white rounded-3">
  <div class="container-fluid py-3">
    <h1 class="display-5 fw-bold"><i class="bi bi-shop"></i> SecondHand Marketplace</h1>
    <p class="col-md-8 fs-4">Buy and sell used electronics, clothing, books, vinyl, collectibles, and more.</p>
    <a href="/marketplace/products.php" class="btn btn-light btn-lg me-2">Browse Listings</a>
    <?php if (isLoggedIn()): ?>
      <a href="/marketplace/sell.php" class="btn btn-outline-light btn-lg">Sell an Item</a>
    <?php else: ?>
      <a href="/marketplace/register.php" class="btn btn-outline-light btn-lg">Get Started</a>
    <?php endif; ?>
  </div>
</div>

<div class="mb-4 d-flex flex-wrap gap-2">
  <?php foreach (['Electronics','Clothing','Books','Music','Collectibles','Other'] as $cat): ?>
    <a href="/marketplace/products.php?category=<?= urlencode($cat) ?>" class="btn btn-outline-secondary btn-sm"><?= $cat ?></a>
  <?php endforeach; ?>
</div>

<h4 class="mb-3">Recent Listings</h4>
<div class="row row-cols-1 row-cols-md-4 g-4">
  <?php foreach ($products as $p): ?>
    <div class="col">
      <div class="card h-100 shadow-sm">
        <img src="<?= htmlspecialchars($p['image_url'] ?: '/marketplace/images/placeholder.png') ?>"
             class="card-img-top product-thumb" alt="<?= htmlspecialchars($p['name']) ?>"
             onerror="this.src='/marketplace/images/placeholder.png'">
        <div class="card-body">
          <h6 class="card-title"><?= htmlspecialchars($p['name']) ?></h6>
          <p class="text-muted small mb-1"><?= htmlspecialchars($p['category']) ?> &bull; <?= htmlspecialchars($p['condition']) ?></p>
          <p class="fw-bold text-success">$<?= number_format($p['price'], 2) ?></p>
        </div>
        <div class="card-footer bg-white border-0">
          <a href="/marketplace/product.php?id=<?= $p['id'] ?>" class="btn btn-dark btn-sm w-100">View Item</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
