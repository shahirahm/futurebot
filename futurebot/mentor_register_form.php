<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$showSuccessPopup = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $university = trim($_POST['university']);
    $subject = trim($_POST['subject']);
    $recent_profession = trim($_POST['recent_profession']);
    $bio = trim($_POST['bio']);
    $location = trim($_POST['location']);

    if (empty($full_name) || empty($email) || empty($university) || empty($subject)) {
        $error = "Full Name, Email, University, and Subject are required.";
    } else {
        // Check if email is already used by another user
        $emailCheck = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $emailCheck->bind_param("si", $email, $user_id);
        $emailCheck->execute();
        $emailResult = $emailCheck->get_result();

        if ($emailResult->num_rows > 0) {
            $error = "This email is already registered by another user.";
        } else {
            // Update user info
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $full_name, $email, $phone, $user_id);
            $stmt->execute();
            $stmt->close();

            // Insert mentor details
            $stmt2 = $conn->prepare("INSERT INTO mentor_details (user_id, university, subject, recent_profession, bio, location) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("isssss", $user_id, $university, $subject, $recent_profession, $bio, $location);
            $stmt2->execute();
            $stmt2->close();

            // Update role
            $stmt3 = $conn->prepare("UPDATE users SET role = 'mentor' WHERE user_id = ?");
            $stmt3->bind_param("i", $user_id);
            $stmt3->execute();
            $stmt3->close();

            // Insert into mentor suggestions for students
            $suggestInsert = $conn->prepare("INSERT INTO mentor_suggestions (full_name, email, phone, university, subject, recent_profession, bio, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $suggestInsert->bind_param("ssssssss", $full_name, $email, $phone, $university, $subject, $recent_profession, $bio, $location);
            $suggestInsert->execute();
            $suggestInsert->close();

            $_SESSION['role'] = 'mentor';
            
            // Show success popup instead of redirect
            $showSuccessPopup = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Mentor Registration - FutureBot</title>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
   background: linear-gradient(135deg, #e4fcf8ff 0%, #d3dbffff 100%);
            color: #04395e;
  }
  .navbar {
    background-color: #fff;
    color: darkblue;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(97, 95, 95, 0.84);
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 20px;
  }
  .navbar a { color: darkblue; text-decoration: none; margin-left: 20px; font-weight: bold; }
  .navbar a:hover { text-decoration: underline; }
  .container {
    max-width: 400px;
    margin: 50px auto;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 6px 24px rgba(72, 65, 65, 1);
    padding: 15px 20px;
    position: relative;
    z-index: 1;
  }
  h2 { text-align: center; color: darkblue; }
  label { display: block; margin-bottom: 6px; color: #333; font-weight: bold; }
  input, textarea {
    width: 92.5%;
    padding: 5px 15px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 15px;
    font-size: 15px;
  }
  button {
    width: 100%;
    padding: 8px;
      background: linear-gradient(90deg, #24153e, #00c6ff);
    color: black;
    font-size: 24px;
    border: none;
    border-radius: 30px;
    cursor: pointer;
  }
  button:hover { background-color: #a58ca4ff; }
  .error { color: red; text-align: center; margin-bottom: 20px; font-weight: bold; }

  /* Popup Modal */
  .modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0; top: 0;
    width: 100%; height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.5s;
  }
  .modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 30px 20px;
    border-radius: 15px;
    max-width: 350px;
    text-align: center;
    animation: slideDown 0.5s;
  }
  .modal-content h3 { color: #0466c8; margin-bottom: 15px; }
  .close-btn {
    background: #0466c8;
    color: white;
    border: none;
    border-radius: 30px;
    padding: 8px 20px;
    cursor: pointer;
    font-size: 16px;
  }
  .close-btn:hover { background: #0353a4; }

  @keyframes fadeIn { from {opacity:0;} to {opacity:1;} }
  @keyframes slideDown { from {transform: translateY(-30px); opacity:0;} to {transform: translateY(0); opacity:1;} }
</style>
</head>
<body>

<div class="navbar">
  <div><strong>FutureBot</strong></div>
  <div>
    <a href="home.php">Home</a>
    <a href="mentor_profile.php">My Profile</a>
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="container">
  <h2>Mentor Registration Form</h2>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <label>Full Name *</label>
    <input type="text" name="full_name" required />

    <label>Email *</label>
    <input type="email" name="email" required />

    <label>Phone Number</label>
    <input type="text" name="phone" placeholder="Optional" />

    <label>University *</label>
    <input type="text" name="university" required />

    <label>Subject *</label>
    <input type="text" name="subject" required />

    <label>Recent Profession</label>
    <input type="text" name="recent_profession" placeholder="Your latest job/profession" />

    <label>Bio</label>
    <textarea name="bio" placeholder="Tell us about yourself, achievements, and mission..."></textarea>

    <label>Location</label>
    <input type="text" name="location" placeholder="City, Area, Online, etc." />

    <button type="submit">Submit</button>
  </form>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal">
  <div class="modal-content">
    <h3>Registration Successful!</h3>
    <p>Welcome to FutureBot as a Mentor 🎉</p>
    <button class="close-btn" onclick="closeModal()">Go to Profile</button>
  </div>
</div>

<script>
function closeModal() {
    document.getElementById('successModal').style.display = 'none';
    window.location.href = 'mentor_profile.php';
}

// Show popup if registration successful
<?php if ($showSuccessPopup): ?>
    document.getElementById('successModal').style.display = 'block';
<?php endif; ?>
</script>

</body>
</html>
