<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$user_email = $_SESSION['email'];
$user_data = [];
$enrolled_courses = [];
$purchased_books = [];
$upcoming_webinars = [];
$user_performance = [];
$recommended_courses = [];

// Initialize success/error messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

// Fetch user data with error handling
$sql = "SELECT full_name, skills, institution, gpa FROM Users WHERE email = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $user_data = $result->fetch_assoc();
    if (!$user_data) {
        // If user exists in session but not in database, redirect to complete profile
        header("Location: register_details.php");
        exit;
    }
} else {
    $error = "Error fetching user data: " . $conn->error;
}
$stmt->close();

// Fetch enrolled courses with progress
$sql = "SELECT c.course_id, c.title, c.instructor, c.duration, c.level, uc.progress 
        FROM User_Courses uc 
        JOIN Courses c ON uc.course_id = c.course_id 
        WHERE uc.user_email = ?
        ORDER BY uc.enrolled_at DESC 
        LIMIT 5";
        
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $enrolled_courses[] = $row;
    }
    $stmt->close();
}

// Fetch purchased books
$sql = "SELECT b.book_id, b.title, b.author, b.price 
        FROM User_Books ub 
        JOIN Books b ON ub.book_id = b.book_id 
        WHERE ub.user_email = ? 
        ORDER BY ub.purchased_at DESC 
        LIMIT 5";
        
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $purchased_books[] = $row;
    }
    $stmt->close();
}

// Fetch upcoming webinars
$sql = "SELECT w.webinar_id, w.title, w.instructor, w.description, w.scheduled_at, w.duration 
        FROM Webinars w 
        WHERE w.scheduled_at > NOW() 
        ORDER BY w.scheduled_at ASC 
        LIMIT 5";
        
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Check if user is already registered
        $check_sql = "SELECT * FROM User_Webinars WHERE user_email = ? AND webinar_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $user_email, $row['webinar_id']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row['is_registered'] = $check_result->num_rows > 0;
        $check_stmt->close();
        
        $upcoming_webinars[] = $row;
    }
    $stmt->close();
}

// Fetch weekly performance data
$sql = "SELECT DATE_FORMAT(date, '%a') as day, score 
        FROM User_Performance 
        WHERE user_email = ? AND date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
        ORDER BY date ASC";
        
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $user_performance[] = $row;
    }
    $stmt->close();
}

// Fetch recommended courses (not enrolled)
$sql = "SELECT c.course_id, c.title, c.instructor, c.level, c.duration, c.price 
        FROM Courses c 
        WHERE c.course_id NOT IN (
            SELECT course_id FROM User_Courses WHERE user_email = ?
        )
        ORDER BY RAND() 
        LIMIT 3";
        
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recommended_courses[] = $row;
    }
    $stmt->close();
}

// Calculate user statistics
$total_courses = 0;
$total_books = 0;
$completion_rate = 0;
$skills_learned = 0;

// Total courses
$sql = "SELECT COUNT(*) as total FROM User_Courses WHERE user_email = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_courses = $result->fetch_assoc()['total'] ?? 0;
    $stmt->close();
}

// Total books
$sql = "SELECT COUNT(*) as total FROM User_Books WHERE user_email = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_books = $result->fetch_assoc()['total'] ?? 0;
    $stmt->close();
}

// Completion rate
$sql = "SELECT AVG(progress) as rate FROM User_Courses WHERE user_email = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $completion_rate = $result->fetch_assoc()['rate'] ?? 0;
    $stmt->close();
}

// Skills learned
if (!empty($user_data['skills'])) {
    $skills_learned = count(explode(', ', $user_data['skills']));
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - FutureBot</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
  * { 
    box-sizing: border-box; 
    margin:0; 
    padding:0; 
  }
  html, body {
    width: 100%;
    min-height: 100vh;
    overflow-x: hidden;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
    color: #2c3e50;
    display: flex;
    flex-direction: column;
    align-items: center;
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

  /* Main Content */
  .main-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 1400px;
    margin-top: 100px;
    padding: 0 20px;
    flex: 1;
  }

  .dashboard-container {
    width: 100%;
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 20px;
    margin-bottom: 40px;
  }

  .dashboard-card {
    background: #fff;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(67, 97, 238, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
  }

  .dashboard-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
  }

  .dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }

  .dashboard-header h2 {
    font-size: 22px;
    color: #2c3e50;
    font-weight: 700;
    margin: 0;
  }

  .dashboard-header h2::after {
    display: none;
  }

  .dashboard-header .view-all {
    color: #4361ee;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
  }

  .dashboard-header .view-all:hover {
    text-decoration: underline;
  }

  .user-greeting {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #2c3e50;
  }

  .user-greeting span {
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .user-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 20px;
  }

  .stat-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid rgba(67, 97, 238, 0.1);
  }

  .stat-value {
    font-size: 24px;
    font-weight: 700;
    color: #4361ee;
    margin-bottom: 5px;
  }

  .stat-label {
    font-size: 14px;
    color: #7f8c8d;
  }

  .webinar-card {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(67, 97, 238, 0.1);
  }

  .webinar-card:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }

  .webinar-img {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
  }

  .webinar-content {
    flex: 1;
  }

  .webinar-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #2c3e50;
  }

  .webinar-instructor {
    font-size: 14px;
    color: #7f8c8d;
    margin-bottom: 10px;
  }

  .webinar-actions {
    display: flex;
    gap: 10px;
  }

  .btn {
    padding: 8px 15px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .btn-primary {
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
  }

  .btn-outline {
    background: transparent;
    border: 1px solid #4361ee;
    color: #4361ee;
  }

  .btn-outline:hover {
    background: rgba(67, 97, 238, 0.05);
  }

  .btn-disabled {
    background: #95a5a6 !important;
    cursor: not-allowed !important;
    transform: none !important;
    box-shadow: none !important;
  }
  
  .btn-disabled:hover {
    transform: none !important;
    box-shadow: none !important;
  }

  .progress-card {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(67, 97, 238, 0.1);
  }

  .progress-card:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }

  .progress-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
  }

  .progress-title {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
  }

  .progress-percent {
    font-size: 14px;
    font-weight: 600;
    color: #4361ee;
  }

  .progress-bar {
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
  }

  .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    border-radius: 4px;
    transition: width 0.5s ease;
  }

  .book-card {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(67, 97, 238, 0.1);
  }

  .book-card:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }

  .book-img {
    width: 60px;
    height: 80px;
    border-radius: 8px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    flex-shrink: 0;
  }

  .book-content {
    flex: 1;
  }

  .book-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #2c3e50;
  }

  .book-author {
    font-size: 14px;
    color: #7f8c8d;
    margin-bottom: 10px;
  }

  .book-price {
    font-size: 16px;
    font-weight: 700;
    color: #4361ee;
  }

  .performance-chart {
    height: 200px;
    display: flex;
    align-items: flex-end;
    gap: 10px;
    margin-top: 20px;
  }

  .chart-bar {
    flex: 1;
    background: linear-gradient(to top, #4361ee, #3a0ca3);
    border-radius: 4px 4px 0 0;
    position: relative;
    transition: all 0.3s ease;
  }

  .chart-bar:hover {
    opacity: 0.8;
    transform: scale(1.05);
  }

  .chart-label {
    position: absolute;
    bottom: -25px;
    left: 0;
    width: 100%;
    text-align: center;
    font-size: 12px;
    color: #7f8c8d;
  }

  .chart-value {
    position: absolute;
    top: -25px;
    left: 0;
    width: 100%;
    text-align: center;
    font-size: 12px;
    font-weight: 600;
    color: #4361ee;
  }

  /* Success/Error Messages */
  .success-message, .error-message {
    padding: 15px;
    margin: 20px 0;
    border-radius: 8px;
    font-weight: 600;
    text-align: center;
    width: 100%;
    max-width: 1400px;
  }
  
  .success-message {
    background: rgba(46, 204, 113, 0.1);
    border: 1px solid #2ecc71;
    color: #27ae60;
  }
  
  .error-message {
    background: rgba(231, 76, 60, 0.1);
    border: 1px solid #e74c3c;
    color: #c0392b;
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

  /* Responsive Design */
  @media (max-width: 1200px) {
    .dashboard-container {
      grid-template-columns: 1fr 1fr;
    }
  }

  @media (max-width: 768px) {
    .dashboard-container {
      grid-template-columns: 1fr;
    }
    
    .user-stats {
      grid-template-columns: 1fr 1fr;
    }
    
    .footer-links {
      gap: 20px;
    }
    
    .footer-content {
      text-align: center;
    }
  }

  @media (max-width: 500px) {
    nav { 
      flex-direction: column; 
      gap: 10px; 
      padding: 15px 20px;
    }
    
    .dashboard-card {
      padding: 20px 15px;
    }
    
    .webinar-card, .book-card {
      flex-direction: column;
    }
    
    .webinar-img, .book-img {
      align-self: center;
    }
    
    .user-stats {
      grid-template-columns: 1fr;
    }
    
    .footer-links {
      flex-direction: column;
      gap: 15px;
    }
    
    .footer-social {
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

  <!-- Navbar -->
  <nav>
    <div class="logo">
      <i class="fas fa-robot"></i>FutureBot
    </div>
    <div class="nav-buttons">
      <button onclick="location.href='career_suggestions.php'"><i class="fas fa-briefcase"></i> Career Suggestions</button>
      <button onclick="location.href='profile.php'"><i class="fas fa-user"></i> My Profile</button>
      <button onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Display success/error messages -->
    <?php if (!empty($success)): ?>
      <div class="success-message"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <!-- Dashboard -->
    <div class="dashboard-container">
      <!-- User Welcome Card -->
      <div class="dashboard-card" style="grid-column: span 3;">
        <div class="user-greeting">Hi, <span><?= htmlspecialchars($user_data['full_name'] ?? 'User') ?>!</span> Welcome to your FutureBot Dashboard</div>
        <div class="user-stats">
          <div class="stat-card">
            <div class="stat-value"><?= $total_courses ?></div>
            <div class="stat-label">Courses Enrolled</div>
          </div>
          <div class="stat-card">
            <div class="stat-value"><?= $total_books ?></div>
            <div class="stat-label">Books Purchased</div>
          </div>
          <div class="stat-card">
            <div class="stat-value"><?= round($completion_rate) ?>%</div>
            <div class="stat-label">Completion Rate</div>
          </div>
          <div class="stat-card">
            <div class="stat-value"><?= $skills_learned ?></div>
            <div class="stat-label">Skills Learned</div>
          </div>
        </div>
      </div>

      <!-- Upcoming Webinars -->
      <div class="dashboard-card">
        <div class="dashboard-header">
          <h2>Upcoming Webinars</h2>
          <a href="webinars.php" class="view-all">View All</a>
        </div>
        
        <?php if (empty($upcoming_webinars)): ?>
          <p style="text-align: center; color: #7f8c8d; padding: 20px;">No upcoming webinars</p>
        <?php else: ?>
          <?php foreach ($upcoming_webinars as $webinar): ?>
            <div class="webinar-card">
              <div class="webinar-img">
                <i class="fas fa-chalkboard-teacher"></i>
              </div>
              <div class="webinar-content">
                <div class="webinar-title"><?= htmlspecialchars($webinar['title']) ?></div>
                <div class="webinar-instructor">by <?= htmlspecialchars($webinar['instructor']) ?></div>
                <div class="webinar-actions">
                  <?php if ($webinar['is_registered']): ?>
                    <button class="btn btn-disabled" disabled><i class="fas fa-check"></i> Registered</button>
                  <?php else: ?>
                    <form method="POST" action="register_webinar.php" style="display: inline;">
                      <input type="hidden" name="webinar_id" value="<?= $webinar['webinar_id'] ?>">
                      <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Register</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Continue Learning -->
      <div class="dashboard-card">
        <div class="dashboard-header">
          <h2>Continue Learning</h2>
          <a href="my_courses.php" class="view-all">View All</a>
        </div>
        
        <?php if (empty($enrolled_courses)): ?>
          <p style="text-align: center; color: #7f8c8d; padding: 20px;">No courses enrolled yet</p>
        <?php else: ?>
          <?php foreach ($enrolled_courses as $course): ?>
            <div class="progress-card">
              <div class="progress-header">
                <div class="progress-title"><?= htmlspecialchars($course['title']) ?></div>
                <div class="progress-percent"><?= $course['progress'] ?>%</div>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $course['progress'] ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Purchased Books -->
      <div class="dashboard-card">
        <div class="dashboard-header">
          <h2>My Books</h2>
          <a href="my_books.php" class="view-all">View All</a>
        </div>
        
        <?php if (empty($purchased_books)): ?>
          <p style="text-align: center; color: #7f8c8d; padding: 20px;">No books purchased yet</p>
        <?php else: ?>
          <?php foreach ($purchased_books as $book): ?>
            <div class="book-card">
              <div class="book-img">
                <i class="fas fa-book"></i>
              </div>
              <div class="book-content">
                <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
                <div class="book-author">by <?= htmlspecialchars($book['author']) ?></div>
                <div class="book-price">$<?= $book['price'] ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Weekly Performance -->
      <div class="dashboard-card">
        <div class="dashboard-header">
          <h2>Weekly Performance</h2>
          <a href="performance.php" class="view-all">View Details</a>
        </div>
        
        <div class="performance-chart">
          <?php
          $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
          $performance_data = [];
          
          // Create associative array for performance data
          foreach ($user_performance as $perf) {
              $performance_data[$perf['day']] = $perf['score'];
          }
          
          foreach ($days as $day): 
              $score = $performance_data[$day] ?? 0;
              $height = $score > 0 ? $score : 10; // Minimum height for visual
          ?>
            <div class="chart-bar" style="height: <?= $height ?>%">
              <div class="chart-value"><?= $score ?>%</div>
              <div class="chart-label"><?= $day ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Recommended Courses -->
      <div class="dashboard-card">
        <div class="dashboard-header">
          <h2>Recommended For You</h2>
          <a href="courses.php" class="view-all">View All</a>
        </div>
        
        <?php if (empty($recommended_courses)): ?>
          <p style="text-align: center; color: #7f8c8d; padding: 20px;">No recommended courses</p>
        <?php else: ?>
          <?php foreach ($recommended_courses as $course): ?>
            <div class="webinar-card">
              <div class="webinar-img" style="background: linear-gradient(135deg, #ff6b6b, #ee5a24);">
                <i class="fas fa-palette"></i>
              </div>
              <div class="webinar-content">
                <div class="webinar-title"><?= htmlspecialchars($course['title']) ?></div>
                <div class="webinar-instructor">by <?= htmlspecialchars($course['instructor']) ?></div>
                <div class="webinar-actions">
                  <form method="POST" action="enroll_course.php" style="display: inline;">
                    <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-play"></i> Enroll Now</button>
                  </form>
                </div>
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
        <a href="career_suggestions.php">Career Suggestions</a>
        <a href="courses.php">Courses</a>
        <a href="books.php">Books</a>
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
  // Add interactivity to dashboard elements
  document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to dashboard cards
    const cards = document.querySelectorAll('.dashboard-card');
    cards.forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.boxShadow = '0 15px 30px rgba(0, 0, 0, 0.12)';
      });
      
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.08)';
      });
    });

    // Add animation to progress bars
    const progressBars = document.querySelectorAll('.progress-fill');
    progressBars.forEach(bar => {
      const width = bar.style.width;
      bar.style.width = '0';
      setTimeout(() => {
        bar.style.width = width;
      }, 300);
    });

    // Add animation to chart bars
    const chartBars = document.querySelectorAll('.chart-bar');
    chartBars.forEach(bar => {
      const height = bar.style.height;
      bar.style.height = '0';
      setTimeout(() => {
        bar.style.height = height;
      }, 500);
    });

    // Handle form submissions with confirmation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
      form.addEventListener('submit', function(e) {
        const button = this.querySelector('button[type="submit"]');
        if (button && !button.disabled) {
          button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
          button.disabled = true;
        }
      });
    });
  });
  </script>
</body>
</html>