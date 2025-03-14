<?php
// Include user authentication
require_once "../config/user-auth.php";

// Initialize variables
$task_id = isset($_GET['id']) ? $_GET['id'] : 0;
$status_updated = false;
$error_message = "";
$success_message = "";

// Check if task ID is provided
if (!$task_id) {
  header("Location: view-all-tasks.php");
  exit;
}

// Connect to database
require_once "../config/config.php";

// Process status update if submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
  $new_status = $_POST['new_status'];
  $comment = trim($_POST['comment']);

  // Update the task status
  $update_sql = "UPDATE tasks SET status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";

  if ($stmt = $conn->prepare($update_sql)) {
    $stmt->bind_param("sii", $new_status, $task_id, $_SESSION["user_id"]);

    if ($stmt->execute()) {
      $status_updated = true;
      $success_message = "Task status updated successfully.";

      // Add a status update record
      $insert_update_sql = "INSERT INTO task_updates (task_id, user_id, update_type, previous_status, new_status, comment) 
                           VALUES (?, ?, 'status_change', ?, ?, ?)";

      if ($update_stmt = $conn->prepare($insert_update_sql)) {
        $update_type = "status_change";
        $previous_status = $_POST['current_status'];

        $update_stmt->bind_param("iisss", $task_id, $_SESSION["user_id"], $previous_status, $new_status, $comment);
        $update_stmt->execute();
        $update_stmt->close();
      }
    } else {
      $error_message = "Error updating task status.";
    }

    $stmt->close();
  } else {
    $error_message = "Error preparing statement.";
  }
}

// Get the task details
$task = null;
$updates = [];

$sql = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
if ($stmt = $conn->prepare($sql)) {
  $stmt->bind_param("ii", $task_id, $_SESSION["user_id"]);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $task = $result->fetch_assoc();
  } else {
    // Task not found or doesn't belong to user
    header("Location: view-all-tasks.php");
    exit;
  }

  $stmt->close();
}

// Get task updates/comments
if ($task) {
  $updates_sql = "SELECT tu.*, u.name as user_name 
                 FROM task_updates tu 
                 JOIN users u ON tu.user_id = u.id 
                 WHERE tu.task_id = ? 
                 ORDER BY tu.created_at DESC";

  if ($stmt = $conn->prepare($updates_sql)) {
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $updates_result = $stmt->get_result();

    while ($update = $updates_result->fetch_assoc()) {
      $updates[] = $update;
    }

    $stmt->close();
  }
}

// Close connection
$conn->close();

// Format task data for display
$formatted_task_date = date("F j, Y", strtotime($task['task_date']));
$formatted_from_date = date("F j, Y", strtotime($task['from_date']));
$formatted_to_date = date("F j, Y", strtotime($task['to_date']));
$formatted_created_date = date("F j, Y g:i A", strtotime($task['created_at']));
$formatted_amount = number_format($task['amount'], 2);

// Format task status for display
$status_classes = [
  'pending' => 'bg-blue-100 text-blue-800',
  'in_progress' => 'bg-yellow-100 text-yellow-800',
  'completed' => 'bg-green-100 text-green-800',
  'cancelled' => 'bg-red-100 text-red-800'
];

$status_bg_classes = [
  'pending' => 'bg-blue-50',
  'in_progress' => 'bg-yellow-50',
  'completed' => 'bg-green-50',
  'cancelled' => 'bg-red-50'
];

$priority_classes = [
  'low' => 'bg-blue-100 text-blue-800',
  'medium' => 'bg-green-100 text-green-800',
  'high' => 'bg-yellow-100 text-yellow-800',
  'urgent' => 'bg-red-100 text-red-800'
];

$status_display = ucfirst(str_replace('_', ' ', $task['status']));
$priority_display = ucfirst($task['priority']);
$status_class = $status_classes[$task['status']] ?? 'bg-gray-100 text-gray-800';
$status_bg_class = $status_bg_classes[$task['status']] ?? 'bg-gray-50';
$priority_class = $priority_classes[$task['priority']] ?? 'bg-gray-100 text-gray-800';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Task Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
  <style>
    /* Base input styling */
    input[type="text"],
    input[type="date"],
    input[type="email"],
    input[type="number"],
    input[type="password"],
    textarea,
    select {
      border: 2px solid #d1d5db;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
      background-color: #f9fafb;
      transition: all 0.2s ease-in-out;
      outline: none;
      padding: 10px;
    }

    /* Hover state */
    input[type="text"]:hover,
    input[type="date"]:hover,
    input[type="email"]:hover,
    input[type="number"]:hover,
    input[type="password"]:hover,
    textarea:hover,
    select:hover {
      border-color: #9ca3af;
    }

    /* Focus state */
    input[type="text"]:focus,
    input[type="date"]:focus,
    input[type="email"]:focus,
    input[type="number"]:focus,
    input[type="password"]:focus,
    textarea:focus,
    select:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
    }

    /* Make the dropdown arrow more visible in select elements */
    select {
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
      background-position: right 0.5rem center;
      background-repeat: no-repeat;
      background-size: 1.5em 1.5em;
      padding-right: 2.5rem;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }
  </style>
</head>

<body class="bg-gray-50">
  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar / Navigation -->
    <div class="hidden md:flex md:flex-shrink-0">
      <div class="flex flex-col w-64">
        <div class="flex flex-col h-0 flex-1 bg-primary-700">
          <!-- Logo -->
          <div class="flex items-center h-16 flex-shrink-0 px-4 bg-primary-800">
            <span class="text-2xl font-bold text-white">BS Traders</span>
          </div>
          <!-- User info -->
          <div class="flex-shrink-0 flex border-t border-primary-800 p-4">
            <a href="user-profile.php" class="flex-shrink-0 group block">
              <div class="flex items-center">
                <div>
                  <img class="inline-block h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
                </div>
                <div class="ml-3">
                  <p class="text-base font-medium text-white"><?php echo htmlspecialchars($_SESSION["name"]); ?></p>
                  <p class="text-sm font-medium text-primary-200 group-hover:text-primary-100">Customer Account</p>
                </div>
              </div>
            </a>
          </div>
          <!-- Navigation -->
          <div class="flex-1 flex flex-col overflow-y-auto">
            <nav class="flex-1 px-2 py-4 space-y-1">
              <a href="index.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-home mr-4 h-6 w-6"></i>
                Dashboard
              </a>
              <a href="user-orders.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-shopping-cart mr-4 h-6 w-6"></i>
                My Orders
              </a>
              <a href="user-quotes.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-file-invoice-dollar mr-4 h-6 w-6"></i>
                Quotations
              </a>
              <a href="task-upload.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-upload mr-4 h-6 w-6"></i>
                Upload Task
              </a>
              <a href="view-all-tasks.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md bg-primary-800 text-white">
                <i class="fas fa-tasks mr-4 h-6 w-6"></i>
                My Tasks
              </a>
              <a href="user-products.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-boxes mr-4 h-6 w-6"></i>
                Products
              </a>
              <a href="user-invoices.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-file-invoice mr-4 h-6 w-6"></i>
                Invoices
              </a>
              <a href="user-support.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-headset mr-4 h-6 w-6"></i>
                Support
              </a>
              <a href="user-profile.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-user-circle mr-4 h-6 w-6"></i>
                My Profile
              </a>
              <a href="user-settings.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-cog mr-4 h-6 w-6"></i>
                Settings
              </a>
            </nav>
          </div>
        </div>
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
            <!-- Mobile navigation links (same as desktop) -->
            <a href="index.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
              <i class="fas fa-home mr-4 h-6 w-6"></i>
              Dashboard
            </a>
            <!-- Repeat other nav items here -->
          </nav>
        </div>
        <div class="flex-shrink-0 flex border-t border-primary-800 p-4">
          <a href="user-profile.php" class="flex-shrink-0 group block">
            <div class="flex items-center">
              <div>
                <img class="inline-block h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
              </div>
              <div class="ml-3">
                <p class="text-base font-medium text-white"><?php echo htmlspecialchars($_SESSION["name"]); ?></p>
                <p class="text-sm font-medium text-primary-200 group-hover:text-primary-100">Customer Account</p>
              </div>
            </div>
          </a>
        </div>
      </div>
      <div class="flex-shrink-0 w-14"></div>
    </div>

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
                  BS Traders Customer Portal
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
                <button class="p-1 ml-3 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 relative">
                  <i class="fas fa-bell h-6 w-6"></i>
                  <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                </button>
                <div class="ml-3 relative">
                  <div>
                    <button type="button" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="user-menu-button">
                      <span class="sr-only">Open user menu</span>
                      <img class="h-8 w-8 rounded-full" src="https://via.placeholder.com/150" alt="">
                    </button>
                  </div>
                  <div id="user-dropdown" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu">
                    <a href="user-profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                    <a href="user-settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>
                    <a href="../logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Logout</a>
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
            <div>
              <h3 class="text-lg leading-6 font-medium text-gray-900">Task Details</h3>
              <p class="mt-1 text-sm text-gray-500">
                View and manage your task
              </p>
            </div>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <a href="view-all-tasks.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-arrow-left mr-2 -ml-1 h-5 w-5"></i>
                Back to Tasks
              </a>
            </div>
          </div>

          <!-- Success and Error Messages -->
          <?php if (!empty($success_message)): ?>
            <div class="mt-6 rounded-md bg-green-50 p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <i class="fas fa-check-circle text-green-400 h-5 w-5"></i>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-800"><?php echo $success_message; ?></p>
                </div>
                <div class="ml-auto pl-3">
                  <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">
                      <span class="sr-only">Dismiss</span>
                      <i class="fas fa-times h-5 w-5"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($error_message)): ?>
            <div class="mt-6 rounded-md bg-red-50 p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <i class="fas fa-exclamation-circle text-red-400 h-5 w-5"></i>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-red-800"><?php echo $error_message; ?></p>
                </div>
                <div class="ml-auto pl-3">
                  <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">
                      <span class="sr-only">Dismiss</span>
                      <i class="fas fa-times h-5 w-5"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <!-- Task Details Card -->
          <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-4 py-5 sm:px-6">
              <div class="flex justify-between items-center">
                <div>
                  <h3 class="text-lg leading-6 font-medium text-white">
                    <?php echo htmlspecialchars($task['task_name']); ?>
                  </h3>
                  <p class="mt-1 max-w-2xl text-sm text-white opacity-80">
                    Task #<?php echo $task['id']; ?> • Created on <?php echo $formatted_created_date; ?>
                  </p>
                </div>
                <div class="flex space-x-2">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $status_class; ?>">
                    <?php echo $status_display; ?>
                  </span>
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $priority_class; ?>">
                    <?php echo $priority_display; ?> Priority
                  </span>
                </div>
              </div>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-6">
              <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Task Area</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($task['task_area']); ?></dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Task Date</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo $formatted_task_date; ?></dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo $formatted_from_date; ?></dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">End Date</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo $formatted_to_date; ?></dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Amount</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo $formatted_amount; ?></dd>
                </div>
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Assigned To</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    <?php echo !empty($task['assigned_to']) ? htmlspecialchars($task['assigned_to']) : 'Not assigned'; ?>
                  </dd>
                </div>
                <div class="sm:col-span-2">
                  <dt class="text-sm font-medium text-gray-500">Target Objectives</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($task['target']); ?></dd>
                </div>
                <?php if (!empty($task['description'])): ?>
                  <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line"><?php echo htmlspecialchars($task['description']); ?></dd>
                  </div>
                <?php endif; ?>
                <?php if (!empty($task['attachment'])): ?>
                  <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Attachment</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <a href="../uploads/tasks/<?php echo $task['attachment']; ?>" target="_blank" class="text-primary-600 hover:text-primary-900 flex items-center">
                        <i class="fas fa-paperclip mr-2"></i>
                        <span>Download Attachment</span>
                      </a>
                    </dd>
                  </div>
                <?php endif; ?>
              </dl>
            </div>
          </div>

          <!-- Update Task Status -->
          <div class="mt-6 bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Update Task Status</h3>
              <div class="mt-2 max-w-xl text-sm text-gray-500">
                <p>Change the status of this task and add a comment about the update.</p>
              </div>
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $task_id); ?>" method="POST" class="mt-5">
                <input type="hidden" name="current_status" value="<?php echo $task['status']; ?>">
                <div class="sm:flex sm:items-start sm:space-x-4">
                  <div class="w-full sm:max-w-xs">
                    <label for="new_status" class="block text-sm font-medium text-gray-700">New Status</label>
                    <select id="new_status" name="new_status" class="mt-1 block w-full sm:text-sm rounded-md py-2">
                      <option value="pending" <?php echo $task['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                      <option value="in_progress" <?php echo $task['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                      <option value="completed" <?php echo $task['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                      <option value="cancelled" <?php echo $task['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                  </div>
                  <div class="w-full mt-2 sm:mt-0">
                    <label for="comment" class="block text-sm font-medium text-gray-700">Comment (Optional)</label>
                    <div class="mt-1">
                      <textarea id="comment" name="comment" rows="2" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-2 border-gray-300 bg-gray-50 rounded-md"></textarea>
                    </div>
                  </div>
                </div>
                <div class="mt-5">
                  <button type="submit" name="update_status" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Update Status
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Task Timeline / Updates -->
          <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
              <h3 class="text-lg font-medium leading-6 text-gray-900">Task Timeline</h3>
              <p class="mt-1 max-w-2xl text-sm text-gray-500">Updates and activity history for this task.</p>
            </div>
            <div class="border-t border-gray-200">
              <?php if (count($updates) > 0): ?>
                <ul class="divide-y divide-gray-200">
                  <?php foreach ($updates as $update): ?>
                    <?php
                    $update_time = date("F j, Y g:i A", strtotime($update['created_at']));
                    $update_icon = 'fa-circle-info';
                    $update_bg = 'bg-blue-100 text-blue-600';

                    switch ($update['update_type']) {
                      case 'status_change':
                        $update_icon = 'fa-sync-alt';

                        switch ($update['new_status']) {
                          case 'in_progress':
                            $update_bg = 'bg-yellow-100 text-yellow-600';
                            break;
                          case 'completed':
                            $update_icon = 'fa-check-circle';
                            $update_bg = 'bg-green-100 text-green-600';
                            break;
                          case 'cancelled':
                            $update_icon = 'fa-times-circle';
                            $update_bg = 'bg-red-100 text-red-600';
                            break;
                          default:
                            $update_bg = 'bg-blue-100 text-blue-600';
                        }
                        break;
                      case 'comment':
                        $update_icon = 'fa-comment';
                        $update_bg = 'bg-indigo-100 text-indigo-600';
                        break;
                      case 'attachment':
                        $update_icon = 'fa-paperclip';
                        $update_bg = 'bg-purple-100 text-purple-600';
                        break;
                      case 'edit':
                        $update_icon = 'fa-edit';
                        $update_bg = 'bg-gray-100 text-gray-600';
                        break;
                    }
                    ?>
                    <li class="px-4 py-4 sm:px-6">
                      <div class="flex items-start">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full <?php echo $update_bg; ?> flex items-center justify-center">
                          <i class="fas <?php echo $update_icon; ?>"></i>
                        </div>
                        <div class="ml-4 flex-1">
                          <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">
                              <?php echo htmlspecialchars($update['user_name']); ?>
                            </p>
                            <p class="text-sm text-gray-500"><?php echo $update_time; ?></p>
                          </div>
                          <div class="mt-1">
                            <?php if ($update['update_type'] == 'status_change'): ?>
                              <p class="text-sm text-gray-700">
                                Changed status from
                                <span class="font-medium"><?php echo ucfirst(str_replace('_', ' ', $update['previous_status'])); ?></span>
                                to
                                <span class="font-medium"><?php echo ucfirst(str_replace('_', ' ', $update['new_status'])); ?></span>
                              </p>
                            <?php endif; ?>

                            <?php if (!empty($update['comment'])): ?>
                              <p class="mt-1 text-sm text-gray-700 whitespace-pre-line"><?php echo htmlspecialchars($update['comment']); ?></p>
                            <?php endif; ?>

                            <?php if (!empty($update['attachment'])): ?>
                              <p class="mt-1 text-sm text-gray-700">
                                <a href="../uploads/tasks/updates/<?php echo $update['attachment']; ?>" target="_blank" class="text-primary-600 hover:text-primary-900 flex items-center">
                                  <i class="fas fa-paperclip mr-2"></i>
                                  <span>View Attachment</span>
                                </a>
                              </p>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php else: ?>
                <div class="px-4 py-5 sm:p-6 text-center text-gray-500">
                  <p>No updates available for this task.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- JavaScript for interactivity -->
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

    sidebarToggle.addEventListener('click', () => {
      mobileSidebar.classList.remove('hidden');
    });

    closeSidebar.addEventListener('click', () => {
      mobileSidebar.classList.add('hidden');
    });

    // Alert dismissal
    const closeButtons = document.querySelectorAll('.bg-green-50 button, .bg-red-50 button');
    closeButtons.forEach(button => {
      button.addEventListener('click', () => {
        button.closest('.rounded-md').style.display = 'none';
      });
    });
  </script>
</body>

</html>