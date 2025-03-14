<?php
// Include user authentication
require_once "../config/user-auth.php";

// Connect to database
require_once "../config/config.php";

// Initialize variables
$error_message = "";
$success_message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
  $current_password = trim($_POST['current_password']);
  $new_password = trim($_POST['new_password']);
  $confirm_password = trim($_POST['confirm_password']);

  // Validate current password
  if (empty($current_password)) {
    $error_message = "Current password is required.";
  }

  // Validate new password
  if (empty($new_password)) {
    $error_message = "New password is required.";
  } elseif (strlen($new_password) < 6) {
    $error_message = "New password must be at least 6 characters long.";
  }

  // Validate password confirmation
  if (empty($confirm_password)) {
    $error_message = "Please confirm your new password.";
  } elseif ($new_password !== $confirm_password) {
    $error_message = "New passwords do not match.";
  }

  // If no errors, verify current password and update
  if (empty($error_message)) {
    // Get the user's current hashed password
    $sql = "SELECT password FROM users WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
      $stmt->bind_param("i", $_SESSION["user_id"]);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify current password
        if (password_verify($current_password, $user['password'])) {
          // Hash the new password
          $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

          // Update the password
          $update_sql = "UPDATE users SET password = ? WHERE id = ?";

          if ($update_stmt = $conn->prepare($update_sql)) {
            $update_stmt->bind_param("si", $hashed_password, $_SESSION["user_id"]);

            if ($update_stmt->execute()) {
              $success_message = "Your password has been updated successfully.";

              // Clear the form fields
              $current_password = $new_password = $confirm_password = "";
            } else {
              $error_message = "Error updating password: " . $conn->error;
            }

            $update_stmt->close();
          } else {
            $error_message = "Error preparing statement: " . $conn->error;
          }
        } else {
          $error_message = "Current password is incorrect.";
        }
      } else {
        $error_message = "User not found.";
      }

      $stmt->close();
    } else {
      $error_message = "Error preparing statement: " . $conn->error;
    }
  }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Change Password</title>
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
            <h1 class="text-2xl font-bold text-gray-900">Change Password</h1>
            <p class="mt-1 text-sm text-gray-600">Update your account password</p>
          </div>
          <a href="user-profile.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Profile
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

        <!-- Change Password Form -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
          <div class="bg-indigo-600 px-6 py-4">
            <h2 class="text-lg font-medium text-white">Password Security</h2>
            <p class="mt-1 text-sm text-indigo-200">Create a strong password to protect your account</p>
          </div>

          <div class="p-6">
            <form action="change-password.php" method="POST">
              <div class="space-y-6">
                <!-- Current Password -->
                <div>
                  <label for="current_password" class="block text-sm font-medium text-gray-700">
                    Current Password <span class="text-red-500">*</span>
                  </label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="current_password" id="current_password"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      required>
                  </div>
                </div>

                <!-- New Password -->
                <div>
                  <label for="new_password" class="block text-sm font-medium text-gray-700">
                    New Password <span class="text-red-500">*</span>
                  </label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-key text-gray-400"></i>
                    </div>
                    <input type="password" name="new_password" id="new_password"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      required>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">Must be at least 6 characters long</p>
                </div>

                <!-- Confirm New Password -->
                <div>
                  <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                    Confirm New Password <span class="text-red-500">*</span>
                  </label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-key text-gray-400"></i>
                    </div>
                    <input type="password" name="confirm_password" id="confirm_password"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      required>
                  </div>
                </div>
              </div>

              <!-- Password Requirements -->
              <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-blue-800">Password Requirements</h3>
                <ul class="mt-2 text-sm text-blue-700 space-y-1">
                  <li class="flex items-center">
                    <i class="fas fa-check-circle mr-2 text-blue-500"></i>
                    At least 6 characters long
                  </li>
                  <li class="flex items-center">
                    <i class="fas fa-check-circle mr-2 text-blue-500"></i>
                    Mix of letters, numbers, and symbols recommended
                  </li>
                  <li class="flex items-center">
                    <i class="fas fa-check-circle mr-2 text-blue-500"></i>
                    Avoid using personal information
                  </li>
                </ul>
              </div>

              <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                <a href="user-profile.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  Cancel
                </a>
                <button type="submit" name="change_password" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  <i class="fas fa-save mr-2"></i>
                  Change Password
                </button>
              </div>
            </form>
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
          <a href="user-profile.php" class="flex items-center px-4 py-2 bg-indigo-700 text-white rounded-lg">
            <i class="fas fa-user-circle w-5 h-5 mr-3"></i>
            <span>My Profile</span>
          </a>
          <!-- Add other mobile menu items here -->
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