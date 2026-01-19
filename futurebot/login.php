<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $selected_role = isset($_POST['role']) ? $_POST['role'] : null;

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, password_hash, username, role FROM users WHERE email = ? AND is_active = 1 AND is_deleted = 0");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id, $password_hash, $username, $role);
            $stmt->fetch();

            if (password_verify($password, $password_hash)) {
                if ($selected_role && $selected_role !== $role) {
                    $error = "Role mismatch. Please select the correct role.";
                } else {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $role;
                    $_SESSION['email'] = $email;

                    if ($role === 'mentor') {
                        header("Location: mentor_register_form.php");
                    } elseif ($role === 'student') {
                        header("Location: register_details.php");
                    } elseif ($role === 'admin') {
                        header("Location: dashboard.php");
                    } else {
                        header("Location: dashboard.php");
                    }
                    exit;
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No user found with that email.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login - FutureBot AI Career Path Advisor</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { 
  box-sizing: border-box; 
  margin:0; 
  padding:0; 
}
html, body {
  width: 100%;
  height: 100%;
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
nav .nav-buttons button,
nav .dropdown-btn {
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
nav .nav-buttons button:hover,
nav .dropdown-btn:hover {
  background: linear-gradient(135deg, #3a0ca3, #4361ee);
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
}
.dropdown { 
  position: relative; 
  display: inline-block; 
}
.dropdown-content {
  display: none;
  position: absolute;
  right: 0;
  background: #fff;
  min-width: 160px;
  box-shadow: 0px 8px 16px rgba(0,0,0,0.15);
  border-radius: 8px;
  z-index: 9999;
  overflow: hidden;
  margin-top: 5px;
  border: 1px solid rgba(67, 97, 238, 0.1);
}
.dropdown-content a {
  color: #2c3e50;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border-bottom: 1px solid rgba(67, 97, 238, 0.05);
}
.dropdown-content a:hover { 
  background: rgba(67, 97, 238, 0.05);
  padding-left: 20px;
  color: #4361ee;
}
.dropdown-content a:last-child {
  border-bottom: none;
}
.dropdown:hover .dropdown-content { 
  display: block; 
  animation: fadeIn 0.3s ease;
}

/* Main Content */
.main-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  margin-top: 100px;
  padding: 0;
}

.login-container {
  background: #fff;
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  width: 90%;
  max-width: 420px;
  animation: slideUp 0.8s ease-out;
  border: 1px solid rgba(67, 97, 238, 0.1);
  position: relative;
  overflow: hidden;
  margin-bottom: 40px;
}

.login-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, #4361ee, #3a0ca3);
}

h2 {
  text-align: center;
  margin-bottom: 30px;
  font-size: 28px;
  color: #2c3e50;
  font-weight: 700;
  position: relative;
}

h2::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #4361ee, #3a0ca3);
  border-radius: 2px;
}

.input-group {
  position: relative;
  margin-bottom: 20px;
}

.input-group i {
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #4361ee;
  z-index: 1;
}

input[type="email"],
input[type="password"],
select {
  width: 100%;
  padding: 12px 15px 12px 45px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: #f8f9fa;
  color: #2c3e50;
  font-size: 16px;
  transition: all 0.3s ease;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

input:focus,
select:focus {
  outline: none;
  border-color: #4361ee;
  background: #fff;
  box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

input::placeholder {
  color: #95a5a6;
}

select {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234361ee'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 15px center;
  background-size: 16px;
  background-color: #f8f9fa;
}

select option {
  color: #2c3e50;
  background: #fff;
}

button[type="submit"] {
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

button[type="submit"]:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
}

.error {
  background: rgba(231, 76, 60, 0.1);
  border-left: 4px solid #e74c3c;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 20px;
  text-align: center;
  font-weight: 600;
  word-wrap: break-word;
  color: #c0392b;
  animation: shake 0.5s ease;
}

.link { 
  margin-top: 25px; 
  text-align: center; 
  font-size: 15px;
  color: #7f8c8d;
}
.link a { 
  color: #4361ee; 
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  position: relative;
}
.link a::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 2px;
  background: #4361ee;
  transition: width 0.3s ease;
}
.link a:hover::after {
  width: 100%;
}

/* Full Screen Information Section */
.fullscreen-section {
  width: 100%;
  min-height: 100vh;
  background: #fff;
  padding: 80px 40px;
  position: relative;
  border-top: 1px solid rgba(67, 97, 238, 0.1);
}

.fullscreen-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, #4361ee, #3a0ca3);
}

.fullscreen-section h3 {
  text-align: center;
  margin-bottom: 50px;
  font-size: 2.8rem;
  color: #2c3e50;
  font-weight: 800;
  position: relative;
  line-height: 1.2;
}

.fullscreen-section h3::after {
  content: '';
  position: absolute;
  bottom: -20px;
  left: 50%;
  transform: translateX(-50%);
  width: 100px;
  height: 4px;
  background: linear-gradient(90deg, #4361ee, #3a0ca3);
  border-radius: 2px;
}

.fullscreen-section .intro-text {
  text-align: center;
  margin-bottom: 60px;
  font-size: 1.3rem;
  color: #5a6c7d;
  line-height: 1.7;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.features {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 40px;
  margin-top: 50px;
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
}

.feature {
  background: #f8f9fa;
  padding: 40px 30px;
  border-radius: 16px;
  transition: all 0.4s ease;
  border: 1px solid rgba(67, 97, 238, 0.1);
  position: relative;
  overflow: hidden;
}

.feature:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px rgba(67, 97, 238, 0.15);
}

.feature::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 6px;
  height: 100%;
  background: linear-gradient(to bottom, #4361ee, #3a0ca3);
}

.feature-header {
  display: flex;
  align-items: center;
  margin-bottom: 25px;
}

.feature-icon {
  width: 70px;
  height: 70px;
  background: linear-gradient(135deg, #4361ee, #3a0ca3);
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 20px;
  flex-shrink: 0;
  box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
}

.feature-icon i {
  color: white;
  font-size: 1.8rem;
}

.feature h4 {
  color: #2c3e50;
  font-size: 1.5rem;
  margin: 0;
  font-weight: 700;
}

.feature p {
  color: #5a6c7d;
  line-height: 1.7;
  margin-bottom: 25px;
  font-size: 1.1rem;
}

.feature-list {
  list-style: none;
  padding-left: 0;
}

.feature-list li {
  padding: 8px 0;
  color: #5a6c7d;
  position: relative;
  padding-left: 30px;
  font-size: 1rem;
}

.feature-list li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: #4361ee;
  font-weight: bold;
  font-size: 1.2rem;
}

.stats-container {
  background: linear-gradient(135deg, #4361ee, #3a0ca3);
  padding: 60px 40px;
  border-radius: 20px;
  margin-top: 80px;
  color: white;
  max-width: 1200px;
  margin-left: auto;
  margin-right: auto;
  box-shadow: 0 15px 35px rgba(67, 97, 238, 0.3);
}

.stats-container h4 {
  text-align: center;
  margin-bottom: 40px;
  font-size: 2rem;
  font-weight: 700;
}

.stats {
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
  gap: 30px;
}

.stat {
  text-align: center;
  flex: 1;
  min-width: 200px;
}

.stat-number {
  display: block;
  font-size: 3.5rem;
  font-weight: 800;
  margin-bottom: 10px;
  text-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.stat-label {
  font-size: 1.1rem;
  opacity: 0.95;
  font-weight: 600;
}

.testimonial {
  background: #f8f9fa;
  padding: 40px;
  border-radius: 20px;
  margin-top: 80px;
  border-left: 6px solid #4361ee;
  position: relative;
  max-width: 1000px;
  margin-left: auto;
  margin-right: auto;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.testimonial::before {
  content: '"';
  position: absolute;
  top: 20px;
  left: 30px;
  font-size: 5rem;
  color: rgba(67, 97, 238, 0.2);
  font-family: Georgia, serif;
  line-height: 1;
}

.testimonial p {
  font-style: italic;
  color: #5a6c7d;
  line-height: 1.8;
  margin-bottom: 25px;
  padding-left: 50px;
  font-size: 1.2rem;
}

.testimonial-author {
  display: flex;
  align-items: center;
  padding-left: 50px;
}

.author-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4361ee, #3a0ca3);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 15px;
  color: white;
  font-weight: bold;
  font-size: 1.2rem;
  box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
}

.author-info h5 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.2rem;
  font-weight: 700;
}

.author-info p {
  margin: 5px 0 0 0;
  color: #7f8c8d;
  font-size: 1rem;
  padding: 0;
}

.modal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
  backdrop-filter: blur(3px);
}
.modal-content {
  background: #fff;
  color: #2c3e50;
  padding: 30px;
  border-radius: 12px;
  width: 90%;
  max-width: 500px;
  text-align: center;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
  overflow-wrap: break-word;
  animation: modalSlideIn 0.4s ease;
  border: 1px solid rgba(67, 97, 238, 0.1);
  position: relative;
}

.modal-content::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: linear-gradient(90deg, #4361ee, #3a0ca3);
}

.modal-content h3 { 
  margin-bottom: 15px; 
  font-size: 24px;
  color: #2c3e50;
}
.modal-content p { 
  margin: 10px 0; 
  font-size: 16px;
  line-height: 1.5;
  color: #5a6c7d;
}
.close {
  margin-top: 20px;
  background: linear-gradient(135deg, #4361ee, #3a0ca3);
  border: none;
  padding: 10px 25px;
  border-radius: 6px;
  cursor: pointer;
  color: #fff;
  font-weight: 600;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
}
.close:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
}

/* Animations */
@keyframes fadeIn { 
  from { opacity: 0; } 
  to { opacity: 1; } 
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

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
  20%, 40%, 60%, 80% { transform: translateX(5px); }
}

/* Responsive Design */
@media (max-width: 1024px) {
  .fullscreen-section h3 {
    font-size: 2.3rem;
  }
  
  .features {
    grid-template-columns: 1fr;
    gap: 30px;
  }
}

@media (max-width: 768px) {
  .fullscreen-section {
    padding: 60px 20px;
  }
  
  .fullscreen-section h3 {
    font-size: 2rem;
  }
  
  .feature {
    padding: 30px 20px;
  }
  
  .feature-header {
    flex-direction: column;
    text-align: center;
  }
  
  .feature-icon {
    margin-right: 0;
    margin-bottom: 15px;
  }
  
  .stats {
    flex-direction: column;
    gap: 30px;
  }
  
  .stat-number {
    font-size: 2.8rem;
  }
  
  .testimonial {
    padding: 30px 20px;
  }
  
  .testimonial p {
    padding-left: 30px;
  }
  
  .testimonial-author {
    padding-left: 30px;
  }
}

@media (max-width: 500px) {
  nav { 
    flex-direction: column; 
    gap: 10px; 
    padding: 15px 20px;
  }
  
  .login-container {
    padding: 30px 20px;
    width: 95%;
  }
  
  h2 {
    font-size: 24px;
  }
  
  .modal-content {
    padding: 20px;
  }
  
  .fullscreen-section h3 {
    font-size: 1.8rem;
  }
  
  .fullscreen-section .intro-text {
    font-size: 1.1rem;
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
    <button onclick="location.href='index.html'"><i class="fas fa-arrow-left"></i> Back</button>
    <div class="dropdown">
      <button class="dropdown-btn"><i class="fas fa-bars"></i> Menu</button>
      <div class="dropdown-content">
        <a href="admin_login.php"><i class="fas fa-shield-alt"></i> Admin</a>
        <a href="company_login.php"><i class="fas fa-building"></i> Company</a>
        <a onclick="openModal('aboutModal')"><i class="fas fa-info-circle"></i> About</a>
        <a onclick="openModal('contactModal')"><i class="fas fa-phone-alt"></i> Contact Info</a>
      </div>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="main-content">
  <!-- Login Form -->
  <div class="login-container">
    <h2>Login to FutureBot</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="Email Address" required />
      </div>
      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required />
      </div>
      <div class="input-group">
        <i class="fas fa-user-tag"></i>
        <select name="role" required>
          <option value="" disabled selected>Select Role</option>
          <option value="student">👩‍🎓 Student</option>
          <option value="mentor">👨‍🏫 Mentor</option>
        </select>
      </div>
      <button type="submit"><i class="fas fa-sign-in-alt"></i> Sign In</button>
    </form>
    <div class="link">
      Don't have an account? <a href="register.php">Register now</a>
    </div>
  </div>

  <!-- Full Screen Information Section -->
  <div class="fullscreen-section">
    <h3>Discover Your Ideal Career Path with FutureBot</h3>
    <p class="intro-text">
      FutureBot uses advanced AI algorithms to analyze your skills, interests, and goals, providing personalized academic and career guidance to help you make informed decisions about your future. Our platform combines cutting-edge technology with expert insights to create a comprehensive career planning experience.
    </p>
    
    <div class="features">
      <div class="feature">
        <div class="feature-header">
          <div class="feature-icon">
            <i class="fas fa-brain"></i>
          </div>
          <h4>AI-Powered Analysis</h4>
        </div>
        <p>Our sophisticated machine learning algorithms analyze multiple data points to provide accurate career recommendations tailored to your unique profile.</p>
        <ul class="feature-list">
          <li>Comprehensive skill assessment and gap analysis</li>
          <li>Advanced interest and personality matching</li>
          <li>Real-time market demand forecasting</li>
          <li>Personalized learning and development paths</li>
          <li>Continuous progress tracking and optimization</li>
        </ul>
      </div>
      
      <div class="feature">
        <div class="feature-header">
          <div class="feature-icon">
            <i class="fas fa-road"></i>
          </div>
          <h4>Personalized Roadmaps</h4>
        </div>
        <p>Get step-by-step guidance tailored to your unique profile, timeline, and career aspirations with our dynamic roadmap system.</p>
        <ul class="feature-list">
          <li>Custom academic and course planning</li>
          <li>Detailed skill development timelines</li>
          <li>Internship and project recommendations</li>
          <li>Career milestone tracking and achievement</li>
          <li>Adaptive planning based on your progress</li>
        </ul>
      </div>
      
      <div class="feature">
        <div class="feature-header">
          <div class="feature-icon">
            <i class="fas fa-chart-line"></i>
          </div>
          <h4>Career Alignment</h4>
        </div>
        <p>Connect your academic journey with real-world career opportunities and industry requirements through our comprehensive matching system.</p>
        <ul class="feature-list">
          <li>In-depth industry trend analysis</li>
          <li>Salary and career growth projections</li>
          <li>Company and role compatibility matching</li>
          <li>Professional networking opportunities</li>
          <li>Industry expert mentorship programs</li>
        </ul>
      </div>
    </div>
    
    <div class="stats-container">
      <h4>Our Impact in Numbers</h4>
      <div class="stats">
        <div class="stat">
          <span class="stat-number">12,847</span>
          <span class="stat-label">Students Guided</span>
        </div>
        <div class="stat">
          <span class="stat-number">94%</span>
          <span class="stat-label">Satisfaction Rate</span>
        </div>
        <div class="stat">
          <span class="stat-number">63</span>
          <span class="stat-label">Career Paths</span>
        </div>
        <div class="stat">
          <span class="stat-number">89%</span>
          <span class="stat-label">Career Readiness</span>
        </div>
      </div>
    </div>
    
    <div class="testimonial">
      <p>"FutureBot completely transformed my academic journey. The AI recommendations helped me discover a career path I never would have considered, and now I'm pursuing my dream job in data science. The personalized roadmap made all the difference in helping me stay focused and motivated throughout my studies!"</p>
      <div class="testimonial-author">
        <div class="author-avatar">SM</div>
        <div class="author-info">
          <h5>Samiha Maisha</h5>
          <p>Computer Science Student at UIU | Future Data Scientist</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- About Modal -->
<div class="modal" id="aboutModal">
  <div class="modal-content">
    <h3>About FutureBot</h3>
    <p>FutureBot is an AI-powered academic path recommender system developed by a team of educators, career counselors, and data scientists.</p>
    <p>We help students align their academic goals with real-world career opportunities through data-driven insights and personalized guidance.</p>
    <p>Our mission is to make career planning accessible, accurate, and empowering for every student.</p>
    <button class="close" onclick="closeModal('aboutModal')">Close</button>
  </div>
</div>

<!-- Contact Modal -->
<div class="modal" id="contactModal">
  <div class="modal-content">
    <h3>Contact Information</h3>
    <p><i class="fas fa-phone"></i> <strong>Phone:</strong> +8801738915382</p>
    <p><i class="fas fa-envelope"></i> <strong>Email:</strong> support@futurebot.com</p>
    <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> 123 Education Avenue, Tech City</p>
    <p><i class="fas fa-clock"></i> <strong>Support Hours:</strong> Mon-Fri 9AM-6PM</p>
    <button class="close" onclick="closeModal('contactModal')">Close</button>
  </div>
</div>

<script>
function openModal(id) { 
  document.getElementById(id).style.display = 'flex'; 
}

function closeModal(id) { 
  document.getElementById(id).style.display = 'none'; 
}

window.onclick = function(event) {
  const modals = document.querySelectorAll('.modal');
  modals.forEach(modal => { 
    if (event.target === modal) modal.style.display = 'none'; 
  });
};

// Add some interactive effects to form inputs
document.addEventListener('DOMContentLoaded', function() {
  const inputs = document.querySelectorAll('input, select');
  
  inputs.forEach(input => {
    // Add focus effect
    input.addEventListener('focus', function() {
      this.parentElement.style.transform = 'scale(1.02)';
    });
    
    // Remove focus effect
    input.addEventListener('blur', function() {
      this.parentElement.style.transform = 'scale(1)';
    });
  });
});
</script>
</body>
</html>