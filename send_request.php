<?php
session_start();
$mentor_email = isset($_GET['mentor_email']) ? $_GET['mentor_email'] : '';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Send Request - FutureBot</title>
  <style>
   body {
      font-family: Arial, sans-serif;
     background: linear-gradient(135deg, #e4fcf8ff 0%, #d3dbffff 100%);
            color: #1033a6ff;
        
      margin: 0;
      padding-top: 0px; /* Add top padding for fixed navbar */
    }
    .navbar {
      background-color: #fff;
      padding: 15px 30px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .navbar h1 {
      margin: 0;
      font-size: 28px;
      color: #4e4975;
    }

    .container {
      max-width: 400px;
      margin: 50px auto;
      background: white;
      padding: 25px 20px;
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    }

    label {
      font-weight: bold;
      margin-top: 15px;
      display: block;
      color: #4e4975;
    }

    input, textarea {
      width: 92.5%;
      padding: 8px 15px;
      margin-bottom: 8px;
      border: 1px solid #ccc;
      border-radius: 12px;
      font-size: 15px;
    }

    input:focus, textarea:focus {
      outline: none;
      border-color: #836fff;
      box-shadow: 0 0 8px rgba(131,111,255,0.4);
    }

    .submit-button {
      width: 100%;
      background-color: #2422c4ff;
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 25px;
      cursor: pointer;
      font-weight: bold;
      margin-top: 15px;
      transition: background 0.3s ease;
    }

    .submit-button:hover {
      background-color: #836fff;
    }

    .success {
      color: green;
      font-weight: bold;
      margin-top: 20px;
      text-align: center;
    }

    @media (max-width: 500px) {
      .container {
        width: 90%;
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="navbar">
    <h1>Send Request</h1>
  </div>

  <div class="container">
    <form action="submit_mentor_request.php" method="POST">
      <input type="hidden" name="mentor_email" value="<?= htmlspecialchars($mentor_email) ?>">

      <label>Student Name:</label>
      <input type="text" name="student_name" required>

      <label>Location:</label>
      <input type="text" name="location" required>

      <label>Institute Name:</label>
      <input type="text" name="institute" required>

      <label>Subject You Want to Learn:</label>
      <input type="text" name="subject" required>

      <label>Contact Info:</label>
      <input type="text" name="contact" required>

      <button type="submit" class="submit-button">Send</button>
    </form>
  </div>
</body>
</html>
