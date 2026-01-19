<?php
session_start();
require_once 'db.php';

$mentor_name = $_SESSION['username'] ?? 'Unknown Mentor';

// Fetch only necessary fields
$result = $conn->query("SELECT id, student_name, location, institute, subject, contact FROM mentor_requests ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>FutureBot - Hire Requests</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #e4fcf8ff 0%, #d3dbffff 100%);
      color: #1033a6ff;
      min-height: 100vh;
      margin: 0;
      padding-top: 80px; /* padding for fixed navbar */
    }

    .navbar {
      background-color: #ffffffff;
      font-size: 1rem;
      padding: 15px 30px;
      border-bottom: 2px solid #dad5dbff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 8px rgba(41, 39, 39, 1);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      width: 100%;
      z-index: 1000;
    }

    .navbar h1 {
      color: #27205bff;
      margin: 0;
      font-size: 28px;
    }

    .back-button {
      background-color: white;
      color: #2c174bff;
      padding: 8px 16px;
      border-radius: 20px;
      border: none;
      cursor: pointer;
      font-weight: bold;
      text-decoration: none;
    }
    

    .container {
      max-width: 100%;
      animation: fadeIn 0.8s ease-in-out;
      background: white;
      padding: 15px;
      border-radius: 30px;
      box-shadow: 0 4px 10px rgba(24, 23, 23, 1);
      margin-top: 50px;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      text-align: left;
    }

    th {
      background-color: #383c76ff;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f3faff;
    }

    .action-btn {
      padding: 6px 12px;
      border: none;
      cursor: pointer;
      color: white;
      border-radius: 20px;
      margin-right: 5px;
      font-weight: bold;
    }

    .accept { background-color: #289842ff; }
    .reject { background-color: #dc3545; }
    .ignore { background-color: #6c757d; }
  </style>
</head>
<body>

  <div class="navbar">
    <h1>Hire Requests</h1>
    <a href="mentor_profile.php" class="back-button">← Back</a>
  </div>

  <div class="container">
    <table>
      <tr>
        <th>Student Name</th>
        <th>Location</th>
        <th>Institute Name</th>
        <th>Subject You Want to Learn</th>
        <th>Contact Info</th>
        <th>Action</th>
      </tr>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['student_name']) ?></td>
        <td><?= htmlspecialchars($row['location']) ?></td>
        <td><?= htmlspecialchars($row['institute']) ?></td>
        <td><?= htmlspecialchars($row['subject']) ?></td>
        <td><?= htmlspecialchars($row['contact']) ?></td>
        <td>
          <!-- Accept -->
          <form action="handle_request_action.php" method="POST" style="display:inline;">
            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
            <input type="hidden" name="action" value="accept">
            <input type="hidden" name="mentor" value="<?= htmlspecialchars($mentor_name) ?>">
            <button type="submit" class="action-btn accept">Accept</button>
          </form>

          <!-- Reject -->
          <form action="handle_request_action.php" method="POST" style="display:inline;">
            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="mentor" value="<?= htmlspecialchars($mentor_name) ?>">
            <button type="submit" class="action-btn reject">Reject</button>
          </form>

          <!-- Ignore -->
          <form action="handle_ignore_request.php" method="POST" style="display:inline;">
            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
            <button type="submit" class="action-btn ignore">Ignore</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>

</body>
</html>
