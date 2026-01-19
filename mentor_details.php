<?php
session_start();

// Mentor data
$mentorData = [
    ['id' => 1, 'mentor' => 'Dr. Jane Smith', 'name' => 'AI Research Institute', 'email' => 'jane.smith@aiinstitute.com', 'phone' => '+8801234567890', 'details' => 'Leading AI researcher with 10+ years of experience.'],
    ['id' => 2, 'mentor' => 'Prof. John Doe', 'name' => 'Tech AI Academy', 'email' => 'john.doe@techaiacademy.com', 'phone' => '+8801987654321', 'details' => 'Professor of Machine Learning and AI enthusiast.'],
    ['id' => 3, 'mentor' => 'Alice Johnson', 'name' => 'CyberSecure Labs', 'email' => 'alice.j@cybersecurelabs.com', 'phone' => '+8801122334455', 'details' => 'Cybersecurity expert specializing in network security.'],
    ['id' => 4, 'mentor' => 'Bob Brown', 'name' => 'SecureTech Training Center', 'email' => 'bob.brown@securetech.com', 'phone' => '+8805566778899', 'details' => 'Trainer and consultant for cybersecurity best practices.'],
    ['id' => 5, 'mentor' => 'Emily White', 'name' => 'Marketing Pro Institute', 'email' => 'emily.white@marketingpro.com', 'phone' => '+8809988776655', 'details' => 'Digital marketing strategist with a passion for data-driven campaigns.'],
    ['id' => 6, 'mentor' => 'Michael Green', 'name' => 'Digital Growth Academy', 'email' => 'michael.green@digitalgrowth.com', 'phone' => '+8802233445566', 'details' => 'Expert in SEO and online brand building.'],
    ['id' => 7, 'mentor' => 'Chris Black', 'name' => 'CodeMaster School', 'email' => 'chris.black@codemaster.com', 'phone' => '+8806677889900', 'details' => 'Full-stack web developer and coding instructor.'],
    ['id' => 8, 'mentor' => 'Sandra Blue', 'name' => 'Web Dev Hub', 'email' => 'sandra.blue@webdevhub.com', 'phone' => '+8803344556677', 'details' => 'Front-end specialist focused on UX/UI design.'],
    ['id' => 9, 'mentor' => 'David King', 'name' => 'Data Wizards Institute', 'email' => 'david.king@datawizards.com', 'phone' => '+8807788990011', 'details' => 'Data scientist with expertise in big data and analytics.'],
    ['id' => 10, 'mentor' => 'Anna Queen', 'name' => 'Science Data Academy', 'email' => 'anna.queen@datascienceacademy.com', 'phone' => '+8804455667788', 'details' => 'Educator and mentor for aspiring data scientists.'],
];

// Get mentor ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Find mentor
$mentor = null;
foreach ($mentorData as $m) {
    if ($m['id'] === $id) {
        $mentor = $m;
        break;
    }
}

if (!$mentor) {
    echo "<p>Mentor or Institute not found. <a href='skill_develop.php'>Go back</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($mentor['mentor']) ?> - Details | FutureBot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #e4cdd3ff, #e8dce3ff, #ffffffff);
      color: #2e3a59;
      margin: 0;
      padding: 0;
    }

    nav {
      padding: 20px 20px;
      background-color: #ffffff;
      box-shadow: 0 2px 10px rgba(97, 95, 95, 1);
      margin: 0;
    }

    nav a {
      color: #58586eff;
      font-weight: 500;
      text-decoration: none;
      border: 2px solid #626377ff;
      padding: 5px 5px;
      border-radius: 25px;
      transition: all 0.3s ease;
    }

    nav a:hover, nav a:focus {
      background-color: #c49bb0ff;
      color: white;
      outline: none;
    }

    .container {
      max-width: 500px;
      margin: 80px auto;
      background: white;
      padding: 30px 25px;
      border-radius: 12px;
      box-shadow: 0 6px 24px rgba(81, 75, 75, 1);
    }

    h1 {
      font-size: 2rem;
      margin-bottom: 12px;
      color: #2e3a59;
    }

    .institute {
      font-style: italic;
      font-size: 1.1rem;
      color: #6b7280;
      margin-bottom: 20px;
    }

    .detail-section {
      margin-bottom: 20px;
    }

    .detail-section strong {
      display: inline-block;
      width: 110px;
      color: #5f5f7dff;
    }

    .detail-section span {
      font-weight: 600;
    }
  </style>
</head>
<body>

<nav>
  <a href="skill_develop.php" aria-label="Back to Skill Develop">← Back to Skill Develop</a>
</nav>

<div class="container">
  <h1><?= htmlspecialchars($mentor['mentor']) ?></h1>
  <div class="institute"><?= htmlspecialchars($mentor['name']) ?></div>

  <div class="detail-section">
    <strong>Email:</strong> <span><a href="mailto:<?= htmlspecialchars($mentor['email']) ?>"><?= htmlspecialchars($mentor['email']) ?></a></span>
  </div>

  <div class="detail-section">
    <strong>Phone:</strong> <span><a href="tel:<?= htmlspecialchars($mentor['phone']) ?>"><?= htmlspecialchars($mentor['phone']) ?></a></span>
  </div>

  <div class="detail-section">
    <strong>About:</strong>
    <p><?= nl2br(htmlspecialchars($mentor['details'])) ?></p>
  </div>
</div>

</body>
</html>
