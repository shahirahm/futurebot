<?php
session_start();
require 'db.php';

// Make sure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You do not have permission to delete this post.");
}

// Check if hire_post_id is set
if (!isset($_POST['hiring_id'])) {
    die("Invalid request. No post ID provided.");
}

$hire_post_id = intval($_POST['hiring_id']);



// Delete the post
$sql = "DELETE FROM job_posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hire_post_id);

if ($stmt->execute()) {
    // Redirect back to home with success message
    header("Location: home.php?msg=Post+removed+successfully");
    exit;
} else {
    die("Failed to remove post: " . $conn->error);
}
?>
