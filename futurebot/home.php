<?php
session_start();
require 'db.php';

// ----------------------
// Fetch approved student projects
// ----------------------
$projects_sql = "SELECT * FROM student_projects WHERE approved = 1 ORDER BY created_at DESC";
$projects = $conn->query($projects_sql);

// ----------------------
// Fetch approved mentor posts (type = 'mentorship')
// ----------------------
$mentor_sql = "
    SELECT jp.*, u.username AS full_name, u.user_id, u.role, u.photo
    FROM job_posts jp
    LEFT JOIN users u ON jp.user_id = u.user_id
    WHERE jp.approved = 1 AND jp.type = 'mentorship'
    ORDER BY jp.created_at DESC
";
$mentors = $conn->query($mentor_sql);

// ----------------------
// Fetch approved company/internship posts
// ----------------------
$company_sql = "
    SELECT jp.*, c.company_name AS full_name, c.id AS company_id, c.photo
    FROM job_posts jp
    LEFT JOIN companies c ON jp.user_id = c.id
    WHERE jp.approved = 1
      AND jp.type = 'internship'
      AND jp.source_page = 'company_profile'
    ORDER BY jp.created_at DESC
";
$companies_posts = $conn->query($company_sql);

// Dummy variables to avoid undefined errors
$user_skills = $user_skills ?? [];
$total_courses = $total_courses ?? 0;
$total_projects = $total_projects ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>FutureBot Home</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
body { background: linear-gradient(135deg, #e4fcf8ff 0%, #d3dbffff 100%); color: #04395e; min-height: 100vh; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding-top: 60px; }
nav { width: 100%; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; background-color: #fff; box-shadow: 0 2px 10px rgba(97, 95, 95, 0.84); position: fixed; top: 0; z-index: 1000; }
nav .logo { font-size: 1.8rem; font-weight: bold; color: darkblue; user-select: none; }
nav .nav-buttons { display: flex; gap: 5px; align-items: center; }
.btn-primary, .apply-btn button, .request-btn button, .offer-btn button, .send-offer-btn button, .filter-btn button { font-weight: 600; font-size: 1rem; padding: 10px 22px; border-radius: 30px; border: none; cursor: pointer; background: linear-gradient(90deg, #24153e, #00c6ff); color: white; transition: background 0.3s ease, box-shadow 0.3s ease; }
.btn-primary:hover, .apply-btn button:hover, .request-btn button:hover, .offer-btn button:hover, .send-offer-btn button:hover { box-shadow: 0 6px 15px rgba(36, 21, 62, 0.5); }
.container { max-width: 900px; margin: 40px auto 60px; padding: 0 20px; }
h1 { text-align: center; margin-bottom: 50px; font-weight: 900; color: #34495e; font-size: 3rem; letter-spacing: 1.5px; }
.card { border-radius: 1rem; box-shadow: 0 8px 20px rgba(4, 57, 94, 0.2); background: #ffffffcc; color: #04395e; margin-bottom: 50px; padding: 0; transition: transform 0.3s ease, box-shadow 0.3s ease; }
.card:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(4, 57, 94, 0.3); }
.card-header { font-weight: 900; font-size: 1.5rem; padding: 20px 30px; border-radius: 1rem 1rem 0 0; letter-spacing: 1.1px; color: white; user-select: none; background-color: #0466c8; box-shadow: 0 6px 16px rgba(4, 102, 200, 0.6); }
.card-body { padding: 30px 40px; }
.post-item { border: 1px solid #cfd8dc; border-radius: 1rem; padding: 24px 30px; margin-bottom: 30px; background: #fff; box-shadow: 0 6px 12px rgba(4, 57, 94, 0.06); transition: box-shadow 0.3s ease, background-color 0.3s ease; position: relative; }
.post-item:hover { box-shadow: 0 10px 22px rgba(4, 102, 200, 0.25); background-color: #f0f7ff; }
.post-item h5 { font-weight: 700; margin-bottom: 10px; color: #0466c8; font-size: 1.3rem; }
.post-item small { color: #607d8b; font-weight: 600; }
.post-item p { margin-top: 12px; white-space: pre-wrap; line-height: 1.6; font-size: 1rem; color: #34495e; }
.remove-btn { position: absolute; top: 15px; right: 15px; background: #e74c3c; color: white; border: none; border-radius: 5px; padding: 5px 10px; font-size: 0.8rem; cursor: pointer; }
.remove-btn:hover { background: #c0392b; }
.mentor-link { display: inline-block; margin-top: 8px; font-weight: 700; font-size: 0.95rem; color: #0466c8; text-decoration: none; border-bottom: 2px solid transparent; transition: border-bottom-color 0.25s ease; user-select: none; }
.mentor-link:hover { border-bottom-color: #0466c8; }
</style>
</head>
<body>
<nav>
  <a href="#" class="logo">FutureBot</a>
  <div class="nav-buttons dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
      🔍 Filter
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
      <li><a class="dropdown-item" href="wishlist.php">💖 Wishlists</a></li>
      <li><a class="dropdown-item" href="all_books.php">📚 Explore Skill Books</a></li>
      <li><a class="dropdown-item" href="home.php">🏠 Home</a></li>
      <li><a class="dropdown-item" href="register_details.php">📝 Register Form</a></li>
    </ul>
  </div>
</nav>

<div class="container">
<h1>Welcome to FutureBot!</h1>

<!-- Student Projects -->
<div class="card">
<div class="card-header">🛠 Student Projects (Approved)</div>
<div class="card-body">
<?php if ($projects && $projects->num_rows > 0): ?>
    <?php while ($proj = $projects->fetch_assoc()): ?>
        <div class="post-item">
            <form method="POST" action="remove_project.php" style="display:inline;">
                <input type="hidden" name="project_id" value="<?= $proj['id'] ?>">
                <button type="submit" class="remove-btn" onclick="return confirm('Are you sure you want to remove this project?');">❌ Remove</button>
            </form>
            <h5>📌 <?= htmlspecialchars($proj['title']) ?></h5>
            <p><?= nl2br(htmlspecialchars($proj['description'])) ?></p>
            <small>🛠 <strong>Skills:</strong> <?= htmlspecialchars($proj['skills_used']) ?></small><br/>
            <small>👤 <strong>Submitted by:</strong> <?= htmlspecialchars($proj['user_email']) ?></small>

            <?php if (isset($_SESSION['role']) && strcasecmp($_SESSION['role'], 'company') === 0): ?>
              <form method="POST" action="send_offer.php" class="offer-btn">
                  <input type="hidden" name="project_id" value="<?= $proj['id'] ?>">
                  <input type="hidden" name="student_email" value="<?= htmlspecialchars($proj['user_email']) ?>">
                  <button type="submit" onclick="return confirm('Send a job offer to this student?');">💼 Job Offer / Hire</button>
              </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>⚠️ No approved projects yet.</p>
<?php endif; ?>
</div>
</div>

<!-- Mentor Posts -->
<div class="card">
<div class="card-header">💡 Mentor Posts</div>
<div class="card-body">
<?php if ($mentors && $mentors->num_rows > 0): ?>
    <?php while ($mentor = $mentors->fetch_assoc()): ?>
        <?php
        $display_name = !empty($mentor['full_name']) ? $mentor['full_name'] : 'Unknown';
        $profile_pic = (!empty($mentor['photo']) && file_exists('uploads/' . $mentor['photo']))
                       ? 'uploads/' . $mentor['photo']
                       : "https://ui-avatars.com/api/?name=" . urlencode($display_name) . "&background=0466c8&color=fff&rounded=true&size=64";
        ?>
        <div class="post-item">
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $mentor['user_id']): ?>
                <form method="POST" action="remove_hiring.php" style="display:inline;">
                    <input type="hidden" name="hiring_id" value="<?= $mentor['id'] ?>">
                    <button type="submit" class="remove-btn" onclick="return confirm('Are you sure you want to remove this post?');">❌ Remove</button>
                </form>
            <?php endif; ?>
            <div style="display:flex; align-items:center; margin-bottom:10px;">
              <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile" width="50" height="50" style="border-radius:50%; margin-right:10px;">
              <div>
                <h5 style="margin:0;">👤 <?= htmlspecialchars($display_name) ?></h5>
                <small class="text-muted">(Mentor)</small>
              </div>
            </div>
            <p>📍 <strong>Location:</strong> <?= htmlspecialchars($mentor['location'] ?? 'N/A') ?></p>
            <p>📝 <strong>Description:</strong> <?= nl2br(htmlspecialchars($mentor['description'] ?? '')) ?></p>
            <p>💡 <strong>Subjects / Skills:</strong> <?= htmlspecialchars($mentor['skills'] ?? 'N/A') ?></p>
            <p>🎓 <strong>Experience / Qualifications:</strong> <?= htmlspecialchars($mentor['experience'] ?? 'N/A') ?></p>
            <p>⏰ <strong>Availability:</strong> <?= htmlspecialchars($mentor['availability'] ?? 'N/A') ?></p>
            <p>💰 <strong>Fee / Rate:</strong> <?= htmlspecialchars($mentor['fee'] ?? 'N/A') ?></p>
            <p>📖 <strong>Bio:</strong> <?= nl2br(htmlspecialchars($mentor['bio'] ?? '')) ?></p>
            <a href="mentor_profile.php?user_id=<?= urlencode($mentor['user_id']) ?>" class="mentor-link">👁️ View Profile</a>

            <?php if (isset($_SESSION['user_id']) && strcasecmp($_SESSION['role'], 'student') === 0): ?>
              <form method="POST" action="send_request.php" class="request-btn">
                  <input type="hidden" name="hire_post_id" value="<?= $mentor['id'] ?>" />
                  <button type="submit" onclick="return confirm('Send request for this mentor?');">📩 Send Request</button>
              </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>⚠️ No mentor posts.</p>
<?php endif; ?>
</div>
</div>

<!-- Company / Internship Posts -->
<div class="card">
<div class="card-header">💼 Internship / Company Offers</div>
<div class="card-body">
<?php if ($companies_posts && $companies_posts->num_rows > 0): ?>
    <?php while ($comp_post = $companies_posts->fetch_assoc()): ?>
        <?php
        $display_name = !empty($comp_post['full_name']) ? $comp_post['full_name'] : 'Unknown Company';
        $profile_pic = (!empty($comp_post['photo']) && file_exists('uploads/' . $comp_post['photo']))
                       ? 'uploads/' . $comp_post['photo']
                       : "https://ui-avatars.com/api/?name=" . urlencode($display_name) . "&background=0466c8&color=fff&rounded=true&size=64";
        ?>
        <div class="post-item">
            <div style="display:flex; align-items:center; margin-bottom:10px;">
              <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile" width="50" height="50" style="border-radius:50%; margin-right:10px;">
              <div>
                <h5 style="margin:0;">🏢 <?= htmlspecialchars($display_name) ?></h5>
                <small class="text-muted">(Company)</small>
              </div>
            </div>
            <h5>📄 <?= htmlspecialchars($comp_post['title']) ?></h5>
            <p>📝 <?= nl2br(htmlspecialchars($comp_post['description'] ?? '')) ?></p>
            <a href="company_profile.php?id=<?= urlencode($comp_post['company_id']) ?>" class="mentor-link">👁️ View Profile</a>

            <?php if (isset($_SESSION['user_id']) && strcasecmp($_SESSION['role'], 'student') === 0): ?>
              <form method="POST" action="apply_hire.php" class="apply-btn">
                  <input type="hidden" name="hire_post_id" value="<?= $comp_post['id'] ?>" />
                  <button type="submit" onclick="return confirm('Are you sure you want to apply?');">📝 Apply</button>
              </form>
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>⚠️ No company or internship posts.</p>
<?php endif; ?>
</div>
</div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
