<?php
// Include user authentication
require_once "../config/user-auth.php";
require_once "../admin/includes/SalaryModel.php";

// Initialize the salary model
$salaryModel = new SalaryModel();

// Get user ID from session
$user_id = $_SESSION["user_id"];

// Process advance request form submission
$success_message = "";
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  if ($_POST['action'] === 'request_advance') {
    // Prepare data for adding new advance salary request
    $advanceData = [
      'user_id' => $user_id,
      'amount' => $_POST['amount'],
      'request_date' => $_POST['request_date'],
      'reason' => $_POST['reason'],
      'repayment_method' => $_POST['repayment_method'],
      'installments' => $_POST['installments'],
      'notes' => $_POST['notes']
    ];

    if ($salaryModel->requestAdvanceSalary($advanceData)) {
      $success_message = "Advance salary request submitted successfully!";
    } else {
      $error_message = "Error submitting advance salary request. Please try again.";
    }
  }
}

// Get salary data for the current user
$filters = [
  'user_id' => $user_id
];
$salaries = $salaryModel->getAllSalaries($filters);

// Get advance salary requests for the current user
$advanceFilters = [
  'user_id' => $user_id
];
$advances = $salaryModel->getAllAdvanceSalaries($advanceFilters);

// Get current month statistics
$currentMonth = date('F');
$currentYear = date('Y');

// Calculate totals
$totalSalary = 0;
$totalAdvance = 0;
$totalNetPayable = 0;

foreach ($salaries as $salary) {
  if ($salary['month'] === $currentMonth && $salary['year'] === $currentYear) {
    $totalSalary += $salary['amount'] + $salary['bonus'];
  }
}

foreach ($advances as $advance) {
  if ($advance['status'] === 'approved' || $advance['status'] === 'partially_paid') {
    $totalAdvance += $advance['remaining_amount'];
  }
}

$totalNetPayable = $totalSalary - $totalAdvance;

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
        <a href="index.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
          <i class="fas fa-home w-5 h-5 mr-3"></i>
          <span>Dashboard</span>
        </a>
        <a href="task-upload.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
          <i class="fas fa-tasks w-5 h-5 mr-3"></i>
          <span>Upload Task</span>
        </a>
        <a href="salary.php" class="flex items-center px-4 py-2 bg-indigo-700 text-white rounded-lg">
          <i class="fas fa-money-bill-wave w-5 h-5 mr-3"></i>
          <span>Salary</span>
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
                  <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none">
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
                  <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Salary Management</h1>
            <p class="mt-1 text-sm text-gray-600">View your salary information and manage advance requests</p>
          </div>
          <button type="button" onclick="openAdvanceRequestModal()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-hand-holding-usd mr-2"></i>
            Request Advance
          </button>
        </div>

        <!-- Tab navigation -->
        <div class="border-b border-gray-200 mb-6">
          <nav class="-mb-px flex space-x-8">
            <a href="#" id="salary-tab" class="text-indigo-600 border-indigo-500 whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm" onclick="showTab('salary-content'); return false;">
              Salary Information
            </a>
            <a href="#" id="advance-tab" class="text-gray-500 border-transparent whitespace-nowrap pb-3 px-1 border-b-2 font-medium text-sm hover:text-gray-700 hover:border-gray-300" onclick="showTab('advance-content'); return false;">
              Advance Requests
            </a>
          </nav>
        </div>

        <!-- Salary Stats -->
        <div id="salary-content" class="tab-content">
          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-6">
            <!-- Total Salary -->
            <div class="bg-white overflow-hidden rounded-xl shadow-md">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-12 w-12 rounded-md bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-indigo-600"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">Total Salary (<?php echo $currentMonth; ?>)</dt>
                      <dd class="text-2xl font-semibold text-gray-900"><?php echo SalaryModel::formatMoney($totalSalary); ?></dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Advance Amount -->
            <div class="bg-white overflow-hidden rounded-xl shadow-md">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-12 w-12 rounded-md bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-yellow-600"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">Advance Amount</dt>
                      <dd class="text-2xl font-semibold text-gray-900"><?php echo SalaryModel::formatMoney($totalAdvance); ?></dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Net Payable -->
            <div class="bg-white overflow-hidden rounded-xl shadow-md">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-12 w-12 rounded-md bg-green-100 flex items-center justify-center">
                    <i class="fas fa-calculator text-green-600"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">Net Payable</dt>
                      <dd class="text-2xl font-semibold text-gray-900"><?php echo SalaryModel::formatMoney($totalNetPayable); ?></dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Salary History Table -->
          <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
              <h2 class="text-lg font-medium text-gray-900">Salary History</h2>
              <!-- Filter by Year and Month -->
              <div class="flex items-center space-x-2">
                <select id="filter-month" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" onchange="filterSalary()">
                  <option value="">All Months</option>
                  <?php foreach ($months as $month): ?>
                    <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                  <?php endforeach; ?>
                </select>
                <select id="filter-year" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" onchange="filterSalary()">
                  <option value="">All Years</option>
                  <?php foreach ($years as $year): ?>
                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody id="salary-table-body" class="bg-white divide-y divide-gray-200">
                  <?php if (empty($salaries)): ?>
                    <tr>
                      <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                        No salary records found.
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($salaries as $salary): ?>
                      <tr class="salary-row" data-month="<?php echo $salary['month']; ?>" data-year="<?php echo $salary['year']; ?>">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          <?php echo htmlspecialchars($salary['month'] . ' ' . $salary['year']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          <?php echo SalaryModel::formatMoney($salary['amount']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          <?php echo SalaryModel::formatMoney($salary['bonus']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                          <?php echo SalaryModel::formatMoney($salary['amount'] + $salary['bonus']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          <?php echo !empty($salary['payment_date']) ? date('M j, Y', strtotime($salary['payment_date'])) : '-'; ?>
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
                          <button onclick="viewSalaryDetails(<?php echo $salary['id']; ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3" title="View Details">
                            <i class="fas fa-eye"></i>
                          </button>
                          <a href="salary-slip.php?id=<?php echo $salary['id']; ?>" class="text-gray-600 hover:text-gray-900" title="Download Slip">
                            <i class="fas fa-download"></i>
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Advance Requests Tab -->
        <div id="advance-content" class="tab-content hidden">
          <!-- Advance Requests Table -->
          <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
              <h2 class="text-lg font-medium text-gray-900">Advance Requests History</h2>
              <!-- Filter by Status -->
              <div class="flex items-center space-x-2">
                <select id="filter-status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm" onchange="filterAdvances()">
                  <option value="">All Statuses</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                  <option value="partially_paid">Partially Paid</option>
                  <option value="paid">Paid</option>
                </select>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Repayment</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody id="advance-table-body" class="bg-white divide-y divide-gray-200">
                  <?php if (empty($advances)): ?>
                    <tr>
                      <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                        No advance requests found.
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($advances as $advance): ?>
                      <tr class="advance-row" data-status="<?php echo $advance['status']; ?>">
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
                          <?php echo ucfirst($advance['repayment_method']); ?>
                          <?php if ($advance['repayment_method'] == 'installment'): ?>
                            (<?php echo $advance['installments']; ?> installments)
                          <?php endif; ?>
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
                          <button onclick="viewAdvanceDetails(<?php echo $advance['id']; ?>)" class="text-indigo-600 hover:text-indigo-900" title="View Details">
                            <i class="fas fa-eye"></i>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
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
          <a href="task-upload.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-tasks w-5 h-5 mr-3"></i>
            <span>Upload Task</span>
          </a>
          <a href="salary.php" class="flex items-center px-4 py-2 bg-indigo-700 text-white rounded-lg">
            <i class="fas fa-money-bill-wave w-5 h-5 mr-3"></i>
            <span>Salary</span>
            <span>Salary</span>
          </a>
          <a href="user-invoices.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-file-invoice w-5 h-5 mr-3"></i>
            <span>Invoices</span>
          </a>
          <a href="user-support.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-headset w-5 h-5 mr-3"></i>
            <span>Support</span>
          </a>
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

  <!-- Advance Request Modal -->
  <div id="advanceRequestModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="advanceRequestModalOverlay"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="closeModal('advanceRequestModal')">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              Request Advance Salary
            </h3>
            <div class="mt-4">
              <form action="salary.php" method="POST">
                <input type="hidden" name="action" value="request_advance">

                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-3">
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rs.</span>
                      </div>
                      <input type="number" step="0.01" name="amount" id="amount" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00" required>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="request_date" class="block text-sm font-medium text-gray-700">Request Date</label>
                    <div class="mt-1">
                      <input type="date" name="request_date" id="request_date" value="<?php echo date('Y-m-d'); ?>" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label for="reason" class="block text-sm font-medium text-gray-700">Reason</label>
                    <div class="mt-1">
                      <textarea id="reason" name="reason" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" required></textarea>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="repayment_method" class="block text-sm font-medium text-gray-700">Repayment Method</label>
                    <div class="mt-1">
                      <select id="repayment_method" name="repayment_method" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" onchange="toggleInstallments()">
                        <option value="salary_deduction">Salary Deduction</option>
                        <option value="installment">Installment</option>
                        <option value="one_time">One Time Payment</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3" id="installments_div">
                    <label for="installments" class="block text-sm font-medium text-gray-700">Installments</label>
                    <div class="mt-1">
                      <input type="number" min="1" name="installments" id="installments" value="1" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                    <div class="mt-1">
                      <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>
                  </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                  <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                    Submit Request
                  </button>
                  <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm" onclick="closeModal('advanceRequestModal')">
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

  <!-- Salary Detail Modal -->
  <div id="salaryDetailModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="salaryDetailModalOverlay"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="closeModal('salaryDetailModal')">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              Salary Details
            </h3>
            <div class="mt-4" id="salary-detail-content">
              <!-- Content will be loaded dynamically -->
              <div class="flex justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Advance Detail Modal -->
  <div id="advanceDetailModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="advanceDetailModalOverlay"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="closeModal('advanceDetailModal')">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              Advance Request Details
            </h3>
            <div class="mt-4" id="advance-detail-content">
              <!-- Content will be loaded dynamically -->
              <div class="flex justify-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div>
              </div>
            </div>
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

    // Tab functionality
    function showTab(tabId) {
      const tabs = document.querySelectorAll('.tab-content');
      tabs.forEach(tab => {
        tab.classList.add('hidden');
      });

      document.getElementById(tabId).classList.remove('hidden');

      // Update active tab styling
      document.querySelectorAll('a[id$="-tab"]').forEach(tabLink => {
        tabLink.classList.remove('text-indigo-600', 'border-indigo-500');
        tabLink.classList.add('text-gray-500', 'border-transparent');
      });

      if (tabId === 'salary-content') {
        document.getElementById('salary-tab').classList.remove('text-gray-500', 'border-transparent');
        document.getElementById('salary-tab').classList.add('text-indigo-600', 'border-indigo-500');
      } else if (tabId === 'advance-content') {
        document.getElementById('advance-tab').classList.remove('text-gray-500', 'border-transparent');
        document.getElementById('advance-tab').classList.add('text-indigo-600', 'border-indigo-500');
      }
    }

    // Modal functions
    function openModal(modalId) {
      document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
    }

    function openAdvanceRequestModal() {
      openModal('advanceRequestModal');
    }

    // Filter functions
    function filterSalary() {
      const month = document.getElementById('filter-month').value;
      const year = document.getElementById('filter-year').value;
      const rows = document.querySelectorAll('.salary-row');

      rows.forEach(row => {
        let showRow = true;

        if (month && row.dataset.month !== month) {
          showRow = false;
        }

        if (year && row.dataset.year !== year) {
          showRow = false;
        }

        row.style.display = showRow ? '' : 'none';
      });
    }

    function filterAdvances() {
      const status = document.getElementById('filter-status').value;
      const rows = document.querySelectorAll('.advance-row');

      rows.forEach(row => {
        let showRow = true;

        if (status && row.dataset.status !== status) {
          showRow = false;
        }

        row.style.display = showRow ? '' : 'none';
      });
    }

    // Toggle installments section visibility
    function toggleInstallments() {
      const repaymentMethod = document.getElementById('repayment_method').value;
      const installmentsDiv = document.getElementById('installments_div');

      if (repaymentMethod === 'installment') {
        installmentsDiv.style.display = '';
      } else {
        installmentsDiv.style.display = 'none';
      }
    }

    // Initialize installments visibility
    toggleInstallments();

    // View salary details
    function viewSalaryDetails(salaryId) {
      const detailContent = document.getElementById('salary-detail-content');
      detailContent.innerHTML = '<div class="flex justify-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div></div>';

      openModal('salaryDetailModal');

      // Fetch salary details via AJAX
      fetch('get-salary-details.php?id=' + salaryId)
        .then(response => response.text())
        .then(data => {
          detailContent.innerHTML = data;
        })
        .catch(error => {
          detailContent.innerHTML = '<div class="text-red-500">Error loading salary details. Please try again.</div>';
          console.error('Error:', error);
        });
    }

    // View advance details
    function viewAdvanceDetails(advanceId) {
      const detailContent = document.getElementById('advance-detail-content');
      detailContent.innerHTML = '<div class="flex justify-center"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500"></div></div>';

      openModal('advanceDetailModal');

      // Fetch advance details via AJAX
      fetch('get-advance-details.php?id=' + advanceId)
        .then(response => response.text())
        .then(data => {
          detailContent.innerHTML = data;
        })
        .catch(error => {
          detailContent.innerHTML = '<div class="text-red-500">Error loading advance details. Please try again.</div>';
          console.error('Error:', error);
        });
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