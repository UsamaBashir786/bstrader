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

// Process profile image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile_image'])) {
  $target_dir = "../uploads/profile/";

  // Create directory if it doesn't exist
  if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
  }

  if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] == 0) {
    $allowed_types = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
    $file_name = $_FILES["profile_pic"]["name"];
    $file_type = $_FILES["profile_pic"]["type"];
    $file_size = $_FILES["profile_pic"]["size"];

    // Verify file extension
    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if (!array_key_exists($ext, $allowed_types) || !in_array($file_type, $allowed_types)) {
      $error_message = "Error: Please select a valid file format (JPG, JPEG, PNG).";
    }

    // Verify file size - 5MB maximum
    $maxsize = 5 * 1024 * 1024;
    if ($file_size > $maxsize) {
      $error_message = "Error: File size is larger than the allowed limit (5MB).";
    }

    // If no errors, proceed with upload
    if (empty($error_message)) {
      // Create a unique filename
      $new_filename = uniqid() . "." . $ext;
      $target_file = $target_dir . $new_filename;

      if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        // Update the user's profile picture in the database
        $update_sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
        if ($update_stmt = $conn->prepare($update_sql)) {
          $update_stmt->bind_param("si", $new_filename, $_SESSION["user_id"]);

          if ($update_stmt->execute()) {
            $success_message = "Profile picture updated successfully.";

            // Update the user data
            $user_data['profile_pic'] = $new_filename;
          } else {
            $error_message = "Error updating profile picture in database.";
          }

          $update_stmt->close();
        }
      } else {
        $error_message = "Error: There was a problem uploading your file. Please try again.";
      }
    }
  } else {
    $error_message = "Error: Please select a file to upload.";
  }
}

// Get user statistics
$order_count = 0;
$invoice_count = 0;
$task_count = 0;

// Count tasks (we know this table exists)
$sql = "SELECT COUNT(*) as count FROM tasks WHERE user_id = ?";
if ($stmt = $conn->prepare($sql)) {
  $stmt->bind_param("i", $_SESSION["user_id"]);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    $task_count = $row['count'];
  }

  $stmt->close();
}

// Note: The orders and invoices tables might not exist yet, so we'll use placeholder values
// instead of querying those tables to avoid fatal errors

// Get account creation date
$account_created = isset($user_data['created_at']) ? date("F j, Y", strtotime($user_data['created_at'])) : "N/A";

// Format contract dates
$contract_start = isset($user_data['contract_start']) ? date("F j, Y", strtotime($user_data['contract_start'])) : "N/A";
$contract_end = isset($user_data['contract_end']) ? date("F j, Y", strtotime($user_data['contract_end'])) : "N/A";

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - My Profile</title>
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
            <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
            <p class="mt-1 text-sm text-gray-600">View and manage your account information</p>
          </div>
          <a href="user-settings.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-cog mr-2"></i>
            Account Settings
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

        <!-- Profile Overview -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
          <!-- Profile Card -->
          <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-indigo-600 px-6 py-12 flex flex-col items-center">
              <div class="relative mb-4 group">
                <?php if (!empty($user_data['profile_pic'])): ?>
                  <img class="h-24 w-24 rounded-full border-4 border-white" src="../uploads/profile/<?php echo $user_data['profile_pic']; ?>" alt="Profile Picture">
                <?php else: ?>
                  <div class="h-24 w-24 rounded-full bg-indigo-500 border-4 border-white flex items-center justify-center text-3xl font-bold text-white">
                    <?php echo substr($user_data['name'], 0, 1); ?>
                  </div>
                <?php endif; ?>

                <button type="button" onclick="document.getElementById('profile-image-upload').click()" class="absolute bottom-0 right-0 h-8 w-8 bg-white rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100">
                  <i class="fas fa-camera"></i>
                </button>

                <form id="profile-image-form" action="user-profile.php" method="POST" enctype="multipart/form-data" class="hidden">
                  <input type="file" id="profile-image-upload" name="profile_pic" accept="image/*" onchange="document.getElementById('profile-image-form').submit()">
                  <input type="hidden" name="update_profile_image" value="1">
                </form>
              </div>

              <h2 class="text-xl font-bold text-white"><?php echo htmlspecialchars($user_data['name']); ?></h2>
              <p class="text-indigo-200 mt-1"><?php echo htmlspecialchars($user_data['email']); ?></p>

              <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-800 text-white">
                <i class="fas fa-check-circle mr-1"></i>
                Customer Account
              </div>
            </div>

            <div class="p-6">
              <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                  <div class="text-2xl font-bold text-indigo-600"><?php echo $order_count; ?></div>
                  <div class="text-xs text-gray-500 mt-1">Orders</div>
                </div>
                <div>
                  <div class="text-2xl font-bold text-indigo-600"><?php echo $invoice_count; ?></div>
                  <div class="text-xs text-gray-500 mt-1">Invoices</div>
                </div>
                <div>
                  <div class="text-2xl font-bold text-indigo-600"><?php echo $task_count; ?></div>
                  <div class="text-xs text-gray-500 mt-1">Tasks</div>
                </div>
              </div>

              <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center py-2">
                  <i class="fas fa-calendar-check text-gray-400 w-5"></i>
                  <span class="ml-4 text-sm text-gray-500">Member since <?php echo $account_created; ?></span>
                </div>
                <div class="flex items-center py-2">
                  <i class="fas fa-phone text-gray-400 w-5"></i>
                  <span class="ml-4 text-sm text-gray-900"><?php echo htmlspecialchars($user_data['phone']); ?></span>
                </div>
                <div class="flex items-center py-2">
                  <i class="fas fa-id-card text-gray-400 w-5"></i>
                  <span class="ml-4 text-sm text-gray-900"><?php echo htmlspecialchars($user_data['cnic']); ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Account Information -->
          <div class="bg-white rounded-xl shadow-md overflow-hidden lg:col-span-2">
            <div class="border-b border-gray-200 px-6 py-4">
              <h2 class="text-lg font-medium text-gray-900">Account Information</h2>
            </div>

            <div class="p-6">
              <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user_data['name']); ?></dd>
                </div>

                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user_data['email']); ?></dd>
                </div>

                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user_data['phone']); ?></dd>
                </div>

                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">CNIC Number</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user_data['cnic']); ?></dd>
                </div>

                <div class="sm:col-span-2">
                  <dt class="text-sm font-medium text-gray-500">Address</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($user_data['address']); ?></dd>
                </div>

                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Contract Start Date</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo $contract_start; ?></dd>
                </div>

                <div class="sm:col-span-1">
                  <dt class="text-sm font-medium text-gray-500">Contract End Date</dt>
                  <dd class="mt-1 text-sm text-gray-900"><?php echo $contract_end; ?></dd>
                </div>

                <div class="sm:col-span-2">
                  <dt class="text-sm font-medium text-gray-500">Account Status</dt>
                  <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      <i class="fas fa-check-circle mr-1"></i>
                      Active
                    </span>
                  </dd>
                </div>
              </dl>

              <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end">
                <a href="edit-profile.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                  <i class="fas fa-edit mr-2"></i>
                  Edit Profile
                </a>
              </div>
            </div>
          </div>

          <!-- Recent Activity -->
          <div class="bg-white rounded-xl shadow-md overflow-hidden lg:col-span-3">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
              <h2 class="text-lg font-medium text-gray-900">Recent Activity</h2>
              <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">View all activity</a>
            </div>

            <div class="divide-y divide-gray-200">
              <!-- Activity items -->
              <div class="p-6 hover:bg-gray-50">
                <div class="flex items-start">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500">
                    <i class="fas fa-shopping-cart"></i>
                  </div>
                  <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                      <h3 class="text-sm font-medium text-gray-900">New Order Placed</h3>
                      <p class="text-sm text-gray-500">2 days ago</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">
                      You placed order #ORD-1234 for $1,250.00
                    </p>
                  </div>
                </div>
              </div>

              <div class="p-6 hover:bg-gray-50">
                <div class="flex items-start">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-500">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                      <h3 class="text-sm font-medium text-gray-900">Task Completed</h3>
                      <p class="text-sm text-gray-500">1 week ago</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">
                      Task "Website Redesign" was marked as completed
                    </p>
                  </div>
                </div>
              </div>

              <div class="p-6 hover:bg-gray-50">
                <div class="flex items-start">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-500">
                    <i class="fas fa-file-invoice"></i>
                  </div>
                  <div class="ml-4 flex-1">
                    <div class="flex items-center justify-between">
                      <h3 class="text-sm font-medium text-gray-900">Invoice Paid</h3>
                      <p class="text-sm text-gray-500">2 weeks ago</p>
                    </div>
                    <p class="mt-1 text-sm text-gray-600">
                      You paid invoice #INV-5678 for $850.00
                    </p>
                  </div>
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
          <a href="user-orders.php" class="flex items-center px-4 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700 hover:text-white">
            <i class="fas fa-shopping-cart w-5 h-5 mr-3"></i>
            <span>My Orders</span>
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