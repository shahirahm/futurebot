<?php
session_start();
require_once 'db.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header("Location: company_login.php");
    exit;
}

$companyId = $_SESSION['company_id'];

// Handle Internship Post Submission
$post_success = false;
$post_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_internship'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($description)) {
        $post_error = "Please fill in all fields.";
    } else {
        // ✅ Fixed: include source_page and type columns for proper home.php display
        $stmt = $conn->prepare("
            INSERT INTO job_posts (user_id, title, description, type, approved, source_page, created_at)
            VALUES (?, ?, ?, 'internship', 0, 'company_profile', NOW())
        ");
        $stmt->bind_param("iss", $companyId, $title, $description);

        if ($stmt->execute()) {
            $post_success = true;
        } else {
            $post_error = "Error saving internship post. Please try again.";
        }
        $stmt->close();
    }
}

// Fetch company info
$stmt = $conn->prepare("SELECT company_name, start_year, trade_license, email, phone, address FROM companies WHERE id = ?");
$stmt->bind_param("i", $companyId);
$stmt->execute();
$companyResult = $stmt->get_result();
$company = $companyResult->fetch_assoc();
$stmt->close();

if (!$company) {
    die("Company not found.");
}

// Fetch profile info
$stmt = $conn->prepare("SELECT bio, location, facilities, rating, logo_path FROM company_profiles WHERE company_id = ?");
$stmt->bind_param("i", $companyId);
$stmt->execute();
$profileResult = $stmt->get_result();
$profile = $profileResult->fetch_assoc();
$stmt->close();

// Fetch internships posted by this company
$stmt = $conn->prepare("
    SELECT id, title, description, approved, created_at 
    FROM job_posts 
    WHERE user_id = ? AND type = 'internship' 
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $companyId);
$stmt->execute();
$internshipsResult = $stmt->get_result();
$internships = $internshipsResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Company Profile - <?= htmlspecialchars($company['company_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <style>
        body { background-color: #f0f2f5; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif; }
        .profile-card, .post-internship-card, .internship-list-card { background: white; border-radius: 10px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); padding: 25px 30px; max-width: 720px; margin: 20px auto; }
        .profile-logo { max-width: 150px; border-radius: 8px; margin-bottom: 20px; }
        .edit-btn { position: absolute; top: 30px; right: 30px; }
        .rating-stars { color: #f7b500; font-size: 1.25rem; vertical-align: middle; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand" href="home.php">
      <i class="bi bi-house-door-fill"></i> Home
    </a>
  </div>
</nav>

<div class="profile-card shadow-sm position-relative">
    <a href="company_profile_edit.php?id=<?= urlencode($companyId) ?>" class="btn btn-outline-primary edit-btn" title="Edit Profile">
        <i class="bi bi-pencil-square"></i> Edit Profile
    </a>
    <div class="d-flex align-items-center mb-4">
        <?php if (!empty($profile['logo_path']) && file_exists($profile['logo_path'])): ?>
            <img src="<?= htmlspecialchars($profile['logo_path']) ?>" class="profile-logo me-4" />
        <?php else: ?>
            <div class="profile-logo bg-secondary text-white d-flex justify-content-center align-items-center fs-4">
                <?= strtoupper(substr($company['company_name'], 0, 2)) ?>
            </div>
        <?php endif; ?>
        <div>
            <h1><?= htmlspecialchars($company['company_name']) ?></h1>
            <p class="text-muted mb-1"><?= htmlspecialchars($profile['location'] ?? 'Location not specified') ?></p>
            <p class="text-muted mb-0 small">Started: <?= htmlspecialchars($company['start_year']) ?> | License: <?= htmlspecialchars($company['trade_license']) ?></p>
        </div>
    </div>
    <div class="mb-4">
        <h5>About</h5>
        <p><?= nl2br(htmlspecialchars($profile['bio'] ?? 'No bio available.')) ?></p>
    </div>
</div>

<!-- Post Internship Form -->
<div class="post-internship-card shadow-sm">
    <h4>Post a New Internship</h4>
    <?php if ($post_success): ?>
        <div class="alert alert-success">Internship posted successfully! It will be visible once approved by admin.</div>
    <?php elseif ($post_error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($post_error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Internship Title</label>
            <input type="text" class="form-control" id="title" name="title" required maxlength="255" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Internship Description</label>
            <textarea class="form-control" id="description" name="description" rows="5" required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
        </div>
        <button type="submit" name="post_internship" class="btn btn-primary">Post Internship</button>
    </form>
</div>

<!-- Internship List -->
<div class="internship-list-card shadow-sm">
    <h4>Your Internship Posts</h4>
    <?php if (empty($internships)): ?>
        <p class="text-muted">You have not posted any internships yet.</p>
    <?php else: ?>
        <?php foreach ($internships as $internship): ?>
            <div class="border rounded p-3 mb-3">
                <h5><?= htmlspecialchars($internship['title']) ?></h5>
                <p><?= nl2br(htmlspecialchars($internship['description'])) ?></p>
                <span class="badge <?= $internship['approved'] ? 'bg-success' : 'bg-warning text-dark' ?>">
                    <?= $internship['approved'] ? 'Approved' : 'Pending Approval' ?>
                </span>
                <small class="text-muted ms-2"><?= htmlspecialchars(date("M d, Y", strtotime($internship['created_at']))) ?></small>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
