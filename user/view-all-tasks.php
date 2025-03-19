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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
  <style>
    .main-style {
      height: 100vh;
      overflow-y: auto;
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen">
  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="hidden md:block bg-indigo-800 text-white w-64 p-4 flex-shrink-0">
      <div class="flex items-center justify-center h-16">
        <h1 class="text-2xl font-bold">BS Traders</h1>
      </div>

      <!-- User profile section -->
      <div class="mt-6 border-t border-indigo-700 pt-4">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-lg font-bold">
            <?php echo substr($_SESSION["name"], 0, 1); ?>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION["name"]); ?></p>
            <p class="text-xs text-indigo-300">Customer Account</p>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="mt-8 space-y-1">
        <a href="index.php" class="flex items-center px-4 py-2 bg-indigo-700 text-white rounded-lg">
          <i class="fas fa-home w-5 h-5 mr-3"></i>
          <span>Dashboard</span>
        </a>
        <a href="task-upload.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
          <i class="fas fa-tasks w-5 h-5 mr-3"></i>
          <span>Upload Task</span>
        </a>
        <a href="user-invoices.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
          <i class="fas fa-file-invoice w-5 h-5 mr-3"></i>
          <span>Invoices</span>
        </a>
        <a href="salary.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
          <i class="fas fa-tasks w-5 h-5 mr-3"></i>
          <span>Salary</span>
        </a>
        <a href="user-support.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
          <i class="fas fa-headset w-5 h-5 mr-3"></i>
          <span>Support</span>
        </a>
        <a href="user-profile.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
          <i class="fas fa-user-circle w-5 h-5 mr-3"></i>
          <span>My Profile</span>
        </a>
        <a href="user-settings.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
          <i class="fas fa-cog w-5 h-5 mr-3"></i>
          <span>Settings</span>
        </a>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-style flex-1 flex flex-col">
      <!-- Top Navigation -->
      <header class="bg-white shadow-sm">
        <div class="flex justify-between items-center px-6 py-3">
          <div class="flex items-center">
            <button id="sidebarToggle" class="md:hidden mr-4 text-gray-500">
              <i class="fas fa-bars w-6 h-6"></i>
            </button>
            <h2 class="text-lg font-medium text-gray-900">Customer Portal</h2>
          </div>

          <div class="flex items-center space-x-4">
            <div class="hidden sm:block text-sm text-gray-700">
              <span id="current-date"></span>
            </div>

            <div class="relative">
              <button id="userMenuBtn" class="flex items-center focus:outline-none">
                <span class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500">
                  <i class="fas fa-user"></i>
                </span>
              </button>

              <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                <a href="user-profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Your Profile</a>
                <a href="user-settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                <a href="../logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
              </div>
            </div>

            <!-- Notification Bell -->
            <div class="relative">
              <button class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-bell"></i>
                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-1 ring-white"></span>
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">My Tasks</h1>
            <p class="mt-1 text-sm text-gray-600">View and manage all your tasks</p>
          </div>
          <a href="task-upload.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-plus mr-2"></i>
            Upload New Task
          </a>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
          <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-900">Filter & Search</h2>
          </div>

          <div class="p-6">
            <form action="view-all-tasks.php" method="GET" class="grid grid-cols-1 gap-6 md:grid-cols-4">
              <!-- Status Filter -->
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-filter text-gray-400"></i>
                  </div>
                  <select id="status" name="status"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Tasks</option>
                    <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $status_filter == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                  </select>
                </div>
              </div>

              <!-- Sort By -->
              <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-sort text-gray-400"></i>
                  </div>
                  <select id="sort" name="sort"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="created_at" <?php echo $sort_by == 'created_at' ? 'selected' : ''; ?>>Date Created</option>
                    <option value="task_date" <?php echo $sort_by == 'task_date' ? 'selected' : ''; ?>>Task Date</option>
                    <option value="from_date" <?php echo $sort_by == 'from_date' ? 'selected' : ''; ?>>Start Date</option>
                    <option value="to_date" <?php echo $sort_by == 'to_date' ? 'selected' : ''; ?>>End Date</option>
                    <option value="amount" <?php echo $sort_by == 'amount' ? 'selected' : ''; ?>>Amount</option>
                    <option value="priority" <?php echo $sort_by == 'priority' ? 'selected' : ''; ?>>Priority</option>
                  </select>
                </div>
              </div>

              <!-- Order -->
              <div>
                <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-arrow-down-a-z text-gray-400"></i>
                  </div>
                  <select id="order" name="order"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="DESC" <?php echo $sort_order == 'DESC' ? 'selected' : ''; ?>>Descending</option>
                    <option value="ASC" <?php echo $sort_order == 'ASC' ? 'selected' : ''; ?>>Ascending</option>
                  </select>
                </div>
              </div>

              <!-- Search -->
              <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                  </div>
                  <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search_term); ?>"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Search tasks...">
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="md:col-span-4 flex justify-end space-x-3">
                <a href="view-all-tasks.php" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Reset
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  <i class="fas fa-filter mr-2"></i>
                  Apply Filters
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Tasks List -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
          <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Task List</h2>
            <p class="text-sm text-gray-600">
              Showing <?php echo min($total_tasks, 1) ?>-<?php echo min($total_tasks, $page * $tasks_per_page) ?> of <?php echo $total_tasks ?> tasks
            </p>
          </div>

          <?php if (count($tasks) > 0): ?>
            <div class="divide-y divide-gray-200">
              <?php foreach ($tasks as $task): ?>
                <?php
                // Determine status color and icon
                $status_class = "";
                $status_bg = "bg-blue-100 text-blue-800";
                $icon_bg = "bg-blue-100";
                $icon_text = "text-blue-600";

                switch ($task['status']) {
                  case 'completed':
                    $status_bg = "bg-green-100 text-green-800";
                    $icon_bg = "bg-green-100";
                    $icon_text = "text-green-600";
                    break;
                  case 'in_progress':
                    $status_bg = "bg-yellow-100 text-yellow-800";
                    $icon_bg = "bg-yellow-100";
                    $icon_text = "text-yellow-600";
                    break;
                  case 'cancelled':
                    $status_bg = "bg-red-100 text-red-800";
                    $icon_bg = "bg-red-100";
                    $icon_text = "text-red-600";
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
                $priority_bg = "bg-gray-100 text-gray-800";
                switch ($task['priority']) {
                  case 'low':
                    $priority_bg = "bg-blue-100 text-blue-800";
                    break;
                  case 'medium':
                    $priority_bg = "bg-green-100 text-green-800";
                    break;
                  case 'high':
                    $priority_bg = "bg-yellow-100 text-yellow-800";
                    break;
                  case 'urgent':
                    $priority_bg = "bg-red-100 text-red-800";
                    break;
                }
                ?>

                <div class="hover:bg-gray-50 transition-colors">
                  <a href="task-details.php?id=<?php echo $task['id']; ?>" class="block p-6">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <div class="h-12 w-12 flex-shrink-0 rounded-full <?php echo $icon_bg ?> flex items-center justify-center">
                          <i class="fas fa-tasks <?php echo $icon_text ?>"></i>
                        </div>
                        <div class="ml-4">
                          <div class="flex items-center">
                            <h3 class="text-lg font-medium text-indigo-600"><?php echo htmlspecialchars($task['task_name']); ?></h3>
                            <?php if (!empty($task['attachment'])): ?>
                              <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                <i class="fas fa-paperclip mr-1"></i>
                                Attachment
                              </span>
                            <?php endif; ?>
                          </div>
                          <div class="mt-1 flex items-center text-sm text-gray-500">
                            <span><?php echo $task_date; ?></span>
                            <span class="mx-2">•</span>
                            <span>Created on <?php echo $created_date; ?></span>
                          </div>
                        </div>
                      </div>
                      <div class="flex space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_bg; ?>">
                          <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $priority_bg; ?>">
                          <?php echo ucfirst($task['priority']); ?> Priority
                        </span>
                      </div>
                    </div>

                    <div class="mt-4 flex flex-wrap justify-between text-sm">
                      <div class="flex flex-wrap mt-2 md:mt-0">
                        <div class="flex items-center mr-4">
                          <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                          <span class="text-gray-600"><?php echo htmlspecialchars($task['task_area']); ?></span>
                        </div>
                        <div class="flex items-center">
                          <i class="fas fa-calendar text-gray-400 mr-1"></i>
                          <span class="text-gray-600"><?php echo $from_date; ?> - <?php echo $to_date; ?></span>
                        </div>
                      </div>
                      <div class="flex items-center mt-2 md:mt-0">
                        <div class="font-medium text-gray-900">$<?php echo $formatted_amount; ?></div>
                        <i class="fas fa-chevron-right ml-3 text-indigo-500"></i>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
              <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                  <div class="hidden sm:block">
                    <p class="text-sm text-gray-700">
                      Showing <span class="font-medium"><?php echo min($total_tasks, 1) ?></span> to <span class="font-medium"><?php echo min($total_tasks, $page * $tasks_per_page) ?></span> of <span class="font-medium"><?php echo $total_tasks ?></span> results
                    </p>
                  </div>

                  <div class="flex-1 flex justify-between sm:justify-end">
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
                </div>
              </div>
            <?php endif; ?>

          <?php else: ?>
            <div class="p-10 text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 mb-4">
                <i class="fas fa-tasks text-indigo-600 text-2xl"></i>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks found</h3>
              <p class="text-gray-500 mb-6">No tasks match your search criteria.</p>
              <div class="flex justify-center">
                <a href="task-upload.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  <i class="fas fa-plus mr-2"></i>
                  Upload a Task
                </a>
                <a href="view-all-tasks.php" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Reset Filters
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <!-- Mobile sidebar (hidden by default) -->
  <div id="mobileSidebar" class="fixed inset-0 z-40 hidden">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-indigo-800">
      <div class="absolute top-0 right-0 -mr-12 pt-2">
        <button id="closeSidebar" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-white">
          <i class="fas fa-times text-white"></i>
        </button>
      </div>
      <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
        <div class="flex items-center justify-center h-16">
          <h1 class="text-2xl font-bold text-white">BS Traders</h1>
        </div>
        <nav class="mt-6 px-4 space-y-1">
          <a href="index.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-home w-5 h-5 mr-3"></i>
            <span>Dashboard</span>
          </a>
          <a href="user-orders.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
            <span>My Orders</span>
          </a>
          <a href="task-upload.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-upload w-5 h-5 mr-3"></i>
            <span>Upload Task</span>
          </a>
          <a href="view-all-tasks.php" class="flex items-center px-4 py-2 bg-indigo-700 text-white rounded-lg">
            <i class="fas fa-tasks w-5 h-5 mr-3"></i>
            <span>My Tasks</span>
          </a>
          <!-- Add other mobile menu items here -->
        </nav>
      </div>
      <div class="flex-shrink-0 flex border-t border-indigo-700 p-4">
        <div class="flex items-center">
          <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-lg font-bold">
            <?php echo substr($_SESSION["name"], 0, 1); ?>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($_SESSION["name"]); ?></p>
            <p class="text-xs text-indigo-300">Customer Account</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Current date display
    const currentDate = new Date();
    const options = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    };
    document.getElementById('current-date').textContent = currentDate.toLocaleDateString('en-US', options);

    // User dropdown toggle
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    userMenuBtn.addEventListener('click', () => {
      userDropdown.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (event) => {
      if (!userMenuBtn.contains(event.target) && !userDropdown.contains(event.target)) {
        userDropdown.classList.add('hidden');
      }
    });

    // Mobile sidebar
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebar = document.getElementById('mobileSidebar');
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
  </script>
</body>

</html>