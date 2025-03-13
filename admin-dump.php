<?php
// Database connection configuration
require_once "config/config.php";

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin user details
$name = "BS Trader Admin";
$email = "bstrader@admin.com";
$password = "11221234";
$phone = "+92 300 1234567";
$address = "123 Business Avenue, Commercial Area, Lahore, Pakistan";
$cnic = "12345-1234567-1";
$contract_start = date("Y-m-d"); // Current date
$contract_end = date("Y-m-d", strtotime("+5 years")); // 5 years from now
$role = "admin";
$status = "active";

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$check_query = "SELECT id FROM users WHERE email = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("s", $email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "Admin user with email '{$email}' already exists in the database.<br>";
    $check_stmt->close();
} else {
    $check_stmt->close();
    
    // Prepare SQL statement for insertion
    $sql = "INSERT INTO users (name, email, phone, address, cnic, contract_start, contract_end, password, role, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $name, $email, $phone, $address, $cnic, $contract_start, $contract_end, $hashed_password, $role, $status);
    
    // Execute query
    if ($stmt->execute()) {
        echo "Admin user created successfully.<br>";
        echo "Email: " . $email . "<br>";
        echo "Password: " . $password . " (unencrypted for your reference only)<br>";
        echo "Role: " . $role . "<br>";
        echo "<strong>Please delete this script after use for security reasons.</strong>";
    } else {
        echo "Error creating admin user: " . $stmt->error . "<br>";
    }
    
    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>