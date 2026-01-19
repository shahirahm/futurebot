<?php
session_start();
require_once 'db.php';

$user_email = $_SESSION['email'];

// Get wishlist books
$stmt = $conn->prepare("
    SELECT b.*
    FROM books b
    INNER JOIN wishlist w ON b.id = w.book_id
    WHERE w.user_email = ?
    ORDER BY w.added_at DESC
");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Your Wishlist</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: #f0f7ff;
      font-family: Arial, sans-serif;
      padding: 40px;
      color: #1a2e3b;
    }
    h1 {
      text-align: center;
      color: #0b3d91;
      margin-bottom: 30px;
    }
    .card {
      border-radius: 14px;
      background: white;
      box-shadow: 0 4px 15px rgba(11, 61, 145, 0.15);
      padding: 1rem;
      margin-bottom: 1.5rem;
    }
    .btn-remove {
      background-color: #d9534f;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 5px 10px;
      font-size: 0.85rem;
    }
    .btn-remove:hover {
      background-color: #c9302c;
      color: white;
    }
  </style>
</head>
<body>

  <h1>Your Wishlist</h1>

  <?php if ($result->num_rows === 0): ?>
    <p class="text-center fs-5 text-muted">Your wishlist is empty.</p>
  <?php else: ?>
    <div class="container" style="max-width: 800px; margin: 0 auto;">
      <?php while ($book = $result->fetch_assoc()): ?>
        <div class="card d-flex flex-row align-items-center justify-content-between">
          <div>
            <strong><?= htmlspecialchars($book['title']) ?></strong><br />
            <small>Skill: <?= htmlspecialchars($book['skill']) ?> | Price: $<?= number_format($book['price'], 2) ?></small>
          </div>
          <form method="POST" action="wishlist_action.php" style="margin:0;">
            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
            <button type="submit" name="toggle_wishlist" class="btn-remove" title="Remove from wishlist">Remove</button>
          </form>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>

</body>
</html>
