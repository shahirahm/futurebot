<?php
session_start();
require_once 'db.php';




if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid book ID.");
}

$id = intval($_GET['id']);

$errors = [];
$success = "";




// Fetch existing book info
$stmt = $conn->prepare("SELECT * FROM books WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Book not found.");
}
$book = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $skill = trim($_POST['skill'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $rating = floatval($_POST['rating'] ?? null);

    // Handle image upload if new image is uploaded
    $image_path = $book['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($ext), $allowed)) {
            $errors[] = "Only JPG, PNG, GIF images allowed.";
        } else {
            $image_name = uniqid('book_') . '.' . $ext;
            $upload_dir = 'uploads/books/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $destination = $upload_dir . $image_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                // Delete old image
                if ($image_path && file_exists($image_path)) unlink($image_path);
                $image_path = $destination;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    if (!$title || !$description || !$skill) {
        $errors[] = "Please fill all required fields.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE books SET title=?, description=?, skill=?, price=?, image=?, rating=? WHERE id=?");
        $stmt->bind_param("sssdsdi", $title, $description, $skill, $price, $image_path, $rating, $id);
        if ($stmt->execute()) {
            $success = "Book updated successfully.";
            // Refresh book data
            $book = ['title'=>$title, 'description'=>$description, 'skill'=>$skill, 'price'=>$price, 'image'=>$image_path, 'rating'=>$rating];
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Book - <?= htmlspecialchars($book['title']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container py-4">
  <a href="admin_books.php" class="btn btn-link mb-3">&larr; Back to Manage Books</a>

  <h2>Edit Book</h2>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
    </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Title*</label>
      <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($book['title']) ?>" />
    </div>
    <div class="mb-3">
      <label class="form-label">Description*</label>
      <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($book['description']) ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Skill Category*</label>
      <input type="text" name="skill" class="form-control" required value="<?= htmlspecialchars($book['skill']) ?>" />
    </div>
    <div class="mb-3">
      <label class="form-label">Price (USD)</label>
      <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlspecialchars($book['price']) ?>" />
    </div>
    <div class="mb-3">
      <label class="form-label">Rating</label>
      <input type="number" step="0.1" min="0" max="5" name="rating" class="form-control" value="<?= htmlspecialchars($book['rating'] ?? '') ?>" />
    </div>
    <div class="mb-3">
      <label class="form-label">Current Cover Image</label><br>
      <?php if ($book['image'] && file_exists($book['image'])): ?>
        <img src="<?= htmlspecialchars($book['image']) ?>" alt="Current Image" style="max-height:150px; border-radius:8px;" />
      <?php else: ?>
        <div style="width:150px; height:200px; background:#eee; display:flex; align-items:center; justify-content:center;">No Image</div>
      <?php endif; ?>
    </div>
    <div class="mb-3">
      <label class="form-label">Change Cover Image</label>
      <input type="file" name="image" class="form-control" accept="image/*" />
    </div>

    <button type="submit" class="btn btn-primary">Update Book</button>
  </form>
</div>
</body>
</html>
