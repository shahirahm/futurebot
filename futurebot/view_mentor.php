<?php
session_start();
require_once 'db.php';

if (!isset($_GET['id'])) {
    echo "Mentor ID missing.";
    exit;
}

$mentor_id = intval($_GET['id']);

// Fetch mentor details from mentor_details and users
$sql = "
    SELECT u.full_name, u.email, u.phone, u.profile_pic, u.bio,
           md.university, md.subject, md.recent_profession, md.location, md.demo_link, md.demo_schedule
    FROM mentor_details md
    INNER JOIN users u ON md.user_id = u.user_id
    WHERE md.user_id = $mentor_id
";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "Mentor not found.";
    exit;
}

$mentor = $result->fetch_assoc();
$demo_link = $mentor['demo_link'];
$demo_schedule = $mentor['demo_schedule'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mentor Profile - <?= htmlspecialchars($mentor['full_name']) ?></title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    background: linear-gradient(135deg, #e4fcf8ff 0%, #d3dbffff 100%);
    color: #04395e;
}
.container {
    max-width: 500px;
    margin: 50px auto;
    background: #fff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 6px 24px rgba(72,65,65,0.2);
    text-align: center;
}
h2 {
    color: darkblue;
    margin-bottom: 15px;
}
p {
    font-size: 1rem;
    margin: 8px 0;
}
.profile-pic {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 15px;
    border: 3px solid #04395e;
}
a {
    color: #1a73e8;
    text-decoration: none;
    font-weight: bold;
}
a:hover {
    text-decoration: underline;
}
.demo-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 8px 14px;
    background-color: #34a853;
    color: white;
    font-weight: bold;
    border-radius: 10px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}
.demo-btn:hover { background-color: #0b8043; }
.demo-info {
    margin-top: 10px;
    font-weight: bold;
    color: #555;
}
</style>
</head>
<body>

<div class="container">
    <img src="<?= $mentor['profile_pic'] ? htmlspecialchars($mentor['profile_pic']) : 'uploads/default_profile.png' ?>" alt="Profile Picture" class="profile-pic">
    <h2>🧑‍💼 <?= htmlspecialchars($mentor['full_name']) ?></h2>
    
    <p>🎓 University: 
        <a href="https://www.google.com/search?q=<?= urlencode($mentor['university']) ?>" target="_blank">
            <?= htmlspecialchars($mentor['university']) ?>
        </a>
    </p>
    
    <p>📚 Subject: <strong><?= htmlspecialchars($mentor['subject']) ?></strong></p>
    <p>💼 Recent Profession: <strong><?= htmlspecialchars($mentor['recent_profession']) ?></strong></p>
    <p>📍 Location: <?= htmlspecialchars($mentor['location']) ?></p>
    <p>📞 Phone: <?= htmlspecialchars($mentor['phone']) ?></p>
    <p>✉️ Email: <a href="mailto:<?= htmlspecialchars($mentor['email']) ?>"><?= htmlspecialchars($mentor['email']) ?></a></p>
    <p>📝 Bio: <?= nl2br(htmlspecialchars($mentor['bio'])) ?></p>

    <?php if (!empty($demo_link)): ?>
        <!-- Button to trigger modal -->
        <button type="button" class="btn demo-btn" data-toggle="modal" data-target="#demoModal">
            📹 View Demo Class
        </button>
    <?php endif; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="demoModal" tabindex="-1" aria-labelledby="demoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="demoModalLabel">Demo Class Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <p><strong>Google Meet Link:</strong></p>
        <a href="<?= htmlspecialchars($demo_link) ?>" target="_blank" class="btn btn-success mb-2">📹 Join Demo Class</a>
        <?php if (!empty($demo_schedule)): ?>
            <p class="demo-info">🗓️ Scheduled: <?= date('l, d M Y \a\t h:i A', strtotime($demo_schedule)) ?></p>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
