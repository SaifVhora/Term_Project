<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

$categories = ['Electronics','Clothing','Books','Music','Collectibles','Other'];
$filter = $_GET['category'] ?? '';
$search = trim($_GET['search'] ?? '');

$sql = "SELECT p.*, u.name AS seller_name FROM products p JOIN users u ON p.seller_id = u.id WHERE p.stock > 0";
$params = [];

if ($filter && in_array($filter, $categories)) {
    $sql .= " AND p.category = ?";
    $params[] = $filter;
}
if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<h4 class="mb-3">Browse Listings</h4>

<form method="GET" class="row g-2 mb-4">
  <div class="col-md-6">
    <input type="text" name="search" class="form-control" placeholder="Search listings..." value="<?= htmlspecialchars($search) ?>">
  </div>
  <div class="col-md-4">
    <select name="category" class="form-select">
      <option value="">All Categories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat ?>" <?= $filter === $cat ? 'selected' : '' ?>><?= $cat ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-dark w-100">Filter</button>
  </div>
</form>

<?php if (empty($products)): ?>
  <div class="alert alert-info">No listings found.</div>
<?php else: ?>
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
            <p class="fw-bold text-success mb-0">$<?= number_format($p['price'], 2) ?></p>
            <p class="text-muted small">by <?= htmlspecialchars($p['seller_name']) ?></p>
          </div>
          <div class="card-footer bg-white border-0">
            <a href="/marketplace/product.php?id=<?= $p['id'] ?>" class="btn btn-dark btn-sm w-100">View Item</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
