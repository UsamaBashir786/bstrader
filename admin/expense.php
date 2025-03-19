<?php

/**
 * BS Traders - Expense Management System
 * Complete implementation with Model-View-Controller pattern
 */

/**
 * Expense Model
 * 
 * This class manages all database operations for expenses
 */
class ExpenseModel
{
  private $conn;

  /**
   * Constructor - establishes database connection
   */
  public function __construct()
  {
    $this->conn = new mysqli('localhost', 'root', '', 'bs_trader');

    if ($this->conn->connect_error) {
      die("Connection failed: " . $this->conn->connect_error);
    }

    // Create the expenses table if it doesn't exist
    $this->createExpensesTable();
  }

  /**
   * Create expenses table if it doesn't exist
   */
  private function createExpensesTable()
  {
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

    if ($this->conn->query($create_table_sql) === FALSE) {
      die("Error creating table: " . $this->conn->error);
    }
  }

  /**
   * Get all expenses, with optional filtering
   *
   * @param array $filters Optional filtering criteria
   * @return array Expenses matching the criteria
   */
  public function getAllExpenses($filters = [])
  {
    $where_clause = "";
    $filter_params = [];
    $param_types = "";

    if (!empty($filters)) {
      $conditions = [];

      if (!empty($filters['category']) && $filters['category'] != 'all') {
        $conditions[] = "category = ?";
        $filter_params[] = $filters['category'];
        $param_types .= "s";
      }

      if (!empty($filters['month']) && $filters['month'] != 'all') {
        $conditions[] = "MONTH(date) = ?";
        $filter_params[] = $filters['month'];
        $param_types .= "i";
      }

      if (!empty($filters['year']) && $filters['year'] != 'all') {
        $conditions[] = "YEAR(date) = ?";
        $filter_params[] = $filters['year'];
        $param_types .= "i";
      }

      if (!empty($filters['search'])) {
        $conditions[] = "(title LIKE ? OR notes LIKE ?)";
        $search_term = "%" . $filters['search'] . "%";
        $filter_params[] = $search_term;
        $filter_params[] = $search_term;
        $param_types .= "ss";
      }

      if (count($conditions) > 0) {
        $where_clause = "WHERE " . implode(" AND ", $conditions);
      }
    }

    $query = "SELECT id, title, category, amount, date, paid_by, status, payment_method, notes, receipt_path 
                  FROM expenses 
                  $where_clause
                  ORDER BY date DESC";

    $expenses = [];

    if (!empty($param_types)) {
      $stmt = $this->conn->prepare($query);
      $stmt->bind_param($param_types, ...$filter_params);
      $stmt->execute();
      $result = $stmt->get_result();
    } else {
      $result = $this->conn->query($query);
    }

    while ($row = $result->fetch_assoc()) {
      $expenses[] = $row;
    }

    return $expenses;
  }

  /**
   * Get a single expense by ID
   *
   * @param int $id Expense ID
   * @return array|null Expense details or null if not found
   */
  public function getExpenseById($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM expenses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
      return null;
    }

    return $result->fetch_assoc();
  }

  /**
   * Add a new expense
   *
   * @param array $expense_data Expense details
   * @return int|bool New expense ID on success, false on failure
   */
  public function addExpense($expense_data)
  {
    $stmt = $this->conn->prepare("INSERT INTO expenses (title, category, amount, date, paid_by, status, payment_method, notes, receipt_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
      "ssdssssss",
      $expense_data['title'],
      $expense_data['category'],
      $expense_data['amount'],
      $expense_data['date'],
      $expense_data['paid_by'],
      $expense_data['status'],
      $expense_data['payment_method'],
      $expense_data['notes'],
      $expense_data['receipt_path']
    );

    if ($stmt->execute()) {
      return $this->conn->insert_id;
    }

    return false;
  }

  /**
   * Update an existing expense
   *
   * @param int $id Expense ID
   * @param array $expense_data Updated expense details
   * @return bool True on success, false on failure
   */
  public function updateExpense($id, $expense_data)
  {
    $stmt = $this->conn->prepare("UPDATE expenses SET 
            title = ?, 
            category = ?, 
            amount = ?, 
            date = ?, 
            paid_by = ?, 
            status = ?, 
            payment_method = ?, 
            notes = ?
            WHERE id = ?");

    $stmt->bind_param(
      "ssdssssis",
      $expense_data['title'],
      $expense_data['category'],
      $expense_data['amount'],
      $expense_data['date'],
      $expense_data['paid_by'],
      $expense_data['status'],
      $expense_data['payment_method'],
      $expense_data['notes'],
      $id
    );

    if ($stmt->execute()) {
      return true;
    }

    return false;
  }

  /**
   * Update receipt path for an expense
   *
   * @param int $id Expense ID
   * @param string $receipt_path Path to receipt file
   * @return bool True on success, false on failure
   */
  public function updateReceiptPath($id, $receipt_path)
  {
    $stmt = $this->conn->prepare("UPDATE expenses SET receipt_path = ? WHERE id = ?");
    $stmt->bind_param("si", $receipt_path, $id);

    return $stmt->execute();
  }

  /**
   * Delete an expense
   *
   * @param int $id Expense ID to delete
   * @return bool True on success, false on failure
   */
  public function deleteExpense($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->bind_param("i", $id);

    return $stmt->execute();
  }

  /**
   * Get monthly expense total
   *
   * @param int $month Month number (1-12)
   * @param int $year Year (e.g., 2023)
   * @return float Total expenses for the month
   */
  public function getMonthlyTotal($month, $year)
  {
    $stmt = $this->conn->prepare("SELECT SUM(amount) as total FROM expenses WHERE MONTH(date) = ? AND YEAR(date) = ?");
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'] ?? 0;
  }

  /**
   * Get monthly totals by category
   *
   * @param int $month Month number (1-12)
   * @param int $year Year (e.g., 2023)
   * @return array Category totals for the month
   */
  public function getMonthlyCategoryTotals($month, $year)
  {
    $query = "SELECT 
                    category,
                    SUM(amount) as category_total 
                  FROM expenses 
                  WHERE MONTH(date) = ? AND YEAR(date) = ?
                  GROUP BY category";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();

    $result = $stmt->get_result();
    $category_totals = [];

    while ($row = $result->fetch_assoc()) {
      $category_totals[$row['category']] = $row['category_total'];
    }

    return $category_totals;
  }

  /**
   * Get total expenses by specific categories
   *
   * @param array $categories Array of category names
   * @param int $month Month number (1-12)
   * @param int $year Year (e.g., 2023)
   * @return array Category totals
   */
  public function getTotalsByCategories($categories, $month, $year)
  {
    $category_totals = [];

    foreach ($categories as $category) {
      $query = "SELECT SUM(amount) as category_total FROM expenses WHERE category = ? AND MONTH(date) = ? AND YEAR(date) = ?";
      $stmt = $this->conn->prepare($query);
      $stmt->bind_param("sii", $category, $month, $year);
      $stmt->execute();
      $result = $stmt->get_result()->fetch_assoc();
      $category_totals[$category] = $result['category_total'] ?? 0;
    }

    return $category_totals;
  }

  /**
   * Get monthly totals aggregated by year and month
   *
   * @return array Monthly totals and category breakdowns
   */
  public function getMonthlyTotals()
  {
    $query = "SELECT 
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

    $result = $this->conn->query($query);
    $monthly_totals = [];

    while ($row = $result->fetch_assoc()) {
      $monthly_totals[] = $row;
    }

    return $monthly_totals;
  }

  /**
   * Get all expense categories
   *
   * @return array List of unique categories
   */
  public function getAllCategories()
  {
    $query = "SELECT DISTINCT category FROM expenses ORDER BY category";
    $result = $this->conn->query($query);
    $categories = [];

    while ($row = $result->fetch_assoc()) {
      $categories[] = $row['category'];
    }

    return $categories;
  }

  /**
   * Get all paid by persons
   *
   * @return array List of unique persons who paid
   */
  public function getAllPaidByPersons()
  {
    $query = "SELECT DISTINCT paid_by FROM expenses ORDER BY paid_by";
    $result = $this->conn->query($query);
    $persons = [];

    while ($row = $result->fetch_assoc()) {
      $persons[] = $row['paid_by'];
    }

    return $persons;
  }

  /**
   * Handle file upload for expense receipt
   *
   * @param array $file File data from $_FILES
   * @return string|false Path to uploaded file or false on failure
   */
  public function handleFileUpload($file)
  {
    if (isset($file) && $file['error'] == 0) {
      $upload_dir = 'uploads/receipts/';

      // Create directory if it doesn't exist
      if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
      }

      $file_name = time() . '_' . basename($file['name']);
      $target_path = $upload_dir . $file_name;

      if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return $target_path;
      }
    }

    return false;
  }

  /**
   * Get expense statistics for dashboard
   *
   * @return array Statistics data
   */
  public function getExpenseStats()
  {
    $current_month = date('n');
    $current_year = date('Y');
    $previous_month = ($current_month == 1) ? 12 : $current_month - 1;
    $previous_month_year = ($current_month == 1) ? $current_year - 1 : $current_year;

    // Current month total
    $current_month_total = $this->getMonthlyTotal($current_month, $current_year);

    // Previous month total
    $previous_month_total = $this->getMonthlyTotal($previous_month, $previous_month_year);

    // Month over month change
    $month_change_percent = 0;
    if ($previous_month_total > 0) {
      $month_change_percent = (($current_month_total - $previous_month_total) / $previous_month_total) * 100;
    }

    // Current year total
    $stmt = $this->conn->prepare("SELECT SUM(amount) as total FROM expenses WHERE YEAR(date) = ?");
    $stmt->bind_param("i", $current_year);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $year_total = $result['total'] ?? 0;

    // Top categories
    $query = "SELECT 
                    category, 
                    SUM(amount) as total 
                  FROM expenses 
                  WHERE MONTH(date) = ? AND YEAR(date) = ?
                  GROUP BY category 
                  ORDER BY total DESC 
                  LIMIT 3";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ii", $current_month, $current_year);
    $stmt->execute();
    $result = $stmt->get_result();

    $top_categories = [];
    while ($row = $result->fetch_assoc()) {
      $top_categories[] = $row;
    }

    return [
      'current_month_total' => $current_month_total,
      'previous_month_total' => $previous_month_total,
      'month_change_percent' => $month_change_percent,
      'year_total' => $year_total,
      'top_categories' => $top_categories
    ];
  }

  /**
   * Helper function to format money amount
   *
   * @param float $amount Amount to format
   * @return string Formatted amount
   */
  public static function formatMoney($amount)
  {
    return '$' . number_format($amount, 2);
  }

  /**
   * Helper function to get month name from number
   *
   * @param int $month_number Month number (1-12)
   * @return string Month name
   */
  public static function getMonthName($month_number)
  {
    return date("F", mktime(0, 0, 0, $month_number, 1));
  }

  /**
   * Destructor - Close the database connection
   */
  public function __destruct()
  {
    $this->conn->close();
  }
}

/**
 * Controller functions for expense management
 */

/**
 * Handle the Add Expense form submission
 */
function handleAddExpense()
{
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expense-title'])) {
    $expenseModel = new ExpenseModel();

    // Collect form data
    $expense_data = [
      'title' => $_POST['expense-title'],
      'category' => $_POST['category'],
      'amount' => $_POST['amount'],
      'date' => $_POST['expense-date'],
      'paid_by' => $_POST['paid-by'],
      'status' => $_POST['status'],
      'payment_method' => $_POST['payment-method'],
      'notes' => $_POST['notes'],
      'receipt_path' => ''
    ];

    // Handle file upload if present
    if (isset($_FILES['file-upload']) && $_FILES['file-upload']['error'] == 0) {
      $receipt_path = $expenseModel->handleFileUpload($_FILES['file-upload']);
      if ($receipt_path) {
        $expense_data['receipt_path'] = $receipt_path;
      }
    }

    // Add the expense
    $result = $expenseModel->addExpense($expense_data);

    if ($result) {
      // Redirect to prevent form resubmission
      header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
      exit();
    } else {
      $GLOBALS['error_message'] = "Error adding expense.";
    }
  }
}

/**
 * Handle expense deletion
 */
function handleDeleteExpense()
{
  if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $expenseModel = new ExpenseModel();
    $id = $_GET['delete'];

    // Get the expense to check if it has a receipt file
    $expense = $expenseModel->getExpenseById($id);

    // Delete the expense
    $result = $expenseModel->deleteExpense($id);

    if ($result) {
      // Delete the receipt file if it exists
      if (!empty($expense['receipt_path']) && file_exists($expense['receipt_path'])) {
        unlink($expense['receipt_path']);
      }

      header("Location: " . $_SERVER['PHP_SELF'] . "?deleted=1");
      exit();
    }
  }
}

/**
 * Get expenses with optional filtering
 */
function getExpenses()
{
  $expenseModel = new ExpenseModel();
  $filters = [];

  // Process filter parameters if present
  if (isset($_GET['filter'])) {
    if (!empty($_GET['category']) && $_GET['category'] != 'all') {
      $filters['category'] = $_GET['category'];
    }

    if (!empty($_GET['month']) && $_GET['month'] != 'all') {
      $filters['month'] = $_GET['month'];
    }

    if (!empty($_GET['year']) && $_GET['year'] != 'all') {
      $filters['year'] = $_GET['year'];
    }

    if (!empty($_GET['search'])) {
      $filters['search'] = $_GET['search'];
    }
  }

  // Get filtered expenses
  return $expenseModel->getAllExpenses($filters);
}

/**
 * Get monthly summary data for display
 */
function getMonthlySummary()
{
  $expenseModel = new ExpenseModel();

  // Current month and year
  $current_month = date('n');
  $current_year = date('Y');

  // Total expenses for current month
  $month_total = $expenseModel->getMonthlyTotal($current_month, $current_year);

  // Category totals
  $categories = ['Bills & Utilities', 'Purchases', 'Tax', 'Tea & Refreshments', 'Monthly Expenses'];
  $category_totals = $expenseModel->getTotalsByCategories($categories, $current_month, $current_year);

  // Monthly totals for all months
  $monthly_totals = $expenseModel->getMonthlyTotals();

  return [
    'month_total' => $month_total,
    'category_totals' => $category_totals,
    'monthly_totals' => $monthly_totals
  ];
}

/**
 * Format money amount 
 */
function formatMoney($amount)
{
  return ExpenseModel::formatMoney($amount);
}

/**
 * Get month name from number
 */
function getMonthName($month_number)
{
  return ExpenseModel::getMonthName($month_number);
}

// Initialize the expense model
$expenseModel = new ExpenseModel();

// Handle form submissions
handleAddExpense();
handleDeleteExpense();

// Get expenses for display
$expenses = getExpenses();

// Get monthly summary data
$summary_data = getMonthlySummary();
$month_total = $summary_data['month_total'];
$category_totals = $summary_data['category_totals'];
$monthly_totals = $summary_data['monthly_totals'];

// Get all unique categories for dropdown/filtering
$all_categories = $expenseModel->getAllCategories();

// Get all unique paid-by persons
$all_paid_by = $expenseModel->getAllPaidByPersons();

// Get expense statistics for dashboard
$expense_stats = $expenseModel->getExpenseStats();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BS Traders - Expense Management</title>
  <link rel="stylesheet" href="../src/output.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
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
          <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
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
                        <div class="text-sm text-<?php echo $expense_stats['month_change_percent'] >= 0 ? 'red' : 'green'; ?>-500">
                          <?php echo $expense_stats['month_change_percent'] >= 0 ? '+' : ''; ?>
                          <?php echo number_format($expense_stats['month_change_percent'], 1); ?>% from last month
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
                          <?php echo formatMoney($category_totals['Bills & Utilities'] ?? 0); ?>
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
                          <?php echo formatMoney($category_totals['Purchases'] ?? 0); ?>
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
                          <?php echo formatMoney($category_totals['Tea & Refreshments'] ?? 0); ?>
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
                  <input
                    type="text"
                    name="category"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                    placeholder="Filter by category"
                    list="category-options"
                    value="<?php echo (isset($_GET['category']) && $_GET['category'] != 'all') ? htmlspecialchars($_GET['category']) : ''; ?>" />
                  <datalist id="category-options">
                    <?php foreach ($all_categories as $category): ?>
                      <option value="<?php echo htmlspecialchars($category); ?>">
                      <?php endforeach; ?>
                  </datalist>
                </div>
                <div>
                  <select
                    name="month"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="all">All Months</option>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
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
                    for ($i = $current_year; $i >= $current_year - 2; $i--):
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
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                  <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Expense
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Category
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Paid By
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
                        <?php if (count($expenses) > 0): ?>
                          <?php foreach ($expenses as $expense): ?>
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
                                <?php if ($expense['status'] == 'Paid'): ?>
                                  <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Paid
                                  </span>
                                <?php elseif ($expense['status'] == 'Pending'): ?>
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
                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  Previous
                </a>
                <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                  Next
                </a>
              </div>
              <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                  <p class="text-sm text-gray-700">
                    Showing <span class="font-medium"><?php echo count($expenses) > 0 ? 1 : 0; ?></span> to
                    <span class="font-medium"><?php echo count($expenses); ?></span> of
                    <span class="font-medium"><?php echo count($expenses); ?></span> expenses
                  </p>
                </div>
                <div>
                  <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                      <span class="sr-only">Previous</span>
                      <i class="fas fa-chevron-left h-5 w-5"></i>
                    </a>
                    <a href="#" aria-current="page" class="z-10 bg-primary-50 border-primary-500 text-primary-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                      1
                    </a>
                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
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
                        <?php if (count($monthly_totals) > 0): ?>
                          <?php foreach ($monthly_totals as $month_data): ?>
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

  <!-- Add Expense Modal with Modern Design -->
  <div id="addExpenseModal" class="fixed z-50 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay with blur effect -->
      <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity" id="modalOverlay"></div>

      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

      <!-- Modal container with rounded corners and subtle shadow -->
      <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-gray-200 dark:border-gray-700">
        <!-- Close button with hover effect -->
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button type="button"
            class="bg-white dark:bg-gray-800 rounded-full p-1 text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
            onclick="closeModal()">
            <span class="sr-only">Close</span>
            <i class="fas fa-times h-5 w-5"></i>
          </button>
        </div>

        <div>
          <div class="sm:mt-0 sm:text-left">
            <!-- Title with decorative element -->
            <div class="flex items-center mb-4">
              <div class="bg-primary-600 h-8 w-1 rounded-full mr-3"></div>
              <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                Add New Expense
              </h3>
            </div>

            <!-- Form with modern styling -->
            <div class="mt-4">
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <!-- Expense Title -->
                  <div class="sm:col-span-6">
                    <label for="expense-title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Expense Title
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-tag text-gray-400"></i>
                      </div>
                      <input type="text"
                        name="expense-title"
                        id="expense-title"
                        class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
                        placeholder="Enter expense title"
                        required />
                    </div>
                  </div>

                  <!-- Category - Text Input -->
                  <div class="sm:col-span-3">
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Category
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-folder text-gray-400"></i>
                      </div>
                      <input type="text"
                        id="category"
                        name="category"
                        class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
                        placeholder="Enter category"
                        list="category-options"
                        required />
                      <datalist id="category-options">
                        <?php foreach ($all_categories as $category): ?>
                          <option value="<?php echo htmlspecialchars($category); ?>">
                          <?php endforeach; ?>
                      </datalist>
                    </div>
                  </div>

                  <!-- Date -->
                  <div class="sm:col-span-3">
                    <label for="expense-date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Date
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar text-gray-400"></i>
                      </div>
                      <input type="date"
                        name="expense-date"
                        id="expense-date"
                        class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
                        value="<?php echo date('Y-m-d'); ?>"
                        required />
                    </div>
                  </div>

                  <!-- Amount -->
                  <div class="sm:col-span-3">
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Amount
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 dark:text-gray-400 sm:text-sm">$</span>
                      </div>
                      <input type="number"
                        step="0.01"
                        name="amount"
                        id="amount"
                        class="pl-7 focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
                        placeholder="0.00"
                        required />
                    </div>
                  </div>

                  <!-- Paid By - Text Input Field -->
                  <div class="sm:col-span-3">
                    <label for="paid-by" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Paid By
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                      </div>
                      <input type="text"
                        id="paid-by"
                        name="paid-by"
                        class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
                        placeholder="Enter name"
                        list="paid-by-options"
                        required />
                      <!-- Datalist for paid-by suggestions -->
                      <datalist id="paid-by-options">
                        <?php foreach ($all_paid_by as $person): ?>
                          <option value="<?php echo htmlspecialchars($person); ?>">
                          <?php endforeach; ?>
                      </datalist>
                    </div>
                  </div>

                  <!-- Status - Select with Modern Styling -->
                  <div class="sm:col-span-3">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Status
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-check-circle text-gray-400"></i>
                      </div>
                      <select id="status"
                        name="status"
                        class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg appearance-none"
                        required>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Rejected">Rejected</option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                      </div>
                    </div>
                  </div>

                  <!-- Payment Method - Select with Modern Styling -->
                  <div class="sm:col-span-3">
                    <label for="payment-method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Payment Method
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-credit-card text-gray-400"></i>
                      </div>
                      <select id="payment-method"
                        name="payment-method"
                        class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg appearance-none"
                        required>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Other">Other</option>
                      </select>
                      <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                      </div>
                    </div>
                  </div>

                  <!-- Receipt Upload - Modern Design -->
                  <div class="sm:col-span-6">
                    <label for="receipt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Receipt
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                      <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                          <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                          <label for="file-upload" class="relative cursor-pointer bg-white dark:bg-transparent rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none">
                            <span>Upload a file</span>
                            <input id="file-upload" name="file-upload" type="file" class="sr-only" />
                          </label>
                          <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                          PNG, JPG, PDF up to 10MB
                        </p>
                      </div>
                    </div>
                  </div>

                  <!-- Notes - Textarea with Modern Styling -->
                  <div class="sm:col-span-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                      Notes
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                        <i class="fas fa-sticky-note text-gray-400"></i>
                      </div>
                      <textarea id="notes"
                        name="notes"
                        rows="3"
                        class="pl-10 shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg"
                        placeholder="Add any additional notes here..."></textarea>
                    </div>
                  </div>
                </div>

                <!-- Action Buttons with Modern Design -->
                <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row-reverse gap-3">
                  <button type="submit"
                    class="w-full sm:w-auto flex justify-center items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Expense
                  </button>
                  <button type="button"
                    class="w-full sm:w-auto flex justify-center items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-base font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors"
                    onclick="closeModal()">
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