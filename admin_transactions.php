<?php
session_start();

// Admin authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Fetch transactions data - example table name: course_payments
$result = $conn->query("SELECT * FROM course_payments ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Transactions Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<!-- Admin Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <a class="navbar-brand" href="admin_panel.php">FutureBot Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin_company_approvals.php">Company Approvals</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="admin_transactions.php">Transactions</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="admin_logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="card shadow p-4">
        <h2 class="mb-4 text-center">💰 Transactions</h2>

        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info text-center">No transactions found.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User Email</th>
                        <th>Course Name</th>
                        <th>Amount Paid</th>
                        <th>Payment Type</th>
                        <th>Payment Method</th>
                        <th>Total Price</th>
                        <th>Remaining Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['user_email']) ?></td>
                        <td><?= htmlspecialchars($row['course_name']) ?></td>
                        <td>$<?= number_format($row['amount_paid'], 2) ?></td>
                        <td><?= htmlspecialchars($row['payment_type']) ?></td>
                        <td><?= htmlspecialchars($row['payment_method']) ?></td>
                        <td>$<?= number_format($row['total_price'], 2) ?></td>
                        <td>$<?= number_format($row['remaining_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
