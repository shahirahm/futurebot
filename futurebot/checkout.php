<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: register.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$book_id = intval($_GET['book_id'] ?? 0);

if ($book_id <= 0) {
    die("Invalid book.");
}

// Fetch book info
$stmt = $conn->prepare("SELECT title, price FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Book not found.");
}

$book = $result->fetch_assoc();

// PayPal sandbox URL (for testing)
$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";

// Your PayPal Business Email
$paypal_id = "your-paypal-business@example.com"; // 🔁 change to your PayPal email
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout - <?= htmlspecialchars($book['title']) ?></title>
    <style>
      body {
        font-family: Arial, sans-serif;
        padding: 30px;
        background: #f5f9ff;
        color: #1a2e3b;
      }
      h2 {
        color: #0b3d91;
      }
      .payment-options {
        margin-top: 25px;
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
      }
      form, .payment-button {
        background: white;
        padding: 20px 25px;
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(11, 61, 145, 0.15);
        border: 1px solid #a6c8e6;
        flex: 1 1 250px;
        max-width: 300px;
        text-align: center;
      }
      input[type="submit"], .payment-button a {
        background-color: #0a3d62;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        margin-top: 15px;
        transition: background-color 0.3s ease;
      }
      input[type="submit"]:hover, .payment-button a:hover {
        background-color: #14507a;
      }
    </style>
</head>
<body>

<h2>Checkout for: <?= htmlspecialchars($book['title']) ?></h2>
<p>Price: $<?= number_format($book['price'], 2) ?></p>

<div class="payment-options">

  <!-- PayPal Payment -->
  <form action="<?= $paypal_url ?>" method="post">
      <input type="hidden" name="business" value="<?= htmlspecialchars($paypal_id) ?>">
      <input type="hidden" name="cmd" value="_xclick">
      <input type="hidden" name="item_name" value="<?= htmlspecialchars($book['title']) ?>">
      <input type="hidden" name="amount" value="<?= $book['price'] ?>">
      <input type="hidden" name="currency_code" value="USD">
      <input type="hidden" name="return" value="http://localhost/futurebot/payment_success.php?book_id=<?= $book_id ?>">
      <input type="hidden" name="cancel_return" value="http://localhost/futurebot/payment_cancel.php">
      <input type="submit" value="Pay with PayPal">
  </form>

  <!-- bkash Payment -->
  <div class="payment-button">
    <h3>Pay with bkash</h3>
    <p>Use bkash mobile app or USSD</p>
    <a href="bkash_payment.php?book_id=<?= $book_id ?>" target="_blank">Pay with bkash</a>
  </div>

  <!-- Nagad Payment -->
  <div class="payment-button">
    <h3>Pay with Nagad</h3>
    <p>Use Nagad mobile app or USSD</p>
    <a href="nagad_payment.php?book_id=<?= $book_id ?>" target="_blank">Pay with Nagad</a>
  </div>

</div>

</body>
</html>
