<?php
// start_mock_interview.php
session_start();
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'General';

// Sample score logic for simplicity
function evaluateAnswers($answers) {
    $score = 0;
    foreach ($answers as $ans) {
        if (strlen(trim($ans)) > 50) {
            $score += 1;
        }
    }
    return $score;
}

$submitted = false;
$score = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer'])) {
    $submitted = true;
    $score = evaluateAnswers($_POST['answer']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mock Interview - <?= $category ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f8fb;
            margin: 0;
            padding: 20px;
        }
        .navbar {
            background: #333;
            padding: 15px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-right: 15px;
            font-weight: bold;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #444;
        }
        .question {
            background: #e8eaf6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .answer {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }
        .submit-btn {
            padding: 10px 20px;
            background: #7c4dff;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }
        .submit-btn:hover {
            background: #673ab7;
        }
        .score-box {
            background: #d1e7dd;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #0f5132;
            font-size: 18px;
        }
        select {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="skill_develop.php">← Back to Skill Development</a>
</div>

<div class="container">
    <h2>Mock Interview - <?= $category ?> Domain</h2>

    <?php if ($submitted): ?>
        <div class="score-box">
            <strong>Your Interview Score:</strong> <?= $score ?>/10<br>
            <?= ($score >= 7) ? "Excellent preparation!" : (($score >= 4) ? "Good attempt, keep practicing!" : "Needs improvement. Keep learning!") ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST">
        <?php
        $questions = [
            'General' => [
                'Tell me about yourself.',
                'Why should we hire you?',
                'What are your strengths and weaknesses?'
            ],
            'AI & Machine Learning' => [
                'Explain overfitting and how to avoid it.',
                'What is the difference between supervised and unsupervised learning?',
                'How would you deploy a machine learning model to production?'
            ],
            'Cybersecurity' => [
                'What is the difference between symmetric and asymmetric encryption?',
                'How do you protect against SQL injection?',
                'What are common vulnerabilities in web applications?'
            ],
            'Web Development' => [
                'What is the difference between HTML, CSS, and JavaScript?',
                'How do you ensure your websites are responsive?',
                'What is REST API and how does it work?'
            ]
            // You can add more domains and questions here.
        ];

        $asked = $questions[$category] ?? $questions['General'];

        foreach ($asked as $index => $q) {
            echo "<div class='question'><strong>Q" . ($index+1) . ":</strong> $q</div>";
            echo "<textarea class='answer' name='answer[]' rows='4' placeholder='Type your answer...'></textarea><br><br>";
        }
        ?>
        <button type="submit" class="submit-btn">Submit Answers</button>
    </form>

    <br><hr>
    <form method="GET" action="">
        <label for="category"><strong>Select Topic:</strong></label>
        <select name="category" onchange="this.form.submit()">
            <option value="General" <?= $category === 'General' ? 'selected' : '' ?>>General</option>
            <option value="AI & Machine Learning" <?= $category === 'AI & Machine Learning' ? 'selected' : '' ?>>AI & Machine Learning</option>
            <option value="Cybersecurity" <?= $category === 'Cybersecurity' ? 'selected' : '' ?>>Cybersecurity</option>
            <option value="Web Development" <?= $category === 'Web Development' ? 'selected' : '' ?>>Web Development</option>
            <!-- Add more topics -->
        </select>
    </form>
</div>
</body>
</html>
