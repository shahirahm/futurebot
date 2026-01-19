<?php
// minimal_test.php

// DB connection details (adjust as needed)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "futurebot";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Test user email
$user_email = "test@example.com"; // replace with an existing email in your course_payments table

$sql = "
    SELECT c.title, cp.amount_paid, cp.payment_type, cp.total_price, cp.remaining_amount, cp.payment_date 
    FROM course_payments cp
    JOIN courses c ON c.title = cp.course_name
    WHERE cp.user_email = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No courses found for user: $user_email";
} else {
    while ($row = $result->fetch_assoc()) {
        echo "Course: " . htmlspecialchars($row['title']) . "<br>";
        echo "Paid: " . $row['amount_paid'] . "<br>";
        echo "Payment Type: " . htmlspecialchars($row['payment_type']) . "<br>";
        echo "Total Price: " . $row['total_price'] . "<br>";
        echo "Remaining Amount: " . $row['remaining_amount'] . "<br>";
        echo "Date: " . $row['payment_date'] . "<br><hr>";
    }
}

$stmt->close();
$conn->close();
