<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$course_id = $_GET['course_id'] ?? null;

if (!$course_id || !is_numeric($course_id)) {
    die("Invalid course ID.");
}

// Check if user is enrolled in this course
$stmt = $conn->prepare("SELECT id FROM course_enrollments WHERE user_email = ? AND course_id = ?");
$stmt->bind_param("si", $user_email, $course_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    die("You are not enrolled in this course.");
}
$stmt->close();

// Get course details
$stmt = $conn->prepare("SELECT title, description FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->bind_result($course_title, $course_description);
if (!$stmt->fetch()) {
    die("Course not found.");
}
$stmt->close();

// Get lessons for this course ordered by lesson_order
$stmt = $conn->prepare("SELECT lesson_title, lesson_content, video_url FROM course_lessons WHERE course_id = ? ORDER BY lesson_order ASC");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$lessons = [];
while ($row = $result->fetch_assoc()) {
    $lessons[] = $row;
}
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= htmlspecialchars($course_title) ?> - Course Content</title>
<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 900px;
        margin: 30px auto;
        padding: 0 20px;
        background-color: #f9fafb;
        color: #222;
    }
    h1 {
        color: #0d47a1;
        margin-bottom: 5px;
    }
    p.description {
        font-style: italic;
        color: #555;
        margin-bottom: 25px;
    }
    .lesson {
        background: white;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .lesson-title {
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 10px;
        color: #0d47a1;
    }
    .lesson-content {
        margin-bottom: 10px;
        white-space: pre-wrap;
    }
    iframe, video {
        max-width: 100%;
        height: 360px;
        border-radius: 8px;
    }
    .no-lessons {
        font-size: 1.1rem;
        color: #666;
        text-align: center;
        margin-top: 50px;
    }
    a.back-link {
        display: inline-block;
        margin-bottom: 25px;
        color: #1976d2;
        font-weight: 600;
        text-decoration: none;
    }
    a.back-link:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<a href="my_courses.php" class="back-link">&larr; Back to My Courses</a>

<h1><?= htmlspecialchars($course_title) ?></h1>
<p class="description"><?= nl2br(htmlspecialchars($course_description)) ?></p>

<?php if (count($lessons) > 0): ?>
    <?php foreach ($lessons as $lesson): ?>
        <div class="lesson">
            <div class="lesson-title"><?= htmlspecialchars($lesson['lesson_title']) ?></div>
            <div class="lesson-content"><?= nl2br(htmlspecialchars($lesson['lesson_content'])) ?></div>
            
            <?php if (!empty($lesson['video_url'])): ?>
                <?php
                // If YouTube URL, embed iframe
                if (preg_match('/youtu\.be\/([^\?&]+)/', $lesson['video_url'], $matches) || preg_match('/youtube\.com\/watch\?v=([^\?&]+)/', $lesson['video_url'], $matches)) {
                    $video_id = $matches[1];
                    echo "<iframe src='https://www.youtube.com/embed/" . htmlspecialchars($video_id) . "' frameborder='0' allowfullscreen></iframe>";
                } else {
                    // Otherwise just show video tag
                    echo "<video controls src='" . htmlspecialchars($lesson['video_url']) . "'></video>";
                }
                ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="no-lessons">No lessons available for this course yet.</p>
<?php endif; ?>

</body>
</html>
