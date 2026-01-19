<?php
session_start();
require_once 'db.php';

// Handle notification deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_notification'])) {
    $notifId = intval($_POST['delete_notification']);
    $stmt = $conn->prepare("DELETE FROM appointed_list WHERE id = ?");
    $stmt->bind_param("i", $notifId);
    $stmt->execute();
    $stmt->close();
}

// Fetch notifications again after potential deletion
$notifications = [];
$notif_result = $conn->query("SELECT * FROM appointed_list ORDER BY action_time DESC");
if ($notif_result && $notif_result->num_rows > 0) {
    while ($row = $notif_result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

// Load appointed mentors and ratings from session
$appointed = $_SESSION['appointed_mentors'] ?? [];
$ratings = $_SESSION['mentor_ratings'] ?? [];

$mentor_removed = false;

// Handle accept/reject message
$action_message = '';
if (isset($_GET['action']) && isset($_GET['mentor'])) {
    $mentorName = htmlspecialchars($_GET['mentor']);
    if ($_GET['action'] === 'accept') {
        $action_message = "✅ Request accepted by {$mentorName}";
    } elseif ($_GET['action'] === 'reject') {
        $action_message = "❌ Request rejected by {$mentorName}";
    }
}

// Handle remove request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_email'])) {
        $emailToRemove = $_POST['remove_email'];
        $_SESSION['appointed_mentors'] = array_values(array_filter($appointed, function($mentor) use ($emailToRemove) {
            return $mentor['email'] !== $emailToRemove;
        }));
        $_SESSION['success_message'] = 'Mentor removed successfully!';
        header("Location: appointed_mentors.php?removed=1");
        exit;
    }

    // Handle rating submission
    if (isset($_POST['rate_email']) && isset($_POST['rating'])) {
        $email = $_POST['rate_email'];
        $rating = intval($_POST['rating']);
        $_SESSION['mentor_ratings'][$email] = $rating;
        header("Location: appointed_mentors.php");
        exit;
    }
}

// Show success message if set
$success_message = '';
if (isset($_GET['removed']) && $_GET['removed'] == 1) {
    $success_message = 'Mentor removed successfully!';
    $mentor_removed = true;
    $appointed = []; // Hide mentor list
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Appointed Mentors - FutureBot</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: linear-gradient(to right, #f7e7f0, #ffffff);
      color: #555;
    }
    .navbar {
      background-color: #fff;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 6px rgba(46, 44, 44, 1);
    }
    .navbar h1 {
      margin: 0;
      color: #2a264bff;
    }
    .navbar a {
      text-decoration: none;
      color: #585586ff;
      font-weight: bold;
      margin-left: 15px;
    }
    .container {
      max-width: 800px;
      margin: 40px auto;
      padding: 20px;
      animation: fadeInPop 0.8s ease-out forwards;
      opacity: 0;
      transform: scale(0.95);
    }
    @keyframes fadeInPop {
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
    .mentor {
      background: #fff;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .mentor h3 {
      margin: 0;
      color: #333;
    }
    .mentor p {
      margin: 6px 0;
    }
    .remove-button, .rate-button, .back-button, .delete-button {
      background-color: #d97b93;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 8px 14px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px;
    }
    .remove-button:hover, .rate-button:hover, .back-button:hover, .delete-button:hover {
      background-color: #c46c85;
    }
    select {
      padding: 5px 10px;
      margin-top: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    .rating {
      margin-top: 10px;
    }
    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 15px;
      border-radius: 8px;
      border: 1px solid #c3e6cb;
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
    }
    .action-message {
      background-color: #e8f0fe;
      color: #1a237e;
      padding: 15px;
      border-radius: 8px;
      border: 1px solid #b0bec5;
      margin-bottom: 20px;
      text-align: center;
      font-weight: bold;
    }
    .notification {
      background-color: #f4eab5ff;
      color: #856404;
      padding: 10px 15px;
      border-radius: 20px;
      border: 1px solid #696761ff;
      margin-bottom: 12px;
      font-weight: bold;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .center {
      text-align: center;
    }
    .delete-form {
      margin: 0;
    }
  </style>
</head>
<body>

<div class="navbar">
  <h1>Appointed Mentors</h1>
  <div>
    <a href="mentor_suggestions.php">← Back to Suggestions</a>
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="container">

  <!-- Notifications -->
  <?php if (!empty($notifications)): ?>
    <?php foreach ($notifications as $index => $note): ?>
      <div class="notification" id="notif-<?= $index ?>">
        <span>
          <?= htmlspecialchars($note['student_name']) ?>'s request was 
          <strong><?= ucfirst(htmlspecialchars($note['status'])) ?></strong> by 
          <em><?= htmlspecialchars($note['action_by']) ?></em> on 
          <?= htmlspecialchars($note['action_time']) ?>
        </span>
        <form class="delete-form" method="POST" style="display:inline;">
          <input type="hidden" name="delete_notification" value="<?= $note['id'] ?>">
          <button type="submit" class="delete-button">Delete</button>
        </form>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (!empty($action_message)): ?>
    <div class="action-message">
      <?= $action_message ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($success_message)): ?>
    <div class="success-message">
      <?= htmlspecialchars($success_message) ?>
    </div>
    <?php if ($mentor_removed): ?>
      <div class="center">
        <a href="appointed_mentors.php">
          <button class="back-button">Back</button>
        </a>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <?php if (empty($appointed) && !$mentor_removed): ?>
    <p>No mentors appointed yet.</p>
  <?php endif; ?>

  <?php if (!empty($appointed)): ?>
    <?php foreach ($appointed as $mentor): ?>
      <div class="mentor">
        <h3><?= htmlspecialchars($mentor['company_name']) ?></h3>
        <p><strong>Location:</strong> <?= htmlspecialchars($mentor['location']) ?></p>
        <p><strong>Rating:</strong> <?= htmlspecialchars($mentor['rating']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($mentor['email']) ?></p>

        <form method="post" style="display:inline;">
          <input type="hidden" name="remove_email" value="<?= htmlspecialchars($mentor['email']) ?>">
          <button type="submit" class="remove-button">Remove</button>
        </form>

        <form method="post" class="rating">
          <input type="hidden" name="rate_email" value="<?= htmlspecialchars($mentor['email']) ?>">
          <label for="rating">Rate this mentor:</label>
          <select name="rating" onchange="this.form.submit()">
            <option value="">Select</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
              <option value="<?= $i ?>" <?= (isset($ratings[$mentor['email']]) && $ratings[$mentor['email']] == $i) ? 'selected' : '' ?>><?= $i ?> ★</option>
            <?php endfor; ?>
          </select>
        </form>

        <?php if (isset($ratings[$mentor['email']])): ?>
          <p><strong>Your Rating:</strong> <?= $ratings[$mentor['email']] ?> ★</p>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

</div>

<!-- Auto-remove notifications from DOM after 5 minutes -->
<script>
  window.onload = function() {
    setTimeout(() => {
      document.querySelectorAll('.notification').forEach(el => el.remove());
    }, 300000); // 5 minutes = 300000 ms
  };
</script>

</body>
</html>
