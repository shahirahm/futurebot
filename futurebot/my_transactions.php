<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Prepare statement to fetch all transactions of the logged-in user, ordered by latest payment
$stmt = $conn->prepare("
    SELECT course_name, amount_paid, payment_type, total_price, remaining_amount, payment_date, payment_method, sender_info, transaction_id
    FROM course_payments
    WHERE user_email = ?
    ORDER BY payment_date DESC
");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Payment Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 70px; /* for navbar */
        }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#">FutureBot</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="btn btn-outline-light" href="index.php">Home</a>
        </li>
        <li class="nav-item ms-2">
          <a class="btn btn-outline-light" href="enroll.php">Enroll</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-3">
    <h2>My Payment Transactions</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Course Name</th>
                    <th>Amount Paid ($)</th>
                    <th>Payment Type</th>
                    <th>Total Price ($)</th>
                    <th>Remaining Amount ($)</th>
                    <th>Payment Date</th>
                    <th>Payment Method</th>
                    <th>Sender Info</th>
                    <th>Transaction ID</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= number_format($row['amount_paid'], 2) ?></td>
                    <td><?= htmlspecialchars($row['payment_type']) ?></td>
                    <td><?= number_format($row['total_price'], 2) ?></td>
                    <td><?= number_format($row['remaining_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['payment_date']) ?></td>
                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                    <td><?= htmlspecialchars($row['sender_info']) ?></td>
                    <td><?= htmlspecialchars($row['transaction_id']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info mt-4">No payment transactions found.</div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
