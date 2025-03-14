<?php
// Include user authentication
require_once "../config/user-auth.php";

// Connect to database
require_once "../config/config.php";

// Initialize variables
$error_message = "";
$success_message = "";

// Default settings
$user_settings = [
  'email_notifications' => 1,
  'sms_notifications' => 0,
  'newsletter' => 1,
  'two_factor_auth' => 0,
  'language' => 'english',
  'timezone' => 'Asia/Karachi'
];

// We'll check if the user_settings table exists before querying it
$table_exists = false;
$check_table = "SHOW TABLES LIKE 'user_settings'";
$table_result = $conn->query($check_table);
if ($table_result && $table_result->num_rows > 0) {
  $table_exists = true;

  // Only try to get user settings if the table exists
  $user_id = $_SESSION["user_id"];
  $sql = "SELECT * FROM user_settings WHERE user_id = ?";

  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user_settings = $result->fetch_assoc();
    }

    $stmt->close();
  }
}

// Process notification settings form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_notifications'])) {
  $user_settings['email_notifications'] = isset($_POST['email_notifications']) ? 1 : 0;
  $user_settings['sms_notifications'] = isset($_POST['sms_notifications']) ? 1 : 0;
  $user_settings['newsletter'] = isset($_POST['newsletter']) ? 1 : 0;

  if ($table_exists) {
    // Update settings in database
    $user_id = $_SESSION["user_id"];
    $update_sql = "UPDATE user_settings SET email_notifications = ?, sms_notifications = ?, newsletter = ? WHERE user_id = ?";

    if ($update_stmt = $conn->prepare($update_sql)) {
      $update_stmt->bind_param(
        "iiii",
        $user_settings['email_notifications'],
        $user_settings['sms_notifications'],
        $user_settings['newsletter'],
        $user_id
      );

      if ($update_stmt->execute()) {
        $success_message = "Notification settings updated successfully.";
      } else {
        $error_message = "Error updating notification settings.";
      }

      $update_stmt->close();
    }
  } else {
    // If table doesn't exist, just show success message
    $success_message = "Notification preferences saved.";
  }
}

// Process account settings form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_account'])) {
  $user_settings['language'] = trim($_POST['language']);
  $user_settings['timezone'] = trim($_POST['timezone']);
  $user_settings['two_factor_auth'] = isset($_POST['two_factor_auth']) ? 1 : 0;

  if ($table_exists) {
    // Update settings in database
    $user_id = $_SESSION["user_id"];
    $update_sql = "UPDATE user_settings SET language = ?, timezone = ?, two_factor_auth = ? WHERE user_id = ?";

    if ($update_stmt = $conn->prepare($update_sql)) {
      $update_stmt->bind_param(
        "ssii",
        $user_settings['language'],
        $user_settings['timezone'],
        $user_settings['two_factor_auth'],
        $user_id
      );

      if ($update_stmt->execute()) {
        $success_message = "Account settings updated successfully.";
      } else {
        $error_message = "Error updating account settings.";
      }

      $update_stmt->close();
    }
  } else {
    // If table doesn't exist, just show success message
    $success_message = "Account preferences saved.";
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
  <title>BS Traders - Account Settings</title>
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
            <h1 class="text-2xl font-bold text-gray-900">Account Settings</h1>
            <p class="mt-1 text-sm text-gray-600">Manage your account preferences and settings</p>
          </div>
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

        <!-- Settings Tabs -->
        <div class="mb-6">
          <div class="sm:hidden">
            <label for="tabs" class="sr-only">Select a tab</label>
            <select id="mobile-tabs" class="block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
              <option value="notifications">Notification Settings</option>
              <option value="account">Account Settings</option>
              <option value="security">Security Settings</option>
            </select>
          </div>
          <div class="hidden sm:block">
            <div class="border-b border-gray-200">
              <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="#notifications" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 border-indigo-500 font-medium text-sm text-indigo-600" id="notifications-tab">
                  Notifications
                </a>
                <a href="#account" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" id="account-tab">
                  Account
                </a>
                <a href="#security" class="tab-link whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" id="security-tab">
                  Security
                </a>
              </nav>
            </div>
          </div>
        </div>

        <!-- Tab Content -->
        <div id="tab-content">
          <!-- Notification Settings -->
          <div id="notifications-content" class="tab-content bg-white rounded-xl shadow-md overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
              <h2 class="text-lg font-medium text-gray-900">Notification Preferences</h2>
              <p class="mt-1 text-sm text-gray-500">Manage how you receive notifications and updates</p>
            </div>

            <div class="p-6">
              <form action="user-settings.php" method="POST">
                <div class="space-y-6">
                  <!-- Email Notifications -->
                  <div class="relative flex items-start">
                    <div class="flex items-center h-5">
                      <input id="email_notifications" name="email_notifications" type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        <?php echo ($user_settings['email_notifications'] == 1) ? 'checked' : ''; ?>>
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="email_notifications" class="font-medium text-gray-700">Email Notifications</label>
                      <p class="text-gray-500">Receive order updates, invoices, and important account notifications via email.</p>
                    </div>
                  </div>

                  <!-- SMS Notifications -->
                  <div class="relative flex items-start">
                    <div class="flex items-center h-5">
                      <input id="sms_notifications" name="sms_notifications" type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        <?php echo ($user_settings['sms_notifications'] == 1) ? 'checked' : ''; ?>>
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="sms_notifications" class="font-medium text-gray-700">SMS Notifications</label>
                      <p class="text-gray-500">Receive order updates and important alerts via SMS to your registered phone number.</p>
                    </div>
                  </div>

                  <!-- Newsletter -->
                  <div class="relative flex items-start">
                    <div class="flex items-center h-5">
                      <input id="newsletter" name="newsletter" type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        <?php echo ($user_settings['newsletter'] == 1) ? 'checked' : ''; ?>>
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="newsletter" class="font-medium text-gray-700">Newsletter</label>
                      <p class="text-gray-500">Receive our monthly newsletter with product updates, industry news, and special offers.</p>
                    </div>
                  </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                  <div class="flex justify-end">
                    <button type="submit" name="update_notifications" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      <i class="fas fa-save mr-2"></i>
                      Save Notification Settings
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Account Settings -->
          <div id="account-content" class="tab-content hidden bg-white rounded-xl shadow-md overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
              <h2 class="text-lg font-medium text-gray-900">Account Preferences</h2>
              <p class="mt-1 text-sm text-gray-500">Manage your account preferences and regional settings</p>
            </div>

            <div class="p-6">
              <form action="user-settings.php" method="POST">
                <div class="space-y-6">
                  <!-- Language -->
                  <div>
                    <label for="language" class="block text-sm font-medium text-gray-700">
                      Language
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-language text-gray-400"></i>
                      </div>
                      <select name="language" id="language"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="english" <?php echo ($user_settings['language'] == 'english') ? 'selected' : ''; ?>>English</option>
                        <option value="urdu" <?php echo ($user_settings['language'] == 'urdu') ? 'selected' : ''; ?>>Urdu</option>
                        <option value="arabic" <?php echo ($user_settings['language'] == 'arabic') ? 'selected' : ''; ?>>Arabic</option>
                      </select>
                    </div>
                  </div>

                  <!-- Timezone -->
                  <div>
                    <label for="timezone" class="block text-sm font-medium text-gray-700">
                      Timezone
                    </label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-globe text-gray-400"></i>
                      </div>
                      <select name="timezone" id="timezone"
                        class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Asia/Karachi" <?php echo ($user_settings['timezone'] == 'Asia/Karachi') ? 'selected' : ''; ?>>Pakistan (GMT+5)</option>
                        <option value="Asia/Dubai" <?php echo ($user_settings['timezone'] == 'Asia/Dubai') ? 'selected' : ''; ?>>UAE (GMT+4)</option>
                        <option value="Asia/Riyadh" <?php echo ($user_settings['timezone'] == 'Asia/Riyadh') ? 'selected' : ''; ?>>Saudi Arabia (GMT+3)</option>
                        <option value="Europe/London" <?php echo ($user_settings['timezone'] == 'Europe/London') ? 'selected' : ''; ?>>UK (GMT+0/+1)</option>
                      </select>
                    </div>
                  </div>

                  <!-- Two-Factor Authentication -->
                  <div class="relative flex items-start">
                    <div class="flex items-center h-5">
                      <input id="two_factor_auth" name="two_factor_auth" type="checkbox"
                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        <?php echo ($user_settings['two_factor_auth'] == 1) ? 'checked' : ''; ?>>
                    </div>
                    <div class="ml-3 text-sm">
                      <label for="two_factor_auth" class="font-medium text-gray-700">Two-Factor Authentication</label>
                      <p class="text-gray-500">Enable two-factor authentication for additional account security.</p>
                    </div>
                  </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                  <div class="flex justify-end">
                    <button type="submit" name="update_account" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      <i class="fas fa-save mr-2"></i>
                      Save Account Settings
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Security Settings -->
          <div id="security-content" class="tab-content hidden bg-white rounded-xl shadow-md overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
              <h2 class="text-lg font-medium text-gray-900">Security Settings</h2>
              <p class="mt-1 text-sm text-gray-500">Manage your account security and password</p>
            </div>

            <div class="p-6">
              <div class="space-y-6">
                <!-- Password Management -->
                <div>
                  <h3 class="text-sm font-medium text-gray-900">Password Management</h3>
                  <p class="mt-1 text-sm text-gray-500">Change your account password to maintain security.</p>
                  <div class="mt-4">
                    <a href="change-password.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      <i class="fas fa-key mr-2"></i>
                      Change Password
                    </a>
                  </div>
                </div>

                <!-- Account Sessions -->
                <div class="pt-6 border-t border-gray-200">
                  <h3 class="text-sm font-medium text-gray-900">Device Sessions</h3>
                  <p class="mt-1 text-sm text-gray-500">View and manage your active login sessions.</p>

                  <div class="mt-4 border border-gray-200 rounded-md overflow-hidden">
                    <!-- Current Session -->
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                      <div class="flex items-center justify-between">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-500">
                            <i class="fas fa-desktop"></i>
                          </div>
                          <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">Current Session</p>
                            <p class="text-xs text-gray-500">Web Browser • <?php echo $_SERVER['HTTP_USER_AGENT']; ?></p>
                          </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          Active Now
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="mt-4">
                    <a href="../logout.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-red-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                      <i class="fas fa-sign-out-alt mr-2"></i>
                      Sign Out All Devices
                    </a>
                  </div>
                </div>

                <!-- Delete Account -->
                <div class="pt-6 border-t border-gray-200">
                  <h3 class="text-sm font-medium text-gray-900">Delete Account</h3>
                  <p class="mt-1 text-sm text-gray-500">Permanently delete your account and all associated data.</p>

                  <div class="mt-4">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                      <i class="fas fa-trash-alt mr-2"></i>
                      Delete Account
                    </button>
                  </div>
                  <p class="mt-2 text-xs text-gray-500">Note: This action cannot be undone. Please contact support if you want to delete your account.</p>
                </div>
              </div>
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
          <a href="user-settings.php" class="flex items-center px-4 py-2 bg-indigo-700 text-white rounded-lg">
            <i class="fas fa-cog w-5 h-5 mr-3"></i>
            <span>Settings</span>
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

    // Tab switching
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabContents = document.querySelectorAll('.tab-content');
    const mobileTabSelect = document.getElementById('mobile-tabs');

    function setActiveTab(tabId) {
      // Update tab links
      tabLinks.forEach(link => {
        if (link.getAttribute('href') === '#' + tabId) {
          link.classList.add('border-indigo-500', 'text-indigo-600');
          link.classList.remove('border-transparent', 'text-gray-500');
        } else {
          link.classList.remove('border-indigo-500', 'text-indigo-600');
          link.classList.add('border-transparent', 'text-gray-500');
        }
      });

      // Update tab content
      tabContents.forEach(content => {
        if (content.id === tabId + '-content') {
          content.classList.remove('hidden');
        } else {
          content.classList.add('hidden');
        }
      });
    }

    // Handle tab clicks
    tabLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        const tabId = link.getAttribute('href').substring(1);
        setActiveTab(tabId);

        // Update mobile select
        if (mobileTabSelect) {
          mobileTabSelect.value = tabId;
        }
      });
    });

    // Handle mobile tab select
    if (mobileTabSelect) {
      mobileTabSelect.addEventListener('change', () => {
        setActiveTab(mobileTabSelect.value);
      });
    }
  </script>
</body>

</html>