<?php
session_start();
require_once 'db.php';

// Check if company is logged in
if (!isset($_SESSION['company_id'])) {
    header("Location: company_login.php");
    exit;
}

$companyId = $_SESSION['company_id'];

// Handle Internship Post Submission
$post_success = false;
$post_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_internship'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($description)) {
        $post_error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO job_posts (user_id, title, description, type, approved, created_at) VALUES (?, ?, ?, 'internship', 0, NOW())");
        $stmt->bind_param("iss", $companyId, $title, $description);

        if ($stmt->execute()) {
            $post_success = true;
        } else {
            $post_error = "Error saving internship post. Please try again.";
        }
        $stmt->close();
    }
}

// Fetch company info
$stmt = $conn->prepare("SELECT company_name, start_year, trade_license, email, phone, address FROM companies WHERE id = ?");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Company Profile - <?= htmlspecialchars($company['company_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
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
    padding-top: 50px; /* Added to account for fixed navbar */
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
    padding: 0.3rem 2rem; /* DRAMATICALLY REDUCED */
    border-bottom: 1px solid rgba(67, 97, 238, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow);
    position: fixed; /* Changed to fixed */
    top: 0;
    width: 100%;
    z-index: 1000;
    backdrop-filter: blur(10px);
    height: 70px; /* Fixed height */
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.5rem; /* Reduced */
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
    gap: 1rem; /* Reduced */
    align-items: center;
}

.nav-link {
    color: var(--text-dark);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 0.3rem 0.6rem; /* DRAMATICALLY REDUCED */
    border-radius: 6px;
    font-size: 0.8rem; /* Smaller font */
}

.nav-link:hover {
    color: var(--primary-blue);
    background: rgba(67, 97, 238, 0.05);
    transform: translateY(-1px);
}

.nav-link.active {
    color: var(--primary-blue);
    background: rgba(67, 97, 238, 0.1);
}

.container {
    max-width: 1250px; /* REDUCED CONTAINER WIDTH from 1200px */
    margin: 3rem auto 1.5rem auto; /* INCREASED TOP MARGIN FOR GAP */
    padding: 0 1.25rem;
}

/* Add gap after navbar */
.navbar-gap {
    height: 40px; /* PROPER GAP */
    width: 100%;
}

.profile-card, .post-internship-card {
    background: var(--white);
    border-radius: 20px;
    box-shadow: var(--shadow);
    padding: 2.5rem;
    animation: fadeIn 0.8s ease-in-out;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(67, 97, 238, 0.1);
    backdrop-filter: blur(10px);
    margin-bottom: 2rem;
    max-width: 1500px; /* PROPER WIDTH */
    margin-left: auto; /* CENTER THE CARDS */
    margin-right: auto; /* CENTER THE CARDS */
}

.profile-card::before, .post-internship-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient);
}

/* Gap between navbar and profile container */
.profile-container {
    margin-top: 50px; /* Adjust gap as needed */
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.profile-header {
    display: flex;
    align-items: flex-start;
    margin-bottom: 2rem;
    position: relative;
}

.profile-logo-container {
    position: relative;
    margin-right: 2rem;
}

.profile-logo {
    width: 120px;
    height: 120px;
    border-radius: 16px;
    object-fit: cover;
    border: 4px solid var(--white);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
}

.profile-logo-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 16px;
    background: var(--gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    font-weight: bold;
    border: 4px solid var(--white);
    box-shadow: 0 8px 25px rgba(67, 97, 238, 0.2);
}

.profile-info {
    flex: 1;
}

.company-name {
    color: var(--text-dark);
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    line-height: 1.2;
}

.company-location {
    color: var(--primary-blue);
    font-size: 1.1rem;
    font-weight: 500;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.company-details {
    color: var(--text-light);
    font-size: 0.95rem;
    margin-bottom: 0;
}

.edit-btn {
    background: var(--gradient);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    position: absolute;
    top: 0;
    right: 10px;
   margin-top: 15px;
}

.edit-btn:hover {
    background: var(--gradient-hover);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
    color: white;
    text-decoration: none;
}

.profile-section {
    margin-bottom: 2rem;
}

.section-title {
    color: var(--text-dark);
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid rgba(67, 97, 238, 0.1);
}

.profile-bio {
    color: var(--text-light);
    font-size: 1.05rem;
    line-height: 1.7;
    margin-bottom: 0;
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
    gap: 0.75rem;
}

.btn-primary:hover {
    background: var(--gradient-hover);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
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

.alert-success {
    background: rgba(40, 152, 66, 0.1);
    color: #198754;
    border-left: 4px solid #198754;
}

.alert-danger {
    background: rgba(230, 57, 70, 0.1);
    color: #dc3545;
    border-left: 4px solid #dc3545;
}

.alert-info {
    background: rgba(67, 97, 238, 0.1);
    color: var(--primary-blue);
    border-left: 4px solid var(--primary-blue);
}

/* Rating Stars */
.rating-stars {
    color: #f7b500;
    font-size: 1.25rem;
    vertical-align: middle;
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
    body {
        padding-top: 45px; /* Adjusted for mobile */
    }
    
    .navbar {
        padding: 0.25rem 1rem; /* Even more reduced */
        height: 45px; /* Smaller on mobile */
    }
    
    .logo-text {
        font-size: 0.9rem;
    }
    
    .container {
        margin: 2.5rem auto 1rem auto; /* Adjusted top margin for mobile */
        padding: 0 1rem;
        max-width: 95%; /* More flexible on mobile */
    }
    
    .navbar-gap {
        height: 30px; /* Smaller gap on mobile */
    }
    
    .profile-card, .post-internship-card {
        padding: 1.5rem;
        border-radius: 16px;
        max-width: 100%; /* Full width on mobile */
    }
    
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-logo-container {
        margin-right: 0;
        margin-bottom: 1.5rem;
    }
    
    .company-name {
        font-size: 1.75rem;
    }
    
    .edit-btn {
        position: relative;
        margin-top: 1rem;
    }
    
    .nav-links {
        gap: 0.8rem;
    }
    
    .footer-links {
        flex-direction: column;
        gap: 1rem;
    }
}

@media (max-width: 480px) {
    body {
        padding-top: 40px; /* Even smaller */
    }
    
    .navbar {
        padding: 0.2rem 0.8rem;
        height: 40px;
    }
    
    .container {
        margin: 2rem auto 1rem auto; /* Adjusted for smallest screens */
        max-width: 95%;
    }
    
    .navbar-gap {
        height: 25px; /* Smallest gap */
    }
    
    .company-name {
        font-size: 1.5rem;
    }
    
    .profile-card, .post-internship-card {
        padding: 1.25rem;
        max-width: 100%;
    }

    .logo-icon {
        width: 24px;
        height: 24px;
        font-size: 0.7rem;
    }
    
    .nav-link {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
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
    <div class="navbar">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-robot"></i>
            </div>
            <div class="logo-text">FutureBot</div>
        </div>
        
        <div class="nav-links">
            <a href="company_dashboard.php" class="nav-link">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="company_login.php" class="nav-link">
                <i class="fas fa-sign-in-alt"></i> Back
            </a>
        </div>
    </div>

    <!-- Gap between navbar and content -->
    <div class="navbar-gap"></div>

<div class="container">
    <div class="profile-card shadow-sm">
        <a href="company_profile_edit.php?id=<?= urlencode($companyId) ?>" class="edit-btn" title="Edit Profile">
            <i class="fas fa-edit"></i> Edit Profile
        </a>

        <div class="profile-header">
            <div class="profile-logo-container">
                <?php if (!empty($profile['logo_path']) && file_exists($profile['logo_path'])): ?>
                    <img src="<?= htmlspecialchars($profile['logo_path']) ?>" class="profile-logo" alt="<?= htmlspecialchars($company['company_name']) ?>">
                <?php else: ?>
                    <div class="profile-logo-placeholder">
                        <?= strtoupper(substr($company['company_name'], 0, 2)) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <h1 class="company-name"><?= htmlspecialchars($company['company_name']) ?></h1>
                <div class="company-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?= htmlspecialchars($profile['location'] ?? 'Location not specified') ?>
                </div>
                <p class="company-details">
                    Started in <?= htmlspecialchars($company['start_year']) ?> | 
                    License: <?= htmlspecialchars($company['trade_license']) ?>
                </p>
            </div>
        </div>

        <div class="profile-section">
            <h3 class="section-title">About Company</h3>
            <p class="profile-bio"><?= nl2br(htmlspecialchars($profile['bio'] ?? 'No bio available.')) ?></p>
        </div>

        <?php if (!empty($profile['facilities'])): ?>
        <div class="profile-section">
            <h3 class="section-title">Facilities</h3>
            <p class="profile-bio"><?= nl2br(htmlspecialchars($profile['facilities'])) ?></p>
        </div>
        <?php endif; ?>

        <div class="profile-section">
            <h3 class="section-title">Company Rating</h3>
            <div class="rating-stars">
                <?php
                $rating = floatval($profile['rating'] ?? 0);
                $fullStars = floor($rating);
                $halfStar = ($rating - $fullStars) >= 0.5;
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                ?>
                <?php for ($i = 0; $i < $fullStars; $i++): ?>
                    <i class="fas fa-star"></i>
                <?php endfor; ?>
                <?php if ($halfStar): ?>
                    <i class="fas fa-star-half-alt"></i>
                <?php endif; ?>
                <?php for ($i = 0; $i < $emptyStars; $i++): ?>
                    <i class="far fa-star"></i>
                <?php endfor; ?>
                <span class="ms-2 text-muted">(<?= number_format($rating, 1) ?>/5)</span>
            </div>
        </div>

        <div class="profile-section">
            <h3 class="section-title">Contact Information</h3>
            <div class="profile-bio">
                <p><strong>Email:</strong> <?= htmlspecialchars($company['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($company['phone'] ?? 'N/A') ?></p>
                <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($company['address'])) ?></p>
            </div>
        </div>
    </div>

    <!-- Post Internship Form -->
    <div class="post-internship-card shadow-sm">
        <h3 class="section-title">Post a New Internship</h3>
        
        <?php if ($post_success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Internship posted successfully! It will be visible once approved by admin.
            </div>
        <?php elseif ($post_error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($post_error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="title" class="form-label">Internship Title</label>
                <input type="text" class="form-control" id="title" name="title" required maxlength="255" 
                       placeholder="Enter internship position title" 
                       value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="description" class="form-label">Internship Description</label>
                <textarea class="form-control" id="description" name="description" rows="5" required
                          placeholder="Describe the internship role, responsibilities, requirements, and benefits"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
            </div>
            <button type="submit" name="post_internship" class="btn-primary">
                <i class="fas fa-paper-plane"></i> Post Internship
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>