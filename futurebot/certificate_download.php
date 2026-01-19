<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];
$course = $_GET['course'] ?? '';

if (empty($course)) {
    echo "Invalid course name.";
    exit;
}

$stmt = $conn->prepare("SELECT full_name FROM users WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($full_name);
$stmt->fetch();
$stmt->close();

$completion_date = date("F j, Y");

$course_steps = [
    "Web Development Bootcamp" => [
        "Watch Introduction Video",
        "Complete Chapter 1 Quiz",
        "Submit Assignment",
        "Pass Final Test"
    ],
    "Machine Learning Basics" => [
        "Watch Introduction Video",
        "Understand Linear Regression",
        "Submit Mini Project",
        "Pass Final Assessment"
    ],
    "Advanced Python" => [
        "Watch Introduction Video",
        "Complete Chapter 1 Quiz",
        "Submit Assignment",
        "Pass Final Test"
    ],
    "React for Beginners" => [
        "Watch Introduction Video",
        "Complete React Basics Quiz",
        "Build a Simple React App",
        "Pass Final React Test"
    ],
];

if (!array_key_exists($course, $course_steps)) {
    echo "Invalid course name.";
    exit;
}

$steps = $course_steps[$course];
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Certificate of Completion</title>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@700&family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <style>
    /* Reset */
    *, *::before, *::after {
      box-sizing: border-box;
    }
    html, body {
      margin: 0; padding: 0;
      overflow-x: hidden;
    }
    body {
      background: #fefcf9 url('https://www.transparenttextures.com/patterns/gplay.png') repeat;
      font-family: 'Poppins', sans-serif;
      color: #1c1c1c;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 100px 20px 60px;
    }
    /* Navbar */
    .navbar {
      position: fixed;
      top: 20px; right: 20px;
      background: rgba(255 255 255 / 0.85);
      backdrop-filter: saturate(180%) blur(10px);
      box-shadow: 0 4px 20px rgb(0 0 0 / 0.1);
      border-radius: 40px;
      padding: 12px 30px;
      z-index: 1100;
      display: flex;
      justify-content: center;
      width: fit-content;
      animation: fadeSlideDown 0.7s ease forwards;
    }
    .btn-back {
      background: linear-gradient(90deg, #00c6ff, #24153e);
      border: none;
      color: #fff;
      font-weight: 700;
      font-size: 18px;
      padding: 0.6rem 2.5rem;
      border-radius: 50px;
      cursor: pointer;
      transition: all 0.3s ease;
      letter-spacing: 1px;
      user-select: none;
    }
    .btn-back:hover {
      background: linear-gradient(90deg, #24153e, #00c6ff);
      transform: scale(1.05);
    }

    /* Certificate Container */
    .certificate {
      max-width: 600px;
      background: #fff;
      border-radius: 35px;
      padding: 20px 30px 35px;
      box-shadow: 0 15px 30px rgb(0 0 0 / 0.1);
      border: 14px solid transparent;
      background-clip: padding-box;
      position: relative;
      animation: fadeIn 1s ease forwards;
      overflow: hidden;
    }
    /* Animated border */
    .certificate::before {
      content: "";
      pointer-events: none;
      position: absolute;
      inset: 0;
      border-radius: 35px;
      padding: 10px;
      background: linear-gradient(90deg, #00c6ff, #24153e);
      background-size: 300% 300%;
      animation: shine 5s linear infinite;
      -webkit-mask:
        linear-gradient(#fff 0 0) content-box, 
        linear-gradient(#fff 0 0);
      -webkit-mask-composite: destination-out;
      mask-composite: exclude;
      z-index: 0;
    }

    /* Title */
    h1 {
      font-family: 'Great Vibes', cursive;
      font-weight: 300;
      font-size: 2rem;
      background: linear-gradient(90deg, #00c6ff, #24153e);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0;
      letter-spacing: 0.06em;
    }
    .subtitle {
      font-size: 1rem;
      font-style: italic;
      color: #333;
      margin: 0 0 35px 0;
    }
    .name {
      font-size: 1rem;
      font-weight: 700;
      color: #111;
      margin-bottom: 0px;
      text-transform: capitalize;
    }
    .course-name {
      font-size: 1.5rem;
      font-weight: 600;
      font-style: italic;
      background: linear-gradient(90deg, #00c6ff, #24153e);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 6px;
    }
    /* Steps */
    .steps {
      text-align: left;
      margin-bottom: 5px;
      font-size: 1.1rem;
      color: #24153e;
      padding-left: 1rem;
    }
    .steps h5 {
      font-weight: 700;
      margin-bottom: 5px;
      background: linear-gradient(90deg, #00c6ff, #24153e);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .steps ul {
      list-style-type: disc;
      padding-left: 10px;
    }
    .steps li {
      margin-bottom: 5px;
      line-height: 1.5;
    }
    /* Date */
    .date {
      font-size: 1rem;
      font-weight: 700;
      color: #24153e;
      margin-bottom: 6px;
      text-align: center;
    }
    /* Signature Section */
    .signature-section {
      display: flex;
      justify-content: space-around;
      gap: 5px;
      flex-wrap: wrap;
    }
    .signature {
      width: 200px;
      text-align: center;
      color: #24153e;
      font-weight: 400;
      position: relative;
      padding-bottom: 15px;
    }
    .signature .line {
      height: 3px;
      width: 230px;
      margin: 0 auto 10px;
      border-radius: 2px;
      background: linear-gradient(90deg, #00c6ff, #24153e);
    }
    .signature .label {
      font-size: 1.1rem;
      color: #24153e;
    }
    /* Download Button */
    .download-btn {
      display: block;
      margin: 0 auto;
      background: linear-gradient(90deg, #00c6ff, #24153e);
      border: none;
      color: #fff;
      font-weight: 800;
      font-size: 1.35rem;
      padding: 0.8rem 4rem;
      border-radius: 50px;
      cursor: pointer;
      transition: all 0.35s ease;
    }
    .download-btn:hover {
      background: linear-gradient(90deg, #24153e, #00c6ff);
      transform: scale(1.1);
    }
    /* Animations */
    @keyframes shine {
      0% { background-position: 0% 50%; }
      100% { background-position: 300% 50%; }
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(40px);}
      to {opacity: 1; transform: translateY(0);}
    }
    @keyframes fadeSlideDown {
      from {opacity: 0; transform: translateY(-30px);}
      to {opacity: 1; transform: translateY(0);}
    }

    /* Print styling */
    @media print {
      body { background: #fff !important; padding: 0 !important; }
      .navbar, .download-btn { display: none !important; }
      .certificate {
        box-shadow: none !important;
        border-color: #24153e !important;
        margin: 0 !important;
        border-radius: 0 !important;
        padding: 30px !important;
      }
    }

    /* Responsive */
    @media (max-width: 720px) {
      .certificate {
        padding: 40px 30px 50px;
        max-width: 100%;
      }
      .signature-section {
        flex-direction: column;
        gap: 40px;
      }
      .signature { width: 100%; }
      .btn-back {
        padding: 0.5rem 1.5rem;
        font-size: 16px;
      }
      .navbar {
        top: 10px; right: 10px;
        padding: 8px 20px;
      }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
  <button class="btn-back" onclick="window.location.href='enroll_course.php'">&larr; Back</button>
</nav>

<!-- Certificate -->
<div class="certificate" role="main" aria-label="Certificate of Completion">
  <h1>Certificate of Completion</h1>
  <p class="subtitle">This is to proudly certify that</p>
  <div class="name"><?php echo htmlspecialchars($full_name); ?></div>
  <p>has successfully completed the course</p>
  <div class="course-name"><?php echo htmlspecialchars($course); ?></div>

  <div class="steps" aria-label="Completion Steps">
    <h5>Completion Steps:</h5>
    <ul>
      <?php foreach ($steps as $step): ?>
        <li><?php echo htmlspecialchars($step); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="date">Date of Completion: <?php echo $completion_date; ?></div>

  <section class="signature-section">
    <div class="signature">
      <div class="line"></div>
      <div class="label">Instructor Signature</div>
    </div>
    <div class="signature">
      <div class="line"></div>
      <div class="label">Authorized By</div>
    </div>
  </section>
</div>

<!-- Download Button -->
<button class="download-btn" onclick="window.print()">Download Certificate</button>

</body>
</html>
