<?php
session_start();
require 'db.php';

// Check admin login here (implement as needed)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = intval($_POST['project_id']);
    if (isset($_POST['approve'])) {
        $stmt = $conn->prepare("UPDATE student_projects SET approved = 1 WHERE id = ?");
    } elseif (isset($_POST['reject'])) {
        $stmt = $conn->prepare("UPDATE student_projects SET approved = 2 WHERE id = ?");
    }
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    header("Location: admin_projects.php");
    exit;
}

$result = $conn->query("SELECT * FROM student_projects WHERE approved = 0 ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin: Review Projects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <h2 class="mb-4 text-center">Pending Project Submissions</h2>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info text-center">No pending projects.</div>
    <?php else: ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                            <p class="card-text"><strong>Description:</strong><br><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                            <p><strong>Skills Used:</strong> <?= htmlspecialchars($row['skills_used']) ?></p>
                            <p><strong>Submitted By:</strong> <?= htmlspecialchars($row['user_email']) ?></p>

                            <div class="d-flex justify-content-between">
                                <form method="POST">
                                    <input type="hidden" name="project_id" value="<?= $row['id'] ?>" />
                                    <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form method="POST">
                                    <input type="hidden" name="project_id" value="<?= $row['id'] ?>" />
                                    <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
