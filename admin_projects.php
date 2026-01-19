<?php
session_start();
require 'db.php';

// Check admin login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Ensure the table has the correct structure
$alter_table_sql = "
    ALTER TABLE student_projects 
    ADD COLUMN IF NOT EXISTS status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    ADD COLUMN IF NOT EXISTS submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
";
$conn->query($alter_table_sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = intval($_POST['project_id']);
    
    if (isset($_POST['approve'])) {
        $stmt = $conn->prepare("UPDATE student_projects SET status = 'approved' WHERE id = ?");
    } elseif (isset($_POST['reject'])) {
        $stmt = $conn->prepare("UPDATE student_projects SET status = 'rejected' WHERE id = ?");
    }
    
    if ($stmt) {
        $stmt->bind_param("i", $project_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = isset($_POST['approve']) ? "Project approved successfully!" : "Project rejected successfully!";
        } else {
            $_SESSION['error'] = "Error updating project: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
    }
    header("Location: admin_projects.php");
    exit;
}

// First, let's check what columns actually exist in the table
$check_columns_sql = "SHOW COLUMNS FROM student_projects";
$columns_result = $conn->query($check_columns_sql);
$existing_columns = [];
if ($columns_result) {
    while ($row = $columns_result->fetch_assoc()) {
        $existing_columns[] = $row['Field'];
    }
}

// Build the query based on available columns
$order_clause = "ORDER BY ";
if (in_array('submitted_at', $existing_columns)) {
    $order_clause .= "submitted_at DESC";
} elseif (in_array('created_at', $existing_columns)) {
    $order_clause .= "created_at DESC";
} else {
    $order_clause .= "id DESC"; // Fallback to ID ordering
}

// Get pending projects
$query = "SELECT * FROM student_projects WHERE status = 'pending' $order_clause";
$result = $conn->query($query);

// Check if query was successful
if ($result === false) {
    // If the query failed, try to create the table with correct structure
    $create_table_sql = "
        CREATE TABLE IF NOT EXISTS student_projects (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    if ($conn->query($create_table_sql)) {
        // Retry the query after creating table
        $result = $conn->query("SELECT * FROM student_projects WHERE status = 'pending' ORDER BY submitted_at DESC");
    } else {
        die("Error creating student_projects table: " . $conn->error);
    }
}

// If result is still false, show error
if ($result === false) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Review Projects - FutureBot</title>
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
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        color: var(--text-dark);
        min-height: 100vh;
        line-height: 1.6;
        padding-top: 70px;
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

    /* Navbar Styles */
    .navbar {
        background-color: var(--white);
        padding: 0.5rem 2rem;
        border-bottom: 1px solid rgba(67, 97, 238, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow);
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(10px);
        height: 70px;
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

    .nav-links {
        display: flex;
        gap: 2rem;
        align-items: center;
    }

    .nav-link {
        color: var(--text-dark);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }

    .nav-link:hover {
        color: var(--primary-blue);
        background: rgba(67, 97, 238, 0.05);
        transform: translateY(-1px);
    }

    .back-button {
        background: var(--gradient);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .back-button:hover {
        background: var(--gradient-hover);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Main Content */
    .container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1.25rem;
    }

    .page-header {
        background: var(--white);
        border-radius: 20px;
        box-shadow: var(--shadow);
        padding: 2rem;
        margin-bottom: 2rem;
        text-align: center;
        border: 1px solid rgba(67, 97, 238, 0.1);
    }

    .page-title {
        color: var(--text-dark);
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        color: var(--text-light);
        font-size: 1.1rem;
    }

    /* Alert Messages */
    .alert {
        border-radius: 12px;
        border: none;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        font-weight: 500;
    }

    .alert-success {
        background: var(--success-light);
        color: var(--success);
        border-left: 4px solid var(--success);
    }

    .alert-danger {
        background: var(--danger-light);
        color: var(--danger);
        border-left: 4px solid var(--danger);
    }

    /* Project Cards */
    .project-card {
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(67, 97, 238, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .project-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .project-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--warning);
    }

    .project-header {
        display: flex;
        justify-content: between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .project-title {
        color: var(--text-dark);
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        flex: 1;
    }

    .project-meta {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-light);
        font-size: 0.9rem;
    }

    .meta-item i {
        color: var(--primary-blue);
    }

    .project-description {
        color: var(--text-dark);
        line-height: 1.6;
        margin-bottom: 1rem;
        background: var(--light-gray);
        padding: 1rem;
        border-radius: 8px;
    }

    .skills-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .skill-tag {
        background: var(--gradient);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .btn-approve {
        background: var(--gradient-success);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
        color: white;
    }

    .btn-reject {
        background: var(--gradient-danger);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(230, 57, 70, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(230, 57, 70, 0.4);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--white);
        border-radius: 20px;
        box-shadow: var(--shadow);
    }

    .empty-icon {
        font-size: 4rem;
        color: var(--light-blue);
        margin-bottom: 1.5rem;
    }

    .empty-title {
        color: var(--text-dark);
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: var(--text-light);
        font-size: 1rem;
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
        
        .page-header {
            padding: 1.5rem;
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

        .project-header {
            flex-direction: column;
        }

        .project-title {
            margin-bottom: 1rem;
        }
    }

    @media (max-width: 480px) {
        .page-header {
            padding: 1.25rem;
        }

        .navbar {
            padding: 0.4rem 0.8rem;
        }

        .logo-icon {
            width: 35px;
            height: 35px;
        }
        
        .page-title {
            font-size: 1.8rem;
        }

        .project-card {
            padding: 1.25rem;
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

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="logo-text">FutureBot</div>
        </div>
        
        <div class="nav-links">
            <a href="home.php" class="back-button">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="admin_dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Project Review Panel</h1>
            <p class="page-subtitle">Review and approve student project submissions</p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if ($result->num_rows === 0): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="empty-title">All Caught Up!</h3>
                <p class="empty-text">There are no pending projects to review at the moment.</p>
            </div>
        <?php else: ?>
            <div class="projects-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="project-card">
                        <div class="project-header">
                            <h3 class="project-title"><?= htmlspecialchars($row['title']) ?></h3>
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span>
                                    <?php 
                                    // Display date based on available columns
                                    if (isset($row['submitted_at'])) {
                                        echo date('M j, Y g:i A', strtotime($row['submitted_at']));
                                    } elseif (isset($row['created_at'])) {
                                        echo date('M j, Y g:i A', strtotime($row['created_at']));
                                    } else {
                                        echo 'Date not available';
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="project-meta">
                            <div class="meta-item">
                                <i class="fas fa-user"></i>
                                <span><?= htmlspecialchars($row['user_email']) ?></span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-hourglass-half"></i>
                                <span>Pending Review</span>
                            </div>
                        </div>

                        <div class="project-description">
                            <?= nl2br(htmlspecialchars($row['description'])) ?>
                        </div>

                        <?php if (!empty($row['skills_used'])): ?>
                            <div class="skills-container">
                                <?php 
                                $skills = explode(',', $row['skills_used']);
                                foreach ($skills as $skill): 
                                    $skill = trim($skill);
                                    if (!empty($skill)):
                                ?>
                                    <span class="skill-tag"><?= htmlspecialchars($skill) ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($row['image_path']) && file_exists($row['image_path'])): ?>
                            <div class="project-image mb-3">
                                <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Project Image" class="img-fluid rounded" style="max-height: 200px;">
                            </div>
                        <?php endif; ?>

                        <div class="action-buttons">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="project_id" value="<?= $row['id'] ?>" />
                                <button type="submit" name="approve" class="btn-approve">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="project_id" value="<?= $row['id'] ?>" />
                                <button type="submit" name="reject" class="btn-reject">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>