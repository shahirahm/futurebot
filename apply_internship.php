<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: register.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid internship ID.");
}

$internship_id = intval($_GET['id']);
$user_email = $_SESSION['user_email'];

// Get the application link for redirect
$stmt = $conn->prepare("SELECT application_link, title FROM micro_internships WHERE id = ?");
$stmt->bind_param("i", $internship_id);
$stmt->execute();
$stmt->bind_result($application_link, $internship_title);
if (!$stmt->fetch()) {
    $stmt->close();
    die("Internship not found.");
}
$stmt->close();

// Log the application in the database
$log_stmt = $conn->prepare("INSERT INTO internship_applications (user_email, internship_id, applied_at) VALUES (?, ?, NOW())");
$log_stmt->bind_param("si", $user_email, $internship_id);
$log_stmt->execute();
$log_stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Application Submitted - <?= htmlspecialchars($internship_title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #c8f4edff 0%, #ACB6E5 100%);
      color: #04395e;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem;
    }
    .confirmation-box {
      background: #ffffffcc;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(4, 57, 94, 0.3);
      max-width: 500px;
      text-align: center;
    }
    .countdown {
      font-weight: bold;
      color: #0466c8;
    }
    a.btn-primary {
      background: #0466c8;
      border: none;
    }
    a.btn-primary:hover {
      background: #0353a4;
    }
  </style>
</head>
<body>

<div class="confirmation-box">
  <h2>Thank you for applying!</h2>
  <p>Your application for <strong><?= htmlspecialchars($internship_title) ?></strong> has been recorded.</p>
  <p>You will be redirected to the application page in <span class="countdown" id="countdown">5</span> seconds.</p>
  <p>If you are not redirected automatically, <a href="<?= htmlspecialchars($application_link) ?>" target="_blank" class="btn btn-primary">Click here to continue</a>.</p>
</div>

<script>
  let countdownElement = document.getElementById('countdown');
  let secondsLeft = 5;

  const interval = setInterval(() => {
    secondsLeft--;
    countdownElement.textContent = secondsLeft;
    if (secondsLeft <= 0) {
      clearInterval(interval);
      window.location.href = "<?= htmlspecialchars($application_link) ?>";
    }
  }, 1000);
</script>

</body>
</html>
