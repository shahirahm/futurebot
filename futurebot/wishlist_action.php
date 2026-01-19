<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: register.php");
    exit;
}

$user_email = $_SESSION['user_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']);

    // Check if already in wishlist
    $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_email = ? AND book_id = ?");
    $stmt->bind_param("si", $user_email, $book_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If exists, remove from wishlist (toggle)
        $stmt->close();
        $del_stmt = $conn->prepare("DELETE FROM wishlist WHERE user_email = ? AND book_id = ?");
        $del_stmt->bind_param("si", $user_email, $book_id);
        $del_stmt->execute();
        $del_stmt->close();
        $_SESSION['wishlist_msg'] = "Removed from wishlist.";
    } else {
        // If not, add to wishlist
        $stmt->close();
        $ins_stmt = $conn->prepare("INSERT INTO wishlist (user_email, book_id) VALUES (?, ?)");
        $ins_stmt->bind_param("si", $user_email, $book_id);
        $ins_stmt->execute();
        $ins_stmt->close();
        $_SESSION['wishlist_msg'] = "Added to wishlist.";
    }
} else {
    $_SESSION['wishlist_msg'] = "Invalid request.";
}

$redirect_url = $_SERVER['HTTP_REFERER'] ?? 'explore_books.php';  // fallback page if no referrer
header("Location: " . $redirect_url);
exit;
