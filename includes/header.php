<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SecondHand Marketplace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="/marketplace/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/marketplace/index.php"><i class="bi bi-shop"></i> SecondHand</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/marketplace/products.php">Browse</a></li>
        <?php if (isLoggedIn()): ?>
          <li class="nav-item"><a class="nav-link" href="/marketplace/sell.php">Sell an Item</a></li>
          <?php if (isAdmin()): ?>
            <li class="nav-item"><a class="nav-link text-warning" href="/marketplace/admin/index.php"><i class="bi bi-shield-lock"></i> Admin</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (isLoggedIn()): ?>
          <li class="nav-item"><a class="nav-link" href="/marketplace/cart.php"><i class="bi bi-cart3"></i> Cart</a></li>
          <li class="nav-item"><a class="nav-link" href="/marketplace/orders.php"><i class="bi bi-bag"></i> Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="/marketplace/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/marketplace/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link btn btn-outline-light btn-sm px-3 ms-2" href="/marketplace/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container my-4">
