<?php
// Include admin authentication
require_once "../config/admin-auth.php";
require_once "../config/config.php";

// Check if task ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  // Redirect to tasks page if no valid ID
  header('Location: tasks.php');
  exit;
}

$task_id = $_GET['id'];
$user_id = $_SESSION['user_id']; // Make sure this matches your authentication system

// Get task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  // Task not found, redirect to tasks page
  header('Location: tasks.php');
  exit;
}

$task = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Edit Task</title>
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
    <?php include 'includes/sidebar.php' ?>
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
                        <!-- Admin icon SVG -->
                        <circle cx="100" cy="100" r="90" fill="#3f51b5" />
                        <path d="M100 30 L150 100 L130 140 H70 L50 100 Z" fill="none" stroke="white" stroke-width="6" stroke-linejoin="round" />
                        <line x1="70" y1="80" x2="130" y2="80" stroke="white" stroke-width="6" stroke-linecap="round" />
                        <line x1="80" y1="100" x2="120" y2="100" stroke="white" stroke-width="6" stroke-linecap="round" />
                        <line x1="90" y1="120" x2="110" y2="120" stroke="white" stroke-width="6" stroke-linecap="round" />
                        <path d="M70 60 L85 45 L100 60 L115 45 L130 60" fill="none" stroke="white" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="100" cy="100" r="90" fill="none" stroke="white" stroke-width="2" opacity="0.3" />
                        <circle cx="100" cy="100" r="85" fill="none" stroke="white" stroke-width="1" opacity="0.2" />
                      </svg>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Task</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <a href="tasks.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-arrow-left mr-2 -ml-1 h-5 w-5"></i>
                Back to Tasks
              </a>
            </div>
          </div>

          <!-- Edit Task Form -->
          <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <form action="process-task.php" method="POST" enctype="multipart/form-data">
              <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">

              <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                  <div>
                    <label for="task_name" class="block text-sm font-medium text-gray-700">Task Name</label>
                    <input type="text" name="task_name" id="task_name" value="<?php echo htmlspecialchars($task['task_name']); ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="task_date" class="block text-sm font-medium text-gray-700">Task Date</label>
                    <input type="date" name="task_date" id="task_date" value="<?php echo $task['task_date']; ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="task_area" class="block text-sm font-medium text-gray-700">Task Area</label>
                    <input type="text" name="task_area" id="task_area" value="<?php echo htmlspecialchars($task['task_area']); ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                    <input type="number" name="amount" id="amount" step="0.01" value="<?php echo $task['amount']; ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="from_date" class="block text-sm font-medium text-gray-700">From Date</label>
                    <input type="date" name="from_date" id="from_date" value="<?php echo $task['from_date']; ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="to_date" class="block text-sm font-medium text-gray-700">To Date</label>
                    <input type="date" name="to_date" id="to_date" value="<?php echo $task['to_date']; ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="target" class="block text-sm font-medium text-gray-700">Target</label>
                    <input type="text" name="target" id="target" value="<?php echo htmlspecialchars($task['target']); ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                  </div>
                  <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                    <select name="priority" id="priority" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                      <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                      <option value="medium" <?php echo $task['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                      <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                      <option value="urgent" <?php echo $task['priority'] === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                    </select>
                  </div>
                  <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assigned To</label>
                    <input type="text" name="assigned_to" id="assigned_to" value="<?php echo htmlspecialchars($task['assigned_to'] ?? ''); ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                  </div>
                  <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                      <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                      <option value="in_progress" <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                      <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                      <option value="cancelled" <?php echo $task['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                  </div>
                  <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"><?php echo htmlspecialchars($task['description'] ?? ''); ?></textarea>
                  </div>

                  <!-- Current attachment display and new upload -->
                  <div class="sm:col-span-2">
                    <label for="attachment" class="block text-sm font-medium text-gray-700">Attachment</label>
                    <?php if (!empty($task['attachment'])): ?>
                      <div class="mt-1 flex items-center">
                        <span class="mr-2 text-sm text-gray-500">Current attachment:</span>
                        <a href="../uploads/<?php echo $task['attachment']; ?>" target="_blank" class="text-primary-600 hover:text-primary-900">
                          <i class="fas fa-paperclip mr-1"></i>
                          <?php echo $task['attachment']; ?>
                        </a>
                      </div>
                    <?php endif; ?>
                    <div class="mt-1">
                      <input type="file" name="attachment" id="attachment" class="focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                      <p class="mt-1 text-sm text-gray-500">Leave empty to keep current attachment. Upload new file to replace.</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  Save Changes
                </button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    // Display current date
    document.addEventListener('DOMContentLoaded', function() {
      const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      };
      document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', options);

      // Initialize user menu dropdown
      const userMenuButton = document.getElementById('user-menu-button');
      const userDropdown = document.getElementById('user-dropdown');

      if (userMenuButton && userDropdown) {
        userMenuButton.addEventListener('click', function() {
          userDropdown.classList.toggle('hidden');
        });

        // Close the dropdown when clicking outside
        document.addEventListener('click', function(event) {
          if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
            userDropdown.classList.add('hidden');
          }
        });
      }

      // Sidebar toggle for mobile
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebar = document.querySelector('.sidebar');

      if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
          sidebar.classList.toggle('hidden');
        });
      }
    });
  </script>
</body>

</html>