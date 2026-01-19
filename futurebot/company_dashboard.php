<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = $_GET['message'] ?? '';

// Fetch basic company info
$stmt1 = $conn->prepare("SELECT name, start_year FROM companies WHERE id = ?");
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$company_basic = $result1->fetch_assoc();
$stmt1->close();

// Fetch extended profile info
$stmt2 = $conn->prepare("SELECT bio, location, facilities, rating, logo_path FROM company_profiles WHERE company_id = ?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$profile = $result2->fetch_assoc();
$stmt2->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Company Dashboard</title>
    <style>
        /* Styles as before, your existing CSS */
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo">FutureBot</div>
    <ul>
        <li><a href="company_profile_create.php">Edit Profile</a></li>
        <li><a href="job_post.php">Post Job</a></li>
    </ul>
</nav>

<?php if ($message === 'created') : ?>
    <div class="message" id="msg">🎉 Profile created successfully.</div>
<?php elseif ($message === 'updated') : ?>
    <div class="message" id="msg">✅ Profile updated successfully.</div>
<?php endif; ?>

<?php if ($company_basic): ?>
    <div class="profile-card" style="margin-top: 90px;">
        <div class="profile-img">
            <?php if (!empty($profile['logo_path']) && file_exists($profile['logo_path'])) : ?>
                <img src="<?= htmlspecialchars($profile['logo_path']) ?>" alt="Company Logo" style="width:150px;height:150px;border-radius:50%;">
            <?php else: ?>
                <img src="default-company.png" alt="Default Logo" style="width:150px;height:150px;border-radius:50%;">
            <?php endif; ?>
        </div>
        <div class="profile-details">
            <h2><?= htmlspecialchars($company_basic['name']) ?></h2>
            <p><strong>📆 Started:</strong> <?= htmlspecialchars($company_basic['start_year'] ?? 'N/A') ?></p>
            <p><strong>⭐ Rating:</strong> <?= htmlspecialchars($profile['rating'] ?? 'N/A') ?></p>
            <p><strong>📍 Location:</strong> <?= htmlspecialchars($profile['location'] ?? '') ?></p>
            <p><strong>🏢 Facilities:</strong><br><?= nl2br(htmlspecialchars($profile['facilities'] ?? '')) ?></p>
            <p><strong>📝 Bio:</strong><br><?= nl2br(htmlspecialchars($profile['bio'] ?? '')) ?></p>
        </div>
    </div>
<?php else: ?>
    <p>Company details not found.</p>
<?php endif; ?>

<script>
    setTimeout(function () {
        const msg = document.getElementById('msg');
        if (msg) {
            msg.style.opacity = '0';
            setTimeout(() => msg.style.display = 'none', 500);
        }
    }, 3000);
</script>

</body>
</html>
