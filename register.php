<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) { header("Location: /marketplace/index.php"); exit(); }

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $conf  = $_POST['confirm_password'] ?? '';

    if (empty($name))  $errors[] = "Name is required.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (strlen($pass) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($pass !== $conf) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "That email is already registered.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            $success = "Account created! <a href='/marketplace/login.php'>Login here</a>.";
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-5">
    <h2 class="mb-4">Create an Account</h2>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
      </div>
    <?php endif; ?>

    <form method="POST" id="registerForm" novalidate>
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" minlength="6" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-dark w-100">Register</button>
    </form>
    <p class="mt-3 text-center">Already have an account? <a href="/marketplace/login.php">Login</a></p>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
