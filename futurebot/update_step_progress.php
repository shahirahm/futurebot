<?php
session_start();

$course_id = $_POST['course_id'] ?? null;
$step_index = $_POST['step_index'] ?? null;
$checked = $_POST['checked'] ?? null;

if ($course_id === null || $step_index === null || $checked === null) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

$step_key = "course_steps_" . $course_id;

if (!isset($_SESSION[$step_key])) {
    $_SESSION[$step_key] = array_fill(0, 4, false); // Assuming 4 steps
}

$_SESSION[$step_key][$step_index] = ($checked == 1);

// Calculate new percentage
$total = count($_SESSION[$step_key]);
$completed = count(array_filter($_SESSION[$step_key]));
$percentage = intval(($completed / $total) * 100);

echo json_encode([
    'success' => true,
    'percentage' => $percentage
]);
