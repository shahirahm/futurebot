<?php
session_start();
require_once 'db.php';


$error = '';
$success = '';

// Handle status updates (approve/reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $post_id = $_POST['post_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE mentor_posts SET status = ? WHERE post_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $status, $post_id);
        if ($stmt->execute()) {
            $action = $status === 'approved' ? 'approved' : 'rejected';
            $success = "Post {$action} successfully!";
        } else {
            $error = "Error updating post status: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Database error: " . $conn->error;
    }
}



// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $post_id = $_POST['post_id'];
    
    $stmt = $conn->prepare("DELETE FROM mentor_posts WHERE post_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $post_id);
        if ($stmt->execute()) {
            $success = "Post deleted successfully!";
        } else {
            $error = "Error deleting post: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Database error: " . $conn->error;
    }
}

// Fetch all job posts with mentor information
$posts = [];
try {
    $posts_query = "
        SELECT 
            mp.*, 
            u.full_name as mentor_name,
            u.email as mentor_email
        FROM mentor_posts mp
        JOIN users u ON mp.mentor_id = u.user_id
        ORDER BY mp.created_at DESC
    ";
    
    $posts_result = $conn->query($posts_query);
    if ($posts_result) {
        $posts = $posts_result->fetch_all(MYSQLI_ASSOC);
    } else {
        throw new Exception("Error fetching posts: " . $conn->error);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Count posts by status
$pending_count = count(array_filter($posts, function($post) { return $post['status'] === 'pending'; }));
$approved_count = count(array_filter($posts, function($post) { return $post['status'] === 'approved'; }));
$rejected_count = count(array_filter($posts, function($post) { return $post['status'] === 'rejected'; }));
$total_count = count($posts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Job Post Approvals - FutureBot</title>
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
        
        nav .nav-buttons button, 
        nav .nav-buttons a {
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
        
        nav .nav-buttons button:hover, 
        nav .nav-buttons a:hover {
            background: linear-gradient(135deg, #3a0ca3, #4361ee);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px 20px;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            animation: fadeSlideIn 0.8s ease;
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dashboard-container {
            display: grid;
            grid-template-columns: 1fr;
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

        /* Stats Container */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 10px 20px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            border-color: #4361ee;
        }

        .filter-tab:hover:not(.active) {
            background: #e9ecef;
        }

        /* Posts Section */
        .posts-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .post-item {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 25px;
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

        .post-mentor {
            color: #5a6c7d;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }

        .post-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .approve-btn {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .approve-btn:hover {
            background: #28a745;
            color: white;
        }

        .reject-btn {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.3);
        }

        .reject-btn:hover {
            background: #dc3545;
            color: white;
        }

        .delete-btn {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.3);
        }

        .delete-btn:hover {
            background: #6c757d;
            color: white;
        }

        .post-status {
            padding: 6px 12px;
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

        /* Responsive Design */
        @media (max-width: 1024px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
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
            
            .post-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .post-actions {
                width: 100%;
                justify-content: flex-start;
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
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .filter-tabs {
                justify-content: center;
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
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="home.php"><i class="fas fa-home"></i> Home</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</nav>

<div class="main-content">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success" id="successMessage"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="dashboard-container">
        <!-- Statistics -->
        <div class="dashboard-card">
            <h2 class="card-title">Job Post Overview</h2>
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-number"><?= $total_count ?></div>
                    <div class="stat-label">Total Posts</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $pending_count ?></div>
                    <div class="stat-label">Pending Review</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $approved_count ?></div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $rejected_count ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="dashboard-card">
            <h2 class="card-title">Manage Job Posts</h2>
            <div class="filter-tabs" id="filterTabs">
                <div class="filter-tab active" data-filter="all">All Posts (<?= $total_count ?>)</div>
                <div class="filter-tab" data-filter="pending">Pending (<?= $pending_count ?>)</div>
                <div class="filter-tab" data-filter="approved">Approved (<?= $approved_count ?>)</div>
                <div class="filter-tab" data-filter="rejected">Rejected (<?= $rejected_count ?>)</div>
            </div>

            <!-- Posts List -->
            <div class="posts-container">
                <?php if (empty($posts)): ?>
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <h3>No Job Posts Yet</h3>
                        <p>Mentors haven't submitted any job posts for review yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post-item" data-status="<?= $post['status'] ?>">
                            <div class="post-header">
                                <div>
                                    <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
                                    <div class="post-mentor">
                                        <i class="fas fa-user"></i>
                                        Posted by: <?= htmlspecialchars($post['mentor_name']) ?> (<?= htmlspecialchars($post['mentor_email']) ?>)
                                    </div>
                                </div>
                                <div class="post-status status-<?= $post['status'] ?>">
                                    <?= ucfirst($post['status']) ?>
                                </div>
                            </div>
                            
                            <div class="post-details">
                                <div class="post-detail">
                                    <i class="fas fa-dollar-sign"></i>
                                    <span>Fee: $<?= htmlspecialchars($post['fee']) ?>/hour</span>
                                </div>
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
                                <strong>Experience:</strong> <?= htmlspecialchars($post['experience']) ?>
                            </div>
                            
                            <div class="post-actions">
                                <?php if ($post['status'] === 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" name="update_status" class="action-btn approve-btn">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" name="update_status" class="action-btn reject-btn">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this post? This action cannot be undone.');">
                                    <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
                                    <button type="submit" name="delete_post" class="action-btn delete-btn">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
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

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterTabs = document.querySelectorAll('.filter-tab');
    const postItems = document.querySelectorAll('.post-item');
    
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            filterTabs.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Show/hide posts based on filter
            postItems.forEach(post => {
                if (filter === 'all' || post.getAttribute('data-status') === filter) {
                    post.style.display = 'block';
                } else {
                    post.style.display = 'none';
                }
            });
        });
    });
    
    // Add some interactivity to action buttons
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

</body>
</html>