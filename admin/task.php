<?php
// Include admin authentication
require_once "../config/admin-auth.php";
require_once "../config/config.php";

// Handle task deletion if requested
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
  $task_id = $_GET['delete'];
  $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
  $stmt->bind_param("i", $task_id);

  if ($stmt->execute()) {
    $success_message = "Task deleted successfully!";
  } else {
    $error_message = "Error deleting task: " . $conn->error;
  }
  $stmt->close();
}

// Handle task status update if requested
if (isset($_GET['update_status']) && is_numeric($_GET['update_status']) && isset($_GET['status'])) {
  $task_id = $_GET['update_status'];
  $status = $_GET['status'];
  $completed_at = null;

  if ($status == 'completed') {
    $completed_at = date('Y-m-d H:i:s');
  }

  $stmt = $conn->prepare("UPDATE tasks SET status = ?, completed_at = ? WHERE id = ?");
  $stmt->bind_param("ssi", $status, $completed_at, $task_id);

  if ($stmt->execute()) {
    $success_message = "Task status updated successfully!";
  } else {
    $error_message = "Error updating task status: " . $conn->error;
  }
  $stmt->close();
}

// Get task counts for summary cards
$query_total = "SELECT COUNT(*) as total FROM tasks";
$query_completed = "SELECT COUNT(*) as count FROM tasks WHERE status = 'completed'";
$query_in_progress = "SELECT COUNT(*) as count FROM tasks WHERE status = 'in_progress'";
$query_pending = "SELECT COUNT(*) as count FROM tasks WHERE status = 'pending'";
$query_overdue = "SELECT COUNT(*) as count FROM tasks WHERE to_date < CURDATE() AND status != 'completed' AND status != 'cancelled'";

$total_tasks = $conn->query($query_total)->fetch_assoc()['total'];
$completed_tasks = $conn->query($query_completed)->fetch_assoc()['count'];
$in_progress_tasks = $conn->query($query_in_progress)->fetch_assoc()['count'];
$pending_tasks = $conn->query($query_pending)->fetch_assoc()['count'];
$overdue_tasks = $conn->query($query_overdue)->fetch_assoc()['count'];

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

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Count total items after filtering
$count_query = "SELECT COUNT(*) as total FROM tasks $where_clause";
$stmt_count = $conn->prepare($count_query);

// Bind search parameters
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
  $stmt_count->bind_param($param_types, ...$param_values);
}

$stmt_count->execute();
$total_filtered = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_filtered / $items_per_page);
$stmt_count->close();

// Get tasks with pagination and filtering
$query = "SELECT * FROM tasks $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);

// Add pagination parameters
$param_types .= "ii";
$param_values[] = $items_per_page;
$param_values[] = $offset;

if (!empty($param_values)) {
  $stmt->bind_param($param_types, ...$param_values);
}

$stmt->execute();
$tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get distinct task areas for filter dropdown
$query_areas = "SELECT DISTINCT task_area FROM tasks ORDER BY task_area";
$task_areas = $conn->query($query_areas)->fetch_all(MYSQLI_ASSOC);

// Get distinct assignees for filter dropdown
$query_assignees = "SELECT DISTINCT assigned_to FROM tasks WHERE assigned_to IS NOT NULL ORDER BY assigned_to";
$assignees = $conn->query($query_assignees)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Task Management</title>
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
    <?php include 'includes/sidebar.php' ?>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900">Task Management</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" onclick="openAddTaskModal()">
                <i class="fas fa-plus mr-2 -ml-1 h-5 w-5"></i>
                Add Task
              </button>
            </div>
          </div>

          <?php if (isset($success_message)): ?>
            <div class="mt-4 p-4 rounded-md bg-green-50">
              <div class="flex">
                <div class="flex-shrink-0">
                  <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-800"><?php echo $success_message; ?></p>
                </div>
                <div class="ml-auto pl-3">
                  <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                      <span class="sr-only">Dismiss</span>
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if (isset($error_message)): ?>
            <div class="mt-4 p-4 rounded-md bg-red-50">
              <div class="flex">
                <div class="flex-shrink-0">
                  <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-red-800"><?php echo $error_message; ?></p>
                </div>
                <div class="ml-auto pl-3">
                  <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                      <span class="sr-only">Dismiss</span>
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <!-- Task Summary Cards -->
          <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5">
            <!-- Card 1 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                    <i class="fas fa-tasks text-primary-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Tasks
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo $total_tasks; ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-check text-green-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Completed
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo $completed_tasks; ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-clock text-yellow-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        In Progress
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo $in_progress_tasks; ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-pause text-blue-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Pending
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo $pending_tasks; ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 5 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Overdue
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo $overdue_tasks; ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Task filters and search -->
          <form method="GET" action="" class="mt-6 bg-white shadow rounded-lg p-4">
            <div class="flex flex-col md:flex-row justify-between gap-4">
              <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <div class="relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                  </div>
                  <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="Search task...">
                </div>
                <div>
                  <select name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $status_filter == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                  </select>
                </div>
                <div>
                  <select name="priority" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="">All Priorities</option>
                    <option value="low" <?php echo $priority_filter == 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo $priority_filter == 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo $priority_filter == 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="urgent" <?php echo $priority_filter == 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                  </select>
                </div>
                <div>
                  <select name="assigned_to" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="">All Assignees</option>
                    <?php foreach ($assignees as $assignee): ?>
                      <?php if (!empty($assignee['assigned_to'])): ?>
                        <option value="<?php echo htmlspecialchars($assignee['assigned_to']); ?>" <?php echo $assigned_to_filter == $assignee['assigned_to'] ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($assignee['assigned_to']); ?>
                        </option>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div>
                  <select name="task_area" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="">All Areas</option>
                    <?php foreach ($task_areas as $area): ?>
                      <option value="<?php echo htmlspecialchars($area['task_area']); ?>" <?php echo $task_area_filter == $area['task_area'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($area['task_area']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-filter mr-2 h-5 w-5"></i>
                  Filter
                </button>
                <a href="tasks.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-redo mr-2 h-5 w-5 text-gray-500"></i>
                  Reset
                </a>
                <a href="export-tasks.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-download mr-2 h-5 w-5 text-gray-500"></i>
                  Export
                </a>
              </div>
            </div>
          </form>

          <!-- Task toggle buttons -->
          <div class="mt-6 bg-white shadow rounded-lg p-4">
            <div class="flex space-x-2 border-b border-gray-200">
              <a href="tasks.php" class="py-2 px-4 text-sm font-medium text-primary-600 border-b-2 border-primary-600 focus:outline-none">
                All Tasks
              </a>
              <a href="tasks.php?status=pending" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 focus:outline-none">
                Pending
              </a>
              <a href="tasks.php?status=in_progress" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 focus:outline-none">
                In Progress
              </a>
              <a href="tasks.php?status=completed" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 focus:outline-none">
                Completed
              </a>
              <a href="tasks.php?priority=urgent" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 focus:outline-none">
                Urgent
              </a>
            </div>
          </div>

          <!-- Tasks List -->
          <div class="mt-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
              <ul class="divide-y divide-gray-200">
                <?php if (empty($tasks)): ?>
                  <li class="px-6 py-4 text-center text-gray-500">
                    No tasks found. Create a new task or adjust your filter settings.
                  </li>
                <?php else: ?>
                  <?php foreach ($tasks as $task): ?>
                    <?php
                    $status_class = '';
                    $progress_class = '';

                    switch ($task['status']) {
                      case 'completed':
                        $status_class = 'bg-green-100 text-green-800';
                        $progress_class = 'bg-green-600';
                        $progress = 100;
                        break;
                      case 'in_progress':
                        $status_class = 'bg-yellow-100 text-yellow-800';
                        $progress_class = 'bg-yellow-500';
                        $progress = 60;
                        break;
                      case 'cancelled':
                        $status_class = 'bg-gray-100 text-gray-800';
                        $progress_class = 'bg-gray-600';
                        $progress = 0;
                        break;
                      default: // pending
                        $status_class = 'bg-blue-100 text-blue-800';
                        $progress_class = 'bg-primary-500';
                        $progress = 0;
                    }

                    // Check if task is overdue
                    $is_overdue = false;
                    if ($task['status'] != 'completed' && $task['status'] != 'cancelled') {
                      $to_date = new DateTime($task['to_date']);
                      $today = new DateTime();

                      if ($to_date < $today) {
                        $status_class = 'bg-red-100 text-red-800';
                        $progress_class = 'bg-red-500';
                        $is_overdue = true;
                      }
                    }

                    // Priority color
                    $priority_class = '';
                    switch ($task['priority']) {
                      case 'low':
                        $priority_class = 'text-gray-600';
                        break;
                      case 'medium':
                        $priority_class = 'text-blue-600';
                        break;
                      case 'high':
                        $priority_class = 'text-orange-600';
                        break;
                      case 'urgent':
                        $priority_class = 'text-red-600';
                        break;
                    }
                    ?>
                    <li>
                      <div class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                          <div class="flex items-center justify-between">
                            <div class="flex items-center">
                              <div class="flex-shrink-0 h-4 w-4">
                                <input type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" <?php echo $task['status'] == 'completed' ? 'checked' : ''; ?> onclick="updateTaskStatus(<?php echo $task['id']; ?>, this.checked ? 'completed' : 'pending')">
                              </div>
                              <p class="ml-3 text-sm font-medium text-gray-900 <?php echo $task['status'] == 'completed' ? 'line-through' : ''; ?>">
                                <?php echo htmlspecialchars($task['task_name']); ?>
                              </p>
                            </div>
                            <div class="ml-2 flex-shrink-0 flex">
                              <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                <?php
                                if ($is_overdue) {
                                  echo 'Overdue';
                                } else {
                                  echo ucfirst(str_replace('_', ' ', $task['status']));
                                }
                                ?>
                              </p>
                            </div>
                          </div>
                          <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                              <p class="flex items-center text-sm text-gray-500">
                                <i class="flex-shrink-0 mr-1.5 fas fa-map-marker-alt text-gray-400"></i>
                                <?php echo htmlspecialchars($task['task_area']); ?>
                              </p>
                              <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                <i class="flex-shrink-0 mr-1.5 fas fa-user-tie text-gray-400"></i>
                                <?php echo htmlspecialchars($task['assigned_to']); ?>
                              </p>
                              <p class="mt-2 flex items-center text-sm <?php echo $priority_class; ?> sm:mt-0 sm:ml-6">
                                <i class="flex-shrink-0 mr-1.5 fas fa-flag text-gray-400"></i>
                                <?php echo ucfirst($task['priority']); ?> Priority
                              </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                              <i class="flex-shrink-0 mr-1.5 fas fa-calendar text-gray-400"></i>
                              <p>
                                Due: <time datetime="<?php echo $task['to_date']; ?>"><?php echo date("M j, Y", strtotime($task['to_date'])); ?></time>
                              </p>
                            </div>
                          </div>
                          <!-- <div class="mt-2 flex justify-between items-center">
                            <div class="flex items-center">
                              <span class="text-sm text-gray-500">Progress: <?php echo $task['status'] == 'completed' ? '100' : ($task['status'] == 'in_progress' ? '60' : '0'); ?>%</span>
                              <div class="ml-4 w-48 bg-gray-200 rounded-full h-2.5">
                                <div class="<?php echo $progress_class; ?> h-2.5 rounded-full" style="width: <?php echo $task['status'] == 'completed' ? '100' : ($task['status'] == 'in_progress' ? '60' : '0'); ?>%"></div>
                              </div>
                            </div>
                            <div class="flex space-x-4">
                              <div class="dropdown">
                                <button type="button" class="text-primary-600 hover:text-primary-900 dropdown-toggle">
                                  <i class="fas fa-sync-alt"></i>
                                </button>
                                <div class="dropdown-menu hidden absolute right-0 mt-2 w-48 bg-white shadow-lg py-1 rounded-md">
                                  <a href="?update_status=<?php echo $task['id']; ?>&status=pending" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mark as Pending</a>
                                  <a href="?update_status=<?php echo $task['id']; ?>&status=in_progress" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mark as In Progress</a>
                                  <a href="?update_status=<?php echo $task['id']; ?>&status=completed" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mark as Completed</a>
                                  <a href="?update_status=<?php echo $task['id']; ?>&status=cancelled" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mark as Cancelled</a>
                                </div>
                              </div>
                              <a href="view-task.php?id=<?php echo $task['id']; ?>" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-eye"></i>
                              </a>
                              <a href="edit-task.php?id=<?php echo $task['id']; ?>" class="text-primary-600 hover:text-primary-900">
                                <i class="fas fa-edit"></i>
                              </a>
                              <button type="button" class="text-red-600 hover:text-red-900" onclick="confirmDelete(<?php echo $task['id']; ?>)">
                                <i class="fas fa-trash"></i>
                              </button>
                            </div>
                          </div> -->
                          <?php if (!empty($task['description'])): ?>
                            <div class="mt-2 text-sm text-gray-500">
                              <p class="truncate"><?php echo htmlspecialchars($task['description']); ?></p>
                            </div>
                          <?php endif; ?>
                          <?php if (!empty($task['attachment'])): ?>
                            <div class="mt-2">
                              <a href="../uploads/<?php echo $task['attachment']; ?>" class="inline-flex items-center text-xs text-primary-600 hover:text-primary-900">
                                <i class="fas fa-paperclip mr-1"></i> Attachment
                              </a>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </li>
                  <?php endforeach; ?>
                <?php endif; ?>
              </ul>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
              <div class="mt-4 flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                  <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($priority_filter) ? '&priority=' . urlencode($priority_filter) : ''; ?><?php echo !empty($assigned_to_filter) ? '&assigned_to=' . urlencode($assigned_to_filter) : ''; ?><?php echo !empty($task_area_filter) ? '&task_area=' . urlencode($task_area_filter) : ''; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                      Previous
                    </a>
                  <?php endif; ?>
                  <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($priority_filter) ? '&priority=' . urlencode($priority_filter) : ''; ?><?php echo !empty($assigned_to_filter) ? '&assigned_to=' . urlencode($assigned_to_filter) : ''; ?><?php echo !empty($task_area_filter) ? '&task_area=' . urlencode($task_area_filter) : ''; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                      Next
                    </a>
                  <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                  <div>
                    <p class="text-sm text-gray-700">
                      Showing <span class="font-medium"><?php echo ($page - 1) * $items_per_page + 1; ?></span> to <span class="font-medium"><?php echo min($page * $items_per_page, $total_filtered); ?></span> of <span class="font-medium"><?php echo $total_filtered; ?></span> tasks
                    </p>
                  </div>
                  <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                      <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($priority_filter) ? '&priority=' . urlencode($priority_filter) : ''; ?><?php echo !empty($assigned_to_filter) ? '&assigned_to=' . urlencode($assigned_to_filter) : ''; ?><?php echo !empty($task_area_filter) ? '&task_area=' . urlencode($task_area_filter) : ''; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                          <span class="sr-only">Previous</span>
                          <i class="fas fa-chevron-left h-5 w-5"></i>
                        </a>
                      <?php endif; ?>

                      <?php
                      $start_page = max(1, $page - 2);
                      $end_page = min($total_pages, $start_page + 4);

                      if ($end_page - $start_page < 4) {
                        $start_page = max(1, $end_page - 4);
                      }

                      for ($i = $start_page; $i <= $end_page; $i++):
                      ?>
                        <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($priority_filter) ? '&priority=' . urlencode($priority_filter) : ''; ?><?php echo !empty($assigned_to_filter) ? '&assigned_to=' . urlencode($assigned_to_filter) : ''; ?><?php echo !empty($task_area_filter) ? '&task_area=' . urlencode($task_area_filter) : ''; ?>"
                          class="<?php echo $i == $page ? 'z-10 bg-primary-50 border-primary-500 text-primary-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                          <?php echo $i; ?>
                        </a>
                      <?php endfor; ?>

                      <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; ?><?php echo !empty($priority_filter) ? '&priority=' . urlencode($priority_filter) : ''; ?><?php echo !empty($assigned_to_filter) ? '&assigned_to=' . urlencode($assigned_to_filter) : ''; ?><?php echo !empty($task_area_filter) ? '&task_area=' . urlencode($task_area_filter) : ''; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                          <span class="sr-only">Next</span>
                          <i class="fas fa-chevron-right h-5 w-5"></i>
                        </a>
                      <?php endif; ?>
                    </nav>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Add Task Modal -->
  <div id="addTaskModal" class="hidden fixed inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
      <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-2xl w-full">
        <form id="addTaskForm" action="process-task.php" method="POST" enctype="multipart/form-data">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="w-full">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Add New Task</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <label for="task_name" class="block text-sm font-medium text-gray-700">Task Name</label>
                    <input type="text" name="task_name" id="task_name" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="task_date" class="block text-sm font-medium text-gray-700">Task Date</label>
                    <input type="date" name="task_date" id="task_date" value="<?php echo date('Y-m-d'); ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="task_area" class="block text-sm font-medium text-gray-700">Task Area</label>
                    <input type="text" name="task_area" id="task_area" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                    <input type="number" name="amount" id="amount" step="0.01" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
                    <input type="date" name="from_date" id="from_date" value="<?php echo date('Y-m-d'); ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
                    <input type="date" name="to_date" id="to_date" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="target" class="block text-sm font-medium text-gray-700">Target</label>
                    <input type="text" name="target" id="target" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                    <select name="priority" id="priority" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                      <option value="low">Low</option>
                      <option value="medium" selected>Medium</option>
                      <option value="high">High</option>
                      <option value="urgent">Urgent</option>
                    </select>
                  </div>
                  <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assigned To</label>
                    <input type="text" name="assigned_to" id="assigned_to" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                  </div>
                  <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                      <option value="pending" selected>Pending</option>
                      <option value="in_progress">In Progress</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </div>
                  <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                  </div>
                  <div class="sm:col-span-2">
                    <label for="attachment" class="block text-sm font-medium text-gray-700">Attachment</label>
                    <input type="file" name="attachment" id="attachment" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
              Save Task
            </button>
            <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="hidden fixed inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
      <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
              <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Task</h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Are you sure you want to delete this task? This action cannot be undone.
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <a href="#" id="confirmDeleteBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
            Delete
          </a>
          <button type="button" onclick="closeDeleteModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Display current date
    document.addEventListener('DOMContentLoaded', function() {
      const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      };
      document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', options);

      // Initialize user menu dropdown
      const userMenuButton = document.getElementById('user-menu-button');
      const userDropdown = document.getElementById('user-dropdown');

      if (userMenuButton && userDropdown) {
        userMenuButton.addEventListener('click', function() {
          userDropdown.classList.toggle('hidden');
        });

        // Close the dropdown when clicking outside
        document.addEventListener('click', function(event) {
          if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.add('hidden');
          }
        });
      }

      // Initialize dropdowns for status change
      const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

      dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
          e.stopPropagation();
          const dropdown = this.nextElementSibling;
          dropdown.classList.toggle('hidden');

          // Close other dropdowns
          dropdownToggles.forEach(otherToggle => {
            if (otherToggle !== toggle) {
              otherToggle.nextElementSibling.classList.add('hidden');
            }
          });
        });
      });

      // Close dropdowns when clicking outside
      document.addEventListener('click', function() {
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(dropdown => {
          dropdown.classList.add('hidden');
        });
      });

      // Sidebar toggle for mobile
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebar = document.querySelector('.sidebar');

      if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
          sidebar.classList.toggle('hidden');
        });
      }
    });

    // Modal functions
    function openAddTaskModal() {
      document.getElementById('addTaskModal').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('addTaskModal').classList.add('hidden');
    }

    function confirmDelete(taskId) {
      document.getElementById('confirmDeleteBtn').href = '?delete=' + taskId;
      document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.add('hidden');
    }

    function updateTaskStatus(taskId, status) {
      window.location.href = '?update_status=' + taskId + '&status=' + status;
    }
  </script>
</body>

</html>