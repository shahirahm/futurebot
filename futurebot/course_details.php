<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid course ID.");
}

$course_id = (int)$_GET['id'];

// Verify user is enrolled in this course
$stmt = $conn->prepare("SELECT course_name FROM course_payments WHERE id = ? AND user_email = ?");
$stmt->bind_param("is", $course_id, $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("You are not enrolled in this course or course not found.");
}

$course = $result->fetch_assoc();

// Sample static data for course details — replace with DB queries as needed
$course_outline = [
    "Introduction to the course",
    "Module 1: Basics",
    "Module 2: Intermediate concepts",
    "Module 3: Advanced techniques",
    "Final project and assessment"
];

$course_videos = [
    ["title" => "Welcome Video", "url" => "https://www.youtube.com/embed/dQw4w9WgXcQ"],
    ["title" => "Module 1 Overview", "url" => "https://www.youtube.com/embed/VIDEO_ID_1"],
    ["title" => "Module 2 Deep Dive", "url" => "https://www.youtube.com/embed/VIDEO_ID_2"],
];

$course_tests = [
    "Test 1: Basics Quiz",
    "Test 2: Intermediate Quiz",
    "Test 3: Final Exam"
];

$course_certifications = [
    "Certificate of Completion",
    "Certificate of Excellence (for top scorers)"
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Course Details - <?= htmlspecialchars($course['course_name']) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            background: linear-gradient(135deg, #c8f4edff 0%, #ACB6E5 100%);
            color: #04395e;
            min-height: 100vh;
            padding-top: 80px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
        }
        .section {
            background: white;
            padding: 25px 30px;
            margin-bottom: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        iframe {
            width: 100%;
            height: 315px;
            border-radius: 8px;
            border: none;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="my_courses.php">FutureBot</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a href="my_courses.php" class="nav-link">My Courses</a></li>
            <li class="nav-item"><a href="enroll_course.php" class="nav-link">Enroll More</a></li>
            <li class="nav-item"><a href="logout.php" class="nav-link">Logout</a></li>
        </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h1 class="mb-4"><?= htmlspecialchars($course['course_name']) ?></h1>

    <div class="section">
        <h3>Course Outline</h3>
        <ul>
            <?php foreach ($course_outline as $item): ?>
                <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="section">
        <h3>Course Videos</h3>
        <?php foreach ($course_videos as $video): ?>
            <h5><?= htmlspecialchars($video['title']) ?></h5>
            <iframe src="<?= htmlspecialchars($video['url']) ?>" allowfullscreen></iframe>
            <hr>
        <?php endforeach; ?>
    </div>

    <div class="section">
        <h3>Tests & Exams</h3>
        <ul>
            <?php foreach ($course_tests as $test): ?>
                <li><?= htmlspecialchars($test) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="section">
        <h3>Certifications</h3>
        <ul>
            <?php foreach ($course_certifications as $cert): ?>
                <li><?= htmlspecialchars($cert) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
