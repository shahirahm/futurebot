<?php
require_once 'db.php';

$admin_email = 'admin@futurebot.com'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Admin Panel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="admin_panel.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="admin_transactions.php">View Transactions</a>
        </li>
        
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <span class="navbar-text text-white me-3">
            <?= htmlspecialchars($admin_email) ?>
          </span>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-outline-light btn-sm" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h1>Welcome to Admin Panel</h1>
    <p>Use the navbar to navigate through admin functions.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
