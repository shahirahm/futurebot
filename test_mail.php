<?php
require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'samihamaisha231@gmail.com';
$mail->Password = 'your_app_password_here';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

$mail->setFrom('samihamaisha231@gmail.com', 'Test Bot');
$mail->addAddress('your_email@example.com');
$mail->Subject = 'SMTP Test';
$mail->Body = 'If you see this, SMTP is working!';
$mail->send();
echo "Sent!";
