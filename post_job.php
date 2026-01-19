<?php
session_start();
require 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type = trim($_POST['type'] ?? ''); // job, internship, mentor_hiring
    $location = trim($_POST['location'] ?? '');
    $skills = trim($_POST['skills'] ?? '');
    $deadline = trim($_POST['deadline'] ?? '');

    // Validation
    if (empty($title) || empty($description) || empty($type)) {
        $error = 'Please fill in all required fields.';
    } else {
        $posted_by = $_SESSION['user_email'];

        // Get user_id based on logged-in user's email
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $posted_by);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        if (!$user_id) {
            $error = "User not found in database.";
        } else {
            // Insert the job post
            $stmt = $conn->prepare("INSERT INTO job_posts (user_id, title, description, type, location, skills, deadline, posted_by, created_at) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param("isssssss", $user_id, $title, $description, $type, $location, $skills, $deadline, $posted_by);
            if ($stmt->execute()) {
                $success = "Job post submitted successfully.";
            } else {
                $error = "Error posting job.";
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Post Job / Internship / Mentor Hiring - FutureBot</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  body {
    background: #f0f4f8;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  .container {
    max-width: 600px;
    margin: 40px auto;
    background: #fff;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
  }
  h2 {
    margin-bottom: 25px;
    color: #0d6efd;
    text-align: center;
    font-weight: 700;
  }
  form {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  label {
    font-weight: 600;
    color: #343a40;
  }
  input[type="text"],
  input[type="date"],
  select,
  textarea {
    padding: 12px 15px;
    border: 1.8px solid #ced4da;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
  }
  input[type="text"]:focus,
  input[type="date"]:focus,
  select:focus,
  textarea:focus {
    border-color: #0d6efd;
    outline: none;
    box-shadow: 0 0 8px rgba(13,110,253,0.4);
  }
  textarea {
    resize: vertical;
    min-height: 120px;
  }
  button[type="submit"] {
    background: linear-gradient(90deg, #0d6efd, #6610f2);
    border: none;
    border-radius: 30px;
    padding: 14px 0;
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
  }
  button[type="submit"]:hover {
    background: linear-gradient(90deg, #6610f2, #0d6efd);
    transform: translateY(-2px);
  }
  .alert {
    margin-top: -10px;
  }
  @media (max-width: 640px) {
    .container {
      margin: 20px 15px;
      padding: 25px;
    }
  }
</style>
</head>
<body>
<div class="container">
  <h2>Post a Job / Internship / Mentor Hiring</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php elseif ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <label for="title">Title *</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required />

    <label for="description">Description *</label>
    <textarea id="description" name="description" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

    <label for="type">Type *</label>
    <select id="type" name="type" required>
      <option value="" <?= empty($_POST['type']) ? 'selected' : '' ?>>Select type</option>
      <option value="job" <?= (($_POST['type'] ?? '') === 'job') ? 'selected' : '' ?>>Job</option>
      <option value="internship" <?= (($_POST['type'] ?? '') === 'internship') ? 'selected' : '' ?>>Internship</option>
      <option value="mentor_hiring" <?= (($_POST['type'] ?? '') === 'mentor_hiring') ? 'selected' : '' ?>>Mentor Hiring</option>
    </select>

    <label for="location">Location</label>
    <input type="text" id="location" name="location" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" />

    <label for="skills">Skills (comma separated)</label>
    <input type="text" id="skills" name="skills" value="<?= htmlspecialchars($_POST['skills'] ?? '') ?>" />

    <label for="deadline">Application Deadline</label>
    <input type="date" id="deadline" name="deadline" value="<?= htmlspecialchars($_POST['deadline'] ?? '') ?>" />

    <button type="submit">Post</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
