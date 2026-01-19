<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

function sendApprovalEmail($toEmail, $toName, $status) {
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'samihamaisha231@gmail.com'; // Your Gmail
        $mail->Password   = 'ipzz khhd xzil sutm';       // Your App Password (change now!)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // From and to
        $mail->setFrom('samihamaisha231@gmail.com', 'FutureBot Admin');
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Company Approval Status';

        $statusText = ($status === 'approved') ? '✅ Approved' : '❌ Rejected';
        $trackingURL = "https://yourdomain.com/track_open.php?email=" . urlencode($toEmail);

        $mail->Body = "
            <h3>Hello $toName,</h3>
            <p>Your company registration has been <strong>$statusText</strong>.</p>
            <p>Thank you for using FutureBot.</p>
            <img src='$trackingURL' width='1' height='1' alt=''>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
