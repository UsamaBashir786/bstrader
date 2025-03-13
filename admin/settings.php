<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Settings</title>
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
              <a href="salary.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-white  hover:bg-primary-600 hover:text-white">
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
              <a href="settings.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 bg-primary-800">
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
                      </svg> </button>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900">Settings</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="save-settings-btn">
                <i class="fas fa-save mr-2 -ml-1 h-5 w-5"></i>
                Save All Changes
              </button>
            </div>
          </div>

          <!-- Settings Navigation -->
          <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="border-b border-gray-200">
              <nav class="flex -mb-px">
                <a href="#account-settings" class="settings-tab border-primary-500 text-primary-600 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" aria-current="page" data-target="account-settings">
                  <i class="fas fa-user-cog mr-2"></i>
                  Account Settings
                </a>
                <a href="#system-settings" class="settings-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" data-target="system-settings">
                  <i class="fas fa-cogs mr-2"></i>
                  System Settings
                </a>
                <a href="#notification-settings" class="settings-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" data-target="notification-settings">
                  <i class="fas fa-bell mr-2"></i>
                  Notification Settings
                </a>
                <a href="#security-settings" class="settings-tab border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm" data-target="security-settings">
                  <i class="fas fa-shield-alt mr-2"></i>
                  Security Settings
                </a>
              </nav>
            </div>

            <!-- Account Settings Panel -->
            <div id="account-settings" class="settings-panel">
              <div class="px-4 py-5 sm:p-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h4>
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-3">
                    <label for="first-name" class="block text-sm font-medium text-gray-700">First name</label>
                    <div class="mt-1">
                      <input type="text" name="first-name" id="first-name" value="Ahmad" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="last-name" class="block text-sm font-medium text-gray-700">Last name</label>
                    <div class="mt-1">
                      <input type="text" name="last-name" id="last-name" value="Khan" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <div class="mt-1">
                      <input type="email" name="email" id="email" value="ahmad.khan@bstraders.com" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone number</label>
                    <div class="mt-1">
                      <input type="text" name="phone" id="phone" value="+92 300 1234567" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Profile Picture</h4>
                  <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                      <img class="h-24 w-24 rounded-full" src="https://via.placeholder.com/150" alt="Profile picture">
                    </div>
                    <div>
                      <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-camera mr-2 -ml-1"></i>
                        Change Picture
                      </button>
                      <p class="mt-2 text-sm text-gray-500">JPG, GIF or PNG. 1MB max.</p>
                    </div>
                  </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Language & Time Zone</h4>
                  <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                      <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
                      <div class="mt-1">
                        <select id="language" name="language" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                          <option value="en" selected>English</option>
                          <option value="ur">Urdu</option>
                          <option value="ar">Arabic</option>
                        </select>
                      </div>
                    </div>
                    <div class="sm:col-span-3">
                      <label for="timezone" class="block text-sm font-medium text-gray-700">Time Zone</label>
                      <div class="mt-1">
                        <select id="timezone" name="timezone" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                          <option value="PKT" selected>Pakistan Time (GMT+5)</option>
                          <option value="GMT">Greenwich Mean Time (GMT)</option>
                          <option value="EST">Eastern Standard Time (GMT-5)</option>
                          <option value="CST">Central Standard Time (GMT-6)</option>
                          <option value="PST">Pacific Standard Time (GMT-8)</option>
                        </select>
                      </div>
                    </div>
                    <div class="sm:col-span-3">
                      <label for="date-format" class="block text-sm font-medium text-gray-700">Date Format</label>
                      <div class="mt-1">
                        <select id="date-format" name="date-format" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                          <option value="dd/mm/yyyy" selected>DD/MM/YYYY</option>
                          <option value="mm/dd/yyyy">MM/DD/YYYY</option>
                          <option value="yyyy/mm/dd">YYYY/MM/DD</option>
                        </select>
                      </div>
                    </div>
                    <div class="sm:col-span-3">
                      <label for="time-format" class="block text-sm font-medium text-gray-700">Time Format</label>
                      <div class="mt-1">
                        <select id="time-format" name="time-format" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                          <option value="12h" selected>12 Hour (AM/PM)</option>
                          <option value="24h">24 Hour</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- System Settings Panel -->
            <div id="system-settings" class="settings-panel hidden">
              <div class="px-4 py-5 sm:p-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">General System Settings</h4>
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-3">
                    <label for="company-name" class="block text-sm font-medium text-gray-700">Company Name</label>
                    <div class="mt-1">
                      <input type="text" name="company-name" id="company-name" value="BS Traders" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="business-type" class="block text-sm font-medium text-gray-700">Business Type</label>
                    <div class="mt-1">
                      <select id="business-type" name="business-type" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="trading" selected>Trading</option>
                        <option value="manufacturing">Manufacturing</option>
                        <option value="retail">Retail</option>
                        <option value="services">Services</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label for="company-address" class="block text-sm font-medium text-gray-700">Business Address</label>
                    <div class="mt-1">
                      <input type="text" name="company-address" id="company-address" value="123 Business Avenue, Commercial Area, Lahore, Pakistan" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Dashboard Settings</h4>
                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <h5 class="text-sm font-medium text-gray-900">Show Quick Stats</h5>
                        <p class="text-sm text-gray-500">Display quick statistics on dashboard home page</p>
                      </div>
                      <div class="flex items-center">
                        <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                          <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                      </div>
                    </div>
                    <div class="flex items-center justify-between">
                      <div>
                        <h5 class="text-sm font-medium text-gray-900">Show Recent Activity</h5>
                        <p class="text-sm text-gray-500">Display recent system activity on dashboard</p>
                      </div>
                      <div class="flex items-center">
                        <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                          <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                      </div>
                    </div>
                    <div class="flex items-center justify-between">
                      <div>
                        <h5 class="text-sm font-medium text-gray-900">Enable Analytics</h5>
                        <p class="text-sm text-gray-500">Collect and display system analytics</p>
                      </div>
                      <div class="flex items-center">
                        <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                          <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Financial Settings</h4>
                  <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-2">
                      <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                      <div class="mt-1">
                        <select id="currency" name="currency" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                          <option value="PKR" selected>Pakistani Rupee (PKR)</option>
                          <option value="USD">US Dollar (USD)</option>
                          <option value="EUR">Euro (EUR)</option>
                          <option value="GBP">British Pound (GBP)</option>
                        </select>
                      </div>
                    </div>
                    <div class="sm:col-span-2">
                      <label for="tax-rate" class="block text-sm font-medium text-gray-700">Default Tax Rate (%)</label>
                      <div class="mt-1">
                        <input type="text" name="tax-rate" id="tax-rate" value="16" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                      </div>
                    </div>
                    <div class="sm:col-span-2">
                      <label for="fiscal-year" class="block text-sm font-medium text-gray-700">Fiscal Year Start</label>
                      <div class="mt-1">
                        <select id="fiscal-year" name="fiscal-year" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                          <option value="january">January</option>
                          <option value="july" selected>July</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Notification Settings Panel -->
            <div id="notification-settings" class="settings-panel hidden">
              <div class="px-4 py-5 sm:p-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Email Notifications</h4>
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <div>
                      <h5 class="text-sm font-medium text-gray-900">Login Notifications</h5>
                      <p class="text-sm text-gray-500">Receive email alerts when someone logs into your account</p>
                    </div>
                    <div class="flex items-center">
                      <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                        <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                      </button>
                    </div>
                  </div>
                  <div class="flex items-center justify-between">
                    <div>
                      <h5 class="text-sm font-medium text-gray-900">System Updates</h5>
                      <p class="text-sm text-gray-500">Receive email notifications for system updates</p>
                    </div>
                    <div class="flex items-center">
                      <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                        <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                      </button>
                    </div>
                  </div>
                  <div class="flex items-center justify-between">
                    <div>
                      <h5 class="text-sm font-medium text-gray-900">New User Registrations</h5>
                      <p class="text-sm text-gray-500">Receive email notifications when new users register</p>
                    </div>
                    <div class="flex items-center">
                      <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                        <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">System Notifications</h4>
                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <h5 class="text-sm font-medium text-gray-900">Low Inventory Alerts</h5>
                        <p class="text-sm text-gray-500">Get notified when inventory items are low</p>
                      </div>
                      <div class="flex items-center">
                        <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                          <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                      </div>
                    </div>
                    <div class="flex items-center justify-between">
                      <div>
                        <h5 class="text-sm font-medium text-gray-900">Task Assignments</h5>
                        <p class="text-sm text-gray-500">Get notified when tasks are assigned to you</p>
                      </div>
                      <div class="flex items-center">
                        <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                          <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                      </div>
                    </div>
                    <div class="flex items-center justify-between">
                      <div>
                        <h5 class="text-sm font-medium text-gray-900">Payment Notifications</h5>
                        <p class="text-sm text-gray-500">Get notified about payment activities</p>
                      </div>
                      <div class="flex items-center">
                        <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                          <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mt-8 pt-8 border-t border-gray-200">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Notification Delivery Methods</h4>
                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <h5 class="text-sm font-medium text-gray-900">Email</h5>
                        <p class="text-sm text-gray-500">Receive notifications via email</p>
                      </div>
                      <div class="flex items-center">
                        <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                          <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                      </div>
                    </div>
                    <div class="flex items-center justify-between">
                      <div>
                        <h5 class="text-sm font-medium text-gray-900">SMS</h5>
                        <p class="text-sm text-gray-500">Receive notifications via SMS</p>
                      </div>
                      <div class="flex items-center">
                        <button type="button" class="bg-gray-200 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="false">
                          <span aria-hidden="true" class="translate-x-0 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                      </div>
                    </div>
                    <div class="flex items-center justify-between">
                      <div>
                        <h5 class="text-sm font-medium text-gray-900">Browser Notifications</h5>
                        <p class="text-sm text-gray-500">Receive notifications in your browser</p>
                      </div>
                      <div class="flex items-center">
                        <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                          <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Security Settings Panel -->
            <div id="security-settings" class="settings-panel hidden">
              <div class="px-4 py-5 sm:p-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Password Settings</h4>
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                  <div class="sm:col-span-4">
                    <label for="current-password" class="block text-sm font-medium text-gray-700">Current Password</label>
                    <div class="mt-1">
                      <input type="password" name="current-password" id="current-password" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-4">
                    <label for="new-password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <div class="mt-1">
                      <input type="password" name="new-password" id="new-password" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-4">
                    <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <div class="mt-1">
                      <input type="password" name="confirm-password" id="confirm-password" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      Change Password
                    </button>
                  </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Two-Factor Authentication</h4>
                  <div class="flex items-center justify-between">
                    <div>
                      <h5 class="text-sm font-medium text-gray-900">Enable Two-Factor Authentication</h5>
                      <p class="text-sm text-gray-500">Add an extra layer of security to your account</p>
                    </div>
                    <div class="flex items-center">
                      <button type="button" class="bg-gray-200 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="false">
                        <span aria-hidden="true" class="translate-x-0 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                      </button>
                    </div>
                  </div>
                  <div class="mt-4">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                      <i class="fas fa-qrcode mr-2 -ml-1"></i>
                      Set Up Two-Factor Authentication
                    </button>
                  </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Session Management</h4>
                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <h5 class="text-sm font-medium text-gray-900">Automatic Logout</h5>
                        <p class="text-sm text-gray-500">Automatically log out after period of inactivity</p>
                      </div>
                      <select class="mt-1 block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                        <option value="15">15 minutes</option>
                        <option value="30" selected>30 minutes</option>
                        <option value="60">60 minutes</option>
                        <option value="120">2 hours</option>
                        <option value="never">Never</option>
                      </select>
                    </div>
                  </div>
                  <div class="mt-6">
                    <h5 class="text-sm font-medium text-gray-900 mb-4">Active Sessions</h5>
                    <div class="bg-gray-50 rounded-md border border-gray-200 overflow-hidden">
                      <ul class="divide-y divide-gray-200">
                        <li class="px-4 py-4">
                          <div class="flex items-center justify-between">
                            <div>
                              <p class="text-sm font-medium text-gray-900">Current Session</p>
                              <p class="text-sm text-gray-500">Chrome on Windows - 192.168.1.105</p>
                              <p class="text-xs text-gray-500 mt-1">Started: March 11, 2025 10:45 AM</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                              Active
                            </span>
                          </div>
                        </li>
                        <li class="px-4 py-4">
                          <div class="flex items-center justify-between">
                            <div>
                              <p class="text-sm font-medium text-gray-900">Mobile Session</p>
                              <p class="text-sm text-gray-500">Safari on iPhone - 192.168.1.120</p>
                              <p class="text-xs text-gray-500 mt-1">Started: March 10, 2025 09:30 AM</p>
                            </div>
                            <button type="button" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                              Revoke
                            </button>
                          </div>
                        </li>
                      </ul>
                    </div>
                    <div class="mt-4">
                      <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fas fa-sign-out-alt mr-2 -ml-1"></i>
                        Log Out All Other Sessions
                      </button>
                    </div>
                  </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-200">
                  <h4 class="text-lg font-medium text-gray-900 mb-4">Login History</h4>
                  <div class="bg-gray-50 rounded-md border border-gray-200 overflow-hidden">
                    <ul class="divide-y divide-gray-200">
                      <li class="px-4 py-4">
                        <div class="flex items-center justify-between">
                          <div>
                            <p class="text-sm font-medium text-gray-900">Chrome on Windows</p>
                            <p class="text-sm text-gray-500">192.168.1.105 - Lahore, Pakistan</p>
                          </div>
                          <p class="text-sm text-gray-500">March 11, 2025 10:45 AM</p>
                        </div>
                      </li>
                      <li class="px-4 py-4">
                        <div class="flex items-center justify-between">
                          <div>
                            <p class="text-sm font-medium text-gray-900">Safari on iPhone</p>
                            <p class="text-sm text-gray-500">192.168.1.120 - Lahore, Pakistan</p>
                          </div>
                          <p class="text-sm text-gray-500">March 10, 2025 09:30 AM</p>
                        </div>
                      </li>
                      <li class="px-4 py-4">
                        <div class="flex items-center justify-between">
                          <div>
                            <p class="text-sm font-medium text-gray-900">Firefox on MacOS</p>
                            <p class="text-sm text-gray-500">192.168.1.110 - Lahore, Pakistan</p>
                          </div>
                          <p class="text-sm text-gray-500">March 9, 2025 02:15 PM</p>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <div class="mt-4 text-right">
                    <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                      View Full Login History <i class="fas fa-arrow-right ml-1"></i>
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

    // Settings tab switching
    const settingsTabs = document.querySelectorAll('.settings-tab');
    const settingsPanels = document.querySelectorAll('.settings-panel');

    settingsTabs.forEach(tab => {
      tab.addEventListener('click', (e) => {
        e.preventDefault();

        // Deactivate all tabs
        settingsTabs.forEach(t => {
          t.classList.remove('border-primary-500', 'text-primary-600');
          t.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        // Activate clicked tab
        tab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        tab.classList.add('border-primary-500', 'text-primary-600');

        // Hide all panels
        settingsPanels.forEach(panel => {
          panel.classList.add('hidden');
        });

        // Show corresponding panel
        const targetId = tab.getAttribute('data-target');
        document.getElementById(targetId).classList.remove('hidden');
      });
    });

    // Toggle switches functionality
    const toggleButtons = document.querySelectorAll('[role="switch"]');
    toggleButtons.forEach(button => {
      button.addEventListener('click', function() {
        const checked = this.getAttribute('aria-checked') === 'true';
        this.setAttribute('aria-checked', !checked);

        if (!checked) {
          this.classList.remove('bg-gray-200');
          this.classList.add('bg-primary-600');
          this.querySelector('span').classList.remove('translate-x-0');
          this.querySelector('span').classList.add('translate-x-5');
        } else {
          this.classList.remove('bg-primary-600');
          this.classList.add('bg-gray-200');
          this.querySelector('span').classList.remove('translate-x-5');
          this.querySelector('span').classList.add('translate-x-0');
        }
      });
    });

    // Save settings button
    const saveSettingsBtn = document.getElementById('save-settings-btn');
    saveSettingsBtn.addEventListener('click', () => {
      // In a real application, this would save all settings
      alert('Settings saved successfully!');
    });
  </script>
</body>

</html>