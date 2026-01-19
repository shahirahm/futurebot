<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$viewer_id = $_SESSION['user_id'];
$viewer_role = $_SESSION['role'];
$profile_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : $viewer_id;

// Check if viewing own profile
$is_own_profile = ($viewer_id === $profile_user_id && $viewer_role === 'mentor');

$error = '';
$success = '';

// Fetch user data for profile_user_id
$stmt = $conn->prepare("SELECT username, full_name, bio, profile_pic, role, email, phone FROM users WHERE user_id = ?");
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$stmt->bind_result($username, $full_name, $bio, $profile_pic, $role, $email, $phone);
if (!$stmt->fetch()) {
    $stmt->close();
    die("Mentor profile not found.");
}
$stmt->close();

// Additional mentor details including demo link & schedule
$university = $subject = $recent_profession = $location = $demo_link = $demo_schedule = '';

$stmt = $conn->prepare("SELECT university, subject, recent_profession, location, demo_link, demo_schedule FROM mentor_details WHERE user_id = ?");
$stmt->bind_param("i", $profile_user_id);
$stmt->execute();
$stmt->bind_result($university, $subject, $recent_profession, $location, $demo_link, $demo_schedule);
$stmt->fetch();
$stmt->close();

// Handle profile update only if own profile
if ($is_own_profile && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = trim($_POST['full_name']);
    $bio = trim($_POST['bio']);
    $university = trim($_POST['university']);
    $subject = trim($_POST['subject']);
    $recent_profession = trim($_POST['recent_profession']);
    $location = trim($_POST['location']);
    $phone = trim($_POST['phone']);
    $demo_link = trim($_POST['demo_link']);
    $demo_schedule = trim($_POST['demo_schedule']);

    // Image upload
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = $viewer_id . '_' . time() . '.' . $fileExtension;
            $uploadFileDir = 'uploads/profile_pics/';
            if (!is_dir($uploadFileDir)) mkdir($uploadFileDir, 0755, true);
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_pic = $dest_path;
            } else {
                $error = "There was an error uploading the profile picture.";
            }
        } else {
            $error = "Allowed file types: " . implode(', ', $allowedfileExtensions);
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, bio = ?, profile_pic = ?, phone = ? WHERE user_id = ?");
        $stmt->bind_param("ssssi", $full_name, $bio, $profile_pic, $phone, $viewer_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("SELECT COUNT(*) FROM mentor_details WHERE user_id = ?");
        $stmt->bind_param("i", $viewer_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $stmt = $conn->prepare("UPDATE mentor_details SET university = ?, subject = ?, recent_profession = ?, location = ?, demo_link = ?, demo_schedule = ? WHERE user_id = ?");
            $stmt->bind_param("ssssssi", $university, $subject, $recent_profession, $location, $demo_link, $demo_schedule, $viewer_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO mentor_details (user_id, university, subject, recent_profession, location, demo_link, demo_schedule) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", $viewer_id, $university, $subject, $recent_profession, $location, $demo_link, $demo_schedule);
        }
        $stmt->execute();
        $stmt->close();

        $success = "Profile updated successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Mentor Profile - FutureBot</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: linear-gradient(145deg, #dde4f3, #f0f4ff); padding-top: 70px; color: #2e323c; animation: fadeSlideIn 0.8s ease; }
@keyframes fadeSlideIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.user-avatar img { width: 90px; height: 90px; border-radius: 100px; object-fit: cover; }
.about p { font-size: 0.85rem; }
.form-control { font-size: 0.75rem; padding: 3px 6px; height: 28px; }
textarea.form-control { height: auto; min-height: 30px; padding: 4px 6px; font-size: 0.75rem; border-radius: 5px; }
.card .card-body { padding: 0.8rem; }
.navbar-custom { background-color: #ffffff; box-shadow: 0 2px 8px rgba(14, 12, 12, 1); }
.navbar-custom .navbar-brand, .navbar-custom .nav-link { color: #5c2df5ff; }
.navbar-custom .nav-link:hover { color: #641bdaff; }
.btn-dashboard {    background: linear-gradient(90deg, #24153e, #00c6ff); color: white; }
.btn-dashboard:hover { background-color: #341f97; color: white; }
@media (min-width: 800px) { .col-md-8 { max-width: 80%; flex: 0 0 50%; margin-left: 190px; margin-top: 35px; } .col-md-4 { margin-top: 40px; } }
.alert { animation: fadeInUp 0.6s ease-in-out; position: relative; z-index: 999; transform-origin: top; }
@keyframes fadeInUp { 0% { opacity: 0; transform: translateY(40px) scale(0.95); } 100% { opacity: 1; transform: translateY(0) scale(1); } }
.demo-btn { display: inline-block; margin-top: 10px; padding: 6px 12px;    background: linear-gradient(90deg, #24153e, #00c6ff); color: white; font-weight: bold; border-radius: 10px; text-decoration: none; transition: background-color 0.3s ease; }
.demo-btn:hover { background-color: #0b8043; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container">
        <span class="navbar-brand">Welcome, <?= htmlspecialchars($username) ?> (<?= htmlspecialchars(ucfirst($role)) ?>)</span>
        <div class="ml-auto">
            <a href="dashboard.php" class="btn btn-dashboard btn-sm">Dashboard</a>
        </div>
    </div>
</nav>

<div class="container">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success" id="successMessage"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="user-avatar mb-3">
                        <img src="<?= $profile_pic ? htmlspecialchars($profile_pic) : 'uploads/default_profile.png' ?>" alt="Profile Picture">
                    </div>
                    <h5 class="user-name"><?= htmlspecialchars($full_name ?: $username) ?></h5>
                    <p class="text-muted mb-1"><strong>University:</strong> <?= htmlspecialchars($university) ?></p>
                    <p class="text-muted mb-1"><strong>Subject:</strong> <?= htmlspecialchars($subject) ?></p>
                    <p class="text-muted mb-1"><strong>Recent Profession:</strong> <?= htmlspecialchars($recent_profession) ?></p>
                    <p class="text-muted mb-1"><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                    <p class="text-muted mb-1"><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
                    <p class="text-muted mb-1"><strong>Location:</strong> <?= htmlspecialchars($location) ?></p>
                    <div class="about mt-3">
                        <h6>About</h6>
                        <p><?= nl2br(htmlspecialchars($bio)) ?></p>
                    </div>

                    <?php if (!empty($demo_link)): ?>
                        <a href="<?= htmlspecialchars($demo_link) ?>" target="_blank" class="demo-btn">📹 Join Demo Class</a>
                        <?php if (!empty($demo_schedule)): ?>
                            <p class="mt-1 text-muted" style="font-size: 0.75rem;">🕒 Scheduled: <?= date('d M Y, H:i', strtotime($demo_schedule)) ?></p>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($is_own_profile): ?>
                        <hr />
                        <div style="margin-top: 10px;">
                            <a href="mentor_requests.php" style="text-decoration: none;    background: linear-gradient(90deg, #24153e, #00c6ff); color: white; padding: 6px 12px; border-radius: 10px; font-size: 13px; font-weight: bold;">View Requests</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($is_own_profile): ?>
            <div class="col-md-8">
                <div class="card">
                    <form method="POST" enctype="multipart/form-data" class="card-body">
                        <h5 class="text-primary mb-3">Edit Profile</h5>

                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($full_name) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea name="bio" class="form-control"><?= htmlspecialchars($bio) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="university">University</label>
                            <input type="text" name="university" class="form-control" value="<?= htmlspecialchars($university) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" name="subject" class="form-control" value="<?= htmlspecialchars($subject) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="recent_profession">Recent Profession</label>
                            <input type="text" name="recent_profession" class="form-control" value="<?= htmlspecialchars($recent_profession) ?>">
                        </div>

                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($location) ?>">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($phone) ?>">
                        </div>

                        <div class="form-group">
                            <label for="profile_pic">Profile Picture (optional)</label>
                            <input type="file" name="profile_pic" class="form-control-file" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label for="demo_link">Demo Class Link (Google Meet)</label>
                            <input type="url" name="demo_link" class="form-control" value="<?= htmlspecialchars($demo_link) ?>" placeholder="https://meet.google.com/xxx-xxxx-xxx">
                        </div>

                        <div class="form-group">
                            <label for="demo_schedule">Demo Class Schedule</label>
                            <input type="datetime-local" name="demo_schedule" class="form-control" value="<?= $demo_schedule ? date('Y-m-d\TH:i', strtotime($demo_schedule)) : '' ?>">
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-success btn-sm">Update Profile</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
const successMsg = document.getElementById('successMessage');
if (successMsg) {
    setTimeout(() => {
        successMsg.style.opacity = '0';
        successMsg.style.transition = 'opacity 1s ease';
        setTimeout(() => successMsg.remove(), 1000);
    }, 3000);
}
</script>

</body>
</html>
