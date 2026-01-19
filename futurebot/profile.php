<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$user_email = $_SESSION['email'];
$user_data = [];
$success = '';
$error = '';

// Check and update database schema if needed
$required_columns = ['website', 'phone', 'location', 'bio', 'skills', 'institution', 'full_name', 'role'];
foreach ($required_columns as $column) {
    $check_column = $conn->query("SHOW COLUMNS FROM Users LIKE '$column'");
    if ($check_column->num_rows == 0) {
        // Add missing column based on type
        if (in_array($column, ['website', 'location', 'bio', 'skills', 'institution', 'full_name'])) {
            $alter_sql = "ALTER TABLE Users ADD COLUMN $column VARCHAR(255) DEFAULT ''";
        } elseif ($column == 'phone') {
            $alter_sql = "ALTER TABLE Users ADD COLUMN $column VARCHAR(20) DEFAULT ''";
        } elseif ($column == 'role') {
            $alter_sql = "ALTER TABLE Users ADD COLUMN $column ENUM('student', 'instructor', 'admin') DEFAULT 'student'";
        }
        
        if (isset($alter_sql) && !$conn->query($alter_sql)) {
            $error = "Error adding $column column: " . $conn->error;
        }
    }
}

// Create hidden_buddies table if it doesn't exist
$check_table = $conn->query("SHOW TABLES LIKE 'hidden_buddies'");
if ($check_table->num_rows == 0) {
    $create_table = "CREATE TABLE hidden_buddies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL,
        hidden_user_email VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_email) REFERENCES Users(email) ON DELETE CASCADE,
        FOREIGN KEY (hidden_user_email) REFERENCES Users(email) ON DELETE CASCADE,
        UNIQUE KEY unique_hidden (user_email, hidden_user_email)
    )";
    if (!$conn->query($create_table)) {
        $error = "Error creating hidden_buddies table: " . $conn->error;
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update profile information
        $full_name = $_POST['full_name'] ?? '';
        $institution = $_POST['institution'] ?? '';
        $skills = $_POST['skills'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $location = $_POST['location'] ?? '';
        $website = $_POST['website'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // Build dynamic UPDATE query based on available columns
        $update_fields = [];
        $update_types = '';
        $update_values = [];
        
        $columns = [
            'full_name' => $full_name,
            'institution' => $institution,
            'skills' => $skills,
            'bio' => $bio,
            'location' => $location,
            'website' => $website,
            'phone' => $phone
        ];
        
        foreach ($columns as $column => $value) {
            $check_column = $conn->query("SHOW COLUMNS FROM Users LIKE '$column'");
            if ($check_column->num_rows > 0) {
                $update_fields[] = "$column = ?";
                $update_types .= "s";
                $update_values[] = $value;
            }
        }
        
        if (!empty($update_fields)) {
            $update_values[] = $user_email;
            $sql = "UPDATE Users SET " . implode(', ', $update_fields) . " WHERE email = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param($update_types . "s", ...$update_values);
                if ($stmt->execute()) {
                    $success = "Profile updated successfully!";
                    // Refresh user data
                    $user_data = array_merge($user_data, $columns);
                } else {
                    $error = "Error updating profile: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
    
    if (isset($_POST['add_course'])) {
        // Add new course
        $course_name = $_POST['course_name'] ?? '';
        $status = $_POST['status'] ?? 'running';
        $progress = $status === 'running' ? ($_POST['progress'] ?? 0) : 100;
        $completion_date = $status === 'completed' ? ($_POST['completion_date'] ?? date('Y-m-d')) : null;
        
        $sql = "INSERT INTO user_courses (user_email, course_name, status, progress, completion_date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssis", $user_email, $course_name, $status, $progress, $completion_date);
            if ($stmt->execute()) {
                $success = "Course added successfully!";
            } else {
                $error = "Error adding course: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    if (isset($_POST['update_course'])) {
        // Update course
        $course_id = $_POST['course_id'] ?? '';
        $progress = $_POST['progress'] ?? 0;
        $status = $_POST['status'] ?? 'running';
        $completion_date = $status === 'completed' ? ($_POST['completion_date'] ?? date('Y-m-d')) : null;
        
        $sql = "UPDATE user_courses SET progress = ?, status = ?, completion_date = ? WHERE id = ? AND user_email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("issis", $progress, $status, $completion_date, $course_id, $user_email);
            if ($stmt->execute()) {
                $success = "Course updated successfully!";
            } else {
                $error = "Error updating course: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    if (isset($_POST['delete_course'])) {
        // Delete course
        $course_id = $_POST['course_id'] ?? '';
        
        $sql = "DELETE FROM user_courses WHERE id = ? AND user_email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("is", $course_id, $user_email);
            if ($stmt->execute()) {
                $success = "Course deleted successfully!";
            } else {
                $error = "Error deleting course: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    if (isset($_POST['add_certificate'])) {
        // Add new certificate
        $certificate_name = $_POST['certificate_name'] ?? '';
        $certificate_url = $_POST['certificate_url'] ?? '';
        $issue_date = $_POST['issue_date'] ?? date('Y-m-d');
        
        $sql = "INSERT INTO user_certificates (user_email, certificate_name, certificate_url, issue_date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssss", $user_email, $certificate_name, $certificate_url, $issue_date);
            if ($stmt->execute()) {
                $success = "Certificate added successfully!";
            } else {
                $error = "Error adding certificate: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    if (isset($_POST['delete_certificate'])) {
        // Delete certificate
        $certificate_id = $_POST['certificate_id'] ?? '';
        
        $sql = "DELETE FROM user_certificates WHERE id = ? AND user_email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("is", $certificate_id, $user_email);
            if ($stmt->execute()) {
                $success = "Certificate deleted successfully!";
            } else {
                $error = "Error deleting certificate: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    if (isset($_POST['create_post'])) {
        // Handle post creation
        $post_content = $_POST['post_content'] ?? '';
        
        // Check if posts table exists
        $check_table = $conn->query("SHOW TABLES LIKE 'user_posts'");
        if ($check_table->num_rows == 0) {
            $create_table = "CREATE TABLE user_posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_email VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                post_type ENUM('text', 'achievement', 'course_completion') DEFAULT 'text',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_email) REFERENCES Users(email) ON DELETE CASCADE
            )";
            if (!$conn->query($create_table)) {
                $error = "Error creating posts table: " . $conn->error;
            }
        }
        
        if (!empty($post_content)) {
            $sql = "INSERT INTO user_posts (user_email, content) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ss", $user_email, $post_content);
                if ($stmt->execute()) {
                    $success = "Post shared successfully!";
                } else {
                    $error = "Error creating post: " . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            $error = "Please write something to share!";
        }
    }
    
    if (isset($_POST['connect_user'])) {
        // Handle user connection request
        $target_user_email = $_POST['target_user_email'] ?? '';
        
        // Check if connections table exists
        $check_table = $conn->query("SHOW TABLES LIKE 'user_connections'");
        if ($check_table->num_rows == 0) {
            $create_table = "CREATE TABLE user_connections (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_email VARCHAR(255) NOT NULL,
                connected_user_email VARCHAR(255) NOT NULL,
                status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_email) REFERENCES Users(email) ON DELETE CASCADE,
                FOREIGN KEY (connected_user_email) REFERENCES Users(email) ON DELETE CASCADE,
                UNIQUE KEY unique_connection (user_email, connected_user_email)
            )";
            if (!$conn->query($create_table)) {
                $error = "Error creating connections table: " . $conn->error;
            }
        }
        
        if (!empty($target_user_email) && $target_user_email != $user_email) {
            // Check if connection already exists
            $check_sql = "SELECT id FROM user_connections WHERE user_email = ? AND connected_user_email = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $user_email, $target_user_email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                $sql = "INSERT INTO user_connections (user_email, connected_user_email, status) VALUES (?, ?, 'pending')";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ss", $user_email, $target_user_email);
                    if ($stmt->execute()) {
                        $success = "Connection request sent!";
                    } else {
                        $error = "Error sending connection request: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                $error = "Connection request already sent!";
            }
            $check_stmt->close();
        }
    }
    
    if (isset($_POST['hide_buddy'])) {
        // Handle hiding a study buddy
        $hidden_user_email = $_POST['hidden_user_email'] ?? '';
        
        if (!empty($hidden_user_email) && $hidden_user_email != $user_email) {
            // Check if already hidden
            $check_sql = "SELECT id FROM hidden_buddies WHERE user_email = ? AND hidden_user_email = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $user_email, $hidden_user_email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                $sql = "INSERT INTO hidden_buddies (user_email, hidden_user_email) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("ss", $user_email, $hidden_user_email);
                    if ($stmt->execute()) {
                        $success = "Buddy hidden successfully!";
                    } else {
                        $error = "Error hiding buddy: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                $success = "Buddy already hidden!";
            }
            $check_stmt->close();
        }
    }
}

// Fetch user data - dynamically build SELECT based on available columns
$select_columns = [];
$all_columns = ['full_name', 'institution', 'skills', 'bio', 'location', 'website', 'phone', 'role'];

foreach ($all_columns as $column) {
    $check_column = $conn->query("SHOW COLUMNS FROM Users LIKE '$column'");
    if ($check_column->num_rows > 0) {
        $select_columns[] = $column;
    }
}

if (!empty($select_columns)) {
    $sql = "SELECT " . implode(', ', $select_columns) . " FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error = "Database error: " . $conn->error;
    }
} else {
    $error = "No valid columns found in Users table";
}

// Check if user_courses table exists, if not create it
$check_table = $conn->query("SHOW TABLES LIKE 'user_courses'");
if ($check_table->num_rows == 0) {
    $create_table = "CREATE TABLE user_courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL,
        course_name VARCHAR(255) NOT NULL,
        status ENUM('running', 'completed') NOT NULL,
        progress INT DEFAULT 0,
        enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        completion_date DATE NULL,
        FOREIGN KEY (user_email) REFERENCES Users(email) ON DELETE CASCADE
    )";
    if (!$conn->query($create_table)) {
        $error = "Error creating courses table: " . $conn->error;
    }
}

// Check if user_certificates table exists, if not create it
$check_table = $conn->query("SHOW TABLES LIKE 'user_certificates'");
if ($check_table->num_rows == 0) {
    $create_table = "CREATE TABLE user_certificates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL,
        certificate_name VARCHAR(255) NOT NULL,
        certificate_url VARCHAR(500) NULL,
        issue_date DATE NOT NULL,
        FOREIGN KEY (user_email) REFERENCES Users(email) ON DELETE CASCADE
    )";
    if (!$conn->query($create_table)) {
        $error = "Error creating certificates table: " . $conn->error;
    }
}

// Fetch courses from database (with IDs)
$running_courses = [];
$completed_courses = [];
$sql_courses = "SELECT id, course_name, progress, status, completion_date FROM user_courses WHERE user_email = ? ORDER BY status, enrollment_date DESC";
$stmt_courses = $conn->prepare($sql_courses);
if ($stmt_courses) {
    $stmt_courses->bind_param("s", $user_email);
    $stmt_courses->execute();
    $result_courses = $stmt_courses->get_result();
    while ($row = $result_courses->fetch_assoc()) {
        if ($row['status'] === 'running') {
            $running_courses[] = $row;
        } else {
            $completed_courses[] = $row;
        }
    }
    $stmt_courses->close();
}

// Fetch certificates from database (with IDs)
$certificates = [];
$sql_certificates = "SELECT id, certificate_name, issue_date, certificate_url FROM user_certificates WHERE user_email = ? ORDER BY issue_date DESC";
$stmt_certificates = $conn->prepare($sql_certificates);
if ($stmt_certificates) {
    $stmt_certificates->bind_param("s", $user_email);
    $stmt_certificates->execute();
    $result_certificates = $stmt_certificates->get_result();
    while ($row = $result_certificates->fetch_assoc()) {
        $certificates[] = $row;
    }
    $stmt_certificates->close();
}

// Fetch study partners (other students) excluding hidden ones
$study_partners = [];
$sql_partners = "SELECT u.email, u.full_name, u.institution, u.skills, u.bio 
                 FROM Users u 
                 WHERE u.role = 'student' 
                 AND u.email != ? 
                 AND u.email NOT IN (
                     SELECT hidden_user_email 
                     FROM hidden_buddies 
                     WHERE user_email = ?
                 )
                 LIMIT 3";
$stmt_partners = $conn->prepare($sql_partners);
if ($stmt_partners) {
    $stmt_partners->bind_param("ss", $user_email, $user_email);
    $stmt_partners->execute();
    $result_partners = $stmt_partners->get_result();
    while ($row = $result_partners->fetch_assoc()) {
        $study_partners[] = $row;
    }
    $stmt_partners->close();
}

// Fetch user posts for activity feed
$user_posts = [];
$check_table = $conn->query("SHOW TABLES LIKE 'user_posts'");
if ($check_table->num_rows > 0) {
    $sql_posts = "SELECT up.*, u.full_name FROM user_posts up 
                 JOIN Users u ON up.user_email = u.email 
                 WHERE up.user_email = ? 
                 ORDER BY up.created_at DESC 
                 LIMIT 5";
    $stmt_posts = $conn->prepare($sql_posts);
    if ($stmt_posts) {
        $stmt_posts->bind_param("s", $user_email);
        $stmt_posts->execute();
        $result_posts = $stmt_posts->get_result();
        while ($row = $result_posts->fetch_assoc()) {
            $user_posts[] = $row;
        }
        $stmt_posts->close();
    }
}

$conn->close();

// Calculate stats
$total_courses = count($running_courses) + count($completed_courses);
$completion_rate = $total_courses > 0 ? round((count($completed_courses) / $total_courses) * 100) : 0;
$user_skills = !empty($user_data['skills']) ? array_filter(array_map('trim', explode(',', $user_data['skills']))) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($user_data['full_name'] ?? 'User') ?> - FutureBot</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
  /* Previous CSS styles remain the same, just adding new styles for activity feed */
  
  .post-card {
    background: #ffffff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    border: 1px solid #e4e6eb;
    transition: all 0.3s ease;
  }

  .post-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
  }

  .post-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
  }

  .post-user {
    font-weight: 600;
    color: #1c1e21;
  }

  .post-time {
    color: #65676b;
    font-size: 0.85rem;
  }

  .post-content {
    color: #1c1e21;
    line-height: 1.5;
    margin-bottom: 15px;
  }

  .post-actions {
    display: flex;
    gap: 15px;
    border-top: 1px solid #e4e6eb;
    padding-top: 12px;
  }

  .post-action {
    display: flex;
    align-items: center;
    gap: 5px;
    background: none;
    border: none;
    color: #65676b;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 5px;
    transition: all 0.3s ease;
  }

  .post-action:hover {
    background: #f0f2f5;
    color: #4361ee;
  }

  /* Connection buttons */
  .connect-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .connect-btn:hover {
    background: #218838;
  }

  .connected-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    cursor: default;
  }

  /* Hide buddy button */
  .hide-buddy-btn {
    background: none;
    border: none;
    color: #dc3545;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    transition: all 0.3s ease;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .hide-buddy-btn:hover {
    background: #dc3545;
    color: white;
  }

  /* Alert close button */
  .close-alert {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    font-size: 1.2rem;
    margin-left: 15px;
    opacity: 0.7;
    transition: opacity 0.3s ease;
  }

  .close-alert:hover {
    opacity: 1;
  }

  /* Alert animations */
  .alert {
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.5s ease;
  }

  .alert.fade-out {
    opacity: 0;
    transform: translateY(-20px);
    margin-bottom: 0;
    padding-top: 0;
    padding-bottom: 0;
    height: 0;
    overflow: hidden;
  }

  .alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }

  .alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  }

  /* Rest of the CSS remains the same as previous version */
  * { 
    box-sizing: border-box; 
    margin:0; 
    padding:0; 
  }
  html, body {
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f2f5;
    color: #1c1e21;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  /* Navigation */
  nav {
    width: 100%;
    padding: 12px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    z-index: 1000;
    border-bottom: 1px solid #dddfe2;
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
    background: #4361ee;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
  }
  nav .nav-buttons button:hover {
    background: #3a0ca3;
    transform: translateY(-1px);
  }

  /* Main Content */
  .main-content {
    display: flex;
    width: 100%;
    max-width: 1200px;
    margin-top: 80px;
    padding: 20px;
    gap: 20px;
  }

  /* Left Sidebar */
  .left-sidebar {
    flex: 1;
    max-width: 300px;
  }

  /* Profile Card */
  .profile-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 20px;
  }

  .profile-cover {
    height: 120px;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    position: relative;
  }

  .profile-picture {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    border: 4px solid #ffffff;
    position: absolute;
    bottom: -50px;
    left: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }

  .profile-info {
    padding: 60px 20px 20px;
  }

  .profile-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1c1e21;
    margin-bottom: 5px;
  }

  .profile-title {
    color: #65676b;
    font-size: 0.95rem;
    margin-bottom: 15px;
  }

  .profile-stats {
    display: flex;
    justify-content: space-between;
    border-top: 1px solid #e4e6eb;
    padding: 15px 0;
  }

  .stat {
    text-align: center;
  }

  .stat-number {
    display: block;
    font-size: 1.2rem;
    font-weight: 700;
    color: #4361ee;
  }

  .stat-label {
    font-size: 0.85rem;
    color: #65676b;
  }

  /* About Card */
  .about-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px;
  }

  .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
  }

  .card-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1c1e21;
  }

  .about-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    color: #65676b;
  }

  .about-item i {
    width: 20px;
    color: #4361ee;
  }

  .skills-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
  }

  .skill-tag {
    background: #e7f3ff;
    color: #4361ee;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
  }

  /* Main Feed */
  .main-feed {
    flex: 2;
    max-width: 680px;
  }

  /* Create Post */
  .create-post {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px;
  }

  .post-input {
    width: 100%;
    border: 1px solid #e4e6eb;
    border-radius: 20px;
    padding: 12px 20px;
    font-size: 1rem;
    margin-bottom: 15px;
    resize: none;
    height: 60px;
  }

  .post-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .action-buttons {
    display: flex;
    gap: 15px;
  }

  .action-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 15px;
    border: none;
    background: #f0f2f5;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #65676b;
    font-weight: 500;
  }

  .action-btn:hover {
    background: #e4e6eb;
  }

  .post-btn {
    background: #4361ee;
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 20px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .post-btn:hover {
    background: #3a0ca3;
  }

  /* Feed Content */
  .feed-tabs {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
  }

  .tab-header {
    display: flex;
    border-bottom: 1px solid #e4e6eb;
  }

  .tab-btn {
    flex: 1;
    padding: 15px;
    text-align: center;
    background: none;
    border: none;
    cursor: pointer;
    font-weight: 600;
    color: #65676b;
    transition: all 0.3s ease;
    border-bottom: 3px solid transparent;
  }

  .tab-btn.active {
    color: #4361ee;
    border-bottom-color: #4361ee;
  }

  .tab-content {
    display: none;
    padding: 20px;
  }

  .tab-content.active {
    display: block;
  }

  /* Course Card */
  .course-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    border: 1px solid #e4e6eb;
    transition: all 0.3s ease;
    position: relative;
  }

  .course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .course-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
  }

  .course-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1c1e21;
  }

  .course-status {
    font-size: 0.85rem;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 600;
  }

  .status-running {
    background: #fff3cd;
    color: #856404;
  }

  .status-completed {
    background: #d1ecf1;
    color: #0c5460;
  }

  .progress-bar {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 10px;
    margin: 10px 0;
    overflow: hidden;
  }

  .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    border-radius: 10px;
    transition: width 1s ease-in-out;
  }

  .course-meta {
    display: flex;
    justify-content: space-between;
    color: #65676b;
    font-size: 0.9rem;
    align-items: center;
  }

  /* Certificate Card */
  .certificate-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    border: 1px solid #e4e6eb;
    transition: all 0.3s ease;
    position: relative;
  }

  .certificate-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .certificate-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }

  .certificate-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1c1e21;
  }

  .certificate-date {
    color: #65676b;
    font-size: 0.9rem;
  }

  .certificate-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
  }

  .btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.9rem;
  }

  .btn-primary {
    background: #4361ee;
    color: white;
  }

  .btn-primary:hover {
    background: #3a0ca3;
  }

  .btn-outline {
    background: transparent;
    color: #4361ee;
    border: 1px solid #4361ee;
  }

  .btn-outline:hover {
    background: #4361ee;
    color: white;
  }

  .btn-success {
    background: #28a745;
    color: white;
  }

  .btn-success:hover {
    background: #218838;
  }

  .btn-danger {
    background: #dc3545;
    color: white;
  }

  .btn-danger:hover {
    background: #c82333;
  }

  .btn-sm {
    padding: 5px 10px;
    font-size: 0.8rem;
  }

  /* Right Sidebar */
  .right-sidebar {
    flex: 1;
    max-width: 300px;
  }

  /* Stats Card */
  .stats-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px;
  }

  .stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e4e6eb;
  }

  .stat-item:last-child {
    border-bottom: none;
  }

  .stat-info {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e7f3ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4361ee;
  }

  .stat-text {
    font-weight: 500;
    color: #1c1e21;
  }

  .stat-value {
    font-weight: 700;
    color: #4361ee;
  }

  /* Friends Card */
  .friends-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 20px;
  }

  .friend-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #e4e6eb;
    position: relative;
  }

  .friend-item:last-child {
    border-bottom: none;
  }

  .friend-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4361ee, #3a0ca3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
  }

  .friend-name {
    font-weight: 500;
    color: #1c1e21;
  }

  .friend-title {
    font-size: 0.85rem;
    color: #65676b;
  }

  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #65676b;
  }

  .empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #bdc3c7;
  }

  .empty-state p {
    margin-bottom: 20px;
  }

  /* Modal */
  .modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 2000;
    align-items: center;
    justify-content: center;
  }

  .modal-content {
    background: white;
    border-radius: 12px;
    padding: 30px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
  }

  .modal-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e4e6eb;
  }

  .modal-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1c1e21;
  }

  .close-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #65676b;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #1c1e21;
  }

  .form-control {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e4e6eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: border 0.3s ease;
  }

  .form-control:focus {
    outline: none;
    border-color: #4361ee;
  }

  .form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 25px;
  }

  /* Edit buttons */
  .edit-buttons {
    position: absolute;
    top: 15px;
    right: 15px;
    display: flex;
    gap: 5px;
  }

  .edit-btn {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #4361ee;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  .edit-btn:hover {
    background: #4361ee;
    color: white;
    transform: scale(1.1);
  }

  /* Responsive Design */
  @media (max-width: 1024px) {
    .main-content {
      flex-direction: column;
    }
    
    .left-sidebar, .right-sidebar {
      max-width: 100%;
    }
    
    .profile-stats {
      justify-content: space-around;
    }
  }

  @media (max-width: 768px) {
    nav {
      padding: 12px 20px;
    }
    
    .main-content {
      padding: 15px;
      margin-top: 70px;
    }
    
    .profile-picture {
      width: 80px;
      height: 80px;
      bottom: -40px;
      font-size: 2rem;
    }
    
    .profile-info {
      padding: 50px 15px 15px;
    }
    
    .tab-header {
      flex-direction: column;
    }
    
    .tab-btn {
      padding: 12px;
    }

    .modal-content {
      width: 95%;
      padding: 20px;
    }
  }

  @media (max-width: 480px) {
    .nav-buttons {
      flex-direction: column;
      gap: 5px;
    }
    
    .nav-buttons button {
      padding: 8px 15px;
      font-size: 0.9rem;
    }
    
    .profile-cover {
      height: 100px;
    }
    
    .post-actions {
      flex-direction: column;
      gap: 10px;
    }
    
    .action-buttons {
      width: 100%;
      justify-content: space-between;
    }

    .edit-buttons {
      position: static;
      justify-content: flex-end;
      margin-bottom: 10px;
    }
  }
  </style>
</head>
<body>
  <!-- Navigation -->
  <nav>
    <div class="logo">
      <i class="fas fa-robot"></i>FutureBot
    </div>
    <div class="nav-buttons">
      <button onclick="location.href='dashboard.php'"><i class="fas fa-home"></i> Home</button>
      <button onclick="location.href='career_suggestions.php'"><i class="fas fa-briefcase"></i> Career</button>
      <button onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </div>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Left Sidebar -->
    <div class="left-sidebar">
      <!-- Profile Card -->
      <div class="profile-card">
        <div class="profile-cover">
          <div class="profile-picture">
            <i class="fas fa-user"></i>
          </div>
        </div>
        <div class="profile-info">
          <div class="profile-name"><?= htmlspecialchars($user_data['full_name'] ?? 'User') ?></div>
          <div class="profile-title">Student at <?= htmlspecialchars($user_data['institution'] ?? 'FutureBot Academy') ?></div>
          <div class="profile-stats">
            <div class="stat">
              <span class="stat-number"><?= $total_courses ?></span>
              <span class="stat-label">Courses</span>
            </div>
            <div class="stat">
              <span class="stat-number"><?= count($certificates) ?></span>
              <span class="stat-label">Certificates</span>
            </div>
            <div class="stat">
              <span class="stat-number"><?= $completion_rate ?>%</span>
              <span class="stat-label">Completion</span>
            </div>
          </div>
          <button class="btn btn-primary" style="width: 100%;" onclick="openModal('editProfileModal')">
            <i class="fas fa-edit"></i> Edit Profile
          </button>
        </div>
      </div>

      <!-- About Card -->
      <div class="about-card">
        <div class="card-header">
          <div class="card-title">About</div>
          <button class="edit-btn" onclick="openModal('editProfileModal')">
            <i class="fas fa-edit"></i>
          </button>
        </div>
        <div class="about-item">
          <i class="fas fa-graduation-cap"></i>
          <span><?= htmlspecialchars($user_data['institution'] ?? 'Not specified') ?></span>
        </div>
        <div class="about-item">
          <i class="fas fa-map-marker-alt"></i>
          <span><?= htmlspecialchars($user_data['location'] ?? 'Location not set') ?></span>
        </div>
        <div class="about-item">
          <i class="fas fa-globe"></i>
          <span><?= htmlspecialchars($user_data['website'] ?? 'No website') ?></span>
        </div>
        <div class="about-item">
          <i class="fas fa-phone"></i>
          <span><?= htmlspecialchars($user_data['phone'] ?? 'Phone not set') ?></span>
        </div>
        <?php if(!empty($user_data['bio'])): ?>
        <div class="about-item">
          <i class="fas fa-info-circle"></i>
          <span><?= htmlspecialchars($user_data['bio']) ?></span>
        </div>
        <?php endif; ?>
        
        <div class="card-title" style="margin-top: 20px;">Skills</div>
        <div class="skills-container">
          <?php if(!empty($user_skills)): ?>
            <?php foreach($user_skills as $skill): ?>
              <div class="skill-tag"><?= htmlspecialchars($skill) ?></div>
            <?php endforeach; ?>
          <?php else: ?>
            <div style="color: #65676b; font-size: 0.9rem;">No skills added yet</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Main Feed -->
    <div class="main-feed">
      <?php if($success): ?>
        <div class="alert alert-success" id="successAlert">
          <?= htmlspecialchars($success) ?>
          <button class="close-alert" onclick="closeAlert('successAlert')">&times;</button>
        </div>
      <?php endif; ?>
      
      <?php if($error): ?>
        <div class="alert alert-error" id="errorAlert">
          <?= htmlspecialchars($error) ?>
          <button class="close-alert" onclick="closeAlert('errorAlert')">&times;</button>
        </div>
      <?php endif; ?>

      <!-- Create Post -->
      <div class="create-post">
        <form method="POST" action="">
          <textarea class="post-input" name="post_content" placeholder="Share your learning progress..."></textarea>
          <div class="post-actions">
            <div class="action-buttons">
              <button type="button" class="action-btn">
                <i class="fas fa-image"></i> Photo
              </button>
              <button type="button" class="action-btn">
                <i class="fas fa-video"></i> Video
              </button>
              <button type="button" class="action-btn">
                <i class="fas fa-graduation-cap"></i> Achievement
              </button>
            </div>
            <button type="submit" class="post-btn" name="create_post">Post</button>
          </div>
        </form>
      </div>

      <!-- Feed Content -->
      <div class="feed-tabs">
        <div class="tab-header">
          <button class="tab-btn active" onclick="switchTab('courses')">Courses</button>
          <button class="tab-btn" onclick="switchTab('certificates')">Certificates</button>
          <button class="tab-btn" onclick="switchTab('activity')">Activity</button>
        </div>

        <!-- Courses Tab -->
        <div class="tab-content active" id="courses-tab">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: #1c1e21; margin: 0;">My Courses</h3>
            <button class="btn btn-primary" onclick="openModal('addCourseModal')">
              <i class="fas fa-plus"></i> Add Course
            </button>
          </div>
          
          <?php if(!empty($running_courses) || !empty($completed_courses)): ?>
            <?php if(!empty($running_courses)): ?>
              <h3 style="margin-bottom: 15px; color: #1c1e21;">In Progress</h3>
              <?php foreach($running_courses as $course): ?>
                <div class="course-card">
                  <div class="edit-buttons">
                    <button class="edit-btn" onclick="editCourse(<?= $course['id'] ?>, '<?= htmlspecialchars($course['course_name']) ?>', <?= $course['progress'] ?>, '<?= $course['status'] ?>')">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="edit-btn" style="color: #dc3545;" onclick="deleteCourse(<?= $course['id'] ?>, '<?= htmlspecialchars($course['course_name']) ?>')">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                  <div class="course-header">
                    <div class="course-title"><?= htmlspecialchars($course['course_name']) ?></div>
                    <div class="course-status status-running">In Progress</div>
                  </div>
                  <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= htmlspecialchars($course['progress'] ?? 0) ?>%"></div>
                  </div>
                  <div class="course-meta">
                    <span><?= htmlspecialchars($course['progress'] ?? 0) ?>% Complete</span>
                    <button class="btn btn-outline btn-sm" onclick="editCourse(<?= $course['id'] ?>, '<?= htmlspecialchars($course['course_name']) ?>', <?= $course['progress'] ?>, '<?= $course['status'] ?>')">
                      <i class="fas fa-edit"></i> Update Progress
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>

            <?php if(!empty($completed_courses)): ?>
              <h3 style="margin: 25px 0 15px; color: #1c1e21;">Completed</h3>
              <?php foreach($completed_courses as $course): ?>
                <div class="course-card">
                  <div class="edit-buttons">
                    <button class="edit-btn" onclick="editCourse(<?= $course['id'] ?>, '<?= htmlspecialchars($course['course_name']) ?>', <?= $course['progress'] ?>, '<?= $course['status'] ?>', '<?= $course['completion_date'] ?>')">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button class="edit-btn" style="color: #dc3545;" onclick="deleteCourse(<?= $course['id'] ?>, '<?= htmlspecialchars($course['course_name']) ?>')">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                  <div class="course-header">
                    <div class="course-title"><?= htmlspecialchars($course['course_name']) ?></div>
                    <div class="course-status status-completed">Completed</div>
                  </div>
                  <div class="course-meta">
                    <span>Completed on <?= htmlspecialchars($course['completion_date'] ?? 'Unknown date') ?></span>
                    <button class="btn btn-outline btn-sm">
                      <i class="fas fa-redo"></i> Review
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-book-open"></i>
              <p>No courses yet</p>
              <p>Start your learning journey by enrolling in courses</p>
              <button class="btn btn-primary" onclick="openModal('addCourseModal')">
                <i class="fas fa-plus"></i> Add Your First Course
              </button>
            </div>
          <?php endif; ?>
        </div>

        <!-- Certificates Tab -->
        <div class="tab-content" id="certificates-tab">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="color: #1c1e21; margin: 0;">My Certificates</h3>
            <button class="btn btn-primary" onclick="openModal('addCertificateModal')">
              <i class="fas fa-plus"></i> Add Certificate
            </button>
          </div>
          
          <?php if(!empty($certificates)): ?>
            <?php foreach($certificates as $certificate): ?>
              <div class="certificate-card">
                <div class="edit-buttons">
                  <button class="edit-btn" style="color: #dc3545;" onclick="deleteCertificate(<?= $certificate['id'] ?>, '<?= htmlspecialchars($certificate['certificate_name']) ?>')">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
                <div class="certificate-header">
                  <div class="certificate-title"><?= htmlspecialchars($certificate['certificate_name']) ?></div>
                  <div class="certificate-date">Issued: <?= htmlspecialchars($certificate['issue_date']) ?></div>
                </div>
                <p style="color: #65676b; margin-bottom: 15px;">Congratulations on completing this course!</p>
                <div class="certificate-actions">
                  <?php if(isset($certificate['certificate_url']) && !empty($certificate['certificate_url'])): ?>
                    <a href="<?= htmlspecialchars($certificate['certificate_url']) ?>" target="_blank" class="btn btn-primary">
                      <i class="fas fa-download"></i> Download
                    </a>
                  <?php endif; ?>
                  <button class="btn btn-outline">
                    <i class="fas fa-share"></i> Share
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-certificate"></i>
              <p>No certificates yet</p>
              <p>Complete courses to earn certificates</p>
              <button class="btn btn-primary" onclick="openModal('addCertificateModal')">
                <i class="fas fa-plus"></i> Add Your First Certificate
              </button>
            </div>
          <?php endif; ?>
        </div>

        <!-- Activity Tab -->
        <div class="tab-content" id="activity-tab">
          <?php if(!empty($user_posts)): ?>
            <?php foreach($user_posts as $post): ?>
              <div class="post-card">
                <div class="post-header">
                  <div class="post-avatar">
                    <i class="fas fa-user"></i>
                  </div>
                  <div>
                    <div class="post-user"><?= htmlspecialchars($post['full_name']) ?></div>
                    <div class="post-time"><?= date('M j, Y g:i A', strtotime($post['created_at'])) ?></div>
                  </div>
                </div>
                <div class="post-content">
                  <?= htmlspecialchars($post['content']) ?>
                </div>
                <div class="post-actions">
                  <button class="post-action">
                    <i class="far fa-thumbs-up"></i> Like
                  </button>
                  <button class="post-action">
                    <i class="far fa-comment"></i> Comment
                  </button>
                  <button class="post-action">
                    <i class="far fa-share-square"></i> Share
                  </button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-chart-line"></i>
              <p>No recent activity</p>
              <p>Your learning activities will appear here</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Right Sidebar -->
    <div class="right-sidebar">
      <!-- Stats Card -->
      <div class="stats-card">
        <div class="card-title">Learning Statistics</div>
        <div class="stat-item">
          <div class="stat-info">
            <div class="stat-icon">
              <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-text">Active Courses</div>
          </div>
          <div class="stat-value"><?= count($running_courses) ?></div>
        </div>
        <div class="stat-item">
          <div class="stat-info">
            <div class="stat-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-text">Completed</div>
          </div>
          <div class="stat-value"><?= count($completed_courses) ?></div>
        </div>
        <div class="stat-item">
          <div class="stat-info">
            <div class="stat-icon">
              <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-text">Certificates</div>
          </div>
          <div class="stat-value"><?= count($certificates) ?></div>
        </div>
        <div class="stat-item">
          <div class="stat-info">
            <div class="stat-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-text">Completion Rate</div>
          </div>
          <div class="stat-value"><?= $completion_rate ?>%</div>
        </div>
      </div>

      <!-- Study Partners Card -->
      <div class="friends-card">
        <div class="card-title">Learning Buddies</div>
        <?php if(!empty($study_partners)): ?>
          <?php foreach($study_partners as $partner): ?>
            <div class="friend-item">
              <div class="friend-avatar">
                <i class="fas fa-user"></i>
              </div>
              <div style="flex: 1;">
                <div class="friend-name"><?= htmlspecialchars($partner['full_name']) ?></div>
                <div class="friend-title"><?= htmlspecialchars($partner['institution']) ?></div>
                <?php if(!empty($partner['skills'])): ?>
                  <div style="font-size: 0.8rem; color: #4361ee; margin-top: 5px;">
                    <?= htmlspecialchars(implode(', ', array_slice(explode(',', $partner['skills']), 0, 2))) ?>
                  </div>
                <?php endif; ?>
              </div>
              <div style="display: flex; gap: 5px; align-items: center;">
                <form method="POST" action="" style="display: inline;">
                  <input type="hidden" name="target_user_email" value="<?= htmlspecialchars($partner['email']) ?>">
                  <button type="submit" class="connect-btn" name="connect_user">
                    <i class="fas fa-user-plus"></i> Connect
                  </button>
                </form>
                <form method="POST" action="" style="display: inline;">
                  <input type="hidden" name="hidden_user_email" value="<?= htmlspecialchars($partner['email']) ?>">
                  <button type="submit" class="hide-buddy-btn" name="hide_buddy" title="Hide this buddy">
                    <i class="fas fa-times"></i>
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state" style="padding: 20px 0;">
            <i class="fas fa-users"></i>
            <p>No study partners found</p>
          </div>
        <?php endif; ?>
        <a href="study_partners.php" class="btn btn-outline" style="width: 100%; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 5px;">
          <i class="fas fa-users"></i> Find More Partners
        </a>
      </div>
    </div>
  </div>

  <!-- All modals remain the same as previous version -->
  <!-- Edit Profile Modal -->
  <div class="modal" id="editProfileModal">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">Edit Profile</div>
        <button class="close-modal" onclick="closeModal('editProfileModal')">&times;</button>
      </div>
      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label" for="full_name">Full Name</label>
          <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="institution">Institution</label>
          <input type="text" class="form-control" id="institution" name="institution" value="<?= htmlspecialchars($user_data['institution'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="location">Location</label>
          <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($user_data['location'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="website">Website</label>
          <input type="url" class="form-control" id="website" name="website" value="<?= htmlspecialchars($user_data['website'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="phone">Phone</label>
          <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="skills">Skills (comma separated)</label>
          <input type="text" class="form-control" id="skills" name="skills" value="<?= htmlspecialchars($user_data['skills'] ?? '') ?>" placeholder="e.g. PHP, JavaScript, Python">
        </div>
        <div class="form-group">
          <label class="form-label" for="bio">Bio</label>
          <textarea class="form-control" id="bio" name="bio" rows="4"><?= htmlspecialchars($user_data['bio'] ?? '') ?></textarea>
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-outline" onclick="closeModal('editProfileModal')">Cancel</button>
          <button type="submit" class="btn btn-primary" name="update_profile">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Course Modal -->
  <div class="modal" id="addCourseModal">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">Add New Course</div>
        <button class="close-modal" onclick="closeModal('addCourseModal')">&times;</button>
      </div>
      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label" for="course_name">Course Name</label>
          <input type="text" class="form-control" id="course_name" name="course_name" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="status">Status</label>
          <select class="form-control" id="status" name="status" onchange="toggleProgressField()">
            <option value="running">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="form-group" id="progressGroup">
          <label class="form-label" for="progress">Progress (%)</label>
          <input type="number" class="form-control" id="progress" name="progress" min="0" max="100" value="0">
        </div>
        <div class="form-group" id="completionDateGroup" style="display: none;">
          <label class="form-label" for="completion_date">Completion Date</label>
          <input type="date" class="form-control" id="completion_date" name="completion_date" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-outline" onclick="closeModal('addCourseModal')">Cancel</button>
          <button type="submit" class="btn btn-primary" name="add_course">Add Course</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Course Modal -->
  <div class="modal" id="editCourseModal">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">Edit Course</div>
        <button class="close-modal" onclick="closeModal('editCourseModal')">&times;</button>
      </div>
      <form method="POST" action="">
        <input type="hidden" id="edit_course_id" name="course_id">
        <div class="form-group">
          <label class="form-label" for="edit_course_name">Course Name</label>
          <input type="text" class="form-control" id="edit_course_name" name="course_name" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="edit_status">Status</label>
          <select class="form-control" id="edit_status" name="status" onchange="toggleEditProgressField()">
            <option value="running">In Progress</option>
            <option value="completed">Completed</option>
          </select>
        </div>
        <div class="form-group" id="editProgressGroup">
          <label class="form-label" for="edit_progress">Progress (%)</label>
          <input type="number" class="form-control" id="edit_progress" name="progress" min="0" max="100" value="0">
        </div>
        <div class="form-group" id="editCompletionDateGroup" style="display: none;">
          <label class="form-label" for="edit_completion_date">Completion Date</label>
          <input type="date" class="form-control" id="edit_completion_date" name="completion_date">
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-outline" onclick="closeModal('editCourseModal')">Cancel</button>
          <button type="submit" class="btn btn-primary" name="update_course">Update Course</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Course Modal -->
  <div class="modal" id="deleteCourseModal">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">Delete Course</div>
        <button class="close-modal" onclick="closeModal('deleteCourseModal')">&times;</button>
      </div>
      <form method="POST" action="">
        <input type="hidden" id="delete_course_id" name="course_id">
        <p>Are you sure you want to delete the course "<span id="delete_course_name"></span>"?</p>
        <div class="form-actions">
          <button type="button" class="btn btn-outline" onclick="closeModal('deleteCourseModal')">Cancel</button>
          <button type="submit" class="btn btn-danger" name="delete_course">Delete Course</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Add Certificate Modal -->
  <div class="modal" id="addCertificateModal">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">Add Certificate</div>
        <button class="close-modal" onclick="closeModal('addCertificateModal')">&times;</button>
      </div>
      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label" for="certificate_name">Certificate Name</label>
          <input type="text" class="form-control" id="certificate_name" name="certificate_name" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="certificate_url">Certificate URL (optional)</label>
          <input type="url" class="form-control" id="certificate_url" name="certificate_url" placeholder="https://...">
        </div>
        <div class="form-group">
          <label class="form-label" for="issue_date">Issue Date</label>
          <input type="date" class="form-control" id="issue_date" name="issue_date" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="form-actions">
          <button type="button" class="btn btn-outline" onclick="closeModal('addCertificateModal')">Cancel</button>
          <button type="submit" class="btn btn-primary" name="add_certificate">Add Certificate</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Certificate Modal -->
  <div class="modal" id="deleteCertificateModal">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">Delete Certificate</div>
        <button class="close-modal" onclick="closeModal('deleteCertificateModal')">&times;</button>
      </div>
      <form method="POST" action="">
        <input type="hidden" id="delete_certificate_id" name="certificate_id">
        <p>Are you sure you want to delete the certificate "<span id="delete_certificate_name"></span>"?</p>
        <div class="form-actions">
          <button type="button" class="btn btn-outline" onclick="closeModal('deleteCertificateModal')">Cancel</button>
          <button type="submit" class="btn btn-danger" name="delete_certificate">Delete Certificate</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function switchTab(tabName) {
      // Hide all tab contents
      document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
      });
      
      // Remove active class from all tab buttons
      document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
      });
      
      // Show selected tab content
      document.getElementById(tabName + '-tab').classList.add('active');
      
      // Add active class to clicked tab button
      event.target.classList.add('active');
    }

    // Modal functions
    function openModal(modalId) {
      document.getElementById(modalId).style.display = 'flex';
    }

    function closeModal(modalId) {
      document.getElementById(modalId).style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      document.querySelectorAll('.modal').forEach(modal => {
        if (event.target === modal) {
          modal.style.display = 'none';
        }
      });
    }

    // Course form handling
    function toggleProgressField() {
      const status = document.getElementById('status').value;
      const progressGroup = document.getElementById('progressGroup');
      const completionDateGroup = document.getElementById('completionDateGroup');
      
      if (status === 'completed') {
        progressGroup.style.display = 'none';
        completionDateGroup.style.display = 'block';
        document.getElementById('progress').value = 100;
      } else {
        progressGroup.style.display = 'block';
        completionDateGroup.style.display = 'none';
      }
    }

    function toggleEditProgressField() {
      const status = document.getElementById('edit_status').value;
      const progressGroup = document.getElementById('editProgressGroup');
      const completionDateGroup = document.getElementById('editCompletionDateGroup');
      
      if (status === 'completed') {
        progressGroup.style.display = 'none';
        completionDateGroup.style.display = 'block';
        document.getElementById('edit_progress').value = 100;
      } else {
        progressGroup.style.display = 'block';
        completionDateGroup.style.display = 'none';
      }
    }

    function editCourse(id, name, progress, status, completionDate = '') {
      document.getElementById('edit_course_id').value = id;
      document.getElementById('edit_course_name').value = name;
      document.getElementById('edit_progress').value = progress;
      document.getElementById('edit_status').value = status;
      
      if (completionDate) {
        document.getElementById('edit_completion_date').value = completionDate;
      } else {
        document.getElementById('edit_completion_date').value = '<?= date('Y-m-d') ?>';
      }
      
      toggleEditProgressField();
      openModal('editCourseModal');
    }

    function deleteCourse(id, name) {
      document.getElementById('delete_course_id').value = id;
      document.getElementById('delete_course_name').textContent = name;
      openModal('deleteCourseModal');
    }

    function deleteCertificate(id, name) {
      document.getElementById('delete_certificate_id').value = id;
      document.getElementById('delete_certificate_name').textContent = name;
      openModal('deleteCertificateModal');
    }

    // Alert functions
    function closeAlert(alertId) {
      const alert = document.getElementById(alertId);
      alert.classList.add('fade-out');
      setTimeout(() => {
        alert.style.display = 'none';
      }, 500);
    }

    // Auto-hide alerts after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-hide success alerts after 3 seconds
      const successAlert = document.getElementById('successAlert');
      if (successAlert) {
        setTimeout(() => {
          closeAlert('successAlert');
        }, 3000);
      }

      // Auto-hide error alerts after 5 seconds
      const errorAlert = document.getElementById('errorAlert');
      if (errorAlert) {
        setTimeout(() => {
          closeAlert('errorAlert');
        }, 5000);
      }

      // Add animation to progress bars
      const progressBars = document.querySelectorAll('.progress-fill');
      progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
          bar.style.width = width;
        }, 300);
      });
    });
  </script>
</body>
</html>