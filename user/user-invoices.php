<?php
// Include user authentication
require_once "../config/user-auth.php";

// Connect to database
require_once "../config/config.php";

// Initialize variables
$error_message = "";
$success_message = "";

// Set default filter values
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC';

// Set up pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$invoices_per_page = 10;
$offset = ($page - 1) * $invoices_per_page;

// Check if invoices table exists
$invoices_table_exists = false;
$check_table = "SHOW TABLES LIKE 'invoices'";
$table_result = $conn->query($check_table);
if ($table_result && $table_result->num_rows > 0) {
  $invoices_table_exists = true;
}

// Initialize invoices array and counts
$invoices = [];
$total_invoices = 0;
$pending_count = 0;
$paid_count = 0;
$overdue_count = 0;

// Only query the database if the table exists
if ($invoices_table_exists) {
  // Build the query based on filters
  $where_clause = "WHERE user_id = ?";

  if ($filter_status != 'all') {
    $where_clause .= " AND status = ?";
  }

  if (!empty($search_term)) {
    $where_clause .= " AND (invoice_number LIKE ? OR description LIKE ?)";
  }

  // Count total matching invoices
  $count_sql = "SELECT 
                  COUNT(*) as total,
                  SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                  SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                  SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) as overdue_count
                FROM invoices $where_clause";

  if ($stmt = $conn->prepare($count_sql)) {
    if ($filter_status != 'all') {
      if (!empty($search_term)) {
        $search_param = "%$search_term%";
        $stmt->bind_param("isss", $_SESSION["user_id"], $filter_status, $search_param, $search_param);
      } else {
        $stmt->bind_param("is", $_SESSION["user_id"], $filter_status);
      }
    } else {
      if (!empty($search_term)) {
        $search_param = "%$search_term%";
        $stmt->bind_param("iss", $_SESSION["user_id"], $search_param, $search_param);
      } else {
        $stmt->bind_param("i", $_SESSION["user_id"]);
      }
    }

    $stmt->execute();
    $count_result = $stmt->get_result();

    if ($count_row = $count_result->fetch_assoc()) {
      $total_invoices = $count_row['total'];
      $pending_count = $count_row['pending_count'];
      $paid_count = $count_row['paid_count'];
      $overdue_count = $count_row['overdue_count'];
    }

    $stmt->close();
  }

  // Get the invoices for current page
  if ($total_invoices > 0) {
    $sql = "SELECT * FROM invoices $where_clause ORDER BY $sort_by $sort_order LIMIT $invoices_per_page OFFSET $offset";

    if ($stmt = $conn->prepare($sql)) {
      if ($filter_status != 'all') {
        if (!empty($search_term)) {
          $search_param = "%$search_term%";
          $stmt->bind_param("isss", $_SESSION["user_id"], $filter_status, $search_param, $search_param);
        } else {
          $stmt->bind_param("is", $_SESSION["user_id"], $filter_status);
        }
      } else {
        if (!empty($search_term)) {
          $search_param = "%$search_term%";
          $stmt->bind_param("iss", $_SESSION["user_id"], $search_param, $search_param);
        } else {
          $stmt->bind_param("i", $_SESSION["user_id"]);
        }
      }

      $stmt->execute();
      $result = $stmt->get_result();

      while ($invoice = $result->fetch_assoc()) {
        $invoices[] = $invoice;
      }

      $stmt->close();
    }
  }
} else {
  // If the table doesn't exist, create sample invoices for display purposes
  // Sample data representing the expected format if the table existed
  $invoices = [
    [
      'id' => 1,
      'invoice_number' => 'INV-2025-001',
      'amount' => 1250.00,
      'status' => 'paid',
      'issue_date' => '2025-02-15',
      'due_date' => '2025-03-15',
      'payment_date' => '2025-03-10',
      'description' => 'Website Development Services'
    ],
    [
      'id' => 2,
      'invoice_number' => 'INV-2025-002',
      'amount' => 850.00,
      'status' => 'pending',
      'issue_date' => '2025-03-01',
      'due_date' => '2025-03-31',
      'payment_date' => null,
      'description' => 'Monthly Maintenance'
    ],
    [
      'id' => 3,
      'invoice_number' => 'INV-2025-003',
      'amount' => 1780.00,
      'status' => 'overdue',
      'issue_date' => '2025-01-15',
      'due_date' => '2025-02-15',
      'payment_date' => null,
      'description' => 'E-commerce Development'
    ]
  ];

  $total_invoices = count($invoices);
  $pending_count = 1;
  $paid_count = 1;
  $overdue_count = 1;
}

// Calculate pagination information
$total_pages = ceil($total_invoices / $invoices_per_page);
$has_previous = $page > 1;
$has_next = $page < $total_pages;

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - My Invoices</title>
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
            <h1 class="text-2xl font-bold text-gray-900">My Invoices</h1>
            <p class="mt-1 text-sm text-gray-600">View and manage your invoices</p>
          </div>

          <a href="payment-history.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-history mr-2"></i>
            Payment History
          </a>
        </div>

        <!-- Alerts -->
        <?php if (!empty($success_message)): ?>
          <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm text-green-800"><?php echo $success_message; ?></p>
              </div>
              <button class="ml-auto alert-close">
                <i class="fas fa-times text-green-500"></i>
              </button>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
          <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-800"><?php echo $error_message; ?></p>
              </div>
              <button class="ml-auto alert-close">
                <i class="fas fa-times text-red-500"></i>
              </button>
            </div>
          </div>
        <?php endif; ?>

        <!-- Invoice Statistics -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-6">
          <!-- Pending Invoices -->
          <div class="bg-white overflow-hidden rounded-xl shadow-md">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-md bg-yellow-100 flex items-center justify-center">
                  <i class="fas fa-hourglass-half text-yellow-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Invoices</dt>
                    <dd class="text-2xl font-semibold text-gray-900"><?php echo $pending_count; ?></dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
              <a href="?status=pending" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                View all <i class="fas fa-arrow-right ml-1"></i>
              </a>
            </div>
          </div>

          <!-- Paid Invoices -->
          <div class="bg-white overflow-hidden rounded-xl shadow-md">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-md bg-green-100 flex items-center justify-center">
                  <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Paid Invoices</dt>
                    <dd class="text-2xl font-semibold text-gray-900"><?php echo $paid_count; ?></dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
              <a href="?status=paid" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                View all <i class="fas fa-arrow-right ml-1"></i>
              </a>
            </div>
          </div>

          <!-- Overdue Invoices -->
          <div class="bg-white overflow-hidden rounded-xl shadow-md">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-md bg-red-100 flex items-center justify-center">
                  <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Overdue Invoices</dt>
                    <dd class="text-2xl font-semibold text-gray-900"><?php echo $overdue_count; ?></dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
              <a href="?status=overdue" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                View all <i class="fas fa-arrow-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Filter and Search -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
          <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-900">Filter & Search</h2>
          </div>

          <div class="p-6">
            <form action="user-invoices.php" method="GET" class="grid grid-cols-1 gap-6 md:grid-cols-4">
              <!-- Status Filter -->
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-filter text-gray-400"></i>
                  </div>
                  <select id="status" name="status"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all" <?php echo $filter_status == 'all' ? 'selected' : ''; ?>>All Invoices</option>
                    <option value="pending" <?php echo $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="paid" <?php echo $filter_status == 'paid' ? 'selected' : ''; ?>>Paid</option>
                    <option value="overdue" <?php echo $filter_status == 'overdue' ? 'selected' : ''; ?>>Overdue</option>
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
                    <option value="issue_date" <?php echo $sort_by == 'issue_date' ? 'selected' : ''; ?>>Issue Date</option>
                    <option value="due_date" <?php echo $sort_by == 'due_date' ? 'selected' : ''; ?>>Due Date</option>
                    <option value="amount" <?php echo $sort_by == 'amount' ? 'selected' : ''; ?>>Amount</option>
                    <option value="invoice_number" <?php echo $sort_by == 'invoice_number' ? 'selected' : ''; ?>>Invoice Number</option>
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
                    placeholder="Search invoices...">
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="md:col-span-4 flex justify-end space-x-3">
                <a href="user-invoices.php" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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

        <!-- Invoices List -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
          <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Invoice List</h2>
            <p class="text-sm text-gray-600">
              Showing <?php echo min($total_invoices, 1) ?>-<?php echo min($total_invoices, $page * $invoices_per_page) ?> of <?php echo $total_invoices ?> invoices
            </p>
          </div>

          <?php if (count($invoices) > 0): ?>
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Invoice #
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Issue Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Due Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Amount
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Action
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <?php foreach ($invoices as $invoice): ?>
                    <?php
                    // Set status badge
                    $status_badge = '';
                    switch ($invoice['status']) {
                      case 'paid':
                        $status_badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Paid</span>';
                        break;
                      case 'pending':
                        $status_badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>';
                        break;
                      case 'overdue':
                        $status_badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Overdue</span>';
                        break;
                      default:
                        $status_badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Unknown</span>';
                    }

                    // Format dates
                    $issue_date = date('M d, Y', strtotime($invoice['issue_date']));
                    $due_date = date('M d, Y', strtotime($invoice['due_date']));
                    ?>
                    <tr class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500">
                            <i class="fas fa-file-invoice"></i>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-indigo-600"><?php echo htmlspecialchars($invoice['invoice_number']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo htmlspecialchars($invoice['description']); ?></div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?php echo $issue_date; ?></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?php echo $due_date; ?></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">$<?php echo number_format($invoice['amount'], 2); ?></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <?php echo $status_badge; ?>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex space-x-2">
                          <a href="view-invoice.php?id=<?php echo $invoice['id']; ?>" class="text-indigo-600 hover:text-indigo-900" title="View">
                            <i class="fas fa-eye"></i>
                          </a>
                          <a href="download-invoice.php?id=<?php echo $invoice['id']; ?>" class="text-indigo-600 hover:text-indigo-900" title="Download">
                            <i class="fas fa-download"></i>
                          </a>
                          <?php if ($invoice['status'] != 'paid'): ?>
                            <a href="process-payment.php?id=<?php echo $invoice['id']; ?>" class="text-green-600 hover:text-green-900" title="Pay Now">
                              <i class="fas fa-credit-card"></i>
                            </a>
                          <?php endif; ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
              <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                  <div>
                    <p class="text-sm text-gray-700">
                      Showing
                      <span class="font-medium"><?php echo ($page - 1) * $invoices_per_page + 1; ?></span>
                      to
                      <span class="font-medium"><?php echo min($page * $invoices_per_page, $total_invoices); ?></span>
                      of
                      <span class="font-medium"><?php echo $total_invoices; ?></span>
                      results
                    </p>
                  </div>
                  <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                      <?php if ($has_previous): ?>
                        <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $filter_status; ?>&search=<?php echo urlencode($search_term); ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                          <span class="sr-only">Previous</span>
                          <i class="fas fa-chevron-left h-5 w-5"></i>
                        </a>
                      <?php else: ?>
                        <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                          <span class="sr-only">Previous</span>
                          <i class="fas fa-chevron-left h-5 w-5"></i>
                        </span>
                      <?php endif; ?>

                      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                          <span class="relative inline-flex items-center px-4 py-2 border border-indigo-500 bg-indigo-50 text-sm font-medium text-indigo-600">
                            <?php echo $i; ?>
                          </span>
                        <?php else: ?>
                          <a href="?page=<?php echo $i; ?>&status=<?php echo $filter_status; ?>&search=<?php echo urlencode($search_term); ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <?php echo $i; ?>
                          </a>
                        <?php endif; ?>
                      <?php endfor; ?>

                      <?php if ($has_next): ?>
                        <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $filter_status; ?>&search=<?php echo urlencode($search_term); ?>&sort=<?php echo $sort_by; ?>&order=<?php echo $sort_order; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                          <span class="sr-only">Next</span>
                          <i class="fas fa-chevron-right h-5 w-5"></i>
                        </a>
                      <?php else: ?>
                        <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                          <span class="sr-only">Next</span>
                          <i class="fas fa-chevron-right h-5 w-5"></i>
                        </span>
                      <?php endif; ?>
                    </nav>
                  </div>
                </div>
              </div>
            <?php endif; ?>

          <?php else: ?>
            <div class="px-6 py-12 text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-500 mb-4">
                <i class="fas fa-file-invoice fa-2x"></i>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No invoices found</h3>
              <p class="text-gray-500 mb-6">There are no invoices matching your current filters.</p>
              <a href="user-invoices.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-redo mr-2"></i>
                Reset Filters
              </a>
            </div>
          <?php endif; ?>
        </div>
      </main>

      <!-- Footer -->
      <footer class="bg-white border-t border-gray-200 px-6 py-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="text-sm text-gray-600">
            &copy; <?php echo date('Y'); ?> BS Traders. All rights reserved.
          </div>
          <div class="mt-4 md:mt-0">
            <ul class="flex space-x-4">
              <li><a href="../privacy-policy.php" class="text-sm text-gray-600 hover:text-indigo-600">Privacy Policy</a></li>
              <li><a href="../terms-of-service.php" class="text-sm text-gray-600 hover:text-indigo-600">Terms of Service</a></li>
              <li><a href="../contact-us.php" class="text-sm text-gray-600 hover:text-indigo-600">Contact Us</a></li>
            </ul>
          </div>
        </div>
      </footer>
    </div>
  </div>

  <!-- Mobile Sidebar Navigation (Off-canvas) -->
  <div id="mobileSidebar" class="fixed inset-0 flex z-40 md:hidden transform -translate-x-full transition-transform duration-300 ease-in-out">
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75" id="sidebarOverlay"></div>

    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-indigo-800 text-white">
      <div class="absolute top-0 right-0 -mr-12 pt-2">
        <button id="closeSidebar" class="flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
          <span class="sr-only">Close sidebar</span>
          <i class="fas fa-times text-white"></i>
        </button>
      </div>

      <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
        <div class="flex items-center justify-center h-16">
          <h1 class="text-2xl font-bold">BS Traders</h1>
        </div>

        <!-- User profile section -->
        <div class="mt-6 border-t border-indigo-700 pt-4 px-4">
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

        <!-- Mobile Navigation -->
        <nav class="mt-8 px-4 space-y-1">
          <a href="index.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-home w-5 h-5 mr-3"></i>
            <span>Dashboard</span>
          </a>
          <a href="user-orders.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
            <span>My Orders</span>
          </a>
          <a href="user-quotes.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-file-invoice-dollar w-5 h-5 mr-3"></i>
            <span>Quotations</span>
          </a>
          <a href="task-upload.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-upload w-5 h-5 mr-3"></i>
            <span>Upload Task</span>
          </a>
          <a href="view-all-tasks.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-tasks w-5 h-5 mr-3"></i>
            <span>My Tasks</span>
          </a>
          <a href="user-products.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-boxes w-5 h-5 mr-3"></i>
            <span>Products</span>
          </a>
          <a href="user-invoices.php" class="flex items-center px-4 py-2 bg-indigo-700 text-white rounded-lg">
            <i class="fas fa-file-invoice w-5 h-5 mr-3"></i>
            <span>Invoices</span>
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
      </div>

      <div class="flex-shrink-0 flex border-t border-indigo-700 p-4">
        <a href="../logout.php" class="flex-shrink-0 group block">
          <div class="flex items-center">
            <div>
              <i class="fas fa-sign-out-alt text-indigo-300 group-hover:text-white"></i>
            </div>
            <div class="ml-3">
              <p class="text-base font-medium text-indigo-200 group-hover:text-white">Logout</p>
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="flex-shrink-0 w-14" aria-hidden="true">
      <!-- Force sidebar to shrink to fit close icon -->
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    // Show current date
    const dateElement = document.getElementById('current-date');
    if (dateElement) {
      const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      };
      const today = new Date();
      dateElement.textContent = today.toLocaleDateString('en-US', options);
    }

    // User dropdown toggle
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');

    if (userMenuBtn && userDropdown) {
      userMenuBtn.addEventListener('click', function() {
        userDropdown.classList.toggle('hidden');
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(event) {
        if (!userMenuBtn.contains(event.target) && !userDropdown.contains(event.target)) {
          userDropdown.classList.add('hidden');
        }
      });
    }

    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const closeSidebar = document.getElementById('closeSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle && mobileSidebar) {
      sidebarToggle.addEventListener('click', function() {
        mobileSidebar.classList.remove('-translate-x-full');
      });

      closeSidebar.addEventListener('click', function() {
        mobileSidebar.classList.add('-translate-x-full');
      });

      sidebarOverlay.addEventListener('click', function() {
        mobileSidebar.classList.add('-translate-x-full');
      });
    }

    // Close alert messages
    const alertCloseButtons = document.querySelectorAll('.alert-close');
    alertCloseButtons.forEach(button => {
      button.addEventListener('click', function() {
        const alert = this.closest('div[class^="mb-6 bg-"]');
        if (alert) {
          alert.style.display = 'none';
        }
      });
    });
  </script>
</body>

</html>