<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
    $project_id = intval($_POST['project_id']); // Sanitize input

    // Optional: fetch the project first to delete its uploaded file
    $stmt = $conn->prepare("SELECT image_path FROM student_projects WHERE id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    if ($stmt->fetch() && !empty($image_path) && file_exists($image_path)) {
        unlink($image_path); // Delete the file
    }
    $stmt->close();

    // Delete the project record
    $del_stmt = $conn->prepare("DELETE FROM student_projects WHERE id = ?");
    $del_stmt->bind_param("i", $project_id);
    if ($del_stmt->execute()) {
        $del_stmt->close();
        header("Location: home.php"); // Redirect back to homepage
        exit;
    } else {
        die("Error deleting project: " . $conn->error);
    }
} else {
    die("Invalid request.");
}
?>
