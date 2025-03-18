<?php
// Include admin authentication
require_once "../config/admin-auth.php";
require_once "../config/config.php";

// Set the content type for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="tasks_export_' . date('Y-m-d') . '.csv"');

// Create the file pointer for output
$output = fopen('php://output', 'w');

// Set the column headers
fputcsv($output, [
    'ID', 
    'Task Name', 
    'Task Date', 
    'Task Area', 
    'Amount', 
    'From Date', 
    'To Date', 
    'Target', 
    'Description', 
    'Priority', 
    'Assigned To', 
    'Status', 
    'Created At', 
    'Updated At', 
    'Completed At'
]);

// Get filter parameters
$where_clauses = [];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$priority_filter = isset($_GET['priority']) ? $_GET['priority'] : '';
$assigned_to_filter = isset($_GET['assigned_to']) ? $_GET['assigned_to'] : '';
$task_area_filter = isset($_GET['task_area']) ? $_GET['task_area'] : '';

if (!empty($search)) {
    $search = '%' . $search . '%';
    $where_clauses[] = "(task_name LIKE ? OR description LIKE ? OR target LIKE ?)";
}

if (!empty($status_filter)) {
    $where_clauses[] = "status = ?";
}

if (!empty($priority_filter)) {
    $where_clauses[] = "priority = ?";
}

if (!empty($assigned_to_filter)) {
    $where_clauses[] = "assigned_to = ?";
}

if (!empty($task_area_filter)) {
    $where_clauses[] = "task_area = ?";
}

// Build the WHERE clause
$where_clause = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Prepare the query
$query = "SELECT * FROM tasks $where_clause ORDER BY created_at DESC";
$stmt = $conn->prepare($query);

// Bind search parameters if needed
$param_types = "";
$param_values = [];

if (!empty($search)) {
    $param_types .= "sss";
    $param_values[] = $search;
    $param_values[] = $search;
    $param_values[] = $search;
}

if (!empty($status_filter)) {
    $param_types .= "s";
    $param_values[] = $status_filter;
}

if (!empty($priority_filter)) {
    $param_types .= "s";
    $param_values[] = $priority_filter;
}

if (!empty($assigned_to_filter)) {
    $param_types .= "s";
    $param_values[] = $assigned_to_filter;
}

if (!empty($task_area_filter)) {
    $param_types .= "s";
    $param_values[] = $task_area_filter;
}

if (!empty($param_values)) {
    $stmt->bind_param($param_types, ...$param_values);
}

$stmt->execute();
$result = $stmt->get_result();

// Output each row of the data
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['task_name'],
        $row['task_date'],
        $row['task_area'],
        $row['amount'],
        $row['from_date'],
        $row['to_date'],
        $row['target'],
        $row['description'],
        $row['priority'],
        $row['assigned_to'],
        $row['status'],
        $row['created_at'],
        $row['updated_at'],
        $row['completed_at']
    ]);
}

// Close the statement
$stmt->close();
?>