<?php
session_start();
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request_id = $_POST['request_id'] ?? '';
    $action = $_POST['action'] ?? '';
    $mentor = $_POST['mentor'] ?? '';

    

    // Validate inputs and action type
    if ($request_id && $action && $mentor && in_array($action, ['accept', 'reject'])) {
        // Get original request details
        $stmt = $conn->prepare("SELECT * FROM mentor_requests WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();
        $stmt->close();


        
        if ($request) {
            // Set status to enum value 'accepted' or 'rejected'
            $status = ($action === 'accept') ? 'accepted' : 'rejected';

            // Insert notification into appointed_list table
            $stmt2 = $conn->prepare("INSERT INTO appointed_list (mentor_email, student_name, location, institute, subject, contact, status, action_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("ssssssss", 
                $request['mentor_email'], 
                $request['student_name'], 
                $request['location'], 
                $request['institute'], 
                $request['subject'], 
                $request['contact'], 
                $status,
                $mentor
            );
            $stmt2->execute();
            $stmt2->close();
        }
    }
}





header("Location: mentor_requests.php");
exit;
