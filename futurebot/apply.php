<?php
// Enable error reporting (for debugging only, remove on production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';

// Simulate logged-in user for testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;  // example user id
}

$applicant_id = $_SESSION['user_id'];
$job_id = $_POST['job_id'] ?? null;

if (!$job_id) {
    die("Job ID is missing.");
}

try {
    // Check if already applied
    $stmt = $pdo->prepare("SELECT * FROM jobapplications WHERE job_id = ? AND applicant_id = ?");
    $stmt->execute([$job_id, $applicant_id]);
    if ($stmt->rowCount() > 0) {
        die("You have already applied for this job.");
    }

    $sql = "INSERT INTO jobapplications (job_id, applicant_id, status, applied_at) VALUES (?, ?, 'pending', NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$job_id, $applicant_id]);

    echo "Successfully applied for the job!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
<!DOCTYPE html>
<html>
<head><title>Apply for Job</title></head>
<body>
    <h2>Apply for Job ID 123</h2>
    <form action="apply_job.php" method="post">
        <input type="hidden" name="job_id" value="123">
        <button type="submit">Apply Now</button>
    </form>
</body>
</html>
