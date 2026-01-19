<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$user_email = $_SESSION['email'];
$purchased_books = [];

// Fetch all purchased books
$sql = "SELECT b.book_id, b.title, b.author, b.price, b.description, ub.purchased_at 
        FROM User_Books ub 
        JOIN Books b ON ub.book_id = b.book_id 
        WHERE ub.user_email = ? 
        ORDER BY ub.purchased_at DESC";
        
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $purchased_books[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Books - FutureBot</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
  /* Use similar CSS as my_courses.php but adjust for books */
  * { 
    box-sizing: border-box; 
    margin:0; 
    padding:0; 
  }
  html, body {
    width: 100%;
    min-height: 100vh;
    overflow-x: hidden;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
    color: #2c3e50;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .background-animation {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    overflow: hidden;
  }

  .circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(67, 97, 238, 0.05);
    animation: float 15s infinite ease-in-out;
  }

  .circle:nth-child(1) {
    width: 80px;
    height: 80px;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
  }

  .circle:nth-child(2) {
    width: 120px;
    height: 120px;
    top: 70%;
    left: 80%;
    animation-delay: 2s;
  }

  .circle:nth-child(3) {
    width: 60px;
    height: 60px;
    top: 40%;
    left: 85%;
    animation-delay: 4s;
  }

  .circle:nth-child(4) {
    width: 100px;
    height: 100px;
    top: 80%;
    left: 15%;
    animation-delay: 6s;
  }

  .circle:nth-child(5) {
    width: 70px;
    height: 70px;
    top: 20%;
    left: 70%;
    animation-delay: 8s;
  }

  @keyframes float {
    0%, 100% { transform: translateY(0) translateX(0); }
    25% { transform: translateY(-20px) translateX(10px); }
    50% { transform: translateY(10px) translateX(-15px); }
    75% { transform: translateY(-15px) translateX(-10px); }
  }

  nav {
    width: 100%;
    padding: 15px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    position: fixed;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid rgba(67, 97, 238, 0.1);
  }
  nav .logo {
    font-size: 1.8rem;
    font-weight: bold;
    letter-spacing: 1px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  nav .logo i {
    font-size: 1.5rem;
  }
  nav .nav-buttons {
    display: flex;
    gap: 10px;
    align-items: center;
  }
  nav .nav-buttons button {
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
  }
  nav .nav-buttons button:hover {
    background: linear-gradient(135deg, #3a0ca3, #4361ee);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
  }

  .main-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 1200px;
    margin-top: 100px;
    padding: 0 20px;
    flex: 1;
  }

  .page-header {
    width: 100%;
    text-align: center;
    margin-bottom: 30px;
  }

  .page-header h1 {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 10px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .page-header p {
    color: #7f8c8d;
    font-size: 1.1rem;
  }

  .books-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    width: 100%;
    margin-bottom: 50px;
  }

  .book-card {
    background: #fff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(67, 97, 238, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    gap: 20px;
  }

  .book-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
  }

  .book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
  }

  .book-cover {
    width: 80px;
    height: 100px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    flex-shrink: 0;
  }

  .book-content {
    flex: 1;
  }

  .book-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 5px;
  }

  .book-author {
    color: #7f8c8d;
    font-size: 1rem;
    margin-bottom: 10px;
  }

  .book-price {
    font-size: 1.2rem;
    font-weight: 700;
    color: #4361ee;
    margin-bottom: 15px;
  }

  .book-description {
    color: #5a6c7d;
    margin-bottom: 20px;
    line-height: 1.5;
    font-size: 0.95rem;
  }

  .book-actions {
    display: flex;
    gap: 10px;
  }

  .btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn-primary {
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(67, 97, 238, 0.4);
  }

  .btn-outline {
    background: transparent;
    border: 1px solid #4361ee;
    color: #4361ee;
  }

  .btn-outline:hover {
    background: rgba(67, 97, 238, 0.05);
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #7f8c8d;
  }

  .empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    color: #bdc3c7;
  }

  .empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #95a5a6;
  }

  footer {
    width: 100%;
    background: rgba(255, 255, 255, 0.95);
    padding: 30px 20px;
    margin-top: 50px;
    border-top: 1px solid rgba(67, 97, 238, 0.1);
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.05);
  }

  .footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
  }

  .footer-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.5rem;
    font-weight: bold;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .footer-links {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .footer-links a {
    color: #5a6c7d;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .footer-links a:hover {
    color: #4361ee;
  }

  .footer-bottom {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid rgba(67, 97, 238, 0.1);
    width: 100%;
    color: #7f8c8d;
    font-size: 0.9rem;
  }

  @media (max-width: 768px) {
    .books-grid {
      grid-template-columns: 1fr;
    }
    
    .book-card {
      flex-direction: column;
      text-align: center;
    }
    
    .book-cover {
      align-self: center;
    }
    
    .footer-links {
      gap: 20px;
    }
  }

  @media (max-width: 500px) {
    nav { 
      flex-direction: column; 
      gap: 10px; 
      padding: 15px 20px;
    }
    
    .book-card {
      padding: 20px;
    }
    
    .book-actions {
      flex-direction: column;
    }
    
    .footer-links {
      flex-direction: column;
      gap: 15px;
    }
  }
  </style>
</head>
<body>

  <!-- Animated Background -->
  <div class="background-animation">
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
    <div class="circle"></div>
  </div>

  <!-- Navbar -->
  <nav>
    <div class="logo">
      <i class="fas fa-robot"></i>FutureBot
    </div>
    <div class="nav-buttons">
      <button onclick="location.href='dashboard.php'"><i class="fas fa-home"></i> Dashboard</button>
      <button onclick="location.href='career_suggestions.php'"><i class="fas fa-briefcase"></i> Career</button>
      <button onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <div class="page-header">
      <h1>My Books</h1>
      <p>Your purchased books and learning resources</p>
    </div>

    <?php if (empty($purchased_books)): ?>
      <div class="empty-state">
        <i class="fas fa-book"></i>
        <h3>No Books Purchased Yet</h3>
        <p>Explore our book collection to enhance your learning experience.</p>
        <a href="books.php" class="btn btn-primary" style="margin-top: 20px;">
          <i class="fas fa-shopping-cart"></i> Browse Books
        </a>
      </div>
    <?php else: ?>
      <div class="books-grid">
        <?php foreach ($purchased_books as $book): ?>
          <div class="book-card">
            <div class="book-cover">
              <i class="fas fa-book"></i>
            </div>
            <div class="book-content">
              <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>
              <p class="book-author">by <?= htmlspecialchars($book['author']) ?></p>
              <div class="book-price">$<?= $book['price'] ?></div>
              <p class="book-description"><?= htmlspecialchars($book['description'] ?? 'No description available.') ?></p>
              <div class="book-actions">
                <a href="#" class="btn btn-primary">
                  <i class="fas fa-download"></i> Download
                </a>
                <a href="#" class="btn btn-outline">
                  <i class="fas fa-eye"></i> Preview
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <footer>
    <div class="footer-content">
      <div class="footer-logo">
        <i class="fas fa-robot"></i>FutureBot
      </div>
      
      <div class="footer-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="courses.php">Courses</a>
        <a href="books.php">Books</a>
        <a href="webinars.php">Webinars</a>
        <a href="career_suggestions.php">Career Suggestions</a>
      </div>
      
      <div class="footer-bottom">
        <p>&copy; 2024 FutureBot. All rights reserved. | Empowering students with AI-driven career guidance</p>
      </div>
    </div>
  </footer>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to book cards
    const bookCards = document.querySelectorAll('.book-card');
    bookCards.forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.boxShadow = '0 15px 30px rgba(0, 0, 0, 0.12)';
      });
      
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.08)';
      });
    });
  });
  </script>
</body>
</html>