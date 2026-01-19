<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['webinar_id'])) {
    $user_email = $_SESSION['email'];
    $webinar_id = intval($_POST['webinar_id']);
    
    // Check if already registered
    $check_sql = "SELECT * FROM User_Webinars WHERE user_email = ? AND webinar_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $user_email, $webinar_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0) {
        $sql = "INSERT INTO User_Webinars (user_email, webinar_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $user_email, $webinar_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Successfully registered for the webinar!";
        } else {
            $_SESSION['error'] = "Failed to register for the webinar. Please try again.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "You are already registered for this webinar.";
    }
    $check_stmt->close();
}

header("Location: dashboard.php");
exit;
?>