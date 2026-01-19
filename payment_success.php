<?php
session_start();
require_once 'db.php';

// Check if SSLCommerz data exists in session
if (!isset($_SESSION['sslcommerz_data'])) {
    header("Location: course_suggestions.php");
    exit();
}

$sslcommerz_data = $_SESSION['sslcommerz_data'];
$tran_id = $sslcommerz_data['tran_id'];
$course_name = $sslcommerz_data['course_name'];
$course_price = $sslcommerz_data['course_price'];
$student_name = $sslcommerz_data['student_name'];
$student_email = $sslcommerz_data['student_email'];

// Validate payment with SSLCommerz
$store_id = "future64b9c6f8b764b";
$store_password = "future64b9c6f8b764b@ssl";
$validation_url = "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php";

if (isset($_POST['tran_id']) && !empty($_POST['tran_id'])) {
    $tran_id = $_POST['tran_id'];
    
    // Validate transaction
    $requested_url = $validation_url . "?val_id=" . $_POST['val_id'] . "&store_id=" . $store_id . "&store_passwd=" . $store_password . "&v=1&format=json";
    
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $requested_url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    
    $result = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    
    if ($code == 200 && !curl_errno($handle)) {
        $result = json_decode($result);
        
        if ($result->status == "VALID" || $result->status == "VALIDATED") {
            // Payment is valid
            $payment_status = 'success';
            
            // Save enrollment to database
            $stmt = $pdo->prepare("INSERT INTO enrollments (transaction_id, course_name, student_name, student_email, amount, payment_status, payment_method, bank_tran_id, card_type, card_no, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $tran_id,
                $course_name,
                $student_name,
                $student_email,
                $course_price,
                'success',
                $result->card_type ?? 'Online Payment',
                $result->bank_tran_id ?? '',
                $result->card_type ?? '',
                $result->card_no ?? '',
            ]);
            
            // Send confirmation email (in a real application)
            // sendConfirmationEmail($student_email, $student_name, $course_name, $tran_id);
            
            // Clear session data
            unset($_SESSION['sslcommerz_data']);
        }
    }
    curl_close($handle);
}

// If no POST data, assume success from session
if (!isset($payment_status)) {
    $payment_status = 'success';
    
    // Save enrollment to database
    $stmt = $pdo->prepare("INSERT INTO enrollments (transaction_id, course_name, student_name, student_email, amount, payment_status, payment_method, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $tran_id,
        $course_name,
        $student_name,
        $student_email,
        $course_price,
        'success',
        'SSLCommerz'
    ]);
    
    // Clear session data
    unset($_SESSION['sslcommerz_data']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payment Successful - FutureBot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    * { 
        box-sizing: border-box; 
        margin:0; 
        padding:0; 
    }
    html, body {
        width: 100%;
        height: 100%;
        overflow-x: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        color: #2c3e50;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .main-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin-top: 100px;
        padding: 40px 20px;
        flex: 1;
        text-align: center;
    }

    .success-container {
        background: #fff;
        padding: 60px 40px;
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        width: 100%;
        border: 1px solid rgba(67, 97, 238, 0.1);
        position: relative;
    }

    .success-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #4CAF50, #2E7D32);
    }

    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        margin: 0 auto 30px;
    }

    .success-container h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .success-container p {
        font-size: 1.2rem;
        color: #5a6c7d;
        line-height: 1.6;
        margin-bottom: 30px;
    }

    .payment-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin: 30px 0;
        text-align: left;
    }

    .payment-details h3 {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .payment-details h3 i {
        color: #4CAF50;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(67, 97, 238, 0.1);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #5a6c7d;
    }

    .detail-value {
        font-weight: 700;
        color: #2c3e50;
    }

    .success-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 1rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        color: white;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }

    .btn-success {
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        color: white;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }

    .btn-outline {
        background: transparent;
        border: 2px solid #4361ee;
        color: #4361ee;
    }

    .btn-outline:hover {
        background: #4361ee;
        color: white;
        transform: translateY(-2px);
    }

    .whats-next {
        background: #e8f4fd;
        border-left: 4px solid #4361ee;
        padding: 20px;
        border-radius: 8px;
        margin-top: 30px;
        text-align: left;
    }

    .whats-next h4 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .whats-next ul {
        list-style: none;
        padding-left: 0;
    }

    .whats-next li {
        padding: 8px 0;
        color: #5a6c7d;
        position: relative;
        padding-left: 30px;
    }

    .whats-next li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #4361ee;
        font-weight: bold;
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .success-container {
            padding: 40px 20px;
        }
        
        .success-container h1 {
            font-size: 2rem;
        }
        
        .success-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
    }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav style="width: 100%; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.95); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); position: fixed; top: 0; z-index: 1000; border-bottom: 1px solid rgba(67, 97, 238, 0.1);">
        <div class="logo" style="font-size: 1.8rem; font-weight: bold; letter-spacing: 1px; background: linear-gradient(90deg, #4361ee, #3a0ca3); -webkit-background-clip: text; -webkit-text-fill-color: transparent; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-robot"></i>FutureBot
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            
            <h1>Payment Successful!</h1>
            <p>Thank you for your payment. You have successfully enrolled in the course.</p>
            
            <div class="payment-details">
                <h3><i class="fas fa-receipt"></i> Payment Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Transaction ID:</span>
                    <span class="detail-value"><?= htmlspecialchars($tran_id) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Course Name:</span>
                    <span class="detail-value"><?= htmlspecialchars($course_name) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Student Name:</span>
                    <span class="detail-value"><?= htmlspecialchars($student_name) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?= htmlspecialchars($student_email) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Amount Paid:</span>
                    <span class="detail-value" style="color: #4CAF50;"><?= htmlspecialchars($course_price) ?> Taka</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Status:</span>
                    <span class="detail-value" style="color: #4CAF50;">Completed</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Date:</span>
                    <span class="detail-value"><?= date('F j, Y g:i A') ?></span>
                </div>
            </div>
            
            <div class="whats-next">
                <h4><i class="fas fa-info-circle"></i> What's Next?</h4>
                <ul>
                    <li>Check your email for course access instructions</li>
                    <li>Course materials will be available immediately</li>
                    <li>Join our student community for support</li>
                    <li>Start learning at your own pace</li>
                </ul>
            </div>
            
            <div class="success-actions">
                <a href="student_dashboard.php" class="btn btn-success">
                    <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                </a>
                <a href="course_suggestions.php" class="btn btn-primary">
                    <i class="fas fa-book"></i> Browse More Courses
                </a>
                <a href="index.php" class="btn btn-outline">
                    <i class="fas fa-home"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer style="width: 100%; background: rgba(255, 255, 255, 0.95); padding: 30px 20px; margin-top: 50px; border-top: 1px solid rgba(67, 97, 238, 0.1); box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-direction: column; align-items: center; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 10px; font-size: 1.5rem; font-weight: bold; background: linear-gradient(90deg, #4361ee, #3a0ca3); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                <i class="fas fa-robot"></i>FutureBot
            </div>
            <div style="text-align: center; padding-top: 20px; border-top: 1px solid rgba(67, 97, 238, 0.1); width: 100%; color: #7f8c8d; font-size: 0.9rem;">
                <p>&copy; 2024 FutureBot. All rights reserved. | Empowering students with AI-driven career guidance</p>
            </div>
        </div>
    </footer>
</body>
</html>