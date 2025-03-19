<?php
// Include admin authentication
require_once "../config/admin-auth.php";
require_once "includes/SalaryModel.php";

// Initialize the salary model for dashboard stats
$salaryModel = new SalaryModel();

// Get summary statistics
// Get current month/year
$currentMonth = date('F');
$currentYear = date('Y');

// Get salary statistics
$salaryStats = $salaryModel->getSalaryStatsByYear($currentYear);
$advanceStats = $salaryModel->getAdvanceSalaryStats();

// Calculate monthly salary total
$monthlySalaryTotal = 0;
if ($salaryStats) {
  $monthlySalaryTotal = $salaryStats['total_paid'] ?? 0;
}

// Calculate pending tasks
// This would typically come from a TaskModel, but for now we'll set a placeholder
$pendingTasks = 12;

// Calculate total employees
// This would typically come from a EmployeeModel, but for now we'll set a placeholder
$totalEmployees = 24;

// Calculate total expenses
// This would typically come from a ExpenseModel, but for now we'll set a placeholder
$totalExpenses = 4200;

// Get recent activities
// Ideally this would be from an activity log table, for now using placeholders
$recentActivities = [
  [
    'type' => 'Salary Payment',
    'user' => 'John Smith',
    'date' => 'March 10, 2025',
    'status' => 'Completed',
    'status_class' => 'bg-green-100 text-green-800'
  ],
  [
    'type' => 'Advance Request',
    'user' => 'Sarah Johnson',
    'date' => 'March 9, 2025',
    'status' => 'Pending',
    'status_class' => 'bg-yellow-100 text-yellow-800'
  ],
  [
    'type' => 'Expense Report',
    'user' => 'Michael Brown',
    'date' => 'March 8, 2025',
    'status' => 'In Review',
    'status_class' => 'bg-blue-100 text-blue-800'
  ],
  [
    'type' => 'New Employee Added',
    'user' => 'Jessica Davis',
    'date' => 'March 7, 2025',
    'status' => 'Completed',
    'status_class' => 'bg-green-100 text-green-800'
  ],
  [
    'type' => 'Contract Renewal',
    'user' => 'Robert Wilson',
    'date' => 'March 6, 2025',
    'status' => 'Rejected',
    'status_class' => 'bg-red-100 text-red-800'
  ]
];

// Get pending salary advances needing approval
$pendingAdvances = [];
$advanceFilters = [
  'status' => 'pending'
];
$pendingAdvances = $salaryModel->getAllAdvanceSalaries($advanceFilters);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Admin Dashboard</title>
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
              <a href="index.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-white bg-primary-800">
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
                  <p class="text-base font-medium text-white"><?php echo htmlspecialchars($_SESSION["name"]); ?></p>
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
                <div class="relative inline-block">
                  <button id="notificationButton" class="relative p-1 ml-3 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-bell h-6 w-6"></i>
                    <?php if (count($pendingAdvances) > 0): ?>
                      <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-1 ring-white"></span>
                    <?php endif; ?>
                  </button>
                  <div id="notificationDropdown" class="hidden origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50" role="menu">
                    <div class="px-4 py-2 border-b border-gray-200">
                      <h3 class="text-sm font-medium text-gray-700">Notifications</h3>
                    </div>
                    <?php if (count($pendingAdvances) > 0): ?>
                      <?php foreach ($pendingAdvances as $advance): ?>
                        <a href="salary.php?view_advance=<?php echo $advance['id']; ?>&tab=advance" class="block px-4 py-3 border-b border-gray-200 hover:bg-gray-50">
                          <div class="flex items-start">
                            <div class="flex-shrink-0 pt-0.5">
                              <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/50" alt="">
                            </div>
                            <div class="ml-3 w-0 flex-1">
                              <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($advance['user_name']); ?></p>
                              <p class="text-sm text-gray-500">Advance request for <?php echo SalaryModel::formatMoney($advance['amount']); ?></p>
                              <p class="text-xs text-gray-400 mt-1"><?php echo date('M j, Y', strtotime($advance['request_date'])); ?></p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex">
                              <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
                              </span>
                            </div>
                          </div>
                        </a>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <div class="px-4 py-3 text-sm text-gray-500">
                        No new notifications
                      </div>
                    <?php endif; ?>
                    <a href="#" class="block text-center px-4 py-2 border-t border-gray-200 text-sm text-primary-500 hover:text-primary-700">
                      View all notifications
                    </a>
                  </div>
                </div>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900">Dashboard</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <div class="relative inline-block text-left mr-2">
                <button type="button" id="quickAddButton" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-plus mr-2 -ml-1 h-5 w-5"></i>
                  Quick Add
                  <i class="fas fa-chevron-down ml-1 text-xs"></i>
                </button>
                <div id="quickAddDropdown" class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                  <div class="py-1" role="menu">
                    <a href="employee.php?add=new" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                      <i class="fas fa-user-plus mr-2 text-primary-500"></i>
                      New Employee
                    </a>
                    <a href="task.php?add=new" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                      <i class="fas fa-clipboard-list mr-2 text-primary-500"></i>
                      New Task
                    </a>
                    <a href="expense.php?add=new" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                      <i class="fas fa-receipt mr-2 text-primary-500"></i>
                      New Expense
                    </a>
                    <a href="salary.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                      <i class="fas fa-money-check-alt mr-2 text-primary-500"></i>
                      Process Salary
                    </a>
                  </div>
                </div>
              </div>
              <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-sync-alt mr-2 -ml-1 h-5 w-5"></i>
                Refresh
              </button>
            </div>
          </div>

          <!-- Welcome Banner -->
          <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
              <div>
                <h2 class="text-lg leading-6 font-medium text-gray-900">Welcome back, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h2>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Here's an overview of your system's current state.</p>
              </div>
              <div class="hidden sm:block">
                <span class="text-sm font-medium text-gray-500"><?php echo date('F Y'); ?></span>
              </div>
            </div>
          </div>

          <!-- Dashboard content -->
          <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Card 1 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                    <i class="fas fa-users text-primary-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Employees
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo $totalEmployees = $salaryModel->getTotalUsers(); ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                  <a href="employee.php" class="font-medium text-primary-600 hover:text-primary-900">
                    View all
                  </a>
                </div>
              </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-money-bill-wave text-green-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Monthly Salary
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo SalaryModel::formatMoney($monthlySalaryTotal); ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                  <a href="salary.php" class="font-medium text-primary-600 hover:text-primary-900">
                    View details
                  </a>
                </div>
              </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <i class="fas fa-file-invoice-dollar text-red-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Expenses
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo SalaryModel::formatMoney($totalExpenses); ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                  <a href="expense.php" class="font-medium text-primary-600 hover:text-primary-900">
                    View details
                  </a>
                </div>
              </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-tasks text-yellow-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Pending Tasks
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          <?php echo $pendingTasks; ?>
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                  <a href="task.php" class="font-medium text-primary-600 hover:text-primary-900">
                    View all
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Pending Approvals Section -->
          <?php if (count($pendingAdvances) > 0): ?>
            <div class="mt-8">
              <h2 class="text-lg leading-6 font-medium text-gray-900 flex items-center">
                <i class="fas fa-exclamation-circle text-yellow-500 mr-2"></i>
                Pending Approvals
              </h2>
              <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                  <?php foreach ($pendingAdvances as $advance): ?>
                    <li>
                      <a href="salary.php?view_advance=<?php echo $advance['id']; ?>&tab=advance" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                          <div class="flex items-center justify-between">
                            <div class="flex items-center">
                              <div class="flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/40" alt="">
                              </div>
                              <div class="ml-4">
                                <p class="text-sm font-medium text-primary-600 truncate">
                                  <?php echo htmlspecialchars($advance['user_name']); ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                  Advance Request for <?php echo SalaryModel::formatMoney($advance['amount']); ?>
                                </p>
                              </div>
                            </div>
                            <div class="ml-2 flex-shrink-0 flex">
                              <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending Approval
                              </p>
                            </div>
                          </div>
                          <div class="mt-2 sm:flex sm:justify-between">
                            <div class="sm:flex">
                              <p class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-calendar-alt flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"></i>
                                Requested on <?php echo date('M j, Y', strtotime($advance['request_date'])); ?>
                              </p>
                            </div>
                            <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                              <i class="fas fa-arrow-right flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400"></i>
                              <p>
                                View details
                              </p>
                            </div>
                          </div>
                        </div>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          <?php endif; ?>

          <!-- Recent activity table -->
          <div class="mt-8">
            <h2 class="text-lg leading-6 font-medium text-gray-900">Recent Activities</h2>
            <div class="mt-4 flex flex-col">
              <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                  <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                      <thead class="bg-gray-50">
                        <tr>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Activity
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                          </th>
                          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                          </th>
                        </tr>
                      </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($recentActivities as $activity): ?>
                          <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                              <?php echo $activity['type']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              <?php echo $activity['user']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              <?php echo $activity['date']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $activity['status_class']; ?>">
                                <?php echo $activity['status']; ?>
                              </span>
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

          <!-- System Stats -->
          <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2">
            <!-- Salary Distribution -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Salary Distribution</h3>
                <div class="mt-2">
                  <div class="bg-gray-50 p-4 rounded-md">
                    <div class="flex justify-between items-center mb-2">
                      <div class="text-sm text-gray-500">Total Paid</div>
                      <div class="text-sm font-medium"><?php echo SalaryModel::formatMoney($salaryStats['paid_amount'] ?? 0); ?></div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                      <div class="bg-green-600 h-2.5 rounded-full" style="width: <?php echo ((($salaryStats['paid_amount'] ?? 0) / ($monthlySalaryTotal ?: 1)) * 100); ?>%"></div>
                    </div>

                    <div class="flex justify-between items-center mt-4 mb-2">
                      <div class="text-sm text-gray-500">Pending</div>
                      <div class="text-sm font-medium"><?php echo SalaryModel::formatMoney($salaryStats['pending_amount'] ?? 0); ?></div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                      <div class="bg-yellow-500 h-2.5 rounded-full" style="width: <?php echo ((($salaryStats['pending_amount'] ?? 0) / ($monthlySalaryTotal ?: 1)) * 100); ?>%"></div>
                    </div>

                    <div class="flex justify-between items-center mt-4 mb-2">
                      <div class="text-sm text-gray-500">Bonuses</div>
                      <div class="text-sm font-medium"><?php echo SalaryModel::formatMoney($salaryStats['total_bonus'] ?? 0); ?></div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                      <div class="bg-blue-500 h-2.5 rounded-full" style="width: <?php echo ((($salaryStats['total_bonus'] ?? 0) / ($monthlySalaryTotal ?: 1)) * 100); ?>%"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-4 sm:px-6">
                <div class="text-sm">
                  <a href="reports.php?type=salary" class="font-medium text-primary-600 hover:text-primary-900">
                    View full salary report <span aria-hidden="true">&rarr;</span>
                  </a>
                </div>
              </div>
            </div>

            <!-- Advance Request Stats -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Advance Requests</h3>
                <div class="mt-2">
                  <div class="grid grid-cols-2 gap-4">
                    <div class="bg-yellow-50 p-4 rounded-md">
                      <div class="text-lg font-bold text-yellow-700"><?php echo $advanceStats['pending_count'] ?? 0; ?></div>
                      <div class="text-sm text-yellow-600">Pending Requests</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-md">
                      <div class="text-lg font-bold text-green-700"><?php echo $advanceStats['approved_count'] ?? 0; ?></div>
                      <div class="text-sm text-green-600">Approved</div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-md">
                      <div class="text-lg font-bold text-blue-700"><?php echo SalaryModel::formatMoney($advanceStats['total_amount'] ?? 0, false); ?></div>
                      <div class="text-sm text-blue-600">Total Amount</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-md">
                      <div class="text-lg font-bold text-red-700"><?php echo SalaryModel::formatMoney($advanceStats['total_remaining'] ?? 0, false); ?></div>
                      <div class="text-sm text-red-600">Remaining</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="bg-gray-50 px-4 py-4 sm:px-6">
                <div class="text-sm">
                  <a href="salary.php?tab=advance" class="font-medium text-primary-600 hover:text-primary-900">
                    View all advance requests <span aria-hidden="true">&rarr;</span>
                  </a>
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
      <div class="absolute top-0 right-0 pt-2">
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
          <a href="index.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md bg-primary-800 text-white">
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

    // Quick Add dropdown toggle
    const quickAddButton = document.getElementById('quickAddButton');
    const quickAddDropdown = document.getElementById('quickAddDropdown');

    if (quickAddButton) {
      quickAddButton.addEventListener('click', (e) => {
        e.stopPropagation();
        quickAddDropdown.classList.toggle('hidden');
      });
    }

    // Notification dropdown toggle
    const notificationButton = document.getElementById('notificationButton');
    const notificationDropdown = document.getElementById('notificationDropdown');

    if (notificationButton) {
      notificationButton.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle('hidden');
      });
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', (event) => {
      if (userMenuButton && userDropdown && !userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
        userDropdown.classList.add('hidden');
      }

      if (quickAddButton && quickAddDropdown && !quickAddButton.contains(event.target) && !quickAddDropdown.contains(event.target)) {
        quickAddDropdown.classList.add('hidden');
      }

      if (notificationButton && notificationDropdown && !notificationButton.contains(event.target) && !notificationDropdown.contains(event.target)) {
        notificationDropdown.classList.add('hidden');
      }
    });

    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebar = document.getElementById('mobile-sidebar');
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