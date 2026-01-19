<?php
// project_pathway.php
session_start();

// Get the project name from the query
$project = isset($_GET['career']) ? urldecode($_GET['career']) : '';

if (empty($project)) {
    die("No project selected.");
}

// Define guidelines for each project
$project_guidelines = [
    "Build a web scraper" => [
        "steps" => [
            "Learn Python basics (requests, BeautifulSoup).",
            "Choose a website to scrape (e.g., news, e-commerce).",
            "Extract data such as titles, prices, or articles.",
            "Save results to CSV/Database.",
            "Add error handling and automation."
        ],
        "tools" => [
            "Python 3",
            "Requests library",
            "BeautifulSoup / lxml",
            "CSV/Excel or Database (SQLite/MySQL)"
        ],
        "resources" => [
            ["title" => "BeautifulSoup Documentation", "link" => "https://www.crummy.com/software/BeautifulSoup/"],
            ["title" => "Web Scraping with Python – Book", "link" => "https://www.oreilly.com/library/view/web-scraping-with/9781491985564/"]
        ]
    ],
    "Create a Flask web app" => [
        "steps" => [
            "Install Flask and set up a virtual environment.",
            "Build routes and templates.",
            "Connect with a database (SQLite/MySQL).",
            "Add user authentication.",
            "Deploy on Heroku or Render."
        ],
        "tools" => [
            "Python 3",
            "Flask framework",
            "Jinja2 templates",
            "SQLite/MySQL",
            "Heroku/Render for deployment"
        ],
        "resources" => [
            ["title" => "Flask Documentation", "link" => "https://flask.palletsprojects.com/"],
            ["title" => "Deploying Flask on Heroku", "link" => "https://devcenter.heroku.com/articles/getting-started-with-python"]
        ]
    ],
    "Create a to-do list app" => [
        "steps" => [
            "Design the UI with HTML/CSS.",
            "Add JavaScript for adding/removing tasks.",
            "Use LocalStorage or a backend (PHP/MySQL).",
            "Implement task categories and deadlines.",
            "Enhance with notifications or reminders."
        ],
        "tools" => [
            "HTML, CSS, JavaScript",
            "LocalStorage",
            "Optional: PHP/MySQL backend",
            "Bootstrap for UI"
        ],
        "resources" => [
            ["title" => "MDN JavaScript Guide", "link" => "https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide"],
            ["title" => "Building a To-Do App", "link" => "https://freshman.tech/todo-list/"]
        ]
    ],
    "Build a banking system" => [
        "steps" => [
            "Design the database schema (users, accounts, transactions).",
            "Implement user registration and login system with authentication.",
            "Allow deposits, withdrawals, and transfers between accounts.",
            "Add transaction history and account balance tracking.",
            "Secure the system with encryption and input validation."
        ],
        "tools" => [
            "Java / Python / PHP (depending on language choice)",
            "MySQL / PostgreSQL",
            "JDBC / SQLAlchemy / PDO",
            "Encryption libraries (e.g., bcrypt, hashlib)"
        ],
        "resources" => [
            ["title" => "Bank Management System Project in Java", "link" => "https://www.geeksforgeeks.org/banking-system-project-in-java/"],
            ["title" => "Database Design for Banking Systems", "link" => "https://www.researchgate.net/publication/324770777_Database_Design_for_Banking_Systems"]
        ]
    ],
    "Create a Java chat app" => [
        "steps" => [
            "Learn Java networking basics (Socket & ServerSocket).",
            "Create a server program to handle multiple clients.",
            "Build a client program with GUI (Swing/JavaFX).",
            "Implement real-time message sending and receiving.",
            "Enhance with features like chat rooms, emojis, and file sharing."
        ],
        "tools" => [
            "Java JDK",
            "Socket & ServerSocket APIs",
            "Swing or JavaFX for GUI",
            "Threads for multi-client handling",
            "IDE (Eclipse/IntelliJ/NetBeans)"
        ],
        "resources" => [
            ["title" => "Java Socket Programming Tutorial", "link" => "https://www.geeksforgeeks.org/socket-programming-in-java/"],
            ["title" => "Build a Chat App in Java", "link" => "https://www.javatpoint.com/example-of-java-chat-application"],
            ["title" => "JavaFX Documentation", "link" => "https://openjfx.io/"]
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($project) ?> - Project Guidelines</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #e4fcf8ff 0%, #d3dbffff 100%);
      color: #04395e;
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow-x: hidden;
    }
    .container {
      max-width: 800px; /* smaller container */
    }
    .popup-card {
      animation: popup 0.6s ease forwards;
      transform: scale(0.9);
      opacity: 0;
    }
    @keyframes popup {
      0% {
        transform: scale(0.9);
        opacity: 0;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }
    .card-body {
      max-height: 80vh; /* prevent card from overflowing viewport */
      overflow-y: auto;
    }
    .card-title {
      color: #001a66; /* dark blue */
      font-weight: bold;
    }
    .btn-gradient {
      background: linear-gradient(90deg, #24153e, #00c6ff);
      border: none;
      color: #fff !important;
      font-weight: 500;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .btn-gradient:hover {
      transform: translateY(-2px);
      box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.25);
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="card shadow popup-card">
    <div class="card-body">
      <h2 class="card-title">📌 <?= htmlspecialchars($project) ?> - Project Guidelines</h2>
      <hr>
      
      <?php if (isset($project_guidelines[$project])): ?>
        <h4>📝 Steps:</h4>
        <ul>
          <?php foreach ($project_guidelines[$project]['steps'] as $step): ?>
            <li><?= htmlspecialchars($step) ?></li>
          <?php endforeach; ?>
        </ul>

        <h4>🛠 Tools Required:</h4>
        <ul>
          <?php foreach ($project_guidelines[$project]['tools'] as $tool): ?>
            <li><?= htmlspecialchars($tool) ?></li>
          <?php endforeach; ?>
        </ul>

        <h4>📚 Resources:</h4>
        <ul>
          <?php foreach ($project_guidelines[$project]['resources'] as $res): ?>
            <li>
              <a href="<?= htmlspecialchars($res['link']) ?>" target="_blank">
                <?= htmlspecialchars($res['title']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">No guidelines available yet for this project.</p>
      <?php endif; ?>
      
      <a href="career_suggestions.php" class="btn btn-gradient mt-3">⬅ Back to Suggestions</a>
    </div>
  </div>
</div>

</body>
</html>
