<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireLogin();

$errors = [];
$success = '';
$categories = ['Electronics','Clothing','Books','Music','Collectibles','Other'];
$conditions = ['New','Like New','Good','Fair','Poor'];

// Delete own listing
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $check = $pdo->prepare("SELECT seller_id FROM products WHERE id = ?");
    $check->execute([$delId]);
    $row = $check->fetch();
    if ($row && ((int)$row['seller_id'] === (int)$_SESSION['user_id'] || isAdmin())) {
        $pdo->prepare("DELETE FROM cart WHERE product_id = ?")->execute([$delId]);
        $pdo->prepare("UPDATE order_items SET product_id = NULL WHERE product_id = ?")->execute([$delId]);
        $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$delId]);
    }
    header("Location: /marketplace/sell.php?deleted=1");
    exit();
}

// Post new listing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = $_POST['price'] ?? '';
    $category    = $_POST['category'] ?? '';
    $condition   = $_POST['condition'] ?? '';
    $stock       = (int)($_POST['stock'] ?? 1);
    $image_url   = trim($_POST['image_url'] ?? '');

    if (empty($name))                       $errors[] = "Product name is required.";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Valid price is required.";
    if (!in_array($category, $categories))  $errors[] = "Select a valid category.";
    if (!in_array($condition, $conditions)) $errors[] = "Select a valid condition.";
    if ($stock < 1)                         $errors[] = "Stock must be at least 1.";
    if (!empty($image_url) && !preg_match('/^https?:\/\/.+/i', $image_url))
        $errors[] = "Image URL must start with http:// or https://";

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (seller_id, name, description, price, image_url, category, `condition`, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $name, $description, $price, $image_url ?: null, $category, $condition, $stock]);
        $success = "Listing created! <a href='/marketplace/products.php'>View listings</a>.";
    }
}

// Fetch user's own listings
$myStmt = $pdo->prepare("SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC");
$myStmt->execute([$_SESSION['user_id']]);
$myListings = $myStmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="row g-4">

  <div class="col-md-6">
    <h4 class="mb-4">List an Item for Sale</h4>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
      <div class="alert alert-success">Listing deleted.</div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Product Name *</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
      </div>
      <div class="row g-2 mb-3">
        <div class="col-4">
          <label class="form-label">Price (CAD) *</label>
          <input type="number" name="price" class="form-control" step="0.01" min="0.01"
                 value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required>
        </div>
        <div class="col-4">
          <label class="form-label">Category *</label>
          <select name="category" class="form-select" required>
            <option value="">-- Select --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat ?>" <?= ($_POST['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-4">
          <label class="form-label">Condition *</label>
          <select name="condition" class="form-select" required>
            <option value="">-- Select --</option>
            <?php foreach ($conditions as $cond): ?>
              <option value="<?= $cond ?>" <?= ($_POST['condition'] ?? '') === $cond ? 'selected' : '' ?>><?= $cond ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="row g-2 mb-3">
        <div class="col-4">
          <label class="form-label">Stock *</label>
          <input type="number" name="stock" class="form-control" min="1"
                 value="<?= htmlspecialchars($_POST['stock'] ?? 1) ?>" required>
        </div>
        <div class="col-8">
          <label class="form-label">Image URL <span class="text-muted small">(optional, https://...)</span></label>
          <input type="text" name="image_url" id="imageUrlInput" class="form-control"
                 value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>"
                 placeholder="https://example.com/image.jpg">
        </div>
      </div>
      <div id="imagePreview" class="mb-3" style="display:none">
        <p class="small text-muted mb-1">Preview:</p>
        <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-height:130px; max-width:180px">
      </div>
      <button type="submit" class="btn btn-dark w-100">Post Listing</button>
    </form>
  </div>

  <div class="col-md-6">
    <h4 class="mb-4">My Listings</h4>
    <?php if (empty($myListings)): ?>
      <p class="text-muted">You have not posted any listings yet.</p>
    <?php else: ?>
      <div class="list-group">
        <?php foreach ($myListings as $item): ?>
          <div class="list-group-item d-flex align-items-center gap-3 py-2">
            <img src="<?= htmlspecialchars($item['image_url'] ?: '/marketplace/images/placeholder.png') ?>"
                 width="55" height="55" class="rounded"
                 onerror="this.src='/marketplace/images/placeholder.png'"
                 style="object-fit:cover">
            <div class="flex-grow-1">
              <div class="fw-semibold"><?= htmlspecialchars($item['name']) ?></div>
              <small class="text-muted">$<?= number_format($item['price'], 2) ?> &bull; Stock: <?= $item['stock'] ?></small>
            </div>
            <div class="d-flex gap-1">
              <a href="/marketplace/product.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-outline-secondary">View</a>
              <a href="/marketplace/sell.php?delete=<?= $item['id'] ?>"
                 class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Delete this listing?')">Delete</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<script>
const urlInput   = document.getElementById('imageUrlInput');
const preview    = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');
urlInput.addEventListener('input', function () {
  const url = this.value.trim();
  if (/^https?:\/\/.+/i.test(url)) {
    previewImg.src = url;
    preview.style.display = 'block';
    previewImg.onerror = () => { preview.style.display = 'none'; };
    previewImg.onload  = () => { preview.style.display = 'block'; };
  } else {
    preview.style.display = 'none';
  }
});
</script>

<?php require_once 'includes/footer.php'; ?>
