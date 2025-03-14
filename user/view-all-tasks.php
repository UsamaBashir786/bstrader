<?php
// Include user authentication
require_once "../config/user-auth.php";

// Initialize variables
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

// Connect to database
require_once "../config/config.php";

// Build the query based on filters
$where_clause = "WHERE user_id = ?";

if ($status_filter != 'all') {
  $where_clause .= " AND status = ?";
}

if (!empty($search_term)) {
  $where_clause .= " AND (task_name LIKE ? OR task_area LIKE ? OR description LIKE ?)";
}

// Prepare the SQL query
$sql = "SELECT * FROM tasks $where_clause ORDER BY $sort_by $sort_order";

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM tasks $where_clause";

// Set up pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$tasks_per_page = 10;
$offset = ($page - 1) * $tasks_per_page;

// Add pagination to the main query
$sql .= " LIMIT $tasks_per_page OFFSET $offset";

// Initialize tasks array
$tasks = [];
$total_tasks = 0;

if ($stmt = $conn->prepare($count_sql)) {
  if ($status_filter != 'all') {
    if (!empty($search_term)) {
      $search_param = "%$search_term%";
      $stmt->bind_param("iss", $_SESSION["user_id"], $status_filter, $search_param);
    } else {
      $stmt->bind_param("is", $_SESSION["user_id"], $status_filter);
    }
  } else {
    if (!empty($search_term)) {
      $search_param = "%$search_term%";
      $stmt->bind_param("isss", $_SESSION["user_id"], $search_param, $search_param, $search_param);
    } else {
      $stmt->bind_param("i", $_SESSION["user_id"]);
    }
  }

  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    $total_tasks = $row['total'];
  }

  $stmt->close();
}

// Get the tasks
if ($stmt = $conn->prepare($sql)) {
  if ($status_filter != 'all') {
    if (!empty($search_term)) {
      $search_param = "%$search_term%";
      $stmt->bind_param("isss", $_SESSION["user_id"], $status_filter, $search_param, $search_param, $search_param);
    } else {
      $stmt->bind_param("is", $_SESSION["user_id"], $status_filter);
    }
  } else {
    if (!empty($search_term)) {
      $search_param = "%$search_term%";
      $stmt->bind_param("isss", $_SESSION["user_id"], $search_param, $search_param, $search_param);
    } else {
      $stmt->bind_param("i", $_SESSION["user_id"]);
    }
  }

  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
  }

  $stmt->close();
}

// Calculate pagination information
$total_pages = ceil($total_tasks / $tasks_per_page);
$has_previous = $page > 1;
$has_next = $page < $total_pages;

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - My Tasks</title>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900">My Tasks</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <a href="task-upload.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-plus mr-2 -ml-1 h-5 w-5"></i>
                Upload New Task
              </a>
            </div>
          </div>

          <!-- Filters and search -->
          <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:p-6">
              <form action="view-all-tasks.php" method="GET" class="space-y-4 sm:space-y-0 sm:flex sm:items-center sm:space-x-4">
                <div class="w-full sm:w-1/4">
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                  <select id="status" name="status" class="block w-full sm:text-sm rounded-md py-2">
                    <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Tasks</option>
                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $status_filter == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                  </select>
                </div>
                <div class="w-full sm:w-1/4">
                  <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                  <select id="sort" name="sort" class="block w-full sm:text-sm rounded-md py-2">
                    <option value="created_at" <?php echo $sort_by == 'created_at' ? 'selected' : ''; ?>>Date Created</option>
                    <option value="task_date" <?php echo $sort_by == 'task_date' ? 'selected' : ''; ?>>Task Date</option>
                    <option value="from_date" <?php echo $sort_by == 'from_date' ? 'selected' : ''; ?>>Start Date</option>
                    <option value="to_date" <?php echo $sort_by == 'to_date' ? 'selected' : ''; ?>>End Date</option>
                    <option value="amount" <?php echo $sort_by == 'amount' ? 'selected' : ''; ?>>Amount</option>
                    <option value="priority" <?php echo $sort_by == 'priority' ? 'selected' : ''; ?>>Priority</option>
                  </select>
                </div>
                <div class="w-full sm:w-1/4">
                  <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                  <select id="order" name="order" class="block w-full sm:text-sm rounded-md py-2">
                    <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>Descending</option>
                    <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                  </select>
                </div>
                <div class="w-full sm:w-1/4">
                  <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                  <div class="relative rounded-md shadow-sm">
                    <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search_term); ?>" class="block w-full sm:text-sm rounded-md py-2 pl-10" placeholder="Search tasks...">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-search text-gray-400"></i>
                    </div>
                  </div>
                </div>
                <div class="sm:mt-6 flex items-center">
                  <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Apply Filters
                  </button>
                  <a href="view-all-tasks.php" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Reset
                  </a>
                </div>
              </form>
            </div>
          </div>

          <!-- Tasks list -->
          <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-md">
            <div class="bg-primary-50 px-4 py-3 border-b border-gray-200">
              <h3 class="text-sm leading-6 font-medium text-primary-800">
                Showing <?php echo min(count($tasks), 1) . '-' . min(count($tasks), $tasks_per_page); ?> of <?php echo $total_tasks; ?> tasks
              </h3>
            </div>
            <ul class="divide-y divide-gray-200">
              <?php if (count($tasks) > 0): ?>
                <?php foreach ($tasks as $task): ?>
                  <?php
                  // Determine status color and icon
                  $status_class = "";
                  $icon_class = "bg-primary-100";
                  $icon_text = "text-primary-600";

                  switch ($task['status']) {
                    case 'completed':
                      $status_class = "bg-green-100 text-green-800";
                      $icon_class = "bg-green-100";
                      $icon_text = "text-green-600";
                      break;
                    case 'in_progress':
                      $status_class = "bg-yellow-100 text-yellow-800";
                      $icon_class = "bg-yellow-100";
                      $icon_text = "text-yellow-600";
                      break;
                    case 'cancelled':
                      $status_class = "bg-red-100 text-red-800";
                      $icon_class = "bg-red-100";
                      $icon_text = "text-red-600";
                      break;
                    default: // pending
                      $status_class = "bg-blue-100 text-blue-800";
                      break;
                  }

                  // Format dates
                  $created_date = date("F j, Y", strtotime($task['created_at']));
                  $task_date = date("F j, Y", strtotime($task['task_date']));
                  $from_date = date("M j, Y", strtotime($task['from_date']));
                  $to_date = date("M j, Y", strtotime($task['to_date']));

                  // Format amount
                  $formatted_amount = number_format($task['amount'], 2);

                  // Get priority badge class
                  $priority_class = "bg-gray-100 text-gray-800";
                  switch ($task['priority']) {
                    case 'low':
                      $priority_class = "bg-blue-100 text-blue-800";
                      break;
                    case 'medium':
                      $priority_class = "bg-green-100 text-green-800";
                      break;
                    case 'high':
                      $priority_class = "bg-yellow-100 text-yellow-800";
                      break;
                    case 'urgent':
                      $priority_class = "bg-red-100 text-red-800";
                      break;
                  }
                  ?>
                  <li>
                    <a href="task-details.php?id=<?php echo $task['id']; ?>" class="block hover:bg-gray-50">
                      <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                          <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 <?php echo $icon_class; ?> rounded-full flex items-center justify-center">
                              <i class="fas fa-tasks <?php echo $icon_text; ?>"></i>
                            </div>
                            <div class="ml-4">
                              <div class="flex items-center">
                                <div class="text-sm font-medium text-primary-600"><?php echo htmlspecialchars($task['task_name']); ?></div>
                                <?php if (!empty($task['attachment'])): ?>
                                  <span class="ml-2 flex-shrink-0 inline-block px-2 py-0.5 text-xs font-medium rounded-full bg-primary-100 text-primary-800">
                                    <i class="fas fa-paperclip mr-1"></i> Attachment
                                  </span>
                                <?php endif; ?>
                              </div>
                              <div class="text-sm text-gray-500">
                                <span class="mr-2">Task Date: <?php echo $task_date; ?></span>
                                <span class="mx-2">•</span>
                                <span>Created: <?php echo $created_date; ?></span>
                              </div>
                            </div>
                          </div>
                          <div class="flex items-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                              <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                            </span>
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $priority_class; ?>">
                              <?php echo ucfirst($task['priority']); ?>
                            </span>
                          </div>
                        </div>
                        <div class="mt-2 sm:flex sm:justify-between">
                          <div class="sm:flex">
                            <div class="flex items-center text-sm text-gray-500">
                              <i class="flex-shrink-0 mr-1.5 fas fa-map-marker-alt text-gray-400"></i>
                              <?php echo htmlspecialchars($task['task_area']); ?>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                              <i class="flex-shrink-0 mr-1.5 fas fa-calendar text-gray-400"></i>
                              <?php echo $from_date; ?> - <?php echo $to_date; ?>
                            </div>
                          </div>
                          <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                            <div class="mr-4">
                              <?php echo $formatted_amount; ?>
                            </div>
                            <button class="text-primary-600 hover:text-primary-900 focus:outline-none" onclick="event.preventDefault(); window.location.href='task-details.php?id=<?php echo $task['id']; ?>'">
                              <i class="fas fa-arrow-right"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </a>
                  </li>
                <?php endforeach; ?>
              <?php else: ?>
                <li class="px-4 py-6 text-center text-gray-500">
                  <p>No tasks found matching your criteria.</p>
                  <p class="mt-2">
                    <a href="task-upload.php" class="text-primary-600 hover:text-primary-900 font-medium">
                      <i class="fas fa-plus mr-1"></i> Upload your first task
                    </a>
                  </p>
                </li>
              <?php endif; ?>
            </ul>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
              <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                  <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($has_previous): ?>
                      <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?>&search=<?php echo urlencode($search_term); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                      </a>
                    <?php else: ?>
                      <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-50 cursor-not-allowed">
                        Previous
                      </span>
                    <?php endif; ?>

                    <?php if ($has_next): ?>
                      <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?>&search=<?php echo urlencode($search_term); ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                      </a>
                    <?php else: ?>
                      <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-50 cursor-not-allowed">
                        Next
                      </span>
                    <?php endif; ?>
                  </div>
                  <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                      <p class="text-sm text-gray-700">
                        Showing <span class="font-medium"><?php echo min(count($tasks), 1); ?></span> to <span class="font-medium"><?php echo min(count($tasks), $tasks_per_page); ?></span> of <span class="font-medium"><?php echo $total_tasks; ?></span> results
                      </p>
                    </div>
                    <div>
                      <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <?php if ($has_previous): ?>
                          <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?>&search=<?php echo urlencode($search_term); ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <i class="fas fa-chevron-left h-5 w-5"></i>
                          </a>
                        <?php else: ?>
                          <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-300 cursor-not-allowed">
                            <span class="sr-only">Previous</span>
                            <i class="fas fa-chevron-left h-5 w-5"></i>
                          </span>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);

                        if ($start_page > 1) {
                          echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                        }

                        for ($i = $start_page; $i <= $end_page; $i++) {
                          if ($i == $page) {
                            echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-primary-50 text-sm font-medium text-primary-600">' . $i . '</span>';
                          } else {
                            echo '<a href="?page=' . $i . '&status=' . $status_filter . '&sort=' . $sort_by . '&order=' . $sort_order . '&search=' . urlencode($search_term) . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $i . '</a>';
                          }
                        }

                        if ($end_page < $total_pages) {
                          echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                        }
                        ?>

                        <?php if ($has_next): ?>
                          <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?>&search=<?php echo urlencode($search_term); ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <i class="fas fa-chevron-right h-5 w-5"></i>
                          </a>
                        <?php else: ?>
                          <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-300 cursor-not-allowed">
                            <span class="sr-only">Next</span>
                            <i class="fas fa-chevron-right h-5 w-5"></i>
                          </span>
                        <?php endif; ?>
                      </nav>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
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
  </script>
</body>

</html>