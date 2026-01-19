<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: register.php");
    exit;
}

$email = $_SESSION['user_email'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $uploadDir = 'uploads/profile_pics/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = time() . '_' . basename($_FILES['profile_picture']['name']);
    $targetFilePath = $uploadDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    
    if (in_array(strtolower($fileType), $allowTypes)) {
        // Upload file to server
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
            // Update database
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE email = ?");
            $stmt->bind_param("ss", $fileName, $email);
            
            if ($stmt->execute()) {
                $message = "Profile picture updated successfully!";
                // Update session if needed
                $_SESSION['profile_picture'] = $fileName;
            } else {
                $message = "Database update failed.";
            }
            $stmt->close();
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    } else {
        $message = 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed.';
    }
}

header("Location: career_suggestions.php?message=" . urlencode($message));
exit;
?>