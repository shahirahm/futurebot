<?php
session_start();

// Optional: Redirect if not logged in as company
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Post Job - FutureBot</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: linear-gradient(to right, #e4cdd3ff, #e8dce3ff, #ffffffff);
    }

    .navbar {
      background-color: #fff;
      padding: 15px 30px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar h1 {
      margin: 0;
      font-size: 32px;
      color: #4e4975;
    }

    .navbar a {
      text-decoration: none;
      background-color: #4e4975;
      color: #fff;
      padding: 10px 15px;
      border-radius: 20px;
      font-weight: bold;
    }

    .container {
      max-width: 500px;
      margin: 70px auto;
      background: white;
      padding: 30px;
      border-radius: 30px;
      box-shadow: 0 2px 10px rgba(78, 68, 68, 0.94);
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 1s forwards;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    input, textarea, select {
      width: 92.5%;
      padding: 10px 15px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 15px;
      font-size: 14px;
    }

    .submit-button {
      background-color: #af8ba4ff;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      font-weight: bold;
      width: 100%;
      margin-top: 10px;
    }

    .submit-button:hover {
      background-color: #ba95b4;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <h1>Post a Job</h1>
    <a href="mentor_suggestions.php">View Mentor Suggestions</a>
  </div>

  <div class="container">
    <form action="submit_job.php" method="POST">
      <label>Job Title:</label>
      <input type="text" name="job_title" required>

      <label>Description:</label>
      <textarea name="description" rows="4" required></textarea>

      <label>Location:</label>
      <input type="text" name="location" required>

      <label>Required Skills:</label>
      <input type="text" name="skills" placeholder="E.g., AI, Web Dev, Data Science" required>

      <label>Deadline:</label>
      <input type="date" name="deadline" required>

      <button type="submit" class="submit-button">Post Job</button>
    </form>
  </div>

</body>
</html>