<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BS Traders - User Dashboard</title>
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
        <!-- Sidebar / Navigation -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col h-0 flex-1 bg-primary-700">
                    <!-- Logo -->
                    <div class="flex items-center h-16 flex-shrink-0 px-4 bg-primary-800">
                        <span class="text-2xl font-bold text-white">BS Traders</span>
                    </div>
                    <!-- User info -->
                    <div class="flex-shrink-0 flex border-t border-primary-800 p-4">
                        <a href="user-profile.php" class="flex-shrink-0 group block">
                            <div class="flex items-center">
                                <div>
                                    <img class="inline-block h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
                                </div>
                                <div class="ml-3">
                                    <p class="text-base font-medium text-white">Adnan Khalid</p>
                                    <p class="text-sm font-medium text-primary-200 group-hover:text-primary-100">Customer Account</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <!-- Navigation -->
                    <div class="flex-1 flex flex-col overflow-y-auto">
                        <nav class="flex-1 px-2 py-4 space-y-1">
                            <a href="user-dashboard.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md bg-primary-800 text-white">
                                <i class="fas fa-home mr-4 h-6 w-6"></i>
                                Dashboard
                            </a>
                            <a href="user-orders.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                                <i class="fas fa-shopping-cart mr-4 h-6 w-6"></i>
                                My Orders
                            </a>
                            <a href="user-quotes.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                                <i class="fas fa-file-invoice-dollar mr-4 h-6 w-6"></i>
                                Quotations
                            </a>
                            <a href="user-products.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                                <i class="fas fa-boxes mr-4 h-6 w-6"></i>
                                Products
                            </a>
                            <a href="user-invoices.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                                <i class="fas fa-file-invoice mr-4 h-6 w-6"></i>
                                Invoices
                            </a>
                            <a href="user-support.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                                <i class="fas fa-headset mr-4 h-6 w-6"></i>
                                Support
                            </a>
                            <a href="user-profile.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                                <i class="fas fa-user-circle mr-4 h-6 w-6"></i>
                                My Profile
                            </a>
                            <a href="user-settings.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                                <i class="fas fa-cog mr-4 h-6 w-6"></i>
                                Settings
                            </a>
                        </nav>
                    </div>
                </div>
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
                        <a href="user-dashboard.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md bg-primary-800 text-white">
                            <i class="fas fa-home mr-4 h-6 w-6"></i>
                            Dashboard
                        </a>
                        <a href="user-orders.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                            <i class="fas fa-shopping-cart mr-4 h-6 w-6"></i>
                            My Orders
                        </a>
                        <a href="user-quotes.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                            <i class="fas fa-file-invoice-dollar mr-4 h-6 w-6"></i>
                            Quotations
                        </a>
                        <a href="user-products.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                            <i class="fas fa-boxes mr-4 h-6 w-6"></i>
                            Products
                        </a>
                        <a href="user-invoices.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                            <i class="fas fa-file-invoice mr-4 h-6 w-6"></i>
                            Invoices
                        </a>
                        <a href="user-support.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                            <i class="fas fa-headset mr-4 h-6 w-6"></i>
                            Support
                        </a>
                        <a href="user-profile.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                            <i class="fas fa-user-circle mr-4 h-6 w-6"></i>
                            My Profile
                        </a>
                        <a href="user-settings.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                            <i class="fas fa-cog mr-4 h-6 w-6"></i>
                            Settings
                        </a>
                    </nav>
                </div>
                <div class="flex-shrink-0 flex border-t border-primary-800 p-4">
                    <a href="user-profile.php" class="flex-shrink-0 group block">
                        <div class="flex items-center">
                            <div>
                                <img class="inline-block h-10 w-10 rounded-full" src="https://via.placeholder.com/150" alt="">
                            </div>
                            <div class="ml-3">
                                <p class="text-base font-medium text-white">Adnan Khalid</p>
                                <p class="text-sm font-medium text-primary-200 group-hover:text-primary-100">Customer Account</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="flex-shrink-0 w-14"></div>
        </div>

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
                                    BS Traders Customer Portal
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
                                <button class="p-1 ml-3 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 relative">
                                    <i class="fas fa-bell h-6 w-6"></i>
                                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                                </button>
                                <div class="ml-3 relative">
                                    <div>
                                        <button type="button" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" id="user-menu-button">
                                            <span class="sr-only">Open user menu</span>
                                            <img class="h-8 w-8 rounded-full" src="https://via.placeholder.com/150" alt="">
                                        </button>
                                    </div>
                                    <div id="user-dropdown" class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu">
                                        <a href="user-profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Your Profile</a>
                                        <a href="user-settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Settings</a>
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
                    <!-- User Dashboard Content -->
                    <div class="pb-5 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">User Dashboard</h3>
                        <div class="mt-3 flex sm:mt-0 sm:ml-4">
                            <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-plus mr-2 -ml-1 h-5 w-5"></i>
                                New Order
                            </button>
                        </div>
                    </div>

                    <!-- Welcome Section -->
                    <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <div class="sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Welcome back, Adnan!</h3>
                                    <p class="mt-2 max-w-2xl text-sm text-gray-500">
                                        Here's a summary of your recent activity and account information.
                                    </p>
                                </div>
                                <div class="mt-4 sm:mt-0">
                                    <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1.5 -ml-0.5 h-4 w-4"></i>
                                        Verified Customer
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Summary Cards -->
                    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Card 1 -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 bg-primary-100 rounded-md p-3">
                                        <i class="fas fa-shopping-cart text-primary-600 h-6 w-6"></i>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">
                                                Total Orders
                                            </dt>
                                            <dd>
                                                <div class="text-lg font-medium text-gray-900"></div>