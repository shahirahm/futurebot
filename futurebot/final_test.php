<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$course_id = $_GET['course_id'] ?? null;
if (!$course_id) {
    echo "Invalid course ID.";
    exit;
}

// Example quiz questions - you can replace or fetch from DB
$questions = [
    1 => [
        'question' => "What does HTML stand for?",
        'options' => ['HyperText Markup Language', 'HighText Markup Language', 'Hyperloop Machine Language', 'Hyperlink and Text Markup Language'],
        'answer' => 0
    ],
    2 => [
        'question' => "Which tag is used to create a link in HTML?",
        'options' => ['<link>', '<a>', '<href>', '<url>'],
        'answer' => 1
    ],
    3 => [
        'question' => "Which language is used to style web pages?",
        'options' => ['HTML', 'JQuery', 'CSS', 'XML'],
        'answer' => 2
    ],
];

$score = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    foreach ($questions as $qid => $q) {
        if (isset($_POST["q$qid"]) && intval($_POST["q$qid"]) === $q['answer']) {
            $score++;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chapter 1 Quiz</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .question { margin-bottom: 15px; }
        .submit-btn {
            padding: 10px 15px;
            background-color: #6a5ad2ff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #5848d4;
        }
        .result {
            margin-top: 20px;
            font-size: 18px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>Chapter 1 Quiz - Course ID: <?= htmlspecialchars($course_id) ?></h1>

<?php if ($score === null): ?>
<form method="post">
    <?php foreach ($questions as $qid => $q): ?>
        <div class="question">
            <p><strong>Q<?= $qid ?>. <?= htmlspecialchars($q['question']) ?></strong></p>
            <?php foreach ($q['options'] as $index => $option): ?>
                <label>
                    <input type="radio" name="q<?= $qid ?>" value="<?= $index ?>" required>
                    <?= htmlspecialchars($option) ?>
                </label><br>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <button type="submit" class="submit-btn">Submit Answers</button>
</form>

<?php else: ?>
    <div class="result">
        You scored <?= $score ?> out of <?= count($questions) ?>.
    </div>
    <p><button onclick="window.close()">Close this window</button></p>
<?php endif; ?>

</body>
</html>
