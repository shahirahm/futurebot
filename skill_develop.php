<?php
session_start();
require_once 'db.php';

// Sample skill-building plans array
$skill_building_plans = [
    "Python" => [
        "courses" => ["Intro to Python Programming", "Advanced Python Techniques"],
        "projects" => ["Build a web scraper", "Create a Flask web app"],
        "milestones" => ["Complete basic syntax", "Build first project", "Get certification"]
    ],
    "Data Science" => [
        "courses" => ["Data Science Fundamentals", "Machine Learning Basics"],
        "projects" => ["Analyze a dataset", "Build predictive model"],
        "milestones" => ["Learn pandas and numpy", "Complete ML project", "Earn certification"]
    ],
    "Web Development" => [
        "courses" => ["HTML & CSS Basics", "JavaScript Essentials", "PHP & MySQL"],
        "projects" => ["Portfolio Website", "E-commerce Site"],
        "milestones" => ["Build static pages", "Create dynamic pages", "Deploy site"]
    ]
];

// Skill-based challenges
$skill_challenges = [
    "Python" => [
        [
            "title" => "Build a Calculator CLI App",
            "difficulty" => "Beginner",
            "description" => "Create a command-line calculator that performs basic arithmetic.",
            "link" => "https://github.com/topics/python-projects"
        ],
        [
            "title" => "Python Web Scraper",
            "difficulty" => "Intermediate",
            "description" => "Scrape product data from an eCommerce site using BeautifulSoup.",
            "link" => "https://realpython.com/beautiful-soup-web-scraper-python/"
        ]
    ],
    "Data Science" => [
        [
            "title" => "Titanic Survival Prediction",
            "difficulty" => "Beginner",
            "description" => "Analyze passenger data and predict survival outcomes.",
            "link" => "https://www.kaggle.com/competitions/titanic"
        ],
        [
            "title" => "Customer Churn Prediction",
            "difficulty" => "Advanced",
            "description" => "Use machine learning to predict customer churn from telco data.",
            "link" => "https://www.kaggle.com/code/blastchar/telco-customer-churn"
        ]
    ],
    "Web Development" => [
        [
            "title" => "Build a Portfolio Website",
            "difficulty" => "Beginner",
            "description" => "Create a personal portfolio using HTML, CSS, and JavaScript.",
            "link" => "https://www.freecodecamp.org/news/building-a-personal-portfolio-website/"
        ],
        [
            "title" => "Create a Blog with PHP & MySQL",
            "difficulty" => "Intermediate",
            "description" => "Build a full-stack blog system with login, posting, and comments.",
            "link" => "https://www.youtube.com/watch?v=4RzHd6Q7y8w"
        ]
    ]
];

// Get skill from URL query param
$selectedSkill = $_GET['skill'] ?? '';
$selectedSkill = trim($selectedSkill);

$skillInfo = $skill_building_plans[$selectedSkill] ?? null;
$challenges = $skill_challenges[$selectedSkill] ?? [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Skill Development & Challenges for <?= htmlspecialchars($selectedSkill) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding-top: 80px;
    color: #2c3e50;
  }
  .navbar-brand {
    font-weight: 700;
    font-size: 1.8rem;
    color: #0d6efd !important;
  }
  .container {
    max-width: 960px;
  }
  h2.section-title {
    border-bottom: 3px solid #0d6efd;
    padding-bottom: 6px;
    margin-bottom: 25px;
    font-weight: 700;
    color: #0d6efd;
  }
  .card {
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(13, 110, 253, 0.15);
    transition: transform 0.3s ease;
  }
  .card:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 30px rgba(13, 110, 253, 0.25);
  }
  .card-header {
    background: #0d6efd;
    color: #fff;
    font-weight: 700;
    font-size: 1.25rem;
    border-radius: 15px 15px 0 0;
  }
  .btn-explore {
    background: #198754;
    color: white;
    font-weight: 600;
    transition: background-color 0.3s ease;
    border-radius: 30px;
    padding: 6px 20px;
    font-size: 0.9rem;
  }
  .btn-explore:hover {
    background-color: #14532d;
    color: white;
  }
  .badge-difficulty {
    font-size: 0.85rem;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
  }
  .badge-beginner {
    background-color: #198754;
    color: white;
  }
  .badge-intermediate {
    background-color: #0dcaf0;
    color: #fff;
  }
  .badge-advanced {
    background-color: #dc3545;
    color: white;
  }
  .challenge-description {
    font-size: 0.95rem;
    margin-bottom: 10px;
    color: #34495e;
  }
  .list-group-item {
    border: none;
    padding-left: 0;
  }
  .back-btn {
    margin-bottom: 30px;
  }
  @media (max-width: 576px) {
    h2.section-title {
      font-size: 1.5rem;
    }
  }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="career_suggestions.php">FutureBot</a>
    <button class="btn btn-outline-primary" onclick="location.href='career_suggestions.php'">Back to Career Suggestions</button>
  </div>
</nav>

<div class="container">
    <?php if (!$selectedSkill): ?>
      <div class="alert alert-danger text-center">
        Invalid skill selection. Please go back and select a skill.
      </div>
    <?php elseif (!$skillInfo): ?>
      <div class="alert alert-warning text-center">
        No skill development information found for "<strong><?= htmlspecialchars($selectedSkill) ?></strong>".
        Please go back and select another skill.
      </div>
    <?php else: ?>
      
      <h2 class="section-title">Skill Development Plan for <span><?= htmlspecialchars($selectedSkill) ?></span></h2>

      <div class="row mb-5">
        <div class="col-md-4">
          <div class="card mb-4">
            <div class="card-header">📚 Courses</div>
            <ul class="list-group list-group-flush p-3">
              <?php foreach ($skillInfo['courses'] as $course): ?>
                <li class="list-group-item">
                  <?= htmlspecialchars($course) ?>
                  <!-- Optional explore link -->
                  <a href="explore_course.php?course=<?= urlencode($course) ?>" class="btn btn-sm btn-explore float-end">Explore</a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card mb-4">
            <div class="card-header">📂 Projects</div>
            <ul class="list-group list-group-flush p-3">
              <?php foreach ($skillInfo['projects'] as $project): ?>
                <li class="list-group-item"><?= htmlspecialchars($project) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card mb-4">
            <div class="card-header">🎯 Milestones</div>
            <ul class="list-group list-group-flush p-3">
              <?php foreach ($skillInfo['milestones'] as $milestone): ?>
                <li class="list-group-item"><?= htmlspecialchars($milestone) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>

      <h2 class="section-title">💡 Challenges</h2>
      <?php if ($challenges): ?>
        <div class="row g-4">
          <?php foreach ($challenges as $challenge): 
            $difficultyClass = '';
            switch(strtolower($challenge['difficulty'])) {
              case 'beginner': $difficultyClass = 'badge-beginner'; break;
              case 'intermediate': $difficultyClass = 'badge-intermediate'; break;
              case 'advanced': $difficultyClass = 'badge-advanced'; break;
              default: $difficultyClass = 'badge-secondary';
            }
          ?>
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-body d-flex flex-column">
                  <h5 class="card-title"><?= htmlspecialchars($challenge['title']) ?></h5>
                  <p class="challenge-description"><?= htmlspecialchars($challenge['description']) ?></p>
                  <span class="badge badge-difficulty <?= $difficultyClass ?>">
                    <?= htmlspecialchars($challenge['difficulty']) ?>
                  </span>
                  <a href="<?= htmlspecialchars($challenge['link']) ?>" target="_blank" class="btn btn-explore mt-auto align-self-start">
                    Try Challenge 🚀
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-muted">No challenges available for this skill yet.</p>
      <?php endif; ?>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
