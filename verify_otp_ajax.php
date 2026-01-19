<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$otp = $data['otp'] ?? '';

if (!$email || !$otp) {
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
    exit;
}

$stmt = $conn->prepare("SELECT otp, otp_expires_at FROM companies WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($storedOtp, $otpExpiresAt);
    $stmt->fetch();
    $stmt->close();

    if ($storedOtp === $otp) {
        if (strtotime($otpExpiresAt) >= time()) {
            // OTP valid - update status to 'verified' or 'active'
            $update = $conn->prepare("UPDATE companies SET status = 'verified', otp = NULL, otp_expires_at = NULL WHERE email = ?");
            $update->bind_param("s", $email);
            $update->execute();
            $update->close();

            $_SESSION['verified_company_email'] = $email;

            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'OTP expired. Please request a new one.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Company email not found.']);
    exit;
}
?>
