<?php
session_start();
require_once 'db.php';

$user_id = $_SESSION['user_id'] ?? null;
$message = $_GET['message'] ?? '';

// Fetch company basic info
$company_basic = null;
$profile = null;

if ($user_id) {
    $stmt1 = $conn->prepare("SELECT name, start_year FROM companies WHERE id = ?");
    $stmt1->bind_param("i", $user_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $company_basic = $result1->fetch_assoc();
    $stmt1->close();

    $stmt2 = $conn->prepare("SELECT bio, location, facilities, rating, logo_path FROM company_profiles WHERE company_id = ?");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $profile = $result2->fetch_assoc();
    $stmt2->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard | FutureBot</title>
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
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link:hover {
            color: var(--primary-blue);
            background: rgba(67, 97, 238, 0.05);
            transform: translateY(-1px);
            text-decoration: none;
        }

        .nav-link.active {
            background: var(--gradient);
            color: white;
        }

        .nav-link.btn-primary {
            background: var(--gradient);
            color: white;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .nav-link.btn-primary:hover {
            background: var(--gradient-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 2.5rem auto;
            padding: 0 1.25rem;
        }

        /* Alert Message */
        .alert-message {
            background: rgba(40, 152, 66, 0.1);
            color: #198754;
            border: 1px solid rgba(40, 152, 66, 0.2);
            border-left: 4px solid #198754;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Profile Card */
        .profile-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            padding: 2.5rem;
            animation: fadeIn 0.8s ease-in-out;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(67, 97, 238, 0.1);
            backdrop-filter: blur(10px);
        }

        .profile-card::before {
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

        .profile-header {
            display: flex;
            align-items: flex-start;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .profile-logo-container {
            position: relative;
        }

        .profile-logo {
            width: 140px;
            height: 140px;
            border-radius: 20px;
            object-fit: cover;
            border: 4px solid var(--white);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .profile-logo-placeholder {
            width: 140px;
            height: 140px;
            border-radius: 20px;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
            border: 4px solid var(--white);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.2);
        }

        .profile-info {
            flex: 1;
        }

        .company-name {
            color: var(--text-dark);
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .company-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .meta-item i {
            color: var(--primary-blue);
            font-size: 1.1rem;
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .detail-section {
            background: var(--light-gray);
            border: 1px solid rgba(67, 97, 238, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .detail-section:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(67, 97, 238, 0.2);
        }

        .detail-title {
            color: var(--dark-blue);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-content {
            color: var(--text-dark);
            line-height: 1.6;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-light);
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: rgba(67, 97, 238, 0.3);
        }

        .empty-title {
            color: var(--text-dark);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .empty-description {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
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
            gap: 0.75rem;
            text-decoration: none;
        }

        .btn-primary:hover {
            background: var(--gradient-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
            color: white;
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
                flex-direction: column;
                gap: 1rem;
            }
            
            .logo-text {
                font-size: 1.2rem;
            }
            
            .nav-links {
                gap: 1rem;
            }
            
            .main-content {
                margin: 1.5rem auto;
                padding: 0 1rem;
            }
            
            .profile-card {
                padding: 2rem 1.5rem;
                border-radius: 16px;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .company-meta {
                justify-content: center;
            }
            
            .profile-details {
                grid-template-columns: 1fr;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .company-name {
                font-size: 1.75rem;
            }
            
            .profile-card {
                padding: 1.5rem 1.25rem;
            }

            .navbar {
                padding: 0.75rem 1rem;
            }

            .logo-icon {
                width: 35px;
                height: 35px;
            }

            .nav-link {
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
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

    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="logo-text">FutureBot</div>
        </div>
        
        <div class="nav-links">
            <a href="company_profile_create.php" class="nav-link">
                <i class="fas fa-edit"></i> Edit Profile
            </a>
            <a href="job_post.php" class="nav-link btn-primary">
                <i class="fas fa-briefcase"></i> Post Job
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php if ($message === 'created') : ?>
            <div class="alert-message" id="msg">
                <i class="fas fa-check-circle"></i>
                Profile created successfully.
            </div>
        <?php elseif ($message === 'updated') : ?>
            <div class="alert-message" id="msg">
                <i class="fas fa-check-circle"></i>
                Profile updated successfully.
            </div>
        <?php endif; ?>

        <?php if ($company_basic): ?>
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-logo-container">
                        <?php if (!empty($profile['logo_path']) && file_exists($profile['logo_path'])): ?>
                            <img src="<?= htmlspecialchars($profile['logo_path']) ?>" alt="Company Logo" class="profile-logo">
                        <?php else: ?>
                            <div class="profile-logo-placeholder">
                                <?= strtoupper(substr($company_basic['name'], 0, 2)) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="profile-info">
                        <h1 class="company-name"><?= htmlspecialchars($company_basic['name']) ?></h1>
                        <div class="company-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>Started: <?= htmlspecialchars($company_basic['start_year'] ?? 'N/A') ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-star"></i>
                                <span>Rating: <?= htmlspecialchars($profile['rating'] ?? 'N/A') ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($profile['location'] ?? 'Location not specified') ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="profile-details">
                    <div class="detail-section">
                        <h3 class="detail-title">
                            <i class="fas fa-building"></i>
                            Company Bio
                        </h3>
                        <div class="detail-content">
                            <?= nl2br(htmlspecialchars($profile['bio'] ?? 'No bio available')) ?>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h3 class="detail-title">
                            <i class="fas fa-wrench"></i>
                            Facilities
                        </h3>
                        <div class="detail-content">
                            <?= nl2br(htmlspecialchars($profile['facilities'] ?? 'Not added yet')) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h2 class="empty-title">No Company Profile Found</h2>
                <p class="empty-description">
                    Get started by creating your company profile to showcase your organization 
                    and connect with potential candidates.
                </p>
                <a href="company_profile_create.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Create Company Profile
                </a>
            </div>
        <?php endif; ?>
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
        // Auto-hide success message
        setTimeout(() => {
            const msg = document.getElementById('msg');
            if (msg) {
                msg.style.transition = 'opacity 0.5s ease';
                msg.style.opacity = '0';
                setTimeout(() => {
                    msg.style.display = 'none';
                }, 500);
            }
        }, 4000);

        // Prevent horizontal overflow
        function preventHorizontalOverflow() {
            document.body.style.overflowX = 'hidden';
            document.documentElement.style.overflowX = 'hidden';
        }

        // Initialize on load and resize
        preventHorizontalOverflow();
        window.addEventListener('resize', preventHorizontalOverflow);
    </script>
</body>
</html>