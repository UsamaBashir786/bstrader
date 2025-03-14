<?php
// Include user authentication
require_once "../config/user-auth.php";

// Initialize variables
$task_id = isset($_GET['id']) ? $_GET['id'] : 0;
$status_updated = false;
$error_message = "";
$success_message = "";

// Check if task ID is provided
if (!$task_id) {
  header("Location: view-all-tasks.php");
  exit;
}

// Connect to database
require_once "../config/config.php";

// Process status update if submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
  $new_status = $_POST['new_status'];
  $comment = trim($_POST['comment']);

  // Update the task status
  $update_sql = "UPDATE tasks SET status = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";

  if ($stmt = $conn->prepare($update_sql)) {
    $stmt->bind_param("sii", $new_status, $task_id, $_SESSION["user_id"]);

    if ($stmt->execute()) {
      $status_updated = true;
      $success_message = "Task status updated successfully.";

      // Add a status update record
      $insert_update_sql = "INSERT INTO task_updates (task_id, user_id, update_type, previous_status, new_status, comment) 
                           VALUES (?, ?, 'status_change', ?, ?, ?)";

      if ($update_stmt = $conn->prepare($insert_update_sql)) {
        $update_type = "status_change";
        $previous_status = $_POST['current_status'];

        $update_stmt->bind_param("iisss", $task_id, $_SESSION["user_id"], $previous_status, $new_status, $comment);
        $update_stmt->execute();
        $update_stmt->close();
      }
    } else {
      $error_message = "Error updating task status.";
    }

    $stmt->close();
  } else {
    $error_message = "Error preparing statement.";
  }
}

// Get the task details
$task = null;
$updates = [];

$sql = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
if ($stmt = $conn->prepare($sql)) {
  $stmt->bind_param("ii", $task_id, $_SESSION["user_id"]);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $task = $result->fetch_assoc();
  } else {
    // Task not found or doesn't belong to user
    header("Location: view-all-tasks.php");
    exit;
  }

  $stmt->close();
}

// Get task updates/comments
if ($task) {
  $updates_sql = "SELECT tu.*, u.name as user_name 
                 FROM task_updates tu 
                 JOIN users u ON tu.user_id = u.id 
                 WHERE tu.task_id = ? 
                 ORDER BY tu.created_at DESC";

  if ($stmt = $conn->prepare($updates_sql)) {
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    $updates_result = $stmt->get_result();

    while ($update = $updates_result->fetch_assoc()) {
      $updates[] = $update;
    }

    $stmt->close();
  }
}

// Close connection
$conn->close();

// Format task data for display
$formatted_task_date = date("F j, Y", strtotime($task['task_date']));
$formatted_from_date = date("F j, Y", strtotime($task['from_date']));
$formatted_to_date = date("F j, Y", strtotime($task['to_date']));
$formatted_created_date = date("F j, Y g:i A", strtotime($task['created_at']));
$formatted_amount = number_format($task['amount'], 2);

// Format task status for display
$status_classes = [
  'pending' => 'bg-blue-100 text-blue-800',
  'in_progress' => 'bg-yellow-100 text-yellow-800',
  'completed' => 'bg-green-100 text-green-800',
  'cancelled' => 'bg-red-100 text-red-800'
];

$status_display = ucfirst(str_replace('_', ' ', $task['status']));
$priority_display = ucfirst($task['priority']);
$status_class = $status_classes[$task['status']] ?? 'bg-gray-100 text-gray-800';

// Set priority class
$priority_classes = [
  'low' => 'bg-blue-100 text-blue-800',
  'medium' => 'bg-green-100 text-green-800',
  'high' => 'bg-yellow-100 text-yellow-800',
  'urgent' => 'bg-red-100 text-red-800'
];
$priority_class = $priority_classes[$task['priority']] ?? 'bg-gray-100 text-gray-800';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Task Details</title>
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
            <h1 class="text-2xl font-bold text-gray-900">Task Details</h1>
            <p class="mt-1 text-sm text-gray-600">View and manage task information</p>
          </div>
          <a href="view-all-tasks.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Tasks
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

        <!-- Task Details Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
          <!-- Header -->
          <div class="bg-indigo-600 px-6 py-4">
            <div class="flex justify-between items-center">
              <div>
                <h2 class="text-xl font-bold text-white"><?php echo htmlspecialchars($task['task_name']); ?></h2>
                <p class="mt-1 text-sm text-indigo-200">Task #<?php echo $task['id']; ?> • Created on <?php echo $formatted_created_date; ?></p>
              </div>
              <div class="flex space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                  <?php echo $status_display; ?>
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo $priority_class; ?>">
                  <?php echo $priority_display; ?> Priority
                </span>
              </div>
            </div>
          </div>

          <!-- Task Information -->
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h3 class="text-sm font-medium text-gray-500">Task Area</h3>
                <p class="mt-1 text-base text-gray-900"><?php echo htmlspecialchars($task['task_area']); ?></p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Task Date</h3>
                <p class="mt-1 text-base text-gray-900"><?php echo $formatted_task_date; ?></p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Start Date</h3>
                <p class="mt-1 text-base text-gray-900"><?php echo $formatted_from_date; ?></p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">End Date</h3>
                <p class="mt-1 text-base text-gray-900"><?php echo $formatted_to_date; ?></p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Amount</h3>
                <p class="mt-1 text-base text-gray-900">$<?php echo $formatted_amount; ?></p>
              </div>

              <div>
                <h3 class="text-sm font-medium text-gray-500">Assigned To</h3>
                <p class="mt-1 text-base text-gray-900">
                  <?php echo !empty($task['assigned_to']) ? htmlspecialchars($task['assigned_to']) : 'Not assigned'; ?>
                </p>
              </div>

              <div class="md:col-span-2">
                <h3 class="text-sm font-medium text-gray-500">Target Objectives</h3>
                <p class="mt-1 text-base text-gray-900"><?php echo htmlspecialchars($task['target']); ?></p>
              </div>

              <?php if (!empty($task['description'])): ?>
                <div class="md:col-span-2">
                  <h3 class="text-sm font-medium text-gray-500">Description</h3>
                  <p class="mt-1 text-base text-gray-900 whitespace-pre-line"><?php echo htmlspecialchars($task['description']); ?></p>
                </div>
              <?php endif; ?>

              <?php if (!empty($task['attachment'])): ?>
                <div class="md:col-span-2">
                  <h3 class="text-sm font-medium text-gray-500">Attachment</h3>
                  <div class="mt-2">
                    <a href="../uploads/tasks/<?php echo $task['attachment']; ?>" target="_blank"
                      class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md shadow-sm text-sm font-medium text-indigo-700 bg-white hover:bg-indigo-50">
                      <i class="fas fa-download mr-2"></i>
                      Download Attachment
                    </a>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Update Task Status Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
          <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-900">Update Task Status</h2>
            <p class="mt-1 text-sm text-gray-600">Change the status of this task and add a comment about the update</p>
          </div>

          <div class="p-6">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $task_id); ?>" method="POST">
              <input type="hidden" name="current_status" value="<?php echo $task['status']; ?>">

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label for="new_status" class="block text-sm font-medium text-gray-700 mb-1">New Status</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-flag text-gray-400"></i>
                    </div>
                    <select id="new_status" name="new_status"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                      <option value="pending" <?php echo $task['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                      <option value="in_progress" <?php echo $task['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                      <option value="completed" <?php echo $task['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                      <option value="cancelled" <?php echo $task['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                  </div>
                </div>

                <div class="md:col-span-2">
                  <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Comment (Optional)</label>
                  <div class="relative">
                    <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                      <i class="fas fa-comment text-gray-400"></i>
                    </div>
                    <textarea id="comment" name="comment" rows="2"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Add your comment here..."></textarea>
                  </div>
                </div>
              </div>

              <div class="mt-6">
                <button type="submit" name="update_status"
                  class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  <i class="fas fa-sync-alt mr-2"></i>
                  Update Status
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Task Timeline -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
          <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-900">Task Timeline</h2>
            <p class="mt-1 text-sm text-gray-600">Updates and activity history for this task</p>
          </div>

          <div class="p-6">
            <?php if (count($updates) > 0): ?>
              <div class="flow-root">
                <ul role="list" class="-mb-8">
                  <?php foreach ($updates as $index => $update): ?>
                    <?php
                    $update_time = date("F j, Y g:i A", strtotime($update['created_at']));
                    $update_icon = 'fa-circle-info';
                    $update_color = 'text-blue-500 bg-blue-100';

                    switch ($update['update_type']) {
                      case 'status_change':
                        $update_icon = 'fa-sync-alt';

                        switch ($update['new_status']) {
                          case 'in_progress':
                            $update_color = 'text-yellow-500 bg-yellow-100';
                            break;
                          case 'completed':
                            $update_icon = 'fa-check-circle';
                            $update_color = 'text-green-500 bg-green-100';
                            break;
                          case 'cancelled':
                            $update_icon = 'fa-times-circle';
                            $update_color = 'text-red-500 bg-red-100';
                            break;
                          default:
                            $update_color = 'text-blue-500 bg-blue-100';
                        }
                        break;
                      case 'comment':
                        $update_icon = 'fa-comment';
                        $update_color = 'text-indigo-500 bg-indigo-100';
                        break;
                      case 'attachment':
                        $update_icon = 'fa-paperclip';
                        $update_color = 'text-purple-500 bg-purple-100';
                        break;
                      case 'edit':
                        $update_icon = 'fa-edit';
                        $update_color = 'text-gray-500 bg-gray-100';
                        break;
                    }

                    $isLast = $index === count($updates) - 1;
                    ?>

                    <li>
                      <div class="relative pb-8">
                        <?php if (!$isLast): ?>
                          <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                        <?php endif; ?>

                        <div class="relative flex items-start space-x-3">
                          <div>
                            <div class="relative px-1">
                              <div class="h-10 w-10 rounded-full flex items-center justify-center <?php echo $update_color; ?>">
                                <i class="fas <?php echo $update_icon; ?>"></i>
                              </div>
                            </div>
                          </div>

                          <div class="min-w-0 flex-1">
                            <div>
                              <div class="text-sm">
                                <span class="font-medium text-gray-900"><?php echo htmlspecialchars($update['user_name']); ?></span>
                              </div>
                              <p class="mt-0.5 text-sm text-gray-500"><?php echo $update_time; ?></p>
                            </div>

                            <div class="mt-2">
                              <?php if ($update['update_type'] == 'status_change'): ?>
                                <p class="text-sm text-gray-700">
                                  Changed status from
                                  <span class="font-medium"><?php echo ucfirst(str_replace('_', ' ', $update['previous_status'])); ?></span>
                                  to
                                  <span class="font-medium"><?php echo ucfirst(str_replace('_', ' ', $update['new_status'])); ?></span>
                                </p>
                              <?php endif; ?>

                              <?php if (!empty($update['comment'])): ?>
                                <p class="mt-1 text-sm text-gray-700 whitespace-pre-line bg-gray-50 rounded-md p-3 border border-gray-100">
                                  <?php echo htmlspecialchars($update['comment']); ?>
                                </p>
                              <?php endif; ?>

                              <?php if (!empty($update['attachment'])): ?>
                                <div class="mt-2">
                                  <a href="../uploads/tasks/updates/<?php echo $update['attachment']; ?>" target="_blank"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-paperclip mr-2"></i>
                                    View Attachment
                                  </a>
                                </div>
                              <?php endif; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php else: ?>
              <div class="text-center py-6 text-gray-500">
                <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
                <p>No updates available for this task.</p>
              </div>
            <?php endif; ?>
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