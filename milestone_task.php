<?php
session_start();

$course_id = $_GET['course_id'] ?? null;
$step = $_GET['step'] ?? null;

$steps = [
    "Watch Introduction Video" => "Please watch the introductory video on React basics.",
    "Complete Chapter 1 Quiz" => "Test your understanding by completing the Chapter 1 Quiz. <br><br> <a href='quiz.php?course_id={$course_id}&chapter=1' target='_blank' class='link-primary text-decoration-underline'>Start Quiz</a>",
    "Submit Assignment" => "Submit your assignment for review. Upload your work below:",
    "Pass Final Test" => "Complete the final test to earn your certificate. <br><br> <a href='final_test.php?course_id={$course_id}' target='_blank' class='link-primary text-decoration-underline'>Take Final Test</a>"
];

// Validate inputs
if ($course_id === null || $step === null || !isset(array_keys($steps)[$step])) {
    echo "Invalid milestone step.";
    exit;
}

$step_names = array_keys($steps);
$step_name = $step_names[$step];
$content = $steps[$step_name];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($step_name) ?> - Milestone</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f4f4f9;
        }
        .container {
            max-width: 700px;
            margin-top: 40px;
            background: #fff;
            padding: 30px 40px;
            border-radius: 0.625rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.1);
        }
        .video-wrapper iframe {
            width: 100%;
            height: 360px;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border: none;
        }
        .note {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 1.5rem;
        }
    </style>
    <script>
        function redirectToEnroll() {
            const courseId = "<?= htmlspecialchars($course_id) ?>";
            window.location.href = "enroll_course.php?course_id=" + courseId;
        }
    </script>
</head>
<body>

<div class="container">

    <h1 class="text-primary mb-4"><?= htmlspecialchars($step_name) ?></h1>

    <div class="mb-4">
        <?= $content ?>

        <?php if ($step_name === "Watch Introduction Video"): ?>
            <div class="video-wrapper mt-4">
                <iframe src="https://www.youtube.com/embed/dGcsHMXbSOA" allowfullscreen></iframe>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($step_name === "Submit Assignment"): ?>
        <form action="submit_assignment.php" method="post" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="course_id" value="<?= htmlspecialchars($course_id) ?>">
            <div class="mb-3">
                <label for="assignmentFile" class="form-label">Upload your assignment file:</label>
                <input type="file" id="assignmentFile" name="assignment_file" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Assignment</button>
        </form>
    <?php endif; ?>

    <div class="note">
        <p>Once done, please return to the previous page and tick the checkbox to mark this step as complete.</p>
    </div>

    <button onclick="redirectToEnroll()" class="btn btn-primary mt-4">Close this window</button>
</div>

<!-- Bootstrap JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
