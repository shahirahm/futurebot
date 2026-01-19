<?php
session_start();
require 'db.php';
require 'send_otp_mail.php'; // your PHPMailer OTP sending function

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email required']);
    exit;
}

// Generate 6-digit OTP and expiry 10 minutes from now
$otp = rand(100000, 999999);
$otpExpiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

// Update OTP & expiry in DB
$stmt = $conn->prepare("UPDATE companies SET otp = ?, otp_expiry = ? WHERE email = ?");
$stmt->bind_param("sss", $otp, $otpExpiry, $email);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Email not registered']);
    exit;
}

// Send OTP email with PHPMailer
$mailSent = sendOTPEmail($email, $otp);

if ($mailSent) {
    echo json_encode(['success' => true, 'message' => 'OTP sent to your email']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP email']);
}
