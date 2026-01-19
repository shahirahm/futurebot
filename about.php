<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - FutureBot AI Career Path Advisor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { 
            box-sizing: border-box; 
            margin:0; 
            padding:0; 
        }
        html, body {
            width: 100%;
            overflow-x: hidden; /* Prevent horizontal overflow */
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
        
        .container {
            max-width: 1200px;
            margin: 100px auto 60px;
            padding: 0 20px;
            width: 100%; /* Ensure container doesn't exceed viewport */
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        
        .page-header p {
            font-size: 1.2rem;
            color: #5a6c7d;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            margin-bottom: 60px;
        }
        
        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #5a6c7d;
        }
        
        .mission-vision {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 40px 0;
        }
        
        .mission-card, .vision-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .mission-card h3, .vision-card h3 {
            color: #4361ee;
            margin-bottom: 15px;
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
        
        /* Remove the back button from main content */
        .back-button {
            display: none;
        }
        
        @media (max-width: 768px) {
            .about-content, .mission-vision {
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
            
            .footer-links {
                gap: 20px;
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
        <!-- Back Button removed from here and moved to nav bar -->
        
        <div class="page-header">
            <h1>About FutureBot</h1>
            <p>Revolutionizing career guidance through artificial intelligence and personalized learning</p>
        </div>
        
        <div class="about-content">
            <div class="about-text">
                <h2>Our Story</h2>
                <p>FutureBot was born from a simple observation: traditional career guidance often fails to keep pace with the rapidly evolving job market. We combine cutting-edge AI technology with expert career counseling to provide students and professionals with dynamic, personalized career path recommendations.</p>
                
                <p>Our platform analyzes your skills, interests, and goals to create tailored learning paths that align with real-world career opportunities and industry demands.</p>
            </div>
            
            <div class="about-text">
                <h2>What We Do</h2>
                <p>FutureBot uses advanced machine learning algorithms to match your profile with ideal career paths, recommend relevant courses, track your progress, and connect you with industry mentors.</p>
                
                <p>We bridge the gap between academic education and professional requirements, ensuring our users are well-prepared for the careers of tomorrow.</p>
            </div>
        </div>
        
        <div class="mission-vision">
            <div class="mission-card">
                <h3><i class="fas fa-bullseye"></i> Our Mission</h3>
                <p>To democratize access to quality career guidance and make personalized career planning accessible to every student and professional worldwide.</p>
            </div>
            
            <div class="vision-card">
                <h3><i class="fas fa-eye"></i> Our Vision</h3>
                <p>A world where everyone can discover and pursue their ideal career path with confidence, supported by intelligent technology and expert guidance.</p>
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
    </script>
</body>
</html>