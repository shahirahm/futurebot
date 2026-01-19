<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mentor') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle job post submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $experience = trim($_POST['experience']);
    $availability = trim($_POST['availability']);
    $fee = trim($_POST['fee']);
    $subjects = trim($_POST['subjects']);
    $contact_email = trim($_POST['contact_email']);

    if (empty($title) || empty($description) || empty($experience) || empty($availability) || 
        empty($fee) || empty($subjects) || empty($contact_email)) {
        $error = "All fields are required.";
    } elseif (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!is_numeric($fee) || $fee <= 0) {
        $error = "Please enter a valid fee amount.";
    } else {
        // Check if table exists and handle potential errors
        $stmt = $conn->prepare("INSERT INTO mentor_posts (mentor_id, title, description, experience, availability, fee, subjects, contact_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            $error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("issssdss", $user_id, $title, $description, $experience, $availability, $fee, $subjects, $contact_email);
            
            if ($stmt->execute()) {
                $success = "Job post submitted successfully! Waiting for admin approval.";
                // Clear form fields
                $_POST = array();
            } else {
                $error = "Error creating post: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Fetch mentor's existing posts
$posts = [];
$posts_stmt = $conn->prepare("SELECT post_id, title, description, experience, availability, fee, subjects, contact_email, status, created_at FROM mentor_posts WHERE mentor_id = ? ORDER BY created_at DESC");

if ($posts_stmt) {
    $posts_stmt->bind_param("i", $user_id);
    $posts_stmt->execute();
    $posts_result = $posts_stmt->get_result();
    $posts = $posts_result->fetch_all(MYSQLI_ASSOC);
    $posts_stmt->close();
} else {
    $error = "Error fetching posts: " . $conn->error;
}

// Fetch mentor profile for pre-filling form
$full_name = '';
$email = '';
$profile_stmt = $conn->prepare("SELECT full_name, email FROM users WHERE user_id = ?");

if ($profile_stmt) {
    $profile_stmt->bind_param("i", $user_id);
    $profile_stmt->execute();
    $profile_stmt->bind_result($full_name, $email);
    $profile_stmt->fetch();
    $profile_stmt->close();
} else {
    $error = "Error fetching profile: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Dashboard - FutureBot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        html, body {
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: #2c3e50;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 80px;
            line-height: 1.6;
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
        /* Footer Styles */
  footer {
    width: 100%;
    background: rgba(255, 255, 255, 0.95);
    padding: 30px 20px;
    margin-top: 50px;
    border-top: 1px solid rgba(67, 97, 238, 0.1);
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
  }

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

        /* Navbar */
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
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        nav .nav-buttons button:hover {
            background: linear-gradient(135deg, #3a0ca3, #4361ee);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px 20px;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            animation: fadeSlideIn 0.8s ease;
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        /* Dashboard Cards */
        .dashboard-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 30px;
            position: relative;
            border: 1px solid rgba(67, 97, 238, 0.1);
            animation: slideUp 0.8s ease-out;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #4361ee, #3a0ca3);
            border-radius: 16px 16px 0 0;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        .card-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #4361ee, #3a0ca3);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #f8f9fa;
            color: #2c3e50;
            font-size: 16px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #4361ee;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }

        /* Posts Section */
        .posts-container {
            grid-column: 1 / -1;
        }

        .post-item {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #4361ee;
            transition: all 0.3s ease;
        }

        .post-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .post-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .post-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }

        .status-approved {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-rejected {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .post-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .post-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #5a6c7d;
            font-size: 0.9rem;
        }

        .post-detail i {
            color: #4361ee;
            width: 16px;
        }

        .post-description {
            color: #5a6c7d;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .post-date {
            color: #95a5a6;
            font-size: 0.8rem;
            text-align: right;
        }

        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 600;
            animation: fadeInUp 0.6s ease-in-out;
            position: relative;
            z-index: 999;
        }

        .alert-danger {
            background: rgba(231, 76, 60, 0.1);
            color: #c0392b;
            border-left: 4px solid #e74c3c;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            color: #27ae60;
            border-left: 4px solid #2ecc71;
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(40px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes slideUp {
            from { 
                opacity: 0; 
                transform: translateY(30px); 
            } 
            to { 
                opacity: 1; 
                transform: translateY(0); 
            } 
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #95a5a6;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #bdc3c7;
        }

        .empty-state h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #7f8c8d;
        }

        /* Stats Container */
        .stats-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .dashboard-container {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            nav {
                padding: 15px 20px;
            }
            
            .main-content {
                padding: 20px 15px;
            }
            
            .dashboard-card {
                padding: 25px 20px;
            }
            
            .post-details {
                grid-template-columns: 1fr;
            }
            
            .circle:nth-child(2) {
                left: 75%;
            }
            
            .circle:nth-child(3) {
                left: 80%;
            }
            
            .circle:nth-child(5) {
                left: 65%;
            }
        }

        @media (max-width: 500px) {
            nav { 
                flex-direction: column; 
                gap: 10px; 
                padding: 15px 20px;
            }
            
            nav .nav-buttons {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .dashboard-card {
                padding: 20px 15px;
            }
            
            .post-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .circle {
                display: none;
            }
            
            .circle:nth-child(1) {
                display: block;
                width: 60px;
                height: 60px;
            }
            
            .circle:nth-child(4) {
                display: block;
                width: 80px;
                height: 80px;
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

<!-- Navbar -->
<nav>
    <div class="logo">
        <i class="fas fa-robot"></i>FutureBot
    </div>
    <div class="nav-buttons">
        <button onclick="location.href='home.php'"><i class="fas fa-home"></i> Home</button>
        <button onclick="location.href='mentor_profile.php'"><i class="fas fa-user"></i> Profile</button>
        <button onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
</nav>

<div class="main-content">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success" id="successMessage"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="dashboard-container">
        <!-- Create Post Form -->
        <div class="dashboard-card">
            <h2 class="card-title">Create Job Post</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="title">Job Title *</label>
                    <input type="text" name="title" class="form-input" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>" placeholder="e.g., Computer Science Tutor" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Job Description *</label>
                    <textarea name="description" class="form-textarea" placeholder="Describe the tutoring position, requirements, and what students can expect..." required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="experience">Your Experience *</label>
                    <textarea name="experience" class="form-textarea" placeholder="Describe your teaching experience, qualifications, and expertise..." required><?= isset($_POST['experience']) ? htmlspecialchars($_POST['experience']) : '' ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="subjects">Subjects/Skills *</label>
                    <input type="text" name="subjects" class="form-input" value="<?= isset($_POST['subjects']) ? htmlspecialchars($_POST['subjects']) : '' ?>" placeholder="e.g., Programming, Mathematics, Physics" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="availability">Availability *</label>
                    <input type="text" name="availability" class="form-input" value="<?= isset($_POST['availability']) ? htmlspecialchars($_POST['availability']) : '' ?>" placeholder="e.g., Weekdays 6-9 PM, Weekends 10 AM-2 PM" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="fee">Hourly Fee (USD) *</label>
                    <input type="number" name="fee" class="form-input" value="<?= isset($_POST['fee']) ? htmlspecialchars($_POST['fee']) : '' ?>" placeholder="e.g., 25" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="contact_email">Contact Email *</label>
                    <input type="email" name="contact_email" class="form-input" value="<?= isset($_POST['contact_email']) ? htmlspecialchars($_POST['contact_email']) : htmlspecialchars($email) ?>" required>
                </div>

                <button type="submit" name="create_post" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Submit for Approval
                </button>
            </form>
        </div>

        <!-- Quick Stats -->
        <div class="dashboard-card">
            <h2 class="card-title">My Stats</h2>
            <div class="stats-container">
                <div class="post-detail">
                    <i class="fas fa-file-alt"></i>
                    <span>Total Posts: <strong><?= count($posts) ?></strong></span>
                </div>
                <div class="post-detail">
                    <i class="fas fa-check-circle"></i>
                    <span>Approved: <strong><?= count(array_filter($posts, function($post) { return $post['status'] === 'approved'; })) ?></strong></span>
                </div>
                <div class="post-detail">
                    <i class="fas fa-clock"></i>
                    <span>Pending: <strong><?= count(array_filter($posts, function($post) { return $post['status'] === 'pending'; })) ?></strong></span>
                </div>
                <div class="post-detail">
                    <i class="fas fa-times-circle"></i>
                    <span>Rejected: <strong><?= count(array_filter($posts, function($post) { return $post['status'] === 'rejected'; })) ?></strong></span>
                </div>
            </div>
            
            <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid rgba(67, 97, 238, 0.1);">
                <h3 style="font-size: 1.1rem; margin-bottom: 15px; color: #2c3e50;">How It Works</h3>
                <ol style="color: #5a6c7d; padding-left: 20px; line-height: 1.6;">
                    <li>Submit your job post with all required details</li>
                    <li>Admin will review and approve your post</li>
                    <li>Once approved, students can see your post on the home page</li>
                    <li>Students will send requests through your contact email</li>
                </ol>
            </div>
        </div>

        <!-- My Posts -->
        <div class="dashboard-card posts-container">
            <h2 class="card-title">My Job Posts</h2>
            
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h3>No Job Posts Yet</h3>
                    <p>Create your first job post to start getting tutoring requests from students.</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="post-item">
                        <div class="post-header">
                            <div>
                                <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
                                <div class="post-detail">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>Fee: $<?= htmlspecialchars($post['fee']) ?>/hour</span>
                                </div>
                            </div>
                            <div class="post-status status-<?= $post['status'] ?>">
                                <?= ucfirst($post['status']) ?>
                            </div>
                        </div>
                        
                        <div class="post-details">
                            <div class="post-detail">
                                <i class="fas fa-book"></i>
                                <span>Subjects: <?= htmlspecialchars($post['subjects']) ?></span>
                            </div>
                            <div class="post-detail">
                                <i class="fas fa-clock"></i>
                                <span>Availability: <?= htmlspecialchars($post['availability']) ?></span>
                            </div>
                            <div class="post-detail">
                                <i class="fas fa-envelope"></i>
                                <span>Contact: <?= htmlspecialchars($post['contact_email']) ?></span>
                            </div>
                        </div>
                        
                        <div class="post-description">
                            <strong>Description:</strong> <?= htmlspecialchars($post['description']) ?>
                        </div>
                        
                        <div class="post-description">
                            <strong>My Experience:</strong> <?= htmlspecialchars($post['experience']) ?>
                        </div>
                        
                        <div class="post-date">
                            Posted: <?= date('M j, Y g:i A', strtotime($post['created_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
// Auto-hide success message after 5 seconds
const successMsg = document.getElementById('successMessage');
if (successMsg) {
    setTimeout(() => {
        successMsg.style.opacity = '0';
        successMsg.style.transition = 'opacity 1s ease';
        setTimeout(() => successMsg.remove(), 1000);
    }, 5000);
}

// Add some interactivity to form inputs
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-input, .form-textarea, .form-select');
    
    inputs.forEach(input => {
        // Add focus effect
        input.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        // Remove focus effect
        input.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>

</body>
</html>