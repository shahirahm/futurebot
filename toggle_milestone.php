<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email']) || !isset($_POST['skill'], $_POST['milestone_index'])) {
    http_response_code(400);
    exit;
}

$user_email = $_SESSION['user_email'];
$skill = $_POST['skill'];
$milestone_index = (int)$_POST['milestone_index'];

// Check if exists
$stmt = $conn->prepare("SELECT 1 FROM user_milestones WHERE user_email = ? AND skill = ? AND milestone_index = ?");
$stmt->bind_param("ssi", $user_email, $skill, $milestone_index);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Delete (uncheck)
    $stmt = $conn->prepare("DELETE FROM user_milestones WHERE user_email = ? AND skill = ? AND milestone_index = ?");
    $stmt->bind_param("ssi", $user_email, $skill, $milestone_index);
} else {
    // Insert (check)
    $stmt = $conn->prepare("INSERT INTO user_milestones (user_email, skill, milestone_index) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $user_email, $skill, $milestone_index);
}
$stmt->execute();
