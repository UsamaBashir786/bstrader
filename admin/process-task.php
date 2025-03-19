<?php
// Include admin authentication
require_once "../config/admin-auth.php";
require_once "../config/config.php";

// Set default response
$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred'
];

// Get current user ID from session
$user_id = $_SESSION['user_id']; // Make sure this matches your authentication system

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $task_name = $_POST['task_name'] ?? '';
    $task_date = $_POST['task_date'] ?? '';
    $task_area = $_POST['task_area'] ?? '';
    $amount = $_POST['amount'] ?? 0;
    $from_date = $_POST['from_date'] ?? '';
    $to_date = $_POST['to_date'] ?? '';
    $target = $_POST['target'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = $_POST['priority'] ?? 'medium';
    $assigned_to = $_POST['assigned_to'] ?? '';
    $status = $_POST['status'] ?? 'pending';
    
    // Validate required fields
    if (empty($task_name) || empty($task_date) || empty($task_area) || empty($from_date) || empty($to_date) || empty($target)) {
        $response['message'] = 'Please fill in all required fields';
    } else {
        // Handle file upload if present
        $attachment = '';
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $file_type = $_FILES['attachment']['type'];
            $file_size = $_FILES['attachment']['size'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($file_type, $allowed_types)) {
                $response['message'] = 'Invalid file type. Allowed types: JPG, PNG, GIF, PDF, DOC, DOCX';
            } elseif ($file_size > $max_size) {
                $response['message'] = 'File size exceeds the maximum limit (5MB)';
            } else {
                $file_name = uniqid() . '.' . pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
                $upload_path = '../uploads/' . $file_name;
                
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_path)) {
                    $attachment = $file_name;
                } else {
                    $response['message'] = 'Failed to upload file';
                }
            }
        }
        
        // If validation and file upload were successful
        if ($response['status'] === 'error' && $response['message'] === 'An unknown error occurred') {
            // Check if it's an update or new task
            if (isset($_POST['task_id']) && !empty($_POST['task_id'])) {
                // Update existing task
                $task_id = $_POST['task_id'];
                
                // Prepare the query with or without attachment
                if (!empty($attachment)) {
                    $stmt = $conn->prepare("UPDATE tasks SET task_name = ?, task_date = ?, task_area = ?, amount = ?, from_date = ?, to_date = ?, target = ?, description = ?, priority = ?, assigned_to = ?, attachment = ?, status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
                    $stmt->bind_param("sssdssssssssii", $task_name, $task_date, $task_area, $amount, $from_date, $to_date, $target, $description, $priority, $assigned_to, $attachment, $status, $task_id, $user_id);
                } else {
                    $stmt = $conn->prepare("UPDATE tasks SET task_name = ?, task_date = ?, task_area = ?, amount = ?, from_date = ?, to_date = ?, target = ?, description = ?, priority = ?, assigned_to = ?, status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
                    $stmt->bind_param("sssdsssssssii", $task_name, $task_date, $task_area, $amount, $from_date, $to_date, $target, $description, $priority, $assigned_to, $status, $task_id, $user_id);
                }
                
                if ($stmt->execute()) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Task updated successfully',
                        'task_id' => $task_id
                    ];
                } else {
                    $response['message'] = 'Failed to update task: ' . $conn->error;
                }
                $stmt->close();
            } else {
                // Create new task
                $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_name, task_date, task_area, amount, from_date, to_date, target, description, priority, assigned_to, attachment, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssdsssssss", $user_id, $task_name, $task_date, $task_area, $amount, $from_date, $to_date, $target, $description, $priority, $assigned_to, $attachment, $status);
                
                if ($stmt->execute()) {
                    $task_id = $conn->insert_id;
                    $response = [
                        'status' => 'success',
                        'message' => 'Task created successfully',
                        'task_id' => $task_id
                    ];
                } else {
                    $response['message'] = 'Failed to create task: ' . $conn->error;
                }
                $stmt->close();
            }
        }
    }
}

// Set session messages for the next page
if ($response['status'] === 'success') {
    $_SESSION['success_message'] = $response['message'];
} else {
    $_SESSION['error_message'] = $response['message'];
}

// Redirect back to tasks page
header('Location: task.php');
exit;
?>