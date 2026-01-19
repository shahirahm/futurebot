<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Get enrolled courses
$stmt = $conn->prepare("SELECT c.id, c.title FROM courses c 
    JOIN user_courses uc ON c.id = uc.course_id 
    WHERE uc.user_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$courses_result = $stmt->get_result();
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Define example steps per course (static, or you can store them in DB)
$default_steps = [
    "Watch Introduction Video",
    "Complete Chapter 1 Quiz",
    "Submit Assignment",
    "Pass Final Test"
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Courses & Milestones</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
             background: linear-gradient(to right, #dee9ffff, #e2e0f5ff, #ffffffff);
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(54, 52, 52, 1);
            max-width: 500px;
            margin: auto;
            margin-left: 30px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .course-card {
            background: #f0f1ffff;
            border: 1px solid #ddd;
            border-radius: 19px;
            padding: 15px 20px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(54, 52, 52, 1);
        }
        .course-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .milestone {
            margin-bottom: 1px;
        }
        .progress-bar {
            height: 10px;
            border-radius: 10px;
            background-color: #e0e0e0;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-bar-fill {
            height: 100%;
            background-color: #f3df47ff;
            width: 0;
            transition: width 0.3s ease;
        }
        .certificate-btn {
            background-color: #e7c622ff;
            color: white;
            padding: 10px 10px;
            border: none;
            border-radius: 19px;
            cursor: pointer;
            margin-top: 1px;
            text-decoration: none;
            display: inline-block;
        }
        .certificate-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Your Course Progress</h2>

    <?php foreach ($courses as $course): 
        $course_id = $course['id'];
        $course_title = $course['title'];
        $step_key = "course_steps_" . $course_id;

        // Load progress from session (or database in real case)
        if (!isset($_SESSION[$step_key])) {
            $_SESSION[$step_key] = array_fill(0, count($default_steps), false);
        }

        $completed = $_SESSION[$step_key];
        $completed_count = count(array_filter($completed));
        $total_steps = count($default_steps);
        $percentage = intval(($completed_count / $total_steps) * 100);
    ?>
        <div class="course-card" data-course="<?= $course_id ?>">
            <div class="course-title"><?= htmlspecialchars($course_title) ?></div>

            <?php foreach ($default_steps as $index => $step): ?>
                <div class="milestone">
                    <input type="checkbox"
                        class="step-checkbox"
                        data-course="<?= $course_id ?>"
                        data-step="<?= $index ?>"
                        <?= $completed[$index] ? 'checked' : '' ?>>
                    <?= htmlspecialchars($step) ?>
                </div>
            <?php endforeach; ?>

            <div class="progress-bar">
                <div class="progress-bar-fill" style="width: <?= $percentage ?>%;"></div>
            </div>
            <div><?= $percentage ?>% completed</div>

            <a href="certificate_download.php?course=<?= urlencode($course_title) ?>"
               class="certificate-btn"
               <?= $percentage < 100 ? 'disabled' : '' ?>>
               🎓 Download Certificate
            </a>
        </div>
    <?php endforeach; ?>

    <?php if (empty($courses)): ?>
        <p>You are not enrolled in any courses yet.</p>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.step-checkbox').forEach(box => {
    box.addEventListener('change', function() {
        const courseId = this.dataset.course;
        const stepIndex = this.dataset.step;
        const checked = this.checked;

        fetch('update_step_progress.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `course_id=${courseId}&step_index=${stepIndex}&checked=${checked ? 1 : 0}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const card = this.closest('.course-card');
                const fill = card.querySelector('.progress-bar-fill');
                const percentText = card.querySelector('div:nth-of-type(3)');
                fill.style.width = data.percentage + '%';
                percentText.innerText = data.percentage + '% completed';

                const certBtn = card.querySelector('.certificate-btn');
                certBtn.disabled = data.percentage < 100;
            }
        });
    });
});
</script>
</body>
</html>
