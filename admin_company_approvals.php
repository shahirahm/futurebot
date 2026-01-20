<?php
session_start();
require 'db.php';



if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



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
        $mail->Password   = 'wxyi euui fatx hoyb';
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
    <style>
        body {
            background: linear-gradient(135deg, #c8f4edff 0%, #ACB6E5 100%);
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
            background-color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.61);
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
            <!-- Filter Dropdown -->
<div class="dropdown">
    <button class="admin-btn dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-funnel-fill"></i> Filter
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
        <li><a class="dropdown-item" href="admin_panel.php"><i class="bi bi-speedometer2"></i> Admin Panel</a></li>
       
        <li><a class="dropdown-item" href="admin_transactions.php"><i class="bi bi-cash-coin"></i> Admin Transactions</a></li>
        <li><a class="dropdown-item" href="admin_books.php"><i class="bi bi-book"></i> Admin Books</a></li>
        <!-- ✅ New menu item added below -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
