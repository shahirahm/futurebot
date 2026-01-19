<?php
session_start();
require_once 'db.php';

// Load PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid job ID.");
}

$job_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM jobposts WHERE job_id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Job not found.");
}

$job = $result->fetch_assoc();
$stmt->close();

$applicationStatus = null;
$applicationMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply'])) {
    $userEmail = $_SESSION['user_email'] ?? 'applicant@example.com';

    if (empty($job['company_email']) || !filter_var($job['company_email'], FILTER_VALIDATE_EMAIL)) {
        $applicationStatus = 'error';
        $applicationMessage = "Invalid or missing company email address. Cannot send application.";
    } else {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'samihamaisha231@gmail.com';
            $mail->Password = '1234567'; // ⚠️ Replace with Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('samihamaisha231@gmail.com', 'FutureBot Application');
            $mail->addAddress($job['company_email'], $job['company']);

            $mail->isHTML(true);
            $mail->Subject = "Job Application for " . $job['title'];
            $mail->Body = "<p>Dear " . htmlspecialchars($job['company']) . ",</p>
                <p>You have received a new job application from: <strong>" . htmlspecialchars($userEmail) . "</strong>.</p>
                <p>Job Title: " . htmlspecialchars($job['title']) . "</p>
                <p>Please contact the applicant to proceed with the interview process.</p>
                <p>Best regards,<br>FutureBot Team</p>";

            $mail->send();

            $mail2 = new PHPMailer(true);
            $mail2->isSMTP();
            $mail2->Host = 'smtp.gmail.com';
            $mail2->SMTPAuth = true;
            $mail2->Username = 'samihamaisha231@gmail.com';
            $mail2->Password = '1234567'; // ⚠️ Replace with Gmail App Password
            $mail2->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail2->Port = 587;

            $mail2->setFrom('samihamaisha231@gmail.com', 'FutureBot Notification');
            $mail2->addAddress('samihamaisha231@gmail.com', 'Samiha Maisha');

            $mail2->isHTML(true);
            $mail2->Subject = "New Job Application Received";
            $mail2->Body = "<p>A new application was submitted for the job: <strong>" . htmlspecialchars($job['title']) . "</strong> at <strong>" . htmlspecialchars($job['company']) . "</strong>.</p>
                <p>Applicant Email: <strong>" . htmlspecialchars($userEmail) . "</strong></p>
                <p>Check your FutureBot admin panel or database for more details.</p>";

            $mail2->send();

            $applicationStatus = 'success';
            $applicationMessage = "Apply Successful";
        } catch (Exception $e) {
            $applicationStatus = 'error';
            $applicationMessage = "Application failed to send. Mailer Error: " . $mail->ErrorInfo;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title><?= htmlspecialchars($job['title']) ?> - Job Details</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<style>
  * {
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(to right, #e4cdd3ff, #e8dce3ff, #ffffffff);
    color: #3a5280ff;
    margin: 0;
    padding: 0;
    line-height: 1.4;
    font-size: 15px;
  }

  nav {
    background: #fefdffff;
    padding: 12px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .logo {
    font-size: 30px;
    font-weight: bold;
    color: #45468dff;
  }

  .nav-buttons {
    display: flex;
    gap: 10px;
  }

  .nav-buttons a {
    background-color: #c6a0c3ff;
    color: #fff;
    padding: 6px 14px;
    border-radius: 18px;
    font-weight: 600;
    text-decoration: none;
    font-size: 14px;
    transition: 0.3s ease;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
  }

  .nav-buttons a:hover {
    background-color: #3f5086ff;
  }

  .container {
    max-width: 340px;
    margin: 120px auto 20px auto;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.15);
    padding: 18px 16px;
    overflow: hidden;
  }

  h1 {
    font-size: 1.4rem;
    margin-bottom: 10px;
    color: #2c5282;
  }

  h2 {
    font-size: 1.1rem;
    margin: 16px 0 8px;
  }

  .meta {
    margin: 6px 0;
    color: #4a5568;
    font-size: 14px;
  }

  .section {
    margin-top: 20px;
  }

  .btn {
    display: inline-block;
    margin-top: 18px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 600;
    color: white;
    background-color: #c798baff;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(49, 130, 206, 0.2);
    transition: background 0.3s;
  }

  .btn:hover {
    background-color: #51579dff;
  }

  .message {
    margin-top: 16px;
    padding: 10px 14px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 14px;
  }

  .success {
    background-color: #c6f6d5;
    color: #276749;
    border: 1px solid #9ae6b4;
  }

  .error {
    background-color: #fed7d7;
    color: #9b2c2c;
    border: 1px solid #feb2b2;
  }

  @media (max-width: 480px) {
    .container {
      margin: 80px 16px 16px;
      padding: 16px;
    }

    nav {
      flex-direction: column;
      gap: 10px;
      padding: 10px 20px;
    }

    .nav-buttons {
      justify-content: center;
    }
  }
</style>
</head>
<body>

<nav>
  <div class="logo">FutureBot</div>
  <div class="nav-buttons">
    <a href="career_suggestions.php">← Back</a>
    <a href="skill_develop.php">Skill Develop</a>
  </div>
</nav>

<div class="container">
  <h1><?= htmlspecialchars($job['title']) ?></h1>
  <div class="meta"><strong>Company:</strong> <?= htmlspecialchars($job['company']) ?></div>
  <div class="meta"><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></div>
  <div class="meta"><strong>Type:</strong> <?= htmlspecialchars($job['job_type']) ?></div>
  <div class="meta"><strong>Salary:</strong> <?= htmlspecialchars($job['salary_range'] ?: 'Not specified') ?></div>
  <div class="meta"><strong>Deadline:</strong> <?= htmlspecialchars($job['deadline']) ?></div>
  <div class="meta"><strong>Required Skills:</strong> <?= htmlspecialchars($job['required_skills']) ?></div>

  <div class="section">
    <h2>Description</h2>
    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
  </div>

  <?php if ($applicationStatus): ?>
    <div class="message <?= $applicationStatus ?>">
      <?= htmlspecialchars($applicationMessage) ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <button type="submit" name="apply" class="btn" aria-label="Apply for <?= htmlspecialchars($job['title']) ?>">Apply Now</button>
  </form>
</div>

</body>
</html>
