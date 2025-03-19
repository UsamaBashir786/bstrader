<?php

/**
 * BS Traders - User Management System
 * For admin side management of users
 */

// Database connection
$conn = new mysqli('localhost', 'root', '', 'bs_trader');

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$success_message = '';
$error_message = '';
$users = [];
$user_roles = ['admin', 'manager', 'employee', 'user'];
$status_options = ['active', 'inactive', 'locked', 'pending'];

// Handle Add/Edit User form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  // Common fields for both add and edit
  $name = $_POST['name'] ?? '';
  $email = $_POST['email'] ?? '';
  $phone = $_POST['phone'] ?? '';
  $address = $_POST['address'] ?? '';
  $cnic = $_POST['cnic'] ?? '';
  $contract_start = $_POST['contract_start'] ?? '';
  $contract_end = $_POST['contract_end'] ?? '';
  $role = $_POST['role'] ?? 'user';
  $status = $_POST['status'] ?? 'active';

  // Handle file upload
  $profile_pic = null;
  if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
    $upload_dir = 'uploads/profile_pics/';

    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
      mkdir($upload_dir, 0777, true);
    }

    $file_name = uniqid() . '.' . pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
    $target_path = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_path)) {
      $profile_pic = $file_name;
    }
  }

  // Add new user
  if ($_POST['action'] === 'add') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate passwords match
    if ($password !== $confirm_password) {
      $error_message = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
      $error_message = "Password must be at least 6 characters long!";
    } else {
      // Hash the password
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);

      // Prepare SQL statement
      $stmt = $conn->prepare("INSERT INTO users (name, email, phone, address, cnic, contract_start, contract_end, profile_pic, password, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("sssssssssss", $name, $email, $phone, $address, $cnic, $contract_start, $contract_end, $profile_pic, $hashed_password, $role, $status);

      if ($stmt->execute()) {
        $success_message = "User added successfully!";
      } else {
        $error_message = "Error adding user: " . $stmt->error;
      }

      $stmt->close();
    }
  }
  // Replace this code block in the edit user section (around line 60-100)

  // Edit existing user
  elseif ($_POST['action'] === 'edit' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $stmt = null; // Initialize $stmt variable

    // Check if password is being updated
    if (!empty($_POST['password']) && !empty($_POST['confirm_password'])) {
      $password = $_POST['password'];
      $confirm_password = $_POST['confirm_password'];

      // Validate passwords match
      if ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
      } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long!";
      } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Update with new password
        if ($profile_pic) {
          $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, cnic = ?, contract_start = ?, contract_end = ?, profile_pic = ?, password = ?, role = ?, status = ? WHERE id = ?");
          $stmt->bind_param("sssssssssssi", $name, $email, $phone, $address, $cnic, $contract_start, $contract_end, $profile_pic, $hashed_password, $role, $status, $user_id);
        } else {
          $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, cnic = ?, contract_start = ?, contract_end = ?, password = ?, role = ?, status = ? WHERE id = ?");
          $stmt->bind_param("ssssssssssi", $name, $email, $phone, $address, $cnic, $contract_start, $contract_end, $hashed_password, $role, $status, $user_id);
        }
      }
    } else {
      // Update without changing password
      if ($profile_pic) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, cnic = ?, contract_start = ?, contract_end = ?, profile_pic = ?, role = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssssssssi", $name, $email, $phone, $address, $cnic, $contract_start, $contract_end, $profile_pic, $role, $status, $user_id);
      } else {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ?, cnic = ?, contract_start = ?, contract_end = ?, role = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssssssssi", $name, $email, $phone, $address, $cnic, $contract_start, $contract_end, $role, $status, $user_id);
      }
    }

    // Only execute if there's no error and statement is prepared
    if (empty($error_message) && $stmt) {
      if ($stmt->execute()) {
        $success_message = "User updated successfully!";
      } else {
        $error_message = "Error updating user: " . $stmt->error;
      }
      $stmt->close();
    }
  }
}

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
  $user_id = $_GET['delete'];

  // Get the user to check if exists and is not the current admin
  $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Don't allow deleting the admin account
    if ($user['role'] === 'admin' && $user_id == 3) { // Assuming ID 3 is the main admin
      $error_message = "Cannot delete the main administrator account!";
    } else {
      // Delete the user
      $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
      $stmt->bind_param("i", $user_id);
      if ($stmt->execute()) {
        $success_message = "User deleted successfully!";
      } else {
        $error_message = "Error deleting user: " . $stmt->error;
      }
    }
  } else {
    $error_message = "User not found!";
  }

  $stmt->close();
}

// Reset user password
if (isset($_GET['reset']) && is_numeric($_GET['reset'])) {
  $user_id = $_GET['reset'];

  // Generate a temporary password
  $temp_password = bin2hex(random_bytes(5)); // 10 character random string
  $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);

  $stmt = $conn->prepare("UPDATE users SET password = ?, status = 'pending' WHERE id = ?");
  $stmt->bind_param("si", $hashed_password, $user_id);

  if ($stmt->execute()) {
    $success_message = "Password reset successfully! Temporary password: " . $temp_password;
  } else {
    $error_message = "Error resetting password: " . $stmt->error;
  }

  $stmt->close();
}
// Replace the status change section (around line 150-188) with this code

// Change user status
if (isset($_GET['status']) && isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
  $status = $_GET['status'];
  $user_id = $_GET['user_id'];

  // Validate status
  if (in_array($status, $status_options)) {
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $user_id);

    if ($stmt->execute()) {
      $success_message = "User status updated successfully!";
    } else {
      $error_message = "Error updating user status: " . $stmt->error;
    }

    $stmt->close();
  } else {
    $error_message = "Invalid status value!";
  }
}

// Get user data for editing
$edit_user = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
  $edit_id = $_GET['edit'];
  $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->bind_param("i", $edit_id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $edit_user = $result->fetch_assoc();
  }

  $stmt->close();
}

// Handle search and filtering
$where_clause = "1=1"; // Always true condition to start
$search_term = '';
$role_filter = '';
$status_filter = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
  $search_term = $_GET['search'];
  $where_clause .= " AND (name LIKE '%$search_term%' OR email LIKE '%$search_term%' OR phone LIKE '%$search_term%')";
}

if (isset($_GET['role']) && !empty($_GET['role'])) {
  $role_filter = $_GET['role'];
  $where_clause .= " AND role = '$role_filter'";
}

if (isset($_GET['status']) && !empty($_GET['status']) && $_GET['status'] !== 'all') {
  $status_filter = $_GET['status'];
  $where_clause .= " AND status = '$status_filter'";
}

// Fetch all users with optional filtering
$query = "SELECT * FROM users WHERE $where_clause ORDER BY created_at DESC";
$result = $conn->query($query);

if ($result) {
  while ($row = $result->fetch_assoc()) {
    $users[] = $row;
  }
}

// Close the database connection
$conn->close();

// Helper functions
function getStatusBadgeClass($status)
{
  switch ($status) {
    case 'active':
      return 'bg-green-100 text-green-800';
    case 'inactive':
      return 'bg-gray-100 text-gray-800';
    case 'locked':
      return 'bg-red-100 text-red-800';
    case 'pending':
      return 'bg-yellow-100 text-yellow-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
}

function getRoleLabel($role)
{
  switch ($role) {
    case 'admin':
      return 'Administrator';
    case 'manager':
      return 'Manager';
    case 'employee':
      return 'Employee';
    case 'user':
      return 'Regular User';
    default:
      return ucfirst($role);
  }
}

function formatDate($date)
{
  return date('F j, Y', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - User Management</title>
  <link rel="stylesheet" href="../src/output.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: '#e6f1ff',
              100: '#cce3ff',
              200: '#99c7ff',
              300: '#66aaff',
              400: '#338eff',
              500: '#0072ff',
              600: '#005bcc',
              700: '#004499',
              800: '#002e66',
              900: '#001733',
            }
          }
        }
      }
    }
  </script>
</head>

<body class="bg-gray-50">
  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="hidden md:flex md:flex-shrink-0">
      <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-primary-700 border-r">
          <div class="flex items-center flex-shrink-0 px-4">
            <span class="text-2xl font-bold text-white">BS Traders</span>
          </div>
          <div class="mt-5 flex-grow flex flex-col">
            <nav class="flex-1 px-2 space-y-1 bg-primary-700">
              <a href="index.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-home mr-3 h-6 w-6"></i>
                Dashboard
              </a>
              <a href="employee.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-users mr-3 h-6 w-6"></i>
                Employee Management
              </a>
              <a href="user.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-white bg-primary-800">
                <i class="fas fa-user-shield mr-3 h-6 w-6"></i>
                User Management
              </a>
              <a href="salary.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-money-bill-wave mr-3 h-6 w-6"></i>
                Salary Management
              </a>
              <a href="expense.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-file-invoice-dollar mr-3 h-6 w-6"></i>
                Expense Management
              </a>
              <a href="task.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-tasks mr-3 h-6 w-6"></i>
                Task Management
              </a>
              <a href="reports.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-chart-bar mr-3 h-6 w-6"></i>
                Reports
              </a>
              <a href="settings.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-cog mr-3 h-6 w-6"></i>
                Settings
              </a>
            </nav>
          </div>
          <div class="flex-shrink-0 flex border-t border-primary-800 p-4">
            <a href="profile.php" class="flex-shrink-0 group block">
              <div class="flex items-center">
                <div>
                  <svg class="inline-block h-10 w-10 rounded-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                    <!-- Background Circle -->
                    <circle cx="100" cy="100" r="90" fill="#3f51b5" />

                    <!-- Admin Icon - Stylized "A" with shield/dashboard elements -->
                    <path d="M100 30 L150 100 L130 140 H70 L50 100 Z" fill="none" stroke="white" stroke-width="6" stroke-linejoin="round" />

                    <!-- Horizontal bars - representing dashboard/admin panel -->
                    <line x1="70" y1="80" x2="130" y2="80" stroke="white" stroke-width="6" stroke-linecap="round" />
                    <line x1="80" y1="100" x2="120" y2="100" stroke="white" stroke-width="6" stroke-linecap="round" />
                    <line x1="90" y1="120" x2="110" y2="120" stroke="white" stroke-width="6" stroke-linecap="round" />

                    <!-- Crown element suggesting admin authority -->
                    <path d="M70 60 L85 45 L100 60 L115 45 L130 60" fill="none" stroke="white" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />

                    <!-- Outer ring for polish -->
                    <circle cx="100" cy="100" r="90" fill="none" stroke="white" stroke-width="2" opacity="0.3" />
                    <circle cx="100" cy="100" r="85" fill="none" stroke="white" stroke-width="1" opacity="0.2" />
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-base font-medium text-white">Admin User</p>
                  <p class="text-sm font-medium text-primary-200 group-hover:text-primary-100">View profile</p>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex flex-col flex-1 overflow-hidden">
      <!-- Top navbar -->
      <nav class="bg-white border-b border-gray-200 flex-shrink-0">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div class="flex justify-between h-16">
            <div class="flex">
              <div class="flex items-center flex-shrink-0 md:hidden">
                <button id="sidebarToggle" type="button" class="text-gray-500 hover:text-gray-900 focus:outline-none">
                  <i class="fas fa-bars h-6 w-6"></i>
                </button>
              </div>
              <div class="hidden md:ml-6 md:flex md:items-center md:space-x-4">
                <div class="px-3 py-2 text-sm font-medium text-gray-900">
                  BS Traders Distributed System
                </div>
              </div>
            </div>
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <span class="hidden sm:inline-flex ml-3 items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700">
                  Date: <span id="current-date" class="ml-1"></span>
                </span>
              </div>
              <div class="hidden md:ml-4 md:flex-shrink-0 md:flex md:items-center">
                <button class="p-1 ml-3 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-bell h-6 w-6"></i>
                </button>
                <div class="ml-3 relative">
                  <div>
                    <button type="button" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="user-menu-button">
                      <span class="sr-only">Open user menu</span>
                      <svg class="inline-block h-10 w-10 rounded-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                        <!-- Admin icon SVG code -->
                        <circle cx="100" cy="100" r="90" fill="#3f51b5" />
                        <path d="M100 30 L150 100 L130 140 H70 L50 100 Z" fill="none" stroke="white" stroke-width="6" stroke-linejoin="round" />
                        <line x1="70" y1="80" x2="130" y2="80" stroke="white" stroke-width="6" stroke-linecap="round" />
                        <line x1="80" y1="100" x2="120" y2="100" stroke="white" stroke-width="6" stroke-linecap="round" />
                        <line x1="90" y1="120" x2="110" y2="120" stroke="white" stroke-width="6" stroke-linecap="round" />
                        <path d="M70 60 L85 45 L100 60 L115 45 L130 60" fill="none" stroke="white" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="100" cy="100" r="90" fill="none" stroke="white" stroke-width="2" opacity="0.3" />
                        <circle cx="100" cy="100" r="85" fill="none" stroke="white" stroke-width="1" opacity="0.2" />
                      </svg>
                    </button>
                  </div>
                  <div id="user-dropdown" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu">
                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                    <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>
                    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Logout</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>

      <!-- Main content area -->
      <main class="flex-1 overflow-y-auto p-4 bg-gray-50">
        <div class="max-w-7xl mx-auto">
          <!-- Page header -->
          <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">User Management</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" onclick="openModal()">
                <i class="fas fa-plus mr-2 -ml-1 h-5 w-5"></i>
                Add User
              </button>
            </div>
          </div>

          <!-- Success and Error Messages -->
          <?php if (!empty($success_message)): ?>
            <div class="mt-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
              <p><?php echo $success_message; ?></p>
            </div>
          <?php endif; ?>

          <?php if (!empty($error_message)): ?>
            <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
              <p><?php echo $error_message; ?></p>
            </div>
          <?php endif; ?>

          <!-- User filters and search -->
          <div class="mt-6 bg-white shadow rounded-lg p-4">
            <form action="" method="GET" class="flex flex-col md:flex-row justify-between gap-4">
              <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <div class="relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                  </div>
                  <input type="text" name="search" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="Search users..." value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
                <div>
                  <select name="role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="">All Roles</option>
                    <?php foreach ($user_roles as $role): ?>
                      <option value="<?php echo $role; ?>" <?php echo ($role_filter === $role) ? 'selected' : ''; ?>>
                        <?php echo getRoleLabel($role); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div>
                  <select name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="all">All Status</option>
                    <?php foreach ($status_options as $status): ?>
                      <option value="<?php echo $status; ?>" <?php echo ($status_filter === $status) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($status); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-filter mr-2 h-5 w-5 text-gray-500"></i>
                  Filter
                </button>
                <a href="user.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-sync-alt mr-2 h-5 w-5 text-gray-500"></i>
                  Reset
                </a>
                <button type="button" onclick="exportUserData()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-download mr-2 h-5 w-5 text-gray-500"></i>
                  Export
                </button>
              </div>
            </form>
          </div>

          <!-- User Statistics Cards -->
          <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Users Card -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                    <i class="fas fa-users text-indigo-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Users
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo count($users); ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Active Users -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-user-check text-green-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Active Users
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php
                          $active_count = 0;
                          foreach ($users as $user) {
                            if ($user['status'] === 'active') {
                              $active_count++;
                            }
                          }
                          echo $active_count;
                          ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Locked/Pending Users -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-user-lock text-yellow-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Pending/Locked Users
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php
                          $pending_count = 0;
                          foreach ($users as $user) {
                            if ($user['status'] === 'pending' || $user['status'] === 'locked') {
                              $pending_count++;
                            }
                          }
                          echo $pending_count;
                          ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Admin Users -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <i class="fas fa-user-shield text-purple-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Admin Users
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php
                          $admin_count = 0;
                          foreach ($users as $user) {
                            if ($user['role'] === 'admin') {
                              $admin_count++;
                            }
                          }
                          echo $admin_count;
                          ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- User Table -->
          <div class="mt-6">
            <div class="flex flex-col">
              <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                  <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contract
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (count($users) > 0): ?>
                          <?php foreach ($users as $user): ?>
                            <tr>
                              <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                  <div class="flex-shrink-0 h-10 w-10">
                                    <?php if (!empty($user['profile_pic'])): ?>
                                      <img class="h-10 w-10 rounded-full object-cover" src="uploads/profile_pics/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile">
                                    <?php else: ?>
                                      <div class="h-10 w-10 rounded-full bg-primary-200 flex items-center justify-center">
                                        <span class="text-primary-600 font-medium text-lg">
                                          <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                        </span>
                                      </div>
                                    <?php endif; ?>
                                  </div>
                                  <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                      <?php echo htmlspecialchars($user['name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                      <?php echo htmlspecialchars($user['email']); ?>
                                    </div>
                                  </div>
                                </div>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo getRoleLabel($user['role']); ?></div>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['phone']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo substr(htmlspecialchars($user['cnic']), 0, 15); ?></div>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="text-sm text-gray-900">Start: <?php echo formatDate($user['contract_start']); ?></div>
                                <div class="text-sm text-gray-500">End: <?php echo formatDate($user['contract_end']); ?></div>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo getStatusBadgeClass($user['status']); ?>">
                                  <?php echo ucfirst($user['status']); ?>
                                </span>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                  <a href="?edit=<?php echo $user['id']; ?>" class="text-primary-600 hover:text-primary-900" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                  </a>
                                  <a href="?reset=<?php echo $user['id']; ?>" class="text-yellow-600 hover:text-yellow-900" title="Reset Password" onclick="return confirm('Are you sure you want to reset the password for this user?');">
                                    <i class="fas fa-key"></i>
                                  </a>
                                  <?php if ($user['id'] != 3): /* Don't allow deleting the main admin */ ?>
                                    <a href="?delete=<?php echo $user['id']; ?>" class="text-red-600 hover:text-red-900" title="Delete User" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                      <i class="fas fa-trash"></i>
                                    </a>
                                  <?php endif; ?>
                                  <!-- Replace the status dropdown section in the user table with this code -->

                                  <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" class="text-gray-600 hover:text-gray-900" title="Change Status">
                                      <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="open" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10" role="menu">
                                      <?php foreach ($status_options as $status_option): ?>
                                        <?php if ($status_option !== $user['status']): ?>
                                          <a href="?status=<?php echo $status_option; ?>&user_id=<?php echo $user['id']; ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                            Mark as <?php echo ucfirst($status_option); ?>
                                          </a>
                                        <?php endif; ?>
                                      <?php endforeach; ?>
                                    </div>
                                  </div>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                              No users found. <a href="javascript:void(0);" onclick="openModal()" class="text-primary-600 hover:text-primary-900">Add a user</a>.
                            </td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Mobile sidebar (hidden by default) -->
  <div id="mobile-sidebar" class="fixed inset-0 z-40 hidden">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-primary-700">
      <div class="absolute top-0 right-0 -mr-12 pt-2">
        <button id="closeSidebar" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
          <span class="sr-only">Close sidebar</span>
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
      <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
        <div class="flex-shrink-0 flex items-center px-4">
          <span class="text-2xl font-bold text-white">BS Traders</span>
        </div>
        <nav class="mt-5 px-2 space-y-1">
          <!-- Mobile sidebar navigation items -->
          <a href="index.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-home mr-4 h-6 w-6"></i>
            Dashboard
          </a>
          <!-- Other sidebar navigation items -->
        </nav>
      </div>
    </div>
  </div>

  <!-- Add/Edit User Modal -->
  <div id="userModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay with blur effect -->
      <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity" id="modalOverlay"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

      <!-- Modal container with rounded corners and subtle shadow -->
      <div class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-gray-200">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button type="button" class="bg-white rounded-full p-1 text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" onclick="closeModal()">
            <span class="sr-only">Close</span>
            <i class="fas fa-times h-5 w-5"></i>
          </button>
        </div>

        <div>
          <div class="sm:mt-0 sm:text-left">
            <!-- Title with decorative element -->
            <div class="flex items-center mb-4">
              <div class="bg-primary-600 h-8 w-1 rounded-full mr-3"></div>
              <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                <?php echo isset($edit_user) ? 'Edit User' : 'Add New User'; ?>
              </h3>
            </div>

            <div class="mt-4">
              <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="action" value="<?php echo isset($edit_user) ? 'edit' : 'add'; ?>">
                <?php if (isset($edit_user)): ?>
                  <input type="hidden" name="user_id" value="<?php echo $edit_user['id']; ?>">
                <?php endif; ?>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <!-- Full Name -->
                  <div class="sm:col-span-6">
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                      </div>
                      <input type="text" name="name" id="name" value="<?php echo isset($edit_user) ? htmlspecialchars($edit_user['name']) : ''; ?>" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg" required>
                    </div>
                  </div>

                  <!-- Email Address -->
                  <div class="sm:col-span-6">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                      </div>
                      <input type="email" name="email" id="email" value="<?php echo isset($edit_user) ? htmlspecialchars($edit_user['email']) : ''; ?>" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg" required>
                    </div>
                  </div>

                  <!-- Phone and CNIC -->
                  <div class="sm:col-span-3">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-phone text-gray-400"></i>
                      </div>
                      <input type="text" name="phone" id="phone" value="<?php echo isset($edit_user) ? htmlspecialchars($edit_user['phone']) : ''; ?>" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg" required>
                    </div>
                  </div>

                  <div class="sm:col-span-3">
                    <label for="cnic" class="block text-sm font-medium text-gray-700">CNIC Number</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400"></i>
                      </div>
                      <input type="text" name="cnic" id="cnic" value="<?php echo isset($edit_user) ? htmlspecialchars($edit_user['cnic']) : ''; ?>" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg" placeholder="XXXXX-XXXXXXX-X" required>
                    </div>
                  </div>

                  <!-- Contract Dates -->
                  <div class="sm:col-span-3">
                    <label for="contract_start" class="block text-sm font-medium text-gray-700">Contract Start Date</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                      </div>
                      <input type="date" name="contract_start" id="contract_start" value="<?php echo isset($edit_user) ? htmlspecialchars($edit_user['contract_start']) : date('Y-m-d'); ?>" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg" required>
                    </div>
                  </div>

                  <div class="sm:col-span-3">
                    <label for="contract_end" class="block text-sm font-medium text-gray-700">Contract End Date</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                      </div>
                      <input type="date" name="contract_end" id="contract_end" value="<?php echo isset($edit_user) ? htmlspecialchars($edit_user['contract_end']) : date('Y-m-d', strtotime('+1 year')); ?>" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg" required>
                    </div>
                  </div>

                  <!-- Address -->
                  <div class="sm:col-span-6">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                      </div>
                      <textarea name="address" id="address" rows="2" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg" required><?php echo isset($edit_user) ? htmlspecialchars($edit_user['address']) : ''; ?></textarea>
                    </div>
                  </div>

                  <!-- Password Fields (not required for edit) -->
                  <?php if (!isset($edit_user)): ?>
                    <div class="sm:col-span-3">
                      <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                      <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg" required>
                      </div>
                    </div>

                    <div class="sm:col-span-3">
                      <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                      <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="confirm_password" id="confirm_password" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg" required>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="sm:col-span-3">
                      <label for="password" class="block text-sm font-medium text-gray-700">New Password <span class="text-xs text-gray-500">(Leave blank to keep current)</span></label>
                      <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg">
                      </div>
                    </div>

                    <div class="sm:col-span-3">
                      <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                      <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                          <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="confirm_password" id="confirm_password" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg">
                      </div>
                    </div>
                  <?php endif; ?>

                  <!-- Role and Status -->
                  <div class="sm:col-span-3">
                    <label for="role" class="block text-sm font-medium text-gray-700">User Role</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user-tag text-gray-400"></i>
                      </div>
                      <select name="role" id="role" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg appearance-none" required>
                        <?php foreach ($user_roles as $role): ?>
                          <option value="<?php echo $role; ?>" <?php echo (isset($edit_user) && $edit_user['role'] === $role) ? 'selected' : ''; ?>>
                            <?php echo getRoleLabel($role); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                      </div>
                    </div>
                  </div>

                  <div class="sm:col-span-3">
                    <label for="status" class="block text-sm font-medium text-gray-700">Account Status</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-toggle-on text-gray-400"></i>
                      </div>
                      <select name="status" id="status" class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-lg appearance-none" required>
                        <?php foreach ($status_options as $status): ?>
                          <option value="<?php echo $status; ?>" <?php echo (isset($edit_user) && $edit_user['status'] === $status) ? 'selected' : ''; ?>>
                            <?php echo ucfirst($status); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                      </div>
                    </div>
                  </div>

                  <!-- Profile Picture -->
                  <div class="sm:col-span-6">
                    <label class="block text-sm font-medium text-gray-700">Profile Picture</label>
                    <div class="mt-1 flex items-center">
                      <div class="h-16 w-16 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                        <?php if (isset($edit_user) && !empty($edit_user['profile_pic'])): ?>
                          <img class="h-16 w-16 object-cover" src="uploads/profile_pics/<?php echo htmlspecialchars($edit_user['profile_pic']); ?>" alt="Current profile">
                        <?php else: ?>
                          <i class="fas fa-user text-gray-300 text-3xl"></i>
                        <?php endif; ?>
                      </div>
                      <div class="ml-5">
                        <div class="relative text-center">
                          <label for="profile_pic" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Choose File
                          </label>
                          <input id="profile_pic" name="profile_pic" type="file" class="sr-only" accept="image/*">
                          <p class="mt-1 text-xs text-gray-500"><?php echo isset($edit_user) && !empty($edit_user['profile_pic']) ? 'Replace current image' : 'Upload a profile picture'; ?></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row-reverse gap-3">
                  <button type="submit" class="w-full sm:w-auto flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo isset($edit_user) ? 'Update User' : 'Save User'; ?>
                  </button>
                  <button type="button" onclick="closeModal()" class="w-full sm:w-auto flex justify-center items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript for interactivity -->
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
  <script>
    // Date display
    const currentDate = new Date();
    const options = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    };
    document.getElementById('current-date').textContent = currentDate.toLocaleDateString('en-US', options);

    // User dropdown toggle
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');

    userMenuButton.addEventListener('click', () => {
      userDropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (event) => {
      if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
        userDropdown.classList.add('hidden');
      }
    });

    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const closeSidebar = document.getElementById('closeSidebar');

    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', () => {
        mobileSidebar.classList.remove('hidden');
      });
    }

    if (closeSidebar) {
      closeSidebar.addEventListener('click', () => {
        mobileSidebar.classList.add('hidden');
      });
    }

    // Modal functions
    function openModal() {
      document.getElementById('userModal').classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
      document.getElementById('userModal').classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }

    // Close modal when clicking outside
    document.getElementById('modalOverlay').addEventListener('click', closeModal);

    // Automatically show modal if edit parameter is present in URL
    <?php if (isset($_GET['edit'])): ?>
      document.addEventListener('DOMContentLoaded', function() {
        openModal();
      });
    <?php endif; ?>

    // Format CNIC input with dashes (XXXXX-XXXXXXX-X)
    const cnicInput = document.getElementById('cnic');
    if (cnicInput) {
      cnicInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');

        if (value.length > 5 && value.length <= 12) {
          value = value.slice(0, 5) + '-' + value.slice(5);
        } else if (value.length > 12) {
          value = value.slice(0, 5) + '-' + value.slice(5, 12) + '-' + value.slice(12, 13);
        }

        e.target.value = value;
      });
    }

    // Show filename when uploading profile picture
    const profilePicInput = document.getElementById('profile_pic');
    if (profilePicInput) {
      profilePicInput.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
          const fileNameDisplay = document.querySelector('label[for="profile_pic"] + p');
          if (fileNameDisplay) {
            fileNameDisplay.textContent = fileName;
          }
        }
      });
    }

    // Export user data as CSV
    function exportUserData() {
      window.location.href = 'export-users.php';
    }

    // Auto-hide success and error messages after 5 seconds
    window.addEventListener('DOMContentLoaded', () => {
      const alerts = document.querySelectorAll('[role="alert"]');
      if (alerts.length > 0) {
        setTimeout(() => {
          alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => {
              alert.style.display = 'none';
            }, 500);
          });
        }, 5000);
      }
    });

    // Check contract dates are valid (end date after start date)
    const contractForm = document.querySelector('form[action=""]');
    if (contractForm) {
      contractForm.addEventListener('submit', function(e) {
        const startDate = new Date(document.getElementById('contract_start').value);
        const endDate = new Date(document.getElementById('contract_end').value);

        if (endDate <= startDate) {
          e.preventDefault();
          alert('Contract end date must be after start date.');
          return false;
        }

        // Also check password match if visible and populated
        const passwordField = document.getElementById('password');
        const confirmField = document.getElementById('confirm_password');

        if (passwordField && confirmField && passwordField.value) {
          if (passwordField.value !== confirmField.value) {
            e.preventDefault();
            alert('Passwords do not match.');
            return false;
          }

          if (passwordField.value.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long.');
            return false;
          }
        }
      });
    }
  </script>

  <!-- PHP Export Script - Separate file 'export-users.php' -->
  <?php
  /*
  // Contents for export-users.php
  
  <?php
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
  
  // Fetch all users
  $query = "SELECT id, name, email, phone, cnic, address, role, status, contract_start, contract_end, created_at FROM users ORDER BY name";
  $result = $conn->query($query);
  
  // Add data rows
  while ($row = $result->fetch_assoc()) {
      // Remove password hash from export data
      unset($row['password']);
      fputcsv($output, $row);
  }
  
  // Close database connection
  $conn->close();
  ?>
  */
  ?>

</body>

</html>