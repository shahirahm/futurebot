<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_email@gmail.com'; // Your Gmail
    $mail->Password   = 'your_app_password';    // Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('your_email@gmail.com', 'Your Name');
    $mail->addAddress('receiver@example.com', 'Receiver Name');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Order Confirmation';
    $mail->Body    = 'Thank you for your order!';

    $mail->send();
    echo 'Message has been sent successfully!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
