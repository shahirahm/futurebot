<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
$mail->Host = 'smtp.example.com';
$mail->SMTPAuth = true;
$mail->Username = 'samihamaisha231@gmail.com';
$mail->Password = 'ipzz khhd xzil sutm';
$mail->SMTPSecure = 'tls'; // or 'ssl'
$mail->Port = 587; // or 465

    // From / To
    $mail->setFrom('samihamaisha231@gmail.com', 'maisha');
    $mail->addAddress('RECEIVER_EMAIL@gmail.com', 'Receiver Name');

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer Test Email';
    $mail->Body = '<h3>This is a test email sent from PHPMailer using Gmail SMTP.</h3>';

    // Debug output (optional)
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

    $mail->send();
    echo 'Email has been sent successfully.';
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
