<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: register.php");
    exit;
}

$book_id = intval($_GET['book_id'] ?? 0);
if ($book_id <= 0) {
    die("Invalid book.");
}

// Fetch book info for display
$stmt = $conn->prepare("SELECT title, price FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Book not found.");
}
$book = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get transaction ID from user input
    $trx_id = trim($_POST['trx_id'] ?? '');

    if (empty($trx_id)) {
        $error = "Transaction ID is required.";
    } else {
        // Redirect to payment_success.php with all info in query string
        header("Location: payment_success.php?book_id=$book_id&method=bkash&txn_id=" . urlencode($trx_id));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pay with bkash - <?= htmlspecialchars($book['title']) ?></title>
    <style>
      body { font-family: Arial; padding: 20px; background: #fef9f0; color: #444; }
      h2 { color: #d35400; }
      form { margin-top: 20px; }
      button { padding: 10px 18px; background: #e67e22; border: none; color: white; font-weight: bold; border-radius: 6px; cursor: pointer; }
      button:hover { background: #d35400; }
      .instructions { background: #fff3e0; padding: 15px; border-radius: 8px; border: 1px solid #f0c27b; }
      .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Pay with bkash</h2>
    <p>Book: <strong><?= htmlspecialchars($book['title']) ?></strong></p>
    <p>Amount: <strong>$<?= number_format($book['price'], 2) ?></strong></p>

    <div class="instructions">
        <p><strong>Instructions:</strong></p>
        <ul>
            <li>Open your bkash app or dial *247#.</li>
            <li>Send payment of <strong><?= number_format($book['price'], 2) ?> USD</strong> to merchant number <strong>01738915382</strong> (example).</li>
            <li>Enter your transaction ID below and click "Confirm Payment".</li>
        </ul>
    </div>

    <form method="POST">
        <label for="trx_id">Transaction ID:</label><br />
        <input type="text" id="trx_id" name="trx_id" required style="padding:6px; margin-top:4px; width: 300px;" placeholder="Enter bkash transaction ID" /><br /><br />
        <button type="submit">Confirm Payment</button>
    </form>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</body>
</html>
