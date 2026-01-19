<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$course_id = $_GET['course_id'] ?? 0;
$course_title = "Final Quiz - Web Development";

// Dummy questions for the quiz (you can later load from DB)
$quiz = [
    [
        'question' => "Which tag is used for creating links in HTML?",
        'options' => ["<div>", "<a>", "<span>", "<link>"],
        'answer' => 1
    ],
    [
        'question' => "Which language is used to style web pages?",
        'options' => ["HTML", "Python", "CSS", "PHP"],
        'answer' => 2
    ],
    [
        'question' => "What does PHP stand for?",
        'options' => [
            "Personal Hypertext Processor",
            "Preprocessor Home Page",
            "PHP: Hypertext Preprocessor",
            "Private Hypertext Processor"
        ],
        'answer' => 2
    ]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $course_title ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #dee9ff, #e2e0f5, #fff);
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #ffffff;
            padding: 12px 20px;
            text-align: left;
            box-shadow: 0 3px 8px rgba(53, 53, 53, 1);
        }

        .navbar button {
            background-color: #6a5ad2ff;
            border: none;
            color: white;
            padding: 8px 18px;
            font-size: 16px;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .navbar button:hover {
            background-color: #6847d4ff;
        }

        .quiz-container {
            max-width: 700px;
            background: white;
            padding: 30px;
            border-radius: 20px;
            margin: 30px auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .question {
            margin-bottom: 25px;
        }

        .question p {
            font-weight: bold;
        }

        label {
            display: block;
            margin: 5px 0;
        }

        button.submit-btn {
            background-color: #6a5ad2ff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: block;
            margin: 20px auto 0;
        }

        .result {
            margin-top: 25px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .correct {
            color: green;
        }

        .wrong {
            color: red;
        }

        .back-btn {
            background-color: #f3df47ff;
            color: black;
            border-radius: 12px;
            padding: 10px;
            margin-top: 20px;
            display: inline-block;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #e6cb31;
        }
    </style>
</head>
<body>

<!-- Navbar with Back Button -->
<div class="navbar">
    <button onclick="window.location.href='enroll_course.php'">&larr; Back</button>
</div>

<div class="quiz-container">
    <h2><?= htmlspecialchars($course_title) ?></h2>
    <form id="quizForm">
        <?php foreach ($quiz as $index => $q): ?>
            <div class="question">
                <p><?= ($index + 1) . ". " . htmlspecialchars($q['question']) ?></p>
                <?php foreach ($q['options'] as $optIndex => $option): ?>
                    <label>
                        <input type="radio" name="q<?= $index ?>" value="<?= $optIndex ?>">
                        <?= htmlspecialchars($option) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="button" class="submit-btn" onclick="submitQuiz()">Submit Quiz</button>
    </form>

    <div class="result" id="resultBox"></div>

    <a href="milestones.php" class="back-btn">&larr; Back to Milestones</a>
</div>

<script>
function submitQuiz() {
    const correctAnswers = <?= json_encode(array_column($quiz, 'answer')) ?>;
    let score = 0;
    const total = correctAnswers.length;

    for (let i = 0; i < total; i++) {
        const selected = document.querySelector(`input[name="q${i}"]:checked`);
        if (selected && parseInt(selected.value) === correctAnswers[i]) {
            score++;
        }
    }

    const resultBox = document.getElementById('resultBox');
    const passed = score === total;

    resultBox.innerHTML = `You scored <span class="${passed ? 'correct' : 'wrong'}">${score}/${total}</span><br>`;
    resultBox.innerHTML += passed
        ? "✅ Well done! You passed the quiz."
        : "❌ Please try again to pass.";

    if (passed) {
        // Optionally update session or DB
        // alert('You passed! You can now mark this milestone as completed.');
    }
}
</script>

</body>
</html>
