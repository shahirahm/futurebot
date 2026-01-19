<?php
session_start();
require_once 'db.php'; // Make sure db.php connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mentor_email = $_POST['mentor_email'];
    $student_name = $_POST['student_name'];
    $location = $_POST['location'];
    $institute = $_POST['institute'];
    $subject = $_POST['subject'];
    $contact = $_POST['contact'];

    $stmt = $conn->prepare("INSERT INTO mentor_requests (mentor_email, student_name, location, institute, subject, contact) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $mentor_email, $student_name, $location, $institute, $subject, $contact);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Send Successful!";
        header("Location: send_request.php?mentor_email=" . urlencode($mentor_email));
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
