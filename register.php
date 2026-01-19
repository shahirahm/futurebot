<?php
session_start();
require_once 'db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($role)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (!in_array($role, ['student', 'mentor'])) {
        $error = "Invalid role selected.";
    } else {
        $checkStmt = $conn->prepare("SELECT user_id FROM Users WHERE username = ? OR email = ?");
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $conn->prepare("INSERT INTO Users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("ssss", $username, $email, $hashedPassword, $role);
            if ($insertStmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['username'] = $username;
                $_SESSION['user_email'] = $email;
                $_SESSION['role'] = $role;

                // Redirect all users to register_details.php after registration
                header("Location: register_details.php");
                exit;
            } else {
                $error = "Something went wrong. Try again.";
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - FutureBot AI Career Path Advisor</title>
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

  .registration-container {
    background: #fff;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 480px;
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
    font-size: 20x;
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

  input[type="text"],
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

  /* Full Screen Registration Guide Section */
  .fullscreen-guide {
    width: 100%;
    min-height: 50vh;
    background: #fff;
    padding: 50px 40px;
    position: relative;
    border-top: 1px solid rgba(67, 97, 238, 0.1);
  }

  .fullscreen-guide::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
  }

  .fullscreen-guide h3 {
    text-align: center;
    margin-bottom: 50px;
    font-size: 2.8rem;
    color: #2c3e50;
    font-weight: 800;
    position: relative;
    line-height: 1.2;
  }

  .fullscreen-guide h3::after {
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

  .fullscreen-guide .intro-text {
    text-align: center;
    margin-bottom: 60px;
    font-size: 1.3rem;
    color: #5a6c7d;
    line-height: 1.7;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
  }

  .steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 10px;
    margin-top: 50px;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
  }

  .step {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 16px;
    transition: all 0.4s ease;
    border: 1px solid rgba(67, 97, 238, 0.1);
    position: relative;
    overflow: hidden;
  }

  .step:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(67, 97, 238, 0.15);
  }

  .step::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 6px;
    height: 90%;
    background: linear-gradient(to bottom, #4361ee, #3a0ca3);
  }

  .step-number {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 25px;
    color: white;
    font-weight: bold;
    font-size: 1.8rem;
    box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
  }

  .step h4 {
    text-align: center;
    margin-bottom: 20px;
    color: #2c3e50;
    font-size: 1.5rem;
    font-weight: 700;
  }

  .step p {
    text-align: center;
    color: #5a6c7d;
    line-height: 1.7;
    font-size: 1.1rem;
  }

  .role-info {
    background: #f8f9fa;
    padding: 50px;
    border-radius: 20px;
    margin-top: 80px;
    border: 1px solid rgba(67, 97, 238, 0.1);
    max-width: 1000px;
    margin-left: auto;
    margin-right: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  }

  .role-info h4 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 40px;
    font-size: 2rem;
    font-weight: 700;
    position: relative;
  }

  .role-info h4::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    border-radius: 2px;
  }

  .role-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
  }

  .role-card {
    background: #fff;
    padding: 40px 30px;
    border-radius: 16px;
    border-left: 6px solid #4361ee;
    transition: all 0.3s ease;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
  }

  .role-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(67, 97, 238, 0.15);
  }

  .role-card h5 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
  }

  .role-card p {
    color: #5a6c7d;
    font-size: 1.1rem;
    line-height: 1.7;
    text-align: center;
  }

  .benefits-list {
    list-style: none;
    padding-left: 0;
    margin-top: 20px;
  }

  .benefits-list li {
    padding: 8px 0;
    color: #5a6c7d;
    position: relative;
    padding-left: 25px;
    font-size: 1rem;
  }

  .benefits-list li::before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #4361ee;
    font-weight: bold;
    font-size: 1.1rem;
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
  .site-footer {
    width: 100%;
    background: #2c3e50;
    color: #fff;
    padding: 40px 20px 20px;
    margin-top: 40px;
    border-top: 4px solid #4361ee;
  }
  
  .footer-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-bottom: 30px;
  }
  
  .footer-section h4 {
    font-size: 1.3rem;
    margin-bottom: 20px;
    color: #ecf0f1;
    position: relative;
    padding-bottom: 10px;
  }
  
  .footer-section h4::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background: #4361ee;
    border-radius: 2px;
  }
  
  .footer-section p {
    color: #bdc3c7;
    line-height: 1.6;
    margin-bottom: 15px;
  }
  
  .contact-info p {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
  }
  
  .contact-info i {
    margin-right: 10px;
    color: #4361ee;
    width: 20px;
  }
  
  .tech-stack {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 15px;
  }
  
  .tech-badge {
    background: rgba(67, 97, 238, 0.2);
    color: #ecf0f1;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.85rem;
    border: 1px solid rgba(67, 97, 238, 0.3);
  }
  
  .social-links {
    display: flex;
    gap: 15px;
    margin-top: 15px;
  }
  
  .social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: #fff;
    transition: all 0.3s ease;
  }
  
  .social-link:hover {
    background: #4361ee;
    transform: translateY(-3px);
  }
  
  .footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: #95a5a6;
    font-size: 0.9rem;
  }
  
  /* Responsive Design */
  @media (max-width: 1024px) {
    .fullscreen-guide h3 {
      font-size: 2.3rem;
    }
    
    .steps {
      grid-template-columns: 1fr;
      gap: 30px;
    }
  }

  @media (max-width: 768px) {
    .fullscreen-guide {
      padding: 60px 20px;
    }
    
    .fullscreen-guide h3 {
      font-size: 2rem;
    }
    
    .step {
      padding: 30px 20px;
    }
    
    .role-info {
      padding: 30px 20px;
    }
    
    .role-details {
      grid-template-columns: 1fr;
      gap: 30px;
    }
    
    .step-number {
      width: 60px;
      height: 60px;
      font-size: 1.5rem;
    }
    
    .footer-container {
      grid-template-columns: 1fr;
      gap: 25px;
    }
    
    .footer-section h4 {
      font-size: 1.2rem;
    }
  }

  @media (max-width: 500px) {
    nav { 
      flex-direction: column; 
      gap: 10px; 
      padding: 15px 20px;
    }
    
    .registration-container {
      padding: 30px 20px;
      width: 95%;
    }
    
    h2 {
      font-size: 24px;
    }
    
    .modal-content {
      padding: 20px;
    }
    
    .fullscreen-guide h3 {
      font-size: 1.8rem;
    }
    
    .fullscreen-guide .intro-text {
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
          <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
          <a href="admin_login.php"><i class="fas fa-shield-alt"></i> Admin</a>
          <a onclick="openModal('companyModal')"><i class="fas fa-building"></i> Company</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Registration Form -->
    <div class="registration-container">
      <h2>Join FutureBot Community</h2>
      <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php elseif (!empty($success)): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <form method="POST" action="">
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="username" placeholder="Full Name" required />
        </div>
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" placeholder="Email Address" required />
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Password" required />
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="confirm_password" placeholder="Confirm Password" required />
        </div>
        <div class="input-group">
          <i class="fas fa-user-tag"></i>
          <select name="role" required>
            <option value="" disabled selected>Select Role</option>
            <option value="student">👩‍🎓 Student</option>
            <option value="mentor">👨‍🏫 Mentor</option>
          </select>
        </div>
        <button type="submit"><i class="fas fa-user-plus"></i> Create Account</button>
      </form>
      <div class="link">
        Already have an account? <a href="login.php">Sign in here</a>
      </div>
    </div>

    <!-- Full Screen Registration Guide Section -->
    <div class="fullscreen-guide">
      <h3>How to Register with FutureBot</h3>
      <p class="intro-text">
        Follow these simple steps to create your FutureBot account and start your journey towards discovering your ideal career path. Our registration process is designed to be quick, secure, and user-friendly.
      </p>
      
      <div class="steps">
        <div class="step">
          <div class="step-number">1</div>
          <h4>Fill Personal Details</h4>
          <p>Enter your full name and a valid email address that you have access to. This information helps us personalize your experience and keep your account secure.</p>
        </div>
        
        <div class="step">
          <div class="step-number">2</div>
          <h4>Create Secure Password</h4>
          <p>Choose a strong password with at least 6 characters. Make sure to confirm your password to avoid any typing errors. We recommend using a mix of letters, numbers, and symbols.</p>
        </div>
        
        <div class="step">
          <div class="step-number">3</div>
          <h4>Select Your Role</h4>
          <p>Choose whether you're a Student seeking guidance or a Mentor providing expertise. This selection determines the features and tools available to you.</p>
        </div>
        
        <div class="step">
          <div class="step-number">4</div>
          <h4>Complete Registration</h4>
          <p>Click "Create Account" and you'll be redirected to complete your profile setup. You'll then have access to all FutureBot features tailored to your selected role.</p>
        </div>
      </div>
    </div>
  </div>
    
  <!-- Company Modal -->
  <div class="modal" id="companyModal">
    <div class="modal-content">
      <h3>Company Portal</h3>
      <p>Access the company portal to connect with talented students and mentors.</p>
      <button onclick="location.href='login.php'"><i class="fas fa-sign-in-alt"></i> I Have an Account</button>
      <button onclick="location.href='company_register.php'"><i class="fas fa-user-plus"></i> Create Company Account</button>
      <button onclick="closeModal('companyModal')" style="background: #95a5a6;"><i class="fas fa-times"></i> Close</button>
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