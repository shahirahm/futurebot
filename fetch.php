<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}
$userId = $_SESSION['user_id'];

if (!isset($_GET['mentor_id']) || !is_numeric($_GET['mentor_id'])) {
    http_response_code(400);
    exit;
}
$mentorId = intval($_GET['mentor_id']);

$stmt = $conn->prepare("SELECT sender_id, message, timestamp FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC");
$stmt->bind_param("iiii", $userId, $mentorId, $mentorId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

header('Content-Type: application/json');
echo json_encode($messages);
