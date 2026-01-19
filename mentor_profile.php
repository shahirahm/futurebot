<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$viewer_id = $_SESSION['user_id'];
$viewer_role = $_SESSION['role'];
$profile_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : $viewer_id;

// Check if viewing own profile
$is_own_profile = ($viewer_id === $profile_user_id && $viewer_role === 'mentor');

$error = '';
$success = '';

// Fetch user data for profile_user_id
$stmt = $conn->prepare("SELECT username, full_name, bio, profile_pic, role, email, phone FROM users WHERE user_id = ?");
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$stmt->bind_result($username, $full_name, $bio, $profile_pic, $role, $email, $phone);
if (!$stmt->fetch()) {
    $stmt->close();
    die("Mentor profile not found.");
}
$stmt->close();

// Additional mentor details including demo link & schedule
$university = $subject = $recent_profession = $location = $demo_link = $demo_schedule = '';

$stmt = $conn->prepare("SELECT university, subject, recent_profession, location, demo_link, demo_schedule FROM mentor_details WHERE user_id = ?");
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$stmt->bind_result($university, $subject, $recent_profession, $location, $demo_link, $demo_schedule);
$stmt->fetch();
$stmt->close();

// Handle profile update only if own profile
if ($is_own_profile && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $bio = trim($_POST['bio']);
    $university = trim($_POST['university']);
    $subject = trim($_POST['subject']);
    $recent_profession = trim($_POST['recent_profession']);
    $location = trim($_POST['location']);
    $phone = trim($_POST['phone']);
    $demo_link = trim($_POST['demo_link']);
    $demo_schedule = trim($_POST['demo_schedule']);

    // Image upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = $viewer_id . '_' . time() . '.' . $fileExtension;
            $uploadFileDir = 'uploads/profile_pics/';
            if (!is_dir($uploadFileDir)) mkdir($uploadFileDir, 0755, true);
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_pic = $dest_path;
            } else {
                $error = "There was an error uploading the profile picture.";
            }
        } else {
            $error = "Allowed file types: " . implode(', ', $allowedfileExtensions);
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, bio = ?, profile_pic = ?, phone = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $full_name, $bio, $profile_pic, $phone, $viewer_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("SELECT COUNT(*) FROM mentor_details WHERE user_id = ?");
        $stmt->bind_param("i", $viewer_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $stmt = $conn->prepare("UPDATE mentor_details SET university = ?, subject = ?, recent_profession = ?, location = ?, demo_link = ?, demo_schedule = ? WHERE user_id = ?");
            $stmt->bind_param("ssssssi", $university, $subject, $recent_profession, $location, $demo_link, $demo_schedule, $viewer_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO mentor_details (user_id, university, subject, recent_profession, location, demo_link, demo_schedule) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", $viewer_id, $university, $subject, $recent_profession, $location, $demo_link, $demo_schedule);
        }
        $stmt->execute();
        $stmt->close();

        $success = "Profile updated successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Mentor Profile - FutureBot</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
    display: flex;
    justify-content: center;
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

  .profile-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
    width: 100%;
  }

  /* Profile Card */
  .profile-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    padding: 30px;
    position: relative;
    border: 1px solid rgba(67, 97, 238, 0.1);
    animation: slideUp 0.8s ease-out;
    height: fit-content;
  }

  .profile-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    border-radius: 16px 16px 0 0;
  }

  .profile-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 25px;
  }

  .user-avatar {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid rgba(67, 97, 238, 0.1);
    margin-bottom: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
  }

  .user-name {
    font-size: 1.6rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
  }

  .user-title {
    color: #4361ee;
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 1.1rem;
  }

  .user-rating {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 15px;
  }

  .rating-stars {
    color: #ffc107;
  }

  .rating-value {
    color: #5a6c7d;
    font-weight: 600;
  }

  .profile-stats {
    display: flex;
    justify-content: space-around;
    width: 100%;
    margin: 20px 0;
    padding: 15px 0;
    border-top: 1px solid rgba(67, 97, 238, 0.1);
    border-bottom: 1px solid rgba(67, 97, 238, 0.1);
  }

  .stat {
    text-align: center;
  }

  .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #4361ee;
  }

  .stat-label {
    font-size: 0.85rem;
    color: #5a6c7d;
  }

  .profile-details {
    margin-bottom: 25px;
  }

  .detail-item {
    display: flex;
    margin-bottom: 12px;
    align-items: flex-start;
  }

  .detail-item i {
    color: #4361ee;
    margin-right: 10px;
    margin-top: 2px;
    width: 16px;
    text-align: center;
  }

  .detail-item span {
    color: #5a6c7d;
    font-size: 0.95rem;
    line-height: 1.4;
  }

  .about-section {
    margin-bottom: 25px;
  }

  .section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 10px;
    position: relative;
    padding-bottom: 8px;
  }

  .section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 30px;
    height: 2px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
  }

  .about-text {
    color: #5a6c7d;
    line-height: 1.6;
    font-size: 0.95rem;
  }

  .demo-section {
    margin-top: 20px;
    text-align: center;
    padding: 20px;
    background: rgba(67, 97, 238, 0.05);
    border-radius: 10px;
    border: 1px solid rgba(67, 97, 238, 0.1);
  }

  .demo-btn {
    display: inline-block;
    padding: 12px 24px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    margin-bottom: 10px;
  }

  .demo-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
  }

  .demo-schedule {
    color: #5a6c7d;
    font-size: 0.9rem;
  }

  .requests-btn {
    display: block;
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    text-align: center;
    margin-top: 20px;
  }

  .requests-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
  }

  /* Edit Profile Form */
  .edit-profile-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    padding: 30px;
    position: relative;
    border: 1px solid rgba(67, 97, 238, 0.1);
    animation: slideUp 0.8s ease-out 0.2s both;
  }

  .edit-profile-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    border-radius: 16px 16px 0 0;
  }

  .form-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 25px;
    position: relative;
    padding-bottom: 10px;
  }

  .form-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group.full-width {
    grid-column: 1 / -1;
  }

  .form-label {
    display: block;
    margin-bottom: 8px;
    color: #2c3e50;
    font-weight: 600;
    font-size: 0.95rem;
  }

  .form-input {
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

  .form-input:focus {
    outline: none;
    border-color: #4361ee;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
  }

  textarea.form-input {
    resize: vertical;
    min-height: 100px;
  }

  .file-input {
    padding: 10px;
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
  @media (max-width: 1024px) {
    .profile-container {
      grid-template-columns: 1fr;
      gap: 25px;
    }
  }

  @media (max-width: 768px) {
    nav {
      padding: 15px 20px;
    }
    
    .main-content {
      padding: 20px 15px;
    }
    
    .profile-card, .edit-profile-card {
      padding: 25px 20px;
    }
    
    .form-grid {
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
    
    .profile-card, .edit-profile-card {
      padding: 20px 15px;
    }
    
    .user-avatar {
      width: 120px;
      height: 120px;
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
    <button onclick="location.href='mentor_dashboard.php'"><i class="fas fa-tachometer-alt"></i> Dashboard</button>
    <button onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
    <button onclick="location.href='mentor_register_form.php'"><i class="fas fa-sign-out-alt"></i> Register</button>
  </div>
</nav>

<div class="main-content">
  <div class="profile-container">
    <!-- Profile Card -->
    <div class="profile-card">
      <div class="profile-header">
        <img src="<?= $profile_pic ? htmlspecialchars($profile_pic) : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80' ?>" alt="Profile Picture" class="user-avatar">
        <h2 class="user-name"><?= htmlspecialchars($full_name ?: $username) ?></h2>
        <div class="user-title"><?= htmlspecialchars($subject) ?> Mentor</div>
        <div class="user-rating">
          <div class="rating-stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
          </div>
          <span class="rating-value">4.7</span>
        </div>
      </div>

      <div class="profile-stats">
        <div class="stat">
          <div class="stat-value">42</div>
          <div class="stat-label">Students</div>
        </div>
        <div class="stat">
          <div class="stat-value">156</div>
          <div class="stat-label">Sessions</div>
        </div>
        <div class="stat">
          <div class="stat-value">98%</div>
          <div class="stat-label">Satisfaction</div>
        </div>
      </div>

      <div class="profile-details">
        <div class="detail-item">
          <i class="fas fa-university"></i>
          <span><strong>University:</strong> <?= htmlspecialchars($university) ?></span>
        </div>
        <div class="detail-item">
          <i class="fas fa-book"></i>
          <span><strong>Subject:</strong> <?= htmlspecialchars($subject) ?></span>
        </div>
        <div class="detail-item">
          <i class="fas fa-briefcase"></i>
          <span><strong>Profession:</strong> <?= htmlspecialchars($recent_profession) ?></span>
        </div>
        <div class="detail-item">
          <i class="fas fa-envelope"></i>
          <span><strong>Email:</strong> <?= htmlspecialchars($email) ?></span>
        </div>
        <div class="detail-item">
          <i class="fas fa-phone"></i>
          <span><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></span>
        </div>
        <div class="detail-item">
          <i class="fas fa-map-marker-alt"></i>
          <span><strong>Location:</strong> <?= htmlspecialchars($location) ?></span>
        </div>
      </div>

      <div class="about-section">
        <h3 class="section-title">About</h3>
        <p class="about-text"><?= nl2br(htmlspecialchars($bio ?: "Experienced mentor with a passion for helping students achieve their academic and career goals. Specializing in " . htmlspecialchars($subject) . " with a practical approach that combines theoretical knowledge with real-world applications.")) ?></p>
      </div>

      <?php if (!empty($demo_link)): ?>
        <div class="demo-section">
          <a href="<?= htmlspecialchars($demo_link) ?>" target="_blank" class="demo-btn">
            <i class="fas fa-video"></i> Join Demo Class
          </a>
          <?php if (!empty($demo_schedule)): ?>
            <p class="demo-schedule">
              <i class="fas fa-clock"></i> Scheduled: <?= date('d M Y, H:i', strtotime($demo_schedule)) ?>
            </p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($is_own_profile): ?>
        <a href="mentor_requests.php" class="requests-btn">
          <i class="fas fa-users"></i> View Student Requests
        </a>
      <?php endif; ?>
    </div>

    <!-- Edit Profile Form (only for own profile) -->
    <?php if ($is_own_profile): ?>
      <div class="edit-profile-card">
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
          <div class="alert alert-success" id="successMessage"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <h3 class="form-title">Edit Profile</h3>
        <form method="POST" enctype="multipart/form-data">
          <div class="form-grid">
            <div class="form-group">
              <label class="form-label" for="full_name">Full Name</label>
              <input type="text" name="full_name" class="form-input" value="<?= htmlspecialchars($full_name) ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="university">University</label>
              <input type="text" name="university" class="form-input" value="<?= htmlspecialchars($university) ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="subject">Subject</label>
              <input type="text" name="subject" class="form-input" value="<?= htmlspecialchars($subject) ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="recent_profession">Recent Profession</label>
              <input type="text" name="recent_profession" class="form-input" value="<?= htmlspecialchars($recent_profession) ?>">
            </div>

            <div class="form-group">
              <label class="form-label" for="location">Location</label>
              <input type="text" name="location" class="form-input" value="<?= htmlspecialchars($location) ?>">
            </div>

            <div class="form-group">
              <label class="form-label" for="phone">Phone</label>
              <input type="text" name="phone" class="form-input" value="<?= htmlspecialchars($phone) ?>">
            </div>

            <div class="form-group full-width">
              <label class="form-label" for="bio">Bio</label>
              <textarea name="bio" class="form-input" placeholder="Tell students about your background, teaching philosophy, and expertise..."><?= htmlspecialchars($bio) ?></textarea>
            </div>

            <div class="form-group">
              <label class="form-label" for="profile_pic">Profile Picture</label>
              <input type="file" name="profile_pic" class="form-input file-input" accept="image/*">
            </div>

            <div class="form-group">
              <label class="form-label" for="demo_link">Demo Class Link</label>
              <input type="url" name="demo_link" class="form-input" value="<?= htmlspecialchars($demo_link) ?>" placeholder="https://meet.google.com/xxx-xxxx-xxx">
            </div>

            <div class="form-group">
              <label class="form-label" for="demo_schedule">Demo Schedule</label>
              <input type="datetime-local" name="demo_schedule" class="form-input" value="<?= $demo_schedule ? date('Y-m-d\TH:i', strtotime($demo_schedule)) : '' ?>">
            </div>

            <div class="form-group full-width">
              <button type="submit" name="update_profile" class="submit-btn">
                <i class="fas fa-save"></i> Update Profile
              </button>
            </div>
          </div>
        </form>
      </div>
    <?php endif; ?>
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
  const inputs = document.querySelectorAll('.form-input');
  
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