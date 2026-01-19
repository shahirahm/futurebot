<?php
session_start();
require_once 'db.php';

// Check if mentor ID is provided and valid
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div style='padding: 20px; text-align: center; font-family: Arial; color: red;'>Mentor ID missing. Please provide a valid mentor ID.</div>");
}

$mentor_id = intval($_GET['id']);

if ($mentor_id <= 0) {
    die("<div style='padding: 20px; text-align: center; font-family: Arial; color: red;'>Invalid mentor ID.</div>");
}

// Fetch mentor details from mentor_details and users with prepared statement
$sql = "
    SELECT u.full_name, u.email, u.phone, u.profile_pic, u.bio,
           md.university, md.subject, md.recent_profession, md.location, md.demo_link, md.demo_schedule
    FROM mentor_details md
    INNER JOIN users u ON md.user_id = u.user_id
    WHERE md.user_id = ?
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("<div style='padding: 20px; text-align: center; font-family: Arial; color: red;'>Database error: " . $conn->error . "</div>");
}

$stmt->bind_param("i", $mentor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("<div style='padding: 20px; text-align: center; font-family: Arial;'>Mentor not found. The requested mentor profile does not exist.</div>");
}

$mentor = $result->fetch_assoc();

// Validate that we have the essential data
if (!$mentor || !isset($mentor['full_name'])) {
    die("<div style='padding: 20px; text-align: center; font-family: Arial; color: red;'>Invalid mentor data received.</div>");
}

// Handle profile picture path
$profile_pic = 'uploads/default_profile.png'; // Default image
if (!empty($mentor['profile_pic'])) {
    if (file_exists($mentor['profile_pic'])) {
        $profile_pic = $mentor['profile_pic'];
    } else {
        $possible_paths = [
            $mentor['profile_pic'],
            'uploads/' . basename($mentor['profile_pic']),
            '../uploads/' . basename($mentor['profile_pic']),
            './' . $mentor['profile_pic']
        ];
        
        foreach ($possible_paths as $path) {
            if (file_exists($path)) {
                $profile_pic = $path;
                break;
            }
        }
    }
}

$demo_link = $mentor['demo_link'] ?? '';
$demo_schedule = $mentor['demo_schedule'] ?? '';

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Profile - <?= htmlspecialchars($mentor['full_name']) ?> | FutureBot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .back-button {
            background-color: var(--white);
            color: var(--primary-blue);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            border: 2px solid var(--primary-blue);
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .back-button:hover {
            background-color: var(--primary-blue);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.25);
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 2.5rem auto;
            padding: 0 1.25rem;
        }

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
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(67, 97, 238, 0.08);
        }

        .profile-pic-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--white);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            transition: all 0.3s ease;
        }

        .profile-pic:hover {
            transform: scale(1.03);
            box-shadow: 0 12px 30px rgba(67, 97, 238, 0.2);
        }

        .profile-pic-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            border: 4px solid var(--white);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.2);
        }

        .profile-name {
            color: var(--text-dark);
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .profile-title {
            color: var(--primary-blue);
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1.25rem;
            padding: 0.5rem 1.25rem;
            background: rgba(67, 97, 238, 0.08);
            border-radius: 25px;
            display: inline-block;
        }

        .profile-bio {
            color: var(--text-light);
            font-size: 1rem;
            max-width: 650px;
            line-height: 1.7;
            margin-top: 1rem;
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .detail-card {
            background: var(--white);
            border-radius: 14px;
            padding: 1.75rem;
            transition: all 0.3s ease;
            border: 1px solid rgba(67, 97, 238, 0.08);
            position: relative;
            overflow: hidden;
        }

        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background: var(--gradient);
        }

        .detail-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(67, 97, 238, 0.15);
        }

        .detail-icon {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(67, 97, 238, 0.08);
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 1.1rem;
            color: var(--text-dark);
            font-weight: 500;
            line-height: 1.4;
        }

        .detail-value a {
            color: var(--primary-blue);
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            border-bottom: 1px solid transparent;
        }

        .detail-value a:hover {
            color: var(--dark-blue);
            border-bottom-color: var(--dark-blue);
        }

        .demo-section {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.03) 0%, rgba(58, 12, 163, 0.03) 100%);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            margin-top: 2rem;
            border: 1px solid rgba(67, 97, 238, 0.08);
            position: relative;
            overflow: hidden;
        }

        .demo-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(67, 97, 238, 0.03) 1px, transparent 1px);
            background-size: 25px 25px;
            opacity: 0.4;
        }

        .demo-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-blue);
            margin-bottom: 1.25rem;
            position: relative;
        }

        .demo-btn {
            background: var(--gradient);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 2;
        }

        .demo-btn:hover {
            background: var(--gradient-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }

        .demo-info {
            margin-top: 1.25rem;
            font-weight: 600;
            color: var(--dark-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.25rem;
            border-radius: 25px;
            font-size: 0.95rem;
        }

        .contact-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .contact-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            font-size: 0.9rem;
        }

        .contact-btn.phone {
            background: var(--success);
            color: white;
        }

        .contact-btn.email {
            background: var(--primary-blue);
            color: white;
        }

        .contact-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            color: white;
            text-decoration: none;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            border: 1px solid rgba(67, 97, 238, 0.1);
        }

        .modal-header {
            background: var(--gradient);
            color: white;
            border: none;
            padding: 1.5rem 2rem;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .modal-body {
            padding: 2rem;
            text-align: center;
        }

        .modal-footer {
            border-top: 1px solid rgba(67, 97, 238, 0.08);
            padding: 1.5rem 2rem;
        }

        .btn-close-custom {
            background-color: var(--light-gray);
            color: var(--dark-blue);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-close-custom:hover {
            background-color: var(--primary-blue);
            color: white;
        }

        /* Demo Info Styles */
        .demo-info-item {
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            border-radius: 12px;
            background: var(--light-gray);
            border: 1px solid rgba(67, 97, 238, 0.08);
            transition: all 0.3s ease;
        }

        .demo-info-item:hover {
            border-color: var(--primary-blue);
            transform: translateY(-1px);
        }

        .demo-info-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 1rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .demo-info-value {
            font-size: 0.95rem;
            color: var(--primary-blue);
            font-weight: 500;
        }

        .demo-info-value a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border: 2px solid var(--primary-blue);
            border-radius: 25px;
            transition: all 0.3s ease;
            display: inline-block;
            font-size: 0.9rem;
        }

        .demo-info-value a:hover {
            background: var(--primary-blue);
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
        }

        .demo-schedule {
            font-size: 1rem;
            color: var(--success);
            font-weight: 600;
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
            
            .back-button {
                padding: 0.6rem 1.25rem;
                font-size: 0.85rem;
            }
            
            .container {
                margin: 1.5rem auto;
                padding: 0 1rem;
            }
            
            .profile-card {
                padding: 1.5rem;
                border-radius: 16px;
            }
            
            .profile-pic, .profile-pic-placeholder {
                width: 120px;
                height: 120px;
            }
            
            .profile-name {
                font-size: 1.75rem;
            }

            .profile-title {
                font-size: 1rem;
            }
            
            .profile-details {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .demo-section {
                padding: 1.5rem;
            }
            
            .contact-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .contact-btn {
                width: 100%;
                max-width: 220px;
                justify-content: center;
            }

            .modal-body {
                padding: 1.5rem;
            }

            .demo-info-item {
                padding: 1.25rem;
            }

            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .profile-name {
                font-size: 1.5rem;
            }
            
            .profile-title {
                font-size: 0.9rem;
            }
            
            .detail-value {
                font-size: 1rem;
            }
            
            .demo-title {
                font-size: 1.3rem;
            }

            .navbar {
                padding: 0.75rem 1rem;
            }

            .logo-icon {
                width: 35px;
                height: 35px;
            }
        }

        /* Utility classes for better overflow handling */
        .overflow-hidden {
            overflow: hidden;
        }

        .text-ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .break-word {
            word-wrap: break-word;
            overflow-wrap: break-word;
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

    <div class="navbar">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="logo-text">FutureBot</div>
        </div>
        <a href="javascript:history.back()" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Mentors
        </a>
    </div>

    <div class="container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-pic-container">
                    <?php if (file_exists($profile_pic) && $profile_pic !== 'uploads/default_profile.png'): ?>
                        <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" class="profile-pic">
                    <?php else: ?>
                        <div class="profile-pic-placeholder">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h1 class="profile-name"><?= htmlspecialchars($mentor['full_name'] ?? 'Unknown Mentor') ?></h1>
                <p class="profile-title"><?= htmlspecialchars($mentor['recent_profession'] ?? 'Professional Mentor') ?></p>
                <p class="profile-bio"><?= !empty($mentor['bio']) ? nl2br(htmlspecialchars($mentor['bio'])) : 'Experienced mentor dedicated to helping students achieve their academic and career goals through personalized guidance and support.' ?></p>
            </div>

            <div class="profile-details">
                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="detail-label">University</div>
                    <div class="detail-value">
                        <?php if (!empty($mentor['university'])): ?>
                            <a href="https://www.google.com/search?q=<?= urlencode($mentor['university']) ?>" target="_blank">
                                <?= htmlspecialchars($mentor['university']) ?>
                            </a>
                        <?php else: ?>
                            Not specified
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="detail-label">Subject Expertise</div>
                    <div class="detail-value"><?= !empty($mentor['subject']) ? htmlspecialchars($mentor['subject']) : 'Multiple Subjects' ?></div>
                </div>

                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div class="detail-label">Professional Background</div>
                    <div class="detail-value"><?= !empty($mentor['recent_profession']) ? htmlspecialchars($mentor['recent_profession']) : 'Industry Professional' ?></div>
                </div>

                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="detail-label">Location</div>
                    <div class="detail-value"><?= !empty($mentor['location']) ? htmlspecialchars($mentor['location']) : 'Remote' ?></div>
                </div>

                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="detail-label">Teaching Method</div>
                    <div class="detail-value">Online & Interactive</div>
                </div>

                <div class="detail-card">
                    <div class="detail-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="detail-label">Experience Level</div>
                    <div class="detail-value">Professional Mentor</div>
                </div>
            </div>

            <div class="contact-actions">
                <?php if (!empty($mentor['phone'])): ?>
                    <a href="tel:<?= htmlspecialchars($mentor['phone']) ?>" class="contact-btn phone">
                        <i class="fas fa-phone"></i> Call Mentor
                    </a>
                <?php endif; ?>
                
                <?php if (!empty($mentor['email'])): ?>
                    <a href="mailto:<?= htmlspecialchars($mentor['email']) ?>" class="contact-btn email">
                        <i class="fas fa-envelope"></i> Email Mentor
                    </a>
                <?php endif; ?>
            </div>

            <div class="demo-section">
                <h3 class="demo-title">Demo Session Available</h3>
                <button type="button" class="demo-btn" data-bs-toggle="modal" data-bs-target="#demoModal">
                    <i class="fas fa-video"></i> View Demo Details
                </button>
                
                <?php if (!empty($demo_schedule)): ?>
                    <div class="demo-info">
                        <i class="fas fa-clock"></i>
                        Next available: <?= date('l, d M Y \a\t h:i A', strtotime($demo_schedule)) ?>
                    </div>
                <?php else: ?>
                    <div class="demo-info">
                        <i class="fas fa-info-circle"></i>
                        Demo sessions available upon request
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Demo Modal -->
    <div class="modal fade" id="demoModal" tabindex="-1" aria-labelledby="demoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="demoModalLabel">
                        <i class="fas fa-video me-2"></i>Demo Session
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body">
                    <!-- Join Existing Demo -->
                    <div class="demo-info-item">
                        <span class="demo-info-label">Join Existing Demo</span>
                        <div class="demo-info-value">
                            <?php if (!empty($demo_link)): ?>
                                <a href="<?= htmlspecialchars($demo_link) ?>" target="_blank">
                                    Join Demo Session
                                </a>
                            <?php else: ?>
                                No demo link available
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Next Scheduled Demo -->
                    <div class="demo-info-item">
                        <span class="demo-info-label">Next Scheduled Demo</span>
                        <div class="demo-info-value demo-schedule">
                            <?php if (!empty($demo_schedule)): ?>
                                <?= date('l, d M Y \a\t h:i A', strtotime($demo_schedule)) ?>
                            <?php else: ?>
                                No demo scheduled
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-close-custom" data-bs-dismiss="modal">Close</button>
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

    <!-- Bootstrap & Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <script>
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to detail cards on scroll
            const detailCards = document.querySelectorAll('.detail-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });
            
            detailCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(15px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });

            // Test modal functionality
            const demoBtn = document.querySelector('.demo-btn');
            if (demoBtn) {
                demoBtn.addEventListener('click', function() {
                    console.log('Demo button clicked - modal should open');
                });
            }

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