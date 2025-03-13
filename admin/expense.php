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
    <!-- Sidebar will be added by you -->
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

          <!-- Expense Summary Cards -->
          <div
            class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Card 1 -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
              <div class="p-5">
                <div class="flex items-center">
                  <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <i
                      class="fas fa-file-invoice-dollar text-red-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Expenses (March)
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          $4,200
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
                          $1,250
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
                    <i class="fas fa-shopping-cart text-blue-600 h-6 w-6"></i>
                  </div>
                  <div class="ml-5 w-0 flex-1">
                    <dl>
                      <dt class="text-sm font-medium text-gray-500 truncate">
                        Purchases
                      </dt>
                      <dd>
                        <div class="text-lg font-medium text-gray-900">
                          $2,350
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
                          $600
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
            <div class="flex flex-col md:flex-row justify-between gap-4">
              <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <div class="relative rounded-md shadow-sm">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                  </div>
                  <input
                    type="text"
                    class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md"
                    placeholder="Search expense..." />
                </div>
                <div>
                  <select
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="">All Categories</option>
                    <option value="bills">Bills & Utilities</option>
                    <option value="purchases">Purchases</option>
                    <option value="tax">Tax</option>
                    <option value="tea">Tea & Refreshments</option>
                    <option value="monthly">Monthly Expenses</option>
                  </select>
                </div>
                <div>
                  <select
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="">All Months</option>
                    <option value="january">January</option>
                    <option value="february">February</option>
                    <option value="march">March</option>
                    <option value="april">April</option>
                    <option value="may">May</option>
                    <option value="june">June</option>
                    <option value="july">July</option>
                    <option value="august">August</option>
                    <option value="september">September</option>
                    <option value="october">October</option>
                    <option value="november">November</option>
                    <option value="december">December</option>
                  </select>
                </div>
                <div>
                  <select
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                    <option value="">Year 2025</option>
                    <option value="2024">Year 2024</option>
                    <option value="2023">Year 2023</option>
                  </select>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button
                  type="button"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-filter mr-2 h-5 w-5 text-gray-500"></i>
                  Filter
                </button>
                <button
                  type="button"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-download mr-2 h-5 w-5 text-gray-500"></i>
                  Export
                </button>
              </div>
            </div>
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
                        <tr>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                              Electricity Bill
                            </div>
                            <div class="text-sm text-gray-500">
                              March 2025
                            </div>
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Bills & Utilities
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            March 8, 2025
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $350
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Ayesha Malik
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span
                              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                              Paid
                            </span>
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a
                              href="#"
                              class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-edit"></i></a>
                            <a
                              href="#"
                              class="text-red-600 hover:text-red-900 mr-3"><i class="fas fa-trash"></i></a>
                            <a
                              href="#"
                              class="text-gray-600 hover:text-gray-900"><i class="fas fa-eye"></i></a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                              Office Supplies
                            </div>
                            <div class="text-sm text-gray-500">
                              March 2025
                            </div>
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Purchases
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            March 5, 2025
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $450
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Sara Ali
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span
                              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                              Paid
                            </span>
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a
                              href="#"
                              class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-edit"></i></a>
                            <a
                              href="#"
                              class="text-red-600 hover:text-red-900 mr-3"><i class="fas fa-trash"></i></a>
                            <a
                              href="#"
                              class="text-gray-600 hover:text-gray-900"><i class="fas fa-eye"></i></a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                              Internet Bill
                            </div>
                            <div class="text-sm text-gray-500">
                              March 2025
                            </div>
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Bills & Utilities
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            March 10, 2025
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $150
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Bilal Ahmad
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span
                              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                              Paid
                            </span>
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a
                              href="#"
                              class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-edit"></i></a>
                            <a
                              href="#"
                              class="text-red-600 hover:text-red-900 mr-3"><i class="fas fa-trash"></i></a>
                            <a
                              href="#"
                              class="text-gray-600 hover:text-gray-900"><i class="fas fa-eye"></i></a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                              Office Refreshments
                            </div>
                            <div class="text-sm text-gray-500">
                              March 2025
                            </div>
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Tea & Refreshments
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            March 2, 2025
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $200
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Ahmed Khan
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span
                              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                              Paid
                            </span>
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a
                              href="#"
                              class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-edit"></i></a>
                            <a
                              href="#"
                              class="text-red-600 hover:text-red-900 mr-3"><i class="fas fa-trash"></i></a>
                            <a
                              href="#"
                              class="text-gray-600 hover:text-gray-900"><i class="fas fa-eye"></i></a>
                          </td>
                        </tr>
                        <tr>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                              New Equipment
                            </div>
                            <div class="text-sm text-gray-500">
                              March 2025
                            </div>
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Purchases
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            March 15, 2025
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            $1,900
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Ayesha Malik
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap">
                            <span
                              class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                              Pending
                            </span>
                          </td>
                          <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a
                              href="#"
                              class="text-primary-600 hover:text-primary-900 mr-3"><i class="fas fa-edit"></i></a>
                            <a
                              href="#"
                              class="text-red-600 hover:text-red-900 mr-3"><i class="fas fa-trash"></i></a>
                            <a
                              href="#"
                              class="text-gray-600 hover:text-gray-900"><i class="fas fa-eye"></i></a>
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
                    Showing <span class="font-medium">1</span> to
                    <span class="font-medium">5</span> of
                    <span class="font-medium">15</span> expenses
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
                      class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                      2
                    </a>
                    <a
                      href="#"
                      class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                      3
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
              <form action="#" method="POST">
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
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" />
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
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">Select Category</option>
                        <option value="bills">Bills & Utilities</option>
                        <option value="purchases">Purchases</option>
                        <option value="tax">Tax</option>
                        <option value="tea">Tea & Refreshments</option>
                        <option value="monthly">Monthly Expenses</option>
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
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" />
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
                        type="text"
                        name="amount"
                        id="amount"
                        class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                        placeholder="0.00" />
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
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="">Select Employee</option>
                        <option value="ahmed">Ahmed Khan</option>
                        <option value="sara">Sara Ali</option>
                        <option value="bilal">Bilal Ahmad</option>
                        <option value="ayesha">Ayesha Malik</option>
                        <option value="omar">Omar Farooq</option>
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
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
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
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="card">Credit Card</option>
                        <option value="other">Other</option>
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
    const mobileSidebar = document.getElementById("mobile-sidebar");
    const closeSidebar = document.getElementById("closeSidebar");

    sidebarToggle.addEventListener("click", () => {
      mobileSidebar.classList.remove("hidden");
    });

    closeSidebar.addEventListener("click", () => {
      mobileSidebar.classList.add("hidden");
    });

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
  </script>
</body>

</html>