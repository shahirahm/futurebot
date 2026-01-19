<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

// Receiver details
$toEmail = 'receiver@example.com';
$toName = 'Receiver Name';

// Email subject and body
$subject = "Test Email with Tracking";
$body = "
    <p>Hello <strong>$toName</strong>,</p>
    <p>This is a test email with PHPMailer and includes a tracking pixel.</p>
    <p>Best regards,<br>FutureBot</p>
    <img src='http://yourdomain.com/track_open.php?email=" . urlencode($toEmail) . "' width='1' height='1' alt='' />
";

// Create mail instance
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';              // Gmail SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com';    // Your Gmail address
    $mail->Password = 'your-app-password';       // App password from Google (not your Gmail password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Sender and receiver
    $mail->setFrom('your-email@gmail.com', 'FutureBot Admin');
    $mail->addAddress($toEmail, $toName);

    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    // Send it
    $mail->send();
    echo "✅ Email has been sent to $toEmail";
} catch (Exception $e) {
    echo "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
