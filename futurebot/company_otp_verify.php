<?php
require 'db.php';

$msg = '';

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $otp = trim($_POST['otp']);

    // Validate input
    if (empty($email) || empty($otp)) {
        $msg = "<div class='alert alert-danger'>Please enter both email and OTP.</div>";
    } else {
        // Fetch the company OTP and expiry
        $stmt = $conn->prepare("SELECT otp, otp_expires_at, status FROM companies WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($dbOtp, $otpExpiresAt, $status);
        if ($stmt->fetch()) {
            $stmt->close();

            if ($status !== 'approved') {
                $msg = "<div class='alert alert-warning'>Your account is not approved yet.</div>";
            } else if ($dbOtp === null) {
                $msg = "<div class='alert alert-warning'>OTP already verified or not set.</div>";
            } else {
                $now = date('Y-m-d H:i:s');
                if ($now > $otpExpiresAt) {
                    $msg = "<div class='alert alert-danger'>OTP expired. Please contact admin.</div>";
                } else if ($otp === $dbOtp) {
                    // OTP matches — mark verified by clearing OTP and optionally set a flag
                    $update = $conn->prepare("UPDATE companies SET otp = NULL, otp_expires_at = NULL WHERE email = ?");
                    $update->bind_param("s", $email);
                    $update->execute();
                    $update->close();

                    $msg = "<div class='alert alert-success'>OTP verified successfully! Your registration is complete.</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Incorrect OTP. Please try again.</div>";
                }
            }
        } else {
            $msg = "<div class='alert alert-danger'>No company found with this email.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Company OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="card p-4 shadow mx-auto" style="max-width: 400px;">
        <h3 class="mb-4 text-center">Verify Your Email OTP</h3>

        <?= $msg ?>

        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Company Email</label>
                <input type="email" name="email" id="email" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label for="otp" class="form-label">OTP Code</label>
                <input type="text" name="otp" id="otp" class="form-control" maxlength="6" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
        </form>
    </div>
</div>
</body>
</html>
