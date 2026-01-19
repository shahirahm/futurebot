<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$user_email = $_SESSION['email'];
$upcoming_webinars = [];
$registered_webinars = [];

// Fetch all upcoming webinars
$sql = "SELECT w.* FROM Webinars w WHERE w.scheduled_at > NOW() ORDER BY w.scheduled_at ASC";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Check if user is registered
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

// Fetch user's registered webinars
$sql = "SELECT w.*, uw.registered_at 
        FROM User_Webinars uw 
        JOIN Webinars w ON uw.webinar_id = w.webinar_id 
        WHERE uw.user_email = ? 
        ORDER BY w.scheduled_at ASC";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $registered_webinars[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Webinars - FutureBot</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
  /* Add similar CSS structure as previous pages */
  /* ... (include the same CSS structure as my_courses.php) ... */
  </style>
</head>
<body>
  <!-- Similar structure to my_courses.php but for webinars -->
  <div class="background-animation">
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
  </div>

  <nav>
    <div class="logo">
      <i class="fas fa-robot"></i>FutureBot
    </div>
    <div class="nav-buttons">
      <button onclick="location.href='dashboard.php'"><i class="fas fa-home"></i> Dashboard</button>
      <button onclick="location.href='career_suggestions.php'"><i class="fas fa-briefcase"></i> Career</button>
      <button onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
  </nav>

  <div class="main-content">
    <div class="page-header">
      <h1>Webinars</h1>
      <p>Join live sessions with industry experts</p>
    </div>

    <div style="width: 100%; margin-bottom: 40px;">
      <h2 style="color: #2c3e50; margin-bottom: 20px;">Upcoming Webinars</h2>
      <?php if (empty($upcoming_webinars)): ?>
        <div class="empty-state">
          <i class="fas fa-video"></i>
          <h3>No Upcoming Webinars</h3>
          <p>Check back later for new webinar announcements.</p>
        </div>
      <?php else: ?>
        <div class="courses-grid">
          <?php foreach ($upcoming_webinars as $webinar): ?>
            <div class="course-card">
              <div class="course-header">
                <div>
                  <h3 class="course-title"><?= htmlspecialchars($webinar['title']) ?></h3>
                  <p class="course-instructor">by <?= htmlspecialchars($webinar['instructor']) ?></p>
                </div>
              </div>
              
              <div class="course-meta">
                <span><i class="fas fa-calendar"></i> <?= date('M j, Y g:i A', strtotime($webinar['scheduled_at'])) ?></span>
                <span><i class="fas fa-clock"></i> <?= $webinar['duration'] ?> minutes</span>
              </div>
              
              <p class="course-description"><?= htmlspecialchars($webinar['description']) ?></p>
              
              <div class="course-actions">
                <?php if ($webinar['is_registered']): ?>
                  <button class="btn btn-disabled" disabled>
                    <i class="fas fa-check"></i> Registered
                  </button>
                <?php else: ?>
                  <form method="POST" action="register_webinar.php" style="display: inline;">
                    <input type="hidden" name="webinar_id" value="<?= $webinar['webinar_id'] ?>">
                    <button type="submit" class="btn btn-primary">
                      <i class="fas fa-plus"></i> Register
                    </button>
                  </form>
                <?php endif; ?>
                <a href="#" class="btn btn-outline">
                  <i class="fas fa-info-circle"></i> Details
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div style="width: 100%;">
      <h2 style="color: #2c3e50; margin-bottom: 20px;">My Registered Webinars</h2>
      <?php if (empty($registered_webinars)): ?>
        <div class="empty-state">
          <i class="fas fa-calendar-check"></i>
          <h3>No Registered Webinars</h3>
          <p>Register for webinars to see them here.</p>
        </div>
      <?php else: ?>
        <div class="courses-grid">
          <?php foreach ($registered_webinars as $webinar): ?>
            <div class="course-card">
              <div class="course-header">
                <div>
                  <h3 class="course-title"><?= htmlspecialchars($webinar['title']) ?></h3>
                  <p class="course-instructor">by <?= htmlspecialchars($webinar['instructor']) ?></p>
                </div>
              </div>
              
              <div class="course-meta">
                <span><i class="fas fa-calendar"></i> <?= date('M j, Y g:i A', strtotime($webinar['scheduled_at'])) ?></span>
                <span><i class="fas fa-clock"></i> <?= $webinar['duration'] ?> minutes</span>
              </div>
              
              <p class="course-description"><?= htmlspecialchars($webinar['description']) ?></p>
              
              <div class="course-actions">
                <a href="#" class="btn btn-primary">
                  <i class="fas fa-video"></i> Join Webinar
                </a>
                <a href="#" class="btn btn-outline">
                  <i class="fas fa-calendar"></i> Add to Calendar
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <footer>
    <div class="footer-content">
      <div class="footer-logo">
        <i class="fas fa-robot"></i>FutureBot
      </div>
      
      <div class="footer-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="courses.php">Courses</a>
        <a href="books.php">Books</a>
        <a href="webinars.php">Webinars</a>
        <a href="career_suggestions.php">Career Suggestions</a>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; 2024 FutureBot. All rights reserved. | Empowering students with AI-driven career guidance</p>
      </div>
    </div>
  </footer>
</body>
</html>