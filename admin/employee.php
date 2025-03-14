<?php
// Include admin authentication
require_once "../config/admin-auth.php";
require_once "../config/config.php";

// Initialize variables
$error = "";
$success = "";

// Handle form submission for adding new employee
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'add_employee') {
  // Get form data
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);
  $cnic = trim($_POST['cnic']);
  $contract_start = trim($_POST['contract_start']);
  $contract_end = trim($_POST['contract_end']);
  $password = trim($_POST['password']);
  $role = 'employee'; // Default role for employees

  // Validate inputs
  if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($cnic) || empty($contract_start) || empty($contract_end) || empty($password)) {
    $error = "All required fields must be filled";
  } else {
    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
      $error = "Email already exists in the system";
      $check_stmt->close();
    } else {
      $check_stmt->close();

      // Handle profile picture upload
      $profile_pic = "";
      if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "../uploads/profile/";

        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
          mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        // Check file type
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array(strtolower($file_extension), $allowed_types)) {
          if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $profile_pic = $file_name;
          } else {
            $error = "Failed to upload profile picture";
          }
        } else {
          $error = "Only JPG, JPEG, PNG & GIF files are allowed";
        }
      }

      // If no errors, insert the new employee
      if (empty($error)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, phone, address, cnic, contract_start, contract_end, profile_pic, password, role) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssss", $name, $email, $phone, $address, $cnic, $contract_start, $contract_end, $profile_pic, $hashed_password, $role);

        if ($stmt->execute()) {
          $success = "Employee added successfully";
        } else {
          $error = "Error: " . $stmt->error;
        }

        $stmt->close();
      }
    }
  }
}

// Handle delete employee
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
  $delete_id = intval($_GET['delete_id']);

  // First get the profile pic name to delete the file
  $pic_sql = "SELECT profile_pic FROM users WHERE id = ?";
  $pic_stmt = $conn->prepare($pic_sql);
  $pic_stmt->bind_param("i", $delete_id);
  $pic_stmt->execute();
  $pic_result = $pic_stmt->get_result();

  if ($pic_row = $pic_result->fetch_assoc()) {
    if (!empty($pic_row['profile_pic'])) {
      $file_path = "../uploads/profile/" . $pic_row['profile_pic'];
      if (file_exists($file_path)) {
        unlink($file_path);
      }
    }
  }

  $pic_stmt->close();

  // Now delete the user record
  $sql = "DELETE FROM users WHERE id = ? AND (role = 'employee' OR role = 'user')";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $delete_id);

  if ($stmt->execute()) {
    $success = "Employee deleted successfully";
  } else {
    $error = "Error deleting employee: " . $stmt->error;
  }

  $stmt->close();
}

// Get employees for display
$employees = array();
$sql = "SELECT * FROM users WHERE role = 'employee' OR role = 'user' ORDER BY id DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
  }
}
// Handle toggle employee status
if (isset($_GET['toggle_status']) && !empty($_GET['toggle_status'])) {
  $employee_id = intval($_GET['toggle_status']);

  // Get current status
  $status_sql = "SELECT status FROM users WHERE id = ?";
  $status_stmt = $conn->prepare($status_sql);
  $status_stmt->bind_param("i", $employee_id);
  $status_stmt->execute();
  $status_result = $status_stmt->get_result();

  if ($status_row = $status_result->fetch_assoc()) {
    // Toggle status between active and inactive
    $new_status = ($status_row['status'] == 'active') ? 'inactive' : 'active';

    // Update the user's status
    $update_sql = "UPDATE users SET status = ? WHERE id = ? AND (role = 'employee' OR role = 'user')";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $employee_id);

    if ($update_stmt->execute()) {
      $success = "Employee status updated successfully";

      // Redirect to remove query parameters from URL
      header("Location: employee.php?status_updated=1");
      exit;
    } else {
      $error = "Error updating employee status: " . $update_stmt->error;
    }

    $update_stmt->close();
  }

  $status_stmt->close();
}

// Display success message after redirect
if (isset($_GET['status_updated']) && $_GET['status_updated'] == 1) {
  $success = "Employee status updated successfully";
}
// Handle form submission for editing employee
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit_employee') {
  // Get form data
  $employee_id = intval($_POST['employee_id']);
  $name = trim($_POST['name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);
  $cnic = trim($_POST['cnic']);
  $contract_start = trim($_POST['contract_start']);
  $contract_end = trim($_POST['contract_end']);
  $status = trim($_POST['status']);
  $role = trim($_POST['role']);
  $password = trim($_POST['password']);

  // Validate inputs
  if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($cnic) || empty($contract_start) || empty($contract_end)) {
    $error = "All required fields must be filled";
  } else {
    // Check if email already exists for other employees
    $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $email, $employee_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
      $error = "Email already exists in the system";
      $check_stmt->close();
    } else {
      $check_stmt->close();

      // Handle profile picture upload if a new one is provided
      $profile_pic_sql = "";
      $profile_pic_param = "";

      if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        // Get the old profile pic to delete it later
        $old_pic_sql = "SELECT profile_pic FROM users WHERE id = ?";
        $old_pic_stmt = $conn->prepare($old_pic_sql);
        $old_pic_stmt->bind_param("i", $employee_id);
        $old_pic_stmt->execute();
        $old_pic_result = $old_pic_stmt->get_result();
        $old_pic = '';

        if ($old_pic_row = $old_pic_result->fetch_assoc()) {
          $old_pic = $old_pic_row['profile_pic'];
        }

        $old_pic_stmt->close();

        $target_dir = "../uploads/profile/";

        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
          mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        // Check file type
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array(strtolower($file_extension), $allowed_types)) {
          if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $profile_pic_sql = ", profile_pic = ?";
            $profile_pic_param = $file_name;

            // Delete old profile picture if it exists
            if (!empty($old_pic)) {
              $old_file_path = $target_dir . $old_pic;
              if (file_exists($old_file_path)) {
                unlink($old_file_path);
              }
            }
          } else {
            $error = "Failed to upload profile picture";
          }
        } else {
          $error = "Only JPG, JPEG, PNG & GIF files are allowed";
        }
      }

      // If no errors, update the employee
      if (empty($error)) {
        // Prepare SQL based on whether password is provided or not
        if (!empty($password)) {
          $hashed_password = password_hash($password, PASSWORD_DEFAULT);
          $sql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, cnic = ?, 
                 contract_start = ?, contract_end = ?, status = ?, role = ?, password = ?";

          if (!empty($profile_pic_sql)) {
            $sql .= $profile_pic_sql;
          }

          $sql .= " WHERE id = ?";

          $stmt = $conn->prepare($sql);

          if (!empty($profile_pic_sql)) {
            $stmt->bind_param(
              "sssssssssssi",
              $name,
              $email,
              $phone,
              $address,
              $cnic,
              $contract_start,
              $contract_end,
              $status,
              $role,
              $hashed_password,
              $profile_pic_param,
              $employee_id
            );
          } else {
            $stmt->bind_param(
              "sssssssssi",
              $name,
              $email,
              $phone,
              $address,
              $cnic,
              $contract_start,
              $contract_end,
              $status,
              $role,
              $hashed_password,
              $employee_id
            );
          }
        } else {
          $sql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, cnic = ?, 
                 contract_start = ?, contract_end = ?, status = ?, role = ?";

          if (!empty($profile_pic_sql)) {
            $sql .= $profile_pic_sql;
          }

          $sql .= " WHERE id = ?";

          $stmt = $conn->prepare($sql);

          if (!empty($profile_pic_sql)) {
            // Fix: Properly bind 11 parameters with correct type string
            $stmt->bind_param(
              "sssssssssi",
              $name,
              $email,
              $phone,
              $address,
              $cnic,
              $contract_start,
              $contract_end,
              $status,
              $role,
              $employee_id
            );
          } else {
            $stmt->bind_param(
              "sssssssssi",
              $name,
              $email,
              $phone,
              $address,
              $cnic,
              $contract_start,
              $contract_end,
              $status,
              $role,
              $employee_id
            );
          }
        }

        if ($stmt->execute()) {
          $success = "Employee updated successfully";

          // Redirect to prevent form resubmission on refresh
          header("Location: employee.php?edit_success=1");
          exit;
        } else {
          $error = "Error updating employee: " . $stmt->error;
        }

        $stmt->close();
      }
    }
  }
}

// Display success message after edit redirect
if (isset($_GET['edit_success']) && $_GET['edit_success'] == 1) {
  $success = "Employee updated successfully";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Employee Management</title>
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
              <a href="employee.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-white bg-primary-800">
                <i class="fas fa-users mr-3 h-6 w-6"></i>
                Employee Management
              </a>
              <a href="user.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
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
                  <p class="text-base font-medium text-white"><?php echo isset($_SESSION["name"]) ? htmlspecialchars($_SESSION["name"]) : "Admin User"; ?></p>
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
                      </svg> </button>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900">Employee Management</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" onclick="openModal()">
                <i class="fas fa-plus mr-2 -ml-1 h-5 w-5"></i>
                Add Employee
              </button>
            </div>
          </div>

          <!-- Display success and error messages -->
          <?php if (!empty($success)): ?>
            <div class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
              <strong class="font-bold">Success!</strong>
              <span class="block sm:inline"><?php echo $success; ?></span>
              <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <title>Close</title>
                  <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                </svg>
              </span>
            </div>
          <?php endif; ?>

          <?php if (!empty($error)): ?>
            <div class="mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
              <strong class="font-bold">Error!</strong>
              <span class="block sm:inline"><?php echo $error; ?></span>
              <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                  <title>Close</title>
                  <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                </svg>
              </span>
            </div>
          <?php endif; ?>

          <!-- Employee filters and search -->
          <!-- Improved Employee filters and search section -->
          <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
            <!-- Section header with gradient background -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-500 px-4 py-3">
              <h3 class="text-lg font-medium text-white">Filter Employees</h3>
            </div>

            <div class="p-4 bg-white shadow-md rounded-lg">
              <div class="flex flex-col md:flex-row justify-between gap-4">
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                  <!-- Search Input with better UI -->
                  <div class="relative flex-grow">
                    <input type="text" id="search-input" class="w-full pl-12 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 ease-in-out hover:border-primary-400 shadow-sm" placeholder="Search employees...">
                    <div class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                      <i class="fas fa-search"></i>
                    </div>
                  </div>

                  <!-- Contract Filter Dropdown -->
                  <div class="relative">
                    <select id="contract-filter" class="block w-full px-4 py-2 text-sm border border-gray-300 bg-white rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 ease-in-out hover:border-primary-400 shadow-sm">
                      <option value="">All Contracts</option>
                      <option value="active">Active Contract</option>
                      <option value="expired">Expired Contract</option>
                      <option value="terminating">Ending Soon</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

          </div>


          <!-- Employee Table -->
          <div class="mt-6">
            <div class="flex flex-col">
              <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                  <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200" id="employees-table">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact Info
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            CNIC
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contract Period
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
                        <?php
                        foreach ($employees as $employee):
                          // Determine contract status
                          $today = new DateTime();
                          $contract_end_date = new DateTime($employee['contract_end']);
                          $diff_days = $today->diff($contract_end_date)->days;

                          if ($contract_end_date < $today) {
                            $status = 'expired';
                            $status_class = 'bg-red-100 text-red-800';
                            $status_text = 'Expired';
                          } elseif ($diff_days <= 7) {
                            $status = 'ending-soon';
                            $status_class = 'bg-yellow-100 text-yellow-800';
                            $status_text = 'Ending Soon';
                          } else {
                            $status = 'active';
                            $status_class = 'bg-green-100 text-green-800';
                            $status_text = 'Active';
                          }
                        ?>
                          <tr data-status="<?php echo $status; ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                  <?php if (!empty($employee['profile_pic'])): ?>
                                    <img class="h-10 w-10 rounded-full" src="../uploads/profile/<?php echo htmlspecialchars($employee['profile_pic']); ?>" alt="Profile">
                                  <?php else: ?>
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                      <i class="fas fa-user text-gray-500"></i>
                                    </div>
                                  <?php endif; ?>
                                </div>
                                <div class="ml-4">
                                  <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($employee['name']); ?>
                                  </div>
                                  <div class="text-sm text-gray-500">
                                    <?php echo ucfirst($employee['role']); ?>
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="text-sm text-gray-900"><?php echo htmlspecialchars($employee['email']); ?></div>
                              <div class="text-sm text-gray-500"><?php echo htmlspecialchars($employee['phone']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              <?php echo htmlspecialchars($employee['cnic']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              <?php
                              $start_date = new DateTime($employee['contract_start']);
                              $end_date = new DateTime($employee['contract_end']);
                              echo $start_date->format('M Y') . ' - ' . $end_date->format('M Y');
                              ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                              </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                              <a href="#" class="text-primary-600 hover:text-primary-900 mr-3" onclick="openEditModal(<?php echo $employee['id']; ?>)">
                                <i class="fas fa-edit"></i>
                              </a>

                              <?php if ($employee['status'] == 'active'): ?>
                                <a href="employee.php?toggle_status=<?php echo $employee['id']; ?>" class="text-green-600 hover:text-red-600 mr-3" title="Deactivate User">
                                  <i class="fas fa-toggle-on"></i>
                                </a>
                              <?php else: ?>
                                <a href="employee.php?toggle_status=<?php echo $employee['id']; ?>" class="text-gray-400 hover:text-green-600 mr-3" title="Activate User">
                                  <i class="fas fa-toggle-off"></i>
                                </a>
                              <?php endif; ?>

                              <a href="employee.php?delete_id=<?php echo $employee['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this employee?')">
                                <i class="fas fa-trash"></i>
                              </a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Add Employee Modal -->
          <!-- Add Employee Modal -->
          <div id="addEmployeeModal" class="fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">
              <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Add New Employee</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
              </div>
              <form action="employee.php" method="POST" enctype="multipart/form-data" class="px-6 py-4 space-y-4">
                <input type="hidden" name="action" value="add_employee">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Full Name</label>
                  <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" required class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Address</label>
                  <input type="text" name="address" required class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">CNIC</label>
                    <input type="text" name="cnic" required class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" required class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Contract Start Date</label>
                    <input type="date" name="contract_start" required class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Contract End Date</label>
                    <input type="date" name="contract_end" required class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                  </div>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Profile Picture</label>
                  <input type="file" name="photo" class="w-full px-3 py-2 border rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                  <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</button>
                  <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Add Employee</button>
                </div>
              </form>
            </div>
          </div>


          <!-- Edit Employee Modal -->
          <div id="editEmployeeModal" class="overflow-auto fixed inset-0 z-50 flex items-center justify-center hidden bg-gray-900 bg-opacity-50">
            <div class="bg-white rounded-2xl shadow-lg transform transition-all w-full max-w-lg p-6">
              <form action="employee.php" method="POST" enctype="multipart/form-data" id="edit-employee-form">
                <input type="hidden" name="action" value="edit_employee">
                <input type="hidden" name="employee_id" id="edit-employee-id">
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <div class="mb-5">
                  <h3 class="text-xl font-semibold text-gray-900">Edit Employee</h3>
                </div>

                <div class="space-y-4">
                  <div>
                    <label for="edit-name" class="text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" id="edit-name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label for="edit-email" class="text-sm font-medium text-gray-700">Email</label>
                      <input type="email" name="email" id="edit-email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                      <label for="edit-phone" class="text-sm font-medium text-gray-700">Phone Number</label>
                      <input type="text" name="phone" id="edit-phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                  </div>

                  <div>
                    <label for="edit-address" class="text-sm font-medium text-gray-700">Address</label>
                    <input type="text" name="address" id="edit-address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label for="edit-cnic" class="text-sm font-medium text-gray-700">CNIC</label>
                      <input type="text" name="cnic" id="edit-cnic" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                      <label for="edit-status" class="text-sm font-medium text-gray-700">Account Status</label>
                      <select name="status" id="edit-status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="locked">Locked</option>
                        <option value="pending">Pending</option>
                      </select>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <label for="edit-contract-start" class="text-sm font-medium text-gray-700">Contract Start</label>
                      <input type="date" name="contract_start" id="edit-contract-start" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                      <label for="edit-contract-end" class="text-sm font-medium text-gray-700">Contract End</label>
                      <input type="date" name="contract_end" id="edit-contract-end" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                  </div>

                  <div>
                    <label for="edit-role" class="text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="edit-role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                      <option value="user">User</option>
                      <option value="employee">Employee</option>
                    </select>
                  </div>

                  <div>
                    <label for="edit-password" class="text-sm font-medium text-gray-700">New Password (optional)</label>
                    <input type="password" name="password" id="edit-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  </div>

                  <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 rounded-full bg-gray-100 overflow-hidden">
                      <img id="current-profile-pic" class="h-12 w-12 object-cover" src="" alt="Profile">
                    </div>
                    <input type="file" name="photo" id="edit-photo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                  </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                  <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-900 rounded-lg hover:bg-gray-400 transition">Cancel</button>
                  <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Update Employee</button>
                </div>
              </form>
            </div>
          </div>

      </main>
    </div>
  </div>
  <!-- Mobile sidebar (hidden by default) -->
  <div id="mobile-sidebar" class="fixed inset-0 z-40 hidden">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-primary-700">
      <div class="absolute top-0 right-0  pt-2">
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
          <a href="index.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-home mr-4 h-6 w-6"></i>
            Dashboard
          </a>
          <a href="employee.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md bg-primary-800 text-white">
            <i class="fas fa-users mr-4 h-6 w-6"></i>
            Employee Management
          </a>
          <a href="user.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-user-shield mr-4 h-6 w-6"></i>
            User Management
          </a>
          <a href="salary.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-money-bill-wave mr-4 h-6 w-6"></i>
            Salary Management
          </a>
          <a href="expense.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-file-invoice-dollar mr-4 h-6 w-6"></i>
            Expense Management
          </a>
          <a href="task.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-tasks mr-4 h-6 w-6"></i>
            Task Management
          </a>
          <a href="reports.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-chart-bar mr-4 h-6 w-6"></i>
            Reports
          </a>
          <a href="settings.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-cog mr-4 h-6 w-6"></i>
            Settings
          </a>
        </nav>
      </div>
      <div class="flex-shrink-0 flex border-t border-primary-800 p-4">
        <a href="profile.php" class="flex-shrink-0 group block">
          <div class="flex items-center">
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
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
              <p class="text-base font-medium text-white"><?php echo htmlspecialchars($_SESSION["name"]); ?></p>
              <p class="text-sm font-medium text-primary-200 group-hover:text-primary-100">View profile</p>
            </div>
          </div>
        </a>
      </div>
    </div>
    <div class="flex-shrink-0 w-14"></div>
  </div>
  <script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
      const filterBtn = document.getElementById('filter-btn');
      const contractFilter = document.getElementById('contract-filter');
      const searchInput = document.getElementById('search-input');

      // Search functionality
      if (searchInput) {
        searchInput.addEventListener('keyup', function() {
          filterEmployees();
        });
      }

      // Filter button click event
      if (filterBtn) {
        filterBtn.addEventListener('click', function() {
          filterEmployees();
        });
      }

      // Apply filters directly when contract filter changes
      if (contractFilter) {
        contractFilter.addEventListener('change', function() {
          filterEmployees();
        });
      }

      // Combined filter function
      function filterEmployees() {
        const searchValue = searchInput.value.toLowerCase();
        const contractValue = contractFilter.value;

        const rows = document.querySelectorAll('#employees-table tbody tr');

        rows.forEach(row => {
          let showRow = true;

          // Text search filter
          if (searchValue) {
            const text = row.textContent.toLowerCase();
            if (!text.includes(searchValue)) {
              showRow = false;
            }
          }

          // Contract status filter
          if (contractValue && showRow) {
            const rowStatus = row.getAttribute('data-status');

            // Match the filter value with data-status attribute
            switch (contractValue) {
              case 'active':
                if (rowStatus !== 'active') {
                  showRow = false;
                }
                break;
              case 'expired':
                if (rowStatus !== 'expired') {
                  showRow = false;
                }
                break;
              case 'terminating':
                if (rowStatus !== 'ending-soon') {
                  showRow = false;
                }
                break;
            }
          }

          // Show or hide the row
          row.style.display = showRow ? '' : 'none';
        });
      }
    });
  </script>
  <!-- JavaScript for employee page functionality -->
  <script>
    // Current date display
    document.addEventListener('DOMContentLoaded', function() {
      const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      };
      const currentDate = new Date().toLocaleDateString('en-US', options);
      document.getElementById('current-date').textContent = currentDate;

      // User dropdown toggle
      const userMenuButton = document.getElementById('user-menu-button');
      const userDropdown = document.getElementById('user-dropdown');

      userMenuButton.addEventListener('click', function() {
        userDropdown.classList.toggle('hidden');
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(event) {
        if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
          userDropdown.classList.add('hidden');
        }
      });

      // Sidebar toggle for mobile
      const sidebarToggle = document.getElementById('sidebarToggle');
      if (sidebarToggle) {
        // Mobile sidebar toggle implementation would go here
      }

      // Search functionality
      const searchInput = document.getElementById('search-input');
      if (searchInput) {
        searchInput.addEventListener('keyup', function() {
          const searchValue = this.value.toLowerCase();
          const rows = document.querySelectorAll('#employees-table tbody tr');

          rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchValue)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }

      // Filter functionality
      const filterBtn = document.getElementById('filter-btn');
      if (filterBtn) {
        filterBtn.addEventListener('click', function() {
          const departmentFilter = document.getElementById('department-filter').value;
          const contractFilter = document.getElementById('contract-filter').value;

          const rows = document.querySelectorAll('#employees-table tbody tr');

          rows.forEach(row => {
            let showRow = true;

            if (contractFilter && row.getAttribute('data-status') !== contractFilter) {
              showRow = false;
            }

            // Department filtering would be implemented here

            row.style.display = showRow ? '' : 'none';
          });
        });
      }
    });

    // Modal functions
    function openModal() {
      document.getElementById('addEmployeeModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('addEmployeeModal').classList.add('hidden');
    }

    function openEditModal(employeeId) {
      // Fetch employee data and populate form
      document.getElementById('edit-employee-id').value = employeeId;
      document.getElementById('editEmployeeModal').classList.remove('hidden');

      // AJAX request to get employee data would go here
    }

    function closeEditModal() {
      document.getElementById('editEmployeeModal').classList.add('hidden');
    }
  </script>
  <script>
    function openEditModal(employeeId) {
      // Set employee ID in the form
      document.getElementById('edit-employee-id').value = employeeId;

      // Fetch employee data with AJAX
      fetch(`get_employee.php?id=${employeeId}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Failed to fetch employee data');
          }
          return response.json();
        })
        .then(employee => {
          // Populate form fields with employee data
          document.getElementById('edit-name').value = employee.name;
          document.getElementById('edit-email').value = employee.email;
          document.getElementById('edit-phone').value = employee.phone;
          document.getElementById('edit-address').value = employee.address;
          document.getElementById('edit-cnic').value = employee.cnic;
          document.getElementById('edit-contract-start').value = employee.contract_start;
          document.getElementById('edit-contract-end').value = employee.contract_end;
          document.getElementById('edit-status').value = employee.status;
          document.getElementById('edit-role').value = employee.role;

          // Set profile image if available
          const profilePreview = document.getElementById('current-profile-pic');
          if (employee.profile_pic) {
            profilePreview.src = `../uploads/profile/${employee.profile_pic}`;
            profilePreview.style.display = 'block';
          } else {
            profilePreview.style.display = 'none';
          }

          // Show the modal
          document.getElementById('editEmployeeModal').classList.remove('hidden');
        })
        .catch(error => {
          console.error('Error fetching employee data:', error);
          alert('Failed to load employee data. Please try again.');
        });
    }
    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebar = document.getElementById('mobile-sidebar');
    const closeSidebar = document.getElementById('closeSidebar');

    sidebarToggle.addEventListener('click', () => {
      mobileSidebar.classList.remove('hidden');
    });

    closeSidebar.addEventListener('click', () => {
      mobileSidebar.classList.add('hidden');
    });
  </script>
</body>

</html>