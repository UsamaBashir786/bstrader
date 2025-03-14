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
$tickets_per_page = 5;
$offset = ($page - 1) * $tickets_per_page;

// Check if support_tickets table exists
$support_table_exists = false;
$check_table = "SHOW TABLES LIKE 'support_tickets'";
$table_result = $conn->query($check_table);
if ($table_result && $table_result->num_rows > 0) {
  $support_table_exists = true;
}

// Initialize tickets array and counts
$tickets = [];
$total_tickets = 0;
$open_count = 0;
$in_progress_count = 0;
$resolved_count = 0;
$closed_count = 0;

// Process form submission for new ticket
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_ticket'])) {
  $subject = trim($_POST['subject']);
  $category = trim($_POST['category']);
  $priority = trim($_POST['priority']);
  $message = trim($_POST['message']);
  $user_id = $_SESSION['user_id'];

  // Simple validation
  if (empty($subject) || empty($message)) {
    $error_message = "Subject and message are required fields.";
  } else {
    if ($support_table_exists) {
      // Generate ticket number
      $ticket_number = 'TKT-' . date('Ymd') . '-' . rand(1000, 9999);

      // Insert new ticket
      $sql = "INSERT INTO support_tickets (ticket_number, user_id, subject, category, priority, message, status, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, 'open', NOW())";

      if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sissss", $ticket_number, $user_id, $subject, $category, $priority, $message);

        if ($stmt->execute()) {
          $success_message = "Your support ticket has been submitted successfully. Ticket number: " . $ticket_number;
        } else {
          $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
      } else {
        $error_message = "Error preparing statement: " . $conn->error;
      }
    } else {
      // Simulate success for demo purposes
      $ticket_number = 'TKT-' . date('Ymd') . '-' . rand(1000, 9999);
      $success_message = "Your support ticket has been submitted successfully. Ticket number: " . $ticket_number;
    }
  }
}

// Only query the database if the table exists
if ($support_table_exists) {
  // Build the query based on filters
  $where_clause = "WHERE user_id = ?";

  if ($filter_status != 'all') {
    $where_clause .= " AND status = ?";
  }

  if (!empty($search_term)) {
    $where_clause .= " AND (ticket_number LIKE ? OR subject LIKE ? OR message LIKE ?)";
  }

  // Count total matching tickets and status counts
  $count_sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_count,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_count,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count,
                SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_count
              FROM support_tickets $where_clause";

  if ($stmt = $conn->prepare($count_sql)) {
    if ($filter_status != 'all') {
      if (!empty($search_term)) {
        $search_param = "%$search_term%";
        $stmt->bind_param("issss", $_SESSION["user_id"], $filter_status, $search_param, $search_param, $search_param);
      } else {
        $stmt->bind_param("is", $_SESSION["user_id"], $filter_status);
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
    $count_result = $stmt->get_result();

    if ($count_row = $count_result->fetch_assoc()) {
      $total_tickets = $count_row['total'];
      $open_count = $count_row['open_count'];
      $in_progress_count = $count_row['in_progress_count'];
      $resolved_count = $count_row['resolved_count'];
      $closed_count = $count_row['closed_count'];
    }

    $stmt->close();
  }

  // Get the tickets for current page
  if ($total_tickets > 0) {
    $sql = "SELECT * FROM support_tickets $where_clause ORDER BY $sort_by $sort_order LIMIT $tickets_per_page OFFSET $offset";

    if ($stmt = $conn->prepare($sql)) {
      if ($filter_status != 'all') {
        if (!empty($search_term)) {
          $search_param = "%$search_term%";
          $stmt->bind_param("issss", $_SESSION["user_id"], $filter_status, $search_param, $search_param, $search_param);
        } else {
          $stmt->bind_param("is", $_SESSION["user_id"], $filter_status);
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

      while ($ticket = $result->fetch_assoc()) {
        $tickets[] = $ticket;
      }

      $stmt->close();
    }
  }
} else {
  // If the table doesn't exist, create sample tickets for display purposes
  $tickets = [
    [
      'id' => 1,
      'ticket_number' => 'TKT-20250301-1234',
      'subject' => 'Order Delivery Delay',
      'category' => 'Shipping',
      'priority' => 'high',
      'message' => 'My order #12345 was supposed to be delivered yesterday but I still haven\'t received it.',
      'status' => 'in_progress',
      'created_at' => '2025-03-01 14:30:45',
      'updated_at' => '2025-03-02 09:15:22'
    ],
    [
      'id' => 2,
      'ticket_number' => 'TKT-20250310-5678',
      'subject' => 'Product Information Request',
      'category' => 'Product',
      'priority' => 'medium',
      'message' => 'I need more specifications about the item with SKU BT-45872.',
      'status' => 'open',
      'created_at' => '2025-03-10 10:22:33',
      'updated_at' => null
    ],
    [
      'id' => 3,
      'ticket_number' => 'TKT-20250205-9012',
      'subject' => 'Refund Status',
      'category' => 'Billing',
      'priority' => 'high',
      'message' => 'I returned my order two weeks ago but haven\'t received my refund yet.',
      'status' => 'resolved',
      'created_at' => '2025-02-05 16:45:10',
      'updated_at' => '2025-02-12 11:30:05'
    ]
  ];

  $total_tickets = count($tickets);
  $open_count = 1;
  $in_progress_count = 1;
  $resolved_count = 1;
  $closed_count = 0;
}

// Calculate pagination information
$total_pages = ceil($total_tickets / $tickets_per_page);
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
  <title>BS Traders - Customer Support</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
  <style>
    .main-style {
      height: 100vh;
      overflow-y: auto;
    }
  </style>
  <style>

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
          <div class="flex items-center px-5">
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
      <main class="flex-1 overflow-y-auto">
        <!-- Page Header -->
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-4 sm:space-y-0">
          <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Customer Support</h1>
            <p class="mt-1 text-sm text-gray-600">Get help with your orders, products and services</p>
          </div>

          <a href="#new-ticket" class="inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 w-full sm:w-auto">
            <i class="fas fa-plus mr-2"></i>
            New Support Ticket
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

        <!-- My Support Tickets -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6 max-w-full">
          <!-- Header Section - Fully mobile optimized -->
          <div class="border-b border-gray-200 p-4 flex flex-col space-y-4">
            <h2 class="text-lg font-medium text-gray-900">My Support Tickets</h2>

            <!-- Filter and Search - Stacked and full width on all screens -->
            <form action="user-support.php" method="GET" class="w-full">
              <div class="flex flex-col space-y-3">
                <!-- Status Dropdown - Full width on mobile -->
                <div class="relative w-full">
                  <select id="status" name="status" onChange="this.form.submit()"
                    class="w-full appearance-none pl-3 pr-8 py-2.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="all" <?php echo $filter_status == 'all' ? 'selected' : ''; ?>>All Tickets</option>
                    <option value="open" <?php echo $filter_status == 'open' ? 'selected' : ''; ?>>Open</option>
                    <option value="in_progress" <?php echo $filter_status == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="resolved" <?php echo $filter_status == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    <option value="closed" <?php echo $filter_status == 'closed' ? 'selected' : ''; ?>>Closed</option>
                  </select>
                  <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                    <i class="fas fa-chevron-down text-xs"></i>
                  </div>
                </div>

                <!-- Search Box - Full width on mobile with larger touch targets -->
                <div class="relative w-full">
                  <input type="text" name="search" value="<?php echo htmlspecialchars($search_term); ?>"
                    class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="Search tickets...">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                  </div>
                  <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center text-indigo-600">
                    <i class="fas fa-arrow-right"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>

          <?php if (count($tickets) > 0): ?>
            <!-- Mobile-optimized card view (used at all screen sizes) -->
            <div class="divide-y divide-gray-200">
              <?php foreach ($tickets as $ticket): ?>
                <?php
                // Set status badge
                $status_badge = '';
                switch ($ticket['status']) {
                  case 'open':
                    $status_badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Open</span>';
                    break;
                  case 'in_progress':
                    $status_badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">In Progress</span>';
                    break;
                  case 'resolved':
                    $status_badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Resolved</span>';
                    break;
                  case 'closed':
                    $status_badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Closed</span>';
                    break;
                  default:
                    $status_badge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Unknown</span>';
                }

                // Format date
                $created_date = date('M d, Y', strtotime($ticket['created_at']));

                // Category icon
                $category_icon = 'fa-circle-question';
                $category_color = 'text-gray-500';

                switch (strtolower($ticket['category'])) {
                  case 'billing':
                    $category_icon = 'fa-credit-card';
                    $category_color = 'text-purple-500';
                    break;
                  case 'shipping':
                    $category_icon = 'fa-truck';
                    $category_color = 'text-orange-500';
                    break;
                  case 'product':
                    $category_icon = 'fa-box';
                    $category_color = 'text-blue-500';
                    break;
                  case 'technical':
                    $category_icon = 'fa-wrench';
                    $category_color = 'text-red-500';
                    break;
                  case 'other':
                    $category_icon = 'fa-circle-question';
                    $category_color = 'text-gray-500';
                    break;
                }
                ?>
                <!-- Ticket Card - Optimized for touch with adequate spacing -->
                <div class="p-4">
                  <!-- Ticket Header -->
                  <div class="flex justify-between items-start mb-3">
                    <div class="text-sm font-medium text-indigo-600">
                      #<?php echo htmlspecialchars($ticket['ticket_number']); ?>
                    </div>
                    <div>
                      <?php echo $status_badge; ?>
                    </div>
                  </div>

                  <!-- Ticket Subject and Preview -->
                  <div class="mb-3">
                    <div class="text-base font-medium text-gray-900 mb-1">
                      <?php echo htmlspecialchars($ticket['subject']); ?>
                    </div>
                    <div class="text-sm text-gray-500">
                      <?php echo htmlspecialchars(substr($ticket['message'], 0, 60)) . (strlen($ticket['message']) > 60 ? '...' : ''); ?>
                    </div>
                  </div>

                  <!-- Category and Date info -->
                  <div class="flex flex-wrap justify-between items-center text-sm text-gray-500 mb-3">
                    <div class="flex items-center mr-2 mb-2">
                      <i class="fas <?php echo $category_icon; ?> <?php echo $category_color; ?> mr-2"></i>
                      <span><?php echo htmlspecialchars($ticket['category']); ?></span>
                    </div>
                    <div class="mb-2">
                      <i class="far fa-calendar-alt mr-1 text-gray-400"></i>
                      <?php echo $created_date; ?>
                    </div>
                  </div>

                  <!-- Action Buttons - Large touch targets -->
                  <div class="flex space-x-3 pt-2">
                    <a href="view-ticket.php?id=<?php echo $ticket['id']; ?>"
                      class="flex-1 text-center py-2.5 rounded-md bg-gray-100 text-gray-700 text-sm font-medium hover:bg-gray-200 transition duration-150">
                      <i class="fas fa-eye mr-1"></i> View
                    </a>
                    <?php if ($ticket['status'] == 'open' || $ticket['status'] == 'in_progress'): ?>
                      <a href="ticket-reply.php?id=<?php echo $ticket['id']; ?>"
                        class="flex-1 text-center py-2.5 rounded-md bg-indigo-100 text-indigo-700 text-sm font-medium hover:bg-indigo-200 transition duration-150">
                        <i class="fas fa-reply mr-1"></i> Reply
                      </a>
                    <?php else: ?>
                      <button disabled
                        class="flex-1 text-center py-2.5 rounded-md bg-gray-100 text-gray-400 text-sm font-medium cursor-not-allowed">
                        <i class="fas fa-reply mr-1"></i> Reply
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Mobile-optimized Pagination -->
            <?php if ($total_pages > 1): ?>
              <div class="px-4 py-5 bg-gray-50 border-t border-gray-200">
                <div class="text-sm text-center text-gray-700 mb-4">
                  Showing <span class="font-medium"><?php echo ($page - 1) * $tickets_per_page + 1; ?></span>
                  to <span class="font-medium"><?php echo min($page * $tickets_per_page, $total_tickets); ?></span>
                  of <span class="font-medium"><?php echo $total_tickets; ?></span> results
                </div>

                <div class="flex justify-center">
                  <nav class="inline-flex shadow-sm rounded-md" aria-label="Pagination">
                    <!-- Previous Button - Larger touch target -->
                    <?php if ($has_previous): ?>
                      <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $filter_status; ?>&search=<?php echo urlencode($search_term); ?>"
                        class="relative inline-flex items-center justify-center px-4 py-2 w-12 h-10 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Previous</span>
                        <i class="fas fa-chevron-left"></i>
                      </a>
                    <?php else: ?>
                      <span class="relative inline-flex items-center justify-center px-4 py-2 w-12 h-10 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                        <span class="sr-only">Previous</span>
                        <i class="fas fa-chevron-left"></i>
                      </span>
                    <?php endif; ?>

                    <!-- Current/Total Pages - Simplified for mobile -->
                    <div class="relative inline-flex items-center justify-center px-4 py-2 w-20 border-t border-b border-gray-300 bg-white text-sm font-medium">
                      <?php echo $page; ?> / <?php echo $total_pages; ?>
                    </div>

                    <!-- Next Button - Larger touch target -->
                    <?php if ($has_next): ?>
                      <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $filter_status; ?>&search=<?php echo urlencode($search_term); ?>"
                        class="relative inline-flex items-center justify-center px-4 py-2 w-12 h-10 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <span class="sr-only">Next</span>
                        <i class="fas fa-chevron-right"></i>
                      </a>
                    <?php else: ?>
                      <span class="relative inline-flex items-center justify-center px-4 py-2 w-12 h-10 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-400 cursor-not-allowed">
                        <span class="sr-only">Next</span>
                        <i class="fas fa-chevron-right"></i>
                      </span>
                    <?php endif; ?>
                  </nav>
                </div>
              </div>
            <?php endif; ?>

          <?php else: ?>
            <!-- Empty state - Optimized for mobile -->
            <div class="px-4 py-8 text-center">
              <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100 text-indigo-500 mb-4">
                <i class="fas fa-ticket-alt fa-lg"></i>
              </div>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No support tickets found</h3>
              <p class="text-gray-500 mb-6">You don't have any support tickets matching your current filters.</p>
              <div class="flex flex-col space-y-3">
                <a href="user-support.php"
                  class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                  <i class="fas fa-redo mr-2"></i>
                  Reset Filters
                </a>
                <a href="#new-ticket"
                  class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                  <i class="fas fa-plus mr-2"></i>
                  Create New Ticket
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- New Support Ticket Form -->
        <div id="new-ticket" class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
          <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-900">Create New Support Ticket</h2>
          </div>

          <div class="p-6">
            <form action="user-support.php" method="POST" class="space-y-6">
              <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <!-- Subject -->
                <div class="sm:col-span-4">
                  <label for="subject" class="block text-sm font-medium text-gray-700">Subject <span class="text-red-500">*</span></label>
                  <div class="mt-1 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                      <i class="fas fa-ticket-alt"></i>
                    </span>
                    <input type="text" name="subject" id="subject" required
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Brief description of your issue">
                  </div>
                </div>

                <!-- Category -->
                <div class="sm:col-span-3">
                  <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                  <div class="mt-1 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                      <i class="fas fa-folder"></i>
                    </span>
                    <select id="category" name="category"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                      <option value="Billing">Billing & Payments</option>
                      <option value="Shipping">Shipping & Delivery</option>
                      <option value="Product">Product Information</option>
                      <option value="Technical">Technical Support</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                </div>

                <!-- Priority -->
                <div class="sm:col-span-3">
                  <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                  <div class="mt-1 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                      <i class="fas fa-flag"></i>
                    </span>
                    <select id="priority" name="priority"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                      <option value="low">Low</option>
                      <option value="medium" selected>Medium</option>
                      <option value="high">High</option>
                    </select>
                  </div>
                </div>

                <!-- Message -->
                <div class="sm:col-span-6">
                  <label for="message" class="block text-sm font-medium text-gray-700">Message <span class="text-red-500">*</span></label>
                  <div class="mt-1 relative">
                    <span class="absolute top-3 left-3 text-gray-400">
                      <i class="fas fa-comment"></i>
                    </span>
                    <textarea id="message" name="message" rows="5" required
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Please provide details about your issue..."></textarea>
                  </div>
                  <p class="mt-2 text-sm text-gray-500">
                    Include as much detail as possible to help us resolve your issue quickly.
                  </p>
                </div>

                <!-- Attachments - This would require additional backend processing -->
                <div class="sm:col-span-6">
                  <label for="attachments" class="block text-sm font-medium text-gray-700">Attachments (Optional)</label>
                  <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                    <div class="space-y-1 text-center">
                      <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                      <div class="flex text-sm text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                          <span>Upload files</span>
                          <input id="file-upload" name="attachments[]" type="file" class="sr-only" multiple>
                        </label>
                        <p class="pl-1">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">
                        PNG, JPG, PDF up to 10MB each
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="pt-5">
                <div class="flex justify-end">
                  <button type="reset" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Clear Form
                  </button>
                  <button type="submit" name="submit_ticket" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Submit Ticket
                  </button>
                </div>
              </div>
            </form>
          </div>
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
          <a href="user-invoices.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-file-invoice w-5 h-5 mr-3"></i>
            <span>Invoices</span>
          </a>
          <a href="user-support.php" class="flex items-center px-4 py-2 bg-indigo-700 text-white rounded-lg">
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

    // Smooth scroll to new ticket form when "New Support Ticket" button is clicked
    document.querySelectorAll('a[href="#new-ticket"]').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();

        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });
  </script>
</body>

</html>