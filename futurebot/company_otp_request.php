<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = trim(strtolower($data['email'] ?? ''));
$otp = trim($data['otp'] ?? '');

if (!$email || !$otp) {
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
    exit;
}

// Verify from company_otps table
$stmt = $conn->prepare("SELECT otp, otp_expiry FROM company_otps WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'Email not found or OTP not requested']);
    exit;
}

$stmt->bind_result($storedOtp, $otpExpiry);
$stmt->fetch();

if ($storedOtp !== $otp) {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
    exit;
}

if (strtotime($otpExpiry) < time()) {
    echo json_encode(['success' => false, 'message' => 'OTP expired']);
    exit;
}

// OTP is valid
$_SESSION['verified_company_email'] = $email;
echo json_encode(['success' => true, 'message' => 'OTP verified']);
