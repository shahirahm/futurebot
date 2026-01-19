<?php
session_start();

$login_error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_company_approvals.php");
        exit;
    } else {
        $login_error = "Username and password are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #dee9ffff, #e2e0f5ff, #ffffffff);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .card-glass {
            backdrop-filter: blur(5px) saturate(180%);
            -webkit-backdrop-filter: blur(8px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(40px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .card-glass h3 {
            font-weight: 600;
            color:darkblue;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .form-label {
            color: darkblue;
            font-weight: 500;
        }

        .form-control {
            border-radius: 12px;
            padding: 10px;
            border: none;
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.05);
        }

        .btn-glow {
            background: linear-gradient(90deg, #24153eff, #00c6ff);
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 15px rgba(0, 150, 255, 0.5);
        }

        .btn-glow:hover {
            background: linear-gradient(90deg, #0056b3, #009acb);
            box-shadow: 0 6px 20px rgba(0, 150, 255, 0.7);
        }

        .alert-danger {
            border-radius: 12px;
            text-align: center;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="card-glass text-center">
    <h3 class="mb-4"> Admin Login 🛡️</h3>

    <?php if (!empty($login_error)) : ?>
        <div class="alert alert-danger"><?= $login_error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 text-start">
            <label for="username" class="form-label">Admin Username</label>
            <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required>
        </div>

        <div class="mb-4 text-start">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-glow">Login</button>
        </div>
    </form>
</div>

</body>
</html>
