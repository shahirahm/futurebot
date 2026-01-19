<?php
echo "<h2>🔍 Database Connection Test</h2>";

// Test database connection
try {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'futurebot';
    
    echo "<p>Testing connection to MySQL...</p>";
    
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ Connection failed: " . $conn->connect_error . "</p>";
        echo "<p><strong>Possible solutions:</strong></p>";
        echo "<ul>";
        echo "<li>Make sure XAMPP is running</li>";
        echo "<li>Start MySQL service in XAMPP Control Panel</li>";
        echo "<li>Check if port 3306 is available</li>";
        echo "<li>Verify database 'futurebot' exists</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: green;'>✅ Database connection successful!</p>";
        
        // Test if companies table exists
        $result = $conn->query("SHOW TABLES LIKE 'companies'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Companies table exists</p>";
            
            // Check OTP fields
            $result = $conn->query("DESCRIBE companies");
            $otp_fields = [];
            while ($row = $result->fetch_assoc()) {
                if (in_array($row['Field'], ['otp', 'otp_expires_at'])) {
                    $otp_fields[] = $row['Field'];
                }
            }
            
            if (count($otp_fields) >= 2) {
                echo "<p style='color: green;'>✅ OTP fields exist: " . implode(', ', $otp_fields) . "</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Some OTP fields missing</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Companies table not found</p>";
        }
        
        $conn->close();
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>🚀 Quick Start Guide:</h3>";
echo "<ol>";
echo "<li>Open XAMPP Control Panel (C:\\xampp\\xampp-control.exe)</li>";
echo "<li>Start MySQL service</li>";
echo "<li>Start Apache service</li>";
echo "<li>Refresh this page to test again</li>";
echo "</ol>";

echo "<h3>🔗 Test Links:</h3>";
echo "<ul>";
echo "<li><a href='test_otp_workflow.php'>OTP Workflow Test</a></li>";
echo "<li><a href='company_register.php'>Company Registration</a></li>";
echo "<li><a href='admin_company_approvals.php'>Admin Panel</a></li>";
echo "</ul>";
?> 