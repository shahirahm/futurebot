<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Get enrolled courses including React for Beginners and Web Development Bootcamp
$stmt = $conn->prepare("SELECT c.id, c.title FROM courses c 
    JOIN user_courses uc ON c.id = uc.course_id 
    WHERE uc.user_email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$courses_result = $stmt->get_result();
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Manually append the specified courses if not already in user's enrolled list
$manual_courses = [
    ['id' => 1001, 'title' => 'React for Beginners'],
    ['id' => 1002, 'title' => 'Web Development Bootcamp'],
    ['id' => 1003, 'title' => 'Advanced Python'],
    ['id' => 1004, 'title' => 'Data Science Essentials'],
    ['id' => 1005, 'title' => 'Introduction to Python'],
    ['id' => 1006, 'title' => 'Machine Learning Basics'],
    ['id' => 1008, 'title' => 'SQL for Beginners'],
];

foreach ($manual_courses as $manual_course) {
    $found = false;
    foreach ($courses as $c) {
        if ($c['title'] === $manual_course['title']) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $courses[] = $manual_course;
    }
}

// Define example steps per course (static)
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
    <title>My Courses & Milestones - FutureBot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0F62FE;
            --primary-dark: #0043CE;
            --secondary: #00C7FD;
            --accent: #8A3FFC;
            --success: #42BE65;
            --warning: #F1C21B;
            --dark: #161616;
            --light: #F4F4F4;
            --gray: #8D8D8D;
            --card-bg: rgba(255, 255, 255, 0.95);
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
             background: linear-gradient(135deg, #e6f8e8 0%, #e4f0e8 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            padding-top: 70px;
        }

        .navbar {
          background: rgba(243, 253, 246, 0.95);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.33);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .logo i {
            color: var(--accent);
        }
        
        .btn-back {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(15, 98, 254, 0.3);
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 98, 254, 0.4);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .page-header h1 {
            font-size: 2.2rem;
            color: var(--dark);
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }
        
        .page-header h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 2px;
        }
        
        .page-header p {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 20px auto 0;
        }
        
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .course-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 25px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }
        
        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        
        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .course-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
            line-height: 1.3;
            flex: 1;
        }
        
        .course-badge {
            background: rgba(15, 98, 254, 0.1);
            color: var(--primary);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 10px;
            white-space: nowrap;
        }
        
        .milestones {
            margin: 20px 0;
        }
        
        .milestone {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .milestone:last-child {
            border-bottom: none;
        }
        
        .milestone-checkbox {
            margin-right: 12px;
            position: relative;
        }
        
        .milestone-checkbox input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ddd;
            border-radius: 4px;
            outline: none;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .milestone-checkbox input[type="checkbox"]:checked {
            background-color: var(--success);
            border-color: var(--success);
        }
        
        .milestone-checkbox input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 14px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .milestone-label {
            flex: 1;
            display: flex;
            align-items: center;
        }
        
        .milestone-link {
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.2s ease;
        }
        
        .milestone-link:hover {
            color: var(--primary);
        }
        
        .milestone-link i {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .progress-section {
            margin: 25px 0 20px;
        }
        
        .progress-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .progress-percent {
            font-weight: 700;
            color: var(--dark);
        }
        
        .progress-text {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .progress-bar {
            height: 10px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        .certificate-section {
            text-align: center;
            margin-top: 20px;
        }
        
        .btn-certificate {
            background: linear-gradient(135deg, var(--accent), #6E32C9);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(138, 63, 252, 0.3);
        }
        
        .btn-certificate:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(138, 63, 252, 0.4);
        }
        
        .btn-certificate.disabled {
            background: #e9ecef;
            color: var(--gray);
            cursor: not-allowed;
            box-shadow: none;
        }
        
        .btn-certificate.disabled:hover {
            transform: none;
            box-shadow: none;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            max-width: 500px;
            margin: 0 auto;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: var(--gray);
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: var(--dark);
            margin-bottom: 15px;
        }
        
        .empty-state p {
            color: var(--gray);
            margin-bottom: 25px;
        }
        
        .btn-explore {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(15, 98, 254, 0.3);
        }
        
        .btn-explore:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 98, 254, 0.4);
        }
        
        @media (max-width: 768px) {
            .courses-grid {
                grid-template-columns: 1fr;
            }
            
            .navbar {
                padding: 15px 20px;
            }
            
            .logo {
                font-size: 1.3rem;
            }
            
            .page-header h1 {
                font-size: 1.8rem;
            }
        }
        
        @media (max-width: 480px) {
            .course-card {
                padding: 20px;
            }
            
            .course-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .course-badge {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
    <div class="logo">
        <i class="fas fa-robot"></i>
        <span>FutureBot</span>
    </div>
    <button class="btn-back" onclick="window.location.href='course_suggestions.php'">
        <i class="fas fa-arrow-left"></i> Back to Courses
    </button>
</nav>

<!-- Main Content -->
<div class="container">
    <div class="page-header">
        <h1>My Learning Journey</h1>
        <p>Track your progress and complete milestones to earn certificates</p>
    </div>

    <?php if (!empty($courses)): ?>
        <div class="courses-grid">
            <?php foreach ($courses as $course): 
                $course_id = $course['id'];
                $course_title = $course['title'];
                $step_key = "course_steps_" . $course_id;

                if (!isset($_SESSION[$step_key])) {
                    $_SESSION[$step_key] = array_fill(0, count($default_steps), false);
                }

                $completed = $_SESSION[$step_key];
                $completed_count = count(array_filter($completed));
                $total_steps = count($default_steps);
                $percentage = intval(($completed_count / $total_steps) * 100);
                
                // Determine course category for badge
                $category = "Development";
                if (stripos($course_title, 'python') !== false) $category = "Python";
                if (stripos($course_title, 'data') !== false) $category = "Data Science";
                if (stripos($course_title, 'machine') !== false) $category = "AI/ML";
                if (stripos($course_title, 'sql') !== false) $category = "Database";
            ?>
                <div class="course-card" data-course="<?= $course_id ?>">
                    <div class="course-header">
                        <div class="course-title"><?= htmlspecialchars($course_title) ?></div>
                        <div class="course-badge"><?= $category ?></div>
                    </div>

                    <div class="milestones">
                        <?php foreach ($default_steps as $index => $step): ?>
                            <div class="milestone">
                                <div class="milestone-checkbox">
                                    <input type="checkbox"
                                        class="step-checkbox"
                                        data-course="<?= $course_id ?>"
                                        data-step="<?= $index ?>"
                                        <?= $completed[$index] ? 'checked' : '' ?>>
                                </div>
                                <div class="milestone-label">
                                    <a href="milestone_task.php?course_id=<?= $course_id ?>&step=<?= $index ?>"
                                       target="_blank"
                                       class="milestone-link"
                                       title="Open task for '<?= htmlspecialchars($step) ?>'">
                                        <i class="fas fa-external-link-alt"></i>
                                        <?= htmlspecialchars($step) ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="progress-section">
                        <div class="progress-info">
                            <span class="progress-percent"><?= $percentage ?>%</span>
                            <span class="progress-text"><?= $completed_count ?> of <?= $total_steps ?> steps completed</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $percentage ?>%;"></div>
                        </div>
                    </div>

                    <div class="certificate-section">
                        <a <?= $percentage >= 100 ? 'href="certificate_download.php?course=' . urlencode($course_title) . '"' : '' ?>
                           class="btn-certificate <?= $percentage < 100 ? 'disabled' : '' ?>"
                           <?= $percentage < 100 ? 'aria-disabled="true"' : '' ?>>
                            <i class="fas fa-award"></i> Download Certificate
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-book-open"></i>
            <h3>No courses enrolled yet</h3>
            <p>You haven't enrolled in any courses. Explore our catalog to start your learning journey.</p>
            <a href="course_suggestions.php" class="btn-explore">
                <i class="fas fa-compass"></i> Explore Courses
            </a>
        </div>
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
                const fill = card.querySelector('.progress-fill');
                const percentText = card.querySelector('.progress-percent');
                const progressText = card.querySelector('.progress-text');
                
                fill.style.width = data.percentage + '%';
                percentText.textContent = data.percentage + '%';
                progressText.textContent = data.completed_count + ' of ' + data.total_steps + ' steps completed';

                const certBtn = card.querySelector('.btn-certificate');
                if (data.percentage < 100) {
                    certBtn.classList.add('disabled');
                    certBtn.removeAttribute('href');
                    certBtn.setAttribute('aria-disabled', 'true');
                } else {
                    certBtn.classList.remove('disabled');
                    certBtn.href = `certificate_download.php?course=${encodeURIComponent(card.querySelector('.course-title').textContent)}`;
                    certBtn.removeAttribute('aria-disabled');
                }
            }
        });
    });
});

// Alert if user tries clicking disabled certificate button (for accessibility)
document.querySelectorAll('.btn-certificate.disabled').forEach(btn => {
    btn.addEventListener('click', function(event) {
        event.preventDefault();
        alert('Please complete all steps to download the certificate.');
    });
});
</script>
</body>
</html>