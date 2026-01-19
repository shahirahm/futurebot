<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = $_SESSION['user_email'];

// Handle Save action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_book'])) {
    $book_title = $_POST['book_title'];
    $book_link = $_POST['book_link'];
    $thumbnail = $_POST['thumbnail'];

    $stmt = $conn->prepare("INSERT INTO saved_books (user_email, book_title, book_link, thumbnail) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user_email, $book_title, $book_link, $thumbnail);
    $stmt->execute();
}

$books = [
    [
        "title" => "Hands-On Machine Learning with Scikit-Learn and TensorFlow",
        "link" => "https://www.amazon.com/dp/1492032646",
        "thumbnail" => "https://m.media-amazon.com/images/I/71CqZnt1URL.jpg"
    ],
    [
        "title" => "Python Machine Learning",
        "link" => "https://www.amazon.com/dp/1801819311",
        "thumbnail" => "https://m.media-amazon.com/images/I/81iKgDnmj8L.jpg"
    ],
    [
        "title" => "Deep Learning with Python",
        "link" => "https://www.amazon.com/dp/1617294438",
        "thumbnail" => "https://m.media-amazon.com/images/I/71Xpsq1H6QL.jpg"
    ]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Recommended Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .book-card {
            height: 100%;
            transition: transform 0.2s;
        }
        .book-card:hover {
            transform: scale(1.03);
        }
        .search-box {
            max-width: 400px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="#">📘 FutureBot - Recommended Books</a>
</nav>

<div class="container mt-4">
    <h3 class="mb-4">📖 Books to Boost Your Career</h3>

    <input type="text" class="form-control search-box mb-4" placeholder="Search books..." id="bookSearch">

    <div class="row" id="bookList">
        <?php foreach ($books as $book): ?>
        <div class="col-md-4 mb-4 book-item">
            <div class="card book-card">
                <img src="<?= htmlspecialchars($book['thumbnail']) ?>" class="card-img-top" alt="Book Thumbnail">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                    <a href="<?= htmlspecialchars($book['link']) ?>" target="_blank" class="btn btn-primary mb-2">🔗 View on Amazon</a>
                    <form method="POST">
                        <input type="hidden" name="book_title" value="<?= htmlspecialchars($book['title']) ?>">
                        <input type="hidden" name="book_link" value="<?= htmlspecialchars($book['link']) ?>">
                        <input type="hidden" name="thumbnail" value="<?= htmlspecialchars($book['thumbnail']) ?>">
                        <button type="submit" name="save_book" class="btn btn-success w-100">💾 Save Book</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    document.getElementById('bookSearch').addEventListener('keyup', function () {
        let query = this.value.toLowerCase();
        let books = document.querySelectorAll('.book-item');
        books.forEach(function (book) {
            const title = book.querySelector('.card-title').innerText.toLowerCase();
            book.style.display = title.includes(query) ? '' : 'none';
        });
    });
</script>

</body>
</html>
