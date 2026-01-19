<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'samihamaisha231@gmail.com';     // 🔁 Your Gmail
    $mail->Password   = 'wxyi euui fatx hoyb';        // 🔁 Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('your_email@gmail.com', 'Your Name');     // 🔁 Your name
    $mail->addAddress('samihamaisha231@gmail.com', 'Receiver');   // 🔁 Receiver's email

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Order Confirmation';
    $mail->Body    = 'Thank you for your order! We have received it successfully.';

    $mail->send();
    echo '✅ Message has been sent successfully!';
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
