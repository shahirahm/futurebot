<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle remove request
if (isset($_GET['remove_id'])) {
    $remove_id = intval($_GET['remove_id']);
    // Optional: Only allow admin or current user to remove (add your checks here)
    $delete_stmt = $conn->prepare("DELETE FROM mentor_details WHERE user_id = ?");
    $delete_stmt->bind_param("i", $remove_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    // Redirect to prevent resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Initialize variables
$mentors = [];
$error = null;

// First, let's check what columns exist in the mentor_details table
$check_columns = $conn->query("SHOW COLUMNS FROM mentor_details");
$existing_columns = [];
if ($check_columns) {
    while ($column = $check_columns->fetch_assoc()) {
        $existing_columns[] = $column['Field'];
    }
    $check_columns->free();
}




// Build query based on available columns
$columns_to_select = ["md.user_id", "md.company_name", "md.location", "md.rating", "u.full_name", "u.email"];

// Check if profile_picture exists in users table
$check_users_columns = $conn->query("SHOW COLUMNS FROM users");
$users_columns = [];
if ($check_users_columns) {
    while ($column = $check_users_columns->fetch_assoc()) {
        $users_columns[] = $column['Field'];
    }
    $check_users_columns->free();
}

if (in_array('profile_picture', $users_columns)) {
    $columns_to_select[] = "u.profile_picture";
}

if (in_array('specialization', $existing_columns)) {
    $columns_to_select[] = "md.specialization";
}

$query = "
    SELECT " . implode(", ", $columns_to_select) . "
    FROM mentor_details md
    INNER JOIN users u ON md.user_id = u.user_id
    ORDER BY md.rating DESC
";

$result = $conn->query($query);



// Check if query was successful
if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $mentors[] = $row;
        }
    }
    $result->free();
} else {
    $error = "Error fetching mentors: " . $conn->error;
    
    // Fallback: Try basic query without problematic columns
    $fallback_query = "
        SELECT md.user_id, md.company_name, md.location, md.rating, u.full_name, u.email
        FROM mentor_details md
        INNER JOIN users u ON md.user_id = u.user_id
        ORDER BY md.rating DESC
    ";
    
    $fallback_result = $conn->query($fallback_query);
    if ($fallback_result) {
        $error = null; // Clear error since fallback worked
        if ($fallback_result->num_rows > 0) {
            while ($row = $fallback_result->fetch_assoc()) {
                $mentors[] = $row;
            }
        }
        $fallback_result->free();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Suggestions - FutureBot AI Career Path Advisor</title>
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

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 90%;
        max-width: 1200px;
        margin-bottom: 40px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 15px;
        position: relative;
    }

    .page-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #4361ee, #3a0ca3);
        border-radius: 2px;
    }

    .page-title i {
        color: #4361ee;
        font-size: 2.2rem;
    }

    .back-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .back-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }

    .mentors-container {
        width: 90%;
        max-width: 1200px;
        margin-bottom: 60px;
    }

    .mentors-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 30px;
        margin-top: 20px;
    }

    .mentor-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        border: 1px solid rgba(67, 97, 238, 0.1);
        position: relative;
        overflow: hidden;
    }

    .mentor-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(67, 97, 238, 0.15);
    }

    .mentor-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #4361ee, #3a0ca3);
    }

    .mentor-header {
        padding: 25px 25px 20px;
        display: flex;
        gap: 20px;
        border-bottom: 1px solid rgba(67, 97, 238, 0.1);
        background: linear-gradient(135deg, rgba(67, 97, 238, 0.02) 0%, rgba(58, 12, 163, 0.02) 100%);
    }

    .mentor-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #4361ee;
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.3);
    }

    .mentor-info {
        flex: 1;
    }

    .mentor-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
    }

    .mentor-company {
        color: #4361ee;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.1rem;
    }

    .mentor-specialization {
        color: #5a6c7d;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(67, 97, 238, 0.08);
        padding: 6px 12px;
        border-radius: 20px;
        width: fit-content;
    }

    .mentor-rating {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
    }

    .stars {
        color: #f59e0b;
    }

    .rating-value {
        font-weight: 700;
        color: #2c3e50;
        font-size: 1.1rem;
        background: rgba(245, 158, 11, 0.1);
        padding: 4px 10px;
        border-radius: 20px;
    }

    .mentor-body {
        padding: 20px 25px;
    }

    .mentor-detail {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
        font-size: 1rem;
        color: #5a6c7d;
    }

    .mentor-detail i {
        width: 20px;
        color: #4361ee;
        font-size: 1.1rem;
    }

    .mentor-actions {
        padding: 20px 25px;
        display: flex;
        gap: 12px;
        border-top: 1px solid rgba(67, 97, 238, 0.1);
        background: rgba(67, 97, 238, 0.02);
    }

    .mentor-actions .btn {
        flex: 1;
        justify-content: center;
        padding: 12px;
        font-size: 0.95rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-outline {
        background: transparent;
        color: #4361ee;
        border: 2px solid #4361ee;
    }

    .btn-outline:hover {
        background: #4361ee;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        border: none;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 40px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 2px dashed rgba(67, 97, 238, 0.3);
    }

    .empty-state i {
        font-size: 4rem;
        color: #4361ee;
        margin-bottom: 20px;
        opacity: 0.7;
    }

    .empty-state h3 {
        font-size: 1.8rem;
        margin-bottom: 15px;
        color: #2c3e50;
        font-weight: 700;
    }

    .empty-state p {
        color: #5a6c7d;
        max-width: 500px;
        margin: 0 auto 25px;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .error-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 40px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border-left: 6px solid #e74c3c;
    }

    .error-state i {
        font-size: 4rem;
        color: #e74c3c;
        margin-bottom: 20px;
    }

    .error-state h3 {
        font-size: 1.8rem;
        margin-bottom: 15px;
        color: #2c3e50;
        font-weight: 700;
    }

    .error-state p {
        color: #5a6c7d;
        max-width: 500px;
        margin: 0 auto 25px;
        font-size: 1.1rem;
        line-height: 1.6;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }

    /* Welcome Modal */
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
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
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
        margin-bottom: 20px; 
        font-size: 1.8rem;
        color: #2c3e50;
        font-weight: 700;
    }
    .modal-content p { 
        margin: 15px 0; 
        font-size: 1.1rem;
        line-height: 1.6;
        color: #5a6c7d;
    }
    .modal-content button {
        margin-top: 20px;
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        cursor: pointer;
        color: #fff;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 20px auto 0;
    }
    .modal-content button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }

    .modal-list {
        list-style: none;
        padding-left: 0;
        margin: 20px 0;
        text-align: left;
    }

    .modal-list li {
        padding: 8px 0;
        color: #5a6c7d;
        position: relative;
        padding-left: 25px;
        font-size: 1rem;
    }

    .modal-list li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #4361ee;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(67, 97, 238, 0.1);
        border: none;
        font-size: 1.2rem;
        color: #4361ee;
        cursor: pointer;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .close-btn:hover {
        background: rgba(67, 97, 238, 0.2);
        transform: rotate(90deg);
    }

    /* Animations */
    @keyframes fadeIn { 
        from { opacity: 0; } 
        to { opacity: 1; } 
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

    /* Responsive Design */
    @media (max-width: 1024px) {
        .mentors-grid {
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
    }

    @media (max-width: 768px) {
        nav {
            padding: 15px 20px;
        }
        
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        
        .page-title {
            font-size: 2rem;
        }
        
        .mentors-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .mentor-header {
            padding: 20px;
        }
        
        .mentor-avatar {
            width: 70px;
            height: 70px;
        }
    }

    @media (max-width: 500px) {
        nav { 
            flex-direction: column; 
            gap: 10px; 
            padding: 15px 20px;
        }
        
        .page-title {
            font-size: 1.8rem;
        }
        
        .mentor-actions {
            flex-direction: column;
        }
        
        .modal-content {
            padding: 25px 20px;
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
            <div class="dropdown">
                <button class="dropdown-btn"><i class="fas fa-bars"></i> Menu</button>
                <div class="dropdown-content">
                    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                    <a href="career_suggestions.php"><i class="fas fa-graduation-cap"></i> Career Suggestions</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-users"></i>
                Mentor Suggestions
            </h1>
            <a href="career_suggestions.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Career Suggestions
            </a>
        </div>

        <div class="mentors-container">
            <div class="mentors-grid">
                <?php if ($error): ?>
                    <div class="error-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Database Error</h3>
                        <p><?= htmlspecialchars($error) ?></p>
                        <p>Please try refreshing the page or contact support if the problem persists.</p>
                        <button class="btn-primary" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt"></i>
                            Try Again
                        </button>
                    </div>
                <?php elseif (!empty($mentors)): ?>
                    <?php foreach ($mentors as $mentor): 
                        $profile_pic = !empty($mentor['profile_picture']) ? $mentor['profile_picture'] : null;
                        $specialization = isset($mentor['specialization']) ? $mentor['specialization'] : 'General Career Advice';
                    ?>
                        <div class="mentor-card">
                            <div class="mentor-header">
                                <?php if ($profile_pic): ?>
                                    <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Mentor Avatar" class="mentor-avatar">
                                <?php else: ?>
                                    <div class="mentor-avatar">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="mentor-info">
                                    <div class="mentor-name"><?= htmlspecialchars($mentor['full_name']) ?></div>
                                    <div class="mentor-company">
                                        <i class="fas fa-building"></i>
                                        <?= htmlspecialchars($mentor['company_name']) ?>
                                    </div>
                                    <div class="mentor-specialization">
                                        <i class="fas fa-tag"></i>
                                        <?= htmlspecialchars($specialization) ?>
                                    </div>
                                    <div class="mentor-rating">
                                        <div class="stars">
                                            <?php
                                            $rating = $mentor['rating'];
                                            $fullStars = floor($rating);
                                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                            
                                            for ($i = 0; $i < $fullStars; $i++) {
                                                echo '<i class="fas fa-star"></i>';
                                            }
                                            
                                            if ($hasHalfStar) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                                $fullStars++;
                                            }
                                            
                                            for ($i = $fullStars; $i < 5; $i++) {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                            ?>
                                        </div>
                                        <span class="rating-value"><?= number_format($rating, 1) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="mentor-body">
                                <div class="mentor-detail">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?= htmlspecialchars($mentor['location']) ?></span>
                                </div>
                                <div class="mentor-detail">
                                    <i class="fas fa-envelope"></i>
                                    <span><?= htmlspecialchars($mentor['email']) ?></span>
                                </div>
                            </div>
                            <div class="mentor-actions">
                                <a href="view_mentor.php?id=<?= $mentor['user_id'] ?>" class="btn btn-outline">
                                    <i class="fas fa-eye"></i>
                                    View Profile
                                </a>
                                <a href="?remove_id=<?= $mentor['user_id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this mentor from your suggestions?');">
                                    <i class="fas fa-times"></i>
                                    Remove
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>No Mentor Suggestions Available</h3>
                        <p>We couldn't find any mentor suggestions at the moment. Please check back later or update your preferences to get better matches.</p>
                        <a href="career_suggestions.php" class="btn-primary">
                            <i class="fas fa-arrow-left"></i>
                            Back to Career Suggestions
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Welcome Modal -->
    <div id="welcomeModal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal()">&times;</button>
            <h3>Welcome to Mentor Suggestions</h3>
            <p>Find experienced mentors to guide your career journey. Our mentor matching system connects you with professionals based on your interests and goals.</p>
            <ul class="modal-list">
                <li>All mentors are verified professionals</li>
                <li>Contact mentors directly via email</li>
                <li>Filter by specialization and rating</li>
                <li>Remove mentors that don't match your needs</li>
            </ul>
            <p>Start connecting with mentors who can help you achieve your career aspirations!</p>
            <button onclick="closeModal()">
                <i class="fas fa-check"></i>
                Get Started
            </button>
        </div>
    </div>

    <script>
    // Show modal on page load
    window.onload = function() {
        document.getElementById('welcomeModal').style.display = 'flex';
    };
    
    // Close modal function
    function closeModal() {
        document.getElementById('welcomeModal').style.display = 'none';
    }
    
    // Close modal if clicked outside content
    window.onclick = function(event) {
        const modal = document.getElementById('welcomeModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };

    // Add hover effects to mentor cards
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.mentor-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
    </script>
    <!-- Footer -->
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
</body>
</html>