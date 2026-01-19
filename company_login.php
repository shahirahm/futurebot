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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Login | FutureBot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #4361ee;
            --dark-blue: #3a0ca3;
            --light-blue: #4cc9f0;
            --very-light-blue: #f8faff;
            --purple: #7209b7;
            --light-purple: #f72585;
            --white: #ffffff;
            --success: #289842;
            --danger: #e63946;
            --warning: #fca311;
            --gray: #6c757d;
            --light-gray: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #64748b;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
            --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --gradient-light: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
            --gradient-hover: linear-gradient(135deg, #3a0ca3 0%, #7209b7 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--text-dark);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* Animated Background */
        .background-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(67, 97, 238, 0.03);
            animation: float 20s infinite ease-in-out;
        }

        .circle:nth-child(1) {
            width: 60px;
            height: 60px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .circle:nth-child(2) {
            width: 100px;
            height: 100px;
            top: 70%;
            left: 80%;
            animation-delay: 2s;
        }

        .circle:nth-child(3) {
            width: 40px;
            height: 40px;
            top: 40%;
            left: 85%;
            animation-delay: 4s;
        }

        .circle:nth-child(4) {
            width: 80px;
            height: 80px;
            top: 80%;
            left: 15%;
            animation-delay: 6s;
        }

        .circle:nth-child(5) {
            width: 50px;
            height: 50px;
            top: 20%;
            left: 70%;
            animation-delay: 8s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            25% {
                transform: translateY(-15px) translateX(8px);
            }
            50% {
                transform: translateY(8px) translateX(-12px);
            }
            75% {
                transform: translateY(-12px) translateX(-8px);
            }
        }

        /* Navigation Bar */
        .navbar {
            background-color: var(--white);
            padding: 1rem 2.5rem;
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
        }

        .logo-text {
            font-size: 1.4rem;
            font-weight: 700;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .nav-link:hover {
            color: var(--primary-blue);
            background: rgba(67, 97, 238, 0.05);
            transform: translateY(-1px);
        }

        .back-btn {
            background: var(--white);
            color: var(--primary-blue);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            border: 2px solid var(--primary-blue);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
        }

        .back-btn:hover {
            background: var(--primary-blue);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.25);
            text-decoration: none;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            min-height: calc(100vh - 140px); /* Account for navbar and footer */
        }

        .login-container {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 2.5rem;
            animation: fadeIn 0.8s ease-in-out;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(67, 97, 238, 0.1);
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 450px;
            margin: 2rem auto;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
        }

        .login-title {
            color: var(--text-dark);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--text-light);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid rgba(67, 97, 238, 0.1);
            border-radius: 12px;
            background: var(--white);
            color: var(--text-dark);
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            transform: translateY(-1px);
        }

        .form-control::placeholder {
            color: var(--text-light);
            opacity: 0.7;
        }

        .btn-primary {
            background: var(--gradient);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.3);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
        }

        .btn-primary:hover {
            background: var(--gradient-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-danger {
            background: rgba(230, 57, 70, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .register-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(67, 97, 238, 0.1);
        }

        .register-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            background: rgba(67, 97, 238, 0.05);
        }

        .register-link a:hover {
            background: rgba(67, 97, 238, 0.1);
            transform: translateY(-1px);
            text-decoration: none;
        }

        /* Footer Styles */
        footer {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem 1.25rem;
            border-top: 1px solid rgba(67, 97, 238, 0.08);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.03);
            backdrop-filter: blur(10px);
            margin-top: auto; /* Push footer to bottom */
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.25rem;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.3rem;
            font-weight: bold;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-links {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .footer-links a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            font-size: 0.9rem;
        }

        .footer-links a:hover {
            color: var(--primary-blue);
            transform: translateY(-1px);
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--primary-blue);
            transition: width 0.3s ease;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-social {
            display: flex;
            gap: 1rem;
            margin: 0.75rem 0;
        }

        .footer-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: var(--gradient);
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
            font-size: 0.9rem;
        }

        .footer-social a:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.35);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(67, 97, 238, 0.08);
            width: 100%;
            color: var(--text-light);
            font-size: 0.85rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem 1.25rem;
            }
            
            .logo-text {
                font-size: 1.2rem;
            }
            
            .nav-links {
                gap: 1rem;
            }
            
            .login-container {
                padding: 2rem 1.5rem;
                border-radius: 16px;
                margin: 1rem;
            }
            
            .login-title {
                font-size: 1.75rem;
            }

            .login-icon {
                width: 70px;
                height: 70px;
                font-size: 1.75rem;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }

            .main-content {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .login-title {
                font-size: 1.5rem;
            }
            
            .login-container {
                padding: 1.5rem 1.25rem;
            }

            .login-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }

            .navbar {
                padding: 0.75rem 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .logo-icon {
                width: 35px;
                height: 35px;
            }

            .nav-links {
                width: 100%;
                justify-content: space-between;
            }
        }

        /* Loading state */
        .btn-loading {
            pointer-events: none;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="background-animation">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="logo-text">FutureBot</div>
        </div>
        
        <div class="nav-links">
            <a href="index.php" class="nav-link">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="company_register.php" class="nav-link">
                <i class="fas fa-user-plus"></i> Register
            </a>
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="login-container">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h1 class="login-title">Company Login</h1>
                <p class="login-subtitle">Access your company dashboard</p>
            </div>

            <?php if ($login_error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($login_error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="email" class="form-label">Company Email</label>
                    <input type="email" name="email" id="email" class="form-control" required 
                           placeholder="Enter your company email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required 
                           placeholder="Enter your password">
                </div>

                <button type="submit" class="btn-primary" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                </button>
            </form>

            <div class="register-link">
                <a href="company_register.php">
                    <i class="fas fa-user-plus"></i> Don't have an account? Register Company
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-logo">
                <i class="fas fa-robot"></i>FutureBot
            </div>
            
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="about.php">About Us</a>
                <a href="career_suggestions.php">Career Suggestions</a>
                <a href="privacy.php">Privacy Policy</a>
                <a href="terms.php">Terms of Service</a>
                <a href="contact.php">Contact Us</a>
            </div>
            
            <div class="footer-social">
                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" title="GitHub"><i class="fab fa-github"></i></a>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 FutureBot. All rights reserved. | Empowering students with AI-driven career guidance</p>
            </div>
        </div>
    </footer>

    <script>
        // Add interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            
            // Add loading state to form submission
            form.addEventListener('submit', function() {
                const originalText = loginBtn.innerHTML;
                
                // Show loading state
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
                loginBtn.classList.add('btn-loading');
                
                // Re-enable after 5 seconds if form doesn't submit
                setTimeout(() => {
                    loginBtn.innerHTML = originalText;
                    loginBtn.classList.remove('btn-loading');
                }, 5000);
            });

            // Add focus effects to form inputs
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Prevent horizontal overflow
            function preventHorizontalOverflow() {
                document.body.style.overflowX = 'hidden';
                document.documentElement.style.overflowX = 'hidden';
            }

            // Initialize on load and resize
            preventHorizontalOverflow();
            window.addEventListener('resize', preventHorizontalOverflow);
        });
    </script>
</body>
</html>