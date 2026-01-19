<?php
session_start();
require_once 'db.php';

$user_email = $_SESSION['user_email'] ?? '';

if (!$user_email) {
    // Not logged in, redirect to login
    header("Location: login.php");
    exit;
}

// Check if form submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $feedback = trim($_POST['feedback'] ?? '');

    if ($course_id <= 0 || empty($feedback)) {
        $_SESSION['feedback_error'] = "Please provide valid feedback.";
        header("Location: enroll_course.php?course_id=" . $course_id);
        exit;
    }

    // Optional: Check if user is actually enrolled in the course (you can add this for security)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_enrollments WHERE user_email = ? AND course_id = ?");
    $stmt->bind_param("si", $user_email, $course_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        $_SESSION['feedback_error'] = "You are not enrolled in this course.";
        header("Location: enroll_course.php?course_id=" . $course_id);
        exit;
    }

    // Insert feedback into database
    $stmt = $conn->prepare("INSERT INTO course_feedback (user_email, course_id, feedback) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $user_email, $course_id, $feedback);

    if ($stmt->execute()) {
        $_SESSION['feedback_success'] = "Thank you for your feedback!";
    } else {
        $_SESSION['feedback_error'] = "Failed to submit feedback. Please try again.";
    }
    $stmt->close();

    // Redirect back to enroll_course page (or anywhere else)
    header("Location: enroll_course.php?course_id=" . $course_id);
    exit;
}

// If accessed without POST, redirect to courses page
header("Location: course_suggestions.php");
exit;
