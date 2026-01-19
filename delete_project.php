<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: register.php");
    exit;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $project_id = intval($_GET['id']);
    $email = $_SESSION['user_email'];

    // Check ownership first
    $stmt = $conn->prepare("SELECT image_path FROM student_projects WHERE id = ? AND user_email = ?");
    $stmt->bind_param("is", $project_id, $email);
    $stmt->execute();
    $stmt->bind_result($image_path);
    if ($stmt->fetch()) {
        $stmt->close();

        // Delete image file if exists
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path);
        }

        // Delete project
        $stmt = $conn->prepare("DELETE FROM student_projects WHERE id = ? AND user_email = ?");
        $stmt->bind_param("is", $project_id, $email);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt->close();
    }
}

header("Location: career_suggestions.php");
exit;
