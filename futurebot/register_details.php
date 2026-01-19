<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$success = '';
$error = '';

$skillsList = [
    "Web Development","Python", "Java", "C++", "C#", "JavaScript","PHP",
    "SQL", "UI/UX Design","AI","Laravel","Bootstrap", "Tailwind CSS",
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // FIX: Check if POST variables exist before accessing them
    $full_name = trim($_POST['full_name'] ?? '');
    $email = $_SESSION['email']; // FIX: Changed from 'user_email' to 'email'
    $institution = trim($_POST['institution'] ?? '');
    $gpa = trim($_POST['gpa'] ?? '');
    $selected_skills = $_POST['skills'] ?? [];

    if (empty($full_name) || empty($institution) || empty($gpa) || count($selected_skills) < 1) {
        $error = "Please fill all fields and select at least 1 skill.";
    } elseif (!is_numeric($gpa) || $gpa < 0 || $gpa > 4.0) {
        $error = "GPA must be between 0.0 and 4.0.";
    } else {
        $skills_str = implode(', ', $selected_skills);
        $stmt = $conn->prepare("UPDATE Users SET full_name=?, skills=?, institution=?, gpa=? WHERE email=?");
        $stmt->bind_param("sssss", $full_name, $skills_str, $institution, $gpa, $email);
        if ($stmt->execute()) {
            header("Location: career_suggestions.php");
            exit;
        } else {
            $error = "Failed to update your info. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Complete Your Profile - FutureBot</title>
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
    max-width: 1200px;
    margin-top: 100px;
    padding: 0 20px;
    flex: 1;
  }

  .profile-container {
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 600px;
    animation: slideUp 0.8s ease-out;
    border: 1px solid rgba(67, 97, 238, 0.1);
    position: relative;
    overflow: hidden;
    margin-bottom: 40px;
  }

  .profile-container::before {
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

  .form-group {
    position: relative;
    margin-bottom: 20px;
  }

  .form-group i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #4361ee;
    z-index: 1;
  }

  input[type="text"],
  input[type="number"] {
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

  input:focus {
    outline: none;
    border-color: #4361ee;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
  }

  input::placeholder {
    color: #95a5a6;
  }

  .skills-section {
    margin: 30px 0;
  }

  .skills-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .skills-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid rgba(67, 97, 238, 0.1);
  }

  .skill-item {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .skill-checkbox {
    width: 18px;
    height: 18px;
    border: 2px solid #4361ee;
    border-radius: 4px;
    cursor: pointer;
    position: relative;
    transition: all 0.3s ease;
  }

  .skill-checkbox.checked {
    background: #4361ee;
  }

  .skill-checkbox.checked::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
  }

  .skill-label {
    color: #2c3e50;
    font-size: 0.95rem;
    cursor: pointer;
    user-select: none;
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
    margin-top: 20px;
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
    border-radius: 16px;
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
  .modal-content ul {
    text-align: left;
    margin: 15px 0;
    padding-left: 20px;
  }
  .modal-content li {
    margin: 8px 0;
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

  /* Responsive Design */
  @media (max-width: 768px) {
    .skills-grid {
      grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
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
    
    .profile-container {
      padding: 30px 20px;
      width: 95%;
    }
    
    h2 {
      font-size: 24px;
    }
    
    .modal-content {
      padding: 20px;
    }
    
    .skills-grid {
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
      <button onclick="location.href='register.php'"><i class="fas fa-arrow-left"></i> Back</button>
      <button onclick="location.href='career_suggestions.php'"><i class="fas fa-briefcase"></i> Career Suggestions</button>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Profile Form -->
    <div class="profile-container">
      <h2>Complete Your Profile</h2>
      <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php elseif (!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      
      <form method="POST" onsubmit="return validateSkillLimit();">
        <div class="form-group">
          <i class="fas fa-user"></i>
          <input type="text" name="full_name" placeholder="Full Name" required />
        </div>
        
        <div class="form-group">
          <i class="fas fa-graduation-cap"></i>
          <input type="text" name="institution" placeholder="Institution Name" required />
        </div>
        
        <div class="form-group">
          <i class="fas fa-chart-line"></i>
          <input type="number" step="0.01" min="0" max="4" name="gpa" placeholder="GPA (e.g., 3.75)" required />
        </div>

        <div class="skills-section">
          <div class="skills-title">
            <i class="fas fa-code"></i>
            Select Your Skills (Choose at least 1)
          </div>
          <div class="skills-grid">
            <?php foreach ($skillsList as $skill): ?>
              <div class="skill-item">
                <div class="skill-checkbox" data-skill="<?= htmlspecialchars($skill) ?>"></div>
                <span class="skill-label"><?= htmlspecialchars($skill) ?></span>
                <input type="checkbox" name="skills[]" value="<?= htmlspecialchars($skill) ?>" style="display: none;">
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <button type="submit"><i class="fas fa-rocket"></i> Complete Profile & Get Career Suggestions</button>
      </form>
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

  <!-- Welcome Modal -->
  <div class="modal" id="welcomeModal">
    <div class="modal-content">
      <h3>🎉 Welcome to FutureBot!</h3>
      <p>Complete your profile to unlock personalized career guidance! 🚀</p>
      <ul>
        <li>🔹 <strong>Personalized Suggestions:</strong> Based on your skills and academic background</li>
        <li>🔹 <strong>Career Exploration:</strong> Discover paths matching your interests</li>
        <li>🔹 <strong>Skill Development:</strong> Get recommendations for courses and projects</li>
        <li>🔹 <strong>Mentor Matching:</strong> Connect with experienced professionals</li>
        <li>🔹 <strong>Progress Tracking:</strong> Monitor your career readiness journey</li>
      </ul>
      <button onclick="closeModal('welcomeModal')"><i class="fas fa-play"></i> Start Your Journey</button>
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

  // Custom checkbox functionality
  document.addEventListener('DOMContentLoaded', function() {
    const skillCheckboxes = document.querySelectorAll('.skill-checkbox');
    
    skillCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('click', function() {
        const skill = this.getAttribute('data-skill');
        const hiddenInput = this.parentElement.querySelector('input[type="checkbox"]');
        
        if (this.classList.contains('checked')) {
          this.classList.remove('checked');
          hiddenInput.checked = false;
        } else {
          this.classList.add('checked');
          hiddenInput.checked = true;
        }
      });
    });

    // Add focus effects to inputs
    const inputs = document.querySelectorAll('input[type="text"], input[type="number"]');
    
    inputs.forEach(input => {
      input.addEventListener('focus', function() {
        this.parentElement.style.transform = 'scale(1.02)';
      });
      
      input.addEventListener('blur', function() {
        this.parentElement.style.transform = 'scale(1)';
      });
    });

    // Show welcome modal
    openModal('welcomeModal');
  });

  function validateSkillLimit() {
    const checkboxes = document.querySelectorAll('input[name="skills[]"]:checked');
    if (checkboxes.length < 1) {
      alert("Please select at least one skill to continue.");
      return false;
    }
    return true;
  }
  </script>
</body>
</html>