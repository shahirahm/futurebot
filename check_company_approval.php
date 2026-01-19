<?php
require 'db.php';

// Set timezone
date_default_timezone_set('Asia/Dhaka');

header('Content-Type: application/json');

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');

if (!$email) {
    echo json_encode(['approved' => false, 'hasOtp' => false, 'message' => 'Email is required']);
    exit;
}

// Check if company exists and is approved
$stmt = $conn->prepare("SELECT status, otp, otp_expires_at FROM companies WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    echo json_encode(['approved' => false, 'hasOtp' => false, 'message' => 'Company not found']);
    exit;
}

$stmt->bind_result($status, $otp, $otp_expires_at);
$stmt->fetch();

$isApproved = ($status === 'approved');
$hasValidOtp = false;

if ($isApproved && $otp && $otp_expires_at) {
    // Check if OTP is not expired
    $current_time = date('Y-m-d H:i:s');
    $hasValidOtp = ($current_time <= $otp_expires_at);
}

echo json_encode([
    'approved' => $isApproved,
    'hasOtp' => $hasValidOtp,
    'status' => $status,
    'message' => $isApproved ? 'Company is approved' : 'Company is not approved yet'
]);
?> 