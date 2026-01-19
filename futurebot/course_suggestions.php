<?php
session_start();
require_once 'db.php';

// Skill-building plans
$skill_building_plans = [
    "Python" => [
        "courses" => ["Intro to Python Programming", "Advanced Python Techniques"],
        "certifications" => ["Python Institute Certified Entry-Level Python Programmer"],
        "projects" => ["Build a web scraper", "Create a Flask web app"],
        "milestones" => ["Complete basic syntax", "Build first project", "Get certification"]
    ],
    "Data Science" => [
        "courses" => ["Data Science Fundamentals", "Machine Learning Basics"],
        "certifications" => ["IBM Data Science Professional Certificate"],
        "projects" => ["Analyze a dataset", "Build predictive model"],
        "milestones" => ["Learn pandas and numpy", "Complete ML project", "Earn certification"]
    ],
    "Java" => [
        "courses" => [
            "Java Programming for Beginners",
            "Object-Oriented Programming in Java",
            "Java Programming Basics",
            "Advanced Java Concepts"
        ],
        "certifications" => ["Oracle Certified Professional Java SE"],
        "projects" => ["Develop a Java desktop app", "Create a REST API with Spring Boot"],
        "milestones" => ["Understand OOP concepts", "Build Java projects", "Pass certification exam"]
    ],
    // other skills...
];

// Course prices (Taka)
$course_prices = [
    "Intro to Python Programming" => 5000,
    "Advanced Python Techniques" => 7000,
    "Data Science Fundamentals" => 6000,
    "Machine Learning Basics" => 8000,
    "Java Programming for Beginners" => 4500,
    "Object-Oriented Programming in Java" => 5500,
    "Java Programming Basics" => 4000,
    "Advanced Java Concepts" => 7500
];

// Related skills map
$related_skills_map = [
    "Python" => ["Data Science", "Machine Learning", "Flask", "Web Development"],
    "Data Science" => ["Python", "Machine Learning", "Deep Learning", "SQL"],
    "Java" => ["OOP", "Spring Framework", "Android"],
    "Javascript" => ["React", "Node.js", "Web Development"],
    "Php" => ["SQL", "Web Development", "Laravel"],
    "Sql" => ["Data Science", "Database Design", "ETL"],
    "React" => ["Javascript", "Next.js", "Frontend"],
    "Web Development" => ["HTML", "CSS", "Javascript", "PHP"]
];

// helper: find skill for a given course name
function find_skill_for_course($course, $skill_building_plans) {
    foreach ($skill_building_plans as $skill => $details) {
        if (!empty($details['courses'])) {
            foreach ($details['courses'] as $c) {
                if (strcasecmp($c, $course) === 0) {
                    return $skill;
                }
            }
        }
    }
    return null;
}

// Get requested career/skill
$career = isset($_GET['career']) ? trim($_GET['career']) : '';
$display_skill = '';
$all_courses = [];

if (!empty($career)) {
    $career_key = ucfirst(strtolower($career));

    if (isset($skill_building_plans[$career_key])) {
        $all_courses = $skill_building_plans[$career_key]['courses'];
        $display_skill = $career_key;

        if (isset($related_skills_map[$career_key])) {
            foreach ($related_skills_map[$career_key] as $related_skill) {
                if (isset($skill_building_plans[$related_skill])) {
                    $all_courses = array_merge($all_courses, $skill_building_plans[$related_skill]['courses']);
                }
            }
        }
    } else {
        foreach ($skill_building_plans as $skill => $details) {
            foreach ($details['courses'] as $course) {
                if (strcasecmp($course, $career) === 0) {
                    $all_courses = [$course];
                    $display_skill = $skill;

                    if (isset($related_skills_map[$skill])) {
                        foreach ($related_skills_map[$skill] as $related_skill) {
                            if (isset($skill_building_plans[$related_skill])) {
                                $all_courses = array_merge($all_courses, $skill_building_plans[$related_skill]['courses']);
                            }
                        }
                    }
                    break 2;
                }
            }
        }
    }
}

$all_courses = array_values(array_unique($all_courses));

// default fallback pathway steps
$default_pathway = [
    "Complete course lessons/modules",
    "Build a mini-project for practice",
    "Create a final project to showcase skills",
    "Document your work (GitHub/Portfolio)",
    "Pursue certification (optional)"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($career) ?> - Course Suggestions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(135deg, #e4fcf8ff 0%, #d3dbffff 100%);
            color: #04395e;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px;
            min-height: 100vh;
        }
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.61);
        }
        .navbar-brand {
            color: #292cd0ff !important;
            font-weight: bold;
            font-size: 1.6rem;
            margin-left: 0px;
        }
        .btn-back,
        .btn-enroll,
        .btn-secondary,
        .btn-success {
            background: linear-gradient(90deg, #24153e, #00c6ff);
            color: white !important;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            box-shadow: 0 6px 20px rgba(0, 150, 255, 0.5);
        }
        .btn-back:hover,
        .btn-enroll:hover,
        .btn-secondary:hover,
        .btn-success:hover {
            opacity: 0.95;
        }
        .card {
            border-radius: 14px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.38);
            transition: transform 0.22s ease, box-shadow 0.22s ease;
            opacity: 0;
            transform: scale(0.95);
            animation: popup 0.45s ease forwards;
            font-size: 0.92rem;
        }
        .card .card-body {
            padding: 0.65rem 0.9rem;
        }
        .card .card-text {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .card:hover {
            transform: translateY(-6px) scale(1.005);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.18);
        }
        @keyframes popup {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .card-header {
            background: linear-gradient(90deg, #24153e, #00c6ff);
            color: white;
            font-weight: 600;
            font-size: 1.02rem;
            padding: 0.5rem 0.9rem;
            border-radius: 12px 12px 0 0;
        }
        .btn-enroll {
            padding: .35rem .65rem;
            font-size: .88rem;
            align-self: flex-end;
            width: auto;
        }
        .pathway {
            margin: 0;
            padding-left: 1rem;
            list-style: none;
            font-size: 0.85rem;
        }
        .pathway li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 6px;
            color: #07324a;
        }
        .pathway li::before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 8px;
            margin-top: 6px;
            background: linear-gradient(90deg, #24153e, #00c6ff);
            border-radius: 50%;
            flex-shrink: 0;
        }
        .pathway-title {
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: #04395e;
        }
        .no-courses {
            margin-top: 40px;
            background-color: #fff3cd;
            padding: 20px;
            border-radius: 12px;
            font-size: 1.1rem;
        }
        .modal-header {
            background: linear-gradient(90deg, #24153e, #00c6ff);
            color: white;
        }
        @media (max-width: 767px) {
            .card-header { font-size: 1rem; padding: .45rem .75rem; }
            .btn-enroll { padding: .35rem .6rem; font-size: .85rem; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand" href="career_suggestions.php">FutureBot</a>
        <button class="btn btn-back" onclick="location.href='career_suggestions.php';">Back to Suggestions</button>
    </div>
</nav>

<div class="container">
    <h2 class="my-4 text-center" data-aos="fade-up" data-aos-delay="100">
        Courses for: <span class="text-primary"><?= htmlspecialchars($career) ?></span>
    </h2>

    <?php if (!empty($all_courses)): ?>
        <div class="row">
            <?php foreach ($all_courses as $index => $course): 
                $origin_skill = find_skill_for_course($course, $skill_building_plans);
                $pathway_steps = $default_pathway;
                if ($origin_skill && !empty($skill_building_plans[$origin_skill]['milestones'])) {
                    $pathway_steps = $skill_building_plans[$origin_skill]['milestones'];
                }
                $price = isset($course_prices[$course]) ? $course_prices[$course] . " Taka" : "Free";
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-12 mb-4" style="animation-delay: <?= ($index * 0.08) ?>s;">
                    <div class="card h-100">
                        <div class="card-header"><?= htmlspecialchars($course) ?></div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <p class="card-text">Build your <strong><?= htmlspecialchars($origin_skill ?: ($display_skill ?: 'skills')) ?></strong> with this course.</p>
                                <p class="text-success fw-bold">Price: <?= $price ?></p>

                                <div class="mt-2">
                                    <div class="pathway-title">Pathway / Step-by-step</div>
                                    <ul class="pathway">
                                        <?php foreach ($pathway_steps as $step_index => $step): ?>
                                            <li><strong><?= ($step_index + 1) ?>.</strong>&nbsp;<?= htmlspecialchars($step) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>

                            <div class="d-flex mt-3">
                                <button class="btn btn-enroll text-white" onclick="confirmEnroll('<?= urlencode($course) ?>')">Enroll</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-courses text-center" data-aos="fade-in">
            No courses found for "<strong><?= htmlspecialchars($career) ?></strong>".<br>
            Please select a different skill or course.
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="enrollModal" tabindex="-1" aria-labelledby="enrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollModalLabel">Confirm Enrollment</h5>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to enroll in this course?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <!-- confirm button links to enroll_course.php -->
                <a id="confirmEnrollBtn" class="btn btn-success rounded-pill text-white">OK</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });

    function confirmEnroll(course) {
        const enrollBtn = document.getElementById('confirmEnrollBtn');
        // link to enroll_course.php (this script should add course to my_courses.php)
        enrollBtn.href = 'enroll_course.php?course=' + course;
        new bootstrap.Modal(document.getElementById('enrollModal')).show();
    }
</script>

</body>
</html>
