<?php
session_start();
require_once 'db.php';

// TODO: Add proper admin authentication here
// Example:
// if (!isset($_SESSION['admin_logged_in'])) {
//     header("Location: admin_login.php");
//     exit;
// }

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['post_id'])) {
    $postId = intval($_POST['post_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE job_posts SET approved = 1 WHERE id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("DELETE FROM job_posts WHERE id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: admin_internship_approvals.php");
    exit;
}

// Fetch all pending internship posts
$stmt = $conn->prepare("
    SELECT jp.id, jp.title, jp.description, jp.created_at, c.company_name 
    FROM job_posts jp 
    JOIN companies c ON jp.user_id = c.id 
    WHERE jp.type = 'internship' AND jp.approved = 0 
    ORDER BY jp.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Internship Approvals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5">
    <h2>Pending Internship Posts</h2>

    <?php if ($result->num_rows === 0): ?>
        <div class="alert alert-info">No pending internship posts to approve.</div>
    <?php else: ?>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Internship Title</th>
                    <th>Description</th>
                    <th>Posted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['company_name']) ?></td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-success btn-sm" onclick="return confirm('Approve this internship?')">Approve</button>
                        </form>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm" onclick="return confirm('Reject this internship? This will delete the post.')">Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
