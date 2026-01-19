<?php
require_once 'db.php';

// Get company ID safely
$companyId = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

if ($companyId <= 0) {
    // Just show message, no link or redirect
    echo "<h2>Invalid company ID.</h2>";
    exit;  // stop execution so rest of page doesn't run without valid ID
}

// Your existing code continues here...


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
    <title>Edit Company Profile - <?= htmlspecialchars($company['company_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      body {
        padding-top: 70px;
        background: linear-gradient(135deg, #f0f4ff, #d9e4ff);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
      }
      .card {
        box-shadow: 0 0.5rem 1rem rgba(13, 71, 161, 0.15);
        border-radius: 1rem;
        background-color: #fff;
        transition: transform 0.3s ease;
      }
      .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 1rem 2rem rgba(13, 71, 161, 0.25);
      }
      .profile-pic {
        max-width: 100px;
        max-height: 100px;
        object-fit: cover;
        border-radius: 1rem;
        box-shadow: 0 4px 8px rgba(13, 71, 161, 0.3);
        margin-bottom: 10px;
        border: 2px solid #0d47a1;
      }
      .btn-back {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 0.35rem 0.75rem;
        transition: background-color 0.3s ease, color 0.3s ease;
        border-radius: 0.35rem;
        margin-left: auto;
        color: darkblue;
      }
      .btn-back:hover {
        background-color: #fff !important;
        color: #1766dcff !important;
      }
      button.btn-primary {
        background: #0d47a1;
        border: none;
        font-weight: 600;
        box-shadow: 0 6px 15px rgba(13, 71, 161, 0.3);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        border-radius: 0.6rem;
        padding: 0.55rem 1.5rem;
      }
      button.btn-primary:hover {
        background: #08306b;
        box-shadow: 0 8px 20px rgba(8, 48, 107, 0.5);
      }
      a.btn-secondary {
        border-radius: 0.6rem;
        padding: 0.55rem 1.5rem;
        transition: background-color 0.3s ease;
      }
      a.btn-secondary:hover {
        background-color: #6c757d;
        color: #fff;
      }
      label.form-label {
        font-weight: 600;
        color: #0d47a1;
      }
      .alert {
        border-radius: 0.7rem;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(13, 71, 161, 0.2);
      }
      .container-small {
        max-width: 900px;
        margin-top: 50px;
        padding: 0.5rem;
      }
      @media (max-width: 450px) {
        .container-small {
          max-width: 95%;
          padding: 0.5rem;
        }
      }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top shadow-sm">
  <div class="container d-flex justify-content-between align-items-center">
    <a href="company_profile.php?id=<?= $companyId ?>" class="btn btn-light btn-back" aria-label="Back to profile">
      &larr; Back to Profile
    </a>
   
    <div></div><!-- empty for spacing -->
  </div>
</nav>

<div class="container container-small">
    <div class="card p-4 mb-5">
        <?php if ($error): ?>
            <div class="alert alert-danger" id="error-alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success" id="success-alert"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>
            <h3 class="mb-4 text-primary fw-bold">Company Information</h3>
            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                <input type="text" name="company_name" id="company_name" class="form-control form-control-lg" required value="<?= htmlspecialchars($company['company_name'] ?? '') ?>" />
            </div>
            <div class="mb-3">
                <label for="start_year" class="form-label">Starting Year</label>
                <input type="number" name="start_year" id="start_year" class="form-control form-control-lg" value="<?= htmlspecialchars($company['start_year'] ?? '') ?>" />
            </div>
            <div class="mb-3">
                <label for="trade_license" class="form-label">Trade License</label>
                <input type="text" name="trade_license" id="trade_license" class="form-control form-control-lg" value="<?= htmlspecialchars($company['trade_license'] ?? '') ?>" />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" class="form-control form-control-lg" required value="<?= htmlspecialchars($company['email'] ?? '') ?>" />
            </div>
            <div class="mb-4">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control form-control-lg" rows="2"><?= htmlspecialchars($company['address'] ?? '') ?></textarea>
            </div>

            <h3 class="mb-4 text-primary fw-bold">Company Profile</h3>
            <div class="mb-3">
                <label for="bio" class="form-label">Bio</label>
                <textarea name="bio" id="bio" class="form-control form-control-lg" rows="2"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" name="location" id="location" class="form-control form-control-lg" value="<?= htmlspecialchars($profile['location'] ?? '') ?>" />
            </div>
            <div class="mb-3">
                <label for="facilities" class="form-label">Facilities</label>
                <textarea name="facilities" id="facilities" class="form-control form-control-lg" rows="2"><?= htmlspecialchars($profile['facilities'] ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">Rating (0 to 5)</label>
                <input type="number" name="rating" id="rating" class="form-control form-control-lg" min="0" max="5" step="0.1" value="<?= htmlspecialchars($profile['rating'] ?? '') ?>" />
            </div>

            <div class="mb-4">
                <label for="logo" class="form-label">Upload Profile Picture (jpg, jpeg, png, gif)</label><br>
                <?php if (!empty($profile['logo_path']) && file_exists($profile['logo_path'])): ?>
                    <img src="<?= htmlspecialchars($profile['logo_path']) ?>" alt="Current Profile Picture" class="profile-pic" />
                <?php endif; ?>
                <input type="file" name="logo" id="logo" accept=".jpg,.jpeg,.png,.gif" class="form-control form-control-lg" />
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">Save Changes</button>
            <a href="company_profile.php?id=<?= $companyId ?>" class="btn btn-secondary btn-lg w-100 mt-3">Cancel</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Fade out success alert after 2 seconds
  window.addEventListener('DOMContentLoaded', () => {
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
      setTimeout(() => {
        successAlert.style.transition = 'opacity 0.5s ease';
        successAlert.style.opacity = '0';
        setTimeout(() => successAlert.remove(), 500);
      }, 2000);
    }
  });
</script>

</body>
</html>
