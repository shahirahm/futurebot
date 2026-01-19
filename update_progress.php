<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_email'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_email = $_SESSION['user_email'];
$skill = $_POST['skill'] ?? '';
$milestone_index = intval($_POST['milestone_index'] ?? 0);

if (!$skill || $milestone_index <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// Normalize skill case to match keys
$skill = ucfirst(strtolower($skill));

// Insert or update progress (upsert)
$stmt = $conn->prepare("
    INSERT INTO user_skill_progress (user_email, skill, milestone_index, completed, completed_at)
    VALUES (?, ?, ?, 1, NOW())
    ON DUPLICATE KEY UPDATE completed = 1, completed_at = NOW()
");

$stmt->bind_param("ssi", $user_email, $skill, $milestone_index);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update progress']);
}

$stmt->close();
$conn->close();
?>
