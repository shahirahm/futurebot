<?php
session_start();
require_once 'db.php';

// Ensure user is logged in
$email = $_SESSION['user_email'] ?? null;
if (!$email) {
    die("Error: You must be logged in to submit a project.");
}

// Create student_projects table if it doesn't exist
$create_table_sql = "
    CREATE TABLE IF NOT EXISTS student_projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        skills_used TEXT,
        image_path VARCHAR(500),
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_email),
        INDEX (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if (!$conn->query($create_table_sql)) {
    error_log("Error creating student_projects table: " . $conn->error);
}

$submission_success = false;
$submission_error = '';
$submitted_title = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $skills = trim($_POST['skills']);
    $file_path = '';

    // Validate required fields
    if (empty($title) || empty($description)) {
        $submission_error = "Please fill in all required fields.";
    } else {
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'application/pdf'
            ];

            $fileType = $_FILES['file']['type'];
            $fileSize = $_FILES['file']['size'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($fileType, $allowedTypes)) {
                $submission_error = "Error: Only JPG, PNG, GIF, WEBP images, and PDF files are allowed.";
            } elseif ($fileSize > $maxSize) {
                $submission_error = "Error: File size must be 5MB or less.";
            } else {
                $uploadDir = 'project_uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9_\.-]/", "_", basename($_FILES['file']['name']));
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                    $file_path = $uploadFile;
                } else {
                    $submission_error = "Failed to upload file.";
                }
            }
        }

        if (empty($submission_error)) {
            $stmt = $conn->prepare("INSERT INTO student_projects (user_email, title, description, skills_used, image_path) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt) {
                $stmt->bind_param("sssss", $email, $title, $description, $skills, $file_path);
                
                if ($stmt->execute()) {
                    $submission_success = true;
                    $submitted_title = $title;
                    // Clear form data
                    $_POST = array();
                } else {
                    $submission_error = "Failed to submit project. Database error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $submission_error = "Database error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Submit Project - FutureBot</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
        --primary-blue: #4361ee;
        --dark-blue: #3a0ca3;
        --light-blue: #4cc9f0;
        --very-light-blue: #f8faff;
        --purple: #7209b7;
        --light-purple: #f72585;
        --white: #ffffff;
        --success: #289842;
        --danger: #e63946;
        --warning: #fca311;
        --gray: #6c757d;
        --light-gray: #f8f9fa;
        --text-dark: #2c3e50;
        --text-light: #64748b;
        --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --shadow-hover: 0 8px 30px rgba(0, 0, 0, 0.12);
        --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        --gradient-light: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
        --gradient-hover: linear-gradient(135deg, #3a0ca3 0%, #7209b7 100%);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        color: var(--text-dark);
        min-height: 100vh;
        line-height: 1.6;
        overflow-x: hidden;
        position: relative;
        padding-top: 70px;
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
        background: rgba(67, 97, 238, 0.03);
        animation: float 20s infinite ease-in-out;
    }

    .circle:nth-child(1) {
        width: 60px;
        height: 60px;
        top: 10%;
        left: 10%;
        animation-delay: 0s;
    }

    .circle:nth-child(2) {
        width: 100px;
        height: 100px;
        top: 70%;
        left: 80%;
        animation-delay: 2s;
    }

    .circle:nth-child(3) {
        width: 40px;
        height: 40px;
        top: 40%;
        left: 85%;
        animation-delay: 4s;
    }

    .circle:nth-child(4) {
        width: 80px;
        height: 80px;
        top: 80%;
        left: 15%;
        animation-delay: 6s;
    }

    .circle:nth-child(5) {
        width: 50px;
        height: 50px;
        top: 20%;
        left: 70%;
        animation-delay: 8s;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0) translateX(0);
        }
        25% {
            transform: translateY(-15px) translateX(8px);
        }
        50% {
            transform: translateY(8px) translateX(-12px);
        }
        75% {
            transform: translateY(-12px) translateX(-8px);
        }
    }

    /* Navbar Styles */
    .navbar {
        background-color: var(--white);
        padding: 0.5rem 2rem;
        border-bottom: 1px solid rgba(67, 97, 238, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow);
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        backdrop-filter: blur(10px);
        height: 70px;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .logo-icon {
        width: 40px;
        height: 40px;
        background: var(--gradient);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.1rem;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
    }

    .logo-text {
        font-size: 1.4rem;
        font-weight: 700;
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .nav-links {
        display: flex;
        gap: 2rem;
        align-items: center;
    }

    .nav-link {
        color: var(--text-dark);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }

    .nav-link:hover {
        color: var(--primary-blue);
        background: rgba(67, 97, 238, 0.05);
        transform: translateY(-1px);
    }

    .back-button {
        background: var(--gradient);
        color: white;
        border: none;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .back-button:hover {
        background: var(--gradient-hover);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        color: white;
        text-decoration: none;
    }

    /* Main Content */
    .container {
        max-width: 600px;
        margin: 2rem auto;
        padding: 0 1.25rem;
    }

    .form-card {
        background: var(--white);
        border-radius: 20px;
        box-shadow: var(--shadow);
        padding: 2.5rem;
        animation: fadeIn 0.8s ease-in-out;
        overflow: hidden;
        position: relative;
        border: 1px solid rgba(67, 97, 238, 0.1);
        backdrop-filter: blur(10px);
    }

    .form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-title {
        color: var(--text-dark);
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .form-subtitle {
        color: var(--text-light);
        text-align: center;
        margin-bottom: 2rem;
        font-size: 1rem;
    }

    /* Alert Styles */
    .alert {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: none;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        animation: fadeIn 0.6s ease-in-out;
    }

    .alert-success {
        background: rgba(40, 152, 66, 0.1);
        color: #198754;
        border-left: 4px solid #198754;
    }

    .alert-warning {
        background: rgba(255, 193, 7, 0.1);
        color: #856404;
        border-left: 4px solid #ffc107;
    }

    .alert-danger {
        background: rgba(230, 57, 70, 0.1);
        color: #dc3545;
        border-left: 4px solid #dc3545;
    }

    /* Success Modal */
    .modal-success {
        background: rgba(0, 0, 0, 0.5);
    }

    .modal-success .modal-dialog {
        max-width: 500px;
    }

    .modal-success .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: var(--shadow-hover);
        overflow: hidden;
    }

    .modal-success .modal-header {
        border-bottom: none;
        padding: 2rem 2rem 0.5rem;
        position: relative;
    }

    .modal-success .modal-body {
        padding: 0 2rem 2rem;
        text-align: center;
    }

    .success-icon {
        font-size: 4rem;
        color: var(--success);
        margin-bottom: 1.5rem;
        animation: bounce 1s ease-in-out;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }

    .success-title {
        color: var(--text-dark);
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .success-message {
        color: var(--text-light);
        font-size: 1.1rem;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-secondary {
        background: var(--light-gray);
        color: var(--text-dark);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-secondary:hover {
        background: #e9ecef;
        color: var(--text-dark);
        text-decoration: none;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        display: block;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid rgba(67, 97, 238, 0.1);
        border-radius: 12px;
        background: var(--white);
        color: var(--text-dark);
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: 'Inter', sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        transform: translateY(-1px);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .btn-primary {
        background: var(--gradient);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.3);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        width: 100%;
        margin-top: 1rem;
    }

    .btn-primary:hover {
        background: var(--gradient-hover);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
    }

    .form-text {
        font-size: 0.85rem;
        color: var(--text-light);
        margin-top: 0.25rem;
    }

    /* Footer Styles */
    footer {
        width: 100%;
        background: rgba(255, 255, 255, 0.95);
        padding: 2rem 1.25rem;
        border-top: 1px solid rgba(67, 97, 238, 0.08);
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.03);
        margin-top: 4rem;
        backdrop-filter: blur(10px);
    }

    .footer-content {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.25rem;
    }

    .footer-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.3rem;
        font-weight: bold;
        background: var(--gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .footer-links {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .footer-links a {
        color: var(--text-light);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        font-size: 0.9rem;
    }

    .footer-links a:hover {
        color: var(--primary-blue);
        transform: translateY(-1px);
    }

    .footer-social {
        display: flex;
        gap: 1rem;
        margin: 0.75rem 0;
    }

    .footer-social a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: var(--gradient);
        color: white;
        border-radius: 50%;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
        font-size: 0.9rem;
    }

    .footer-social a:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 15px rgba(67, 97, 238, 0.35);
    }

    .footer-bottom {
        text-align: center;
        padding-top: 1.25rem;
        border-top: 1px solid rgba(67, 97, 238, 0.08);
        width: 100%;
        color: var(--text-light);
        font-size: 0.85rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .navbar {
            padding: 0.5rem 1rem;
        }
        
        .logo-text {
            font-size: 1.2rem;
        }
        
        .container {
            margin: 1.5rem auto;
            padding: 0 1rem;
        }
        
        .form-card {
            padding: 1.5rem;
            border-radius: 16px;
        }
        
        .nav-links {
            gap: 1rem;
        }
        
        .footer-links {
            flex-direction: column;
            gap: 1rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .modal-success .modal-header,
        .modal-success .modal-body {
            padding: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .form-card {
            padding: 1.25rem;
        }

        .navbar {
            padding: 0.4rem 0.8rem;
        }

        .logo-icon {
            width: 35px;
            height: 35px;
        }
        
        .form-title {
            font-size: 1.5rem;
        }

        .success-title {
            font-size: 1.5rem;
        }

        .modal-success .modal-header,
        .modal-success .modal-body {
            padding: 0rem;
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

  <!-- Navigation Bar -->
  <nav class="navbar">
      <div class="logo">
          <div class="logo-icon">
              <i class="fas fa-robot"></i>
          </div>
          <div class="logo-text">FutureBot</div>
      </div>
      
      <div class="nav-links">
          <a href="career_suggestions.php" class="back-button">
              <i class="fas fa-arrow-left"></i> Back to Career
          </a>
      </div>
  </nav>

  <div class="container">
      <div class="form-card">
          <!-- Project Submission Form -->
          <h2 class="form-title">Submit Your Project</h2>
          <p class="form-subtitle">Share your amazing work with the FutureBot community</p>
          
          <?php if ($submission_error): ?>
              <div class="alert alert-danger">
                  <i class="fas fa-exclamation-circle"></i>
                  <?= htmlspecialchars($submission_error) ?>
              </div>
          <?php endif; ?>
          
          <form action="submit_project.php" method="POST" enctype="multipart/form-data" novalidate>
              <div class="form-group">
                  <label for="title" class="form-label">Project Title *</label>
                  <input
                      type="text"
                      name="title"
                      id="title"
                      class="form-control"
                      placeholder="Enter your project title"
                      value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>"
                      required
                      autocomplete="off"
                  />
              </div>
              
              <div class="form-group">
                  <label for="description" class="form-label">Project Description *</label>
                  <textarea
                      name="description"
                      id="description"
                      class="form-control"
                      rows="5"
                      placeholder="Describe your project, what it does, and what problems it solves..."
                      required
                  ><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
              </div>
              
              <div class="form-group">
                  <label for="skills" class="form-label">Skills Used</label>
                  <input
                      type="text"
                      name="skills"
                      id="skills"
                      class="form-control"
                      placeholder="e.g. PHP, JavaScript, SQL, Python, React"
                      value="<?= isset($_POST['skills']) ? htmlspecialchars($_POST['skills']) : '' ?>"
                      autocomplete="off"
                  />
                  <div class="form-text">Separate skills with commas</div>
              </div>
              
              <div class="form-group">
                  <label for="file" class="form-label">Project File (Optional)</label>
                  <input
                      type="file"
                      name="file"
                      id="file"
                      class="form-control"
                      accept="image/*,application/pdf"
                  />
                  <div class="form-text">Max size: 5MB. Supported: JPG, PNG, GIF, WEBP, PDF</div>
              </div>
              
              <button type="submit" class="btn-primary">
                  <i class="fas fa-paper-plane"></i> Submit for Approval
              </button>
          </form>
      </div>
  </div>

  <!-- Success Modal -->
  <?php if ($submission_success): ?>
  <div class="modal modal-success fade show" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-modal="true" role="dialog" style="display: block; background-color: rgba(0,0,0,0.5);">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closeModal()"></button>
              </div>
              <div class="modal-body">
                  <div class="success-icon">
                      <i class="fas fa-check-circle"></i>
                  </div>
                  <h2 class="success-title">Project Submitted Successfully!</h2>
                  <p class="success-message">
                      Your project "<strong><?= htmlspecialchars($submitted_title) ?></strong>" has been submitted for admin approval.<br>
                      You will be notified once it's reviewed and published.
                  </p>
                  <div class="action-buttons">
                      <button type="button" class="btn-primary" onclick="closeModal()">
                          <i class="fas fa-check"></i> Continue
                      </button>
                      <a href="career_suggestions.php" class="btn-secondary">
                          <i class="fas fa-arrow-left"></i> Back to Career
                      </a>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <?php endif; ?>

  <!-- Footer -->
  <footer>
      <div class="footer-content">
          <div class="footer-logo">
              <i class="fas fa-robot"></i>FutureBot
          </div>
          
          <div class="footer-links">
              <a href="home.php">Home</a>
              <a href="about.php">About Us</a>
              <a href="career_suggestions.php">Career Suggestions</a>
              <a href="privacy.php">Privacy Policy</a>
              <a href="contact.php">Contact Us</a>
          </div>
          
          <div class="footer-social">
              <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
              <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
              <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
              <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
          </div>
          
          <div class="footer-bottom">
              <p>&copy; 2024 FutureBot. All rights reserved. | Empowering students with AI-driven career guidance</p>
          </div>
      </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
      function closeModal() {
          const modal = document.getElementById('successModal');
          if (modal) {
              modal.style.display = 'none';
          }
      }

      // Close modal when clicking outside
      document.addEventListener('click', function(event) {
          const modal = document.getElementById('successModal');
          if (modal && event.target === modal) {
              closeModal();
          }
      });

      // Close modal with Escape key
      document.addEventListener('keydown', function(event) {
          if (event.key === 'Escape') {
              closeModal();
          }
      });
  </script>
</body>
</html>