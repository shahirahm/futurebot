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
    <title>Create Company and Profile - FutureBot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5" style="max-width: 700px;">
    <h1>Create Company and Profile</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <h3>Company Information</h3>
        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name *</label>
            <input type="text" name="company_name" id="company_name" class="form-control" required value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="start_year" class="form-label">Starting Year</label>
            <input type="number" name="start_year" id="start_year" class="form-control" value="<?= htmlspecialchars($_POST['start_year'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="trade_license" class="form-label">Trade License</label>
            <input type="text" name="trade_license" id="trade_license" class="form-control" value="<?= htmlspecialchars($_POST['trade_license'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email Address *</label>
            <input type="email" name="email" id="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password *</label>
            <input type="password" name="password" id="password" class="form-control" required />
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea name="address" id="address" class="form-control"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
        </div>

        <h3>Company Profile</h3>
        <div class="mb-3">
            <label for="bio" class="form-label">Bio</label>
            <textarea name="bio" id="bio" class="form-control" rows="3"><?= htmlspecialchars($_POST['bio'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" class="form-control" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="facilities" class="form-label">Facilities</label>
            <textarea name="facilities" id="facilities" class="form-control" rows="2"><?= htmlspecialchars($_POST['facilities'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="rating" class="form-label">Rating (0 to 5)</label>
            <input type="number" name="rating" id="rating" class="form-control" min="0" max="5" step="0.1" value="<?= htmlspecialchars($_POST['rating'] ?? '') ?>" />
        </div>
        <div class="mb-3">
            <label for="logo" class="form-label">Upload Logo (jpg, jpeg, png, gif)</label>
            <input type="file" name="logo" id="logo" accept=".jpg,.jpeg,.png,.gif" class="form-control" />
        </div>

        <button type="submit" class="btn btn-primary">Create Company and Profile</button>
    </form>
</div>
</body>
</html>
