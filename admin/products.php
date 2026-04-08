<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';
requireAdmin();

// Delete product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM cart WHERE product_id = ?")->execute([$id]);
    $pdo->prepare("UPDATE order_items SET product_id = NULL WHERE product_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    header("Location: /marketplace/admin/products.php?deleted=1");
    exit();
}

// Handle edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id        = (int)$_POST['edit_id'];
    $name      = trim($_POST['name']);
    $price     = (float)$_POST['price'];
    $category  = $_POST['category'];
    $condition = $_POST['condition'];
    $stock     = (int)$_POST['stock'];
    $image_url = trim($_POST['image_url']);

    $pdo->prepare("UPDATE products SET name=?, price=?, category=?, `condition`=?, stock=?, image_url=? WHERE id=?")
        ->execute([$name, $price, $category, $condition, $stock, $image_url ?: null, $id]);
    header("Location: /marketplace/admin/products.php?updated=1");
    exit();
}

// Load product for editing
$editProduct = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editProduct = $stmt->fetch();
}

$categories = ['Electronics','Clothing','Books','Music','Collectibles','Other'];
$conditions = ['New','Like New','Good','Fair','Poor'];

$products = $pdo->query("
    SELECT p.*, u.name AS seller_name FROM products p JOIN users u ON p.seller_id = u.id
    ORDER BY p.created_at DESC
")->fetchAll();

require_once '../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Manage Listings</h4>
  <a href="/marketplace/sell.php" class="btn btn-dark btn-sm">+ Add Listing</a>
</div>

<?php if (isset($_GET['deleted'])): ?>
  <div class="alert alert-success">Listing deleted.</div>
<?php endif; ?>
<?php if (isset($_GET['updated'])): ?>
  <div class="alert alert-success">Listing updated.</div>
<?php endif; ?>

<!-- Edit Form (shown when ?edit=ID) -->
<?php if ($editProduct): ?>
  <div class="card mb-4 shadow-sm border-warning">
    <div class="card-header bg-warning text-dark fw-bold">Editing: <?= htmlspecialchars($editProduct['name']) ?></div>
    <div class="card-body">
      <form method="POST">
        <input type="hidden" name="edit_id" value="<?= $editProduct['id'] ?>">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($editProduct['name']) ?>" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control" step="0.01" value="<?= $editProduct['price'] ?>" required>
          </div>
          <div class="col-md-2">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
              <?php foreach ($categories as $c): ?>
                <option value="<?= $c ?>" <?= $editProduct['category'] === $c ? 'selected' : '' ?>><?= $c ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Condition</label>
            <select name="condition" class="form-select">
              <?php foreach ($conditions as $c): ?>
                <option value="<?= $c ?>" <?= $editProduct['condition'] === $c ? 'selected' : '' ?>><?= $c ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Stock</label>
            <input type="number" name="stock" class="form-control" value="<?= $editProduct['stock'] ?>" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Image URL</label>
            <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($editProduct['image_url'] ?? '') ?>">
          </div>
          <div class="col-md-4 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-warning w-100">Save Changes</button>
            <a href="/marketplace/admin/products.php" class="btn btn-outline-secondary w-100">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>

<table class="table table-hover table-bordered">
  <thead class="table-dark">
    <tr><th>#</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Seller</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($products as $p): ?>
      <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= $p['category'] ?></td>
        <td>$<?= number_format($p['price'], 2) ?></td>
        <td><?= $p['stock'] ?></td>
        <td><?= htmlspecialchars($p['seller_name']) ?></td>
        <td>
          <a href="/marketplace/product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">View</a>
          <a href="/marketplace/admin/products.php?edit=<?= $p['id'] ?>" class="btn btn-sm btn-outline-warning">Edit</a>
          <a href="/marketplace/admin/products.php?delete=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger"
             onclick="return confirm('Delete this listing permanently?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require_once '../includes/footer.php'; ?>
