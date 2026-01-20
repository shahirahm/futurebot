<?php
require_once 'db.php';

// Removed session check for admin login




$errors = [];
$success = "";

// Handle new book upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $skill = trim($_POST['skill'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    // Image upload handling
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed_img = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($ext), $allowed_img)) {
            $errors[] = "Only JPG, PNG, GIF images allowed for cover image.";
        } else {
            $image_name = uniqid('book_') . '.' . $ext;
            $upload_dir = 'uploads/books/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $destination = $upload_dir . $image_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $image_path = $destination;
            } else {
                $errors[] = "Failed to upload cover image.";
            }
        }
    }




    // PDF upload handling
    $pdf_path = null;
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $ext_pdf = pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION);
        $allowed_pdf = ['pdf'];
        if (!in_array(strtolower($ext_pdf), $allowed_pdf)) {
            $errors[] = "Only PDF files are allowed for the book PDF.";
        } else {
            $pdf_name = uniqid('book_pdf_') . '.' . $ext_pdf;
            $upload_dir_pdf = 'uploads/books/pdfs/';
            if (!is_dir($upload_dir_pdf)) mkdir($upload_dir_pdf, 0755, true);
            $destination_pdf = $upload_dir_pdf . $pdf_name;
            if (move_uploaded_file($_FILES['pdf']['tmp_name'], $destination_pdf)) {
                $pdf_path = $destination_pdf;
            } else {
                $errors[] = "Failed to upload book PDF.";
            }
        }
    }

    if (!$title || !$description || !$skill) {
        $errors[] = "Please fill all required fields.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO books (title, description, skill, price, image, pdf) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdss", $title, $description, $skill, $price, $image_path, $pdf_path);
        if ($stmt->execute()) {
            $success = "Book added successfully.";
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}



// Handle deletion
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    // Delete image and PDF files before deleting DB record
    $stmt = $conn->prepare("SELECT image, pdf FROM books WHERE id=?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->bind_result($img_to_delete, $pdf_to_delete);
    $stmt->fetch();
    $stmt->close();

    if ($img_to_delete && file_exists($img_to_delete)) {
        unlink($img_to_delete);
    }
    if ($pdf_to_delete && file_exists($pdf_to_delete)) {
        unlink($pdf_to_delete);
    }

    $stmt = $conn->prepare("DELETE FROM books WHERE id=?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_books.php");
    exit;
}

// Fetch all books
$result = $conn->query("SELECT * FROM books ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin - Manage Books</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { background: #f5f7fa; }
    .container { max-width: 900px; margin-top: 30px; }
    img.book-img { max-width: 80px; max-height: 80px; object-fit: cover; border-radius: 6px; }
  </style>
</head>
<body>
<div class="container">
  <h1 class="mb-4">Admin: Manage Books</h1>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
    </div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="mb-5">
    <h4>Add New Book</h4>
    <div class="mb-3">
      <label class="form-label">Title*</label>
      <input type="text" name="title" class="form-control" required />
    </div>
    <div class="mb-3">
      <label class="form-label">Description*</label>
      <textarea name="description" class="form-control" rows="3" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Skill Category*</label>
      <input type="text" name="skill" class="form-control" required placeholder="e.g. Python, JavaScript" />
    </div>
    <div class="mb-3">
      <label class="form-label">Price (USD)</label>
      <input type="number" name="price" step="0.01" class="form-control" value="0" />
    </div>
    <div class="mb-3">
      <label class="form-label">Cover Image</label>
      <input type="file" name="image" class="form-control" accept="image/*" />
    </div>
    <div class="mb-3">
      <label class="form-label">Book PDF</label>
      <input type="file" name="pdf" class="form-control" accept="application/pdf" />
    </div>
    <button type="submit" name="add_book" class="btn btn-primary">Add Book</button>
  </form>

  <hr />

  <h4>Existing Books</h4>
  <table class="table table-striped table-bordered align-middle">
    <thead>
      <tr>
        <th>Cover</th>
        <th>Title</th>
        <th>Skill</th>
        <th>Price</th>
        <th>PDF</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($book = $result->fetch_assoc()): ?>
        <tr>
          <td>
            <?php if ($book['image'] && file_exists($book['image'])): ?>
              <img src="<?= htmlspecialchars($book['image']) ?>" alt="Cover" class="book-img" />
            <?php else: ?>
              <div style="width:80px; height:80px; background:#ddd; border-radius:6px; display:flex; align-items:center; justify-content:center;">No Image</div>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($book['title']) ?></td>
          <td><?= htmlspecialchars($book['skill']) ?></td>
          <td>$<?= number_format($book['price'], 2) ?></td>
          <td>
            <?php if ($book['pdf'] && file_exists($book['pdf'])): ?>
              <a href="<?= htmlspecialchars($book['pdf']) ?>" target="_blank" class="btn btn-sm btn-info">View PDF</a>
            <?php else: ?>
              <span class="text-muted">No PDF</span>
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($book['created_at']) ?></td>
          <td>
            <a href="admin_edit_book.php?id=<?= $book['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
            <a href="admin_books.php?delete_id=<?= $book['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this book?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
      <?php $result->free(); ?>
    </tbody>
  </table>

</div>
</body>
</html>
