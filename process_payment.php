<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Get POST data
$course_name = $_POST['course_name'] ?? '';
$payment_amount = floatval($_POST['payment_amount'] ?? 0);
$payment_type = $_POST['payment_type'] ?? 'full';  // 'full' or 'installment'

// First get total price & calculate remaining amount
$stmt = $conn->prepare("SELECT price FROM courses WHERE title = ?");
$stmt->bind_param("s", $course_name);
$stmt->execute();
$stmt->bind_result($total_price);
$stmt->fetch();
$stmt->close();

if (!$total_price) {
    die("Invalid course.");
}

// Get sum of previous payments for this course & user
$stmt = $conn->prepare("SELECT IFNULL(SUM(amount_paid),0) FROM course_payments WHERE user_email = ? AND course_name = ?");
$stmt->bind_param("ss", $user_email, $course_name);
$stmt->execute();
$stmt->bind_result($total_paid);
$stmt->fetch();
$stmt->close();

$remaining_amount = $total_price - $total_paid - $payment_amount;
if ($remaining_amount < 0) $remaining_amount = 0;

// Insert this payment record
$stmt = $conn->prepare("
    INSERT INTO course_payments (user_email, course_name, amount_paid, payment_type, total_price, remaining_amount)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssdidd", $user_email, $course_name, $payment_amount, $payment_type, $total_price, $remaining_amount);
$stmt->execute();
$stmt->close();

header("Location: transactions.php?course=" . urlencode($course_name));
exit;
?>
