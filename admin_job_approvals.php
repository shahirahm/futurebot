<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

<<<<<<< HEAD
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
=======


$error = '';
$success = '';
>>>>>>> 3a53d640d30dfc9fadbba3f0c744b5a4c85812dc

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function sendApprovalEmail($toEmail, $toName, $status, $companyId = null) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'samihamaisha231@gmail.com';
        $mail->Password   = 'ytid jrgk fwgz fkgt';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('samihamaisha231@gmail.com', 'FutureBot Admin');
        $mail->addAddress($toEmail, $toName);
        $mail->isHTML(true);

        $mail->Subject = 'Company Registration ' . ucfirst($status);
        $trackingPixelUrl = "http://localhost/futurebot/track_open.php?email=" . urlencode($toEmail);

        if ($status === 'approved' && $companyId !== null) {
            $link = "http://localhost/futurebot/company_profile_create.php?company_id=" . $companyId;
            $mail->Body = "
                <p>Dear <strong>" . htmlspecialchars($toName) . "</strong>,</p>
                <p>Your company registration has been <strong>approved</strong>.</p>
                <p><a href='$link'>Complete Company Profile</a></p>
                <p>Regards,<br>FutureBot Team</p>
                <img src='$trackingPixelUrl' width='1' height='1' style='display:none;' alt='tracker'>
            ";
        } else {
            $mail->Body = "
                <p>Dear <strong>" . htmlspecialchars($toName) . "</strong>,</p>
                <p>Your company registration has been <strong>rejected</strong>.</p>
                <p>Regards,<br>FutureBot Team</p>
                <img src='$trackingPixelUrl' width='1' height='1' style='display:none;' alt='tracker'>
            ";
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email error: " . $mail->ErrorInfo);
        return false;
    }
}

<<<<<<< HEAD
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['company_id'], $_POST['action'])) {
    $id = (int) $_POST['company_id'];
    $action = $_POST['action'];
    $status = ($action === "approve") ? "approved" : "rejected";

    $stmt = $conn->prepare("UPDATE companies SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();

    $stmt2 = $conn->prepare("SELECT name, email FROM companies WHERE id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $stmt2->bind_result($companyName, $companyEmail);
    $stmt2->fetch();
    $stmt2->close();

    if ($companyEmail && $companyName) {
        $emailSent = sendApprovalEmail($companyEmail, $companyName, $status, $status === 'approved' ? $id : null);
        echo "<script>alert('Company has been $status. " . ($emailSent ? "Email sent." : "Email failed.") . "');</script>";
=======


// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $post_id = $_POST['post_id'];
    
    $stmt = $conn->prepare("DELETE FROM mentor_posts WHERE post_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $post_id);
        if ($stmt->execute()) {
            $success = "Post deleted successfully!";
        } else {
            $error = "Error deleting post: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Database error: " . $conn->error;
>>>>>>> 3a53d640d30dfc9fadbba3f0c744b5a4c85812dc
    }

    echo "<script>window.location.href = 'admin_company_approvals.php';</script>";
    exit;
}

$result = $conn->query("SELECT * FROM companies WHERE status = 'pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Company Approval Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
             background: linear-gradient(135deg, #e6f8e8 0%, #e4f0e8 100%);
            color: #04395e;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .admin-btn {
            background: linear-gradient(90deg, #0d6efd, #6610f2);
            border: none;
            border-radius: 30px;
            padding: 8px 20px;
            font-weight: bold;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: 0.3s ease-in-out;
            text-decoration: none;
        }

        .admin-btn:hover {
            background: linear-gradient(90deg, #6610f2, #0d6efd);
            transform: translateY(-2px);
            color: white;
        }

        .navbar {
             background: rgba(243, 253, 246, 0.95);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.33);
        }

        .navbar-brand {
            color: #0d6efd !important;
            font-weight: bold;
            font-size: 1.6rem;
            margin-left: 0px;
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .dropdown-item i {
            margin-right: 8px;
        }
         .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .logo i {
            color: var(--accent);
        }
        
        .btn-back {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(15, 98, 254, 0.3);
        }
        
        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 98, 254, 0.4);
        
        }

        /* Footer Styles */
        footer {
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px 20px;
            margin-top: 50px;
            border-top: 1px solid rgba(67, 97, 238, 0.1);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(90deg, #4361ee, #3a0ca3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .footer-links {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .footer-links a {
            color: #5a6c7d;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .footer-links a:hover {
            color: #4361ee;
            transform: translateY(-2px);
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #4361ee;
            transition: width 0.3s ease;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-social {
            display: flex;
            gap: 20px;
            margin: 10px 0;
        }

        .footer-social a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #4361ee, #3a0ca3);
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .footer-social a:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(67, 97, 238, 0.1);
            width: 100%;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        /* Responsive Footer */
        @media (max-width: 768px) {
            .footer-links {
                gap: 15px;
            }
            
            .footer-social {
                gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .footer-links {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            
            .footer-social a {
                width: 35px;
                height: 35px;
            }
        }
    </style>
</head>
<body class="bg-light">

<!-- Admin Panel Navbar -->
<nav class="navbar navbar-expand-lg shadow">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <span class="navbar-brand fw-bold">FutureBot</span>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <!-- Filter Dropdown -->
            <div class="dropdown">
                <button class="admin-btn dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item" href="admin_panel.php"><i class="bi bi-speedometer2"></i> Admin Panel</a></li>
                    <li><a class="dropdown-item" href="admin_transactions.php"><i class="bi bi-cash-coin"></i> Admin Transactions</a></li>
                    <li><a class="dropdown-item" href="admin_books.php"><i class="bi bi-book"></i> Admin Books</a></li>
                    <li><a class="dropdown-item" href="admin_projects.php"><i class="bi bi-kanban"></i> Project Approvals</a></li>
                    <li><a class="dropdown-item" href="admin_job_approvals.php"><i class="bi bi-kanban"></i> Job Approvals</a></li>
                    <li><a class="dropdown-item" href="admin_internship_approvals.php"><i class="bi bi-kanban"></i> Internship Approvals</a></li>
                </ul>
            </div>

            <!-- Logout Button -->
            <a href="logout.php" class="admin-btn"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="card shadow p-4">
        <h2 class="mb-4 text-center">🛡️ Pending Company Verifications</h2>

        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info text-center">No pending companies found.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Start Year</th>
                        <th>License</th>
                        <th>Document</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['start_year']) ?></td>
                        <td><?= htmlspecialchars($row['trade_license']) ?></td>
                        <td>
                            <?php if (!empty($row['document_path'])): ?>
                                <a href="<?= htmlspecialchars($row['document_path']) ?>" class="btn btn-info btn-sm" target="_blank">View</a>
                            <?php else: ?>
                                <span class="text-muted">No document</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="d-flex justify-content-center gap-2 flex-wrap">
                                <input type="hidden" name="company_id" value="<?= $row['id'] ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm px-3">Approve</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm px-3">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer>
  <div class="footer-content">
    <div class="footer-logo">
      <i class="fas fa-robot"></i>FutureBot
    </div>
    
    <div class="footer-links">
      <a href="index.php">Home</a>
      <a href="about.php">About Us</a>
      <a href="privacy.php">Privacy Policy</a>
      <a href="terms.php">Terms of Service</a>
      <a href="contact.php">Contact Us</a>
    </div>
    
    <div class="footer-social">
      <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
      <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
      <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
      <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
      <a href="#" title="GitHub"><i class="fab fa-github"></i></a>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; 2024 FutureBot. All rights reserved. | Empowering students with AI-driven career guidance</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>