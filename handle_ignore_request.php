<?php
session_start();
require_once 'db.php';





if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = intval($_POST['request_id']);

    $stmt = $conn->prepare("DELETE FROM mentor_requests WHERE id = ?");
    $stmt->bind_param("i", $request_id);

    if ($stmt->execute()) {
        // Optionally add a success message
    }

    $stmt->close();
}

$conn->close();
header("Location: mentor_requests.php");
exit;
