<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$showSuccessPopup = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $university = trim($_POST['university']);
    $subject = trim($_POST['subject']);
    $recent_profession = trim($_POST['recent_profession']);
    $bio = trim($_POST['bio']);
    $location = trim($_POST['location']);

    if (empty($full_name) || empty($email) || empty($university) || empty($subject)) {
        $error = "Full Name, Email, University, and Subject are required.";
    } else {
        // Check if email is already used by another user
        $emailCheck = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $emailCheck->bind_param("si", $email, $user_id);
        $emailCheck->execute();
        $emailResult = $emailCheck->get_result();

        if ($emailResult->num_rows > 0) {
            $error = "This email is already registered by another user.";
        } else {
            // Update user info
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $full_name, $email, $phone, $user_id);
            $stmt->execute();
            $stmt->close();

            // Insert mentor details
            $stmt2 = $conn->prepare("INSERT INTO mentor_details (user_id, university, subject, recent_profession, bio, location) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("isssss", $user_id, $university, $subject, $recent_profession, $bio, $location);
            $stmt2->execute();
            $stmt2->close();

            // Update role
            $stmt3 = $conn->prepare("UPDATE users SET role = 'mentor' WHERE user_id = ?");
            $stmt3->bind_param("i", $user_id);
            $stmt3->execute();
            $stmt3->close();

            // Insert into mentor suggestions for students
            $suggestInsert = $conn->prepare("INSERT INTO mentor_suggestions (full_name, email, phone, university, subject, recent_profession, bio, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $suggestInsert->bind_param("ssssssss", $full_name, $email, $phone, $university, $subject, $recent_profession, $bio, $location);
            $suggestInsert->execute();
            $suggestInsert->close();

            $_SESSION['role'] = 'mentor';
            
            // Show success popup instead of redirect
            $showSuccessPopup = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Mentor Registration - FutureBot</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
   background: linear-gradient(135deg, #e6f8e8 0%, #e4f0e8 100%);
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
     background: rgba(243, 253, 246, 0.95);
    box-shadow: 0 4px 20px rgba(71, 71, 71, 0.23);
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

  .registration-container {
     background: rgba(243, 253, 246, 0.95);
    box-shadow: 0 4px 20px rgba(71, 71, 71, 0.23);
    padding: 40px;
    border-radius: 16px;
    width: 90%;
    max-width: 600px;
    animation: slideUp 0.8s ease-out;
    border: 1px solid rgba(67, 97, 238, 0.1);
    position: relative;
    overflow: hidden;
    margin-bottom: 40px;
  }

  .registration-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 0px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
  }

  h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
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

  /* Two-column grid layout for form */
  .form-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
  }

  .form-group {
    flex: 1;
    position: relative;
  }

  .input-group {
    position: relative;
    margin-bottom: 20px;
  }

  .input-group.full-width {
    grid-column: 1 / -1;
  }

  .input-group i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #4361ee;
    z-index: 1;
  }

  input[type="text"],
  input[type="email"],
  input[type="password"],
  select,
  textarea {
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
  select:focus,
  textarea:focus {
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

  textarea {
    resize: vertical;
    min-height: 120px;
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

  .error, .success {
    background: rgba(231, 76, 60, 0.1);
    border-left: 4px solid #e74c3c;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
    font-weight: 600;
    word-wrap: break-word;
    animation: shake 0.5s ease;
  }

  .success {
    background: rgba(46, 204, 113, 0.1);
    border-left: 4px solid #2ecc71;
    color: #27ae60;
  }

  .error {
    color: #c0392b;
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

  /* Modal */
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
  .modal-content button {
    margin-top: 10px;
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
  .modal-content button:hover {
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
  @media (max-width: 768px) {
    .form-row {
      flex-direction: column;
      gap: 0;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .registration-container {
      padding: 30px 20px;
      width: 95%;
    }
    
    h2 {
      font-size: 22px;
    }
    
    nav {
      flex-direction: column;
      gap: 10px;
      padding: 15px 20px;
    }
    
    nav .nav-buttons {
      width: 100%;
      justify-content: center;
    }
    
    .modal-content {
      padding: 20px;
    }
  }

  @media (max-width: 500px) {
    .registration-container {
      max-width: 100%;
      margin: 0 10px;
    }
    
    input[type="text"],
    input[type="email"],
    input[type="password"],
    select,
    textarea {
      padding: 10px 12px 10px 40px;
      font-size: 14px;
    }
    
    button[type="submit"] {
      padding: 12px;
      font-size: 14px;
    }
    
    .footer-links {
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
    <button onclick="location.href='home.php'"><i class="fas fa-home"></i> Home</button>
    <button onclick="location.href='mentor_profile.php'"><i class="fas fa-user"></i> Profile</button>
    <button onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
  </div>
</nav>

<div class="main-content">
  <div class="registration-container">
    <h2>Mentor Registration</h2>

    <?php if (isset($error) && !empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <!-- Row 1: Full Name and Email side by side -->
      <div class="form-row">
        <div class="form-group">
          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="full_name" placeholder="Full Name *" required />
          </div>
        </div>
        <div class="form-group">
          <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email Address *" required />
          </div>
        </div>
      </div>

      <!-- Row 2: Phone and University side by side -->
      <div class="form-row">
        <div class="form-group">
          <div class="input-group">
            <i class="fas fa-phone"></i>
            <input type="text" name="phone" placeholder="Phone Number (Optional)" />
          </div>
        </div>
        <div class="form-group">
          <div class="input-group">
            <i class="fas fa-university"></i>
            <input type="text" name="university" placeholder="University *" required />
          </div>
        </div>
      </div>

      <!-- Row 3: Subject and Recent Profession side by side -->
      <div class="form-row">
        <div class="form-group">
          <div class="input-group">
            <i class="fas fa-book"></i>
            <input type="text" name="subject" placeholder="Subject/Specialization *" required />
          </div>
        </div>
        <div class="form-group">
          <div class="input-group">
            <i class="fas fa-briefcase"></i>
            <input type="text" name="recent_profession" placeholder="Recent Profession" />
          </div>
        </div>
      </div>

      <!-- Row 4: Location (full width) -->
      <div class="input-group">
        <i class="fas fa-map-marker-alt"></i>
        <input type="text" name="location" placeholder="Location (City, Area, Online, etc.)" />
      </div>

      <!-- Row 5: Bio (full width) -->
      <div class="input-group">
        <textarea name="bio" placeholder="Tell us about yourself, achievements, and mission..."></textarea>
      </div>

      <button type="submit"><i class="fas fa-user-plus"></i> Complete Registration</button>
    </form>
  </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal">
  <div class="modal-content">
    <h3>Registration Successful!</h3>
    <p>Welcome to FutureBot as a Mentor 🎉</p>
    <p>Your profile is now active and students can find you.</p>
    <button class="close-btn" onclick="closeModal()"><i class="fas fa-user"></i> Go to Profile</button>
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
function closeModal() {
    document.getElementById('successModal').style.display = 'none';
    window.location.href = 'mentor_profile.php';
}

// Show popup if registration successful
<?php if (isset($showSuccessPopup) && $showSuccessPopup): ?>
    document.getElementById('successModal').style.display = 'flex';
<?php endif; ?>

// Add some interactivity to form inputs
document.addEventListener('DOMContentLoaded', function() {
  const inputs = document.querySelectorAll('input, textarea');
  
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