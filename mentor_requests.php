<?php
session_start();
require_once 'db.php';

$mentor_name = $_SESSION['username'] ?? 'Unknown Mentor';



// Fetch only necessary fields
$result = $conn->query("SELECT id, student_name, location, institute, subject, contact FROM mentor_requests ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FutureBot - Hire Requests</title>
    <style>
        :root {
            --primary-blue: #1033a6;
            --dark-blue: #27205b;
            --light-blue: #d3dbff;
            --very-light-blue: #e4fcf8;
            --purple: #2c174b;
            --light-purple: #dad5db;
            --white: #ffffff;
            --success: #289842;
            --danger: #dc3545;
            --warning: #ffc107;
            --gray: #6c757d;
            --light-gray: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--very-light-blue) 0%, var(--light-blue) 100%);
            color: var(--primary-blue);
            min-height: 100vh;
            padding-top: 80px;
        }

        .navbar {
            background-color: var(--white);
            padding: 15px 40px;
            border-bottom: 2px solid var(--light-purple);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(41, 39, 39, 0.15);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar h1 {
            color: var(--dark-blue);
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .back-button {
            background-color: var(--white);
            color: var(--purple);
            padding: 10px 20px;
            border-radius: 25px;
            border: 2px solid var(--purple);
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .back-button:hover {
            background-color: var(--purple);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(44, 23, 75, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(24, 23, 23, 0.1);
            padding: 25px;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .table-container {
            overflow-x: auto;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            min-width: 800px;
        }

        th, td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #eaeaea;
        }

        th {
            background-color: var(--dark-blue);
            color: var(--white);
            font-weight: 600;
            font-size: 15px;
            position: sticky;
            top: 0;
        }

        tr {
            transition: background-color 0.2s ease;
        }

        tr:hover {
            background-color: #f8faff;
        }

        tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        tr:nth-child(even):hover {
            background-color: #f0f4ff;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            color: white;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            min-width: 70px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .accept { 
            background-color: var(--success); 
        }
        .accept:hover { 
            background-color: #218838; 
        }

        .reject { 
            background-color: var(--danger); 
        }
        .reject:hover { 
            background-color: #c82333; 
        }

        .ignore { 
            background-color: var(--gray); 
        }
        .ignore:hover { 
            background-color: #5a6268; 
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-accepted {
            background-color: #d1edff;
            color: var(--primary-blue);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }

        .empty-state img {
            max-width: 200px;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        /* Responsive design */
        @media (max-width: 1024px) {
            .container {
                max-width: 95%;
            }
            
            th, td {
                padding: 12px 8px;
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 12px 20px;
            }
            
            .navbar h1 {
                font-size: 22px;
            }
            
            .back-button {
                padding: 8px 16px;
                font-size: 14px;
            }
            
            body {
                padding-top: 70px;
            }
            
            .card {
                padding: 20px 15px;
                border-radius: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }
            
            .action-btn {
                min-width: 60px;
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <div class="logo-icon">FB</div>
            <h1>Hire Requests</h1>
        </div>
        <a href="mentor_profile.php" class="back-button">← Back to Profile</a>
    </div>

    <div class="container">
        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Location</th>
                            <th>Institute</th>
                            <th>Subject</th>
                            <th>Contact Info</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Alex Johnson</td>
                            <td>New York, NY</td>
                            <td>Columbia University</td>
                            <td>Advanced Python</td>
                            <td>alex.j@example.com</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn accept">Accept</button>
                                    <button class="action-btn reject">Reject</button>
                                    <button class="action-btn ignore">Ignore</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Maria Garcia</td>
                            <td>Los Angeles, CA</td>
                            <td>UCLA</td>
                            <td>Data Structures</td>
                            <td>maria.g@example.com</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn accept">Accept</button>
                                    <button class="action-btn reject">Reject</button>
                                    <button class="action-btn ignore">Ignore</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>James Wilson</td>
                            <td>Chicago, IL</td>
                            <td>University of Chicago</td>
                            <td>Machine Learning</td>
                            <td>james.w@example.com</td>
                            <td><span class="status-badge status-accepted">Accepted</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn accept" disabled>Accept</button>
                                    <button class="action-btn reject">Reject</button>
                                    <button class="action-btn ignore">Ignore</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Sarah Miller</td>
                            <td>Boston, MA</td>
                            <td>MIT</td>
                            <td>Web Development</td>
                            <td>sarah.m@example.com</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn accept">Accept</button>
                                    <button class="action-btn reject">Reject</button>
                                    <button class="action-btn ignore">Ignore</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Robert Chen</td>
                            <td>San Francisco, CA</td>
                            <td>Stanford University</td>
                            <td>Algorithms</td>
                            <td>robert.c@example.com</td>
                            <td><span class="status-badge status-pending">Pending</span></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn accept">Accept</button>
                                    <button class="action-btn reject">Reject</button>
                                    <button class="action-btn ignore">Ignore</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const actionButtons = document.querySelectorAll('.action-btn');
            
            actionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const studentName = row.cells[0].textContent;
                    const action = this.textContent.trim();
                    
                    if (action === 'Accept') {
                        // Update status in the table
                        const statusCell = row.cells[5];
                        statusCell.innerHTML = '<span class="status-badge status-accepted">Accepted</span>';
                        
                        // Disable the accept button
                        this.disabled = true;
                        
                        // Show success message
                        showNotification(`You have accepted the request from ${studentName}`, 'success');
                    } else if (action === 'Reject') {
                        // Show confirmation for rejection
                        if (confirm(`Are you sure you want to reject ${studentName}'s request?`)) {
                            // Remove the row
                            row.style.opacity = '0';
                            setTimeout(() => {
                                row.remove();
                                showNotification(`You have rejected the request from ${studentName}`, 'warning');
                            }, 300);
                        }
                    } else if (action === 'Ignore') {
                        // Simply show a message for ignore
                        showNotification(`You have ignored the request from ${studentName}`, 'info');
                    }
                });
            });
            
            function showNotification(message, type) {
                // Create notification element
                const notification = document.createElement('div');
                notification.textContent = message;
                notification.style.position = 'fixed';
                notification.style.top = '100px';
                notification.style.right = '20px';
                notification.style.padding = '15px 20px';
                notification.style.borderRadius = '8px';
                notification.style.color = 'white';
                notification.style.fontWeight = '600';
                notification.style.zIndex = '10000';
                notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
                notification.style.transform = 'translateX(400px)';
                notification.style.transition = 'transform 0.3s ease';
                
                // Set background color based on type
                if (type === 'success') {
                    notification.style.backgroundColor = 'var(--success)';
                } else if (type === 'warning') {
                    notification.style.backgroundColor = 'var(--danger)';
                } else {
                    notification.style.backgroundColor = 'var(--gray)';
                }
                
                // Add to page
                document.body.appendChild(notification);
                
                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 10);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(400px)';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }
        });
    </script>
</body>
</html>