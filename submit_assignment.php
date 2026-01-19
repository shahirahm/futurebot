<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$course_id = $_POST['course_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['assignment_file']) || $_FILES['assignment_file']['error'] !== UPLOAD_ERR_OK) {
        echo "Error uploading file. Please try again.";
        exit;
    }

    // Sanitize file name
    $fileName = basename($_FILES['assignment_file']['name']);
    $fileTmpPath = $_FILES['assignment_file']['tmp_name'];

    // Define upload directory - create if it doesn't exist
    $uploadDir = __DIR__ . "/uploads/assignments/{$user_email}/course_{$course_id}/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $destination = $uploadDir . $fileName;

    if (move_uploaded_file($fileTmpPath, $destination)) {
        echo "<h2>Assignment Submitted Successfully!</h2>";
        echo "<p>Your file has been uploaded.</p>";
        echo '<p><a href="javascript:window.close();">Close this window</a></p>';
    } else {
        echo "Failed to move uploaded file.";
    }
} else {
    echo "Invalid request method.";
}
