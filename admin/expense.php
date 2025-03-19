<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bs_trader');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$create_table_sql = "CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    date DATE NOT NULL,
    paid_by VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    notes TEXT,
    receipt_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_table_sql) === FALSE) {
    echo "Error creating table: " . $conn->error;
}

// Handle Add Expense form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expense-title'])) {
    $title = $_POST['expense-title'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $date = $_POST['expense-date'];
    $paid_by = $_POST['paid-by'];
    $status = $_POST['status'];
    $payment_method = $_POST['payment-method'];
    $notes = $_POST['notes'];
    $receipt_path = '';
    
    // Handle file upload if a file was submitted
    if(isset($_FILES['file-upload']) && $_FILES['file-upload']['error'] == 0) {
        $upload_dir = 'uploads/receipts/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['file-upload']['name']);
        $target_path = $upload_dir . $file_name;
        
        if(move_uploaded_file($_FILES['file-upload']['tmp_name'], $target_path)) {
            $receipt_path = $target_path;
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO expenses (title, category, amount, date, paid_by, status, payment_method, notes, receipt_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssssss", $title, $category, $amount, $date, $paid_by, $status, $payment_method, $notes, $receipt_path);
    
    if($stmt->execute()) {
        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit();
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

// Handle Expense Deletion
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
    exit();
}

// Process filter parameters
$where_clause = "";
$filter_params = [];
$param_types = "";

if(isset($_GET['filter'])) {
    $conditions = [];
    
    if(!empty($_GET['category']) && $_GET['category'] != 'all') {
        $conditions[] = "category = ?";
        $filter_params[] = $_GET['category'];
        $param_types .= "s";
    }
    
    if(!empty($_GET['month']) && $_GET['month'] != 'all') {
        $conditions[] = "MONTH(date) = ?";
        $filter_params[] = $_GET['month'];
        $param_types .= "i";
    }
    
    if(!empty($_GET['year']) && $_GET['year'] != 'all') {
        $conditions[] = "YEAR(date) = ?";
        $filter_params[] = $_GET['year'];
        $param_types .= "i";
    }
    
    if(!empty($_GET['search'])) {
        $conditions[] = "(title LIKE ? OR notes LIKE ?)";
        $search_term = "%" . $_GET['search'] . "%";
        $filter_params[] = $search_term;
        $filter_params[] = $search_term;
        $param_types .= "ss";
    }
    
    if(count($conditions) > 0) {
        $where_clause = "WHERE " . implode(" AND ", $conditions);
    }
}

// Retrieve expenses with optional filtering
$expenses = [];
$query = "SELECT id, title, category, amount, date, paid_by, status, payment_method, notes, receipt_path 
          FROM expenses 
          $where_clause
          ORDER BY date DESC";

if(!empty($param_types)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types, ...$filter_params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

while ($row = $result->fetch_assoc()) {
    $expenses[] = $row;
}

// Calculate monthly summary
$current_month = date('n');
$current_year = date('Y');

// Total expenses for current month
$month_total_query = "SELECT SUM(amount) as total FROM expenses WHERE MONTH(date) = ? AND YEAR(date) = ?";
$stmt = $conn->prepare($month_total_query);
$stmt->bind_param("ii", $current_month, $current_year);
$stmt->execute();
$month_total_result = $stmt->get_result()->fetch_assoc();
$month_total = $month_total_result['total'] ?? 0;

// Category totals for current month
$category_totals = [];
$categories = ['Bills & Utilities', 'Purchases', 'Tax', 'Tea & Refreshments', 'Monthly Expenses'];

foreach($categories as $category) {
    $query = "SELECT SUM(amount) as category_total FROM expenses WHERE category = ? AND MONTH(date) = ? AND YEAR(date) = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sii", $category, $current_month, $current_year);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $category_totals[$category] = $result['category_total'] ?? 0;
}

// Calculate monthly totals for all months
$monthly_totals_query = "SELECT 
                            YEAR(date) as year, 
                            MONTH(date) as month, 
                            SUM(amount) as total,
                            SUM(CASE WHEN category = 'Bills & Utilities' THEN amount ELSE 0 END) as bills_total,
                            SUM(CASE WHEN category = 'Purchases' THEN amount ELSE 0 END) as purchases_total,
                            SUM(CASE WHEN category = 'Tea & Refreshments' THEN amount ELSE 0 END) as tea_total,
                            SUM(CASE WHEN category = 'Monthly Expenses' THEN amount ELSE 0 END) as monthly_total
                        FROM expenses 
                        GROUP BY YEAR(date), MONTH(date) 
                        ORDER BY year DESC, month DESC";
                        
$monthly_totals_result = $conn->query($monthly_totals_query);
$monthly_totals = [];

while($row = $monthly_totals_result->fetch_assoc()) {
    $monthly_totals[] = $row;
}

// Close the database connection
$conn->close();

// Helper function to get month name from number
function getMonthName($month_number) {
    return date("F", mktime(0, 0, 0, $month_number, 1));
}

// Helper function to format money amount
function formatMoney($amount) {
    return '$' . number_format($amount, 2);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BS Traders - Expense Management</title>
  <link rel="stylesheet" href="../src/output.css">
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: "#e6f1ff",
              100: "#cce3ff",
              200: "#99c7ff",
              300: "#66aaff",
              400: "#338eff",
              500: "#0072ff",
              600: "#005bcc",
              700: "#004499",
              800: "#002e66",
              900: "#001733",
            },
          },
        },
      },
    };
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
              <a href="user.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-user-shield mr-3 h-6 w-6"></i>
                User Management
              </a>
              <a href="salary.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-white  hover:bg-primary-600 hover:text-white">
                <i class="fas fa-money-bill-wave mr-3 h-6 w-6"></i>
                Salary Management
              </a>
              <a href="expense.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 bg-primary-800">
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
                <button
                  id="sidebarToggle"
                  type="button"
                  class="text-gray-500 hover:text-gray-900 focus:outline-none">
                  <i class="fas fa-bars h-6 w-6"></i>
                </button>
              </div>
              <div
                class="hidden md:ml-6 md:flex md:items-center md:space-x-4">
                <div class="px-3 py-2 text-sm font-medium text-gray-900">
                  BS Traders Distributed System
                </div>
              </div>
            </div>
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <span
                  class="hidden sm:inline-flex ml-3 items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700">
                  Date: <span id="current-date" class="ml-1"></span>
                </span>
              </div>
              <div
                class="hidden md:ml-4 md:flex-shrink-0 md:flex md:items-center">
                <button
                  class="p-1 ml-3 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-bell h-6 w-6"></i>
                </button>
                <div class="ml-3 relative">
                  <div>
                    <button
                      type="button"
                      class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                      id="user-menu-button">
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
                  <div
                    id="user-dropdown"
                    class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu">
                    <a
                      href="profile.php"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      role="menuitem">Your Profile</a>
                    <a
                      href="settings.php"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      role="menuitem">Settings</a>
                    <a
                      href="logout.php"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      role="menuitem">Logout</a>
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
          <div
            class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Expense Management
            </h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <button
                type="button"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                onclick="openModal()">
                <i class="fas fa-plus mr-2 -ml-1 h-5 w-5"></i>
                Add Expense
              </button>
            </div>
          </div>

          <?php if (isset($_GET['success'])): ?>
          <div class="mt-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
            <p>Expense added successfully!</p>
          </div>
          <?php endif; ?>

          <?php if (isset($_GET['deleted'])): ?>
          <div class="mt-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
            <p>Expense deleted successfully!</p>
          </div>
          <?php endif; ?>

          <?php if (isset($error_message)): ?>
          <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p><?php echo $error_message; ?></p>
          </div>
          <?php endif; ?>

          <!-- Expense Summary Cards -->
          <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Card 1 - Total Expenses -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <i class="fas fa-file-invoice-dollar text-red-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Expenses (<?php echo date('F'); ?>)
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo formatMoney($month_total); ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 2 - Bills & Utilities -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <i class="fas fa-receipt text-purple-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Bills & Utilities
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo formatMoney($category_totals['Bills & Utilities']); ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 3 - Purchases -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-shopping-cart text-blue-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Purchases
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo formatMoney($category_totals['Purchases']); ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card 4 - Tea & Misc -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-coffee text-green-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Tea & Misc
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo formatMoney($category_totals['Tea & Refreshments']); ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Expense filters and search -->
          <div class="mt-6 bg-white shadow rounded-lg p-4">
            <form action="" method="GET" class="flex flex-col md:flex-row justify-between gap-4">
              <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <div class="relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                  </div>
                  <input
                    type="text"
                    name="search"
                    class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md"
                    placeholder="Search expense..."
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
                </div>
                <div>
                  <select
                    name="category"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="all">All Categories</option>
                    <option value="Bills & Utilities" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Bills & Utilities') ? 'selected' : ''; ?>>Bills & Utilities</option>
                    <option value="Purchases" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Purchases') ? 'selected' : ''; ?>>Purchases</option>
                    <option value="Tax" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Tax') ? 'selected' : ''; ?>>Tax</option>
                    <option value="Tea & Refreshments" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Tea & Refreshments') ? 'selected' : ''; ?>>Tea & Refreshments</option>
                    <option value="Monthly Expenses" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Monthly Expenses') ? 'selected' : ''; ?>>Monthly Expenses</option>
                  </select>
                </div>
                <div>
                  <select
                    name="month"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="all">All Months</option>
                    <?php for($i = 1; $i <= 12; $i++): ?>
                      <option value="<?php echo $i; ?>" <?php echo (isset($_GET['month']) && $_GET['month'] == $i) ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                      </option>
                    <?php endfor; ?>
                  </select>
                </div>
                <div>
                  <select
                    name="year"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="all">All Years</option>
                    <?php 
                      $current_year = date('Y');
                      for($i = $current_year; $i >= $current_year-2; $i--): 
                    ?>
                      <option value="<?php echo $i; ?>" <?php echo (isset($_GET['year']) && $_GET['year'] == $i) ? 'selected' : ''; ?>>
                        Year <?php echo $i; ?>
                      </option>
                    <?php endfor; ?>
                  </select>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button
                  type="submit"
                  name="filter"
                  value="1"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-filter mr-2 h-5 w-5 text-gray-500"></i>
                  Filter
                </button>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-sync-alt mr-2 h-5 w-5 text-gray-500"></i>
                  Reset
                </a>
                <button
                  type="button"
                  onclick="window.location.href='export-expenses.php'"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-download mr-2 h-5 w-5 text-gray-500"></i>
                  Export
                </button>
              </div>
            </form>
          </div>

          <!-- Expense Table -->
          <div class="mt-6">
            <div class="flex flex-col">
              <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div
                  class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                  <div
                    class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Expense
                          </th>
                          <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                          </th>
                          <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                          </th>
                          <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                          </th>
                          <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Paid By
                          </th>
                          <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                          </th>
                          <th
                            scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(count($expenses) > 0): ?>
                          <?php foreach($expenses as $expense): ?>
                            <tr>
                              <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                  <?php echo htmlspecialchars($expense['title']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                  <?php echo date('F Y', strtotime($expense['date'])); ?>
                                </div>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($expense['category']); ?>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('F j, Y', strtotime($expense['date'])); ?>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                $<?php echo number_format($expense['amount'], 2); ?>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo htmlspecialchars($expense['paid_by']); ?>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($expense['status'] == 'Paid'): ?>
                                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Paid
                                  </span>
                                <?php elseif($expense['status'] == 'Pending'): ?>
                                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                  </span>
                                <?php else: ?>
                                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Rejected
                                  </span>
                                <?php endif; ?>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="edit-expense.php?id=<?php echo $expense['id']; ?>" class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-edit"></i></a>
                                <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $expense['id']; ?>)" class="text-red-600 hover:text-red-900 mr-3"><i class="fas fa-trash"></i></a>
                                <a href="view-expense.php?id=<?php echo $expense['id']; ?>" class="text-gray-600 hover:text-gray-900"><i class="fas fa-eye"></i></a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                              No expenses found. <a href="javascript:void(0);" onclick="openModal()" class="text-primary-600 hover:text-primary-900">Add an expense</a>.
                            </td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex items-center justify-between">
              <div class="flex-1 flex justify-between sm:hidden">
                <a
                  href="#"
                  class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  Previous
                </a>
                <a
                  href="#"
                  class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  Next
                </a>
              </div>
              <div
                class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing <span class="font-medium"><?php echo count($expenses) > 0 ? 1 : 0; ?></span> to
                    <span class="font-medium"><?php echo count($expenses); ?></span> of
                    <span class="font-medium"><?php echo count($expenses); ?></span> expenses
                  </p>
                </div>
                <div>
                  <nav
                    class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                    aria-label="Pagination">
                    <a
                      href="#"
                      class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                      <span class="sr-only">Previous</span>
                      <i class="fas fa-chevron-left h-5 w-5"></i>
                    </a>
                    <a
                      href="#"
                      aria-current="page"
                      class="z-10 bg-primary-50 border-primary-500 text-primary-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                      1
                    </a>
                    <a
                      href="#"
                      class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                      <span class="sr-only">Next</span>
                      <i class="fas fa-chevron-right h-5 w-5"></i>
                    </a>
                  </nav>
                </div>
              </div>
            </div>
          </div>

          <!-- Monthly Summary Table -->
          <div class="mt-8">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Monthly Expense Summary</h3>
            <div class="flex flex-col">
              <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                  <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Month
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Expenses
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bills & Utilities
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Purchases
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tea & Refreshments
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <?php if(count($monthly_totals) > 0): ?>
                          <?php foreach($monthly_totals as $month_data): ?>
                            <tr>
                              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?php echo getMonthName($month_data['month']) . ' ' . $month_data['year']; ?>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                $<?php echo number_format($month_data['total'], 2); ?>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                $<?php echo number_format($month_data['bills_total'], 2); ?>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                $<?php echo number_format($month_data['purchases_total'], 2); ?>
                              </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                $<?php echo number_format($month_data['tea_total'], 2); ?>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                              No monthly summary available yet.
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

  <!-- Add Expense Modal -->
  <div id="addExpenseModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div
      class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        id="modalOverlay"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      <div
        class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button
            type="button"
            class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            onclick="closeModal()">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3
              class="text-lg leading-6 font-medium text-gray-900"
              id="modal-title">
              Add New Expense
            </h3>
            <div class="mt-4">
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-6">
                    <label
                      for="expense-title"
                      class="block text-sm font-medium text-gray-700">Expense Title</label>
                    <div class="mt-1">
                      <input
                        type="text"
                        name="expense-title"
                        id="expense-title"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        required />
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label
                      for="category"
                      class="block text-sm font-medium text-gray-700">Category</label>
                    <div class="mt-1">
                      <select
                        id="category"
                        name="category"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        required>
                        <option value="">Select Category</option>
                        <option value="Bills & Utilities">Bills & Utilities</option>
                        <option value="Purchases">Purchases</option>
                        <option value="Tax">Tax</option>
                        <option value="Tea & Refreshments">Tea & Refreshments</option>
                        <option value="Monthly Expenses">Monthly Expenses</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label
                      for="expense-date"
                      class="block text-sm font-medium text-gray-700">Date</label>
                    <div class="mt-1">
                      <input
                        type="date"
                        name="expense-date"
                        id="expense-date"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" 
                        value="<?php echo date('Y-m-d'); ?>"
                        required />
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label
                      for="amount"
                      class="block text-sm font-medium text-gray-700">Amount</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input
                        type="number"
                        step="0.01"
                        name="amount"
                        id="amount"
                        class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                        placeholder="0.00"
                        required />
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label
                      for="paid-by"
                      class="block text-sm font-medium text-gray-700">Paid By</label>
                    <div class="mt-1">
                      <select
                        id="paid-by"
                        name="paid-by"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        required>
                        <option value="">Select Employee</option>
                        <option value="Ahmed Khan">Ahmed Khan</option>
                        <option value="Sara Ali">Sara Ali</option>
                        <option value="Bilal Ahmad">Bilal Ahmad</option>
                        <option value="Ayesha Malik">Ayesha Malik</option>
                        <option value="Omar Farooq">Omar Farooq</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label
                      for="status"
                      class="block text-sm font-medium text-gray-700">Status</label>
                    <div class="mt-1">
                      <select
                        id="status"
                        name="status"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        required>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Rejected">Rejected</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label
                      for="payment-method"
                      class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <div class="mt-1">
                      <select
                        id="payment-method"
                        name="payment-method"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                        required>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Other">Other</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label
                      for="receipt"
                      class="block text-sm font-medium text-gray-700">Receipt</label>
                    <div
                      class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                      <div class="space-y-1 text-center">
                        <svg
                          class="mx-auto h-12 w-12 text-gray-400"
                          stroke="currentColor"
                          fill="none"
                          viewBox="0 0 48 48"
                          aria-hidden="true">
                          <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                          <label
                            for="file-upload"
                            class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                            <span>Upload a file</span>
                            <input
                              id="file-upload"
                              name="file-upload"
                              type="file"
                              class="sr-only" />
                          </label>
                          <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">
                          PNG, JPG, PDF up to 10MB
                        </p>
                      </div>
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label
                      for="notes"
                      class="block text-sm font-medium text-gray-700">Notes</label>
                    <div class="mt-1">
                      <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>
                  </div>
                </div>
                <div
                  class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                  <button
                    type="submit"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm">
                    Save
                  </button>
                  <button
                    type="button"
                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                    onclick="closeModal()">
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
  <script>
    // Date display
    const currentDate = new Date();
    const options = {
      weekday: "long",
      year: "numeric",
      month: "long",
      day: "numeric",
    };
    document.getElementById("current-date").textContent =
      currentDate.toLocaleDateString("en-US", options);

    // User dropdown toggle
    const userMenuButton = document.getElementById("user-menu-button");
    const userDropdown = document.getElementById("user-dropdown");

    userMenuButton.addEventListener("click", () => {
      userDropdown.classList.toggle("hidden");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", (event) => {
      if (
        !userMenuButton.contains(event.target) &&
        !userDropdown.contains(event.target)
      ) {
        userDropdown.classList.add("hidden");
      }
    });

    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById("sidebarToggle");
    
    if (sidebarToggle) {
      sidebarToggle.addEventListener("click", () => {
        // Add your sidebar toggle logic here
      });
    }

    // Modal functions
    function openModal() {
      document.getElementById("addExpenseModal").classList.remove("hidden");
    }

    function closeModal() {
      document.getElementById("addExpenseModal").classList.add("hidden");
    }

    // Close modal when clicking outside
    document
      .getElementById("modalOverlay")
      .addEventListener("click", closeModal);
      
    // Confirm delete function
    function confirmDelete(id) {
      if (confirm("Are you sure you want to delete this expense?")) {
        window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>?delete=" + id;
      }
    }
    
    // Show success message for a limited time
    const successAlert = document.querySelector('.bg-green-100');
    const deletedAlert = document.querySelector('.bg-blue-100');
    
    if (successAlert || deletedAlert) {
      setTimeout(() => {
        if (successAlert) successAlert.style.display = 'none';
        if (deletedAlert) deletedAlert.style.display = 'none';
      }, 5000);
    }
    
    <?php if (isset($_GET['success']) || isset($_GET['deleted'])): ?>
    // Push the success/delete URL state without the query parameters
    window.history.replaceState({}, document.title, "<?php echo $_SERVER['PHP_SELF']; ?>");
    <?php endif; ?>
  </script>
</body>

</html>