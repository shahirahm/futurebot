<?php
require 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$email = trim(strtolower($data['email'] ?? ''));

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please provide an email.']);
    exit;
}

// Generate OTP and expiry
$otp = rand(100000, 999999);
$otp_expiry = date("Y-m-d H:i:s", time() + 300); // 5 minutes

// Insert or update OTP in company_otps table
$stmt = $conn->prepare("REPLACE INTO company_otps (email, otp, otp_expiry) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $otp, $otp_expiry);
$stmt->execute();

// Send OTP via email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'YOUR_EMAIL@gmail.com';
    $mail->Password = 'YOUR_APP_PASSWORD';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('YOUR_EMAIL@gmail.com', 'FutureBot Verification');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Your Company OTP Code';
    $mail->Body = "Your OTP is: <strong>$otp</strong>. It expires in 5 minutes.";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'OTP sent']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send OTP']);
}
?>
