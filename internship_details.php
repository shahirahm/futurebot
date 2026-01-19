<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: register.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid internship ID.");
}

$internship_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT title, company_name, duration_hours, location_type, description, application_link 
                        FROM micro_internships 
                        WHERE id = ?");
$stmt->bind_param("i", $internship_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Internship not found.");
}

$internship = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Internship Details - <?= htmlspecialchars($internship['title']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #c8f4edff 0%, #ACB6E5 100%);
      color: #04395e;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      padding-top: 40px;
    }
    .card {
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(4, 57, 94, 0.2);
      background: #ffffffcc;
      color: #04395e;
      max-width: 700px;
      margin: auto;
      padding: 2rem;
    }
    a.btn-primary {
      background: #0466c8;
      border: none;
    }
    a.btn-primary:hover {
      background: #0353a4;
    }
  </style>
</head>
<body>

<div class="card">
  <h1 class="mb-3"><?= htmlspecialchars($internship['title']) ?></h1>
  <h5 class="text-muted mb-3"><?= htmlspecialchars($internship['company_name']) ?></h5>
  <p><strong>Duration:</strong> <?= intval($internship['duration_hours']) ?> hours</p>
  <p><strong>Location:</strong> <?= htmlspecialchars($internship['location_type']) ?></p>
  <hr>
  <p><?= nl2br(htmlspecialchars($internship['description'])) ?></p>
  <a href="apply_internship.php?id=<?= $internship_id ?>" class="btn btn-primary my-3">Apply for this Internship</a>

  <br />
  <a href="career_suggestions.php" class="btn btn-outline-primary">Back to Career Suggestions</a>
</div>

</body>
</html>
