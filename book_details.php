<?php
session_start();
require_once 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid book ID.");
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM books WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Book not found.");
}

$book = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($book['title']) ?> - Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container py-4">
  <a href="all_books.php" class="btn btn-link mb-3">&larr; Back to all books</a>
  <div class="row g-4">
    <div class="col-md-4">
      <?php if ($book['image'] && file_exists($book['image'])): ?>
        <img src="<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="img-fluid rounded" />
      <?php else: ?>
        <div style="width:100%; height:300px; background:#eee; display:flex; align-items:center; justify-content:center;">No Image</div>
      <?php endif; ?>
    </div>
    <div class="col-md-8">
      <h2><?= htmlspecialchars($book['title']) ?></h2>
      <p><strong>Skill:</strong> <?= htmlspecialchars($book['skill']) ?></p>
      <p><strong>Price:</strong> $<?= number_format($book['price'], 2) ?></p>
      <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>
      <p><strong>Rating:</strong> <?= $book['rating'] ?? 'N/A' ?></p>
    </div>
  </div>
</div>
</body>
</html>
