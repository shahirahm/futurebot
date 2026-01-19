<?php
session_start();
require 'db.php'; // Make sure $conn is correctly set

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['action'])) {
    $post_id = intval($_POST['post_id']);
    $action = $_POST['action'];
    $approved = ($action === 'approve') ? 1 : 2; // 1 = approved, 2 = rejected

    $stmt = $conn->prepare("UPDATE job_posts SET approved = ? WHERE id = ?");
    $stmt->bind_param("ii", $approved, $post_id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin_job_approvals.php');
    exit;
}

// Fetch pending job posts along with user info
$posts = $conn->query("
    SELECT jp.*, 
           u.username, u.role, u.profile_pic, 
           u.company_name, u.trade_license, u.location AS user_location
    FROM job_posts jp
    JOIN users u ON jp.user_id = u.user_id
    WHERE jp.approved = 0
    ORDER BY jp.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin: Approve Job Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { padding: 30px; font-family: Arial, sans-serif; background:#f4f6f7; }
        .table th, .table td { vertical-align: middle; }
        img.profile-pic { border-radius: 50%; }
    </style>
</head>
<body class="container">

<h2 class="mb-4">Pending Job Posts for Approval</h2>

<?php if ($posts->num_rows === 0): ?>
    <p>No posts pending approval.</p>
<?php else: ?>
<table class="table table-bordered table-striped bg-white">
    <thead>
        <tr>
            <th>Title</th>
            <th>Details</th>
            <th>Posted By</th>
            <th>Role</th>
            <th>Profile Picture</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($post = $posts->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($post['title'] ?: 'N/A') ?></td>
                <td>
                    <?php if (strcasecmp($post['role'], 'mentor') === 0): ?>
                        <!-- Mentor post details -->
                        <strong>Location:</strong> <?= htmlspecialchars($post['user_location'] ?: 'N/A') ?><br>
                        <strong>Description:</strong> <?= nl2br(htmlspecialchars($post['description'] ?: 'N/A')) ?><br>
                       
                    <?php else: ?>
                        <!-- Company post details -->
                        <strong>Location:</strong> <?= htmlspecialchars($post['user_location'] ?: 'N/A') ?><br>
                        <strong>Company Name:</strong> <?= htmlspecialchars($post['company_name'] ?: 'N/A') ?><br>
                       
                        <strong>Description:</strong> <?= nl2br(htmlspecialchars($post['description'] ?: '')) ?><br>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($post['username']) ?></td>
                <td><?= htmlspecialchars($post['role']) ?></td>
                <td>
                    <?php
                        $profile_pic = $post['profile_pic'] ?? '';
                        if (!$profile_pic || !file_exists('uploads/'.$profile_pic)) {
                            $profile_pic = "https://ui-avatars.com/api/?name=" . urlencode($post['username'] ?: 'User') . "&background=0466c8&color=fff&rounded=true&size=64";
                        }
                    ?>
                    <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile" width="50" height="50" class="profile-pic" />
                </td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                    </form>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>
