<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: register.php");
    exit;
}

$email = $_SESSION['user_email'];
$skill_building_plans = [
    "Python" => [
        "courses" => ["Intro to Python Programming", "Advanced Python Techniques"],
        "certifications" => ["Python Institute Certified Entry-Level Python Programmer"],
        "projects" => ["Build a web scraper", "Create a Flask web app"],
        "books" => [
            ["title" => "Python Crash Course – Eric Matthes", "link" => "https://ehmatthes.github.io/pcc/"],
            ["title" => "Automate the Boring Stuff with Python – Al Sweigart", "link" => "https://automatetheboringstuff.com/"],
            ["title" => "Fluent Python – Luciano Ramalho", "link" => "https://www.oreilly.com/library/view/fluent-python/9781491946237/"]
        ]
    ],
    "Java" => [
        "courses" => ["Java Programming for Beginners", "Object-Oriented Programming in Java"],
        "projects" => ["Build a banking system", "Create a Java chat app"],
        "books" => [
            ["title" => "Head First Java", "link" => "https://www.oreilly.com/library/view/head-first-java/0596009208/"],
            ["title" => "Effective Java – Joshua Bloch", "link" => "https://www.oreilly.com/library/view/effective-java-3rd/9780134686097/"]
        ]
    ],
    "Javascript" => [
        "courses" => ["JavaScript Basics", "JavaScript DOM Manipulation"],
        "projects" => ["Create a to-do list app", "Build a calculator"],
        "books" => [
            ["title" => "Eloquent JavaScript – Marijn Haverbeke", "link" => "https://eloquentjavascript.net/"],
            ["title" => "You Don't Know JS – Kyle Simpson", "link" => "https://github.com/getify/You-Dont-Know-JS"]
        ]
    ],
    "Php" => [
        "courses" => ["PHP for Beginners", "PHP with MySQL"],
        "projects" => ["Create a login/register system", "Build a blog CMS"],
        "books" => [
            ["title" => "PHP and MySQL Web Development – Welling & Thomson", "link" => "https://www.amazon.com/dp/0321833899"],
            ["title" => "Modern PHP – Josh Lockhart", "link" => "https://www.oreilly.com/library/view/modern-php/9781491905173/"]
        ]
    ],
    "Sql" => [
        "courses" => ["SQL Fundamentals", "Advanced SQL Queries"],
        "projects" => ["Design a student database", "Build an inventory system"],
        "books" => [
            ["title" => "SQL For Dummies", "link" => "https://www.wiley.com/en-us/SQL+For+Dummies-p-9781119527077"],
            ["title" => "Learning SQL – Alan Beaulieu", "link" => "https://www.oreilly.com/library/view/learning-sql-3rd/9781492057604/"]
        ]
    ],
    "Web Development" => [
        "courses" => ["Full Stack Web Development", "Responsive Web Design"],
        "projects" => ["Create a personal website", "Build a blog platform"],
        "books" => [
            ["title" => "HTML and CSS: Design and Build Websites – Jon Duckett", "link" => "https://www.htmlandcssbook.com/"],
            ["title" => "Learning Web Design – Jennifer Niederst Robbins", "link" => "https://learningwebdesign.com/"]
        ]
    ]
];

$earning_sources = [
    "Python" => [
        ["title" => "Freelance Python Developer on Upwork", "link" => "https://www.upwork.com/freelance-jobs/python/"],
        ["title" => "Python Bug Bounty Programs", "link" => "https://bugcrowd.com/"],
        ["title" => "Teach Python on Udemy", "link" => "https://www.udemy.com/instructor/"],
    ],
    "Data Science" => [
        ["title" => "Kaggle Competitions", "link" => "https://www.kaggle.com/competitions"],
        ["title" => "Freelance Data Scientist", "link" => "https://www.freelancer.com/hire/data-science/"],
    ],
    "Java" => [
        ["title" => "Android App Development Freelance", "link" => "https://www.fiverr.com/categories/programming-tech/mobile-app-services"],
        ["title" => "Java Developer Jobs", "link" => "https://www.indeed.com/q-Java-Developer-jobs.html"],
    ],
];

$recommended_degrees_map = [
    "Python" => [
        ["title" => "BSc in Computer Science", "link" => "https://www.harvard.edu/programs/computer-science/"],
        ["title" => "Software Engineering", "link" => "https://www.stanford.edu/academics/software-engineering/"]
    ],
];

$stmt = $conn->prepare("SELECT skills FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($skills_str);
$stmt->fetch();
$stmt->close();

if (empty($skills_str)) {
    die("No skills found for your profile. Please update your profile first.");
}

$user_skills = array_filter(array_map('trim', explode(',', $skills_str)));

$total_courses = 0;
$total_projects = 0;
$all_courses = [];
$all_projects = [];
$recommended_books = [];
$recommended_degrees = [];

foreach ($user_skills as $skill) {
    $skill_key = ucfirst(strtolower($skill));
    if (isset($skill_building_plans[$skill_key])) {
        $total_courses += count($skill_building_plans[$skill_key]['courses']);
        $total_projects += count($skill_building_plans[$skill_key]['projects']);
        $all_courses = array_merge($all_courses, $skill_building_plans[$skill_key]['courses']);
        $all_projects = array_merge($all_projects, $skill_building_plans[$skill_key]['projects']);
        $recommended_books = array_merge($recommended_books, $skill_building_plans[$skill_key]['books'] ?? []);
    }
    if (isset($recommended_degrees_map[$skill_key])) {
        $recommended_degrees = array_merge($recommended_degrees, $recommended_degrees_map[$skill_key]);
    }
}

$all_courses = array_unique($all_courses);
$all_projects = array_unique($all_projects);

$temp_degrees = [];
$unique_degrees = [];
foreach ($recommended_degrees as $degree) {
    if (!in_array($degree['title'], $temp_degrees)) {
        $temp_degrees[] = $degree['title'];
        $unique_degrees[] = $degree;
    }
}
$recommended_degrees = $unique_degrees;

$micro_internships = [];
if (!empty($user_skills)) {
    $likeClauses = [];
    $types = '';
    $params = [];
    foreach ($user_skills as $skill) {
        $likeClauses[] = "LOWER(skills_required) LIKE ?";
        $params[] = '%' . strtolower($skill) . '%';
        $types .= 's';
    }
    $sql = "SELECT id, title, description, duration_hours, company_name, application_link, location_type 
            FROM micro_internships 
            WHERE " . implode(" OR ", $likeClauses) . " 
            ORDER BY duration_hours ASC
            LIMIT 10";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $micro_internships[] = $row;
        }
        $stmt->close();
    }
}

$mentor_suggestions = [];
if (!empty($user_skills)) {
    $likeClauses = [];
    $types = '';
    $params = [];
    foreach ($user_skills as $skill) {
        $likeClauses[] = "LOWER(expertise_skills) LIKE ?";
        $params[] = '%' . strtolower($skill) . '%';
        $types .= 's';
    }
    $sql = "SELECT mentor_id, name, expertise_skills, bio, contact_info, profile_image_url 
            FROM mentors 
            WHERE " . implode(" OR ", $likeClauses) . " 
            LIMIT 10";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $mentor_suggestions[] = $row;
        }
        $stmt->close();
    }
}

// earning sources build
$recommended_earnings = [];
foreach ($user_skills as $skill) {
    $skill_key = ucfirst(strtolower($skill));
    if (isset($earning_sources[$skill_key])) {
        $recommended_earnings = array_merge($recommended_earnings, $earning_sources[$skill_key]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Career Suggestions - FutureBot</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
  * { 
    box-sizing: border-box; 
    margin:0; 
    padding:0; 
  }
  html, body {
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
    color: #2c3e50;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  /* Animated Background */
  .background-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    overflow: hidden;
  }

  .circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(67, 97, 238, 0.05);
    animation: float 15s infinite ease-in-out;
  }

  .circle:nth-child(1) {
    width: 80px;
    height: 80px;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
  }

  .circle:nth-child(2) {
    width: 120px;
    height: 120px;
    top: 70%;
    left: 80%;
    animation-delay: 2s;
  }

  .circle:nth-child(3) {
    width: 60px;
    height: 60px;
    top: 40%;
    left: 85%;
    animation-delay: 4s;
  }

  .circle:nth-child(4) {
    width: 100px;
    height: 100px;
    top: 80%;
    left: 15%;
    animation-delay: 6s;
  }

  .circle:nth-child(5) {
    width: 70px;
    height: 70px;
    top: 20%;
    left: 70%;
    animation-delay: 8s;
  }

  @keyframes float {
    0%, 100% {
      transform: translateY(0) translateX(0);
    }
    25% {
      transform: translateY(-20px) translateX(10px);
    }
    50% {
      transform: translateY(10px) translateX(-15px);
    }
    75% {
      transform: translateY(-15px) translateX(-10px);
    }
  }

  nav {
    width: 100%;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    position: fixed;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(67, 97, 238, 0.1);
  }
  nav .logo {
    font-size: 1.8rem;
    font-weight: bold;
    letter-spacing: 1px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  nav .logo i {
    font-size: 1.5rem;
  }
  nav .nav-buttons {
    display: flex;
    gap: 10px;
    align-items: center;
  }
  nav .nav-buttons button,
  nav .dropdown-btn {
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
  }
  nav .nav-buttons button:hover,
  nav .dropdown-btn:hover {
    background: linear-gradient(135deg, #3a0ca3, #4361ee);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
  }
  .dropdown { 
    position: relative; 
    display: inline-block; 
  }
  .dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background: #fff;
    min-width: 200px;
    box-shadow: 0px 8px 16px rgba(0,0,0,0.15);
    border-radius: 8px;
    z-index: 9999;
    overflow: hidden;
    margin-top: 5px;
    border: 1px solid rgba(67, 97, 238, 0.1);
  }
  
  .dropdown-content.show {
    display: block;
    animation: fadeIn 0.3s ease;
  }
  
  .dropdown-content a {
    color: #2c3e50;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 1px solid rgba(67, 97, 238, 0.05);
  }
  .dropdown-content a:hover { 
    background: rgba(67, 97, 238, 0.05);
    padding-left: 20px;
    color: #4361ee;
  }
  .dropdown-content a:last-child {
    border-bottom: none;
  }

  /* User Profile Header Styles - MODIFIED POSITION */
  .profile-header {
    background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
    color: white;
    padding: 15px 40px;
    position: fixed;
    top: 80px;
    right: 0;
    z-index: 999;
    border-radius: 0 0 0 16px;
    box-shadow: -4px 4px 20px rgba(0, 0, 0, 0.15);
    max-width: 500px;
    transition: all 0.3s ease;
  }

  .profile-header.collapsed {
    max-width: 300px;
    padding: 10px 25px;
  }

  .profile-container {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 20px;
  }

  .profile-header.collapsed .profile-container {
    gap: 15px;
  }

  .profile-info {
    display: flex;
    align-items: center;
    gap: 15px;
  }

  .profile-header.collapsed .profile-info {
    gap: 10px;
  }

  .profile-avatar {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
  }

  .profile-header.collapsed .profile-avatar {
    width: 40px;
    height: 40px;
    font-size: 1.1rem;
  }

  .profile-details h2 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 700;
    transition: all 0.3s ease;
  }

  .profile-header.collapsed .profile-details h2 {
    font-size: 1rem;
  }

  .profile-email {
    margin: 2px 0;
    opacity: 0.9;
    font-size: 0.8rem;
    transition: all 0.3s ease;
  }

  .profile-header.collapsed .profile-email {
    font-size: 0.7rem;
  }

  .profile-stats {
    display: flex;
    gap: 10px;
    margin-top: 5px;
    transition: all 0.3s ease;
  }

  .profile-header.collapsed .profile-stats {
    display: none;
  }

  .stat-badge {
    background: rgba(255, 255, 255, 0.2);
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    gap: 3px;
    backdrop-filter: blur(10px);
  }

  .profile-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
  }

  .profile-header.collapsed .profile-actions .btn-outline span {
    display: none;
  }

  .profile-actions .btn-outline {
    background: transparent;
    border: 1px solid white;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 600;
    font-size: 0.8rem;
  }

  .profile-header.collapsed .profile-actions .btn-outline {
    padding: 6px 8px;
  }

  .profile-actions .btn-outline:hover {
    background: white;
    color: #4361ee;
    transform: translateY(-1px);
  }

  .profile-toggle {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 0.8rem;
  }

  .profile-toggle:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
  }

  /* Main Content */
  .main-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 150%;
    max-width: 1500px;
    margin-top: 100px;
    padding: 0 20px;
  }

  .welcome-card {
    background: #fff;
    padding: 10px;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    width: 70%;
    max-width: 1100px;
    animation: slideUp 0.8s ease-out;
    border: 1px solid rgba(67, 97, 238, 0.1);
    position: relative;
    overflow: hidden;
    margin-bottom: 40px;
    text-align: center;
  }

  .welcome-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
  }

  .welcome-card h2 {
    color: #2c3e50;
    font-size: 1.8rem;
    margin-bottom: 15px;
    font-weight: 700;
  }

  .welcome-card p {
    color: #5a6c7d;
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 10px;
  }

  .stats-highlight {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
    flex-wrap: wrap;
  }

  .stat-item {
    text-align: center;
    padding: 10px 15px;
    background: #ebf2f9ff;
    border-radius: 12px;
    border: 1px solid rgba(67, 97, 238, 0.1);
  }

  .stat-number {
    display: block;
    font-size: 1rem;
    font-weight: 700;
    color: #4361ee;
    margin-bottom: 5px;
  }

  .stat-label {
    color: #5a6c7d;
    font-size: 0.9rem;
  }

  .section-card {
    background: #ffffffff;
    padding: 10px;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.36);
    width: 110%;
    max-width: 1300px;
    animation: slideUp 0.8s ease-out;
    border: 1px solid rgba(67, 97, 238, 0.1);
    position: relative;
    overflow: hidden;
    margin-bottom: 30px;
  }

  .section-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
  }

  .section-header {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    gap: 12px;
  }

  .section-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
  }

  .section-title {
    font-size: 1.2rem;
    color: #2c3e50;
    font-weight: 700;
    margin: 0;
  }

  .content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
  }

  .content-item {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid rgba(67, 97, 238, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .content-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(67, 97, 238, 0.15);
  }

  .content-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(to bottom, #4361ee, #3a0ca3);
  }

  .item-title {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 10px;
    line-height: 1.4;
  }

  .item-link {
    color: #4361ee;
    text-decoration: none;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.3s ease;
  }

  .item-link:hover {
    color: #3a0ca3;
    transform: translateX(5px);
  }

  .internship-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
  }

  .internship-card {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 12px;
    border: 1px solid rgba(67, 97, 238, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .internship-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(67, 97, 238, 0.15);
  }

  .internship-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(to bottom, #4361ee, #3a0ca3);
  }

  .internship-title {
    font-size: 1.2rem;
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 10px;
  }

  .internship-meta {
    color: #5a6c7d;
    font-size: 0.9rem;
    margin-bottom: 15px;
  }

  .internship-description {
    color: #5a6c7d;
    line-height: 1.5;
    margin-bottom: 20px;
  }

  .btn-primary {
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    color: #fff;
  }

  .empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #5a6c7d;
  }

  .empty-state i {
    font-size: 3rem;
    color: #bdc3c7;
    margin-bottom: 15px;
  }

  .empty-state p {
    font-size: 1.1rem;
  }

  /* Modal */
  .modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(3px);
  }
  .modal-content {
    background: #fff;
    color: #2c3e50;
    padding: 30px;
    border-radius: 16px;
    width: 90%;
    max-width: 500px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    overflow-wrap: break-word;
    animation: modalSlideIn 0.4s ease;
    border: 1px solid rgba(67, 97, 238, 0.1);
    position: relative;
  }

  .modal-content::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
  }

  .modal-content h3 { 
    margin-bottom: 15px; 
    font-size: 24px;
    color: #2c3e50;
  }
  .modal-content p { 
    margin: 10px 0; 
    font-size: 16px;
    line-height: 1.5;
    color: #5a6c7d;
  }
  .modal-content ul {
    text-align: left;
    margin: 15px 0;
    padding-left: 20px;
  }
  .modal-content li {
    margin: 8px 0;
    color: #5a6c7d;
  }
  .modal-content button {
    margin-top: 10px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    border: none;
    padding: 10px 25px;
    border-radius: 6px;
    cursor: pointer;
    color: #fff;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
  }
  .modal-content button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
  }

  /* Animations */
  @keyframes fadeIn { 
    from { opacity: 0; } 
    to { opacity: 1; } 
  }

  @keyframes slideUp { 
    from { 
      opacity: 0; 
      transform: translateY(30px); 
    } 
    to { 
      opacity: 1; 
      transform: translateY(0); 
    } 
  }

  @keyframes modalSlideIn {
    from {
      opacity: 0;
      transform: scale(0.9) translateY(-20px);
    }
    to {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
  }

 /* Footer Styles */
  footer {
    width: 100%;
    background: rgba(255, 255, 255, 0.95);
    padding: 30px 20px;
    margin-top: 50px;
    border-top: 1px solid rgba(67, 97, 238, 0.1);
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
  }

  .footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
  }

  .footer-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.5rem;
    font-weight: bold;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .footer-links {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .footer-links a {
    color: #5a6c7d;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
  }

  .footer-links a:hover {
    color: #4361ee;
    transform: translateY(-2px);
  }

  .footer-links a::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 0;
    height: 2px;
    background: #4361ee;
    transition: width 0.3s ease;
  }

  .footer-links a:hover::after {
    width: 100%;
  }

  .footer-social {
    display: flex;
    gap: 20px;
    margin: 10px 0;
  }

  .footer-social a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
  }

  .footer-social a:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
  }

  .footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid rgba(67, 97, 238, 0.1);
    width: 100%;
    color: #7f8c8d;
    font-size: 0.9rem;
  }

  /* Modal */
  .modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(3px);
  }
  /* Responsive Design */
  @media (max-width: 768px) {
    .content-grid {
      grid-template-columns: 1fr;
    }
    
    .internship-grid {
      grid-template-columns: 1fr;
    }
    
    .stats-highlight {
      flex-direction: column;
      gap: 15px;
    }
    
    .section-header {
      flex-direction: column;
      text-align: center;
      gap: 15px;
    }
    
    .profile-header {
      position: relative;
      top: 0;
      right: 0;
      border-radius: 0;
      max-width: 100%;
      margin-top: 80px;
    }
    
    .profile-container {
      flex-direction: column;
      text-align: center;
      gap: 15px;
    }
    
    .profile-info {
      flex-direction: column;
    }
    
    .profile-stats {
      justify-content: center;
    }
    
    .main-content {
      margin-top: 30px !important;
    }
    
    .profile-toggle {
      position: absolute;
      top: 10px;
      right: 10px;
    }
    
    /* Footer Styles */
footer {
  width: 100%;
  background: rgba(255, 255, 255, 0.95);
  padding: 30px 20px;
  margin-top: 50px;
  border-top: 1px solid rgba(67, 97, 238, 0.1);
  box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
}

.footer-content {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 20px;
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 1.5rem;
  font-weight: bold;
  background: linear-gradient(90deg, #4361ee, #3a0ca3);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.footer-links {
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
  justify-content: center;
}

.footer-links a {
  color: #5a6c7d;
  text-decoration: none;
  font-weight: 500;
  transition: all 0.3s ease;
  position: relative;
}

.footer-links a:hover {
  color: #4361ee;
  transform: translateY(-2px);
}

.footer-links a::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 0;
  height: 2px;
  background: #4361ee;
  transition: width 0.3s ease;
}

.footer-links a:hover::after {
  width: 100%;
}

.footer-social {
  display: flex;
  gap: 20px;
  margin: 10px 0;
}

.footer-social a {
  display: flex;
  align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
  }

  .footer-social a:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
  }

  .footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid rgba(67, 97, 238, 0.1);
    width: 100%;
    color: #7f8c8d;
    font-size: 0.9rem;
  }

  /* Responsive Footer */
  @media (max-width: 768px) {
    .footer-links {
      gap: 20px;
    }
    
    .footer-content {
      text-align: center;
    }
  }

  @media (max-width: 500px) {
    .footer-links {
      flex-direction: column;
      gap: 15px;
    }
    
    .footer-social {
      gap: 15px;
    }
  }
    
    .social-links {
      justify-content: center;
    }
  }

  @media (max-width: 500px) {
    nav { 
      flex-direction: column; 
      gap: 10px; 
      padding: 15px 20px;
    }
    
    .welcome-card, .section-card {
      padding: 20px;
      width: 95%;
    }
    
    .section-title {
      font-size: 1.3rem;
    }
    
    .profile-stats {
      flex-direction: column;
      gap: 8px;
    }
    
    .stat-badge {
      justify-content: center;
    }
  }
  </style>
</head>
<body>

  <!-- Animated Background -->
  <div class="background-animation">
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
  </div>

  <!-- Navbar -->
  <nav>
    <div class="logo">
      <i class="fas fa-robot"></i>FutureBot
    </div>
    <div class="nav-buttons">
      <button onclick="location.href='home.php'"><i class="fas fa-home"></i> Home</button>
      <div class="dropdown">
        <button class="dropdown-btn" id="menuButton"><i class="fas fa-bars"></i> Menu</button>
        <div class="dropdown-content" id="menuDropdown">
          <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlists</a>
          <a href="profile.php"><i class="fas fa-user-edit"></i> Profile</a>
          <a href="resgister_details.php"><i class="fas fa-user-edit"></i> RegisterForm</a>
          <a href="#" onclick="openModal('booksModal')"><i class="fas fa-book"></i> Explore Books</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- User Profile Header - MOVED TO TOP RIGHT -->
  <div class="profile-header" id="profileHeader">
    <div class="profile-container">
      <div class="profile-info">
        <div class="profile-avatar">
          <i class="fas fa-user"></i>
        </div>
        <div class="profile-details">
          <h2>Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Student') ?>!</h2>
          <p class="profile-email"><?= htmlspecialchars($email) ?></p>
          <div class="profile-stats">
            <span class="stat-badge">
              <i class="fas fa-star"></i>
              <?= count($user_skills) ?> Skills
            </span>
            <span class="stat-badge">
              <i class="fas fa-graduation-cap"></i>
              <?= $total_courses ?> Courses
            </span>
          </div>
        </div>
      </div>
      <div class="profile-actions">
        <button onclick="location.href='register_details.php'" class="btn-outline">
          <i class="fas fa-edit"></i> <span>Edit Profile</span>
        </button>
        <button class="profile-toggle" id="profileToggle" title="Toggle Profile Size">
          <i class="fas fa-compress" id="toggleIcon"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Welcome Card -->
    <div class="welcome-card">
      <h2>🎯 Your Personalized Career Roadmap</h2>
      <p>Based on your skills: <strong><?= implode(', ', $user_skills) ?></strong></p>
      <p>We've curated personalized recommendations to help you advance your career journey</p>
      
      <div class="stats-highlight">
        <div class="stat-item">
          <span class="stat-number"><?= count($user_skills) ?></span>
          <span class="stat-label">Skills Selected</span>
        </div>
        <div class="stat-item">
          <span class="stat-number"><?= $total_courses ?></span>
          <span class="stat-label">Courses Available</span>
        </div>
        <div class="stat-item">
          <span class="stat-number"><?= $total_projects ?></span>
          <span class="stat-label">Projects Suggested</span>
        </div>
        <div class="stat-item">
          <span class="stat-number"><?= count($micro_internships) ?></span>
          <span class="stat-label">Internships</span>
        </div>
      </div>
    </div>

    <!-- Rest of your content sections remain the same -->
      <!-- Course Suggestions -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon">
          <i class="fas fa-graduation-cap"></i>
        </div>
        <h3 class="section-title">📚 Course Suggestions</h3>
      </div>
      
      <?php if (count($all_courses) > 0): ?>
        <div class="content-grid">
          <?php foreach ($all_courses as $course): ?>
            <div class="content-item">
              <div class="item-title"><?= htmlspecialchars($course) ?></div>
              <a href="course_suggestions.php?career=<?= urlencode($course) ?>" class="item-link">
                Explore Course <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-book-open"></i>
          <p>No course suggestions available yet.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Project Suggestions -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon">
          <i class="fas fa-laptop-code"></i>
        </div>
        <h3 class="section-title">💻 Project Idea Suggestions</h3>
      </div>
      
      <?php if (count($all_projects) > 0): ?>
        <div class="content-grid">
          <?php foreach ($all_projects as $project): ?>
            <div class="content-item">
              <div class="item-title"><?= htmlspecialchars($project) ?></div>
              <a href="project_pathway.php?career=<?= urlencode($project) ?>" class="item-link">
                View Project <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-code"></i>
          <p>No project suggestions available yet.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- ... rest of your content sections ... -->

   <!-- Recommended Books -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon">
          <i class="fas fa-book"></i>
        </div>
        <h3 class="section-title">📒 Recommended Books</h3>
      </div>
      
      <?php if (count($recommended_books) > 0): ?>
        <div class="content-grid">
          <?php foreach ($recommended_books as $book): ?>
            <div class="content-item">
              <div class="item-title"><?= htmlspecialchars($book['title']) ?></div>
              <a href="<?= htmlspecialchars($book['link']) ?>" target="_blank" class="item-link">
                Read Book <i class="fas fa-external-link-alt"></i>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-book"></i>
          <p>No recommended books found for your skills.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Earning Opportunities -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon">
          <i class="fas fa-money-bill-wave"></i>
        </div>
        <h3 class="section-title">💰 Skill-Based Earning Opportunities</h3>
      </div>
      
      <?php if (count($recommended_earnings) > 0): ?>
        <div class="content-grid">
          <?php foreach ($recommended_earnings as $earning): ?>
            <div class="content-item">
              <div class="item-title"><?= htmlspecialchars($earning['title']) ?></div>
              <a href="<?= htmlspecialchars($earning['link']) ?>" target="_blank" class="item-link">
                Explore Opportunity <i class="fas fa-external-link-alt"></i>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-search-dollar"></i>
          <p>No earning opportunities found for your skills at the moment.</p>
        </div>
      <?php endif; ?>
    </div>
   <!-- Project Showcase Gallery -->
<div class="section-card">
  <div class="section-header">
    <div class="section-icon">
      <i class="fas fa-images"></i>
    </div>
    <h3 class="section-title">🖼️ Project Showcase Gallery</h3>
  </div>
  
  <div style="text-align: center; padding: 40px 20px;">
    <a href="submit_project.php" class="btn-primary" style="padding: 15px 40px; font-size: 1.1rem;">
      <i class="fas fa-plus"></i> Submit New Project
    </a>
    <p style="color: #5a6c7d; margin-top: 15px; font-size: 0.95rem;">
      Share your completed projects with the community
    </p>
  </div>
</div>


  

    <!-- Micro Internships -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon">
          <i class="fas fa-briefcase"></i>
        </div>
        <h3 class="section-title">💼 Micro-Internship Opportunities</h3>
      </div>
      
      <?php if (count($micro_internships) > 0): ?>
        <div class="internship-grid">
          <?php foreach ($micro_internships as $internship): ?>
            <div class="internship-card">
              <h4 class="internship-title"><?= htmlspecialchars($internship['title']) ?></h4>
              <div class="internship-meta">
                <?= htmlspecialchars($internship['company_name']) ?> • 
                <?= intval($internship['duration_hours']) ?> hours • 
                <?= htmlspecialchars($internship['location_type']) ?>
              </div>
              <p class="internship-description"><?= htmlspecialchars($internship['description']) ?></p>
              <a href="internship_details.php?id=<?= urlencode($internship['id']) ?>" class="btn-primary">
                <i class="fas fa-eye"></i> View Details
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-briefcase"></i>
          <p>No micro-internships matched your skills at the moment.</p>
        </div>
      <?php endif; ?>
    </div>

    

    <!-- Mentor Suggestions -->
    <div class="section-card">
      <div class="section-header">
        <div class="section-icon">
          <i class="fas fa-user-tie"></i>
        </div>
        <h3 class="section-title">🧑‍💼 Mentor Suggestions</h3>
      </div>
      
      <?php if (count($mentor_suggestions) > 0): ?>
        <div class="content-grid">
          <?php foreach ($mentor_suggestions as $mentor): ?>
            <div class="content-item">
              <div class="item-title"><?= htmlspecialchars($mentor['name']) ?></div>
              <div style="color: #5a6c7d; font-size: 0.9rem; margin-bottom: 10px;">
                Expertise: <?= htmlspecialchars($mentor['expertise_skills']) ?>
              </div>
              <a href="mentor_suggestions.php?mentor_id=<?= urlencode($mentor['mentor_id']) ?>" class="btn-primary">
                <i class="fas fa-user-plus"></i> Connect
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-user-tie"></i>
          <p>No mentors matched your skills right now.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Additional Skills -->
    <?php
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

      $matched_skills = [];
      foreach ($user_skills as $user_skill) {
        $key = ucfirst(strtolower($user_skill));
        if (isset($related_skills_map[$key])) {
          $matched_skills = array_merge($matched_skills, $related_skills_map[$key]);
        }
      }
      $matched_skills = array_unique(array_diff($matched_skills, $user_skills));
    ?>

    <div class="section-card">
      <div class="section-header">
        <div class="section-icon">
          <i class="fas fa-lightbulb"></i>
        </div>
        <h3 class="section-title">🧠 Matching Skill Suggestions</h3>
      </div>
      
      <?php if (count($matched_skills) > 0): ?>
        <div class="content-grid">
          <?php foreach ($matched_skills as $matched_skill): ?>
            <div class="content-item">
              <div class="item-title"><?= htmlspecialchars($matched_skill) ?></div>
              <a href="skill_develop.php?skill=<?= urlencode($matched_skill) ?>" class="item-link">
                Develop Skill <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-lightbulb"></i>
          <p>No additional matching skills found at this time.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
   

  <!-- Your existing modals and scripts -->
  <!-- Welcome Modal -->
  <div class="modal" id="welcomeModal">
    <div class="modal-content">
      <h3>🎉 Welcome to Career Suggestions!</h3>
      <p>Your personalized career roadmap is ready! 🚀</p>
      <ul>
        <li>🔹 <strong>Personalized Recommendations:</strong> Based on your skills and interests</li>
        <li>🔹 <strong>Comprehensive Resources:</strong> Courses, projects, books, and more</li>
        <li>🔹 <strong>Real Opportunities:</strong> Internships and earning possibilities</li>
        <li>🔹 <strong>Expert Guidance:</strong> Connect with experienced mentors</li>
        <li>🔹 <strong>Skill Development:</strong> Discover related skills to expand your expertise</li>
      </ul>
      <p>Ready to take your career to the next level? Let's get started!</p>
      <button onclick="closeModal('welcomeModal')"><i class="fas fa-play"></i> Start Exploring</button>
    </div>
  </div>

  <!-- Books Modal -->
  <div class="modal" id="booksModal">
    <div class="modal-content">
      <h3>📚 Explore Skill Books</h3>
      <p>Discover books to enhance your skills and knowledge.</p>
      <a href="all_books.php" class="btn-primary" style="display: inline-flex; margin: 10px;">
        <i class="fas fa-book-open"></i> Browse All Books
      </a>
      <button onclick="closeModal('booksModal')" style="background: #95a5a6; margin-left: 10px;">
        <i class="fas fa-times"></i> Close
      </button>
    </div>
  </div>

  <script>
  function openModal(id) { 
    document.getElementById(id).style.display = 'flex'; 
  }

  function closeModal(id) { 
    document.getElementById(id).style.display = 'none'; 
  }

  window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => { 
      if (event.target === modal) modal.style.display = 'none'; 
    });
    
    // Close dropdown when clicking outside
    if (!event.target.matches('.dropdown-btn') && !event.target.closest('.dropdown-content')) {
      const dropdowns = document.querySelectorAll('.dropdown-content');
      dropdowns.forEach(dropdown => {
        dropdown.classList.remove('show');
      });
    }
  };

  // Profile Toggle Functionality
  document.addEventListener('DOMContentLoaded', function() {
    const profileHeader = document.getElementById('profileHeader');
    const profileToggle = document.getElementById('profileToggle');
    const toggleIcon = document.getElementById('toggleIcon');
    const menuButton = document.getElementById('menuButton');
    const menuDropdown = document.getElementById('menuDropdown');
    
    // Check localStorage for saved state
    const isCollapsed = localStorage.getItem('profileCollapsed') === 'true';
    
    // Set initial state
    if (isCollapsed) {
      profileHeader.classList.add('collapsed');
      toggleIcon.className = 'fas fa-expand';
    } else {
      profileHeader.classList.remove('collapsed');
      toggleIcon.className = 'fas fa-compress';
    }
    
    // Toggle functionality for profile
    profileToggle.addEventListener('click', function() {
      profileHeader.classList.toggle('collapsed');
      
      if (profileHeader.classList.contains('collapsed')) {
        toggleIcon.className = 'fas fa-expand';
        localStorage.setItem('profileCollapsed', 'true');
      } else {
        toggleIcon.className = 'fas fa-compress';
        localStorage.setItem('profileCollapsed', 'false');
      }
    });
    
    // Toggle functionality for menu dropdown
    menuButton.addEventListener('click', function(event) {
      event.stopPropagation();
      menuDropdown.classList.toggle('show');
    });
    
    // Close dropdown when clicking on a menu item
    const dropdownItems = menuDropdown.querySelectorAll('a');
    dropdownItems.forEach(item => {
      item.addEventListener('click', function() {
        menuDropdown.classList.remove('show');
      });
    });
    
    // Show welcome modal on page load
    openModal('welcomeModal');
  });
  </script>

  <!-- Footer -->
<!-- Footer -->
  <footer>
    <div class="footer-content">
      <div class="footer-logo">
        <i class="fas fa-robot"></i>FutureBot
      </div>
      
      <div class="footer-links">
        <a href="index.php">Home</a>
        <a href="about.php">About Us</a>
        <a href="career_suggestions.php">Career Suggestions</a>
        <a href="privacy.php">Privacy Policy</a>
        <a href="terms.php">Terms of Service</a>
        <a href="contact.php">Contact Us</a>
      </div>
      
      <div class="footer-social">
        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
        <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" title="GitHub"><i class="fab fa-github"></i></a>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; 2024 FutureBot. All rights reserved. | Empowering students with AI-driven career guidance</p>
      </div>
    </div>
  </footer>

</body>
</html>