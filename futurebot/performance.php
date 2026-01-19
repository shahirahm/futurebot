<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$user_email = $_SESSION['email'];
$performance_data = [];

// Fetch performance data for the last 30 days
$sql = "SELECT date, score, activity_type 
        FROM User_Performance 
        WHERE user_email = ? AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
        ORDER BY date ASC";
        
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $performance_data[] = $row;
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
  <title>Performance - FutureBot</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
  /* Add similar CSS structure as previous pages */
  </style>
</head>
<body>
  <!-- Similar structure to other pages but for performance analytics -->
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
      <h1>Performance Analytics</h1>
      <p>Track your learning progress and achievements</p>
    </div>

    <div class="dashboard-container">
      <div class="dashboard-card" style="grid-column: span 2;">
        <h2>30-Day Performance Trend</h2>
        <div style="height: 300px; margin-top: 20px; display: flex; align-items: flex-end; gap: 10px;">
          <?php
          // Group data by date for the chart
          $chart_data = [];
          foreach ($performance_data as $entry) {
              $chart_data[$entry['date']] = $entry['score'];
          }
          
          // Generate chart bars for last 30 days
          for ($i = 29; $i >= 0; $i--) {
              $date = date('Y-m-d', strtotime("-$i days"));
              $score = $chart_data[$date] ?? 0;
              $height = $score > 0 ? $score : 5; // Minimum height
          ?>
            <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
              <div style="background: linear-gradient(to top, #4361ee, #3a0ca3); width: 20px; border-radius: 4px 4px 0 0; height: <?= $height ?>%; transition: all 0.3s ease;"></div>
              <small style="margin-top: 5px; font-size: 10px; color: #7f8c8d;"><?= date('M j', strtotime($date)) ?></small>
            </div>
          <?php } ?>
        </div>
      </div>

      <div class="dashboard-card">
        <h2>Performance Summary</h2>
        <div style="margin-top: 20px;">
          <?php if (empty($performance_data)): ?>
            <p style="text-align: center; color: #7f8c8d;">No performance data available</p>
          <?php else: 
            $average_score = array_sum(array_column($performance_data, 'score')) / count($performance_data);
            $max_score = max(array_column($performance_data, 'score'));
            $min_score = min(array_column($performance_data, 'score'));
          ?>
            <div style="display: grid; gap: 15px;">
              <div style="display: flex; justify-content: between; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <span>Average Score</span>
                <strong style="color: #4361ee;"><?= round($average_score, 1) ?>%</strong>
              </div>
              <div style="display: flex; justify-content: between; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <span>Highest Score</span>
                <strong style="color: #2ecc71;"><?= $max_score ?>%</strong>
              </div>
              <div style="display: flex; justify-content: between; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <span>Lowest Score</span>
                <strong style="color: #e74c3c;"><?= $min_score ?>%</strong>
              </div>
              <div style="display: flex; justify-content: between; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <span>Total Activities</span>
                <strong style="color: #4361ee;"><?= count($performance_data) ?></strong>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="dashboard-card" style="grid-column: span 3;">
        <h2>Recent Activities</h2>
        <div style="margin-top: 20px;">
          <?php if (empty($performance_data)): ?>
            <p style="text-align: center; color: #7f8c8d; padding: 40px;">No recent activities recorded</p>
          <?php else: ?>
            <div style="display: grid; gap: 10px;">
              <?php foreach (array_slice($performance_data, -10) as $activity): ?>
                <div style="display: flex; justify-content: between; align-items: center; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                  <div>
                    <strong><?= htmlspecialchars($activity['activity_type']) ?></strong>
                    <div style="font-size: 0.9rem; color: #7f8c8d;"><?= date('M j, Y', strtotime($activity['date'])) ?></div>
                  </div>
                  <div style="font-weight: 600; color: #4361ee;"><?= $activity['score'] ?>%</div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
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