<?php
session_start();
require 'db.php';

// Set timezone
date_default_timezone_set('Asia/Dhaka');

header('Content-Type: application/json');

// Get JSON input (email and otp)
$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$otp = trim($data['otp'] ?? '');

if (!$email || !$otp) {
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
    exit;
}

// 1. Find company by email and check if approved
$stmtCompany = $conn->prepare("SELECT id, name, status, otp, otp_expires_at FROM companies WHERE email = ?");
$stmtCompany->bind_param("s", $email);
$stmtCompany->execute();
$stmtCompany->store_result();

if ($stmtCompany->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'Company not found']);
    exit;
}

$stmtCompany->bind_result($company_id, $company_name, $status, $stored_otp, $otp_expires_at);
$stmtCompany->fetch();

// 2. Check if company is approved
if ($status !== 'approved') {
    echo json_encode(['success' => false, 'message' => 'Company registration is not approved yet']);
    exit;
}

// 3. Check if OTP exists and is not expired
if (!$stored_otp || !$otp_expires_at) {
    echo json_encode(['success' => false, 'message' => 'No valid OTP found. Please contact admin.']);
    exit;
}

// 4. Check if OTP is expired
$current_time = date('Y-m-d H:i:s');
if ($current_time > $otp_expires_at) {
    echo json_encode(['success' => false, 'message' => 'OTP has expired. Please contact admin for a new OTP.']);
    exit;
}

// 5. Check if OTP matches
if ($stored_otp !== $otp) {
    echo json_encode(['success' => false, 'message' => 'Invalid OTP']);
    exit;
}

// 6. Clear OTP after successful verification
$updateOtp = $conn->prepare("UPDATE companies SET otp = NULL, otp_expires_at = NULL WHERE id = ?");
$updateOtp->bind_param("i", $company_id);
$updateOtp->execute();

// 7. Set session for verified company
$_SESSION['verified_company_email'] = $email;
$_SESSION['verified_company_id'] = $company_id;
$_SESSION['verified_company_name'] = $company_name;

// 8. Success response
echo json_encode(['success' => true, 'message' => 'OTP verified successfully']);
exit;
?>
