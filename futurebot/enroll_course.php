<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'])) {
    $user_email = $_SESSION['email'];
    $course_id = intval($_POST['course_id']);
    
    // Check if already enrolled
    $check_sql = "SELECT * FROM User_Courses WHERE user_email = ? AND course_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $user_email, $course_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $sql = "INSERT INTO User_Courses (user_email, course_id, progress) VALUES (?, ?, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $user_email, $course_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Successfully enrolled in the course!";
        } else {
            $_SESSION['error'] = "Failed to enroll in the course. Please try again.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "You are already enrolled in this course.";
    }
    $check_stmt->close();
}

header("Location: dashboard.php");
exit;
?>