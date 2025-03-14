<?php
// Include config and authentication
require_once "../config/admin-auth.php";
require_once "../config/config.php";

// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $employee_id = intval($_GET['id']);
    
    // Get employee data
    $sql = "SELECT * FROM users WHERE id = ? AND (role = 'employee' OR role = 'user')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($employee = $result->fetch_assoc()) {
        // Return data as JSON
        header('Content-Type: application/json');
        echo json_encode($employee);
    } else {
        // Employee not found
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Employee not found']);
    }
    
    $stmt->close();
} else {
    // No ID provided
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'No employee ID provided']);
}

$conn->close();
?>