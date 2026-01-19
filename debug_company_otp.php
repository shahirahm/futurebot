<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$otp = trim($data['otp'] ?? '');

if (!$email || !$otp) {
    echo json_encode(['success' => false, 'message' => 'Email and OTP are required']);
    exit;
}

$sql = "
    SELECT id, otp, otp_expires_at, status 
    FROM companies 
    WHERE email = ?
    ORDER BY id DESC
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Company not found']);
    exit;
}

$row = $result->fetch_assoc();

$now = time();
$otpExpiryTime = strtotime($row['otp_expires_at']);

$response = [
    'success' => false,
    'message' => '',
    'debug' => [
        'db_otp' => $row['otp'],
        'input_otp' => $otp,
        'otp_expires_at' => $row['otp_expires_at'],
        'otp_expired' => ($otpExpiryTime < $now),
        'status' => $row['status'],
        'current_time' => date('Y-m-d H:i:s', $now),
    ]
];

// Check OTP
if ($row['otp'] !== $otp) {
    $response['message'] = 'Invalid OTP';
    echo json_encode($response);
    exit;
}

// Check expiry
if ($otpExpiryTime < $now) {
    $response['message'] = 'OTP expired';
    echo json_encode($response);
    exit;
}

// Check approval status
if ($row['status'] !== 'approved') {
    $response['message'] = 'Company not approved yet';
    echo json_encode($response);
    exit;
}

$_SESSION['verified_company_email'] = $email;
$response['success'] = true;
$response['message'] = 'OTP verified successfully!';

echo json_encode($response);
