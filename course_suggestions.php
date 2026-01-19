<?php
session_start();
require_once 'db.php';

// Skill-building plans
$skill_building_plans = [
    "Python" => [
        "courses" => ["Intro to Python Programming", "Advanced Python Techniques"],
        "certifications" => ["Python Institute Certified Entry-Level Python Programmer"],
        "projects" => ["Build a web scraper", "Create a Flask web app"],
        "milestones" => ["Complete basic syntax", "Build first project", "Get certification"]
    ],
    "Data Science" => [
        "courses" => ["Data Science Fundamentals", "Machine Learning Basics"],
        "certifications" => ["IBM Data Science Professional Certificate"],
        "projects" => ["Analyze a dataset", "Build predictive model"],
        "milestones" => ["Learn pandas and numpy", "Complete ML project", "Earn certification"]
    ],
    "Java" => [
        "courses" => [
            "Java Programming for Beginners",
            "Object-Oriented Programming in Java",
            "Java Programming Basics",
            "Advanced Java Concepts"
        ],
        "certifications" => ["Oracle Certified Professional Java SE"],
        "projects" => ["Develop a Java desktop app", "Create a REST API with Spring Boot"],
        "milestones" => ["Understand OOP concepts", "Build Java projects", "Pass certification exam"]
    ],
    "Javascript" => [
        "courses" => ["JavaScript Fundamentals", "Advanced JavaScript", "ES6+ Features"],
        "certifications" => ["JavaScript Developer Certificate"],
        "projects" => ["Build a interactive web app", "Create a Node.js API"],
        "milestones" => ["Master DOM manipulation", "Learn async programming", "Build full-stack app"]
    ],
    "React" => [
        "courses" => ["React Basics", "Advanced React Patterns", "React with Redux"],
        "certifications" => ["React Developer Certification"],
        "projects" => ["Build a SPA", "Create a React Native mobile app"],
        "milestones" => ["Learn component lifecycle", "Master state management", "Deploy production app"]
    ]
];

// Course prices (Taka)
$course_prices = [
    "Intro to Python Programming" => 5000,
    "Advanced Python Techniques" => 7000,
    "Data Science Fundamentals" => 6000,
    "Machine Learning Basics" => 8000,
    "Java Programming for Beginners" => 4500,
    "Object-Oriented Programming in Java" => 5500,
    "Java Programming Basics" => 4000,
    "Advanced Java Concepts" => 7500,
    "JavaScript Fundamentals" => 4800,
    "Advanced JavaScript" => 6200,
    "ES6+ Features" => 5500,
    "React Basics" => 5800,
    "Advanced React Patterns" => 7200,
    "React with Redux" => 6500
];

// Related skills map
$related_skills_map = [
    "Python" => ["Data Science", "Machine Learning", "Flask", "Web Development"],
    "Data Science" => ["Python", "Machine Learning", "Deep Learning", "SQL"],
    "Java" => ["OOP", "Spring Framework", "Android"],
    "Javascript" => ["React", "Node.js", "Web Development"],
    "React" => ["Javascript", "Next.js", "Frontend"]
];

// helper: find skill for a given course name
function find_skill_for_course($course, $skill_building_plans) {
    foreach ($skill_building_plans as $skill => $details) {
        if (!empty($details['courses'])) {
            foreach ($details['courses'] as $c) {
                if (strcasecmp($c, $course) === 0) {
                    return $skill;
                }
            }
        }
    }
    return null;
}

// Get requested career/skill
$career = isset($_GET['career']) ? trim($_GET['career']) : '';
$display_skill = '';
$all_courses = [];

if (!empty($career)) {
    $career_key = ucfirst(strtolower($career));

    if (isset($skill_building_plans[$career_key])) {
        $all_courses = $skill_building_plans[$career_key]['courses'];
        $display_skill = $career_key;

        if (isset($related_skills_map[$career_key])) {
            foreach ($related_skills_map[$career_key] as $related_skill) {
                if (isset($skill_building_plans[$related_skill])) {
                    $all_courses = array_merge($all_courses, $skill_building_plans[$related_skill]['courses']);
                }
            }
        }
    } else {
        foreach ($skill_building_plans as $skill => $details) {
            foreach ($details['courses'] as $course) {
                if (strcasecmp($course, $career) === 0) {
                    $all_courses = [$course];
                    $display_skill = $skill;

                    if (isset($related_skills_map[$skill])) {
                        foreach ($related_skills_map[$skill] as $related_skill) {
                            if (isset($skill_building_plans[$related_skill])) {
                                $all_courses = array_merge($all_courses, $skill_building_plans[$related_skill]['courses']);
                            }
                        }
                    }
                    break 2;
                }
            }
        }
    }
}

$all_courses = array_values(array_unique($all_courses));

// default fallback pathway steps
$default_pathway = [
    "Complete course lessons/modules",
    "Build a mini-project for practice",
    "Create a final project to showcase skills",
    "Document your work (GitHub/Portfolio)",
    "Pursue certification (optional)"
];



// ... your existing skill-building plans and course data ...

// SSLCommerz configuration (Updated with WORKING sandbox credentials)
$sslcommerz_store_id = "testbox";
$sslcommerz_store_password = "qwerty";
$sslcommerz_api_url = "https://sandbox.sslcommerz.com/gwprocess/v4/api.php";

// Get base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'];
$current_path = dirname($_SERVER['PHP_SELF']);
$base_url = rtrim($base_url . $current_path, '/');

// Handle payment initiation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['initiate_sslcommerz'])) {
    $course_name = $_POST['course_name'];
    $course_price = $_POST['course_price'];
    $student_name = $_POST['student_name'];
    $student_email = $_POST['student_email'];
    $student_phone = $_POST['student_phone'];
    $student_address = $_POST['student_address'];
    
    // Generate unique transaction ID
    $tran_id = "FTR" . time() . rand(1000, 9999);
    
    // SSLCommerz payment parameters
    $post_data = array(
        'store_id' => $sslcommerz_store_id,
        'store_passwd' => $sslcommerz_store_password,
        'total_amount' => $course_price,
        'currency' => "BDT",
        'tran_id' => $tran_id,
        'success_url' => $base_url . "/payment_success.php",
        'fail_url' => $base_url . "/payment_fail.php", 
        'cancel_url' => $base_url . "/payment_cancel.php",
        'ipn_url' => $base_url . "/payment_ipn.php",
        
        // Customer information
        'cus_name' => $student_name,
        'cus_email' => $student_email,
        'cus_add1' => $student_address,
        'cus_city' => "Dhaka",
        'cus_postcode' => "1200",
        'cus_country' => "Bangladesh",
        'cus_phone' => $student_phone,
        
        // Product information
        'product_name' => $course_name,
        'product_category' => "Education",
        'product_profile' => "non-physical-goods",
        
        // Shipping information
        'shipping_method' => "NO",
        'num_of_item' => 1,
        
        // Additional parameters
        'value_a' => $course_name,
        'value_b' => session_id()
    );
    
    // Initialize SSLCommerz
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $sslcommerz_api_url);
    curl_setopt($handle, CURLOPT_TIMEOUT, 30);
    curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($handle, CURLOPT_POST, 1);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    
    $content = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    
    if ($code == 200 && !(curl_errno($handle))) {
        curl_close($handle);
        $sslcommerzResponse = json_decode($content, true);
        
        if (isset($sslcommerzResponse['status']) && $sslcommerzResponse['status'] == 'SUCCESS') {
            // Store transaction data in session
            $_SESSION['sslcommerz_data'] = [
                'tran_id' => $tran_id,
                'course_name' => $course_name,
                'course_price' => $course_price,
                'student_name' => $student_name,
                'student_email' => $student_email,
                'student_phone' => $student_phone
            ];
            
            // Store in database as pending
            try {
                $stmt = $pdo->prepare("INSERT INTO enrollments (transaction_id, course_name, student_name, student_email, student_phone, amount, payment_status, payment_method, payment_date) VALUES (?, ?, ?, ?, ?, ?, 'pending', 'SSLCommerz', NOW())");
                $stmt->execute([
                    $tran_id,
                    $course_name,
                    $student_name,
                    $student_email,
                    $student_phone,
                    $course_price
                ]);
            } catch (PDOException $e) {
                // Log error but continue
                error_log("Database error: " . $e->getMessage());
            }
            
            // Redirect to SSLCommerz payment page
            header("Location: " . $sslcommerzResponse['GatewayPageURL']);
            exit();
        } else {
            $sslcommerz_error = $sslcommerzResponse['failedreason'] ?? 'Unknown error occurred from SSLCommerz';
            // Debug output
            error_log("SSLCommerz Error: " . $sslcommerz_error);
            error_log("SSLCommerz Response: " . print_r($sslcommerzResponse, true));
        }
    } else {
        $curl_error = curl_error($handle);
        curl_close($handle);
        $sslcommerz_error = "Failed to connect to SSLCommerz. cURL Error: " . $curl_error;
        error_log("cURL Error: " . $curl_error);
    }
    
    // If we get here, there was an error
    $_SESSION['payment_error'] = $sslcommerz_error ?? 'Unknown payment error';
    header("Location: " . $base_url . "/course_suggestions.php?career=" . urlencode($career ?? ''));
    exit();
}

// Display payment error if exists
if (isset($_SESSION['payment_error'])) {
    echo '<div style="background: #ffebee; color: #c62828; padding: 15px; margin: 20px; border-radius: 8px; border: 1px solid #ffcdd2; text-align: center;">
            <strong>Payment Error:</strong> ' . htmlspecialchars($_SESSION['payment_error']) . '<br>
            <small>Using SSLCommerz Sandbox - Test Mode</small>
          </div>';
    unset($_SESSION['payment_error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Course Suggestions - FutureBot AI Career Path Advisor</title>
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
        background: rgba(67, 97, 238, 0.05);
        animation: float 15s infinite ease-in-out;
    }

    .circle:nth-child(1) {
        width: 80px;
        height: 80px;
        top: 10%;
        left: 10%;
        animation-delay: 0s;
    }

    .circle:nth-child(2) {
        width: 120px;
        height: 120px;
        top: 70%;
        left: 80%;
        animation-delay: 2s;
    }

    .circle:nth-child(3) {
        width: 60px;
        height: 60px;
        top: 40%;
        left: 85%;
        animation-delay: 4s;
    }

    .circle:nth-child(4) {
        width: 100px;
        height: 100px;
        top: 80%;
        left: 15%;
        animation-delay: 6s;
    }

    .circle:nth-child(5) {
        width: 70px;
        height: 70px;
        top: 20%;
        left: 70%;
        animation-delay: 8s;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0) translateX(0);
        }
        25% {
            transform: translateY(-20px) translateX(10px);
        }
        50% {
            transform: translateY(10px) translateX(-15px);
        }
        75% {
            transform: translateY(-15px) translateX(-10px);
        }
    }

    nav {
        width: 100%;
        padding: 15px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.95);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        position: fixed;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid rgba(67, 97, 238, 0.1);
    }
    nav .logo {
        font-size: 1.8rem;
        font-weight: bold;
        letter-spacing: 1px;
        background: linear-gradient(90deg, #4361ee, #3a0ca3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    nav .logo i {
        font-size: 1.5rem;
    }
    nav .nav-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    nav .nav-buttons button {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }
    nav .nav-buttons button:hover {
        background: linear-gradient(135deg, #3a0ca3, #4361ee);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
    }

    .main-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        margin-top: 100px;
        padding: 40px 20px;
        flex: 1;
    }

    .page-header {
        text-align: center;
        margin-bottom: 40px;
        width: 100%;
    }

    .page-header h2 {
        font-size: 2.5rem;
        color: #2c3e50;
        font-weight: 800;
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
    }

    .page-header h2::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: linear-gradient(90deg, #4361ee, #3a0ca3);
        border-radius: 2px;
    }

    .page-header p {
        font-size: 1.2rem;
        color: #5a6c7d;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .career-highlight {
        color: #4361ee;
        font-weight: 700;
    }

    .courses-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .course-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.4s ease;
        border: 1px solid rgba(67, 97, 238, 0.1);
        position: relative;
        animation: slideUp 0.8s ease-out;
    }

    .course-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #4361ee, #3a0ca3);
    }

    .course-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(67, 97, 238, 0.15);
    }

    .course-header {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        color: white;
        padding: 20px;
        position: relative;
    }

    .course-header h3 {
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.4;
    }

    .course-body {
        padding: 25px;
    }

    .course-skill {
        display: inline-block;
        background: rgba(67, 97, 238, 0.1);
        color: #4361ee;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .course-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .course-price i {
        color: #4361ee;
    }

    .pathway-section {
        margin-top: 20px;
    }

    .pathway-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pathway-title i {
        color: #4361ee;
    }

    .pathway {
        list-style: none;
        padding-left: 0;
    }

    .pathway li {
        padding: 8px 0;
        color: #5a6c7d;
        position: relative;
        padding-left: 30px;
        font-size: 0.95rem;
        line-height: 1.5;
        border-bottom: 1px solid rgba(67, 97, 238, 0.1);
    }

    .pathway li:last-child {
        border-bottom: none;
    }

    .pathway li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #4361ee;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .course-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid rgba(67, 97, 238, 0.1);
    }

    .btn-enroll {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        font-size: 0.95rem;
    }

    .btn-enroll:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }

    .course-duration {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #5a6c7d;
        font-size: 0.9rem;
    }

    .no-courses {
        background: #fff;
        padding: 60px 40px;
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        text-align: center;
        max-width: 600px;
        margin: 40px auto;
        border: 1px solid rgba(67, 97, 238, 0.1);
        position: relative;
    }

    .no-courses::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, #4361ee, #3a0ca3);
    }

    .no-courses h3 {
        color: #2c3e50;
        margin-bottom: 15px;
        font-size: 1.5rem;
    }

    .no-courses p {
        color: #5a6c7d;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .btn-back {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        color: #fff;
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

  /* Enhanced Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(5px);
    }
    .modal-content {
        background: #fff;
        color: #2c3e50;
        padding: 0;
        border-radius: 16px;
        width: 90%;
        max-width: 700px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        overflow: hidden;
        animation: modalSlideIn 0.4s ease;
        position: relative;
        display: flex;
        flex-direction: column;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        color: white;
        padding: 25px 30px;
        position: relative;
    }

    .modal-header h3 {
        font-size: 1.6rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modal-header h3 i {
        font-size: 1.4rem;
    }

    .close-modal {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        color: white;
        font-size: 1.2rem;
    }

    .close-modal:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 30px;
        flex: 1;
    }

    .course-info {
        display: flex;
        gap: 25px;
        margin-bottom: 25px;
        padding-bottom: 25px;
        border-bottom: 1px solid rgba(67, 97, 238, 0.1);
    }

    .course-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        flex-shrink: 0;
    }

    .course-details {
        flex: 1;
    }

    .course-details h4 {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #2c3e50;
    }

    .course-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #5a6c7d;
        font-size: 0.9rem;
    }

    .meta-item i {
        color: #4361ee;
    }

    .course-description {
        color: #5a6c7d;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .payment-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .payment-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: #2c3e50;
    }

    .payment-title i {
        color: #4361ee;
    }

    .payment-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .payment-amount {
        font-size: 1.8rem;
        font-weight: 800;
        color: #2c3e50;
    }

    .payment-currency {
        font-size: 1rem;
        color: #5a6c7d;
    }

    .payment-methods {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .payment-method {
        flex: 1;
        min-width: 120px;
        border: 2px solid rgba(67, 97, 238, 0.2);
        border-radius: 10px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .payment-method.active {
        border-color: #4361ee;
        background: rgba(67, 97, 238, 0.05);
    }

    .payment-method:hover {
        border-color: #4361ee;
        transform: translateY(-3px);
    }

    .payment-method-icon {
        font-size: 2rem;
        margin-bottom: 5px;
    }

    .payment-method-name {
        font-weight: 600;
        color: #2c3e50;
    }

    .payment-form {
        margin-top: 20px;
        display: none;
    }

    .payment-form.active {
        display: block;
        animation: fadeIn 0.5s ease;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #2c3e50;
    }

    .form-group input, .form-group select, .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        outline: none;
    }

    .form-row {
        display: flex;
        gap: 15px;
    }

    .form-row .form-group {
        flex: 1;
    }

    .payment-instructions {
        background: #e8f4fd;
        border-left: 4px solid #4361ee;
        padding: 15px;
        border-radius: 4px;
        margin-top: 20px;
        font-size: 0.9rem;
        color: #2c3e50;
    }

    .payment-instructions ol {
        margin-left: 20px;
        margin-top: 10px;
    }

    .payment-instructions li {
        margin-bottom: 8px;
    }

    .sslcommerz-info {
        background: linear-gradient(135deg, #4CAF50, #45a049);
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
        text-align: center;
    }

    .sslcommerz-info i {
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .sslcommerz-info h4 {
        margin: 10px 0 5px 0;
        font-size: 1.2rem;
    }

    .terms-section {
        margin-bottom: 25px;
    }

    .terms-checkbox {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 15px;
    }

    .terms-checkbox input[type="checkbox"] {
        margin-top: 3px;
        accent-color: #4361ee;
    }

    .terms-label {
        font-size: 0.9rem;
        color: #5a6c7d;
        line-height: 1.5;
    }

    .terms-label a {
        color: #4361ee;
        text-decoration: none;
        font-weight: 600;
    }

    .terms-label a:hover {
        text-decoration: underline;
    }

    .modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 30px;
        background: #f8f9fa;
        border-top: 1px solid rgba(67, 97, 238, 0.1);
    }

    .btn-cancel {
        background: transparent;
        border: 1px solid #5a6c7d;
        padding: 12px 24px;
        border-radius: 8px;
        color: #5a6c7d;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-cancel:hover {
        background: rgba(90, 108, 125, 0.1);
        transform: translateY(-2px);
    }

    .btn-confirm {
        background: linear-gradient(135deg, #4361ee, #3a0ca3);
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
    }

    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
    }

    .btn-confirm:disabled {
        background: #bdc3c7;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-sslcommerz {
        background: linear-gradient(135deg, #4CAF50, #45a049);
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        width: 100%;
        justify-content: center;
        margin-top: 15px;
    }

    .btn-sslcommerz:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }

    /* Success Modal */
    .success-modal .modal-header {
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
    }

    .success-content {
        text-align: center;
        padding: 40px 30px;
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        margin: 0 auto 25px;
    }

    .success-content h3 {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #2c3e50;
    }

    .success-content p {
        color: #5a6c7d;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .success-details {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        text-align: left;
    }

    .success-details h4 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 15px;
        color: #2c3e50;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .success-details h4 i {
        color: #4CAF50;
    }

    .success-details p {
        margin-bottom: 8px;
        color: #5a6c7d;
    }

    .success-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 25px;
    }

    .btn-dashboard {
        background: linear-gradient(135deg, #4CAF50, #2E7D32);
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    .btn-dashboard:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }

    /* Animations */
    @keyframes slideUp { 
        from { 
            opacity: 0; 
            transform: translateY(30px); 
        } 
        to { 
            opacity: 1; 
            transform: translateY(0); 
        } 
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    /* Payment Method Specific Colors */
    .bkash { color: #E2136E; }
    .nagad { color: #F12C14; }
    .card { color: #4361ee; }
    .sslcommerz { color: #4CAF50; }

    /* Responsive adjustments for modal */
    @media (max-width: 768px) {
        .modal-content {
            width: 95%;
            max-height: 95vh;
        }
        
        .course-info {
            flex-direction: column;
            gap: 15px;
        }
        
        .course-icon {
            align-self: center;
        }
        
        .payment-methods {
            flex-direction: column;
        }
        
        .form-row {
            flex-direction: column;
            gap: 0;
        }
        
        .modal-footer {
            flex-direction: column;
            gap: 15px;
        }
        
        .btn-cancel, .btn-confirm {
            width: 100%;
            justify-content: center;
        }
        
        .success-actions {
            flex-direction: column;
        }
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .courses-grid {
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 25px;
        }
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 30px 15px;
            margin-top: 80px;
        }
        
        .page-header h2 {
            font-size: 2rem;
        }
        
        .courses-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .course-body {
            padding: 20px;
        }
        
        nav {
            padding: 15px 20px;
        }
    }

    @media (max-width: 500px) {
        .page-header h2 {
            font-size: 1.8rem;
        }
        
        .page-header p {
            font-size: 1.1rem;
        }
        
        .course-header {
            padding: 15px;
        }
        
        .course-header h3 {
            font-size: 1.2rem;
        }
        
        .course-actions {
            flex-direction: column;
            gap: 15px;
            align-items: stretch;
        }
        
        .btn-enroll {
            justify-content: center;
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

<!-- Navbar -->
<nav>
    <div class="logo">
        <i class="fas fa-robot"></i>FutureBot
    </div>
    <div class="nav-buttons">
        <button onclick="location.href='career_suggestions.php'">
            <i class="fas fa-arrow-left"></i> Back to Suggestions
        </button>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <h2>Recommended Courses</h2>
        <p>We've curated these courses specifically for your <span class="career-highlight"><?= htmlspecialchars($career) ?></span> career path</p>
    </div>

    <?php if (!empty($all_courses)): ?>
        <div class="courses-grid">
            <?php foreach ($all_courses as $index => $course): 
                $origin_skill = find_skill_for_course($course, $skill_building_plans);
                $pathway_steps = $default_pathway;
                if ($origin_skill && !empty($skill_building_plans[$origin_skill]['milestones'])) {
                    $pathway_steps = $skill_building_plans[$origin_skill]['milestones'];
                }
                $price = isset($course_prices[$course]) ? $course_prices[$course] . " Taka" : "Free";
                $duration = "6-8 weeks"; // Default duration
                $icon = "fa-code"; // Default icon
                
                // Set appropriate icon based on course type
                if (stripos($course, 'python') !== false) $icon = "fa-python";
                elseif (stripos($course, 'java') !== false) $icon = "fa-coffee";
                elseif (stripos($course, 'javascript') !== false || stripos($course, 'react') !== false) $icon = "fa-js-square";
                elseif (stripos($course, 'data') !== false) $icon = "fa-database";
                elseif (stripos($course, 'machine') !== false) $icon = "fa-brain";
            ?>
                <div class="course-card" style="animation-delay: <?= ($index * 0.1) ?>s;">
                    <div class="course-header">
                        <h3><?= htmlspecialchars($course) ?></h3>
                    </div>
                    <div class="course-body">
                        <div class="course-skill">
                            <i class="fas fa-tag"></i> <?= htmlspecialchars($origin_skill ?: ($display_skill ?: 'General Skills')) ?>
                        </div>
                        
                        <div class="course-price">
                            <i class="fas fa-tag"></i> <?= $price ?>
                        </div>
                        
                        <div class="pathway-section">
                            <div class="pathway-title">
                                <i class="fas fa-road"></i> Learning Pathway
                            </div>
                            <ul class="pathway">
                                <?php foreach ($pathway_steps as $step_index => $step): ?>
                                    <li><?= htmlspecialchars($step) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <div class="course-actions">
                            <div class="course-duration">
                                <i class="far fa-clock"></i> <?= $duration ?>
                            </div>
                            <button class="btn-enroll" onclick="confirmEnroll('<?= urlencode($course) ?>', '<?= htmlspecialchars($course) ?>', '<?= $price ?>', '<?= $duration ?>', '<?= $origin_skill ?: ($display_skill ?: 'General Skills') ?>', '<?= $icon ?>')">
                                <i class="fas fa-play-circle"></i> Enroll Now
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-courses">
            <h3>No Courses Found</h3>
            <p>We couldn't find any courses specifically for "<strong><?= htmlspecialchars($career) ?></strong>".</p>
            <p>This might be because the career path is very specialized or we're still expanding our course catalog.</p>
            <a href="career_suggestions.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Career Suggestions
            </a>
        </div>
    <?php endif; ?>
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
            <a href="career_suggestions.php">Career Suggestions</a>
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

<!-- Enrollment Modal -->
<div class="modal" id="enrollModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-graduation-cap"></i> Confirm Enrollment</h3>
            <button class="close-modal" onclick="closeModal('enrollModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="course-info">
                <div class="course-icon">
                    <i class="fab" id="courseIcon"></i>
                </div>
                <div class="course-details">
                    <h4 id="courseName"></h4>
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            <span id="courseSkill"></span>
                        </div>
                        <div class="meta-item">
                            <i class="far fa-clock"></i>
                            <span id="courseDuration"></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-signal"></i>
                            <span>Beginner to Intermediate</span>
                        </div>
                    </div>
                    <p class="course-description" id="courseDescription">This comprehensive course will help you build foundational skills and advance your career in this field. You'll get access to all course materials, projects, and community support.</p>
                </div>
            </div>
            
            <div class="payment-section">
                <div class="payment-title">
                    <i class="fas fa-credit-card"></i> Payment Details
                </div>
                <div class="payment-details">
                    <div>
                        <div class="payment-amount" id="coursePrice"></div>
                        <div class="payment-currency">Total Amount</div>
                    </div>
                    <div>
                        <div class="payment-amount" id="discountPrice" style="color: #4CAF50;"></div>
                        <div class="payment-currency">After Discount</div>
                    </div>
                </div>
                
                <div class="payment-title" style="margin-top: 20px;">
                    <i class="fas fa-wallet"></i> Select Payment Method
                </div>
                <div class="payment-methods">
                    <div class="payment-method active" data-method="bkash">
                        <div class="payment-method-icon bkash">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="payment-method-name bkash">bKash</div>
                    </div>
                    <div class="payment-method" data-method="nagad">
                        <div class="payment-method-icon nagad">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="payment-method-name nagad">Nagad</div>
                    </div>
                    <div class="payment-method" data-method="card">
                        <div class="payment-method-icon card">
                            <i class="far fa-credit-card"></i>
                        </div>
                        <div class="payment-method-name card">Card</div>
                    </div>
                    <div class="payment-method" data-method="sslcommerz">
                        <div class="payment-method-icon sslcommerz">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="payment-method-name sslcommerz">SSLCommerz</div>
                    </div>
                </div>
                
                <!-- bKash Payment Form -->
                <div class="payment-form" id="bkashForm">
                    <div class="form-group">
                        <label for="bkashPhone">bKash Phone Number</label>
                        <input type="text" id="bkashPhone" placeholder="01XXXXXXXXX" maxlength="11">
                    </div>
                    <div class="form-group">
                        <label for="bkashPin">bKash PIN</label>
                        <input type="password" id="bkashPin" placeholder="Enter your bKash PIN" maxlength="4">
                    </div>
                    <div class="payment-instructions">
                        <p><strong>How to pay with bKash:</strong></p>
                        <ol>
                            <li>Go to your bKash Mobile Menu</li>
                            <li>Select "Send Money"</li>
                            <li>Enter our bKash Number: <strong>017XX-XXXXXX</strong></li>
                            <li>Enter the amount: <span id="bkashAmount"></span></li>
                            <li>Enter your bKash PIN to confirm</li>
                        </ol>
                    </div>
                </div>
                
                <!-- Nagad Payment Form -->
                <div class="payment-form" id="nagadForm">
                    <div class="form-group">
                        <label for="nagadPhone">Nagad Phone Number</label>
                        <input type="text" id="nagadPhone" placeholder="01XXXXXXXXX" maxlength="11">
                    </div>
                    <div class="form-group">
                        <label for="nagadPin">Nagad PIN</label>
                        <input type="password" id="nagadPin" placeholder="Enter your Nagad PIN" maxlength="4">
                    </div>
                    <div class="payment-instructions">
                        <p><strong>How to pay with Nagad:</strong></p>
                        <ol>
                            <li>Go to your Nagad Mobile Menu</li>
                            <li>Select "Send Money"</li>
                            <li>Enter our Nagad Number: <strong>017XX-XXXXXX</strong></li>
                            <li>Enter the amount: <span id="nagadAmount"></span></li>
                            <li>Enter your Nagad PIN to confirm</li>
                        </ol>
                    </div>
                </div>
                
                <!-- Card Payment Form -->
                <div class="payment-form" id="cardForm">
                    <div class="form-group">
                        <label for="cardNumber">Card Number</label>
                        <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiryDate">Expiry Date</label>
                            <input type="text" id="expiryDate" placeholder="MM/YY" maxlength="5">
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" placeholder="123" maxlength="3">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="cardName">Name on Card</label>
                        <input type="text" id="cardName" placeholder="John Doe">
                    </div>
                </div>
                
                <!-- SSLCommerz Payment Form -->
                <div class="payment-form" id="sslcommerzForm">
                    <div class="sslcommerz-info">
                        <i class="fas fa-shield-alt"></i>
                        <h4>Secure Payment Gateway</h4>
                        <p>You will be redirected to SSLCommerz for secure payment processing</p>
                    </div>
                    
                    <form id="sslcommerzPaymentForm" method="POST">
                        <input type="hidden" name="initiate_sslcommerz" value="1">
                        <input type="hidden" name="course_name" id="sslcommerzCourseName">
                        <input type="hidden" name="course_price" id="sslcommerzCoursePrice">
                        
                        <div class="form-group">
                            <label for="studentName">Full Name</label>
                            <input type="text" id="studentName" name="student_name" placeholder="Enter your full name" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="studentEmail">Email Address</label>
                                <input type="email" id="studentEmail" name="student_email" placeholder="Enter your email" required>
                            </div>
                            <div class="form-group">
                                <label for="studentPhone">Phone Number</label>
                                <input type="text" id="studentPhone" name="student_phone" placeholder="01XXXXXXXXX" maxlength="11" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="studentAddress">Address</label>
                            <textarea id="studentAddress" name="student_address" placeholder="Enter your address" rows="3" required></textarea>
                        </div>
                        
                        <div class="payment-instructions">
                            <p><strong>SSLCommerz Payment Process:</strong></p>
                            <ol>
                                <li>Fill in your personal details above</li>
                                <li>Click "Proceed to SSLCommerz" button</li>
                                <li>You will be redirected to SSLCommerz secure payment page</li>
                                <li>Choose your preferred payment method (Card/Mobile Banking)</li>
                                <li>Complete the payment process</li>
                                <li>You will be redirected back to FutureBot after successful payment</li>
                            </ol>
                        </div>
                        
                        <button type="submit" class="btn-sslcommerz">
                            <i class="fas fa-lock"></i> Proceed to SSLCommerz
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="terms-section">
                <div class="terms-checkbox">
                    <input type="checkbox" id="termsAgreement" onchange="toggleConfirmButton()">
                    <label for="termsAgreement" class="terms-label">
                        I agree to the <a href="terms.php" target="_blank">Terms of Service</a> and <a href="privacy.php" target="_blank">Privacy Policy</a>. I understand that I will be charged the amount shown above and have 14 days to request a refund if I'm not satisfied.
                    </label>
                </div>
                <div class="terms-checkbox">
                    <input type="checkbox" id="newsletterSubscription">
                    <label for="newsletterSubscription" class="terms-label">
                        Send me course updates, career tips, and special offers from FutureBot.
                    </label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('enrollModal')">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button id="confirmEnrollBtn" class="btn-confirm" disabled>
                <i class="fas fa-lock"></i> Confirm & Pay
            </button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal success-modal" id="successModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-check-circle"></i> Enrollment Successful!</h3>
            <button class="close-modal" onclick="closeModal('successModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="success-content">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h3>Welcome to the Course!</h3>
                <p>You've successfully enrolled in <strong id="successCourseName"></strong>. We're excited to have you join our learning community!</p>
                
                <div class="success-details">
                    <h4><i class="fas fa-info-circle"></i> What's Next?</h4>
                    <p><i class="fas fa-envelope text-primary"></i> Check your email for course access instructions</p>
                    <p><i class="fas fa-calendar-alt text-primary"></i> Course starts immediately - you can begin learning now</p>
                    <p><i class="fas fa-users text-primary"></i> Join our student community for support and networking</p>
                </div>
                
                <div class="success-actions">
                    <button class="btn-cancel" onclick="closeModal('successModal')">
                        <i class="fas fa-times"></i> Close
                    </button>
                    <a href="student_dashboard.php" class="btn-dashboard">
                        <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentCourse = {};
    let selectedPaymentMethod = 'bkash';
    
    function confirmEnroll(courseEncoded, courseName, price, duration, skill, icon) {
        currentCourse = {
            encoded: courseEncoded,
            name: courseName,
            price: price,
            duration: duration,
            skill: skill,
            icon: icon
        };
        
        // Set modal content
        document.getElementById('courseName').textContent = courseName;
        document.getElementById('courseSkill').textContent = skill;
        document.getElementById('courseDuration').textContent = duration;
        document.getElementById('courseIcon').className = `fab ${icon}`;
        
        // Set price information
        document.getElementById('coursePrice').textContent = price;
        
        // Calculate and display discount (simulated)
        const originalPrice = price.replace(' Taka', '');
        let discountPrice = originalPrice;
        if (originalPrice !== 'Free') {
            discountPrice = Math.round(originalPrice * 0.9); // 10% discount
            document.getElementById('discountPrice').textContent = discountPrice + ' Taka';
            
            // Set payment amounts
            document.getElementById('bkashAmount').textContent = discountPrice + ' Taka';
            document.getElementById('nagadAmount').textContent = discountPrice + ' Taka';
            
            // Set SSLCommerz hidden fields
            document.getElementById('sslcommerzCourseName').value = courseName;
            document.getElementById('sslcommerzCoursePrice').value = discountPrice;
        } else {
            document.getElementById('discountPrice').textContent = 'Free';
            document.getElementById('bkashAmount').textContent = 'Free';
            document.getElementById('nagadAmount').textContent = 'Free';
        }
        
        // Reset forms and selections
        resetPaymentForms();
        selectPaymentMethod('bkash');
        
        // Reset checkboxes
        document.getElementById('termsAgreement').checked = false;
        document.getElementById('newsletterSubscription').checked = false;
        document.getElementById('confirmEnrollBtn').disabled = true;
        
        // Show modal
        document.getElementById('enrollModal').style.display = 'flex';
    }
    
    function selectPaymentMethod(method) {
        selectedPaymentMethod = method;
        
        // Update active state of payment methods
        const paymentMethods = document.querySelectorAll('.payment-method');
        paymentMethods.forEach(pm => {
            if (pm.getAttribute('data-method') === method) {
                pm.classList.add('active');
            } else {
                pm.classList.remove('active');
            }
        });
        
        // Show the corresponding form
        const forms = document.querySelectorAll('.payment-form');
        forms.forEach(form => {
            if (form.id === method + 'Form') {
                form.classList.add('active');
            } else {
                form.classList.remove('active');
            }
        });
        
        // For SSLCommerz, we don't need the main confirm button
        if (method === 'sslcommerz') {
            document.getElementById('confirmEnrollBtn').style.display = 'none';
        } else {
            document.getElementById('confirmEnrollBtn').style.display = 'flex';
        }
    }
    
    function resetPaymentForms() {
        // Clear all form fields
        document.getElementById('bkashPhone').value = '';
        document.getElementById('bkashPin').value = '';
        document.getElementById('nagadPhone').value = '';
        document.getElementById('nagadPin').value = '';
        document.getElementById('cardNumber').value = '';
        document.getElementById('expiryDate').value = '';
        document.getElementById('cvv').value = '';
        document.getElementById('cardName').value = '';
        document.getElementById('studentName').value = '';
        document.getElementById('studentEmail').value = '';
        document.getElementById('studentPhone').value = '';
        document.getElementById('studentAddress').value = '';
    }
    
    function toggleConfirmButton() {
        const termsAgreed = document.getElementById('termsAgreement').checked;
        
        // For free courses, just check terms agreement
        if (currentCourse.price === 'Free') {
            document.getElementById('confirmEnrollBtn').disabled = !termsAgreed;
            return;
        }
        
        // For SSLCommerz, we don't need to validate payment details here
        if (selectedPaymentMethod === 'sslcommerz') {
            return;
        }
        
        // For other payment methods, check payment method validation
        let isValid = false;
        
        if (termsAgreed) {
            if (selectedPaymentMethod === 'bkash') {
                const phone = document.getElementById('bkashPhone').value;
                const pin = document.getElementById('bkashPin').value;
                isValid = phone.length === 11 && pin.length === 4;
            } else if (selectedPaymentMethod === 'nagad') {
                const phone = document.getElementById('nagadPhone').value;
                const pin = document.getElementById('nagadPin').value;
                isValid = phone.length === 11 && pin.length === 4;
            } else if (selectedPaymentMethod === 'card') {
                const cardNumber = document.getElementById('cardNumber').value;
                const expiry = document.getElementById('expiryDate').value;
                const cvv = document.getElementById('cvv').value;
                const name = document.getElementById('cardName').value;
                isValid = cardNumber.length >= 16 && expiry.length === 5 && cvv.length === 3 && name.length > 0;
            }
        }
        
        document.getElementById('confirmEnrollBtn').disabled = !isValid;
    }
    
    function processPayment() {
        // In a real application, this would process payment and enrollment
        // For demo purposes, we'll simulate a successful payment
        
        // Show loading state
        const confirmBtn = document.getElementById('confirmEnrollBtn');
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        confirmBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // Close enrollment modal
            closeModal('enrollModal');
            
            // Set success modal content
            document.getElementById('successCourseName').textContent = currentCourse.name;
            
            // Show success modal
            document.getElementById('successModal').style.display = 'flex';
            
            // Reset confirm button
            confirmBtn.innerHTML = '<i class="fas fa-lock"></i> Confirm & Pay';
            confirmBtn.disabled = false;
        }, 3000);
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listener to confirm button
        document.getElementById('confirmEnrollBtn').addEventListener('click', processPayment);
        
        // Add event listeners to payment methods
        const paymentMethods = document.querySelectorAll('.payment-method');
        paymentMethods.forEach(pm => {
            pm.addEventListener('click', function() {
                selectPaymentMethod(this.getAttribute('data-method'));
                toggleConfirmButton();
            });
        });
        
        // Add input validation for payment forms
        const paymentInputs = document.querySelectorAll('.payment-form input');
        paymentInputs.forEach(input => {
            input.addEventListener('input', toggleConfirmButton);
        });
        
        // Format card number input
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = '';
            
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            
            e.target.value = formattedValue;
        });
        
        // Format expiry date input
        document.getElementById('expiryDate').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\//g, '').replace(/[^0-9]/gi, '');
            
            if (value.length >= 2) {
                e.target.value = value.substring(0, 2) + '/' + value.substring(2, 4);
            } else {
                e.target.value = value;
            }
        });
        
        // SSLCommerz form submission
        document.getElementById('sslcommerzPaymentForm').addEventListener('submit', function(e) {
            const termsAgreed = document.getElementById('termsAgreement').checked;
            if (!termsAgreed) {
                e.preventDefault();
                alert('Please agree to the Terms of Service and Privacy Policy before proceeding.');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirecting to SSLCommerz...';
            submitBtn.disabled = true;
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => { 
                if (event.target === modal) modal.style.display = 'none'; 
            });
        };
        
        // Add some interactive effects to course cards
        const cards = document.querySelectorAll('.course-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
</body>
</html>