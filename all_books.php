<?php
session_start();
require_once 'db.php';

$user_email = $_SESSION['user_email'] ?? null;




// Get filter and sort options
$book_name = $_GET['book_name'] ?? '';
$sort = $_GET['sort'] ?? '';

// Build base SQL
$sql = "SELECT * FROM books WHERE 1=1";

// Add book name filter
$params = [];
$types = "";

if (!empty($book_name)) {
    $sql .= " AND title LIKE ?";
    $params[] = "%$book_name%";
    $types .= "s";
}

// Sorting logic
if ($sort === 'price_asc') {
    $sql .= " ORDER BY price ASC";
} elseif ($sort === 'price_desc') {
    $sql .= " ORDER BY price DESC";
} elseif ($sort === 'rating_desc') {
    $sql .= " ORDER BY rating DESC";
} else {
    $sql .= " ORDER BY created_at DESC";
}

// Prepare and execute query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Explore Books - FutureBot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
    body {
      background: linear-gradient(to bottom, #cde7f0, #eaf6fb);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      color: #1a2e3b;
      padding: 40px 15px 60px;
    }

    h1 {
      text-align: center;
      color: #0b3d91;
      font-weight: 800;
      font-size: 2.8rem;
      margin-bottom: 2.5rem;
      letter-spacing: 0.1em;
      text-shadow: 0 1px 4px rgba(11,61,145,0.3);
      user-select: none;
    }

    form {
      max-width: 900px;
      margin: 0 auto 3rem auto;
      background: #d8ecf8cc;
      padding: 20px 30px;
      border-radius: 12px;
      box-shadow: 0 6px 16px rgba(11, 61, 145, 0.15);
      backdrop-filter: saturate(180%) blur(10px);
      border: 1px solid #a6c8e6;
    }

    .form-label {
      font-weight: 600;
      color: #0a2a57;
    }

    .form-select {
      border-radius: 10px;
      border: 1.8px solid #7db1de;
      box-shadow: inset 0 1px 3px #b3d1f5;
      transition: border-color 0.3s ease;
    }

    .form-select:focus {
      border-color: #0b3d91;
      box-shadow: 0 0 6px #4a90e2aa;
      outline: none;
    }

    .row-cols-1 > * {
      margin-bottom: 1.5rem;
    }

    .row {
      max-width: 900px;
      margin: 0 auto;
    }

    .book-card {
      border-radius: 1rem;
      background: rgba(255, 255, 255, 0.85);
      box-shadow: 0 6px 16px rgba(4, 57, 94, 0.15);
      padding: 1rem 1.25rem;
      margin-bottom: 0.75rem;
      transition: all 0.25s ease;
      backdrop-filter: blur(8px);
      border: 1px solid #e0ecff;

      /* Animation */
      opacity: 0;
      transform: translateY(20px);
      animation: fadeSlideIn 0.5s ease forwards;
    }

    .book-card:nth-child(1) { animation-delay: 0.05s; }
    .book-card:nth-child(2) { animation-delay: 0.1s; }
    .book-card:nth-child(3) { animation-delay: 0.15s; }
    .book-card:nth-child(4) { animation-delay: 0.2s; }
    .book-card:nth-child(5) { animation-delay: 0.25s; }
    .book-card:nth-child(6) { animation-delay: 0.3s; }

    @keyframes fadeSlideIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .book-card:hover {
      box-shadow: 0 10px 24px rgba(4, 57, 94, 0.2);
      transform: translateY(-2px);
    }

    .card-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #0a3d62;
      margin-bottom: 0.5rem;
      text-shadow: 0 1px 2px rgba(0,0,0,0.06);
    }

    .card-text.description {
      font-size: 1rem;
      color: #3b4a60;
      line-height: 1.6;
      margin-bottom: 1rem;
      max-height: 4.5em;
      overflow: hidden;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
    }

    .book-meta {
      display: inline-block;
      background-color: #d9f1ff;
      color: #0a3d62;
      padding: 4px 10px;
      font-size: 0.85rem;
      font-weight: 500;
      border-radius: 12px;
      margin-bottom: 8px;
    }

    .btn-group {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn-group > .btn {
      background-color: #0a3d62;
      color: #ffffff;
      font-weight: 600;
      font-size: 0.85rem;
      padding: 0.4rem 1rem;
      border-radius: 8px;
      border: none;
      box-shadow: 0 4px 10px rgba(10, 61, 98, 0.2);
      transition: all 0.25s ease;
    }

    .btn-group > .btn:hover {
      background-color: #14507a;
      transform: translateY(-1px);
    }

    @media (max-width: 576px) {
      .card-img-top {
        height: 140px;
      }
      .card-title {
        font-size: 1.1rem;
      }
      .card-text.description {
        font-size: 0.9rem;
        max-height: 3.6em;
      }
      .btn-group > .btn {
        font-size: 0.7rem;
        padding: 0.15rem 0.5rem;
        min-width: 40px;
      }
    }
  </style>
</head>
<body>

  <h1>📚 Explore Books</h1>

  <form method="GET" class="row g-3 mb-5 align-items-end justify-content-center">
    <div class="col-md-5">
      <label for="book_name" class="form-label">Search by Book Name</label>
      <input type="text" id="book_name" name="book_name" value="<?= htmlspecialchars($book_name) ?>" class="form-control" placeholder="Enter book title...">
    </div>
    <div class="col-md-4">
      <label for="sort" class="form-label">Sort By</label>
      <select id="sort" name="sort" class="form-select">
        <option value="">Newest</option>
        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
        <option value="rating_desc" <?= $sort === 'rating_desc' ? 'selected' : '' ?>>Rating: High to Low</option>
      </select>
    </div>
    <div class="col-md-2 text-end">
      <a href="career_suggestions.php" class="btn btn-secondary w-100">Back</a>
    </div>
  </form>

  <?php if ($result->num_rows === 0): ?>
    <p class="text-center fs-5 text-muted">No books found matching your criteria.</p>
  <?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <?php while ($book = $result->fetch_assoc()): ?>
        <div class="col">
          <div class="card h-100 shadow-sm book-card">
            <?php if (!empty($book['image']) && file_exists($book['image'])): ?>
              <img src="<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="card-img-top" />
            <?php else: ?>
              <img src="uploads/books/default.png" alt="No Image" class="card-img-top" />
            <?php endif; ?>

            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
              <p class="card-text description"><?= htmlspecialchars(substr($book['description'], 0, 120)) ?>...</p>
              <p class="book-meta mb-3"><strong>Price:</strong> $<?= number_format($book['price'], 2) ?></p>

              <div class="btn-group mt-auto">
                <a href="book_details.php?id=<?= $book['id'] ?>" class="btn btn-outline-primary btn-sm">View Details</a>

                <?php if (!empty($book['pdf']) && file_exists($book['pdf'])): ?>
                  <a href="<?= htmlspecialchars($book['pdf']) ?>" target="_blank" class="btn btn-outline-info btn-sm">View PDF</a>
                <?php endif; ?>

                <?php if ($user_email): ?>
                  <form method="POST" action="wishlist_action.php" class="d-inline">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <button type="submit" name="add_wishlist" class="btn btn-outline-success btn-sm">+ Wishlist</button>
                  </form>
                  <form method="POST" action="order_book.php" class="d-inline">
                    <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                    <button type="submit" class="btn btn-outline-warning btn-sm">Buy Book</button>
                  </form>
                  <!-- Added PayPal checkout link below -->
                  <a href="checkout.php?book_id=<?= $book['id'] ?>" class="btn btn-outline-warning btn-sm">Buy Book (PayPal)</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
