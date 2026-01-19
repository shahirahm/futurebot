<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    // In a real application, you would process the form here
    // For now, we'll just show a success message
    $success = "Thank you for your message! We'll get back to you within 24 hours.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FutureBot AI Career Path Advisor</title>
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
            max-width: 1200px;
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
        
        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
        }
        
        .contact-info {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid rgba(67, 97, 238, 0.1);
            position: relative;
            overflow: hidden;
        }

        .contact-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #4361ee, #3a0ca3);
        }
        
        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border: 1px solid rgba(67, 97, 238, 0.1);
            position: relative;
            overflow: hidden;
        }

        .contact-form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #4361ee, #3a0ca3);
        }
        
        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .info-item:hover {
            transform: translateX(5px);
        }
        
        .info-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #f8f9fa;
            color: #2c3e50;
            font-size: 16px;
            transition: all 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4361ee;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            transform: translateY(-2px);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            text-align: center;
            font-weight: 600;
        }

        .contact-info h2, .contact-form h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 1.8rem;
            position: relative;
            display: inline-block;
        }

        .contact-info h2::after, .contact-form h2::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 3px;
            background: linear-gradient(90deg, #4361ee, #3a0ca3);
            border-radius: 2px;
        }

        .info-item h3 {
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .info-item p {
            color: #5a6c7d;
            line-height: 1.5;
        }

        .contact-map {
            margin-top: 30px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .map-placeholder {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
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
            .contact-content {
                grid-template-columns: 1fr;
            }
            
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
            
            .contact-info, .contact-form {
                padding: 30px 25px;
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
            <h1>Contact Us</h1>
            <p>Get in touch with our team for any questions or support. We're here to help you succeed!</p>
        </div>
        
        <div class="contact-content">
            <div class="contact-info">
                <h2>Get In Touch</h2>
                <p>We're here to help you with your career journey. Reach out to us through any of the following channels:</p>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <h3>Address</h3>
                        <p>123 Education Avenue<br>Tech City, 1200</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <h3>Phone</h3>
                        <p>+8801738915382<br>+8801738915383</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <h3>Email</h3>
                        <p>support@futurebot.com<br>careers@futurebot.com</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h3>Support Hours</h3>
                        <p>Monday - Friday: 9AM - 6PM<br>Saturday: 10AM - 4PM</p>
                    </div>
                </div>

                <div class="contact-map">
                    <div class="map-placeholder">
                        <i class="fas fa-map-marked-alt"></i> Interactive Map Location
                    </div>
                </div>
            </div>
            
            <div class="contact-form">
                <h2>Send us a Message</h2>
                
                <?php if (isset($success)): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required placeholder="Enter your full name">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="Enter your email address">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="">Select a subject</option>
                            <option value="general">General Inquiry</option>
                            <option value="technical">Technical Support</option>
                            <option value="billing">Billing Issue</option>
                            <option value="career">Career Guidance</option>
                            <option value="feedback">Feedback</option>
                            <option value="partnership">Partnership</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" required placeholder="Tell us how we can help you..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
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

        // Add form input animations
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, textarea, select');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        });

        // Prevent horizontal overflow on resize
        window.addEventListener('resize', function() {
            document.body.style.overflowX = 'hidden';
        });
    </script>
</body>
</html>