<?php
session_start();
require_once 'db.php';

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT id, company_name, password, status FROM companies WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $company = $result->fetch_assoc();
        $stmt->close();

        if ($company && password_verify($password, $company['password'])) {
            if ($company['status'] === 'approved') {
                $_SESSION['company_id'] = $company['id'];
                $_SESSION['company_name'] = $company['company_name'];

                // ✅ Always redirect to company_profile.php after login
                header("Location: company_profile.php");
                exit;
            } else {
                $login_error = "Your account is not approved yet. Status: " . htmlspecialchars($company['status']);
            }
        } else {
            $login_error = "Invalid email or password.";
        }
    } else {
        $login_error = "Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Company Login - FutureBot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
        }
        .login-container {
            max-width: 400px;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .form-title {
            font-weight: bold;
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
            color: #1877f2;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="form-title">Company Login</div>

    <?php if ($login_error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($login_error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Company Email</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>

        <div class="mt-3 text-center">
            <a href="company_register.php">Don't have an account? Register</a>
        </div>
    </form>
</div>

</body>
</html>
