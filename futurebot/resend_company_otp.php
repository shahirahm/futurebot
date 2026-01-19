<?php
session_start();
require_once 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$msg = '';
$error = '';

// Function to send OTP email
function sendOtpEmail($toEmail, $toName, $otp) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'samihamaisha231@gmail.com'; // Your Gmail
        $mail->Password   = 'ipzz khhd xzil sutm';       // Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('samihamaisha231@gmail.com', 'FutureBot Admin');
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Company Verification';

        $mail->Body = "
            <p>Dear <strong>" . htmlspecialchars($toName) . "</strong>,</p>
            <p>Your new OTP for company registration verification is:</p>
            <h2>$otp</h2>
            <p>This OTP is valid for 10 minutes.</p>
            <p>Thank you,<br>FutureBot Team</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("OTP Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
        $error = "Please enter your email.";
    } else {
        // Check if company exists
        $stmt = $conn->prepare("SELECT name FROM companies WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($companyName);
            $stmt->fetch();

            // Generate new OTP
            $otp = rand(100000, 999999);
            $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            // Update OTP and expiry in DB
            $updateStmt = $conn->prepare("UPDATE companies SET otp = ?, otp_expiry = ? WHERE email = ?");
            $updateStmt->bind_param("sss", $otp, $otp_expiry, $email);
            if ($updateStmt->execute()) {
                $sent = sendOtpEmail($email, $companyName, $otp);
                if ($sent) {
                    $msg = "✅ OTP resent successfully! Check your email.";
                    $_SESSION['verified_company_email'] = $email;
                } else {
                    $error = "Failed to send OTP email. Please try again later.";
                }
            } else {
                $error = "Failed to update OTP in database.";
            }
            $updateStmt->close();
        } else {
            $error = "Email not found. Please register first.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Resend OTP - Company Verification</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 50px; }
        .container { max-width: 400px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px #ccc; }
        input[type="email"] { width: 100%; padding: 10px; margin: 10px 0; }
        button { padding: 10px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        .msg { color: green; margin-top: 10px; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Resend OTP</h2>
    <form method="POST" onsubmit="return disableBtn()">
        <label for="email">Enter your registered email</label>
        <input
            type="email"
            name="email"
            id="email"
            required
            value="<?= htmlspecialchars($_SESSION['verified_company_email'] ?? '') ?>"
        >

        <button type="submit" id="resendBtn">Resend OTP</button>
    </form>

    <?php if ($msg): ?>
        <div class="msg"><?= $msg ?></div>
    <?php elseif ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
</div>

<script>
    function disableBtn() {
        const btn = document.getElementById('resendBtn');
        btn.disabled = true;
        btn.textContent = 'Sending...';
        return true;
    }
</script>
</body>
</html>
