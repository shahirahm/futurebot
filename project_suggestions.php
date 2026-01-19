<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Fetch user-selected skills
$skill_query = $conn->prepare("SELECT DISTINCT skill FROM user_skill_progress WHERE user_email = ?");
$skill_query->bind_param("s", $user_email);
$skill_query->execute();
$skill_result = $skill_query->get_result();

$skills = [];
while ($row = $skill_result->fetch_assoc()) {
    $skills[] = $row['skill'];
}

// Fetch projects related to the user's skills dynamically
$sql = "SELECT * FROM career_milestones WHERE milestone_type = 'project' AND recommended_for_skills IS NOT NULL";
$project_stmt = $conn->prepare($sql);
$project_stmt->execute();
$project_result = $project_stmt->get_result();

$projects = [];
while ($row = $project_result->fetch_assoc()) {
    foreach ($skills as $skill) {
        if (stripos($row['recommended_for_skills'], $skill) !== false) {
            // Decode JSON steps if stored as JSON
            if (!empty($row['steps'])) {
                $row['steps'] = json_decode($row['steps'], true);
                if (!is_array($row['steps'])) $row['steps'] = explode("\n", $row['steps']);
            } else {
                $row['steps'] = [];
            }
            $projects[] = $row;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Suggestions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .project-card {
            border: 1px solid #aaa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .project-card h3 { margin-top: 0; color: #0466c8; }
        .project-details { margin-top: 10px; }
        .project-details li { margin-bottom: 5px; }
        .badge { margin-right: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="mb-4">Recommended Projects for You</h2>

        <?php if (count($projects) > 0): ?>
            <?php foreach ($projects as $project): ?>
                <div class="project-card">
                    <h3><?= htmlspecialchars($project['title']) ?></h3>
                    <p><?= htmlspecialchars($project['description']) ?></p>

                    <div class="project-details">
                        <?php if (!empty($project['languages'])): ?>
                            <p><strong>Languages:</strong> <?= htmlspecialchars($project['languages']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($project['tools'])): ?>
                            <p><strong>Tools:</strong> <?= htmlspecialchars($project['tools']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($project['domain'])): ?>
                            <p><strong>Domain:</strong> <?= htmlspecialchars($project['domain']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($project['steps'])): ?>
                            <p><strong>Step-by-Step Pathway:</strong></p>
                            <ol>
                                <?php foreach ($project['steps'] as $step): ?>
                                    <li><?= htmlspecialchars($step) ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($project['link'])): ?>
                        <a href="<?= htmlspecialchars($project['link']) ?>" target="_blank" class="btn btn-sm btn-primary mt-2">Project Resource</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No project suggestions found based on your current skills.</p>
        <?php endif; ?>
    </div>
</body>
</html>
