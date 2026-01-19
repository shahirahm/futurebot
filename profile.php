<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$user_email = $_SESSION['email'];

// Initialize all variables
$user_data = [];
$success = '';
$error = '';
$running_courses = [];
$completed_courses = [];
$certificates = [];
$study_partners = [];
$user_posts = [];
$pending_requests = [];
$notifications = [];
$accepted_connections = [];
$unread_count = 0;
$total_courses = 0;
$completion_rate = 0;
$user_skills = [];

// Create uploads directory if it doesn't exist
$upload_dir = "uploads/profile_pictures/";
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        $error = "Failed to create upload directory. Please check permissions.";
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update Profile
    if (isset($_POST['update_profile'])) {
        $full_name = $_POST['full_name'] ?? '';
        $institution = $_POST['institution'] ?? '';
        $skills = $_POST['skills'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $location = $_POST['location'] ?? '';
        $website = $_POST['website'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        // Handle profile picture upload
        $profile_picture = $user_data['profile_picture'] ?? '';
        
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_picture'];
            $file_name = basename($file['name']);
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_error = $file['error'];
            
            // Get file extension
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Allowed extensions
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            // Check if file type is allowed
            if (in_array($file_ext, $allowed_ext)) {
                // Check file size (max 5MB)
                if ($file_size <= 5000000) {
                    // Generate unique filename
                    $new_file_name = uniqid('profile_', true) . '.' . $file_ext;
                    $file_destination = $upload_dir . $new_file_name;
                    
                    // Move uploaded file
                    if (move_uploaded_file($file_tmp, $file_destination)) {
                        // Delete old profile picture if it exists
                        if (!empty($user_data['profile_picture']) && file_exists($upload_dir . $user_data['profile_picture'])) {
                            unlink($upload_dir . $user_data['profile_picture']);
                        }
                        
                        $profile_picture = $new_file_name;
                        $success = "Profile picture updated successfully!";
                    } else {
                        $error = "Failed to upload profile picture. Please check directory permissions.";
                    }
                } else {
                    $error = "File size too large. Maximum size is 5MB.";
                }
            } else {
                $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
            }
        }
        
        // Update user data in database
        $sql = "UPDATE Users SET full_name = ?, institution = ?, skills = ?, bio = ?, location = ?, website = ?, phone = ?, profile_picture = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssssss", $full_name, $institution, $skills, $bio, $location, $website, $phone, $profile_picture, $user_email);
            if ($stmt->execute()) {
                $success = $success ?: "Profile updated successfully!";
            } else {
                $error = "Error updating profile: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    // Remove Profile Picture
    if (isset($_POST['remove_profile_picture'])) {
        if (!empty($user_data['profile_picture']) && file_exists($upload_dir . $user_data['profile_picture'])) {
            if (unlink($upload_dir . $user_data['profile_picture'])) {
                $sql = "UPDATE Users SET profile_picture = '' WHERE email = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("s", $user_email);
                    if ($stmt->execute()) {
                        $success = "Profile picture removed successfully!";
                    } else {
                        $error = "Error removing profile picture from database: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                $error = "Error deleting profile picture file.";
            }
        } else {
            $sql = "UPDATE Users SET profile_picture = '' WHERE email = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("s", $user_email);
                if ($stmt->execute()) {
                    $success = "Profile picture removed successfully!";
                } else {
                    $error = "Error removing profile picture from database: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
    
    // Add Course
    if (isset($_POST['add_course'])) {
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
    
    // Update Course
    if (isset($_POST['update_course'])) {
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
    
    // Delete Course
    if (isset($_POST['delete_course'])) {
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
    
    // Add Certificate
    if (isset($_POST['add_certificate'])) {
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
    
    // Delete Certificate
    if (isset($_POST['delete_certificate'])) {
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
    
    // Create Post
    if (isset($_POST['create_post'])) {
        $post_content = $_POST['post_content'] ?? '';
        
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
    
    // Send Connection Request
    if (isset($_POST['connect_user'])) {
        $target_user_email = $_POST['target_user_email'] ?? '';
        
        if (!empty($target_user_email) && $target_user_email != $user_email) {
            $check_sql = "SELECT id FROM user_connections WHERE 
                         (user_email = ? AND connected_user_email = ?) OR 
                         (user_email = ? AND connected_user_email = ?)";
            $check_stmt = $conn->prepare($check_sql);
            if ($check_stmt) {
                $check_stmt->bind_param("ssss", $user_email, $target_user_email, $target_user_email, $user_email);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                
                if ($check_result->num_rows == 0) {
                    $sql = "INSERT INTO user_connections (user_email, connected_user_email, status) VALUES (?, ?, 'pending')";
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        $stmt->bind_param("ss", $user_email, $target_user_email);
                        if ($stmt->execute()) {
                            $from_user_name = $user_data['full_name'] ?? 'Someone';
                            $notification_message = "$from_user_name sent you a connection request";
                            
                            $notification_sql = "INSERT INTO notifications (user_email, from_user_email, type, message) VALUES (?, ?, 'connection_request', ?)";
                            $notification_stmt = $conn->prepare($notification_sql);
                            if ($notification_stmt) {
                                $notification_stmt->bind_param("sss", $target_user_email, $user_email, $notification_message);
                                $notification_stmt->execute();
                                $notification_stmt->close();
                            }
                            
                            $success = "Connection request sent!";
                        } else {
                            $error = "Error sending connection request: " . $stmt->error;
                        }
                        $stmt->close();
                    }
                } else {
                    $error = "Connection request already sent or exists!";
                }
                $check_stmt->close();
            }
        }
    }
    
    // Accept Connection
    if (isset($_POST['accept_connection'])) {
        $connection_id = $_POST['connection_id'] ?? '';
        $from_user_email = $_POST['from_user_email'] ?? '';
        
        if (!empty($connection_id) && !empty($from_user_email)) {
            $update_sql = "UPDATE user_connections SET status = 'accepted' WHERE id = ? AND connected_user_email = ?";
            $update_stmt = $conn->prepare($update_sql);
            if ($update_stmt) {
                $update_stmt->bind_param("is", $connection_id, $user_email);
                
                if ($update_stmt->execute()) {
                    $user_name = $user_data['full_name'] ?? 'Someone';
                    $notification_message = "$user_name accepted your connection request";
                    
                    $notification_sql = "INSERT INTO notifications (user_email, from_user_email, type, message) VALUES (?, ?, 'connection_accepted', ?)";
                    $notification_stmt = $conn->prepare($notification_sql);
                    if ($notification_stmt) {
                        $notification_stmt->bind_param("sss", $from_user_email, $user_email, $notification_message);
                        $notification_stmt->execute();
                        $notification_stmt->close();
                    }
                    
                    $success = "Connection request accepted!";
                } else {
                    $error = "Error accepting connection: " . $update_stmt->error;
                }
                $update_stmt->close();
            }
        }
    }
    
    // Reject Connection
    if (isset($_POST['reject_connection'])) {
        $connection_id = $_POST['connection_id'] ?? '';
        
        if (!empty($connection_id)) {
            $update_sql = "UPDATE user_connections SET status = 'rejected' WHERE id = ? AND connected_user_email = ?";
            $update_stmt = $conn->prepare($update_sql);
            if ($update_stmt) {
                $update_stmt->bind_param("is", $connection_id, $user_email);
                
                if ($update_stmt->execute()) {
                    $success = "Connection request rejected!";
                } else {
                    $error = "Error rejecting connection: " . $update_stmt->error;
                }
                $update_stmt->close();
            }
        }
    }
    
    // Hide Buddy
    if (isset($_POST['hide_buddy'])) {
        $hidden_user_email = $_POST['hidden_user_email'] ?? '';
        
        if (!empty($hidden_user_email) && $hidden_user_email != $user_email) {
            $check_sql = "SELECT id FROM hidden_buddies WHERE user_email = ? AND hidden_user_email = ?";
            $check_stmt = $conn->prepare($check_sql);
            if ($check_stmt) {
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
    
    // Mark Notification as Read
    if (isset($_POST['mark_notification_read'])) {
        $notification_id = $_POST['notification_id'] ?? '';
        
        if (!empty($notification_id)) {
            $update_sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_email = ?";
            $update_stmt = $conn->prepare($update_sql);
            if ($update_stmt) {
                $update_stmt->bind_param("is", $notification_id, $user_email);
                $update_stmt->execute();
                $update_stmt->close();
            }
        }
    }
    
    // Mark All Notifications as Read
    if (isset($_POST['mark_all_read'])) {
        $update_sql = "UPDATE notifications SET is_read = 1 WHERE user_email = ?";
        $update_stmt = $conn->prepare($update_sql);
        if ($update_stmt) {
            $update_stmt->bind_param("s", $user_email);
            $update_stmt->execute();
            $update_stmt->close();
            $success = "All notifications marked as read!";
        }
    }
}

// Fetch user data
$sql = "SELECT full_name, institution, skills, bio, location, website, phone, role, profile_picture FROM Users WHERE email = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc() ?? [];
    $stmt->close();
}

// Fetch courses
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

// Fetch certificates
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

// Fetch study partners
$sql_partners = "SELECT u.email, u.full_name, u.institution, u.skills, u.bio 
                 FROM Users u 
                 WHERE u.email != ? 
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

// Fetch user posts
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

// Fetch pending connection requests
$sql_requests = "SELECT uc.id, uc.user_email as requester_email, u.full_name, u.institution, u.skills 
                 FROM user_connections uc 
                 JOIN Users u ON uc.user_email = u.email 
                 WHERE uc.connected_user_email = ? AND uc.status = 'pending' 
                 ORDER BY uc.created_at DESC";
$stmt_requests = $conn->prepare($sql_requests);
if ($stmt_requests) {
    $stmt_requests->bind_param("s", $user_email);
    $stmt_requests->execute();
    $result_requests = $stmt_requests->get_result();
    while ($row = $result_requests->fetch_assoc()) {
        $pending_requests[] = $row;
    }
    $stmt_requests->close();
}

// Fetch notifications
$sql_notifications = "SELECT n.*, u.full_name as from_user_name 
                      FROM notifications n 
                      JOIN Users u ON n.from_user_email = u.email 
                      WHERE n.user_email = ? 
                      ORDER BY n.created_at DESC 
                      LIMIT 10";
$stmt_notifications = $conn->prepare($sql_notifications);
if ($stmt_notifications) {
    $stmt_notifications->bind_param("s", $user_email);
    $stmt_notifications->execute();
    $result_notifications = $stmt_notifications->get_result();
    while ($row = $result_notifications->fetch_assoc()) {
        $notifications[] = $row;
    }
    $stmt_notifications->close();
}

// Count unread notifications
$sql_unread = "SELECT COUNT(*) as count FROM notifications WHERE user_email = ? AND is_read = 0";
$stmt_unread = $conn->prepare($sql_unread);
if ($stmt_unread) {
    $stmt_unread->bind_param("s", $user_email);
    $stmt_unread->execute();
    $result_unread = $stmt_unread->get_result();
    $unread_data = $result_unread->fetch_assoc();
    $unread_count = $unread_data['count'] ?? 0;
    $stmt_unread->close();
}

// Fetch accepted connections
$sql_connections = "SELECT u.email, u.full_name, u.institution, u.skills 
                    FROM user_connections uc 
                    JOIN Users u ON (uc.user_email = u.email OR uc.connected_user_email = u.email) 
                    WHERE ((uc.user_email = ? OR uc.connected_user_email = ?) AND uc.status = 'accepted') 
                    AND u.email != ?";
$stmt_connections = $conn->prepare($sql_connections);
if ($stmt_connections) {
    $stmt_connections->bind_param("sss", $user_email, $user_email, $user_email);
    $stmt_connections->execute();
    $result_connections = $stmt_connections->get_result();
    while ($row = $result_connections->fetch_assoc()) {
        $accepted_connections[] = $row;
    }
    $stmt_connections->close();
}

$conn->close();

// Calculate stats
$total_courses = count($running_courses) + count($completed_courses);
$completion_rate = $total_courses > 0 ? round((count($completed_courses) / $total_courses) * 100) : 0;
$user_skills = !empty($user_data['skills']) ? array_filter(array_map('trim', explode(',', $user_data['skills']))) : [];
?>
DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($user_data['full_name'] ?? 'User') ?> - FutureBot</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Reset and Base Styles */
    * { 
      box-sizing: border-box; 
      margin: 0; 
      padding: 0; 
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

    /* Navigation Styles */
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

    /* Notifications Dropdown */
    .notifications-dropdown {
      position: relative;
      display: inline-block;
    }

    .notification-btn {
      background: #4361ee;
      border: none;
      padding: 10px 15px;
      border-radius: 6px;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 5px;
      position: relative;
    }

    .notification-btn:hover {
      background: #3a0ca3;
    }

    .notification-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: #dc3545;
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 0.7rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .notifications-menu {
      display: none;
      position: absolute;
      top: 100%;
      right: 0;
      width: 350px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
      z-index: 1000;
      margin-top: 5px;
    }

    .notifications-menu.show {
      display: block;
    }

    .notifications-header {
      padding: 15px;
      border-bottom: 1px solid #e4e6eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .notifications-header h4 {
      margin: 0;
      color: #1c1e21;
    }

    .mark-all-read {
      background: none;
      border: none;
      color: #4361ee;
      cursor: pointer;
      font-size: 0.8rem;
    }

    .notifications-list {
      max-height: 400px;
      overflow-y: auto;
    }

    .notification-item {
      padding: 12px 15px;
      border-bottom: 1px solid #f0f2f5;
      display: flex;
      align-items: flex-start;
      gap: 10px;
      transition: background 0.3s ease;
    }

    .notification-item:hover {
      background: #f8f9fa;
    }

    .notification-item.unread {
      background: #f0f7ff;
    }

    .notification-content {
      flex: 1;
    }

    .notification-content p {
      margin: 0 0 5px 0;
      color: #1c1e21;
      font-size: 0.9rem;
    }

    .notification-content small {
      color: #65676b;
      font-size: 0.8rem;
    }

    .mark-read-form {
      margin: 0;
    }

    .mark-read-btn {
      background: none;
      border: none;
      color: #28a745;
      cursor: pointer;
      padding: 5px;
      border-radius: 50%;
      transition: all 0.3s ease;
    }

    .mark-read-btn:hover {
      background: #28a745;
      color: white;
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
    /* Animated Background */
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
  0%, 100% {
    transform: translateY(0) translateX(0);
  }
  25% {
    transform: translateY(-20px) translateX(10px);
  }
  50% {
    transform: translateY(10px) translateX(-15px);
  }
  75% {
    transform: translateY(-15px) translateX(-10px);
  }
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

    /* Buttons */
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

    /* Connection Cards */
    .connection-request-card {
      background: #ffffff;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 15px;
      border: 1px solid #e4e6eb;
      transition: all 0.3s ease;
    }

    .connection-request-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .connection-card {
      background: #ffffff;
      border-radius: 10px;
      padding: 20px;
      border: 1px solid #e4e6eb;
      transition: all 0.3s ease;
      text-align: center;
    }

    .connection-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

    /* Alert Styles */
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

    /* Post Card */
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
      justify-content: space-between;
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

      .notifications-menu {
        width: 300px;
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

      .notifications-menu {
        width: 280px;
        right: -50px;
      }
      /* Profile Picture Styles */
.profile-picture {
    position: relative;
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
    overflow: hidden;
}

.profile-picture img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-picture-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.profile-picture:hover .profile-picture-overlay {
    opacity: 1;
}

.profile-picture-overlay i {
    font-size: 1.5rem;
}

/* File input styling */
.form-control[type="file"] {
    padding: 8px;
    border: 2px dashed #e4e6eb;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.form-control[type="file"]:hover {
    border-color: #4361ee;
    background: #f0f7ff;
}

/* Image preview styling */
.image-preview {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 10px;
    border: 2px dashed #e4e6eb;
}

/* Progress bar for upload (optional) */
.upload-progress {
    width: 100%;
    height: 6px;
    background: #e4e6eb;
    border-radius: 3px;
    margin-top: 10px;
    overflow: hidden;
    display: none;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #4361ee, #3a0ca3);
    border-radius: 3px;
    transition: width 0.3s ease;
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
      
      <!-- Notifications Dropdown -->
      <div class="notifications-dropdown">
        <button class="notification-btn" onclick="toggleNotifications()">
          <i class="fas fa-bell"></i>
          <?php if ($unread_count > 0): ?>
            <span class="notification-badge"><?= $unread_count ?></span>
          <?php endif; ?>
        </button>
        <div class="notifications-menu" id="notificationsMenu">
          <div class="notifications-header">
            <h4>Notifications</h4>
            <?php if (!empty($notifications)): ?>
              <form method="POST" action="" style="display: inline;">
                <button type="submit" name="mark_all_read" class="mark-all-read">Mark all as read</button>
              </form>
            <?php endif; ?>
          </div>
          <div class="notifications-list">
            <?php if (!empty($notifications)): ?>
              <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>">
                  <div class="notification-content">
                    <p><?= htmlspecialchars($notification['message']) ?></p>
                    <small><?= date('M j, g:i A', strtotime($notification['created_at'])) ?></small>
                  </div>
                  <?php if (!$notification['is_read']): ?>
                    <form method="POST" action="" class="mark-read-form">
                      <input type="hidden" name="notification_id" value="<?= $notification['id'] ?>">
                      <button type="submit" name="mark_notification_read" class="mark-read-btn" title="Mark as read">
                        <i class="fas fa-check"></i>
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="notification-item">
                <p>No notifications</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
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

      <!-- Connection Requests Section -->
      <?php if(!empty($pending_requests)): ?>
      <div class="connections-section" style="margin-bottom: 30px;">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
          <h3 style="color: #1c1e21; margin: 0;">Connection Requests</h3>
          <span class="request-count" style="background: #ffc107; color: #000; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem;">
            <?= count($pending_requests) ?> pending
          </span>
        </div>
        
        <div class="connection-requests">
          <?php foreach($pending_requests as $request): ?>
            <div class="connection-request-card">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 15px;">
                  <div class="request-avatar" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #4361ee, #3a0ca3); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                    <i class="fas fa-user"></i>
                  </div>
                  <div>
                    <div style="font-weight: 600; color: #1c1e21;"><?= htmlspecialchars($request['full_name']) ?></div>
                    <div style="color: #65676b; font-size: 0.9rem;"><?= htmlspecialchars($request['institution']) ?></div>
                    <?php if(!empty($request['skills'])): ?>
                      <div style="font-size: 0.8rem; color: #4361ee; margin-top: 5px;">
                        <?= htmlspecialchars(implode(', ', array_slice(explode(',', $request['skills']), 0, 2))) ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
                <div style="display: flex; gap: 10px;">
                  <form method="POST" action="" style="display: inline;">
                    <input type="hidden" name="connection_id" value="<?= $request['id'] ?>">
                    <input type="hidden" name="from_user_email" value="<?= $request['requester_email'] ?>">
                    <button type="submit" name="accept_connection" class="btn btn-success">
                      <i class="fas fa-check"></i> Accept
                    </button>
                  </form>
                  <form method="POST" action="" style="display: inline;">
                    <input type="hidden" name="connection_id" value="<?= $request['id'] ?>">
                    <button type="submit" name="reject_connection" class="btn btn-danger">
                      <i class="fas fa-times"></i> Reject
                    </button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
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

      <!-- My Connections Section -->
      <div class="friends-card" style="margin-top: 20px;">
        <div class="card-title">My Connections</div>
        <?php if(!empty($accepted_connections)): ?>
          <?php foreach(array_slice($accepted_connections, 0, 3) as $connection): ?>
            <div class="friend-item">
              <div class="friend-avatar">
                <i class="fas fa-user"></i>
              </div>
              <div style="flex: 1;">
                <div class="friend-name"><?= htmlspecialchars($connection['full_name']) ?></div>
                <div class="friend-title"><?= htmlspecialchars($connection['institution']) ?></div>
                <?php if(!empty($connection['skills'])): ?>
                  <div style="font-size: 0.8rem; color: #4361ee; margin-top: 5px;">
                    <?= htmlspecialchars(implode(', ', array_slice(explode(',', $connection['skills']), 0, 2))) ?>
                  </div>
                <?php endif; ?>
              </div>
              <button class="btn btn-outline btn-sm">
                <i class="fas fa-comment"></i> Message
              </button>
            </div>
          <?php endforeach; ?>
          <?php if(count($accepted_connections) > 3): ?>
            <a href="connections.php" class="btn btn-outline" style="width: 100%; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 5px;">
              <i class="fas fa-eye"></i> View All (<?= count($accepted_connections) ?>)
            </a>
          <?php endif; ?>
        <?php else: ?>
          <div class="empty-state" style="padding: 20px 0;">
            <i class="fas fa-user-friends"></i>
            <p>No connections yet</p>
            <p>Connect with other students to build your network</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

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

  <!-- Profile Card -->
<div class="profile-card">
    <div class="profile-cover">
        <div class="profile-picture">
            <?php if (!empty($user_data['profile_picture'])): ?>
                <img src="uploads/profile_pictures/<?= htmlspecialchars($user_data['profile_picture']) ?>" 
                     alt="Profile Picture" 
                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
            <?php else: ?>
                <i class="fas fa-user"></i>
            <?php endif; ?>
            <div class="profile-picture-overlay" onclick="openModal('changePictureModal')">
                <i class="fas fa-camera"></i>
            </div>
        </div>
    </div>
    <!-- Rest of profile card remains the same -->
</div>

<!-- Change Profile Picture Modal -->
<div class="modal" id="changePictureModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Change Profile Picture</div>
            <button class="close-modal" onclick="closeModal('changePictureModal')">&times;</button>
        </div>
        <div style="text-align: center; padding: 20px;">
            <div class="current-picture" style="margin-bottom: 20px;">
                <h4>Current Picture</h4>
                <?php if (!empty($user_data['profile_picture'])): ?>
                    <img src="uploads/profile_pictures/<?= htmlspecialchars($user_data['profile_picture']) ?>" 
                         alt="Current Profile Picture" 
                         style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #4361ee;">
                <?php else: ?>
                    <div style="width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, #4361ee, #3a0ca3); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; margin: 0 auto;">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <form method="POST" action="" enctype="multipart/form-data" style="margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label" for="profile_picture">Upload New Picture</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" onchange="previewImage(this)">
                    <small style="color: #65676b;">Supported formats: JPG, JPEG, PNG, GIF. Max size: 5MB</small>
                </div>
                <div class="image-preview" id="imagePreview" style="display: none; margin: 15px 0;">
                    <h4>Preview</h4>
                    <img id="previewImage" src="" alt="Preview" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #28a745;">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('changePictureModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="update_profile">Save Picture</button>
                </div>
            </form>
            
            <?php if (!empty($user_data['profile_picture'])): ?>
                <form method="POST" action="" onsubmit="return confirm('Are you sure you want to remove your profile picture?');">
                    <button type="submit" class="btn btn-danger" name="remove_profile_picture">
                        <i class="fas fa-trash"></i> Remove Current Picture
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Edit Profile Modal -->
<div class="modal" id="editProfileModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Edit Profile</div>
            <button class="close-modal" onclick="closeModal('editProfileModal')">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group" style="text-align: center;">
                <label class="form-label">Profile Picture</label>
                <div style="margin-bottom: 15px;">
                    <?php if (!empty($user_data['profile_picture'])): ?>
                        <img src="uploads/profile_pictures/<?= htmlspecialchars($user_data['profile_picture']) ?>" 
                             alt="Profile Picture" 
                             style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #4361ee;">
                    <?php else: ?>
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #4361ee, #3a0ca3); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; margin: 0 auto;">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                <small style="color: #65676b;">Leave empty to keep current picture</small>
            </div>
            
            <!-- Rest of your existing form fields -->
            <div class="form-group">
                <label class="form-label" for="full_name">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" required>
            </div>
            <!-- ... other form fields ... -->
            
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('editProfileModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" name="update_profile">Save Changes</button>
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
    // Image preview function
function previewImage(input) {
    const preview = document.getElementById('previewImage');
    const previewContainer = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
    }
}

// Open change picture modal from profile picture click
function openChangePictureModal() {
    openModal('changePictureModal');
}

// Update the profile picture element to be clickable
document.addEventListener('DOMContentLoaded', function() {
    const profilePicture = document.querySelector('.profile-picture');
    if (profilePicture) {
        profilePicture.style.cursor = 'pointer';
        profilePicture.addEventListener('click', openChangePictureModal);
    }
});

    // Notifications dropdown
    function toggleNotifications() {
      const menu = document.getElementById('notificationsMenu');
      menu.classList.toggle('show');
    }

    // Close notifications when clicking outside
    document.addEventListener('click', function(event) {
      const notificationsDropdown = document.querySelector('.notifications-dropdown');
      if (!notificationsDropdown.contains(event.target)) {
        const menu = document.getElementById('notificationsMenu');
        menu.classList.remove('show');
      }
    });

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