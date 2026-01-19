<?php
require 'db.php';

$skillFilter = $_GET['skill'] ?? '';

$sql = "SELECT * FROM student_projects WHERE approved = 1";
$params = [];
$types = '';

if (!empty($skillFilter)) {
    $sql .= " AND skills_used LIKE ?";
    $params[] = "%$skillFilter%";
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Public Project Gallery</title>
</head>
<body>

<h1>Approved Student Projects</h1>

<form method="GET" action="public_projects.php">
    <input type="text" name="skill" placeholder="Filter by skill" value="<?= htmlspecialchars($skillFilter) ?>">
    <button type="submit">Filter</button>
</form>

<?php if ($result->num_rows === 0): ?>
    <p>No projects found.</p>
<?php else: ?>
    <ul>
    <?php while ($row = $result->fetch_assoc()): ?>
        <li>
            <strong><?= htmlspecialchars($row['title']) ?></strong><br>
            Description: <?= htmlspecialchars($row['description']) ?><br>
            Skills: <?= htmlspecialchars($row['skills_used']) ?><br>
            Submitted by: <?= htmlspecialchars($row['user_email']) ?><br>
            <?php if ($row['image_path'] && file_exists($row['image_path'])): ?>
                <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Project image" style="max-width:200px;">
            <?php endif; ?>
        </li>
        <hr>
    <?php endwhile; ?>
    </ul>
<?php endif; ?>

</body>
</html>
