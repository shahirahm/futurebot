<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    // Only students can send hire requests
    header("Location: login.php");
    exit;
}

if (!isset($_GET['email'])) {
    header("Location: mentor_suggestions.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$mentor_email = $_GET['email'];

// Lookup mentor user_id by email
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $mentor_email);
$stmt->execute();
$stmt->bind_result($mentor_id);
if (!$stmt->fetch()) {
    $stmt->close();
    $_SESSION['hire_error'] = "Mentor not found.";
    header("Location: mentor_suggestions.php");
    exit;
}
$stmt->close();

// Check if request already exists
$stmt = $conn->prepare("SELECT COUNT(*) FROM hire_requests WHERE mentor_id = ? AND student_id = ?");
$stmt->bind_param("ii", $mentor_id, $student_id);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    $_SESSION['hire_error'] = "You have already sent a hire request to this mentor.";
    header("Location: mentor_suggestions.php");
    exit;
}

// Insert new hire request
$stmt = $conn->prepare("INSERT INTO hire_requests (mentor_id, student_id, message) VALUES (?, ?, '')");
$stmt->bind_param("ii", $mentor_id, $student_id);
if ($stmt->execute()) {
    $_SESSION['hire_success'] = "Hire request sent successfully.";
} else {
    $_SESSION['hire_error'] = "Failed to send hire request. Try again.";
}
$stmt->close();

header("Location: mentor_suggestions.php");
exit;
?>
