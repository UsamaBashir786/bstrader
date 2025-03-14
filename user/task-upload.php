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
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Upload New Task</h1>
            <p class="mt-1 text-sm text-gray-600">Submit a new task to be processed by our team</p>
          </div>
          <a href="index.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Dashboard
          </a>
        </div>

        <!-- Alerts -->
        <?php if (!empty($success_message)): ?>
          <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm text-green-800"><?php echo $success_message; ?></p>
              </div>
              <button class="ml-auto alert-close">
                <i class="fas fa-times text-green-500"></i>
              </button>
            </div>
          </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
          <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
            <div class="flex">
              <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500"></i>
              </div>
              <div class="ml-3">
                <p class="text-sm text-red-800"><?php echo $error_message; ?></p>
              </div>
              <button class="ml-auto alert-close">
                <i class="fas fa-times text-red-500"></i>
              </button>
            </div>
          </div>
        <?php endif; ?>

        <!-- Task Form -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
          <!-- Form Header -->
          <div class="bg-indigo-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Task Information</h2>
            <p class="text-indigo-200 text-sm mt-1">Fill out the form below to submit your task</p>
          </div>

          <!-- Form Content -->
          <div class="p-6">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
              <!-- Basic Information -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-6 pb-2 border-b border-gray-200">
                  <i class="fas fa-info-circle text-indigo-500 mr-2"></i>Basic Information
                </h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <!-- Task Name -->
                  <div>
                    <label for="task_name" class="block text-sm font-medium text-gray-700 mb-1">
                      Task Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-tasks text-gray-400"></i>
                      </div>
                      <input type="text" name="task_name" id="task_name" value="<?php echo $task_name; ?>"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($task_name_err)) ? 'border-red-500' : ''; ?>"
                        placeholder="Enter task name">
                    </div>
                    <?php if (!empty($task_name_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $task_name_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <!-- Task Date -->
                  <div>
                    <label for="task_date" class="block text-sm font-medium text-gray-700 mb-1">
                      Task Date <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                      </div>
                      <input type="date" name="task_date" id="task_date" value="<?php echo $task_date; ?>"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($task_date_err)) ? 'border-red-500' : ''; ?>">
                    </div>
                    <?php if (!empty($task_date_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $task_date_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <!-- Task Area -->
                  <div>
                    <label for="task_area" class="block text-sm font-medium text-gray-700 mb-1">
                      Task Area <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                      </div>
                      <input type="text" name="task_area" id="task_area" value="<?php echo $task_area; ?>"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($task_area_err)) ? 'border-red-500' : ''; ?>"
                        placeholder="Specify task area">
                    </div>
                    <?php if (!empty($task_area_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $task_area_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <!-- Amount -->
                  <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                      Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-dollar-sign text-gray-400"></i>
                      </div>
                      <input type="text" name="amount" id="amount" value="<?php echo $amount; ?>"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($amount_err)) ? 'border-red-500' : ''; ?>"
                        placeholder="0.00">
                    </div>
                    <?php if (!empty($amount_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $amount_err; ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <!-- Timeline & Targets -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-6 pb-2 border-b border-gray-200">
                  <i class="fas fa-calendar-check text-indigo-500 mr-2"></i>Timeline & Targets
                </h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <!-- Start Date -->
                  <div>
                    <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">
                      Start Date <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar-day text-gray-400"></i>
                      </div>
                      <input type="date" name="from_date" id="from_date" value="<?php echo $from_date; ?>"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($from_date_err)) ? 'border-red-500' : ''; ?>">
                    </div>
                    <?php if (!empty($from_date_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $from_date_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <!-- End Date -->
                  <div>
                    <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">
                      End Date <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar-day text-gray-400"></i>
                      </div>
                      <input type="date" name="to_date" id="to_date" value="<?php echo $to_date; ?>"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($to_date_err)) ? 'border-red-500' : ''; ?>">
                    </div>
                    <?php if (!empty($to_date_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $to_date_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <!-- Target -->
                  <div class="md:col-span-2">
                    <label for="target" class="block text-sm font-medium text-gray-700 mb-1">
                      Target <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-bullseye text-gray-400"></i>
                      </div>
                      <input type="text" name="target" id="target" value="<?php echo $target; ?>"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($target_err)) ? 'border-red-500' : ''; ?>"
                        placeholder="Define target objectives">
                    </div>
                    <?php if (!empty($target_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $target_err; ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <!-- Additional Details -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-6 pb-2 border-b border-gray-200">
                  <i class="fas fa-clipboard-list text-indigo-500 mr-2"></i>Additional Details
                </h3>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <!-- Description -->
                  <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                      Task Description
                    </label>
                    <div class="relative">
                      <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                        <i class="fas fa-align-left text-gray-400"></i>
                      </div>
                      <textarea name="description" id="description" rows="4"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Provide a detailed description of the task"><?php echo $description; ?></textarea>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Include any additional details or requirements for this task</p>
                  </div>

                  <!-- Priority -->
                  <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                      Priority
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-flag text-gray-400"></i>
                      </div>
                      <select name="priority" id="priority"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="low" <?php if ($priority == "low") echo "selected"; ?>>Low</option>
                        <option value="medium" <?php if ($priority == "medium" || empty($priority)) echo "selected"; ?>>Medium</option>
                        <option value="high" <?php if ($priority == "high") echo "selected"; ?>>High</option>
                        <option value="urgent" <?php if ($priority == "urgent") echo "selected"; ?>>Urgent</option>
                      </select>
                    </div>
                  </div>

                  <!-- Assigned To -->
                  <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">
                      Assign To (Optional)
                    </label>
                    <div class="relative">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                      </div>
                      <input type="text" name="assigned_to" id="assigned_to" value="<?php echo $assigned_to; ?>"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Person or department name">
                    </div>
                  </div>
                </div>
              </div>

              <!-- File Attachment -->
              <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-6 pb-2 border-b border-gray-200">
                  <i class="fas fa-paperclip text-indigo-500 mr-2"></i>Attachments
                </h3>

                <div class="mt-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Task Attachment
                  </label>
                  <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition-colors">
                    <div class="space-y-1 text-center">
                      <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-3"></i>
                      <div class="flex flex-col sm:flex-row items-center justify-center text-sm text-gray-600">
                        <label for="attachment" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 px-3 py-2 border border-gray-300 shadow-sm">
                          <span>Upload a file</span>
                          <input id="attachment" name="attachment" type="file" class="sr-only" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        </label>
                        <p class="pl-1 mt-2 sm:mt-0">or drag and drop</p>
                      </div>
                      <p class="text-xs text-gray-500">PDF, DOC, XLS, PNG, JPG up to 10MB</p>
                      <p class="text-xs text-gray-500 mt-2" id="selected-file">No file selected</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Form Actions -->
              <div class="border-t border-gray-200 pt-6 flex justify-end space-x-3">
                <a href="index.php" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  <i class="fas fa-upload mr-2"></i>Upload Task
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Recent Tasks -->
        <div class="mt-8">
          <h2 class="text-lg font-medium text-gray-900 mb-4">Recently Uploaded Tasks</h2>
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
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
                    // Determine status color
                    $status_color = "";
                    $status_bg = "";

                    switch ($task['status']) {
                      case 'completed':
                        $status_color = "text-green-800";
                        $status_bg = "bg-green-100";
                        break;
                      case 'in_progress':
                        $status_color = "text-yellow-800";
                        $status_bg = "bg-yellow-100";
                        break;
                      case 'cancelled':
                        $status_color = "text-red-800";
                        $status_bg = "bg-red-100";
                        break;
                      default: // pending
                        $status_color = "text-blue-800";
                        $status_bg = "bg-blue-100";
                        break;
                    }

                    // Format dates
                    $created_date = date("F j, Y", strtotime($task['created_at']));
                    $from_date = date("M j", strtotime($task['from_date']));
                    $to_date = date("M j, Y", strtotime($task['to_date']));

                    // Format amount
                    $formatted_amount = number_format($task['amount'], 2);

                    echo "
                      <li>
                        <div class='px-4 py-4 sm:px-6 hover:bg-gray-50'>
                          <div class='flex items-center justify-between'>
                            <div class='flex items-center'>
                              <div class='flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center'>
                                <i class='fas fa-tasks text-indigo-600'></i>
                              </div>
                              <div class='ml-3'>
                                <p class='text-sm font-medium text-indigo-600'>" . htmlspecialchars($task['task_name']) . "</p>
                                <p class='text-xs text-gray-500'>Added on {$created_date}</p>
                              </div>
                            </div>
                            <div>
                              <span class='px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {$status_bg} {$status_color}'>
                                " . ucfirst(str_replace('_', ' ', $task['status'])) . "
                              </span>
                            </div>
                          </div>
                          <div class='mt-2 sm:flex sm:justify-between'>
                            <div class='sm:flex sm:space-x-4'>
                              <p class='flex items-center text-sm text-gray-500'>
                                <i class='flex-shrink-0 mr-1.5 fas fa-map-marker-alt text-gray-400'></i>
                                " . htmlspecialchars($task['task_area']) . "
                              </p>
                              <p class='mt-2 flex items-center text-sm text-gray-500 sm:mt-0'>
                                <i class='flex-shrink-0 mr-1.5 fas fa-calendar text-gray-400'></i>
                                {$from_date} - {$to_date}
                              </p>
                            </div>
                            <div class='mt-2 flex items-center text-sm text-gray-500 sm:mt-0'>
                              <i class='flex-shrink-0 mr-1.5 fas fa-dollar-sign text-gray-400'></i>
                              {$formatted_amount}
                            </div>
                          </div>
                        </div>
                      </li>
                    ";
                  }
                } else {
                  echo "
                    <li class='px-4 py-6 text-center text-gray-500'>
                      <p>No tasks found. Start by uploading your first task!</p>
                    </li>
                  ";
                }
                $stmt->close();
              } else {
                echo "
                  <li class='px-4 py-6 text-center text-gray-500'>
                    <p>Unable to retrieve tasks. Please try again later.</p>
                  </li>
                ";
              }
              $conn->close();
              ?>
            </ul>
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 text-right">
              <a href="view-all-tasks.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                View all tasks <i class="fas fa-arrow-right ml-1"></i>
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
          <a href="index.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-home w-5 h-5 mr-3"></i>
            <span>Dashboard</span>
          </a>
          <a href="user-orders.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
            <span>My Orders</span>
          </a>
          <a href="task-upload.php" class="flex items-center px-4 py-2 bg-indigo-700 text-white rounded-lg">
            <i class="fas fa-tasks w-5 h-5 mr-3"></i>
            <span>Upload Task</span>
          </a>
          <!-- Add other nav items here similar to desktop sidebar -->
        </nav>
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

    // File upload preview
    const fileInput = document.getElementById('attachment');
    const fileDisplay = document.getElementById('selected-file');

    if (fileInput) {
      fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
          fileDisplay.textContent = `Selected: ${e.target.files[0].name}`;
        } else {
          fileDisplay.textContent = 'No file selected';
        }
      });
    }

    // Alert dismissal
    const alertCloseButtons = document.querySelectorAll('.alert-close');

    alertCloseButtons.forEach(button => {
      button.addEventListener('click', () => {
        button.closest('.mb-6').style.display = 'none';
      });
    });
  </script>
</body>

</html>