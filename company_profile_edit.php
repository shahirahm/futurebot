<?php
require_once 'db.php';

// Get company ID safely
$companyId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

if ($companyId <= 0) {
    // Just show message, no link or redirect
    echo "<h2>Invalid company ID.</h2>";
    exit;  // stop execution so rest of page doesn't run without valid ID
}

// Initialize error and success messages
$error = '';
$success = '';

// Fetch company info
$stmt = $conn->prepare("SELECT company_name, start_year, trade_license, email, address FROM companies WHERE id = ?");
$stmt->bind_param("i", $companyId);
$stmt->execute();
$companyResult = $stmt->get_result();
$company = $companyResult->fetch_assoc();
$stmt->close();

if (!$company) {
    die("Company not found.");
}

// Fetch profile info
$stmt = $conn->prepare("SELECT bio, location, facilities, rating, logo_path FROM company_profiles WHERE company_id = ?");
$stmt->bind_param("i", $companyId);
$stmt->execute();
$profileResult = $stmt->get_result();
$profile = $profileResult->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Company data
    $company_name = trim($_POST['company_name'] ?? '');
    $start_year = intval($_POST['start_year'] ?? 0);
    $trade_license = trim($_POST['trade_license'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Profile data
    $bio = trim($_POST['bio'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $facilities = trim($_POST['facilities'] ?? '');
    $rating = floatval($_POST['rating'] ?? 0);

    // Basic validation
    if (!$company_name || !$email) {
        $error = "Company name and email are required.";
    } else {
        // Update companies table
        $stmt = $conn->prepare("UPDATE companies SET company_name = ?, start_year = ?, trade_license = ?, email = ?, address = ? WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sisssi", $company_name, $start_year, $trade_license, $email, $address, $companyId);
        if (!$stmt->execute()) {
            $error = "Failed to update company: " . $stmt->error;
        }
        $stmt->close();

        // Handle logo upload
        $logo_path = $profile['logo_path'] ?? null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileTmpPath = $_FILES['logo']['tmp_name'];
            $fileName = basename($_FILES['logo']['name']);
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($ext, $allowed)) {
                $newFileName = 'logo_' . $companyId . '.' . $ext;
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $logo_path = $destPath;
                } else {
                    $error = "Failed to upload profile picture.";
                }
            } else {
                $error = "Invalid file type for profile picture. Allowed: jpg, jpeg, png, gif.";
            }
        }

        if (!$error) {
            // Check if profile exists
            $stmt = $conn->prepare("SELECT company_id FROM company_profiles WHERE company_id = ?");
            $stmt->bind_param("i", $companyId);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Update existing profile
                $stmt->close();

                if ($logo_path) {
                    $stmt = $conn->prepare("UPDATE company_profiles SET bio = ?, location = ?, facilities = ?, rating = ?, logo_path = ? WHERE company_id = ?");
                    $stmt->bind_param("sssisi", $bio, $location, $facilities, $rating, $logo_path, $companyId);
                } else {
                    $stmt = $conn->prepare("UPDATE company_profiles SET bio = ?, location = ?, facilities = ?, rating = ? WHERE company_id = ?");
                    $stmt->bind_param("sssdi", $bio, $location, $facilities, $rating, $companyId);
                }

                if ($stmt->execute()) {
                    $success = "Profile updated successfully.";
                    // Refresh $profile data
                    $profile['bio'] = $bio;
                    $profile['location'] = $location;
                    $profile['facilities'] = $facilities;
                    $profile['rating'] = $rating;
                    if ($logo_path) {
                        $profile['logo_path'] = $logo_path;
                    }
                } else {
                    $error = "Failed to update profile: " . $stmt->error;
                }
                $stmt->close();
            } else {
                // Insert new profile
                $stmt->close();

                if ($logo_path) {
                    $stmt = $conn->prepare("INSERT INTO company_profiles (company_id, bio, location, facilities, rating, logo_path) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssis", $companyId, $bio, $location, $facilities, $rating, $logo_path);
                } else {
                    $stmt = $conn->prepare("INSERT INTO company_profiles (company_id, bio, location, facilities, rating) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssd", $companyId, $bio, $location, $facilities, $rating);
                }

                if ($stmt->execute()) {
                    $success = "Profile created successfully.";
                } else {
                    $error = "Failed to create profile: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }

    // Update company data in $company array if no error for form re-display
    if (!$error) {
        $company['company_name'] = $company_name;
        $company['start_year'] = $start_year;
        $company['trade_license'] = $trade_license;
        $company['email'] = $email;
        $company['address'] = $address;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Company Profile - <?= htmlspecialchars($company['company_name']) ?> | FutureBot</title>
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

        html {
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--text-dark);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
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

        .navbar {
            background-color: var(--white);
            padding: 1rem 2.5rem;
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            backdrop-filter: blur(10px);
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

        .back-btn {
            background: var(--white);
            color: var(--primary-blue);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            border: 2px solid var(--primary-blue);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
        }

        .back-btn:hover {
            background: var(--primary-blue);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.25);
            text-decoration: none;
        }

        .container {
            max-width: 1000px;
            margin: 2.5rem auto;
            padding: 0 1.25rem;
        }

        .edit-card {
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

        .edit-card::before {
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

        .page-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .page-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.3);
        }

        .page-title {
            color: var(--text-dark);
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        .form-section {
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(67, 97, 238, 0.1);
        }

        .section-title {
            color: var(--dark-blue);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid rgba(67, 97, 238, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--primary-blue);
        }

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

        .required::after {
            content: ' *';
            color: var(--danger);
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

        .form-control::placeholder {
            color: var(--text-light);
            opacity: 0.7;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .file-upload-wrapper {
            position: relative;
        }

        .current-logo {
            margin-bottom: 1rem;
        }

        .profile-logo {
            width: 120px;
            height: 120px;
            border-radius: 16px;
            object-fit: cover;
            border: 4px solid var(--white);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .logo-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 16px;
            background: var(--gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            border: 4px solid var(--white);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.2);
        }

        .file-input-wrapper input[type="file"] {
            padding: 0.875rem 1rem;
            border: 2px dashed rgba(67, 97, 238, 0.2);
            border-radius: 12px;
            background: rgba(67, 97, 238, 0.02);
            transition: all 0.3s ease;
            width: 100%;
        }

        .file-input-wrapper input[type="file"]:hover {
            border-color: var(--primary-blue);
            background: rgba(67, 97, 238, 0.05);
        }

        .file-input-wrapper input[type="file"]::file-selector-button {
            background: var(--gradient);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }

        .file-input-wrapper input[type="file"]::file-selector-button:hover {
            background: var(--gradient-hover);
            transform: translateY(-1px);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
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
            gap: 0.75rem;
            flex: 1;
            justify-content: center;
        }

        .btn-primary:hover {
            background: var(--gradient-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--text-dark);
            border: 2px solid rgba(67, 97, 238, 0.2);
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
            justify-content: center;
        }

        .btn-secondary:hover {
            background: rgba(67, 97, 238, 0.05);
            border-color: var(--primary-blue);
            transform: translateY(-2px);
            text-decoration: none;
            color: var(--text-dark);
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
        }

        .alert-danger {
            background: rgba(230, 57, 70, 0.1);
            color: #dc3545;
            border-left: 4px solid #dc3545;
        }

        .alert-success {
            background: rgba(40, 152, 66, 0.1);
            color: #198754;
            border-left: 4px solid #198754;
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

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--primary-blue);
            transition: width 0.3s ease;
        }

        .footer-links a:hover::after {
            width: 100%;
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

        /* Responsive design */
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem 1.25rem;
            }
            
            .logo-text {
                font-size: 1.2rem;
            }
            
            .container {
                margin: 1.5rem auto;
                padding: 0 1rem;
            }
            
            .edit-card {
                padding: 2rem 1.5rem;
                border-radius: 16px;
            }
            
            .page-title {
                font-size: 1.75rem;
            }

            .page-icon {
                width: 70px;
                height: 70px;
                font-size: 1.75rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.5rem;
            }
            
            .edit-card {
                padding: 1.5rem 1rem;
            }

            .navbar {
                padding: 0.75rem 1rem;
            }

            .logo-icon {
                width: 35px;
                height: 35px;
            }

            .section-title {
                font-size: 1.25rem;
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

    <!-- Navigation -->
    <div class="navbar">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="logo-text">FutureBot</div>
        </div>
        <a href="company_profile.php?id=<?= $companyId ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>

    <div class="container">
        <div class="edit-card">
            <div class="page-header">
                <div class="page-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h1 class="page-title">Edit Company Profile</h1>
                <p class="page-subtitle">Update your company information and profile details</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" novalidate>
                <!-- Company Information Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Company Information
                    </h3>
                    
                    <div class="form-group">
                        <label for="company_name" class="form-label required">Company Name</label>
                        <input type="text" name="company_name" id="company_name" class="form-control" required 
                               placeholder="Enter your company name" 
                               value="<?= htmlspecialchars($company['company_name'] ?? '') ?>" />
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_year" class="form-label">Starting Year</label>
                                <input type="number" name="start_year" id="start_year" class="form-control" 
                                       placeholder="e.g., 2020" min="1900" max="2030"
                                       value="<?= htmlspecialchars($company['start_year'] ?? '') ?>" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="trade_license" class="form-label">Trade License</label>
                                <input type="text" name="trade_license" id="trade_license" class="form-control" 
                                       placeholder="Enter trade license number"
                                       value="<?= htmlspecialchars($company['trade_license'] ?? '') ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label required">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" required 
                               placeholder="company@example.com"
                               value="<?= htmlspecialchars($company['email'] ?? '') ?>" />
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">Address</label>
                        <textarea name="address" id="address" class="form-control" rows="3" 
                                  placeholder="Enter your company's full address"><?= htmlspecialchars($company['address'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Company Profile Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user-circle"></i>
                        Company Profile
                    </h3>

                    <div class="form-group">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea name="bio" id="bio" class="form-control" rows="4" 
                                  placeholder="Describe your company's mission, values, and what makes you unique"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" name="location" id="location" class="form-control" 
                               placeholder="Enter company location"
                               value="<?= htmlspecialchars($profile['location'] ?? '') ?>" />
                    </div>

                    <div class="form-group">
                        <label for="facilities" class="form-label">Facilities</label>
                        <textarea name="facilities" id="facilities" class="form-control" rows="3" 
                                  placeholder="Describe company facilities and amenities"><?= htmlspecialchars($profile['facilities'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="rating" class="form-label">Rating (0 to 5)</label>
                        <input type="number" name="rating" id="rating" class="form-control" 
                               min="0" max="5" step="0.1" 
                               placeholder="Enter rating between 0 and 5"
                               value="<?= htmlspecialchars($profile['rating'] ?? '') ?>" />
                    </div>
                </div>

                <!-- Logo Upload Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-camera"></i>
                        Company Logo
                    </h3>

                    <div class="current-logo">
                        <label class="form-label">Current Logo</label>
                        <div>
                            <?php if (!empty($profile['logo_path']) && file_exists($profile['logo_path'])): ?>
                                <img src="<?= htmlspecialchars($profile['logo_path']) ?>" alt="Current Company Logo" class="profile-logo" />
                            <?php else: ?>
                                <div class="logo-placeholder">
                                    <?= strtoupper(substr($company['company_name'], 0, 2)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="logo" class="form-label">Upload New Logo</label>
                        <div class="file-input-wrapper">
                            <input type="file" name="logo" id="logo" accept=".jpg,.jpeg,.png,.gif" class="form-control" />
                        </div>
                        <small style="color: var(--text-light); font-size: 0.875rem; margin-top: 0.5rem; display: block;">
                            Accepted formats: JPG, JPEG, PNG, GIF. Maximum file size: 5MB.
                        </small>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="company_profile.php?id=<?= $companyId ?>" class="btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
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

    <script>
        // Add interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            // Add loading state to form submission
            form.addEventListener('submit', function() {
                const originalText = submitBtn.innerHTML;
                
                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                submitBtn.disabled = true;
                
                // Re-enable after 5 seconds if form doesn't submit
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });

            // Add focus effects to form inputs
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });

            // Auto-hide success alert
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                setTimeout(() => {
                    successAlert.style.transition = 'opacity 0.5s ease';
                    successAlert.style.opacity = '0';
                    setTimeout(() => successAlert.remove(), 500);
                }, 4000);
            }

            // Prevent horizontal overflow
            function preventHorizontalOverflow() {
                document.body.style.overflowX = 'hidden';
                document.documentElement.style.overflowX = 'hidden';
            }

            // Initialize on load and resize
            preventHorizontalOverflow();
            window.addEventListener('resize', preventHorizontalOverflow);
        });
    </script>
</body>
</html>