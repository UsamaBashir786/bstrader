<?php
// Include user authentication
require_once "../config/user-auth.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - User Dashboard</title>
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
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-1 text-sm text-gray-600">View your recent activity and account information</p>
          </div>
          <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-plus mr-2"></i>
            New Order
          </button>
        </div>

        <!-- Welcome Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
          <div class="p-6">
            <div class="sm:flex sm:items-center sm:justify-between">
              <div>
                <h2 class="text-xl font-bold text-gray-900">Welcome back, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h2>
                <p class="mt-2 text-sm text-gray-600">
                  Here's a summary of your recent activity and account information.
                </p>
              </div>
              <div class="mt-4 sm:mt-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                  <i class="fas fa-check-circle mr-1.5"></i>
                  Verified Customer
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-6">
          <!-- Total Orders -->
          <div class="bg-white overflow-hidden rounded-xl shadow-md">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-md bg-indigo-100 flex items-center justify-center">
                  <i class="fas fa-shopping-cart text-indigo-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                    <dd class="text-2xl font-semibold text-gray-900">12</dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
              <a href="user-orders.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                View all <i class="fas fa-arrow-right ml-1"></i>
              </a>
            </div>
          </div>

          <!-- Open Invoices -->
          <div class="bg-white overflow-hidden rounded-xl shadow-md">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-md bg-green-100 flex items-center justify-center">
                  <i class="fas fa-file-invoice text-green-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Open Invoices</dt>
                    <dd class="text-2xl font-semibold text-gray-900">3</dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
              <a href="user-invoices.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                View all <i class="fas fa-arrow-right ml-1"></i>
              </a>
            </div>
          </div>

          <!-- Pending Shipments -->
          <div class="bg-white overflow-hidden rounded-xl shadow-md">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-12 w-12 rounded-md bg-yellow-100 flex items-center justify-center">
                  <i class="fas fa-box text-yellow-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Pending Shipments</dt>
                    <dd class="text-2xl font-semibold text-gray-900">2</dd>
                  </dl>
                </div>
              </div>
            </div>
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-100">
              <a href="user-orders.php?status=pending" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                Track shipments <i class="fas fa-arrow-right ml-1"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
          <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Recent Orders</h2>
            <a href="user-orders.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
              View all
            </a>
          </div>

          <div class="divide-y divide-gray-200">
            <!-- Order 1 -->
            <div class="p-6 hover:bg-gray-50 transition-colors">
              <a href="user-orders.php?id=1234" class="block">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-lg font-medium text-indigo-600">Order #ORD-1234</p>
                    <p class="mt-1 text-sm text-gray-500">Placed on March 8, 2025</p>
                  </div>
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Delivered
                  </span>
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-600">
                  <div class="flex items-center mr-6">
                    <i class="fas fa-box text-gray-400 mr-2"></i>
                    5 Items
                  </div>
                  <div class="flex items-center">
                    <i class="fas fa-money-bill-wave text-gray-400 mr-2"></i>
                    $1,250.00
                  </div>
                </div>
              </a>
            </div>

            <!-- Order 2 -->
            <div class="p-6 hover:bg-gray-50 transition-colors">
              <a href="user-orders.php?id=1235" class="block">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-lg font-medium text-indigo-600">Order #ORD-1235</p>
                    <p class="mt-1 text-sm text-gray-500">Placed on March 2, 2025</p>
                  </div>
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Shipped
                  </span>
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-600">
                  <div class="flex items-center mr-6">
                    <i class="fas fa-box text-gray-400 mr-2"></i>
                    3 Items
                  </div>
                  <div class="flex items-center">
                    <i class="fas fa-money-bill-wave text-gray-400 mr-2"></i>
                    $850.00
                  </div>
                </div>
              </a>
            </div>

            <!-- Order 3 -->
            <div class="p-6 hover:bg-gray-50 transition-colors">
              <a href="user-orders.php?id=1236" class="block">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-lg font-medium text-indigo-600">Order #ORD-1236</p>
                    <p class="mt-1 text-sm text-gray-500">Placed on February 24, 2025</p>
                  </div>
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Processing
                  </span>
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-600">
                  <div class="flex items-center mr-6">
                    <i class="fas fa-box text-gray-400 mr-2"></i>
                    2 Items
                  </div>
                  <div class="flex items-center">
                    <i class="fas fa-money-bill-wave text-gray-400 mr-2"></i>
                    $1,780.00
                  </div>
                </div>
              </a>
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
          <a href="index.php" class="flex items-center px-4 py-2 bg-indigo-700 text-white rounded-lg">
            <i class="fas fa-home w-5 h-5 mr-3"></i>
            <span>Dashboard</span>
          </a>
          <a href="user-orders.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
            <span>My Orders</span>
          </a>
          <!-- Add other nav items here -->
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
  </script>
</body>

</html>