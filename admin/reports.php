<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Reports</title>
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
              <a href="reports.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-white bg-primary-800">
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
                  <img class="inline-block h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="Profile photo">
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
                      <img class="h-8 w-8 rounded-full" src="https://via.placeholder.com/150" alt="">
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
            <h3 class="text-lg leading-6 font-medium text-gray-900">Reports & Analytics</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-download mr-2 -ml-1 h-5 w-5"></i>
                Export Reports
              </button>
              <button type="button" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-print mr-2 -ml-1 h-5 w-5"></i>
                Print Reports
              </button>
            </div>
          </div>

          <!-- Time Period Selector -->
          <div class="mt-6 bg-white shadow rounded-lg p-4">
            <div class="flex flex-col md:flex-row justify-between gap-4">
              <div class="flex flex-col sm:flex-row gap-2">
                <div>
                  <label for="report-type" class="block text-sm font-medium text-gray-700">Report Type</label>
                  <select id="report-type" name="report-type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="sales">Sales Report</option>
                    <option value="expenses">Expense Report</option>
                    <option value="inventory">Inventory Report</option>
                    <option value="profit">Profit & Loss</option>
                    <option value="employee">Employee Performance</option>
                  </select>
                </div>
                <div>
                  <label for="time-period" class="block text-sm font-medium text-gray-700">Time Period</label>
                  <select id="time-period" name="time-period" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="yearly">Yearly</option>
                    <option value="custom">Custom Range</option>
                  </select>
                </div>
                <div>
                  <label for="date-from" class="block text-sm font-medium text-gray-700">From</label>
                  <input type="date" name="date-from" id="date-from" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                </div>
                <div>
                  <label for="date-to" class="block text-sm font-medium text-gray-700">To</label>
                  <input type="date" name="date-to" id="date-to" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                </div>
              </div>
              <div class="flex items-end">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-sync-alt mr-2 h-5 w-5"></i>
                  Generate Report
                </button>
              </div>
            </div>
          </div>

          <!-- Dashboard Section -->
          <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Summary Card 1 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-dollar-sign text-green-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Revenue
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          $124,500
                        </div>
                        <div class="text-sm text-green-600">
                          <i class="fas fa-arrow-up mr-1"></i>
                          12.5% from last month
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Summary Card 2 -->
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
                          $42,800
                        </div>
                        <div class="text-sm text-red-600">
                          <i class="fas fa-arrow-up mr-1"></i>
                          5.3% from last month
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Summary Card 3 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                    <i class="fas fa-chart-line text-primary-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Net Profit
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          $81,700
                        </div>
                        <div class="text-sm text-green-600">
                          <i class="fas fa-arrow-up mr-1"></i>
                          18.2% from last month
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>

            <!-- Summary Card 4 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-shopping-cart text-yellow-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Orders
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          235
                        </div>
                        <div class="text-sm text-green-600">
                          <i class="fas fa-arrow-up mr-1"></i>
                          8.4% from last month
                        </div>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sales Chart Section -->
          <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-12">
            <div class="bg-white overflow-hidden shadow rounded-lg sm:col-span-8">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Monthly Sales Analysis</h3>
                <div class="mt-2">
                  <div class="h-80 bg-gray-50 rounded-lg border border-gray-200 p-4">
                    <!-- Sales chart placeholder - would be replaced with actual chart -->
                    <div class="flex items-center justify-center h-full">
                      <div class="text-center">
                        <div class="h-60 w-full bg-gray-100 rounded overflow-hidden">
                          <!-- Chart bars mockup -->
                          <div class="flex h-full items-end justify-between px-2">
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 30%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 40%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 35%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 50%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 45%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 60%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 80%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 75%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 65%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 85%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 90%"></div>
                            <div class="w-8 bg-primary-500 rounded-t" style="height: 70%"></div>
                          </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-2">
                          <span>Apr</span>
                          <span>May</span>
                          <span>Jun</span>
                          <span>Jul</span>
                          <span>Aug</span>
                          <span>Sep</span>
                          <span>Oct</span>
                          <span>Nov</span>
                          <span>Dec</span>
                          <span>Jan</span>
                          <span>Feb</span>
                          <span>Mar</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mt-3 text-sm">
                  <div class="flex justify-between text-gray-500">
                    <span>2024-2025 Monthly Revenue Trend</span>
                    <span class="text-primary-600 hover:text-primary-900 cursor-pointer">
                      <i class="fas fa-expand-alt mr-1"></i>
                      View Detailed Report
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg sm:col-span-4">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Sales Distribution</h3>
                <div class="mt-2">
                  <div class="h-80 bg-gray-50 rounded-lg border border-gray-200 p-4">
                    <!-- Pie chart placeholder - would be replaced with actual chart -->
                    <div class="flex items-center justify-center h-full">
                      <div class="text-center">
                        <div class="h-48 w-48 mx-auto bg-gray-100 rounded-full overflow-hidden relative">
                          <!-- Pie chart mockup -->
                          <div class="absolute inset-0" style="clip-path: polygon(50% 50%, 100% 50%, 100% 0, 0 0, 0 50%); background-color: #0072ff;"></div>
                          <div class="absolute inset-0" style="clip-path: polygon(50% 50%, 0 50%, 0 100%, 50% 100%); background-color: #66aaff;"></div>
                          <div class="absolute inset-0" style="clip-path: polygon(50% 50%, 50% 100%, 100% 100%, 100% 50%); background-color: #004499;"></div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                          <div class="flex items-center">
                            <span class="w-3 h-3 bg-primary-500 rounded-full inline-block mr-1"></span>
                            <span class="text-gray-600">Product A</span>
                          </div>
                          <div class="flex items-center">
                            <span class="w-3 h-3 bg-primary-300 rounded-full inline-block mr-1"></span>
                            <span class="text-gray-600">Product B</span>
                          </div>
                          <div class="flex items-center">
                            <span class="w-3 h-3 bg-primary-700 rounded-full inline-block mr-1"></span>
                            <span class="text-gray-600">Product C</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mt-3 text-sm">
                  <div class="flex justify-between text-gray-500">
                    <span>Product Category Sales</span>
                    <span class="text-primary-600 hover:text-primary-900 cursor-pointer">
                      <i class="fas fa-expand-alt mr-1"></i>
                      View Detailed Report
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Report Tables Section -->
          <div class="mt-6 bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
              <div class="flex flex-wrap items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Transactions</h3>
                <div class="flex mt-2 sm:mt-0">
                  <button type="button" class="text-primary-600 hover:text-primary-900 mr-4">
                    <i class="fas fa-file-csv mr-1"></i> Export CSV
                  </button>
                  <button type="button" class="text-primary-600 hover:text-primary-900">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                  </button>
                </div>
              </div>
            </div>
            <div class="p-6">
              <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                  <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                      <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                          <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Transaction ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Customer
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Products
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                              Amount
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
                          <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              TX-78952
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                  <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
                                </div>
                                <div class="ml-4">
                                  <div class="text-sm font-medium text-gray-900">
                                    Rashid Khan
                                  </div>
                                  <div class="text-sm text-gray-500">
                                    rashid@example.com
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              March 10, 2025
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              Steel Pipes (20), Cement (10)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              $4,250
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Completed
                              </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                              <a href="#" class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-eye"></i></a>
                              <a href="#" class="text-gray-600 hover:text-gray-900"><i class="fas fa-print"></i></a>
                            </td>
                          </tr>
                          <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              TX-78953
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                  <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
                                </div>
                                <div class="ml-4">
                                  <div class="text-sm font-medium text-gray-900">
                                    Ayesha Ahmad
                                  </div>
                                  <div class="text-sm text-gray-500">
                                    ayesha@example.com
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              March 8, 2025
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              Paint (15), Brushes (20)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              $1,850
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Processing
                              </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                              <a href="#" class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-eye"></i></a>
                              <a href="#" class="text-gray-600 hover:text-gray-900"><i class="fas fa-print"></i></a>
                            </td>
                          </tr>
                          <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              TX-78954
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                  <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
                                </div
                                  </div>
                                <div class="ml-4">
                                  <div class="text-sm font-medium text-gray-900">
                                    Imran Ali
                                  </div>
                                  <div class="text-sm text-gray-500">
                                    imran@example.com
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              March 7, 2025
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              Electrical Wires (100m), Switches (50)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              $3,200
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Completed
                              </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                              <a href="#" class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-eye"></i></a>
                              <a href="#" class="text-gray-600 hover:text-gray-900"><i class="fas fa-print"></i></a>
                            </td>
                          </tr>
                          <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              TX-78955
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                  <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
                                </div>
                                <div class="ml-4">
                                  <div class="text-sm font-medium text-gray-900">
                                    Farah Kamal
                                  </div>
                                  <div class="text-sm text-gray-500">
                                    farah@example.com
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              March 5, 2025
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              Bricks (1000), Concrete (5)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              $2,750
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Completed
                              </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                              <a href="#" class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-eye"></i></a>
                              <a href="#" class="text-gray-600 hover:text-gray-900"><i class="fas fa-print"></i></a>
                            </td>
                          </tr>
                          <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              TX-78956
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                  <img class="h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
                                </div>
                                <div class="ml-4">
                                  <div class="text-sm font-medium text-gray-900">
                                    Hassan Khan
                                  </div>
                                  <div class="text-sm text-gray-500">
                                    hassan@example.com
                                  </div>
                                </div>
                              </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              March 3, 2025
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                              Steel Bars (50), Sand (10)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              $5,650
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                              <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Cancelled
                              </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                              <a href="#" class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-eye"></i></a>
                              <a href="#" class="text-gray-600 hover:text-gray-900"><i class="fas fa-print"></i></a>
                            </td>
                          </tr>
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
                      Showing <span class="font-medium">1</span> to <span class="font-medium">5</span> of <span class="font-medium">25</span> transactions
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
                      <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        2
                      </a>
                      <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        3
                      </a>
                      <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                        ...
                      </span>
                      <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                        5
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
          </div>

          <!-- Additional Reports Section -->
          <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <!-- Report Card 1 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <i class="fas fa-users text-purple-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Employee Performance
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          View Report
                        </div>
                      </dd>
                    </dl>
                  </div>
                  <div class="ml-5 flex-shrink-0">
                    <a href="#" class="text-primary-600 hover:text-primary-900">
                      <i class="fas fa-arrow-right h-5 w-5"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Report Card 2 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-warehouse text-blue-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Inventory Analysis
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          View Report
                        </div>
                      </dd>
                    </dl>
                  </div>
                  <div class="ml-5 flex-shrink-0">
                    <a href="#" class="text-primary-600 hover:text-primary-900">
                      <i class="fas fa-arrow-right h-5 w-5"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Report Card 3 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-file-invoice text-green-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Financial Statements
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          View Report
                        </div>
                      </dd>
                    </dl>
                  </div>
                  <div class="ml-5 flex-shrink-0">
                    <a href="#" class="text-primary-600 hover:text-primary-900">
                      <i class="fas fa-arrow-right h-5 w-5"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
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

    if (sidebarToggle && mobileSidebar && closeSidebar) {
      sidebarToggle.addEventListener('click', () => {
        mobileSidebar.classList.remove('hidden');
      });

      closeSidebar.addEventListener('click', () => {
        mobileSidebar.classList.add('hidden');
      });
    }

    // Date range functionality
    const timePeriod = document.getElementById('time-period');
    const dateFrom = document.getElementById('date-from');
    const dateTo = document.getElementById('date-to');

    if (timePeriod && dateFrom && dateTo) {
      // Set default dates (current month)
      const now = new Date();
      const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
      const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

      dateFrom.valueAsDate = firstDay;
      dateTo.valueAsDate = lastDay;

      timePeriod.addEventListener('change', () => {
        const selectedPeriod = timePeriod.value;

        switch (selectedPeriod) {
          case 'daily':
            dateFrom.valueAsDate = now;
            dateTo.valueAsDate = now;
            break;
          case 'weekly':
            const weekStart = new Date(now);
            weekStart.setDate(now.getDate() - now.getDay());
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            dateFrom.valueAsDate = weekStart;
            dateTo.valueAsDate = weekEnd;
            break;
          case 'monthly':
            dateFrom.valueAsDate = firstDay;
            dateTo.valueAsDate = lastDay;
            break;
          case 'quarterly':
            const quarterMonth = Math.floor(now.getMonth() / 3) * 3;
            const quarterStart = new Date(now.getFullYear(), quarterMonth, 1);
            const quarterEnd = new Date(now.getFullYear(), quarterMonth + 3, 0);
            dateFrom.valueAsDate = quarterStart;
            dateTo.valueAsDate = quarterEnd;
            break;
          case 'yearly':
            const yearStart = new Date(now.getFullYear(), 0, 1);
            const yearEnd = new Date(now.getFullYear(), 11, 31);
            dateFrom.valueAsDate = yearStart;
            dateTo.valueAsDate = yearEnd;
            break;
          case 'custom':
            // Do nothing, let user select custom dates
            break;
        }
      });
    }
  </script>
</body>

</html>