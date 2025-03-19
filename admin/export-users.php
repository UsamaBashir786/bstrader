<?php

/**
 * BS Traders - Export Users to CSV
 * This script exports all user data to a CSV file for download
 */

// Database connection
$conn = new mysqli('localhost', 'root', '', 'bs_trader');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="users_' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add CSV header row
fputcsv($output, array('ID', 'Name', 'Email', 'Phone', 'CNIC', 'Address', 'Role', 'Status', 'Contract Start', 'Contract End', 'Created Date'));

// Process filter parameters if coming from the filter form
$where_clause = "";
$filter_params = [];
$param_types = "";

if (isset($_GET['filter'])) {
  $conditions = [];

  if (!empty($_GET['role'])) {
    $conditions[] = "role = ?";
    $filter_params[] = $_GET['role'];
    $param_types .= "s";
  }

  if (!empty($_GET['status']) && $_GET['status'] !== 'all') {
    $conditions[] = "status = ?";
    $filter_params[] = $_GET['status'];
    $param_types .= "s";
  }

  if (!empty($_GET['search'])) {
    $conditions[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $search_term = "%" . $_GET['search'] . "%";
    $filter_params[] = $search_term;
    $filter_params[] = $search_term;
    $filter_params[] = $search_term;
    $param_types .= "sss";
  }

  if (count($conditions) > 0) {
    $where_clause = "WHERE " . implode(" AND ", $conditions);
  }
}

// Fetch users with optional filtering
$query = "SELECT id, name, email, phone, cnic, address, role, status, contract_start, contract_end, created_at 
          FROM users 
          $where_clause 
          ORDER BY name";

if (!empty($param_types)) {
  $stmt = $conn->prepare($query);
  $stmt->bind_param($param_types, ...$filter_params);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  $result = $conn->query($query);
}

// Add data rows
if ($result) {
  while ($row = $result->fetch_assoc()) {
    // Format dates for better readability in CSV
    $row['contract_start'] = date('Y-m-d', strtotime($row['contract_start']));
    $row['contract_end'] = date('Y-m-d', strtotime($row['contract_end']));
    $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));

    // Write the row to CSV
    fputcsv($output, $row);
  }
}

// Close file handle
fclose($output);

// Close database connection
$conn->close();
