<?php
session_start();
require_once 'db.php';

// Fetch approved mentor posts
$mentor_posts = [];
try {
    $mentor_posts_query = "
        SELECT 
            mp.*, 
            u.full_name as mentor_name,
            u.email as mentor_email
        FROM mentor_posts mp
        JOIN users u ON mp.mentor_id = u.user_id
        WHERE mp.status = 'approved'
        ORDER BY mp.created_at DESC
        LIMIT 12
    ";
    
    $mentor_posts_result = $conn->query($mentor_posts_query);
    if ($mentor_posts_result) {
        $mentor_posts = $mentor_posts_result->fetch_all(MYSQLI_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error fetching mentor posts: " . $e->getMessage());
}

// Fetch approved internship posts
$internship_posts = [];
try {
    $internship_posts_query = "
        SELECT 
            jp.id, 
            jp.title, 
            jp.description, 
            jp.created_at,
            c.company_name
        FROM job_posts jp
        INNER JOIN companies c ON jp.user_id = c.id
        WHERE jp.approved = 1 AND jp.type = 'internship'
        ORDER BY jp.created_at DESC
        LIMIT 12
    ";
    
    $internship_posts_result = $conn->query($internship_posts_query);
    if ($internship_posts_result) {
        while ($row = $internship_posts_result->fetch_assoc()) {
            $internship_posts[] = $row;
        }
    }
} catch (Exception $e) {
    error_log("Error fetching internship posts: " . $e->getMessage());
}

// Fetch approved job posts
$job_posts = [];
try {
    $job_posts_query = "
        SELECT 
            jp.id, 
            jp.title, 
            jp.description, 
            jp.created_at,
            c.company_name
        FROM job_posts jp
        INNER JOIN companies c ON jp.user_id = c.id
        WHERE jp.approved = 1 AND jp.type = 'job'
        ORDER BY jp.created_at DESC
        LIMIT 12
    ";
    
    $job_posts_result = $conn->query($job_posts_query);
    if ($job_posts_result) {
        while ($row = $job_posts_result->fetch_assoc()) {
            $job_posts[] = $row;
        }
    }
} catch (Exception $e) {
    error_log("Error fetching job posts: " . $e->getMessage());
}

// Fetch approved student projects
$student_projects = [];
try {
    $student_projects_query = "
        SELECT 
            id,
            title,
            description,
            skills_used,
            image_path,
            user_email,
            submitted_at
        FROM student_projects 
        WHERE status = 'approved'
        ORDER BY submitted_at DESC
        LIMIT 12
    ";
    
    $student_projects_result = $conn->query($student_projects_query);
    if ($student_projects_result) {
        while ($row = $student_projects_result->fetch_assoc()) {
            $student_projects[] = $row;
        }
    }
} catch (Exception $e) {
    error_log("Error fetching student projects: " . $e->getMessage());
}

// Handle send request
$request_error = '';
$request_success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    if (!isset($_SESSION['user_id'])) {
        $request_error = "Please login to send a request.";
    } else {
        $post_id = $_POST['post_id'];
        $student_id = $_SESSION['user_id'];
        $message = trim($_POST['message']);
        
        if (empty($message)) {
            $request_error = "Please write a message to the mentor.";
        } else {
            // Check if request already exists
            $check_stmt = $conn->prepare("SELECT id FROM mentor_requests WHERE student_id = ? AND post_id = ?");
            $check_stmt->bind_param("ii", $student_id, $post_id);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                $request_error = "You have already sent a request for this post.";
            } else {
                // Insert new request
                $stmt = $conn->prepare("INSERT INTO mentor_requests (student_id, post_id, message, status) VALUES (?, ?, ?, 'pending')");
                $stmt->bind_param("iis", $student_id, $post_id, $message);
                
                if ($stmt->execute()) {
                    $request_success = "Your request has been sent successfully! The mentor will contact you soon.";
                } else {
                    $request_error = "Error sending request. Please try again.";
                }
                $stmt->close();
            }
            $check_stmt->close();
        }
    }
}

// Create tables if they don't exist (for backward compatibility)
$tables_sql = [
    "CREATE TABLE IF NOT EXISTS mentor_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        post_id INT NOT NULL,
        message TEXT NOT NULL,
        status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (post_id) REFERENCES mentor_posts(post_id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS job_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        type VARCHAR(50) DEFAULT 'internship',
        approved TINYINT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES companies(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    "CREATE TABLE IF NOT EXISTS student_projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        skills_used TEXT,
        image_path VARCHAR(500),
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_email),
        INDEX (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

foreach ($tables_sql as $sql) {
    $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FutureBot - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            --success: #10b981;
            --success-light: #ecfdf5;
            --danger: #e63946;
            --danger-light: #fef2f2;
            --warning: #f59e0b;
            --warning-light: #fffbeb;
            --gray: #6c757d;
            --light-gray: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #64748b;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
            --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --gradient-danger: linear-gradient(135deg, #e63946 0%, #dc2626 100%);
            --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --gradient-hover: linear-gradient(135deg, #3a0ca3 0%, #4361ee 100%);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        html, body {
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--text-dark);
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
            box-shadow: var(--shadow);
            position: fixed;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
            backdrop-filter: blur(10px);
        }
        
        nav .logo {
            font-size: 1.8rem;
            font-weight: bold;
            letter-spacing: 1px;
            background: var(--gradient);
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
        nav .nav-buttons a {
            background: var(--gradient);
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
        
        nav .nav-buttons button:hover, 
        nav .nav-buttons a:hover {
            background: var(--gradient-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 10px 10px;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            animation: fadeSlideIn 0.8s ease;
            margin-top:0px;
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Hero Section */
        .hero-section {
            text-align: center;
            padding: 20px 10px;
            margin-bottom: 0px;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto 30px;
        }

        /* Section Titles */
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin: 50px 0 30px;
            color: var(--text-dark);
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient);
            border-radius: 2px;
        }

        /* Section Containers with Carousel */
        .section-container {
            position: relative;
            margin-bottom: 50px;
        }

        .posts-carousel {
            display: flex;
            gap: 30px;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 20px 10px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .posts-carousel::-webkit-scrollbar {
            display: none;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: var(--white);
            border: none;
            border-radius: 50%;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
            font-size: 1.2rem;
            color: var(--primary-blue);
        }

        .carousel-nav:hover {
            background: var(--primary-blue);
            color: var(--white);
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-nav.prev {
            left: -25px;
        }

        .carousel-nav.next {
            right: -25px;
        }

        /* Cards */
        .post-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 25px;
            transition: all 0.3s ease;
            border: 1px solid rgba(67, 97, 238, 0.1);
            position: relative;
            overflow: hidden;
            flex: 0 0 auto;
            width: 350px;
        }

        .post-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
        }

        .mentor-card::before {
            background: var(--gradient);
        }

        .internship-card::before {
            background: var(--gradient-success);
        }

        .job-card::before {
            background: var(--gradient-danger);
        }

        .project-card::before {
            background: var(--gradient-warning);
        }

        .post-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
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
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .post-company {
            color: var(--text-light);
            font-size: 0.95rem;
            margin-bottom: 10px;
        }

        .post-type-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
            color: white;
        }

        .mentor-badge {
            background: var(--gradient);
        }

        .internship-badge {
            background: var(--gradient-success);
        }

        .job-badge {
            background: var(--gradient-danger);
        }

        .project-badge {
            background: var(--gradient-warning);
        }

        .post-details {
            margin-bottom: 20px;
        }

        .post-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        .post-detail i {
            width: 16px;
        }

        .mentor-detail i {
            color: var(--primary-blue);
        }

        .internship-detail i {
            color: var(--success);
        }

        .job-detail i {
            color: var(--danger);
        }

        .project-detail i {
            color: var(--warning);
        }

        .post-description {
            color: var(--text-light);
            line-height: 1.5;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .skills-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .skill-tag {
            background: var(--gradient);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .action-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .mentor-btn {
            background: var(--gradient);
        }

        .internship-btn {
            background: var(--gradient-success);
        }

        .job-btn {
            background: var(--gradient-danger);
        }

        .project-btn {
            background: var(--gradient-warning);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            color: white;
            text-decoration: none;
        }

        .action-btn:disabled {
            background: var(--gray);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* View All Button */
        .view-all-container {
            text-align: center;
            margin: 40px 0;
        }

        .view-all-btn {
            background: var(--gradient);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .view-all-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
            color: white;
            text-decoration: none;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: var(--shadow-hover);
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from { opacity: 0; transform: translateY(-50px) scale(0.9); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
            transition: color 0.3s ease;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-modal:hover {
            background: var(--light-gray);
            color: var(--text-dark);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: #f8f9fa;
            color: var(--text-dark);
            font-size: 16px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            resize: vertical;
            min-height: 120px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-blue);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: var(--gradient);
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
            border: none;
            border-left: 4px solid;
        }

        .alert-danger {
            background: var(--danger-light);
            color: var(--danger);
            border-left-color: var(--danger);
        }

        .alert-success {
            background: var(--success-light);
            color: var(--success);
            border-left-color: var(--success);
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(40px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
            width: 100%;
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--light-blue);
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .empty-state p {
            margin-bottom: 20px;
        }

        /* Company Logo Styles */
        .company-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 12px;
        }

        .company-logo {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .company-logo-placeholder {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .company-info {
            flex: 1;
        }

        .company-name {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .company-location {
            font-size: 0.85rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Project Image */
        .project-image {
            width: 100%;
            height: 200px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                padding: 15px 20px;
            }
            
            .main-content {
                padding: 20px 15px;
            }
            
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .post-card {
                width: 300px;
                padding: 20px;
            }
            
            .section-title {
                font-size: 1.6rem;
            }
            
            .carousel-nav {
                display: none;
            }
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

    /* Responsive Design */
    @media (max-width: 768px) {
        .navbar {
            padding: 0.5rem 1rem;
        }
        
        .logo-text {
            font-size: 1.2rem;
        }
        
        .container {
            margin: 1.5rem auto;
            padding: 0 1rem;
        }
        
        .form-card {
            padding: 1.5rem;
            border-radius: 16px;
        }
        
        .nav-links {
            gap: 1rem;
        }
        
        .footer-links {
            flex-direction: column;
            gap: 1rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .modal-success .modal-header,
        .modal-success .modal-body {
            padding: 1.5rem;
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
            
            .hero-title {
                font-size: 1.8rem;
            }
            
            .section-title {
                font-size: 1.4rem;
            }
            
            .post-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .post-type-badge {
                align-self: flex-start;
            }
            
            .post-card {
                width: 280px;
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
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <?php elseif ($_SESSION['role'] === 'mentor'): ?>
                <a href="mentor_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <?php else: ?>
                <a href="student_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <?php endif; ?>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="main-content">
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-title">Welcome to FutureBot</h1>
        <p class="hero-subtitle">Connect with expert mentors, discover internships, find jobs, and accelerate your career journey with personalized opportunities.</p>
    </div>

    <!-- Mentor Hiring Posts Section -->
    <h2 class="section-title">
        <i class="fas fa-user-tie" style="color: var(--primary-blue);"></i>
        Available Mentor Positions
    </h2>
    
    <?php if ($request_error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($request_error) ?>
        </div>
    <?php elseif ($request_success): ?>
        <div class="alert alert-success" id="successMessage">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($request_success) ?>
        </div>
    <?php endif; ?>

    <div class="section-container">
        <?php if (empty($mentor_posts)): ?>
            <div class="empty-state">
                <i class="fas fa-user-tie"></i>
                <h3>No Mentor Positions Available</h3>
                <p>Check back later for new mentoring opportunities.</p>
            </div>
        <?php else: ?>
            <button class="carousel-nav prev" onclick="scrollCarousel('mentorCarousel', -350)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="posts-carousel" id="mentorCarousel">
                <?php foreach ($mentor_posts as $post): ?>
                    <div class="post-card mentor-card">
                        <div class="post-header">
                            <div>
                                <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
                                <div class="post-company">
                                    <i class="fas fa-user"></i>
                                    Mentor: <?= htmlspecialchars($post['mentor_name']) ?>
                                </div>
                            </div>
                            <div class="post-type-badge mentor-badge">
                                $<?= htmlspecialchars($post['fee']) ?>/hr
                            </div>
                        </div>
                        
                        <div class="post-details">
                            <div class="post-detail mentor-detail">
                                <i class="fas fa-book"></i>
                                <span><strong>Subjects:</strong> <?= htmlspecialchars($post['subjects']) ?></span>
                            </div>
                            <div class="post-detail mentor-detail">
                                <i class="fas fa-clock"></i>
                                <span><strong>Availability:</strong> <?= htmlspecialchars($post['availability']) ?></span>
                            </div>
                            <div class="post-detail mentor-detail">
                                <i class="fas fa-envelope"></i>
                                <span><strong>Contact:</strong> <?= htmlspecialchars($post['contact_email']) ?></span>
                            </div>
                        </div>
                        
                        <div class="post-description">
                            <strong>Description:</strong> <?= htmlspecialchars($post['description']) ?>
                        </div>
                        
                        <div class="post-description">
                            <strong>Experience:</strong> <?= htmlspecialchars($post['experience']) ?>
                        </div>
                        
                        <button class="action-btn mentor-btn" onclick="openRequestModal(<?= $post['post_id'] ?>, '<?= htmlspecialchars(addslashes($post['title'])) ?>')">
                            <i class="fas fa-paper-plane"></i> Send Request
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-nav next" onclick="scrollCarousel('mentorCarousel', 350)">
                <i class="fas fa-chevron-right"></i>
            </button>
        <?php endif; ?>
    </div>

    <?php if (!empty($mentor_posts)): ?>
    <div class="view-all-container">
        <a href="mentors.php" class="view-all-btn">
            <i class="fas fa-arrow-right"></i> View All Mentor Positions
        </a>
    </div>
    <?php endif; ?>

    <!-- Internship Posts Section -->
    <h2 class="section-title">
        <i class="fas fa-user-graduate" style="color: var(--success);"></i>
        Latest Internship Opportunities
    </h2>

    <div class="section-container">
        <?php if (empty($internship_posts)): ?>
            <div class="empty-state">
                <i class="fas fa-user-graduate"></i>
                <h3>No Internships Available</h3>
                <p>Check back later for new internship opportunities.</p>
                <?php if (!isset($_SESSION['company_id'])): ?>
                    <p style="margin-top: 1rem;">
                        <a href="company_login.php" style="color: var(--primary-blue); text-decoration: none; font-weight: 600;">
                            Are you a company? Post your internship here!
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <button class="carousel-nav prev" onclick="scrollCarousel('internshipCarousel', -350)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="posts-carousel" id="internshipCarousel">
                <?php foreach ($internship_posts as $post): ?>
                    <div class="post-card internship-card">
                        <div class="company-header">
                            <div class="company-logo-placeholder">
                                <?= strtoupper(substr($post['company_name'], 0, 2)) ?>
                            </div>
                            <div class="company-info">
                                <div class="company-name"><?= htmlspecialchars($post['company_name']) ?></div>
                                <div class="company-location">
                                    <i class="fas fa-building"></i>
                                    Company Opportunity
                                </div>
                            </div>
                        </div>
                        
                        <div class="post-header">
                            <div>
                                <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
                            </div>
                            <div class="post-type-badge internship-badge">
                                <i class="fas fa-user-graduate"></i> Internship
                            </div>
                        </div>
                        
                        <div class="post-details">
                            <div class="post-detail internship-detail">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Posted:</strong> <?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                            </div>
                        </div>
                        
                        <div class="post-description">
                            <?= nl2br(htmlspecialchars(substr($post['description'], 0, 150))) ?>
                            <?= strlen($post['description']) > 150 ? '...' : '' ?>
                        </div>
                        
                        <a href="internship_details.php?id=<?= $post['id'] ?>" class="action-btn internship-btn">
                            <i class="fas fa-eye"></i> View Details & Apply
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-nav next" onclick="scrollCarousel('internshipCarousel', 350)">
                <i class="fas fa-chevron-right"></i>
            </button>
        <?php endif; ?>
    </div>

    <?php if (!empty($internship_posts)): ?>
    <div class="view-all-container">
        <a href="internships.php" class="view-all-btn">
            <i class="fas fa-arrow-right"></i> View All Internships
        </a>
    </div>
    <?php endif; ?>

    <!-- Job Posts Section -->
    <h2 class="section-title">
        <i class="fas fa-briefcase" style="color: var(--danger);"></i>
        Latest Job Opportunities
    </h2>

    <div class="section-container">
        <?php if (empty($job_posts)): ?>
            <div class="empty-state">
                <i class="fas fa-briefcase"></i>
                <h3>No Jobs Available</h3>
                <p>Check back later for new job opportunities.</p>
                <?php if (!isset($_SESSION['company_id'])): ?>
                    <p style="margin-top: 1rem;">
                        <a href="company_login.php" style="color: var(--primary-blue); text-decoration: none; font-weight: 600;">
                            Are you a company? Post your job here!
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <button class="carousel-nav prev" onclick="scrollCarousel('jobCarousel', -350)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="posts-carousel" id="jobCarousel">
                <?php foreach ($job_posts as $post): ?>
                    <div class="post-card job-card">
                        <div class="company-header">
                            <div class="company-logo-placeholder">
                                <?= strtoupper(substr($post['company_name'], 0, 2)) ?>
                            </div>
                            <div class="company-info">
                                <div class="company-name"><?= htmlspecialchars($post['company_name']) ?></div>
                                <div class="company-location">
                                    <i class="fas fa-building"></i>
                                    Company Opportunity
                                </div>
                            </div>
                        </div>
                        
                        <div class="post-header">
                            <div>
                                <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
                            </div>
                            <div class="post-type-badge job-badge">
                                <i class="fas fa-briefcase"></i> Job
                            </div>
                        </div>
                        
                        <div class="post-details">
                            <div class="post-detail job-detail">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Posted:</strong> <?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                            </div>
                        </div>
                        
                        <div class="post-description">
                            <?= nl2br(htmlspecialchars(substr($post['description'], 0, 150))) ?>
                            <?= strlen($post['description']) > 150 ? '...' : '' ?>
                        </div>
                        
                        <a href="job_details.php?id=<?= $post['id'] ?>" class="action-btn job-btn">
                            <i class="fas fa-eye"></i> View Details & Apply
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-nav next" onclick="scrollCarousel('jobCarousel', 350)">
                <i class="fas fa-chevron-right"></i>
            </button>
        <?php endif; ?>
    </div>

    <?php if (!empty($job_posts)): ?>
    <div class="view-all-container">
        <a href="jobs.php" class="view-all-btn">
            <i class="fas fa-arrow-right"></i> View All Jobs
        </a>
    </div>
    <?php endif; ?>

    <!-- Student Projects Section -->
    <h2 class="section-title">
        <i class="fas fa-project-diagram" style="color: var(--warning);"></i>
        Featured Student Projects
    </h2>

    <div class="section-container">
        <?php if (empty($student_projects)): ?>
            <div class="empty-state">
                <i class="fas fa-project-diagram"></i>
                <h3>No Projects Available</h3>
                <p>No student projects have been approved yet.</p>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'student'): ?>
                    <p style="margin-top: 1rem;">
                        <a href="submit_project.php" style="color: var(--primary-blue); text-decoration: none; font-weight: 600;">
                            Be the first to submit your project!
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <button class="carousel-nav prev" onclick="scrollCarousel('projectCarousel', -350)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="posts-carousel" id="projectCarousel">
                <?php foreach ($student_projects as $project): ?>
                    <div class="post-card project-card">
                        <div class="post-header">
                            <div>
                                <h3 class="post-title"><?= htmlspecialchars($project['title']) ?></h3>
                                <div class="post-company">
                                    <i class="fas fa-user"></i>
                                    By: <?= htmlspecialchars($project['user_email']) ?>
                                </div>
                            </div>
                            <div class="post-type-badge project-badge">
                                <i class="fas fa-project-diagram"></i> Project
                            </div>
                        </div>
                        
                        <div class="post-details">
                            <div class="post-detail project-detail">
                                <i class="fas fa-calendar"></i>
                                <span><strong>Submitted:</strong> <?= date('M j, Y', strtotime($project['submitted_at'])) ?></span>
                            </div>
                        </div>

                        <?php if (!empty($project['image_path']) && file_exists($project['image_path'])): ?>
                            <div class="project-image">
                                <img src="<?= htmlspecialchars($project['image_path']) ?>" alt="<?= htmlspecialchars($project['title']) ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-description">
                            <?= nl2br(htmlspecialchars(substr($project['description'], 0, 150))) ?>
                            <?= strlen($project['description']) > 150 ? '...' : '' ?>
                        </div>

                        <?php if (!empty($project['skills_used'])): ?>
                            <div class="skills-container">
                                <?php 
                                $skills = explode(',', $project['skills_used']);
                                $displaySkills = array_slice($skills, 0, 3);
                                foreach ($displaySkills as $skill): 
                                    $skill = trim($skill);
                                    if (!empty($skill)):
                                ?>
                                    <span class="skill-tag"><?= htmlspecialchars($skill) ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                                <?php if (count($skills) > 3): ?>
                                    <span class="skill-tag">+<?= count($skills) - 3 ?> more</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <a href="project_details.php?id=<?= $project['id'] ?>" class="action-btn project-btn">
                            <i class="fas fa-eye"></i> View Project Details
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-nav next" onclick="scrollCarousel('projectCarousel', 350)">
                <i class="fas fa-chevron-right"></i>
            </button>
        <?php endif; ?>
    </div>

    <?php if (!empty($student_projects)): ?>
    <div class="view-all-container">
        <a href="projects.php" class="view-all-btn">
            <i class="fas fa-arrow-right"></i> View All Projects
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Request Modal -->
<div id="requestModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">Send Request to Mentor</h3>
            <button class="close-modal" onclick="closeRequestModal()">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="post_id" id="modalPostId">
            <div class="form-group">
                <label class="form-label" for="message">Your Message to the Mentor *</label>
                <textarea name="message" class="form-textarea" placeholder="Introduce yourself and explain why you're interested in this mentoring opportunity..." required></textarea>
            </div>
            <button type="submit" name="send_request" class="submit-btn">
                <i class="fas fa-paper-plane"></i> Send Request
            </button>
        </form>
    </div>
</div>

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
<!-- Footer -->
  <footer>
      <div class="footer-content">
          <div class="footer-logo">
              <i class="fas fa-robot"></i>FutureBot
          </div>
          
          <div class="footer-links">
              <a href="home.php">Home</a>
              <a href="about.php">About Us</a>
              <a href="career_suggestions.php">Career Suggestions</a>
              <a href="privacy.php">Privacy Policy</a>
              <a href="contact.php">Contact Us</a>
          </div>
          
          <div class="footer-social">
              <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
              <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
              <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
          </div>
          
          <div class="footer-bottom">
              <p>&copy; 2024 FutureBot. All rights reserved. | Empowering students with AI-driven career guidance</p>
          </div>
      </div>
  </footer>

// Modal functions
function openRequestModal(postId, postTitle) {
    <?php if (!isset($_SESSION['user_id'])): ?>
        alert('Please login to send a request to mentors.');
        return;
    <?php endif; ?>
    
    const modal = document.getElementById('requestModal');
    const postIdInput = document.getElementById('modalPostId');
    const modalTitle = document.getElementById('modalTitle');
    
    postIdInput.value = postId;
    modalTitle.textContent = `Send Request: ${postTitle}`;
    modal.style.display = 'flex';
}

function closeRequestModal() {
    const modal = document.getElementById('requestModal');
    modal.style.display = 'none';
}

// Carousel scrolling function
function scrollCarousel(carouselId, scrollAmount) {
    const carousel = document.getElementById(carouselId);
    carousel.scrollBy({
        left: scrollAmount,
        behavior: 'smooth'
    });
}

// Auto-scroll carousels
function autoScrollCarousels() {
    const carousels = document.querySelectorAll('.posts-carousel');
    
    carousels.forEach(carousel => {
        // Only auto-scroll if not hovered
        if (!carousel.isHovered) {
            const maxScroll = carousel.scrollWidth - carousel.clientWidth;
            
            if (carousel.scrollLeft >= maxScroll - 10) {
                // At the end, scroll back to start
                carousel.scrollTo({
                    left: 0,
                    behavior: 'smooth'
                });
            } else {
                // Scroll forward
                carousel.scrollBy({
                    left: 350,
                    behavior: 'smooth'
                });
            }
        }
    });
}

// Set up hover detection for carousels
document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.posts-carousel');
    
    carousels.forEach(carousel => {
        carousel.addEventListener('mouseenter', function() {
            this.isHovered = true;
        });
        
        carousel.addEventListener('mouseleave', function() {
            this.isHovered = false;
        });
    });
    
    // Start auto-scrolling
    setInterval(autoScrollCarousels, 2000);
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('requestModal');
    if (event.target === modal) {
        closeRequestModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeRequestModal();
    }
});
</script>

</body>
</html>