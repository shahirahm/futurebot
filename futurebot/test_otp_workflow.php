<?php
session_start();
require 'db.php';

// Test data - you can modify these for testing
$test_company_email = 'samihamaisha231@gmail.com';
$test_company_name = 'Test Company';

// Function to check if company exists and get status
function getCompanyStatus($email) {
    global $conn;
    $stmt = $conn->prepare("SELECT status, otp, otp_expires_at FROM companies WHERE email = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($status, $otp, $otp_expires_at);
    $stmt->fetch();
    $stmt->close();
    
    return [
        'status' => $status,
        'otp' => $otp,
        'otp_expires_at' => $otp_expires_at,
        'has_valid_otp' => ($otp && $otp_expires_at && date('Y-m-d H:i:s') <= $otp_expires_at)
    ];
}

$company_status = getCompanyStatus($test_company_email);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Workflow Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-card { margin: 20px 0; padding: 20px; border-radius: 8px; }
        .status-pending { background-color: #fff3cd; border: 1px solid #ffeaa7; }
        .status-approved { background-color: #d1ecf1; border: 1px solid #bee5eb; }
        .status-rejected { background-color: #f8d7da; border: 1px solid #f5c6cb; }
        .otp-info { background-color: #e2e3e5; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">🔐 OTP Verification Workflow Test</h3>
                    </div>
                    <div class="card-body">
                        
                        <h5>📋 Test Company Information</h5>
                        <ul>
                            <li><strong>Email:</strong> <?= htmlspecialchars($test_company_email) ?></li>
                            <li><strong>Name:</strong> <?= htmlspecialchars($test_company_name) ?></li>
                        </ul>

                        <h5>📊 Current Status</h5>
                        <div class="status-card status-<?= $company_status['status'] ?>">
                            <h6>Status: <span class="badge bg-<?= 
                                $company_status['status'] === 'approved' ? 'success' : 
                                ($company_status['status'] === 'pending' ? 'warning' : 'danger') 
                            ?>"><?= strtoupper($company_status['status']) ?></span></h6>
                            
                            <?php if ($company_status['status'] === 'approved'): ?>
                                <?php if ($company_status['has_valid_otp']): ?>
                                    <div class="otp-info">
                                        <strong>✅ OTP Available:</strong> <?= htmlspecialchars($company_status['otp']) ?><br>
                                        <strong>⏰ Expires:</strong> <?= htmlspecialchars($company_status['otp_expires_at']) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <strong>ℹ️ Company Approved:</strong> No valid OTP found. Contact admin for new OTP.
                                    </div>
                                <?php endif; ?>
                            <?php elseif ($company_status['status'] === 'pending'): ?>
                                <div class="alert alert-warning">
                                    <strong>⏳ Pending Approval:</strong> Waiting for admin to approve registration.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger">
                                    <strong>❌ Rejected:</strong> Registration has been rejected by admin.
                                </div>
                            <?php endif; ?>
                        </div>

                        <h5>🚀 Test Workflow</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>1. Company Registration</h6>
                                    </div>
                                    <div class="card-body">
                                        <p>Company fills registration form and submits.</p>
                                        <a href="company_register.php" class="btn btn-primary btn-sm">Go to Registration</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>2. Admin Approval</h6>
                                    </div>
                                    <div class="card-body">
                                        <p>Admin reviews and approves company registration.</p>
                                        <a href="admin_company_approvals.php" class="btn btn-success btn-sm">Admin Panel</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>3. OTP Verification</h6>
                                    </div>
                                    <div class="card-body">
                                        <p>Company enters OTP to verify email.</p>
                                        <button class="btn btn-info btn-sm" onclick="testOtpVerification()">Test OTP Check</button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6>4. Profile Creation</h6>
                                    </div>
                                    <div class="card-body">
                                        <p>Company creates profile after verification.</p>
                                        <a href="company_profile_create.php" class="btn btn-warning btn-sm">Create Profile</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>🔧 Technical Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Files Created/Modified:</h6>
                                    <ul>
                                        <li><code>verify_company_otp.php</code> - OTP verification</li>
                                        <li><code>check_company_approval.php</code> - Status checking</li>
                                        <li><code>company_register.php</code> - Enhanced with OTP modal</li>
                                        <li><code>admin_company_approvals.php</code> - OTP generation</li>
                                        <li><code>company_profile_create.php</code> - Session handling</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Database Fields Used:</h6>
                                    <ul>
                                        <li><code>companies.otp</code> - Stores OTP code</li>
                                        <li><code>companies.otp_expires_at</code> - OTP expiry time</li>
                                        <li><code>companies.status</code> - Approval status</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testOtpVerification() {
            const email = '<?= $test_company_email ?>';
            
            fetch('check_company_approval.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                alert(`Status: ${data.status}\nApproved: ${data.approved}\nHas OTP: ${data.hasOtp}\nMessage: ${data.message}`);
            })
            .catch(error => {
                alert('Error testing OTP verification: ' + error);
            });
        }
    </script>
</body>
</html> 