<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) { header("Location: /marketplace/index.php"); exit(); }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if (empty($email) || empty($pass)) {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['name']     = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header("Location: /marketplace/index.php");
            exit();
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <h2 class="mb-4">Login</h2>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><p class="mb-0"><?= htmlspecialchars($e) ?></p><?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-dark w-100">Login</button>
    </form>
    <p class="mt-3 text-center">No account? <a href="/marketplace/register.php">Register here</a></p>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
