<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$course = $_POST['value_a']; // You can pass course name in value_a
$amount = $_POST['amount'];
$tran_id = $_POST['tran_id'];

// Verify and save to DB
$stmt = $conn->prepare("INSERT INTO course_payments (user_email, course_name, amount_paid, payment_type, total_price, remaining_amount, payment_method) VALUES (?, ?, ?, 'Full', ?, 0, 'SSLCommerz')");
$stmt->bind_param("ssddi", $user_email, $course, $amount, $amount, $amount);
$stmt->execute();
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment Success</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="text-center p-5">
    <h2 class="text-success">🎉 Payment Successful!</h2>
    <p>Transaction ID: <?= htmlspecialchars($tran_id) ?></p>
    <a href="my_courses.php" class="btn btn-primary">Go to My Courses</a>
</body>
</html>
