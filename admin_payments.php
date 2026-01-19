<?php
session_start();
require_once 'db.php';

// Example admin check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("Access denied");
}

$stmt = $conn->prepare("
    SELECT user_email, course_name, SUM(amount_paid) AS total_paid, MAX(payment_date) AS last_payment_date,
           MAX(total_price) AS total_price, MIN(remaining_amount) AS remaining_amount
    FROM course_payments
    GROUP BY user_email, course_name
    ORDER BY last_payment_date DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Course Payments Overview</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 0 10px #ccc; }
        th, td { padding: 12px 15px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #007bff; color: #fff; }
        tr:nth-child(even) { background-color: #f3f6ff; }
    </style>
</head>
<body>
    <h2>Admin Course Payments Overview</h2>
    <table>
        <thead>
            <tr>
                <th>User Email</th>
                <th>Course Name</th>
                <th>Total Paid</th>
                <th>Total Price</th>
                <th>Remaining Amount</th>
                <th>Last Payment Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['user_email']) ?></td>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td>$<?= number_format($row['total_paid'], 2) ?></td>
                <td>$<?= number_format($row['total_price'], 2) ?></td>
                <td>$<?= number_format($row['remaining_amount'], 2) ?></td>
                <td><?= $row['last_payment_date'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
