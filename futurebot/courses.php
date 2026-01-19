<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$user_email = $_SESSION['email'];
$all_courses = [];
$user_courses = [];

// Fetch all courses
$sql = "SELECT * FROM Courses ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $all_courses[] = $row;
    }
    $stmt->close();
}

// Fetch user's enrolled courses to check enrollment status
$sql = "SELECT course_id FROM User_Courses WHERE user_email = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $user_courses[] = $row['course_id'];
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
  <title>Courses - FutureBot</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
  /* Add similar CSS structure as previous pages */
  </style>
</head>
<body>
  <!-- Similar structure to webinars.php but for courses -->
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
      <h1>Courses</h1>
      <p>Expand your knowledge with our curated courses</p>
    </div>

    <?php if (empty($all_courses)): ?>
      <div class="empty-state">
        <i class="fas fa-graduation-cap"></i>
        <h3>No Courses Available</h3>
        <p>New courses will be added soon.</p>
      </div>
    <?php else: ?>
      <div class="courses-grid">
        <?php foreach ($all_courses as $course): 
          $is_enrolled = in_array($course['course_id'], $user_courses);
        ?>
          <div class="course-card">
            <div class="course-header">
              <div>
                <h3 class="course-title"><?= htmlspecialchars($course['title']) ?></h3>
                <p class="course-instructor">by <?= htmlspecialchars($course['instructor']) ?></p>
              </div>
            </div>
            
            <div class="course-meta">
              <span><i class="fas fa-clock"></i> <?= $course['duration'] ?> hours</span>
              <span><i class="fas fa-signal"></i> <?= htmlspecialchars($course['level']) ?></span>
              <span><i class="fas fa-dollar-sign"></i> <?= $course['price'] ?></span>
            </div>
            
            <p class="course-description"><?= htmlspecialchars($course['description']) ?></p>
            
            <div class="course-actions">
              <?php if ($is_enrolled): ?>
                <button class="btn btn-disabled" disabled>
                  <i class="fas fa-check"></i> Enrolled
                </button>
              <?php else: ?>
                <form method="POST" action="enroll_course.php" style="display: inline;">
                  <input type="hidden" name="course_id" value="<?= $course['course_id'] ?>">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Enroll Now
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