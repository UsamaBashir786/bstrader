  <?php
  // Include user authentication
  require_once "../config/user-auth.php";

  // Initialize variables
  $task_name = $task_date = $task_area = $amount = $from_date = $to_date = $target = $description = $priority = $assigned_to = "";
  $task_name_err = $task_date_err = $task_area_err = $amount_err = $from_date_err = $to_date_err = $target_err = "";
  $success_message = $error_message = "";

  // Process form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate task name
    if (empty(trim($_POST["task_name"]))) {
      $task_name_err = "Please enter a task name";
    } else {
      $task_name = trim($_POST["task_name"]);
    }

    // Validate task date
    if (empty(trim($_POST["task_date"]))) {
      $task_date_err = "Please select a task date";
    } else {
      $task_date = trim($_POST["task_date"]);
    }

    // Validate task area
    if (empty(trim($_POST["task_area"]))) {
      $task_area_err = "Please enter the task area";
    } else {
      $task_area = trim($_POST["task_area"]);
    }

    // Validate amount
    if (empty(trim($_POST["amount"]))) {
      $amount_err = "Please enter the amount";
    } else if (!is_numeric(trim($_POST["amount"]))) {
      $amount_err = "Amount must be a number";
    } else {
      $amount = trim($_POST["amount"]);
    }

    // Validate from date
    if (empty(trim($_POST["from_date"]))) {
      $from_date_err = "Please select a start date";
    } else {
      $from_date = trim($_POST["from_date"]);
    }

    // Validate to date
    if (empty(trim($_POST["to_date"]))) {
      $to_date_err = "Please select an end date";
    } else {
      $to_date = trim($_POST["to_date"]);

      // Check if to date is after from date
      if (!empty($from_date) && strtotime($to_date) <= strtotime($from_date)) {
        $to_date_err = "End date must be after start date";
      }
    }

    // Validate target
    if (empty(trim($_POST["target"]))) {
      $target_err = "Please enter the target";
    } else {
      $target = trim($_POST["target"]);
    }

    // Get other optional fields
    $description = trim($_POST["description"]);
    $priority = trim($_POST["priority"]);
    $assigned_to = trim($_POST["assigned_to"]);

    // Process file upload if present
    $attachment = "";
    if (isset($_FILES["attachment"]) && $_FILES["attachment"]["error"] == 0) {
      $target_dir = "../uploads/tasks/";

      // Create directory if it doesn't exist
      if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
      }

      $file_extension = pathinfo($_FILES["attachment"]["name"], PATHINFO_EXTENSION);
      $file_name = uniqid() . '.' . $file_extension;
      $target_file = $target_dir . $file_name;

      // Check file type
      $allowed_types = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png');
      if (in_array(strtolower($file_extension), $allowed_types)) {
        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
          $attachment = $file_name;
        } else {
          $error_message = "Failed to upload attachment";
        }
      } else {
        $error_message = "Only PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG & PNG files are allowed";
      }
    }

    // Check if we can proceed with database insertion
    if (
      empty($task_name_err) && empty($task_date_err) && empty($task_area_err) &&
      empty($amount_err) && empty($from_date_err) && empty($to_date_err) &&
      empty($target_err) && empty($error_message)
    ) {

      // Connect to database
      require_once "../config/config.php";

      // Prepare an insert statement
      $sql = "INSERT INTO tasks (user_id, task_name, task_date, task_area, amount, from_date, to_date, target, 
                                description, priority, assigned_to, attachment, status, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

      if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param(
          "isssssssssss",
          $_SESSION["user_id"],
          $task_name,
          $task_date,
          $task_area,
          $amount,
          $from_date,
          $to_date,
          $target,
          $description,
          $priority,
          $assigned_to,
          $attachment
        );

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
          $success_message = "Task uploaded successfully!";

          // Clear the form fields after successful submission
          $task_name = $task_date = $task_area = $amount = $from_date = $to_date = $target = $description = $priority = $assigned_to = "";
        } else {
          $error_message = "Something went wrong. Please try again later.";
        }

        // Close statement
        $stmt->close();
      }

      // Close connection
      $conn->close();
    }
  }
  ?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BS Traders - Upload Task</title>
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
    <style>
      /* form-styles.css - Enhanced form input styling */

      /* Base input styling */
      input[type="text"],
      input[type="date"],
      input[type="email"],
      input[type="number"],
      input[type="password"],
      textarea,
      select {
        border: 2px solid #d1d5db;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        background-color: #f9fafb;
        transition: all 0.2s ease-in-out;
        outline: none;
        padding: 10px;
      }

      /* Hover state */
      input[type="text"]:hover,
      input[type="date"]:hover,
      input[type="email"]:hover,
      input[type="number"]:hover,
      input[type="password"]:hover,
      textarea:hover,
      select:hover {
        border-color: #9ca3af;
      }

      /* Focus state */
      input[type="text"]:focus,
      input[type="date"]:focus,
      input[type="email"]:focus,
      input[type="number"]:focus,
      input[type="password"]:focus,
      textarea:focus,
      select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
      }

      /* Special styling for required fields */
      input:required,
      textarea:required,
      select:required {
        border-left: 3px solid #3b82f6;
      }

      /* Error state */
      .input-error {
        border-color: #ef4444 !important;
      }

      .input-error:focus {
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.25) !important;
      }

      /* File upload area */
      .file-upload-area {
        border: 2px dashed #d1d5db;
        transition: all 0.2s ease-in-out;
      }

      .file-upload-area:hover {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.05);
      }

      /* Form section headers */
      .form-section-header {
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        color: #1f2937;
      }

      /* Submit button styling */
      .submit-button {
        background-color: #3b82f6;
        color: white;
        transition: all 0.2s ease-in-out;
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      }

      .submit-button:hover {
        background-color: #2563eb;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      }

      /* Cancel button styling */
      .cancel-button {
        border: 2px solid #d1d5db;
        color: #4b5563;
        transition: all 0.2s ease-in-out;
      }

      .cancel-button:hover {
        border-color: #9ca3af;
        color: #1f2937;
        background-color: #f9fafb;
      }

      /* Help text styling */
      .help-text {
        color: #6b7280;
        font-size: 0.875rem;
        margin-top: 0.5rem;
      }

      /* Required field indicator */
      .required-indicator {
        color: #ef4444;
        margin-left: 0.25rem;
      }

      /* Add some spacing and alignment to date inputs specifically */
      input[type="date"] {
        padding-right: 0.75rem;
      }

      /* Make the dropdown arrow more visible in select elements */
      select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.5rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
      }
    </style>
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
                    <p class="text-base font-medium text-white"><?php echo htmlspecialchars($_SESSION["name"]); ?></p>
                    <p class="text-sm font-medium text-primary-200 group-hover:text-primary-100">Customer Account</p>
                  </div>
                </div>
              </a>
            </div>
            <!-- Navigation -->
            <div class="flex-1 flex flex-col overflow-y-auto">
              <nav class="flex-1 px-2 py-4 space-y-1">
                <a href="index.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
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
                <a href="task-upload.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md bg-primary-800 text-white">
                  <i class="fas fa-tasks mr-4 h-6 w-6"></i>
                  Upload Task
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
          <div class="absolute top-0 right-0  pt-2">
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
              <a href="user-orders.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-shopping-cart mr-4 h-6 w-6"></i>
                My Orders
              </a>
              <a href="user-quotes.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-file-invoice-dollar mr-4 h-6 w-6"></i>
                Quotations
              </a>
              <a href="task-upload.php" class="group flex items-center px-2 py-2 text-base font-medium rounded-md bg-primary-800 text-white">
                <i class="fas fa-tasks mr-4 h-6 w-6"></i>
                Upload Task
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
                  <p class="text-base font-medium text-white"><?php echo htmlspecialchars($_SESSION["name"]); ?></p>
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
              <h3 class="text-lg leading-6 font-medium text-gray-900">Upload New Task</h3>
              <div class="mt-3 flex sm:mt-0 sm:ml-4">
                <a href="index.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  <i class="fas fa-arrow-left mr-2 -ml-1 h-5 w-5"></i>
                  Back to Dashboard
                </a>
              </div>
            </div>

            <!-- Success and Error Messages -->
            <?php if (!empty($success_message)): ?>
              <div class="mt-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400 h-5 w-5"></i>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?php echo $success_message; ?></p>
                  </div>
                  <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                      <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times h-5 w-5"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
              <div class="mt-6 rounded-md bg-red-50 p-4">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400 h-5 w-5"></i>
                  </div>
                  <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?php echo $error_message; ?></p>
                  </div>
                  <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                      <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times h-5 w-5"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <!-- Task Upload Form -->
            <div class="mt-8 max-w-4xl mx-auto bg-white shadow-lg rounded-xl overflow-hidden">
              <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">New Task Submission</h2>
              </div>
              <div class="px-6 py-8">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" class="space-y-10">
                  <!-- Task Information Section -->
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3 flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                      </svg>
                      Task Information
                    </h3>
                    <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-6">
                      <div class="sm:col-span-3">
                        <label for="task_name" class="block text-sm font-medium text-gray-700">Task Name <span class="text-red-500">*</span></label>
                        <div class="mt-2 relative rounded-md shadow-sm">
                          <input type="text" name="task_name" id="task_name" value="<?php echo $task_name; ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 sm:text-sm border-2 border-gray-300 bg-gray-50 shadow-sm rounded-md hover:border-gray-400 focus:shadow-md transition duration-150 <?php echo (!empty($task_name_err)) ? 'border-red-300 text-red-900 placeholder-red-300' : ''; ?>" placeholder="Enter task name">
                          <?php if (!empty($task_name_err)): ?>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                              <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                              </svg>
                            </div>
                          <?php endif; ?>
                        </div>
                        <?php if (!empty($task_name_err)): ?>
                          <p class="mt-2 text-sm text-red-600"><?php echo $task_name_err; ?></p>
                        <?php endif; ?>
                      </div>

                      <div class="sm:col-span-3">
                        <label for="task_date" class="block text-sm font-medium text-gray-700">Task Date <span class="text-red-500">*</span></label>
                        <div class="mt-2 relative rounded-md shadow-sm">
                          <input type="date" name="task_date" id="task_date" value="<?php echo $task_date; ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 sm:text-sm border-2 border-gray-300 bg-gray-50 shadow-sm rounded-md hover:border-gray-400 focus:shadow-md transition duration-150 <?php echo (!empty($task_date_err)) ? 'border-red-300 text-red-900 placeholder-red-300' : ''; ?>">
                          <?php if (!empty($task_date_err)): ?>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                              <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                              </svg>
                            </div>
                          <?php endif; ?>
                        </div>
                        <?php if (!empty($task_date_err)): ?>
                          <p class="mt-2 text-sm text-red-600"><?php echo $task_date_err; ?></p>
                        <?php endif; ?>
                      </div>

                      <div class="sm:col-span-3">
                        <label for="task_area" class="block text-sm font-medium text-gray-700">Task Area <span class="text-red-500">*</span></label>
                        <div class="mt-2 relative rounded-md shadow-sm">
                          <input type="text" name="task_area" id="task_area" value="<?php echo $task_area; ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 sm:text-sm border-2 border-gray-300 bg-gray-50 shadow-sm rounded-md hover:border-gray-400 focus:shadow-md transition duration-150 <?php echo (!empty($task_area_err)) ? 'border-red-300 text-red-900 placeholder-red-300' : ''; ?>" placeholder="Specify task area">
                          <?php if (!empty($task_area_err)): ?>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                              <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                              </svg>
                            </div>
                          <?php endif; ?>
                        </div>
                        <?php if (!empty($task_area_err)): ?>
                          <p class="mt-2 text-sm text-red-600"><?php echo $task_area_err; ?></p>
                        <?php endif; ?>
                      </div>

                      <div class="sm:col-span-3">
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount <span class="text-red-500">*</span></label>
                        <div class="mt-2 relative rounded-md shadow-sm">
                          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <!-- <span class="text-gray-500 sm:text-sm">$</span> -->
                          </div>
                          <input type="text" name="amount" id="amount" value="<?php echo $amount; ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-10 sm:text-sm border-2 border-gray-300 bg-gray-50 shadow-sm rounded-md hover:border-gray-400 focus:shadow-md transition duration-150 <?php echo (!empty($amount_err)) ? 'border-red-300 text-red-900 placeholder-red-300' : ''; ?>" placeholder="0.00">
                          <?php if (!empty($amount_err)): ?>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                              <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                              </svg>
                            </div>
                          <?php endif; ?>
                        </div>
                        <?php if (!empty($amount_err)): ?>
                          <p class="mt-2 text-sm text-red-600"><?php echo $amount_err; ?></p>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>

                  <!-- Timeline Section -->
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3 flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      Timeline & Targets
                    </h3>
                    <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-6">
                      <div class="sm:col-span-3">
                        <label for="from_date" class="block text-sm font-medium text-gray-700">Start Date <span class="text-red-500">*</span></label>
                        <div class="mt-2 relative rounded-md shadow-sm">
                          <input type="date" name="from_date" id="from_date" value="<?php echo $from_date; ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 sm:text-sm border-2 border-gray-300 bg-gray-50 shadow-sm rounded-md hover:border-gray-400 focus:shadow-md transition duration-150 <?php echo (!empty($from_date_err)) ? 'border-red-300 text-red-900 placeholder-red-300' : ''; ?>">
                          <?php if (!empty($from_date_err)): ?>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                              <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                              </svg>
                            </div>
                          <?php endif; ?>
                        </div>
                        <?php if (!empty($from_date_err)): ?>
                          <p class="mt-2 text-sm text-red-600"><?php echo $from_date_err; ?></p>
                        <?php endif; ?>
                      </div>

                      <div class="sm:col-span-3">
                        <label for="to_date" class="block text-sm font-medium text-gray-700">End Date <span class="text-red-500">*</span></label>
                        <div class="mt-2 relative rounded-md shadow-sm">
                          <input type="date" name="to_date" id="to_date" value="<?php echo $to_date; ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 sm:text-sm border-2 border-gray-300 bg-gray-50 shadow-sm rounded-md hover:border-gray-400 focus:shadow-md transition duration-150 <?php echo (!empty($to_date_err)) ? 'border-red-300 text-red-900 placeholder-red-300' : ''; ?>">
                          <?php if (!empty($to_date_err)): ?>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                              <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                              </svg>
                            </div>
                          <?php endif; ?>
                        </div>
                        <?php if (!empty($to_date_err)): ?>
                          <p class="mt-2 text-sm text-red-600"><?php echo $to_date_err; ?></p>
                        <?php endif; ?>
                      </div>

                      <div class="sm:col-span-6">
                        <label for="target" class="block text-sm font-medium text-gray-700">Target <span class="text-red-500">*</span></label>
                        <div class="mt-2 relative rounded-md shadow-sm">
                          <input type="text" name="target" id="target" value="<?php echo $target; ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-10 sm:text-sm border-2 border-gray-300 bg-gray-50 shadow-sm rounded-md hover:border-gray-400 focus:shadow-md transition duration-150 <?php echo (!empty($target_err)) ? 'border-red-300 text-red-900 placeholder-red-300' : ''; ?>" placeholder="Define target objectives">
                          <?php if (!empty($target_err)): ?>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                              <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                              </svg>
                            </div>
                          <?php endif; ?>
                        </div>
                        <?php if (!empty($target_err)): ?>
                          <p class="mt-2 text-sm text-red-600"><?php echo $target_err; ?></p>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>

                  <!-- Additional Details Section -->
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-3 flex items-center">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Additional Details
                    </h3>
                    <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-6">
                      <div class="sm:col-span-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Task Description</label>
                        <div class="mt-2">
                          <textarea id="description" name="description" rows="4" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-2 border-gray-300 bg-gray-50 rounded-md hover:border-gray-400 focus:shadow-md transition duration-150" placeholder="Provide a detailed description of the task"><?php echo $description; ?></textarea>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Provide any additional details or requirements for the task.</p>
                      </div>

                      <div class="sm:col-span-3">
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                        <div class="mt-2">
                          <select id="priority" name="priority" class="py-2 px-1 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-2 border-gray-300 bg-gray-50 rounded-md hover:border-gray-400 focus:shadow-md transition duration-150">
                            <option value="low" <?php if ($priority == "low") echo "selected"; ?>>Low</option>
                            <option value="medium" <?php if ($priority == "medium" || empty($priority)) echo "selected"; ?>>Medium</option>
                            <option value="high" <?php if ($priority == "high") echo "selected"; ?>>High</option>
                            <option value="urgent" <?php if ($priority == "urgent") echo "selected"; ?>>Urgent</option>
                          </select>
                        </div>
                      </div>

                      <div class="sm:col-span-3">
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assign To (Optional)</label>
                        <div class="mt-2">
                          <input type="text" name="assigned_to" id="assigned_to" value="<?php echo $assigned_to; ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-2 border-gray-300 bg-gray-50 shadow-sm rounded-md hover:border-gray-400 focus:shadow-md transition duration-150" placeholder="Person or department name">
                        </div>
                      </div>

                      <div class="sm:col-span-6">
                        <label for="attachment" class="block text-sm font-medium text-gray-700">
                          Task Attachment
                        </label>
                        <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition duration-150 bg-gray-50">
                          <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                              <label for="attachment" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-3 py-2 border border-gray-300 shadow-sm">
                                <span>Upload a file</span>
                                <input id="attachment" name="attachment" type="file" class="sr-only">
                              </label>
                              <p class="pl-1 pt-2">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, DOC, XLS, PNG, JPG up to 10MB</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="pt-6 border-t border-gray-200">
                    <div class="flex justify-end space-x-3">
                      <button type="button" onclick="window.location.href='index.php'" class="bg-white py-2 px-4 border-2 border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        Cancel
                      </button>
                      <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12" />
                        </svg>
                        Upload Task
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>

            <!-- Recent Tasks Section -->
            <!-- Replace the static Recent Tasks Section with this dynamic one -->
            <div class="mt-8">
              <div class="pb-5 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recently Uploaded Tasks</h3>
              </div>
              <div class="mt-4 bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                  <?php
                  // Connect to database
                  require_once "../config/config.php";

                  // Prepare the query to get recent tasks for the current user
                  $user_id = $_SESSION["user_id"];
                  $recent_tasks_sql = "SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";

                  if ($stmt = $conn->prepare($recent_tasks_sql)) {
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                      while ($task = $result->fetch_assoc()) {
                        // Determine status color and icon
                        $status_class = "";
                        $icon_class = "bg-primary-100";
                        $icon_text = "text-primary-600";

                        switch ($task['status']) {
                          case 'completed':
                            $status_class = "bg-green-100 text-green-800";
                            break;
                          case 'in_progress':
                            $status_class = "bg-yellow-100 text-yellow-800";
                            $icon_class = "bg-yellow-100";
                            $icon_text = "text-yellow-600";
                            break;
                          case 'cancelled':
                            $status_class = "bg-red-100 text-red-800";
                            $icon_class = "bg-red-100";
                            $icon_text = "text-red-600";
                            break;
                          default: // pending
                            $status_class = "bg-blue-100 text-blue-800";
                            break;
                        }

                        // Format dates
                        $created_date = date("F j, Y", strtotime($task['created_at']));
                        $from_date = date("M j", strtotime($task['from_date']));
                        $to_date = date("M j, Y", strtotime($task['to_date']));

                        // Format amount with 2 decimal places
                        $formatted_amount = number_format($task['amount'], 2);

                        // Output task list item
                        echo "
              <li>
                <div class=\"block hover:bg-gray-50\">
                  <div class=\"px-4 py-4 sm:px-6\">
                    <div class=\"flex items-center justify-between\">
                      <div class=\"flex items-center\">
                        <div class=\"flex-shrink-0 h-10 w-10 {$icon_class} rounded-full flex items-center justify-center\">
                          <i class=\"fas fa-tasks {$icon_text}\"></i>
                        </div>
                        <div class=\"ml-4\">
                          <p class=\"text-sm font-medium text-primary-600\">" . htmlspecialchars($task['task_name']) . "</p>
                          <p class=\"text-sm text-gray-500\">Added on {$created_date}</p>
                        </div>
                      </div>
                      <div class=\"ml-2 flex-shrink-0 flex\">
                        <span class=\"px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$status_class}\">
                          " . ucfirst(str_replace('_', ' ', $task['status'])) . "
                        </span>
                      </div>
                    </div>
                    <div class=\"mt-2 sm:flex sm:justify-between\">
                      <div class=\"sm:flex\">
                        <p class=\"flex items-center text-sm text-gray-500\">
                          <i class=\"flex-shrink-0 mr-1.5 fas fa-map-marker-alt text-gray-400\"></i>
                          " . htmlspecialchars($task['task_area']) . "
                        </p>
                        <p class=\"mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6\">
                          <i class=\"flex-shrink-0 mr-1.5 fas fa-calendar text-gray-400\"></i>
                          {$from_date} - {$to_date}
                        </p>
                      </div>
                      <div class=\"mt-2 flex items-center text-sm text-gray-500 sm:mt-0\">
                        <p>
                          {$formatted_amount}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              ";
                      }
                    } else {
                      echo "
            <li class=\"px-4 py-6 text-center text-gray-500\">
              <p>No tasks found. Start by uploading your first task!</p>
            </li>
            ";
                    }

                    $stmt->close();
                  } else {
                    echo "
          <li class=\"px-4 py-6 text-center text-gray-500\">
            <p>Unable to retrieve tasks. Please try again later.</p>
          </li>
          ";
                  }

                  // Close connection
                  $conn->close();
                  ?>
                </ul>
                <div class="bg-white px-4 py-3 border-t border-gray-200 text-right sm:px-6">
                  <a href="view-all-tasks.php" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                    View all tasks <i class="fas fa-arrow-right ml-1"></i>
                  </a>
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

      sidebarToggle.addEventListener('click', () => {
        mobileSidebar.classList.remove('hidden');
      });

      closeSidebar.addEventListener('click', () => {
        mobileSidebar.classList.add('hidden');
      });

      // File input display
      const fileInput = document.getElementById('attachment');
      fileInput.addEventListener('change', (e) => {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
          const fileText = document.querySelector('.text-xs.text-gray-500');
          fileText.textContent = `Selected file: ${fileName}`;
        }
      });

      // Alert dismissal
      const closeButtons = document.querySelectorAll('.bg-green-50 button, .bg-red-50 button');
      closeButtons.forEach(button => {
        button.addEventListener('click', () => {
          button.closest('.rounded-md').style.display = 'none';
        });
      });
    </script>
  </body>

  </html>