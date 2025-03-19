<?php

/**
 * BS Traders - Salary Management System
 * Complete implementation with SalaryModel integration
 */

// Include the salary model
require_once 'includes/SalaryModel.php';

// Initialize the salary model
$salaryModel = new SalaryModel();

// Process form submissions
$success_message = "";
$error_message = "";

// Add or update salary payment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  if ($_POST['action'] === 'add_salary') {
    // Prepare data for adding new salary
    $salaryData = [
      'user_id' => $_POST['employee'],
      'amount' => $_POST['salary'],
      'bonus' => isset($_POST['bonus']) ? $_POST['bonus'] : 0,
      'month' => $_POST['month'],
      'year' => $_POST['year'],
      'payment_date' => !empty($_POST['payment_date']) ? $_POST['payment_date'] : null,
      'payment_method' => isset($_POST['payment_method']) ? $_POST['payment_method'] : 'bank transfer',
      'status' => $_POST['status'],
      'notes' => $_POST['notes']
    ];

    if ($salaryModel->addSalary($salaryData)) {
      $success_message = "Salary payment added successfully!";
    } else {
      $error_message = "Error adding salary payment. Please try again.";
    }
  } elseif ($_POST['action'] === 'update_salary' && isset($_POST['salary_id'])) {
    // Prepare data for updating existing salary
    $salaryData = [
      'user_id' => $_POST['employee'],
      'amount' => $_POST['salary'],
      'bonus' => isset($_POST['bonus']) ? $_POST['bonus'] : 0,
      'month' => $_POST['month'],
      'year' => $_POST['year'],
      'payment_date' => !empty($_POST['payment_date']) ? $_POST['payment_date'] : null,
      'payment_method' => isset($_POST['payment_method']) ? $_POST['payment_method'] : 'bank transfer',
      'status' => $_POST['status'],
      'notes' => $_POST['notes']
    ];

    if ($salaryModel->updateSalary($_POST['salary_id'], $salaryData)) {
      $success_message = "Salary payment updated successfully!";
    } else {
      $error_message = "Error updating salary payment. Please try again.";
    }
  } elseif ($_POST['action'] === 'add_advance') {
    // Prepare data for adding new advance salary request
    $advanceData = [
      'user_id' => $_POST['employee'],
      'amount' => $_POST['amount'],
      'request_date' => $_POST['request_date'],
      'reason' => $_POST['reason'],
      'repayment_method' => $_POST['repayment_method'],
      'installments' => $_POST['installments'],
      'notes' => $_POST['notes']
    ];

    if ($salaryModel->requestAdvanceSalary($advanceData)) {
      $success_message = "Advance salary request added successfully!";
    } else {
      $error_message = "Error adding advance salary request. Please try again.";
    }
  } elseif ($_POST['action'] === 'process_payment' && isset($_POST['advance_id'])) {
    // Prepare data for processing advance payment
    $paymentData = [
      'amount' => $_POST['payment_amount'],
      'payment_date' => $_POST['payment_date'],
      'payment_method' => $_POST['payment_method'],
      'notes' => $_POST['notes']
    ];

    if ($salaryModel->processAdvancePayment($_POST['advance_id'], $paymentData)) {
      $success_message = "Payment processed successfully!";
    } else {
      $error_message = "Error processing payment. Please try again.";
    }
  }
}

// Handle advance approval/rejection
if (isset($_GET['advance_id']) && isset($_GET['status']) && in_array($_GET['status'], ['approved', 'rejected'])) {
  $admin_id = 1; // Replace with actual admin ID from session
  $status = $_GET['status'];
  $advance_id = $_GET['advance_id'];
  $notes = isset($_GET['notes']) ? $_GET['notes'] : '';

  if ($salaryModel->updateAdvanceSalaryStatus($advance_id, $status, $admin_id, date('Y-m-d'), $notes)) {
    $success_message = "Advance request has been " . ucfirst($status);
  } else {
    $error_message = "Error updating advance request status. Please try again.";
  }
}

// Handle salary deletion
if (isset($_GET['delete_salary']) && is_numeric($_GET['delete_salary'])) {
  if ($salaryModel->deleteSalary($_GET['delete_salary'])) {
    $success_message = "Salary payment deleted successfully!";
  } else {
    $error_message = "Error deleting salary payment. Please try again.";
  }
}

// Handle advance deletion
if (isset($_GET['delete_advance']) && is_numeric($_GET['delete_advance'])) {
  if ($salaryModel->deleteAdvanceSalary($_GET['delete_advance'])) {
    $success_message = "Advance request deleted successfully!";
  } else {
    $error_message = "Error deleting advance request. Please try again.";
  }
}

// Set up filters for salary listing
$filters = [];
if (isset($_GET['month']) && !empty($_GET['month'])) {
  $filters['month'] = $_GET['month'];
}
if (isset($_GET['year']) && !empty($_GET['year'])) {
  $filters['year'] = $_GET['year'];
}
if (isset($_GET['status']) && !empty($_GET['status'])) {
  $filters['status'] = $_GET['status'];
}
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
  $filters['user_id'] = $_GET['user_id'];
}

// Get salary data based on filters
$salaries = $salaryModel->getAllSalaries($filters);

// Get advance salaries data
$advanceFilters = [];
if (isset($_GET['advance_status']) && !empty($_GET['advance_status'])) {
  $advanceFilters['status'] = $_GET['advance_status'];
}
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
  $advanceFilters['user_id'] = $_GET['user_id'];
}
$advances = $salaryModel->getAllAdvanceSalaries($advanceFilters);

// Get all users for dropdown selection
$users = $salaryModel->getAllUsers();

// Get current month statistics
$currentMonth = date('F');
$currentYear = date('Y');
$monthlyStats = $salaryModel->getSalaryStatsByYear($currentYear);
$advanceStats = $salaryModel->getAdvanceSalaryStats();

// Calculate totals for current month
$totalSalary = 0;
$totalAdvance = 0;
$totalNetPayable = 0;
$paymentDue = 0;

foreach ($salaries as $salary) {
  if ($salary['month'] === $currentMonth && $salary['year'] === $currentYear) {
    $totalSalary += $salary['amount'] + $salary['bonus'];
    $isPending = ($salary['status'] === 'pending');

    if ($isPending) {
      $paymentDue++;
    }
  }
}

foreach ($advances as $advance) {
  if ($advance['status'] === 'approved' || $advance['status'] === 'partially_paid') {
    $totalAdvance += $advance['remaining_amount'];
  }
}

$totalNetPayable = $totalSalary - $totalAdvance;

// Get salary ID for editing if provided
$editSalary = null;
if (isset($_GET['edit_salary']) && is_numeric($_GET['edit_salary'])) {
  $editSalary = $salaryModel->getSalaryById($_GET['edit_salary']);
}

// Get advance ID for viewing details if provided
$viewAdvance = null;
if (isset($_GET['view_advance']) && is_numeric($_GET['view_advance'])) {
  $viewAdvance = $salaryModel->getAdvanceSalaryById($_GET['view_advance']);
}

// Get the array of months for dropdowns
$months = SalaryModel::getMonths();
$years = SalaryModel::getYears();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Salary Management</title>
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
              <a href="user.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-user-shield mr-3 h-6 w-6"></i>
                User Management
              </a>
              <a href="salary.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-white bg-primary-800">
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
          <!-- Alert messages -->
          <?php if (!empty($success_message)): ?>
            <div class="rounded-md bg-green-50 p-4 mb-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-green-800"><?php echo $success_message; ?></p>
                </div>
                <div class="ml-auto pl-3">
                  <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">
                      <span class="sr-only">Dismiss</span>
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($error_message)): ?>
            <div class="rounded-md bg-red-50 p-4 mb-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-red-800"><?php echo $error_message; ?></p>
                </div>
                <div class="ml-auto pl-3">
                  <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">
                      <span class="sr-only">Dismiss</span>
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          <?php endif; ?>

          <!-- Page header -->
          <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Salary Management</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 mr-2" onclick="openModal('addPaymentModal')">
                <i class="fas fa-plus mr-2 -ml-1 h-5 w-5"></i>
                Add Salary
              </button>
              <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" onclick="openModal('advanceRequestModal')">
                <i class="fas fa-hand-holding-usd mr-2 -ml-1 h-5 w-5"></i>
                Advance Request
              </button>
            </div>
          </div>

          <!-- Tab navigation -->
          <div class="mt-4 border-b border-gray-200">
            <div class="sm:flex sm:items-baseline">
              <div class="mt-4 sm:mt-0 sm:ml-0">
                <nav class="-mb-px flex space-x-8">
                  <a href="#" id="salary-tab" class="text-primary-600 border-primary-500 whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm" onclick="showTab('salary-content'); return false;">
                    Salary Payments
                  </a>
                  <a href="#" id="advance-tab" class="text-gray-500 border-transparent whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm hover:text-gray-700 hover:border-gray-300" onclick="showTab('advance-content'); return false;">
                    Advance Requests
                  </a>
                </nav>
              </div>
            </div>
          </div>

          <!-- Salary tab content -->
          <div id="salary-content" class="tab-content">
            <!-- Salary Summary Cards -->
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
              <!-- Card 1 -->
              <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                      <i class="fas fa-money-bill-wave text-green-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                      <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                          Total Salary (<?php echo $currentMonth; ?>)
                        </dt>
                        <dd>
                          <div class="text-lg font-medium text-gray-900">
                            <?php echo SalaryModel::formatMoney($totalSalary); ?>
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
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                      <i class="fas fa-hand-holding-usd text-yellow-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                      <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                          Total Advances
                        </dt>
                        <dd>
                          <div class="text-lg font-medium text-gray-900">
                            <?php echo SalaryModel::formatMoney($totalAdvance); ?>
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
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                      <i class="fas fa-calculator text-blue-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                      <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                          Net Payable
                        </dt>
                        <dd>
                          <div class="text-lg font-medium text-gray-900">
                            <?php echo SalaryModel::formatMoney($totalNetPayable); ?>
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
                    <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                      <i class="fas fa-calendar-check text-red-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                      <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                          Payment Due
                        </dt>
                        <dd>
                          <div class="text-lg font-medium text-gray-900">
                            <?php echo $paymentDue; ?> Employees
                          </div>
                        </dd>
                      </dl>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Salary filters and search -->
            <div class="mt-6 bg-white shadow rounded-lg p-4">
              <form action="salary.php" method="GET">
                <div class="flex flex-col md:flex-row justify-between gap-4">
                  <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                      </div>
                      <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="Search employee...">
                    </div>
                    <div>
                      <select name="month" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="">All Months</option>
                        <?php foreach ($months as $month): ?>
                          <option value="<?php echo $month; ?>" <?php echo (isset($_GET['month']) && $_GET['month'] == $month) ? 'selected' : ''; ?>>
                            <?php echo $month; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div>
                      <select name="year" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="">All Years</option>
                        <?php foreach ($years as $year): ?>
                          <option value="<?php echo $year; ?>" <?php echo (isset($_GET['year']) && $_GET['year'] == $year) ? 'selected' : ''; ?>>
                            <?php echo $year; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div>
                      <select name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="">Payment Status</option>
                        <option value="paid" <?php echo (isset($_GET['status']) && $_GET['status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                        <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="partial" <?php echo (isset($_GET['status']) && $_GET['status'] == 'partial') ? 'selected' : ''; ?>>Partial</option>
                      </select>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      <i class="fas fa-filter mr-2 h-5 w-5 text-gray-500"></i>
                      Filter
                    </button>
                    <a href="export-salaries.php<?php echo !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      <i class="fas fa-download mr-2 h-5 w-5 text-gray-500"></i>
                      Export
                    </a>
                  </div>
                </div>
              </form>
            </div>

            <!-- Salary Table -->
            <div class="mt-6">
              <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                  <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                      <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                          <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Employee
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Month
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Salary
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Advance
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Net Payable
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
                          <?php if (empty($salaries)): ?>
                            <tr>
                              <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                No salary records found.
                              </td>
                            </tr>
                          <?php else: ?>
                            <?php foreach ($salaries as $salary): ?>
                              <?php
                              // Find related advance records for this user
                              $userAdvance = 0;
                              foreach ($advances as $advance) {
                                if (
                                  $advance['user_id'] == $salary['user_id'] &&
                                  ($advance['status'] == 'approved' || $advance['status'] == 'partially_paid')
                                ) {
                                  $userAdvance += $advance['remaining_amount'];
                                }
                              }

                              // Calculate net payable
                              $netPayable = ($salary['amount'] + $salary['bonus']) - $userAdvance;
                              ?>
                              <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                  <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                      <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
                                    </div>
                                    <div class="ml-4">
                                      <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($salary['user_name']); ?>
                                      </div>
                                      <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($salary['user_role']); ?>
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                  <?php echo htmlspecialchars($salary['month'] . ' ' . $salary['year']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                  <?php echo !empty($salary['payment_date']) ? date('M j, Y', strtotime($salary['payment_date'])) : '--'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                  <?php echo SalaryModel::formatMoney($salary['amount'] + $salary['bonus']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                  <?php echo SalaryModel::formatMoney($userAdvance); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                  <?php echo SalaryModel::formatMoney($netPayable); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                  <?php if ($salary['status'] == 'paid'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                      Paid
                                    </span>
                                  <?php elseif ($salary['status'] == 'pending'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                      Pending
                                    </span>
                                  <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                      <?php echo ucfirst($salary['status']); ?>
                                    </span>
                                  <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                  <a href="?edit_salary=<?php echo $salary['id']; ?>" class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-edit"></i></a>
                                  <a href="salary-receipt.php?id=<?php echo $salary['id']; ?>" class="text-gray-600 hover:text-gray-900 mr-3"><i class="fas fa-print"></i></a>
                                  <a href="#" onclick="confirmDelete('<?php echo $salary['id']; ?>', 'salary'); return false;" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Advance Requests tab content -->
          <div id="advance-content" class="tab-content hidden">
            <!-- Advance filters -->
            <div class="mt-6 bg-white shadow rounded-lg p-4">
              <form action="salary.php" method="GET">
                <input type="hidden" name="tab" value="advance">
                <div class="flex flex-col md:flex-row justify-between gap-4">
                  <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                      </div>
                      <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="Search employee...">
                    </div>
                    <div>
                      <select name="advance_status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo (isset($_GET['advance_status']) && $_GET['advance_status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo (isset($_GET['advance_status']) && $_GET['advance_status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo (isset($_GET['advance_status']) && $_GET['advance_status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                        <option value="partially_paid" <?php echo (isset($_GET['advance_status']) && $_GET['advance_status'] == 'partially_paid') ? 'selected' : ''; ?>>Partially Paid</option>
                        <option value="paid" <?php echo (isset($_GET['advance_status']) && $_GET['advance_status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                      </select>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      <i class="fas fa-filter mr-2 h-5 w-5 text-gray-500"></i>
                      Filter
                    </button>
                    <a href="export-advances.php<?php echo !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''; ?>" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      <i class="fas fa-download mr-2 h-5 w-5 text-gray-500"></i>
                      Export
                    </a>
                  </div>
                </div>
              </form>
            </div>

            <!-- Advance Requests Table -->
            <div class="mt-6">
              <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                  <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                      <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                          <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Employee
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Request Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Amount
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Remaining
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Installments
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
                          <?php if (empty($advances)): ?>
                            <tr>
                              <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                No advance requests found.
                              </td>
                            </tr>
                          <?php else: ?>
                            <?php foreach ($advances as $advance): ?>
                              <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                  <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                      <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
                                    </div>
                                    <div class="ml-4">
                                      <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($advance['user_name']); ?>
                                      </div>
                                      <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($advance['user_email']); ?>
                                      </div>
                                    </div>
                                  </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                  <?php echo date('M j, Y', strtotime($advance['request_date'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                  <?php echo SalaryModel::formatMoney($advance['amount']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                  <?php echo SalaryModel::formatMoney($advance['remaining_amount']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                  <?php echo $advance['installments']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                  <?php if ($advance['status'] == 'pending'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                      Pending
                                    </span>
                                  <?php elseif ($advance['status'] == 'approved'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                      Approved
                                    </span>
                                  <?php elseif ($advance['status'] == 'rejected'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                      Rejected
                                    </span>
                                  <?php elseif ($advance['status'] == 'partially_paid'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                      Partially Paid
                                    </span>
                                  <?php elseif ($advance['status'] == 'paid'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                      Paid
                                    </span>
                                  <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                  <a href="?view_advance=<?php echo $advance['id']; ?>&tab=advance" class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-eye"></i></a>

                                  <?php if ($advance['status'] == 'pending'): ?>
                                    <a href="?advance_id=<?php echo $advance['id']; ?>&status=approved&tab=advance" class="text-green-600 hover:text-green-900 mr-3"><i class="fas fa-check-circle"></i></a>
                                    <a href="?advance_id=<?php echo $advance['id']; ?>&status=rejected&tab=advance" class="text-red-600 hover:text-red-900 mr-3"><i class="fas fa-times-circle"></i></a>
                                  <?php endif; ?>

                                  <?php if ($advance['status'] == 'approved' || $advance['status'] == 'partially_paid'): ?>
                                    <a href="#" onclick="openPaymentModal(<?php echo $advance['id']; ?>, <?php echo $advance['remaining_amount']; ?>); return false;" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-credit-card"></i></a>
                                  <?php endif; ?>

                                  <?php if ($advance['status'] == 'pending'): ?>
                                    <a href="#" onclick="confirmDelete('<?php echo $advance['id']; ?>', 'advance'); return false;" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></a>
                                  <?php endif; ?>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
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
          <a href="index.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-home mr-4 h-6 w-6"></i>
            Dashboard
          </a>
          <a href="employee.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-users mr-4 h-6 w-6"></i>
            Employee Management
          </a>
          <a href="user.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
            <i class="fas fa-user-shield mr-4 h-6 w-6"></i>
            User Management
          </a>
          <a href="salary.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md bg-primary-800 text-white">
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
              <img class="inline-block h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
            </div>
            <div class="ml-3">
              <p class="text-base font-medium text-white">Admin User</p>
              <p class="text-sm font-medium text-primary-200 group-hover:text-primary-100">View profile</p>
            </div>
          </div>
        </a>
      </div>
    </div>
    <div class="flex-shrink-0 w-14"></div>
  </div>

  <!-- Add Payment Modal -->
  <div id="addPaymentModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="addPaymentModalOverlay"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" onclick="closeModal('addPaymentModal')">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              <?php echo $editSalary ? 'Edit Payment' : 'Add New Payment'; ?>
            </h3>
            <div class="mt-4">
              <form action="salary.php" method="POST">
                <input type="hidden" name="action" value="<?php echo $editSalary ? 'update_salary' : 'add_salary'; ?>">
                <?php if ($editSalary): ?>
                  <input type="hidden" name="salary_id" value="<?php echo $editSalary['id']; ?>">
                <?php endif; ?>

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-6">
                    <label for="employee" class="block text-sm font-medium text-gray-700">Employee</label>
                    <div class="mt-1">
                      <select id="employee" name="employee" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                        <option value="">Select Employee</option>
                        <?php foreach ($users as $user): ?>
                          <option value="<?php echo $user['id']; ?>" <?php echo (($editSalary && $editSalary['user_id'] == $user['id']) || (isset($_GET['user_id']) && $_GET['user_id'] == $user['id'])) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['name'] . ' - ' . ucfirst($user['role'])); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                    <div class="mt-1">
                      <select id="month" name="month" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                        <option value="">Select Month</option>
                        <?php foreach ($months as $month): ?>
                          <option value="<?php echo $month; ?>" <?php echo ($editSalary && $editSalary['month'] == $month) ? 'selected' : ''; ?>>
                            <?php echo $month; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                    <div class="mt-1">
                      <select id="year" name="year" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                        <?php foreach ($years as $year): ?>
                          <option value="<?php echo $year; ?>" <?php echo ($editSalary && $editSalary['year'] == $year) ? 'selected' : ($year == date('Y') ? 'selected' : ''); ?>>
                            <?php echo $year; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="payment-date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                    <div class="mt-1">
                      <input type="date" name="payment_date" id="payment-date" value="<?php echo $editSalary ? $editSalary['payment_date'] : date('Y-m-d'); ?>" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <div class="mt-1">
                      <select id="status" name="status" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                        <option value="pending" <?php echo ($editSalary && $editSalary['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="paid" <?php echo ($editSalary && $editSalary['status'] == 'paid') ? 'selected' : ''; ?>>Paid</option>
                        <option value="partial" <?php echo ($editSalary && $editSalary['status'] == 'partial') ? 'selected' : ''; ?>>Partial</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="salary" class="block text-sm font-medium text-gray-700">Salary Amount</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input type="number" step="0.01" name="salary" id="salary" value="<?php echo $editSalary ? $editSalary['amount'] : ''; ?>" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00" required>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="bonus" class="block text-sm font-medium text-gray-700">Bonus (Optional)</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input type="number" step="0.01" name="bonus" id="bonus" value="<?php echo $editSalary ? $editSalary['bonus'] : '0'; ?>" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <div class="mt-1">
                      <select id="payment_method" name="payment_method" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="bank transfer" <?php echo ($editSalary && $editSalary['payment_method'] == 'bank transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                        <option value="cash" <?php echo ($editSalary && $editSalary['payment_method'] == 'cash') ? 'selected' : ''; ?>>Cash</option>
                        <option value="check" <?php echo ($editSalary && $editSalary['payment_method'] == 'check') ? 'selected' : ''; ?>>Check</option>
                        <option value="other" <?php echo ($editSalary && $editSalary['payment_method'] == 'other') ? 'selected' : ''; ?>>Other</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <div class="mt-1">
                      <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"><?php echo $editSalary ? $editSalary['notes'] : ''; ?></textarea>
                    </div>
                  </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                  <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm">
                    <?php echo $editSalary ? 'Update' : 'Save'; ?>
                  </button>
                  <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:col-start-1 sm:text-sm" onclick="closeModal('addPaymentModal')">
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

  <!-- Advance Request Modal -->
  <div id="advanceRequestModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="advanceRequestModalOverlay"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" onclick="closeModal('advanceRequestModal')">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              New Advance Salary Request
            </h3>
            <div class="mt-4">
              <form action="salary.php" method="POST">
                <input type="hidden" name="action" value="add_advance">

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-6">
                    <label for="adv_employee" class="block text-sm font-medium text-gray-700">Employee</label>
                    <div class="mt-1">
                      <select id="adv_employee" name="employee" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" required>
                        <option value="">Select Employee</option>
                        <?php foreach ($users as $user): ?>
                          <option value="<?php echo $user['id']; ?>" <?php echo (isset($_GET['user_id']) && $_GET['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['name'] . ' - ' . ucfirst($user['role'])); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input type="number" step="0.01" name="amount" id="amount" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00" required>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="request_date" class="block text-sm font-medium text-gray-700">Request Date</label>
                    <div class="mt-1">
                      <input type="date" name="request_date" id="request_date" value="<?php echo date('Y-m-d'); ?>" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <div class="mt-1">
                      <textarea id="reason" name="reason" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" required></textarea>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="repayment_method" class="block text-sm font-medium text-gray-700">Repayment Method</label>
                    <div class="mt-1">
                      <select id="repayment_method" name="repayment_method" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="salary deduction">Salary Deduction</option>
                        <option value="installment">Installment</option>
                        <option value="one time">One Time Payment</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="installments" class="block text-sm font-medium text-gray-700">Installments</label>
                    <div class="mt-1">
                      <input type="number" min="1" name="installments" id="installments" value="1" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label for="adv_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <div class="mt-1">
                      <textarea id="adv_notes" name="notes" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>
                  </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                  <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm">
                    Submit Request
                  </button>
                  <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:col-start-1 sm:text-sm" onclick="closeModal('advanceRequestModal')">
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

  <!-- Process Payment Modal -->
  <div id="processPaymentModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="processPaymentModalOverlay"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" onclick="closeModal('processPaymentModal')">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              Process Advance Payment
            </h3>
            <div class="mt-4">
              <form action="salary.php" method="POST">
                <input type="hidden" name="action" value="process_payment">
                <input type="hidden" name="advance_id" id="advance_id" value="">
                <input type="hidden" name="tab" value="advance">

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-6">
                    <label for="payment_amount" class="block text-sm font-medium text-gray-700">Payment Amount</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">$</span>
                      </div>
                      <input type="number" step="0.01" name="payment_amount" id="payment_amount" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00" required>
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <span class="text-gray-500 text-xs" id="max_amount"></span>
                      </div>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                    <div class="mt-1">
                      <input type="date" name="payment_date" id="payment_date" value="<?php echo date('Y-m-d'); ?>" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <div class="mt-1">
                      <select id="payment_method" name="payment_method" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="bank transfer">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                        <option value="salary deduction">Salary Deduction</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label for="payment_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <div class="mt-1">
                      <textarea id="payment_notes" name="notes" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>
                  </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                  <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm">
                    Process Payment
                  </button>
                  <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:col-start-1 sm:text-sm" onclick="closeModal('processPaymentModal')">
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

  <!-- View Advance Modal -->
  <?php if ($viewAdvance): ?>
    <div id="viewAdvanceModal" class="fixed z-10 inset-0 overflow-y-auto">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
          <div class="absolute top-0 right-0 pt-4 pr-4">
            <a href="salary.php?tab=advance" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
              <span class="sr-only">Close</span>
              <i class="fas fa-times"></i>
            </a>
          </div>
          <div>
            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                Advance Salary Details
              </h3>

              <div class="mt-4">
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                  <div class="grid grid-cols-2 gap-4">
                    <div>
                      <p class="text-sm text-gray-500">Employee</p>
                      <p class="font-medium"><?php echo htmlspecialchars($viewAdvance['user_name']); ?></p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Email</p>
                      <p class="font-medium"><?php echo htmlspecialchars($viewAdvance['user_email']); ?></p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Request Date</p>
                      <p class="font-medium"><?php echo date('M j, Y', strtotime($viewAdvance['request_date'])); ?></p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Status</p>
                      <p class="font-medium">
                        <?php if ($viewAdvance['status'] == 'pending'): ?>
                          <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Pending
                          </span>
                        <?php elseif ($viewAdvance['status'] == 'approved'): ?>
                          <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Approved
                          </span>
                        <?php elseif ($viewAdvance['status'] == 'rejected'): ?>
                          <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Rejected
                          </span>
                        <?php elseif ($viewAdvance['status'] == 'partially_paid'): ?>
                          <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                            Partially Paid
                          </span>
                        <?php elseif ($viewAdvance['status'] == 'paid'): ?>
                          <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Paid
                          </span>
                        <?php endif; ?>
                      </p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Amount</p>
                      <p class="font-medium"><?php echo SalaryModel::formatMoney($viewAdvance['amount']); ?></p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Remaining</p>
                      <p class="font-medium"><?php echo SalaryModel::formatMoney($viewAdvance['remaining_amount']); ?></p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Repayment Method</p>
                      <p class="font-medium"><?php echo ucfirst($viewAdvance['repayment_method']); ?></p>
                    </div>
                    <div>
                      <p class="text-sm text-gray-500">Installments</p>
                      <p class="font-medium"><?php echo $viewAdvance['installments']; ?></p>
                    </div>
                  </div>
                  <div class="mt-3">
                    <p class="text-sm text-gray-500">Reason</p>
                    <p class="font-medium"><?php echo nl2br(htmlspecialchars($viewAdvance['reason'])); ?></p>
                  </div>
                  <?php if (!empty($viewAdvance['notes'])): ?>
                    <div class="mt-3">
                      <p class="text-sm text-gray-500">Notes</p>
                      <p class="font-medium"><?php echo nl2br(htmlspecialchars($viewAdvance['notes'])); ?></p>
                    </div>
                  <?php endif; ?>
                  <?php if ($viewAdvance['status'] == 'approved' || $viewAdvance['status'] == 'rejected'): ?>
                    <div class="mt-3">
                      <p class="text-sm text-gray-500">Approved/Rejected By</p>
                      <p class="font-medium"><?php echo htmlspecialchars($viewAdvance['approver_name']); ?> on <?php echo date('M j, Y', strtotime($viewAdvance['approval_date'])); ?></p>
                    </div>
                  <?php endif; ?>
                </div>

                <?php if (!empty($viewAdvance['payments'])): ?>
                  <h4 class="text-md font-medium text-gray-900 mt-4 mb-2">Payment History</h4>
                  <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                          <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($viewAdvance['payments'] as $payment): ?>
                          <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo SalaryModel::formatMoney($payment['amount']); ?></td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo ucfirst($payment['payment_method']); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>

                <div class="mt-5 sm:mt-6 flex justify-end">
                  <?php if ($viewAdvance['status'] == 'pending'): ?>
                    <a href="?advance_id=<?php echo $viewAdvance['id']; ?>&status=approved&tab=advance" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mr-2">
                      <i class="fas fa-check mr-2"></i> Approve
                    </a>
                    <a href="?advance_id=<?php echo $viewAdvance['id']; ?>&status=rejected&tab=advance" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 mr-4">
                      <i class="fas fa-times mr-2"></i> Reject
                    </a>
                  <?php endif; ?>

                  <?php if ($viewAdvance['status'] == 'approved' || $viewAdvance['status'] == 'partially_paid'): ?>
                    <button type="button" onclick="openPaymentModal(<?php echo $viewAdvance['id']; ?>, <?php echo $viewAdvance['remaining_amount']; ?>); return false;" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 mr-2">
                      <i class="fas fa-credit-card mr-2"></i> Process Payment
                    </button>
                  <?php endif; ?>

                  <a href="salary.php?tab=advance" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Delete Confirmation Dialog -->
  <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="deleteModalOverlay"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
        <div>
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <i class="fas fa-exclamation-triangle text-red-600 h-6 w-6"></i>
          </div>
          <div class="mt-3 text-center sm:mt-5">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="deleteModalTitle">
              Delete Confirmation
            </h3>
            <div class="mt-2">
              <p class="text-sm text-gray-500" id="deleteModalText">
                Are you sure you want to delete this record? This action cannot be undone.
              </p>
            </div>
          </div>
        </div>
        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
          <a href="#" id="confirmDeleteBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
            Delete
          </a>
          <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:col-start-1 sm:text-sm" onclick="closeModal('deleteModal')">
            Cancel
          </button>
        </div>
      </div>
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

    // Tab functionality
    function showTab(tabId) {
      const tabs = document.querySelectorAll('.tab-content');
      tabs.forEach(tab => {
        tab.classList.add('hidden');
      });

      document.getElementById(tabId).classList.remove('hidden');

      // Update active tab styling
      document.querySelectorAll('a[id$="-tab"]').forEach(tabLink => {
        tabLink.classList.remove('text-primary-600', 'border-primary-500');
        tabLink.classList.add('text-gray-500', 'border-transparent');
      });

      if (tabId === 'salary-content') {
        document.getElementById('salary-tab').classList.remove('text-gray-500', 'border-transparent');
        document.getElementById('salary-tab').classList.add('text-primary-600', 'border-primary-500');
      } else if (tabId === 'advance-content') {
        document.getElementById('advance-tab').classList.remove('text-gray-500', 'border-transparent');
        document.getElementById('advance-tab').classList.add('text-primary-600', 'border-primary-500');
      }
    }

    // Check if there's a tab parameter in the URL
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam === 'advance') {
      showTab('advance-content');
    } else {
      showTab('salary-content');
    }

    // Modal functions
    function openModal(modalId) {
      document.getElementById(modalId).classList.remove('hidden');

      // Auto-trigger salary calculation if it's the payment modal
      if (modalId === 'addPaymentModal') {
        calculateNetPayable();
      }
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
    }

    // Process payment modal
    function openPaymentModal(advanceId, remainingAmount) {
      document.getElementById('advance_id').value = advanceId;
      document.getElementById('payment_amount').value = remainingAmount;
      document.getElementById('payment_amount').max = remainingAmount;
      document.getElementById('max_amount').textContent = 'max: ' + remainingAmount.toFixed(2);
      openModal('processPaymentModal');
    }

    // Delete confirmation
    function confirmDelete(id, type) {
      const modal = document.getElementById('deleteModal');
      const confirmBtn = document.getElementById('confirmDeleteBtn');
      const modalTitle = document.getElementById('deleteModalTitle');
      const modalText = document.getElementById('deleteModalText');

      if (type === 'salary') {
        modalTitle.textContent = 'Delete Salary Record';
        modalText.textContent = 'Are you sure you want to delete this salary record? This action cannot be undone.';
        confirmBtn.href = 'salary.php?delete_salary=' + id;
      } else if (type === 'advance') {
        modalTitle.textContent = 'Delete Advance Request';
        modalText.textContent = 'Are you sure you want to delete this advance request? This action cannot be undone.';
        confirmBtn.href = 'salary.php?delete_advance=' + id + '&tab=advance';
      }

      modal.classList.remove('hidden');
    }

    // Calculate net payable
    function calculateNetPayable() {
      const salaryInput = document.getElementById('salary');
      const bonusInput = document.getElementById('bonus');
      const netPayableInput = document.getElementById('net-payable');

      if (salaryInput && bonusInput && netPayableInput) {
        const salary = parseFloat(salaryInput.value) || 0;
        const bonus = parseFloat(bonusInput.value) || 0;
        netPayableInput.value = (salary + bonus).toFixed(2);
      }
    }

    // Add event listeners for salary calculation
    const salaryInput = document.getElementById('salary');
    const bonusInput = document.getElementById('bonus');
    if (salaryInput && bonusInput) {
      salaryInput.addEventListener('input', calculateNetPayable);
      bonusInput.addEventListener('input', calculateNetPayable);
    }

    // Close alert messages
    document.querySelectorAll('.bg-green-50 button, .bg-red-50 button').forEach(button => {
      button.addEventListener('click', function() {
        this.closest('.rounded-md').remove();
      });
    });
  </script>
</body>

</html>