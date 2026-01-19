<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $job_title = $_POST['job_title'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $skills = $_POST['skills'];
    $deadline = $_POST['deadline'];

    $stmt = $conn->prepare("INSERT INTO job_posts (user_id, job_title, description, location, skills, deadline) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $job_title, $description, $location, $skills, $deadline);

    if ($stmt->execute()) {
        header("Location: mentor_suggestions.php?message=Job+Posted+Successfully");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
