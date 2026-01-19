<?php
session_start();
require 'db.php';  // Make sure db.php defines $conn

if (!isset($_SESSION['user_email'])) {
    header("Location: register.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$book_id = intval($_GET['book_id'] ?? 0);
$method = $_GET['method'] ?? 'paypal';
$transaction_id = $_GET['transaction_id'] ?? 'N/A';  // or from POST if sent
$amount = 0;

// Validate book_id
if ($book_id <= 0) {
    die("Invalid book.");
}

// Fetch book price and title
$stmt = $conn->prepare("SELECT title, price FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Book not found.");
}
$book = $result->fetch_assoc();
$amount = $book['price'];

// Insert order to DB
$insert_stmt = $conn->prepare("INSERT INTO orders (user_email, book_id, payment_method, transaction_id, amount, status, created_at) VALUES (?, ?, ?, ?, ?, 'completed', NOW())");
$insert_stmt->bind_param("sissd", $user_email, $book_id, $method, $transaction_id, $amount);
$insert_stmt->execute();

// Get inserted order id (optional)
$order_id = $insert_stmt->insert_id;

// Show success info
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment Success</title>
    <style>
        body { font-family: Arial; background: #dff0d8; color: #3c763d; padding: 30px; }
        h2 { color: #3c763d; }
        a { color: #2e6da4; }
    </style>
</head>
<body>
    <h2>Payment Successful!</h2>
    <p>Thank you for purchasing <strong><?= htmlspecialchars($book['title']) ?></strong>.</p>
    <p>Payment Method: <strong><?= htmlspecialchars(ucfirst($method)) ?></strong></p>
    <p>Transaction ID: <strong><?= htmlspecialchars($transaction_id) ?></strong></p>
    <p>Amount Paid: <strong>$<?= number_format($amount, 2) ?></strong></p>
    <p><a href="download.php?book_id=<?= $book_id ?>">Click here to download your book</a></p>
</body>
</html>
