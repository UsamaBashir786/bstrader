<?php
// Include user authentication
require_once "../config/user-auth.php";

// Connect to database
require_once "../config/config.php";

// Initialize variables
$error_message = "";
$success_message = "";

// Get user profile data
$user_data = null;

$sql = "SELECT * FROM users WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
  $stmt->bind_param("i", $_SESSION["user_id"]);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user_data = $result->fetch_assoc();
  } else {
    // User not found
    $error_message = "Error: User profile not found.";
  }

  $stmt->close();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
  // Get form data
  $name = trim($_POST['name']);
  $phone = trim($_POST['phone']);
  $address = trim($_POST['address']);

  // Validate name
  if (empty($name)) {
    $error_message = "Full name is required.";
  }

  // Validate phone
  if (empty($phone)) {
    $error_message = "Phone number is required.";
  }

  // Validate address
  if (empty($address)) {
    $error_message = "Address is required.";
  }

  // If no errors, update the profile
  if (empty($error_message)) {
    $update_sql = "UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?";

    if ($update_stmt = $conn->prepare($update_sql)) {
      $update_stmt->bind_param("sssi", $name, $phone, $address, $_SESSION["user_id"]);

      if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully.";

        // Update session name
        $_SESSION["name"] = $name;

        // Update the user data
        $user_data['name'] = $name;
        $user_data['phone'] = $phone;
        $user_data['address'] = $address;
      } else {
        $error_message = "Error updating profile: " . $conn->error;
      }

      $update_stmt->close();
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
  <title>BS Traders - Edit Profile</title>
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
            <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
            <p class="mt-1 text-sm text-gray-600">Update your account information</p>
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

        <!-- Edit Profile Form -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
          <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-900">Personal Information</h2>
            <p class="mt-1 text-sm text-gray-500">Update your personal details</p>
          </div>

          <div class="p-6">
            <form action="edit-profile.php" method="POST">
              <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <!-- Full Name -->
                <div class="sm:col-span-3">
                  <label for="name" class="block text-sm font-medium text-gray-700">
                    Full Name <span class="text-red-500">*</span>
                  </label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user_data['name']); ?>"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      required>
                  </div>
                </div>

                <!-- Email Address (Read Only) -->
                <div class="sm:col-span-3">
                  <label for="email" class="block text-sm font-medium text-gray-700">
                    Email Address
                  </label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user_data['email']); ?>"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-500"
                      readonly disabled>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">Email cannot be changed. Contact support for assistance.</p>
                </div>

                <!-- Phone Number -->
                <div class="sm:col-span-3">
                  <label for="phone" class="block text-sm font-medium text-gray-700">
                    Phone Number <span class="text-red-500">*</span>
                  </label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-phone text-gray-400"></i>
                    </div>
                    <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      required>
                  </div>
                </div>

                <!-- CNIC (Read Only) -->
                <div class="sm:col-span-3">
                  <label for="cnic" class="block text-sm font-medium text-gray-700">
                    CNIC Number
                  </label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-id-card text-gray-400"></i>
                    </div>
                    <input type="text" name="cnic" id="cnic" value="<?php echo htmlspecialchars($user_data['cnic']); ?>"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-500"
                      readonly disabled>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">CNIC cannot be changed. Contact support for assistance.</p>
                </div>

                <!-- Address -->
                <div class="sm:col-span-6">
                  <label for="address" class="block text-sm font-medium text-gray-700">
                    Address <span class="text-red-500">*</span>
                  </label>
                  <div class="mt-1 relative">
                    <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                      <i class="fas fa-home text-gray-400"></i>
                    </div>
                    <textarea name="address" id="address" rows="3"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      required><?php echo htmlspecialchars($user_data['address']); ?></textarea>
                  </div>
                </div>

                <!-- Contract Dates (Read Only) -->
                <div class="sm:col-span-3">
                  <label for="contract_start" class="block text-sm font-medium text-gray-700">
                    Contract Start Date
                  </label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-calendar-alt text-gray-400"></i>
                    </div>
                    <input type="date" name="contract_start" id="contract_start" value="<?php echo $user_data['contract_start']; ?>"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-500"
                      readonly disabled>
                  </div>
                </div>

                <div class="sm:col-span-3">
                  <label for="contract_end" class="block text-sm font-medium text-gray-700">
                    Contract End Date
                  </label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-calendar-alt text-gray-400"></i>
                    </div>
                    <input type="date" name="contract_end" id="contract_end" value="<?php echo $user_data['contract_end']; ?>"
                      class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-500"
                      readonly disabled>
                  </div>
                </div>
              </div>

              <div class="mt-6 pt-6 border-t border-gray-200 flex items-center justify-between">
                <p class="text-sm text-gray-500">Fields marked with <span class="text-red-500">*</span> are required</p>

                <div class="flex space-x-3">
                  <a href="user-profile.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                  </a>
                  <button type="submit" name="update_profile" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i>
                    Save Changes
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Password Change Section -->
        <div class="mt-6 bg-white rounded-xl shadow-md overflow-hidden">
          <div class="border-b border-gray-200 px-6 py-4">
            <h2 class="text-lg font-medium text-gray-900">Change Password</h2>
            <p class="mt-1 text-sm text-gray-500">Update your account password</p>
          </div>

          <div class="p-6">
            <p class="text-sm text-gray-500 mb-4">
              For security reasons, password changes are handled on a separate page.
            </p>
            <a href="change-password.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              <i class="fas fa-lock mr-2"></i>
              Change Password
            </a>
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