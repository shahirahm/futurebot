<?php
session_start();
require_once 'db.php';

// Ensure user is logged in
$email = $_SESSION['user_email'] ?? null;
if (!$email) {
    die("Error: You must be logged in to submit a project.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $skills = trim($_POST['skills']);
    $file_path = '';

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf'
        ];

        $fileType = $_FILES['file']['type'];
        $fileSize = $_FILES['file']['size'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($fileType, $allowedTypes)) {
            die("Error: Only JPG, PNG, GIF, WEBP images, and PDF files are allowed.");
        }

        if ($fileSize > $maxSize) {
            die("Error: File size must be 5MB or less.");
        }

        $uploadDir = 'project_uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", basename($_FILES['file']['name']));
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            $file_path = $uploadFile;
        } else {
            die("Failed to upload file.");
        }
    }

    $stmt = $conn->prepare("INSERT INTO student_projects (user_email, title, description, skills_used, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $email, $title, $description, $skills, $file_path);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        die("Insert failed: " . $stmt->error);
    }

    $stmt->close();
    header("Location: career_suggestions.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Submit Project - FutureBot</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #e4fcf8ff 0%, #d3dbffff 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }
    .navbar {
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 0.75rem 1.5rem;
      position: sticky;
      top: 0;
      z-index: 1030;
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .back-button {
      font-size: 1rem;
      color: #000000ff;
      background: linear-gradient(90deg, #24153e, #00c6ff);
      padding: 0.4rem 0.8rem;
      border-radius: 5rem;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }
    .back-button:hover {
      opacity: 0.85;
      color: #fff;
      text-decoration: none;
    }
    .navbar-title {
      font-weight: 700;
      font-size: 1.25rem;
      color: #1e1c99ff;
      flex-grow: 1;
      margin-left: 0px;
      user-select: none;
      text-align: center;
    }

    .form-container {
      max-width: 500px;
      margin: 40px auto 70px auto;
      background: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      transition: box-shadow 0.3s ease;
    }
    .form-container:hover {
      box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }
    h2 {
      font-weight: 700;
      color: #341e97ff;
      margin-bottom: 25px;
      letter-spacing: 1px;
      text-align: center;
    }
    label.form-label {
      font-weight: 600;
      color: #212529;
    }
    input.form-control, textarea.form-control {
      border-radius: 8px;
      border: 1.5px solid #ced4da;
      padding: 12px 15px;
      font-size: 1rem;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    input.form-control:focus, textarea.form-control:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 8px rgba(13,110,253,.3);
      outline: none;
    }
    .form-text {
      font-size: 0.85rem;
      color: #6c757d;
    }
    button.btn-primary {
      width: 100%;
      font-weight: 600;
      padding: 12px;
      font-size: 1.1rem;
      border-radius: 8px;
      background: linear-gradient(90deg, #24153e, #00c6ff);
      border: none;
      transition: 0.3s ease;
    }
    button.btn-primary:hover {
      opacity: 0.85;
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <a href="career_suggestions.php" class="back-button" title="Back to Career Suggestions">← Back</a>
    <div class="navbar-title">FutureBot</div>
    <div style="width: 70px;"></div>
  </nav>

  <main>
    <div class="form-container shadow-sm">
      <h2>🛠️ Submit Your Project</h2>
      <form action="submit_project.php" method="POST" enctype="multipart/form-data" novalidate>
        <div class="mb-4">
          <label for="title" class="form-label">Project Title</label>
          <input
            type="text"
            name="title"
            id="title"
            class="form-control"
            placeholder="Enter project title"
            required
            autocomplete="off"
          />
        </div>
        <div class="mb-4">
          <label for="description" class="form-label">Description</label>
          <textarea
            name="description"
            id="description"
            class="form-control"
            rows="5"
            placeholder="Write a brief description of your project"
            required
          ></textarea>
        </div>
        <div class="mb-4">
          <label for="skills" class="form-label">Skills Used <small class="text-muted">(comma-separated)</small></label>
          <input
            type="text"
            name="skills"
            id="skills"
            class="form-control"
            placeholder="e.g. PHP, JavaScript, SQL"
            autocomplete="off"
          />
        </div>
        <div class="mb-4">
          <label for="file" class="form-label">Upload Image or PDF</label>
          <input
            type="file"
            name="file"
            id="file"
            class="form-control"
            accept="image/*,application/pdf"
          />
          <div class="form-text">Optional. Max size: 5MB. Allowed types: JPG, PNG, GIF, WEBP, PDF.</div>
        </div>
        <button type="submit" class="btn btn-primary">Submit Project</button>
      </form>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
