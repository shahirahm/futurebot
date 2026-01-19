<?php
require 'db.php';

$msg = '';

// Define allowed Gmail addresses (only these can register)
$allowedEmails = [
    'approvedcompany1@gmail.com',
    'approvedcompany2@gmail.com',
    'samihamaisha231@gmail.com',
    // Add more allowed emails here
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Safely get POST data with null coalescing operator
    $name = $_POST['name'] ?? '';
    $year = $_POST['start_year'] ?? '';
    $license = $_POST['trade_license'] ?? '';
    $email = $_POST['email'] ?? '';
    $passwordRaw = $_POST['password'] ?? '';
    $address = $_POST['address'] ?? '';

    $password = $passwordRaw ? password_hash($passwordRaw, PASSWORD_DEFAULT) : '';

    $document_path = '';
    if (isset($_FILES['auth_document']) && $_FILES['auth_document']['error'] === 0) {
        $target_dir = "uploads/";
        // Use uniqid prefix to avoid filename collisions
        $filename = uniqid() . '_' . basename($_FILES["auth_document"]["name"]);
        $document_path = $target_dir . $filename;

        if (!move_uploaded_file($_FILES["auth_document"]["tmp_name"], $document_path)) {
            $msg = "<div class='alert alert-danger text-center'>❌ Failed to upload document.</div>";
        }
    }

    // Check if email is in allowed list
    if (!in_array($email, $allowedEmails)) {
        $msg = "<div class='alert alert-danger text-center'>❌ This email is not authorized for registration. Please contact admin.</div>";
    } elseif (empty($name) || empty($year) || empty($license) || empty($email) || empty($passwordRaw)) {
        $msg = "<div class='alert alert-danger text-center'>❌ Please fill in all required fields.</div>";
    } elseif (empty($msg)) {
        // Insert new company with status 'pending'
        $stmt = $conn->prepare("INSERT INTO companies (name, start_year, trade_license, email, password, address, document_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("sisssss", $name, $year, $license, $email, $password, $address, $document_path);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-info text-center'>✅ Registration successful! Please verify OTP sent to your email.</div>";
        } else {
            $msg = "<div class='alert alert-danger text-center'>❌ Error: " . htmlspecialchars($stmt->error) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Company Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow p-4">
                <h3 class="text-center mb-4">🏢 Company Registration</h3>

                <?= $msg ?>

                <form method="POST" enctype="multipart/form-data" id="registrationForm" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" name="name" id="name" required />
                    </div>

                    <div class="mb-3">
                        <label for="start_year" class="form-label">Starting Year</label>
                        <input type="number" class="form-control" name="start_year" id="start_year" required />
                    </div>

                    <div class="mb-3">
                        <label for="trade_license" class="form-label">Trade License Number</label>
                        <input type="text" class="form-control" name="trade_license" id="trade_license" required />
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Company Email</label>
                        <input type="email" class="form-control" name="email" id="email" required />
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Create Password</label>
                        <input type="password" class="form-control" name="password" id="password" required />
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Company Address</label>
                        <textarea class="form-control" name="address" id="address" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="auth_document" class="form-label">Upload Authentication Document</label>
                        <input type="file" class="form-control" name="auth_document" id="auth_document" required />
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">📤 Register</button>
                    </div>
                </form>

                <!-- You can put your OTP verify/send buttons here if needed -->
            </div>
        </div>
    </div>
</div>

</body>
</html>
