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
            $msg = "<div class='alert alert-danger'>❌ Failed to upload document.</div>";
        }
    }

    // Check if email is in allowed list
    if (!in_array($email, $allowedEmails)) {
        $msg = "<div class='alert alert-danger'>❌ This email is not authorized for registration. Please contact admin.</div>";
    } elseif (empty($name) || empty($year) || empty($license) || empty($email) || empty($passwordRaw)) {
        $msg = "<div class='alert alert-danger'>❌ Please fill in all required fields.</div>";
    } elseif (empty($msg)) {
        // Insert new company with status 'pending'
        $stmt = $conn->prepare("INSERT INTO companies (name, start_year, trade_license, email, password, address, document_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("sisssss", $name, $year, $license, $email, $password, $address, $document_path);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>✅ Registration successful! Your account is pending admin approval.</div>";
        } else {
            $msg = "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($stmt->error) . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Registration | FutureBot</title>
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

        .container {
            max-width: 1200px;
            margin: 2.5rem auto;
            padding: 0 1.25rem;
        }

        .registration-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 2.5rem;
            animation: fadeIn 0.8s ease-in-out;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(67, 97, 238, 0.1);
            backdrop-filter: blur(10px);
            max-width: 600px;
            margin: 0 auto;
        }

        .registration-card::before {
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

        .registration-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .registration-icon {
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

        .registration-title {
            color: var(--text-dark);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .registration-subtitle {
            color: var(--text-light);
            font-size: 1rem;
            margin-bottom: 0;
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

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .file-input-wrapper {
            position: relative;
        }

        .file-input-wrapper input[type="file"] {
            padding: 0.875rem 1rem;
            border: 2px dashed rgba(67, 97, 238, 0.2);
            border-radius: 12px;
            background: rgba(67, 97, 238, 0.02);
            transition: all 0.3s ease;
        }

        .file-input-wrapper input[type="file"]:hover {
            border-color: var(--primary-blue);
            background: rgba(67, 97, 238, 0.05);
        }

        .file-input-wrapper input[type="file"]::file-selector-button {
            background: var(--gradient);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }

        .file-input-wrapper input[type="file"]::file-selector-button:hover {
            background: var(--gradient-hover);
            transform: translateY(-1px);
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

        .alert-success {
            background: rgba(40, 152, 66, 0.1);
            color: #198754;
            border-left: 4px solid #198754;
        }

        .alert-info {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
            border-left: 4px solid #0d6efd;
        }

        .back-link {
            text-align: center;
            margin-top: 2rem;
        }

        .back-link a {
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

        .back-link a:hover {
            background: rgba(67, 97, 238, 0.1);
            transform: translateY(-1px);
            text-decoration: none;
        }

        /* Footer Styles */
        footer {
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem 1.25rem;
            border-top: 1px solid rgba(67, 97, 238, 0.08);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.03);
            margin-top: 4rem;
            backdrop-filter: blur(10px);
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
            
            .container {
                margin: 1.5rem auto;
                padding: 0 1rem;
            }
            
            .registration-card {
                padding: 2rem 1.5rem;
                border-radius: 16px;
            }
            
            .registration-title {
                font-size: 1.75rem;
            }

            .registration-icon {
                width: 70px;
                height: 70px;
                font-size: 1.75rem;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .registration-title {
                font-size: 1.5rem;
            }
            
            .registration-card {
                padding: 1.5rem 1rem;
            }

            .navbar {
                padding: 0.75rem 1rem;
            }

            .logo-icon {
                width: 35px;
                height: 35px;
            }
        }

        /* Form validation styles */
        .form-control:invalid:not(:focus):not(:placeholder-shown) {
            border-color: var(--danger);
        }

        .form-control:valid:not(:focus):not(:placeholder-shown) {
            border-color: var(--success);
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

    <!-- Navigation -->
    <div class="navbar">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="logo-text">FutureBot</div>
        </div>
    </div>

    <div class="container">
        <div class="registration-card">
            <div class="registration-header">
                <div class="registration-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h1 class="registration-title">Company Registration</h1>
                <p class="registration-subtitle">Join FutureBot as a partner company</p>
            </div>

            <?= $msg ?>

            <form method="POST" enctype="multipart/form-data" id="registrationForm" novalidate>
                <div class="form-group">
                    <label for="name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter your company name" required />
                </div>

                <div class="form-group">
                    <label for="start_year" class="form-label">Starting Year</label>
                    <input type="number" class="form-control" name="start_year" id="start_year" placeholder="e.g., 2020" min="1900" max="2030" required />
                </div>

                <div class="form-group">
                    <label for="trade_license" class="form-label">Trade License Number</label>
                    <input type="text" class="form-control" name="trade_license" id="trade_license" placeholder="Enter trade license number" required />
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Company Email</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="company@example.com" required />
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Create Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Create a secure password" required />
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Company Address</label>
                    <textarea class="form-control" name="address" id="address" rows="3" placeholder="Enter your company's full address"></textarea>
                </div>

                <div class="form-group">
                    <label for="auth_document" class="form-label">Upload Authentication Document</label>
                    <div class="file-input-wrapper">
                        <input type="file" class="form-control" name="auth_document" id="auth_document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required />
                    </div>
                    <small style="color: var(--text-light); font-size: 0.875rem; margin-top: 0.5rem; display: block;">
                        Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG
                    </small>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-building"></i> Register Company
                    </button>
                </div>
            </form>

            <div class="back-link">
                <a href="login.php">
                    <i class="fas fa-arrow-left"></i> Back 
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
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registrationForm');
            const inputs = form.querySelectorAll('input, textarea');
            
            // Add focus effects
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Form validation enhancement
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                submitBtn.disabled = true;
                
                // Re-enable after 3 seconds if form doesn't submit
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
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