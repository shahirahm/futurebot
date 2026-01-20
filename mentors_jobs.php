<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: register.php");
    exit;
}



// Get user's skills from database
$email = $_SESSION['user_email'];
$stmt = $conn->prepare("SELECT skills FROM Users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($skills_str);
$stmt->fetch();
$stmt->close();

$user_skills = explode(", ", $skills_str);



// For simplicity, get mentors who match at least one skill
$placeholders = implode(',', array_fill(0, count($user_skills), '?'));
$types = str_repeat('s', count($user_skills));

// Mentors query: find mentors whose expertise_skills contain any user skill (simple LIKE example)
$sql = "SELECT name, bio, contact_info FROM Mentors WHERE ";
$whereClauses = [];
$params = [];
foreach ($user_skills as $skill) {
    $whereClauses[] = "expertise_skills LIKE CONCAT('%', ?, '%')";
    $params[] = $skill;
}
$sql .= implode(' OR ', $whereClauses);



$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$mentors = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Job posts: similarly, find jobs matching skills
$sql = "SELECT title, company, location, description, application_link FROM JobPosts WHERE ";
$whereClauses = [];
$params = [];
foreach ($user_skills as $skill) {
    $whereClauses[] = "required_skills LIKE CONCAT('%', ?, '%')";
    $params[] = $skill;
}
$sql .= implode(' OR ', $whereClauses);

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$jobs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Mentors & Jobs - FutureBot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f0f2f5;
      margin: 0; padding: 0;
      color: #333;
    }
    .container {
      max-width: 700px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    h1, h2 {
      color: #4267B2;
      text-align: center;
    }
    .mentor, .job {
      border: 1px solid #ddd;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
      background: #fafbfc;
    }
    .mentor h3, .job h3 {
      margin-top: 0;
      color: #2c3e50;
    }
    .contact {
      font-size: 0.9em;
      color: #555;
    }
    a.apply-link {
      display: inline-block;
      margin-top: 8px;
      color: white;
      background: #4267B2;
      padding: 8px 12px;
      border-radius: 6px;
      text-decoration: none;
    }
    a.apply-link:hover {
      background: #365899;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Mentors & Job Suggestions</h1>

    <h2>Mentors to Guide You</h2>
    <?php if (count($mentors) === 0): ?>
      <p>No mentors found matching your skills yet. Check back later!</p>
    <?php else: ?>
      <?php foreach ($mentors as $mentor): ?>
        <div class="mentor">
          <h3><?= htmlspecialchars($mentor['name']) ?></h3>
          <p><?= nl2br(htmlspecialchars($mentor['bio'])) ?></p>
          <p class="contact">Contact: <?= htmlspecialchars($mentor['contact_info']) ?></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <h2>Job Posts You Might Like</h2>
    <?php if (count($jobs) === 0): ?>
      <p>No job posts found matching your skills right now.</p>
    <?php else: ?>
      <?php foreach ($jobs as $job): ?>
        <div class="job">
          <h3><?= htmlspecialchars($job['title']) ?> at <?= htmlspecialchars($job['company']) ?></h3>
          <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
          <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
          <a class="apply-link" href="<?= htmlspecialchars($job['application_link']) ?>" target="_blank">Apply Now</a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
