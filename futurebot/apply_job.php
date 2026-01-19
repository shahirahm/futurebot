<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'student') {
    header("Location: home.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_post_id = intval($_POST['job_post_id'] ?? 0);
    $student_email = $_SESSION['user_email'];

    if ($job_post_id <= 0) {
        die("Invalid job post.");
    }

    // Check if already applied
    $check = $conn->prepare("SELECT id FROM applications WHERE job_post_id = ? AND student_email = ?");
    $check->bind_param("is", $job_post_id, $student_email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        die("You already applied for this post.");
    }

    $check->close();

    $stmt = $conn->prepare("INSERT INTO applications (job_post_id, student_email, status, applied_at) VALUES (?, ?, 'applied', NOW())");
    $stmt->bind_param("is", $job_post_id, $student_email);
    $stmt->execute();

    header("Location: home.php?message=applied_success");
    exit;
}
?>
