<?php
require_once 'db.php';

$error = '';
$success = '';
$companyId = null;
$logo_path = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Company data ---
    $company_name = trim($_POST['company_name'] ?? '');
    $start_year = intval($_POST['start_year'] ?? 0);
    $trade_license = trim($_POST['trade_license'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $address = trim($_POST['address'] ?? '');

    // --- Profile data ---
    $bio = trim($_POST['bio'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $facilities = trim($_POST['facilities'] ?? '');
    $rating = floatval($_POST['rating'] ?? 0);

    // Basic validation
    if (!$company_name || !$email || !$password) {
        $error = "Please fill in the required company fields (name, email, password).";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Insert company into companies table
        $stmt = $conn->prepare("INSERT INTO companies (company_name, start_year, trade_license, email, password, address) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sissss", $company_name, $start_year, $trade_license, $email, $password_hash, $address);

        if ($stmt->execute()) {
            $companyId = $stmt->insert_id;
            $stmt->close();

            // Handle logo upload if any
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
                        $error = "Failed to upload logo file.";
                    }
                } else {
                    $error = "Invalid logo file type. Allowed types: jpg, jpeg, png, gif.";
                }
            }

            if (!$error) {
                // Insert company profile
                $stmt2 = $conn->prepare("INSERT INTO company_profiles (company_id, bio, location, facilities, rating, logo_path) VALUES (?, ?, ?, ?, ?, ?)");
                if (!$stmt2) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt2->bind_param("isssis", $companyId, $bio, $location, $facilities, $rating, $logo_path);

                if ($stmt2->execute()) {
                    // Success: redirect to company_profile.php with companyId
                    header("Location: company_profile.php?id=" . $companyId);
                    exit();
                } else {
                    $error = "Failed to create profile: " . $stmt2->error;
                }
                $stmt2->close();
            }
        } else {
            $error = "Failed to create company: " . $stmt->error;
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Company | FutureBot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --light: #f8fafc;
            --dark: #1e293b;
            --gray: #e2e8f0;
            --border: #cbd5e1;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --radius: 0.5rem;
            --radius-lg: 0.75rem;
            --transition: all 0.2s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #e6f8e8 0%, #e4f0e8 100%);
            color: #334155;
            line-height: 1.5;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
                        background: rgba(243, 253, 246, 0.95);
        }

        /* Header/Navbar */
        .navbar {
            background: rgba(243, 253, 246, 0.95);
            box-shadow: 0 4px 20px rgba(71, 71, 71, 0.23);
            border-bottom: 1px solid var(--gray);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.025em;
        }

        /* Main Content Layout */
        .main-content {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 2rem;
            padding: 2rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Sidebar (matching company registration) */
        .sidebar {
            background: white;
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
            height: fit-content;
            border: 1px solid var(--gray);
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .sidebar-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }

        .sidebar-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .sidebar-subtitle {
            color: var(--secondary);
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .sidebar-info {
            background: var(--light);
            border-radius: var(--radius);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item i {
            color: var(--primary);
            width: 16px;
        }

        /* Form Content */
        .content {
                        background: rgba(243, 253, 246, 0.95);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray);
        }

        .content-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray);
        }

        .content-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
        }

        .content-header h2 i {
            color: var(--primary);
        }

        /* Alert Styles */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            border-left: 4px solid transparent;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.875rem;
        }

        .alert i {
            margin-top: 0.125rem;
        }

        .alert-danger {
            background: #fef2f2;
            border-color: var(--danger);
            color: #991b1b;
        }

        /* Form Styles */
        .form-section {
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid var(--gray);
        }

        .section-title {
            color: var(--dark);
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--gray);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--primary);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .form-label.required::after {
            content: " *";
            color: var(--danger);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-family: inherit;
            transition: var(--transition);
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .form-control:hover {
            border-color: #94a3b8;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        /* File Upload */
        .file-upload-area {
            border: 2px dashed var(--border);
            border-radius: var(--radius);
            padding: 2rem;
            text-align: center;
            background: var(--light);
            transition: var(--transition);
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .file-upload-area i {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .file-upload-text {
            color: var(--dark);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .file-upload-hint {
            color: var(--secondary);
            font-size: 0.75rem;
        }

        .file-input {
            display: none;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-secondary {
            background: white;
            color: var(--dark);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--light);
            border-color: var(--primary);
        }

        .btn-block {
            width: 100%;
            padding: 0.875rem;
            font-size: 0.9375rem;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        /* Nav Links (for navbar) */
        .nav-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .nav-link {
            color: var(--secondary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
        }

        .nav-link:hover {
            color: var(--primary);
            background: var(--light);
        }

        /* Footer - Updated to match mentor_profile.php */
        footer {
            width: 100%;
            background: white;
            padding: 2rem 1.25rem;
            border-top: 1px solid var(--gray);
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.03);
            margin-top: 4rem;
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
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
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
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .footer-links a:hover {
            color: var(--primary);
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
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
            font-size: 0.9rem;
        }

        .footer-social a:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 15px rgba(37, 99, 235, 0.35);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(37, 99, 235, 0.08);
            width: 100%;
            color: var(--secondary);
            font-size: 0.85rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .sidebar {
                order: 2;
            }
            
            .content {
                order: 1;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }
            
            .sidebar, .content {
                padding: 1.5rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .nav-container {
                padding: 0 1rem;
            }
            
            .footer-links {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .sidebar, .content {
                padding: 1rem;
            }
            
            .sidebar-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .btn {
                padding: 0.75rem 1rem;
            }
            
            .footer-links {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }
            
            .nav-links {
                gap: 0.5rem;
            }
            
            .nav-link {
                padding: 0.5rem;
                font-size: 0.8rem;
            }
        }

        /* Form Validation */
        .form-control:invalid:not(:focus) {
            border-color: var(--danger);
        }

        .form-control:valid:not(:focus):not(:placeholder-shown) {
            border-color: var(--success);
        }

        /* Helper Text */
        .helper-text {
            display: block;
            font-size: 0.75rem;
            color: var(--secondary);
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="logo-text">FutureBot</div>
            </a>
            
            <div class="nav-links">
                <a href="company_dashboard.php" class="nav-link">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="company_login.php" class="nav-link">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <!-- Sidebar (Similar to company registration sidebar) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-icon">
                    <i class="fas fa-building"></i>
                </div>
                <h2 class="sidebar-title">Create Company Profile</h2>
                <p class="sidebar-subtitle">Join FutureBot and showcase your company to potential candidates.</p>
            </div>

            <div class="sidebar-info">
                <div class="info-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Create company profile</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-users"></i>
                    <span>Access to student database</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-bullhorn"></i>
                    <span>Post internships & jobs</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Analytics dashboard</span>
                </div>
            </div>

            <div class="sidebar-info">
                <div class="info-item">
                    <i class="fas fa-info-circle"></i>
                    <span><strong>Requirements:</strong></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-file-alt"></i>
                    <span>Company information</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <span>Valid email address</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-lock"></i>
                    <span>Secure password</span>
                </div>
            </div>
        </aside>

        <!-- Main Form Content -->
        <main class="content">
            <div class="content-header">
                <h2><i class="fas fa-edit"></i> Company Details</h2>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="companyForm">
                <!-- Company Information Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Company Information
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="company_name" class="form-label required">Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control" required 
                                   placeholder="Enter company name"
                                   value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>">
                            <span class="helper-text">Official registered name of your company</span>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label required">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" required 
                                   placeholder="company@example.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            <span class="helper-text">Official company email address</span>
                        </div>

                        <div class="form-group">
                            <label for="start_year" class="form-label">Starting Year</label>
                            <input type="number" name="start_year" id="start_year" class="form-control" 
                                   placeholder="e.g., 2020" min="1900" max="2024"
                                   value="<?= htmlspecialchars($_POST['start_year'] ?? '') ?>">
                            <span class="helper-text">Year your company was founded</span>
                        </div>

                        <div class="form-group">
                            <label for="trade_license" class="form-label">Trade License</label>
                            <input type="text" name="trade_license" id="trade_license" class="form-control" 
                                   placeholder="Enter trade license number"
                                   value="<?= htmlspecialchars($_POST['trade_license'] ?? '') ?>">
                            <span class="helper-text">Government issued trade license number</span>
                        </div>

                        <div class="form-group full-width">
                            <label for="password" class="form-label required">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required 
                                   placeholder="Create a secure password">
                            <span class="helper-text">Minimum 8 characters with letters and numbers</span>
                        </div>

                        <div class="form-group full-width">
                            <label for="address" class="form-label">Company Address</label>
                            <textarea name="address" id="address" class="form-control" rows="3" 
                                      placeholder="Full physical address of your company"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                            <span class="helper-text">Include street, city, state, and zip code</span>
                        </div>
                    </div>
                </div>

                <!-- Company Profile Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-user-circle"></i>
                        Company Profile
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="bio" class="form-label">Company Bio</label>
                            <textarea name="bio" id="bio" class="form-control" rows="4" 
                                      placeholder="Describe your company's mission, values, and what makes you unique"><?= htmlspecialchars($_POST['bio'] ?? '') ?></textarea>
                            <span class="helper-text">Brief description of your company (optional)</span>
                        </div>

                        <div class="form-group">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" name="location" id="location" class="form-control" 
                                   placeholder="Enter company location"
                                   value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                            <span class="helper-text">City, State, Country</span>
                        </div>

                        <div class="form-group">
                            <label for="rating" class="form-label">Rating (0-5)</label>
                            <input type="number" name="rating" id="rating" class="form-control" 
                                   min="0" max="5" step="0.1" 
                                   placeholder="0.0 to 5.0"
                                   value="<?= htmlspecialchars($_POST['rating'] ?? '') ?>">
                            <span class="helper-text">Company rating (optional)</span>
                        </div>

                        <div class="form-group full-width">
                            <label for="facilities" class="form-label">Facilities</label>
                            <textarea name="facilities" id="facilities" class="form-control" rows="3" 
                                      placeholder="Describe company facilities and amenities"><?= htmlspecialchars($_POST['facilities'] ?? '') ?></textarea>
                            <span class="helper-text">Office facilities, perks, etc. (optional)</span>
                        </div>
                    </div>
                </div>

                <!-- Logo Upload Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-camera"></i>
                        Company Logo
                    </h3>
                    
                    <div class="form-group">
                        <div class="file-upload-area" onclick="document.getElementById('logo').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div class="file-upload-text">Click to upload company logo</div>
                            <div class="file-upload-hint">JPG, JPEG, PNG, GIF (Max 5MB)</div>
                            <input type="file" class="file-input" name="logo" id="logo" accept=".jpg,.jpeg,.png,.gif" />
                        </div>
                        <div id="file-name" class="helper-text" style="margin-top: 0.5rem;"></div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-building"></i> Create Company Profile
                    </button>
                    <a href="index.php" class="btn btn-secondary btn-block">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </main>
    </div>

    <!-- Footer - Updated to match mentor_profile.php -->
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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('companyForm');
            const fileInput = document.getElementById('logo');
            const fileNameDisplay = document.getElementById('file-name');
            
            // File upload display
            fileInput.addEventListener('change', function(e) {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                    
                    if (fileSize > 5) {
                        alert('File size exceeds 5MB limit. Please choose a smaller file.');
                        this.value = '';
                        fileNameDisplay.textContent = '';
                        return;
                    }
                    
                    fileNameDisplay.textContent = `Selected: ${file.name} (${fileSize} MB)`;
                    fileNameDisplay.style.color = '#10b981';
                    
                    // Update file upload area appearance
                    const uploadArea = document.querySelector('.file-upload-area');
                    uploadArea.style.borderColor = '#10b981';
                    uploadArea.style.background = '#f0fdf4';
                    uploadArea.querySelector('.file-upload-text').textContent = 'File selected ✓';
                    uploadArea.querySelector('.file-upload-text').style.color = '#10b981';
                }
            });
            
            // Form submission handling
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                // Validate required fields
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#ef4444';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields marked with *');
                    return;
                }
                
                // Show loading state
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
                submitBtn.disabled = true;
                
                // Re-enable after 5 seconds if form doesn't submit
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
            
            // Real-time validation
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.style.borderColor = '#cbd5e1';
                    }
                });
                
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.style.borderColor = '#ef4444';
                    } else if (this.value.trim()) {
                        this.style.borderColor = '#10b981';
                    }
                });
            });
            
            // Password strength indicator
            const passwordInput = document.getElementById('password');
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const helper = this.nextElementSibling;
                
                if (password.length === 0) {
                    helper.textContent = 'Minimum 8 characters with letters and numbers';
                    helper.style.color = '#64748b';
                } else if (password.length < 8) {
                    helper.textContent = 'Password too short (minimum 8 characters)';
                    helper.style.color = '#ef4444';
                } else if (!/\d/.test(password) || !/[a-zA-Z]/.test(password)) {
                    helper.textContent = 'Include both letters and numbers';
                    helper.style.color = '#f59e0b';
                } else {
                    helper.textContent = 'Strong password ✓';
                    helper.style.color = '#10b981';
                }
            });
            
            // Year validation
            const yearInput = document.getElementById('start_year');
            yearInput.addEventListener('blur', function() {
                const year = parseInt(this.value);
                const currentYear = new Date().getFullYear();
                
                if (year && year < 1900) {
                    this.setCustomValidity('Year cannot be before 1900');
                    this.style.borderColor = '#ef4444';
                } else if (year && year > currentYear) {
                    this.setCustomValidity('Year cannot be in the future');
                    this.style.borderColor = '#ef4444';
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
</body>
</html>