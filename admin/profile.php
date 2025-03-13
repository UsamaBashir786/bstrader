<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Admin Profile</title>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900">Admin Profile</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <a href="settings.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-cog mr-2 -ml-1 h-5 w-5"></i>
                Account Settings
              </a>
            </div>
          </div>

          <!-- Profile Content -->
          <div class="mt-6">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
                <div>
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Personal Information</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Admin details and application access.</p>
                </div>
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="edit-profile-btn">
                  <i class="fas fa-edit mr-2 -ml-1 h-5 w-5"></i>
                  Edit Profile
                </button>
              </div>

              <!-- Profile Overview -->
              <div class="border-t border-gray-200">
                <div class="flex flex-col md:flex-row bg-white">
                  <!-- Profile Image Section -->
                  <div class="md:w-1/3 p-8 bg-gray-50 flex flex-col items-center">
                    <div class="relative">
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
                      <div class="absolute bottom-0 right-0">
                        <label for="photo-upload" class="cursor-pointer bg-primary-600 hover:bg-primary-700 text-white rounded-full p-2 shadow-lg">
                          <i class="fas fa-camera"></i>
                          <input id="photo-upload" name="photo-upload" type="file" class="sr-only">
                        </label>
                      </div>
                    </div>
                    <h2 class="mt-4 text-xl font-bold text-gray-900">Ahmad Khan</h2>
                    <p class="text-gray-600">System Administrator</p>
                    <div class="mt-4 flex space-x-3">
                      <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                        <i class="fas fa-shield-alt mr-1"></i> Admin
                      </span>
                      <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i> Active
                      </span>
                    </div>
                    <div class="mt-6 w-full">
                      <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Account Status</h4>
                        <div class="flex items-center justify-between mb-2">
                          <span class="text-sm text-gray-600">Last Login</span>
                          <span class="text-sm font-medium">March 11, 2025 (10:45 AM)</span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                          <span class="text-sm text-gray-600">Account Created</span>
                          <span class="text-sm font-medium">January 15, 2023</span>
                        </div>
                        <div class="flex items-center justify-between">
                          <span class="text-sm text-gray-600">Role</span>
                          <span class="text-sm font-medium">Super Admin</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Profile Details Section -->
                  <div class="md:w-2/3 p-8">
                    <!-- Personal Information -->
                    <div class="mb-8">
                      <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Contact Information</h4>
                      <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                          <dt class="text-sm font-medium text-gray-500">Full name</dt>
                          <dd class="mt-1 text-sm text-gray-900">Ahmad Khan</dd>
                        </div>
                        <div class="sm:col-span-1">
                          <dt class="text-sm font-medium text-gray-500">Employee ID</dt>
                          <dd class="mt-1 text-sm text-gray-900">EMP-001</dd>
                        </div>
                        <div class="sm:col-span-1">
                          <dt class="text-sm font-medium text-gray-500">Email address</dt>
                          <dd class="mt-1 text-sm text-gray-900">ahmad.khan@bstraders.com</dd>
                        </div>
                        <div class="sm:col-span-1">
                          <dt class="text-sm font-medium text-gray-500">Phone number</dt>
                          <dd class="mt-1 text-sm text-gray-900">+92 300 1234567</dd>
                        </div>
                        <div class="sm:col-span-2">
                          <dt class="text-sm font-medium text-gray-500">Address</dt>
                          <dd class="mt-1 text-sm text-gray-900">123 Business Avenue, Commercial Area, Lahore, Pakistan</dd>
                        </div>
                      </dl>
                    </div>

                    <!-- Employment Information -->
                    <div class="mb-8">
                      <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">Employment Information</h4>
                      <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                          <dt class="text-sm font-medium text-gray-500">Department</dt>
                          <dd class="mt-1 text-sm text-gray-900">Management</dd>
                        </div>
                        <div class="sm:col-span-1">
                          <dt class="text-sm font-medium text-gray-500">Position</dt>
                          <dd class="mt-1 text-sm text-gray-900">System Administrator</dd>
                        </div>
                        <div class="sm:col-span-1">
                          <dt class="text-sm font-medium text-gray-500">Joining Date</dt>
                          <dd class="mt-1 text-sm text-gray-900">January 15, 2023</dd>
                        </div>
                        <div class="sm:col-span-1">
                          <dt class="text-sm font-medium text-gray-500">Reports To</dt>
                          <dd class="mt-1 text-sm text-gray-900">Zubair Ahmed (CTO)</dd>
                        </div>
                      </dl>
                    </div>

                    <!-- System Access -->
                    <div>
                      <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-4">System Access</h4>
                      <div class="border border-gray-200 rounded-md divide-y divide-gray-200">
                        <div class="flex items-center justify-between py-3 px-4">
                          <div class="flex items-center">
                            <i class="fas fa-user-shield text-primary-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">User Management</span>
                          </div>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Full Access
                          </span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-4">
                          <div class="flex items-center">
                            <i class="fas fa-file-invoice-dollar text-primary-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Financial Records</span>
                          </div>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Full Access
                          </span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-4">
                          <div class="flex items-center">
                            <i class="fas fa-chart-bar text-primary-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Reports & Analytics</span>
                          </div>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Full Access
                          </span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-4">
                          <div class="flex items-center">
                            <i class="fas fa-cogs text-primary-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">System Configuration</span>
                          </div>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Full Access
                          </span>
                        </div>
                        <div class="flex items-center justify-between py-3 px-4">
                          <div class="flex items-center">
                            <i class="fas fa-database text-primary-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-900">Database Management</span>
                          </div>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Limited Access
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Activity</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">System activities and login history.</p>
              </div>
              <div class="border-t border-gray-200">
                <div class="bg-white overflow-hidden">
                  <ul class="divide-y divide-gray-200">
                    <li class="px-4 py-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-sign-in-alt text-primary-600"></i>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">System Login</div>
                            <div class="text-sm text-gray-500">Successfully logged in from 192.168.1.105</div>
                          </div>
                        </div>
                        <div class="text-sm text-gray-500">March 11, 2025 - 10:45 AM</div>
                      </div>
                    </li>
                    <li class="px-4 py-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-plus text-green-600"></i>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">User Created</div>
                            <div class="text-sm text-gray-500">Added new employee: Fatima Ali (EMP-025)</div>
                          </div>
                        </div>
                        <div class="text-sm text-gray-500">March 10, 2025 - 03:22 PM</div>
                      </div>
                    </li>
                    <li class="px-4 py-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-file-invoice text-yellow-600"></i>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">Report Generated</div>
                            <div class="text-sm text-gray-500">Generated monthly sales report for February 2025</div>
                          </div>
                        </div>
                        <div class="text-sm text-gray-500">March 9, 2025 - 11:05 AM</div>
                      </div>
                    </li>
                    <li class="px-4 py-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-edit text-red-600"></i>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">Permission Changed</div>
                            <div class="text-sm text-gray-500">Updated access permissions for Omar Farooq (EMP-018)</div>
                          </div>
                        </div>
                        <div class="text-sm text-gray-500">March 8, 2025 - 02:17 PM</div>
                      </div>
                    </li>
                    <li class="px-4 py-4">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-cog text-blue-600"></i>
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">System Update</div>
                            <div class="text-sm text-gray-500">Updated system configuration settings</div>
                          </div>
                        </div>
                        <div class="text-sm text-gray-500">March 7, 2025 - 09:30 AM</div>
                      </div>
                    </li>
                  </ul>
                  <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                      View all activity <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Security Settings -->
            <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6 flex items-center justify-between">
                <div>
                  <h3 class="text-lg leading-6 font-medium text-gray-900">Security Settings</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage your account security options.</p>
                </div>
              </div>
              <div class="border-t border-gray-200">
                <div class="bg-white overflow-hidden">
                  <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-6">
                      <div class="flex items-center justify-between">
                        <div>
                          <h4 class="text-sm font-medium text-gray-900">Change Password</h4>
                          <p class="mt-1 text-sm text-gray-500">Update your password regularly for better security.</p>
                        </div>
                        <button type="button" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                          Change Password
                        </button>
                      </div>
                      <div class="flex items-center justify-between">
                        <div>
                          <h4 class="text-sm font-medium text-gray-900">Two-Factor Authentication</h4>
                          <p class="mt-1 text-sm text-gray-500">Add an extra layer of security to your account.</p>
                        </div>
                        <div class="flex items-center">
                          <button type="button" class="bg-gray-200 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                            <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                          </button>
                        </div>
                      </div>
                      <div class="flex items-center justify-between">
                        <div>
                          <h4 class="text-sm font-medium text-gray-900">Login Notifications</h4>
                          <p class="mt-1 text-sm text-gray-500">Get notified when someone logs into your account.</p>
                        </div>
                        <div class="flex items-center">
                          <button type="button" class="bg-primary-600 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" role="switch" aria-checked="true">
                            <span aria-hidden="true" class="translate-x-5 pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                          </button>
                        </div>
                      </div>
                      <div class="flex items-center justify-between">
                        <div>
                          <h4 class="text-sm font-medium text-gray-900">Session Timeout</h4>
                          <p class="mt-1 text-sm text-gray-500">Automatically log out after period of inactivity.</p>
                        </div>
                        <select class="mt-1 block w-48 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                          <option value="15">15 minutes</option>
                          <option value="30" selected>30 minutes</option>
                          <option value="60">60 minutes</option>
                          <option value="120">2 hours</option>
                        </select>
                      </div>
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

  <!-- Edit Profile Modal -->
  <div id="edit-profile-modal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="modalOverlay"></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="absolute top-0 right-0 pt-4 pr-4">
          <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="close-modal-btn">
            <span class="sr-only">Close</span>
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              Edit Profile Information
            </h3>
            <div class="mt-4">
              <form action="#" method="POST">
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
                  <div class="sm:col-span-3">
                    <label for="emp-id" class="block text-sm font-medium text-gray-700">Employee ID</label>
                    <div class="mt-1">
                      <input type="text" name="emp-id" id="emp-id" value="EMP-001" disabled class="shadow-sm bg-gray-50 focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <div class="mt-1">
                      <input type="text" name="address" id="address" value="123 Business Avenue, Commercial Area, Lahore, Pakistan" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                    <div class="mt-1">
                      <select id="department" name="department" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                        <option value="management" selected>Management</option>
                        <option value="sales">Sales</option>
                        <option value="it">IT</option>
                        <option value="hr">Human Resources</option>
                        <option value="finance">Finance</option>
                      </select>
                    </div>
                  </div>
                  <div class="sm:col-span-3">
                    <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                    <div class="mt-1">
                      <input type="text" name="position" id="position" value="System Administrator" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                    </div>
                  </div>
                  <div class="sm:col-span-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                    <div class="mt-1">
                      <textarea id="bio" name="bio" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">Experienced system administrator with expertise in database management, network security, and system operations.</textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Brief description about yourself. This will be displayed on your profile.</p>
                  </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                  <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm">
                    Save Changes
                  </button>
                  <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:col-start-1 sm:text-sm" id="cancel-btn">
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

    // Modal toggle
    const editProfileBtn = document.getElementById('edit-profile-btn');
    const editProfileModal = document.getElementById('edit-profile-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const modalOverlay = document.getElementById('modalOverlay');

    editProfileBtn.addEventListener('click', () => {
      editProfileModal.classList.remove('hidden');
    });

    function closeModal() {
      editProfileModal.classList.add('hidden');
    }

    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', closeModal);

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

    // Profile image upload
    const photoUpload = document.getElementById('photo-upload');
    photoUpload.addEventListener('change', function() {
      // In a real implementation, you would handle the file upload here
      // For this example, we'll just show an alert
      if (this.files && this.files[0]) {
        alert('Profile picture selected. In a real implementation, this would be uploaded to the server.');
      }
    });
  </script>
</body>

</html>