<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - FutureBot AI Career Path Advisor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { 
            box-sizing: border-box; 
            margin:0; 
            padding:0; 
        }
        html, body {
            width: 100%;
            overflow-x: hidden;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: #2c3e50;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navigation */
        nav {
            width: 100%;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: fixed;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
        }
        nav .logo {
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 1px;
            background: linear-gradient(90deg, #4361ee, #3a0ca3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        nav .logo i {
            font-size: 1.5rem;
        }
        nav .nav-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        nav .nav-buttons button {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
        nav .nav-buttons button:hover {
            background: linear-gradient(135deg, #3a0ca3, #4361ee);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
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
            background: rgba(67, 97, 238, 0.05);
            animation: float 15s infinite ease-in-out;
        }

        .circle:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .circle:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 70%;
            left: 80%;
            animation-delay: 2s;
        }

        .circle:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 40%;
            left: 85%;
            animation-delay: 4s;
        }

        .circle:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 80%;
            left: 15%;
            animation-delay: 6s;
        }

        .circle:nth-child(5) {
            width: 70px;
            height: 70px;
            top: 20%;
            left: 70%;
            animation-delay: 8s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            25% {
                transform: translateY(-20px) translateX(10px);
            }
            50% {
                transform: translateY(10px) translateX(-15px);
            }
            75% {
                transform: translateY(-15px) translateX(-10px);
            }
        }
        
        .container {
            max-width: 1000px;
            margin: 100px auto 60px;
            padding: 0 20px;
            width: 100%;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #2c3e50;
            position: relative;
            display: inline-block;
        }

        .page-header h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, #4361ee, #3a0ca3);
            border-radius: 2px;
        }
        
        .page-header p {
            font-size: 1.2rem;
            color: #5a6c7d;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .terms-content {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            line-height: 1.7;
            border: 1px solid rgba(67, 97, 238, 0.1);
            position: relative;
            overflow: hidden;
        }

        .terms-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #4361ee, #3a0ca3);
        }
        
        .section {
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
        }

        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .section h2 {
            color: #4361ee;
            margin-bottom: 20px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section h2 i {
            font-size: 1.3rem;
            width: 30px;
        }
        
        .section p {
            margin-bottom: 15px;
            color: #5a6c7d;
            font-size: 1.05rem;
        }
        
        .section ul {
            margin-left: 25px;
            margin-bottom: 15px;
        }
        
        .section li {
            margin-bottom: 10px;
            color: #5a6c7d;
            position: relative;
            padding-left: 25px;
        }

        .section li::before {
            content: '•';
            color: #4361ee;
            font-weight: bold;
            font-size: 1.2rem;
            position: absolute;
            left: 0;
            top: 0;
        }

        .highlight-box {
            background: rgba(67, 97, 238, 0.05);
            border-left: 4px solid #4361ee;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .highlight-box p {
            margin-bottom: 0;
            color: #2c3e50;
            font-weight: 500;
        }

        .contact-info {
            background: rgba(67, 97, 238, 0.05);
            padding: 25px;
            border-radius: 12px;
            margin-top: 20px;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }

        .contact-info h3 {
            color: #4361ee;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        footer {
            margin-top: auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px 20px;
            border-top: 1px solid rgba(67, 97, 238, 0.1);
            width: 100%;
        }

        /* Footer Styles */
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(90deg, #4361ee, #3a0ca3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-links {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .footer-links a {
            color: #5a6c7d;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .footer-links a:hover {
            color: #4361ee;
            transform: translateY(-2px);
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #4361ee;
            transition: width 0.3s ease;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-social {
            display: flex;
            gap: 20px;
            margin: 10px 0;
        }

        .footer-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .footer-social a:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(67, 97, 238, 0.1);
            width: 100%;
            color: #7f8c8d;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            nav {
                padding: 15px 20px;
            }
            
            .container {
                margin: 80px auto 40px;
                padding: 0 15px;
            }
            
            .page-header h1 {
                font-size: 2.5rem;
            }
            
            .terms-content {
                padding: 30px 25px;
            }
            
            .section h2 {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 500px) {
            nav {
                padding: 12px 15px;
            }
            
            nav .logo {
                font-size: 1.5rem;
            }
            
            nav .nav-buttons button {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 15px;
            }
            
            .section ul {
                margin-left: 15px;
            }
            
            .section li {
                padding-left: 20px;
            }
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
    <nav>
        <div class="logo">
            <i class="fas fa-robot"></i>FutureBot
        </div>
        <div class="nav-buttons">
            <button onclick="goBack()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <button onclick="location.href='index.php'">
                <i class="fas fa-home"></i> Home
            </button>
        </div>
    </nav>
    
    <div class="container">
        <div class="page-header">
            <h1>Terms of Service</h1>
            <p>Last updated: <?php echo date('F j, Y'); ?></p>
        </div>
        
        <div class="terms-content">
            <div class="section">
                <h2><i class="fas fa-check-circle"></i> 1. Acceptance of Terms</h2>
                <p>By accessing and using FutureBot AI Career Path Advisor ("the Service"), you accept and agree to be bound by the terms and provision of this agreement.</p>
                <div class="highlight-box">
                    <p><strong>Important:</strong> Continued use of our services constitutes acceptance of these terms and any future updates.</p>
                </div>
            </div>
            
            <div class="section">
                <h2><i class="fas fa-info-circle"></i> 2. Description of Service</h2>
                <p>FutureBot provides AI-powered career guidance, course recommendations, learning path suggestions, and progress tracking services for educational and professional development purposes.</p>
                <p>Our platform leverages advanced machine learning algorithms to analyze your skills, interests, and career goals to provide personalized recommendations.</p>
            </div>
            
            <div class="section">
                <h2><i class="fas fa-user-circle"></i> 3. User Accounts</h2>
                <p>To access certain features of the Service, you must register for an account. You agree to:</p>
                <ul>
                    <li>Provide accurate and complete registration information</li>
                    <li>Maintain the security of your password</li>
                    <li>Accept responsibility for all activities that occur under your account</li>
                    <li>Notify us immediately of any unauthorized use of your account</li>
                    <li>Keep your profile information current and accurate</li>
                </ul>
            </div>
            
            <div class="section">
                <h2><i class="fas fa-credit-card"></i> 4. Course Enrollment and Payments</h2>
                <p>Some courses may require payment. By enrolling in paid courses, you agree to:</p>
                <ul>
                    <li>Pay all applicable fees</li>
                    <li>Provide accurate billing information</li>
                    <li>Accept our refund policy as stated during enrollment</li>
                    <li>Complete payments through our secure payment gateway</li>
                    <li>Understand that course fees are subject to change</li>
                </ul>
            </div>
            
            <div class="section">
                <h2><i class="fas fa-copyright"></i> 5. Intellectual Property</h2>
                <p>All content provided through the Service, including course materials, AI recommendations, and platform content, is the intellectual property of FutureBot or its licensors and is protected by copyright laws.</p>
                <p>You may not reproduce, distribute, modify, create derivative works of, publicly display, or exploit any content without express written permission.</p>
            </div>
            
            <div class="section">
                <h2><i class="fas fa-shield-alt"></i> 6. Limitation of Liability</h2>
                <p>FutureBot provides career guidance and recommendations based on AI analysis. While we strive for accuracy, we cannot guarantee specific career outcomes or job placements. Users are responsible for their own career decisions.</p>
                <div class="highlight-box">
                    <p><strong>Disclaimer:</strong> Career outcomes depend on various factors including market conditions, individual effort, and external circumstances beyond our control.</p>
                </div>
            </div>

            <div class="section">
                <h2><i class="fas fa-sync-alt"></i> 7. Modifications to Terms</h2>
                <p>We reserve the right to modify these terms at any time. We will notify users of significant changes through email or platform notifications.</p>
                <p>Continued use of the Service after changes constitutes acceptance of the modified terms.</p>
            </div>
            
            <div class="section">
                <h2><i class="fas fa-envelope"></i> 8. Contact Information</h2>
                <p>For questions about these Terms of Service, please contact us:</p>
                <div class="contact-info">
                    <h3><i class="fas fa-headset"></i> Legal Department</h3>
                    <p><strong>Email:</strong> legal@futurebot.com</p>
                    <p><strong>Phone:</strong> +8801738915382</p>
                    <p><strong>Response Time:</strong> Within 2-3 business days</p>
                </div>
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
        function goBack() {
            if (document.referrer && document.referrer.includes(window.location.host)) {
                window.history.back();
            } else {
                window.location.href = 'index.php';
            }
        }

        // Prevent horizontal overflow on resize
        window.addEventListener('resize', function() {
            document.body.style.overflowX = 'hidden';
        });

        // Add smooth scrolling for better UX
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a[href^="#"]');
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>