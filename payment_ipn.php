<?php
require_once 'db.php';

// SSLCommerz IPN (Instant Payment Notification) Handler
// This file receives payment notifications from SSLCommerz

// SSLCommerz configuration
$store_id = "future64b9c6f8b764b";
$store_password = "future64b9c6f8b764b@ssl";

// Receive all the data from SSLCommerz
$received_data = array();
foreach ($_POST as $key => $value) {
    $received_data[$key] = $value;
}

// Verify the IPN request
$is_valid_ipn = false;

if (isset($received_data['verify_key']) && isset($received_data['verify_sign'])) {
    $pre_define_key = explode(',', $received_data['verify_key']);
    
    $new_data = array();
    if (!empty($pre_define_key)) {
        foreach ($pre_define_key as $value) {
            if (isset($received_data[$value])) {
                $new_data[$value] = ($received_data[$value]);
            }
        }
    }
    
    $new_data['store_passwd'] = md5($store_password);
    
    ksort($new_data);
    
    $hash_string = "";
    foreach ($new_data as $key => $value) {
        $hash_string .= $key . '=' . ($value) . '&';
    }
    $hash_string = rtrim($hash_string, '&');
    
    if (md5($hash_string) == $received_data['verify_sign']) {
        $is_valid_ipn = true;
    }
}

if ($is_valid_ipn) {
    $tran_id = $received_data['tran_id'];
    $status = $received_data['status'];
    $val_id = $received_data['val_id'];
    $amount = $received_data['amount'];
    $store_amount = $received_data['store_amount'];
    $card_type = $received_data['card_type'];
    $card_no = $received_data['card_no'];
    $currency = $received_data['currency'];
    $bank_tran_id = $received_data['bank_tran_id'];
    $card_issuer = $received_data['card_issuer'];
    $card_brand = $received_data['card_brand'];
    $card_issuer_country = $received_data['card_issuer_country'];
    $card_issuer_country_code = $received_data['card_issuer_country_code'];
    $currency_type = $received_data['currency_type'];
    $currency_amount = $received_data['currency_amount'];
    
    // Check if transaction exists in database
    $stmt = $pdo->prepare("SELECT * FROM enrollments WHERE transaction_id = ?");
    $stmt->execute([$tran_id]);
    $existing_enrollment = $stmt->fetch();
    
    if ($existing_enrollment) {
        // Update existing enrollment
        $update_stmt = $pdo->prepare("UPDATE enrollments SET 
            payment_status = ?, 
            bank_tran_id = ?, 
            card_type = ?, 
            card_no = ?, 
            payment_date = NOW(),
            ipn_received = 1
            WHERE transaction_id = ?");
        
        $update_stmt->execute([
            $status,
            $bank_tran_id,
            $card_type,
            $card_no,
            $tran_id
        ]);
        
        // Log IPN received
        $log_stmt = $pdo->prepare("INSERT INTO ipn_logs (transaction_id, status, ipn_data, received_at) VALUES (?, ?, ?, NOW())");
        $log_stmt->execute([
            $tran_id,
            $status,
            json_encode($received_data)
        ]);
        
        // Send email notification for successful payments
        if ($status == 'VALID' || $status == 'VALIDATED') {
            // Get enrollment details for email
            $enrollment_stmt = $pdo->prepare("SELECT * FROM enrollments WHERE transaction_id = ?");
            $enrollment_stmt->execute([$tran_id]);
            $enrollment = $enrollment_stmt->fetch();
            
            if ($enrollment) {
                // In a real application, send email here
                // sendCourseAccessEmail($enrollment['student_email'], $enrollment['student_name'], $enrollment['course_name']);
                
                // Log email sent
                $email_log_stmt = $pdo->prepare("INSERT INTO email_logs (transaction_id, email_type, sent_at) VALUES (?, 'course_access', NOW())");
                $email_log_stmt->execute([$tran_id]);
            }
        }
        
        http_response_code(200);
        echo "IPN processed successfully";
    } else {
        // Transaction not found in database
        http_response_code(404);
        echo "Transaction not found";
    }
} else {
    // Invalid IPN request
    http_response_code(400);
    echo "Invalid IPN request";
}

// Function to send course access email (placeholder)
function sendCourseAccessEmail($email, $name, $course) {
    // In a real application, implement email sending logic here
    $subject = "Course Access - FutureBot";
    $message = "
    <html>
    <head>
        <title>Course Access Confirmation</title>
    </head>
    <body>
        <h2>Welcome to FutureBot!</h2>
        <p>Dear $name,</p>
        <p>You have successfully enrolled in <strong>$course</strong>.</p>
        <p>You can now access the course materials from your dashboard.</p>
        <br>
        <p>Best regards,<br>FutureBot Team</p>
    </body>
    </html>
    ";
    
    // Use PHPMailer or similar library to send email
    // mail($email, $subject, $message, "Content-type: text/html; charset=utf-8");
}

// Function to log IPN data for debugging
function logIPNData($data) {
    $log_file = 'ipn_logs.txt';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] " . json_encode($data) . "\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

// Log the IPN request for debugging
logIPNData($_POST);
?>