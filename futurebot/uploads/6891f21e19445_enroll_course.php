<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email']) || !isset($_GET['course'])) {
    header("Location: login.php");
    exit;
}

$course = $_GET['course'];
$user_email = $_SESSION['user_email'];

$course_prices = [
    "Intro to Python Programming" => 100,
    "Advanced Python Techniques" => 150,
    "Java Basics" => 120,
    "Web Development Bootcamp" => 200,
    "SQL for Beginners" => 110,
    "JavaScript Essentials" => 130,
    "React for Beginners" => 140,
    "Data Science Essentials" => 160,
    "Machine Learning Basics" => 180
];

$total_price = $course_prices[$course] ?? 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_type = $_POST['payment_type'];
    $payment_method = $_POST['payment_method'];
    $amount_paid = floatval($_POST['amount_paid']);
    $sender_info = $_POST['sender_info'];
    $transaction_id = $_POST['transaction_id'];
    $remaining = ($payment_type === "Installment") ? $total_price - $amount_paid : 0;

    $stmt = $conn->prepare("INSERT INTO course_payments (user_email, course_name, amount_paid, payment_type, total_price, remaining_amount, payment_method, sender_info, transaction_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssdsss", $user_email, $course, $amount_paid, $payment_type, $total_price, $remaining, $payment_method, $sender_info, $transaction_id);

    if ($stmt->execute()) {
        echo "<script>alert('Enrollment and payment successful!');window.location='my_courses.php';</script>";
    } else {
        echo "<script>alert('Error saving payment.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enroll in <?= htmlspecialchars($course) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h3>Enroll in <?= htmlspecialchars($course) ?></h3>
    <p>Total Course Price: <strong>$<?= $total_price ?></strong></p>
    <form method="POST">
        <div class="mb-3">
            <label>Payment Type:</label><br>
            <input type="radio" name="payment_type" value="Full" required onchange="handlePayment(this)"> Full<br>
            <input type="radio" name="payment_type" value="Installment" onchange="handlePayment(this)"> Installment
        </div>

        <div class="mb-3">
            <label for="payment_method">Select Payment Method:</label>
            <select name="payment_method" id="payment_method" class="form-control" required onchange="toggleTransactionFields(this.value)">
                <option value="">-- Choose --</option>
                <option value="Bkash">Bkash</option>
                <option value="Rocket">Rocket</option>
                <option value="Nagad">Nagad</option>
                <option value="Bank Card">Bank Card</option>
                <option value="Cash">Cash</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Amount Paying Now ($):</label>
            <input type="number" name="amount_paid" class="form-control" step="0.01" min="1" required>
        </div>

        <div class="mb-3" id="sender_info_group" style="display:none;">
            <label id="sender_info_label">Sender Number / Card Info:</label>
            <input type="text" name="sender_info" class="form-control">
        </div>

        <div class="mb-3" id="transaction_id_group" style="display:none;">
            <label>Transaction ID:</label>
            <input type="text" name="transaction_id" class="form-control">
        </div>

        <button class="btn btn-primary">Enroll & Pay</button>
    </form>
</div>

<script>
function handlePayment(radio) {
    const amountInput = document.querySelector('[name="amount_paid"]');
    if (radio.value === "Full") {
        amountInput.value = <?= $total_price ?>;
        amountInput.readOnly = true;
    } else {
        amountInput.value = "";
        amountInput.readOnly = false;
    }
}

function toggleTransactionFields(method) {
    const senderInfoGroup = document.getElementById("sender_info_group");
    const transactionIdGroup = document.getElementById("transaction_id_group");
    const senderLabel = document.getElementById("sender_info_label");

    if (method === "Cash") {
        senderInfoGroup.style.display = "none";
        transactionIdGroup.style.display = "none";
    } else {
        senderInfoGroup.style.display = "block";
        transactionIdGroup.style.display = "block";
        senderLabel.innerText = method === "Bank Card" ? "Card Number:" : "Sender Number:";
    }
}
</script>
</body>
</html>
