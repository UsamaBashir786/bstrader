<?php
// Include admin authentication
require_once "../config/admin-auth.php";
require_once "../config/config.php";

// Check if task ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect to tasks page if no valid ID
    header('Location: task.php');
    exit;
}

$task_id = $_GET['id'];

// Get task details
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Task not found, redirect to tasks page
    header('Location: task.php');
    exit;
}

$task = $result->fetch_assoc();
$stmt->close();

// Status and priority classes
$status_class = '';
$priority_class = '';

switch ($task['status']) {
    case 'completed':
        $status_class = 'bg-green-100 text-green-800';
        break;
    case 'in_progress':
        $status_class = 'bg-yellow-100 text-yellow-800';
        break;
    case 'cancelled':
        $status_class = 'bg-gray-100 text-gray-800';
        break;
    default: // pending
        $status_class = 'bg-blue-100 text-blue-800';
}

// Check if task is overdue
$is_overdue = false;
if ($task['status'] != 'completed' && $task['status'] != 'cancelled') {
    $to_date = new DateTime($task['to_date']);
    $today = new DateTime();
    
    if ($to_date < $today) {
        $status_class = 'bg-red-100 text-red-800';
        $is_overdue = true;
    }
}

// Priority class
switch ($task['priority']) {
    case 'low':
        $priority_class = 'text-gray-600';
        break;
    case 'medium':
        $priority_class = 'text-blue-600';
        break;
    case 'high':
        $priority_class = 'text-orange-600';
        break;
    case 'urgent':
        $priority_class = 'text-red-600';
        break;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - View Task</title>
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
            <h3 class="text-lg leading-6 font-medium text-gray-900">Task Details</h3>
            <div class="mt-3 flex sm:mt-0 sm:ml-4">
              <a href="task.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-arrow-left mr-2 -ml-1 h-5 w-5"></i>
                Back to Tasks
              </a>
              <a href="edit-task.php?id=<?php echo $task_id; ?>" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-edit mr-2 -ml-1 h-5 w-5"></i>
                Edit Task
              </a>
            </div>
          </div>

          <!-- Task Details Card -->
          <div class="mt-6 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                  <?php echo htmlspecialchars($task['task_name']); ?>
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                  Created on <?php echo date("F j, Y, g:i a", strtotime($task['created_at'])); ?>
                </p>
              </div>
              <div>
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                  <?php 
                    if ($is_overdue) {
                      echo 'Overdue';
                    } else {
                      echo ucfirst(str_replace('_', ' ', $task['status']));
                    }
                  ?>
                </span>
              </div>
            </div>
            <div class="border-t border-gray-200">
              <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    Task Area
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <?php echo htmlspecialchars($task['task_area']); ?>
                  </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    Amount
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <?php echo number_format($task['amount'], 2); ?>
                  </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    Task Date
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <?php echo date("F j, Y", strtotime($task['task_date'])); ?>
                  </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    Date Range
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <?php echo date("F j, Y", strtotime($task['from_date'])); ?> to <?php echo date("F j, Y", strtotime($task['to_date'])); ?>
                  </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    Target
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <?php echo htmlspecialchars($task['target']); ?>
                  </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    Assigned To
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <?php echo !empty($task['assigned_to']) ? htmlspecialchars($task['assigned_to']) : 'Not assigned'; ?>
                  </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    Priority
                  </dt>
                  <dd class="mt-1 text-sm <?php echo $priority_class; ?> sm:mt-0 sm:col-span-2">
                    <?php echo ucfirst($task['priority']); ?>
                  </dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    Description
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <?php echo !empty($task['description']) ? nl2br(htmlspecialchars($task['description'])) : 'No description provided'; ?>
                  </dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">
                    Status Timeline
                  </dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                    <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                      <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                        <div class="w-0 flex-1 flex items-center">
                          <i class="fas fa-clock flex-shrink-0 h-5 w-5 text-gray-400"></i>
                          <span class="ml-2 flex-1 w-0 truncate">
                            Created: <?php echo date("F j, Y, g:i a", strtotime($task['created_at'])); ?>
                          </span>
                        </div>
                      </li>
                      <?php if (!empty($task['updated_at'])): ?>
                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                          <div class="w-0 flex-1 flex items-center">
                            <i class="fas fa-edit flex-shrink-0 h-5 w-5 text-gray-400"></i>
                            <span class="ml-2 flex-1 w-0 truncate">
                              Last Updated: <?php echo date("F j, Y, g:i a", strtotime($task['updated_at'])); ?>
                            </span>
                          </div>
                        </li>
                      <?php endif; ?>
                      <?php if (!empty($task['completed_at'])): ?>
                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                          <div class="w-0 flex-1 flex items-center">
                            <i class="fas fa-check-circle flex-shrink-0 h-5 w-5 text-green-500"></i>
                            <span class="ml-2 flex-1 w-0 truncate">
                              Completed: <?php echo date("F j, Y, g:i a", strtotime($task['completed_at'])); ?>
                            </span>
                          </div>
                        </li>
                      <?php endif; ?>
                    </ul>
                  </dd>
                </div>
                <?php if (!empty($task['attachment'])): ?>
                  <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                      Attachments
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                        <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                          <div class="w-0 flex-1 flex items-center">
                            <i class="fas fa-paperclip flex-shrink-0 h-5 w-5 text-gray-400"></i>
                            <span class="ml-2 flex-1 w-0 truncate">
                              <?php echo $task['attachment']; ?>
                            </span>
                          </div>
                          <div class="ml-4 flex-shrink-0">
                            <a href="../uploads/<?php echo $task['attachment']; ?>" target="_blank" class="font-medium text-primary-600 hover:text-primary-500">
                              Download
                            </a>
                          </div>
                        </li>
                      </ul>
                    </dd>
                  </div>
                <?php endif; ?>
                <?php if (!empty($task['comments'])): ?>
                  <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">
                      Comments
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                      <?php echo nl2br(htmlspecialchars($task['comments'])); ?>
                    </dd>
                  </div>
                <?php endif; ?>
              </dl>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="mt-6 flex flex-col sm:flex-row sm:justify-end gap-3">
            <a href="?update_status=<?php echo $task_id; ?>&status=pending" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
              Mark as Pending
            </a>
            <a href="?update_status=<?php echo $task_id; ?>&status=in_progress" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
              Mark as In Progress
            </a>
            <a href="?update_status=<?php echo $task_id; ?>&status=completed" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
              Mark as Completed
            </a>
            <a href="?update_status=<?php echo $task_id; ?>&status=cancelled" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
              Mark as Cancelled
            </a>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script>
    // Display current date
    document.addEventListener('DOMContentLoaded', function() {
      const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
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