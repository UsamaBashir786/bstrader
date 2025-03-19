<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'bs_trader');

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: expense.php");
  exit();
}

$id = $_GET['id'];

// Handle form submission for updating expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_expense'])) {
  $title = $_POST['expense-title'];
  $category = $_POST['category'];
  $amount = $_POST['amount'];
  $date = $_POST['expense-date'];
  $paid_by = $_POST['paid-by'];
  $status = $_POST['status'];
  $payment_method = $_POST['payment-method'];
  $notes = $_POST['notes'];

  // Handle file upload if a file was submitted
  $receipt_path = $_POST['existing_receipt'];

  if (isset($_FILES['file-upload']) && $_FILES['file-upload']['error'] == 0) {
    $upload_dir = 'uploads/receipts/';

    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
      mkdir($upload_dir, 0777, true);
    }

    $file_name = time() . '_' . basename($_FILES['file-upload']['name']);
    $target_path = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['file-upload']['tmp_name'], $target_path)) {
      $receipt_path = $target_path;
    }
  }

  $stmt = $conn->prepare("UPDATE expenses SET 
                            title = ?, 
                            category = ?, 
                            amount = ?, 
                            date = ?, 
                            paid_by = ?, 
                            status = ?, 
                            payment_method = ?, 
                            notes = ?, 
                            receipt_path = ? 
                            WHERE id = ?");

  $stmt->bind_param("ssdssssssi", $title, $category, $amount, $date, $paid_by, $status, $payment_method, $notes, $receipt_path, $id);

  if ($stmt->execute()) {
    header("Location: expense.php?updated=1");
    exit();
  } else {
    $error_message = "Error updating expense: " . $stmt->error;
  }

  $stmt->close();
}

// Get expense data
$stmt = $conn->prepare("SELECT * FROM expenses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header("Location: expense.php");
  exit();
}

$expense = $result->fetch_assoc();
$stmt->close();

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BS Traders - Edit Expense</title>
  <link rel="stylesheet" href="../src/output.css">
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50: "#e6f1ff",
              100: "#cce3ff",
              200: "#99c7ff",
              300: "#66aaff",
              400: "#338eff",
              500: "#0072ff",
              600: "#005bcc",
              700: "#004499",
              800: "#002e66",
              900: "#001733",
            },
          },
        },
      },
    };
  </script>
</head>

<body class="bg-gray-50">
  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <aside class="hidden md:flex md:flex-shrink-0">
      <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow pt-5 overflow-y-auto bg-primary-700 border-r">
          <div class="flex items-center flex-shrink-0 px-4">
            <span class="text-2xl font-bold text-white">BS Traders</span>
          </div>
          <div class="mt-5 flex-grow flex flex-col">
            <nav class="flex-1 px-2 space-y-1 bg-primary-700">
              <a href="index.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-home mr-3 h-6 w-6"></i>
                Dashboard
              </a>
              <a href="employee.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-users mr-3 h-6 w-6"></i>
                Employee Management
              </a>
              <a href="user.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-user-shield mr-3 h-6 w-6"></i>
                User Management
              </a>
              <a href="salary.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-money-bill-wave mr-3 h-6 w-6"></i>
                Salary Management
              </a>
              <a href="expense.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 bg-primary-800">
                <i class="fas fa-file-invoice-dollar mr-3 h-6 w-6"></i>
                Expense Management
              </a>
              <a href="task.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-tasks mr-3 h-6 w-6"></i>
                Task Management
              </a>
              <a href="reports.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-chart-bar mr-3 h-6 w-6"></i>
                Reports
              </a>
              <a href="settings.php" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-primary-100 hover:bg-primary-600 hover:text-white">
                <i class="fas fa-cog mr-3 h-6 w-6"></i>
                Settings
              </a>
            </nav>
          </div>
          <div class="flex-shrink-0 flex border-t border-primary-800 p-4">
            <a href="profile.php" class="flex-shrink-0 group block">
              <div class="mt-6 flex justify-end">
                <a
                  href="expense.php"
                  class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  Cancel
                </a>
                <button
                  type="submit"
                  name="update_expense"
                  value="1"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                  Update Expense
                </button>
              </div>
              </form>
          </div>
        </div>
        </main>
      </div>
  </div>
  <div class="flex items-center">
    <div>
      <svg class="inline-block h-10 w-10 rounded-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
        <!-- Background Circle -->
        <circle cx="100" cy="100" r="90" fill="#3f51b5" />

        <!-- Admin Icon - Stylized "A" with shield/dashboard elements -->
        <path d="M100 30 L150 100 L130 140 H70 L50 100 Z" fill="none" stroke="white" stroke-width="6" stroke-linejoin="round" />

        <!-- Horizontal bars - representing dashboard/admin panel -->
        <line x1="70" y1="80" x2="130" y2="80" stroke="white" stroke-width="6" stroke-linecap="round" />
        <line x1="80" y1="100" x2="120" y2="100" stroke="white" stroke-width="6" stroke-linecap="round" />
        <line x1="90" y1="120" x2="110" y2="120" stroke="white" stroke-width="6" stroke-linecap="round" />

        <!-- Crown element suggesting admin authority -->
        <path d="M70 60 L85 45 L100 60 L115 45 L130 60" fill="none" stroke="white" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />

        <!-- Outer ring for polish -->
        <circle cx="100" cy="100" r="90" fill="none" stroke="white" stroke-width="2" opacity="0.3" />
        <circle cx="100" cy="100" r="85" fill="none" stroke="white" stroke-width="1" opacity="0.2" />
      </svg>
    </div>
    <div class="ml-3">
      <p class="text-base font-medium text-white">Admin User</p>
      <p class="text-sm font-medium text-primary-200 group-hover:text-primary-100">View profile</p>
    </div>
  </div>
  </a>
  </div>
  </div>
  </div>
  </aside>

  <!-- Main content -->
  <div class="flex flex-col flex-1 overflow-hidden">
    <!-- Top navbar -->
    <nav class="bg-white border-b border-gray-200 flex-shrink-0">
      <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <div class="flex items-center flex-shrink-0 md:hidden">
              <button
                id="sidebarToggle"
                type="button"
                class="text-gray-500 hover:text-gray-900 focus:outline-none">
                <i class="fas fa-bars h-6 w-6"></i>
              </button>
            </div>
            <div
              class="hidden md:ml-6 md:flex md:items-center md:space-x-4">
              <div class="px-3 py-2 text-sm font-medium text-gray-900">
                BS Traders Distributed System
              </div>
            </div>
          </div>
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <span
                class="hidden sm:inline-flex ml-3 items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700">
                Date: <span id="current-date" class="ml-1"></span>
              </span>
            </div>
            <div
              class="hidden md:ml-4 md:flex-shrink-0 md:flex md:items-center">
              <button
                class="p-1 ml-3 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-bell h-6 w-6"></i>
              </button>
              <div class="ml-3 relative">
                <div>
                  <button
                    type="button"
                    class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    id="user-menu-button">
                    <span class="sr-only">Open user menu</span>
                    <svg class="inline-block h-10 w-10 rounded-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
                      <!-- Background Circle -->
                      <circle cx="100" cy="100" r="90" fill="#3f51b5" />

                      <!-- Admin Icon - Stylized "A" with shield/dashboard elements -->
                      <path d="M100 30 L150 100 L130 140 H70 L50 100 Z" fill="none" stroke="white" stroke-width="6" stroke-linejoin="round" />

                      <!-- Horizontal bars - representing dashboard/admin panel -->
                      <line x1="70" y1="80" x2="130" y2="80" stroke="white" stroke-width="6" stroke-linecap="round" />
                      <line x1="80" y1="100" x2="120" y2="100" stroke="white" stroke-width="6" stroke-linecap="round" />
                      <line x1="90" y1="120" x2="110" y2="120" stroke="white" stroke-width="6" stroke-linecap="round" />

                      <!-- Crown element suggesting admin authority -->
                      <path d="M70 60 L85 45 L100 60 L115 45 L130 60" fill="none" stroke="white" stroke-width="6" stroke-linecap="round" stroke-linejoin="round" />

                      <!-- Outer ring for polish -->
                      <circle cx="100" cy="100" r="90" fill="none" stroke="white" stroke-width="2" opacity="0.3" />
                      <circle cx="100" cy="100" r="85" fill="none" stroke="white" stroke-width="1" opacity="0.2" />
                    </svg>
                  </button>
                </div>
                <div
                  id="user-dropdown"
                  class="hidden origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
                  role="menu">
                  <a
                    href="profile.php"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem">Your Profile</a>
                  <a
                    href="settings.php"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem">Settings</a>
                  <a
                    href="logout.php"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    role="menuitem">Logout</a>
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
        <div class="pb-5 border-b border-gray-200 flex items-center justify-between">
          <div>
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Edit Expense
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              Update expense information
            </p>
          </div>
          <div>
            <a
              href="expense.php"
              class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
              <i class="fas fa-arrow-left mr-2 -ml-1 h-5 w-5"></i>
              Back to Expenses
            </a>
          </div>
        </div>

        <?php if (isset($error_message)): ?>
          <div class="mt-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
            <p><?php echo $error_message; ?></p>
          </div>
        <?php endif; ?>

        <!-- Edit Expense Form -->
        <div class="mt-6 bg-white shadow rounded-lg p-6">
          <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="existing_receipt" value="<?php echo htmlspecialchars($expense['receipt_path']); ?>">

            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
              <div class="sm:col-span-6">
                <label
                  for="expense-title"
                  class="block text-sm font-medium text-gray-700">Expense Title</label>
                <div class="mt-1">
                  <input
                    type="text"
                    name="expense-title"
                    id="expense-title"
                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    required
                    value="<?php echo htmlspecialchars($expense['title']); ?>" />
                </div>
              </div>

              <div class="sm:col-span-3">
                <label
                  for="category"
                  class="block text-sm font-medium text-gray-700">Category</label>
                <div class="mt-1">
                  <select
                    id="category"
                    name="category"
                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    required>
                    <option value="">Select Category</option>
                    <option value="Bills & Utilities" <?php echo ($expense['category'] == 'Bills & Utilities') ? 'selected' : ''; ?>>Bills & Utilities</option>
                    <option value="Purchases" <?php echo ($expense['category'] == 'Purchases') ? 'selected' : ''; ?>>Purchases</option>
                    <option value="Tax" <?php echo ($expense['category'] == 'Tax') ? 'selected' : ''; ?>>Tax</option>
                    <option value="Tea & Refreshments" <?php echo ($expense['category'] == 'Tea & Refreshments') ? 'selected' : ''; ?>>Tea & Refreshments</option>
                    <option value="Monthly Expenses" <?php echo ($expense['category'] == 'Monthly Expenses') ? 'selected' : ''; ?>>Monthly Expenses</option>
                  </select>
                </div>
              </div>

              <div class="sm:col-span-3">
                <label
                  for="expense-date"
                  class="block text-sm font-medium text-gray-700">Date</label>
                <div class="mt-1">
                  <input
                    type="date"
                    name="expense-date"
                    id="expense-date"
                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    required
                    value="<?php echo htmlspecialchars($expense['date']); ?>" />
                </div>
              </div>

              <div class="sm:col-span-3">
                <label
                  for="amount"
                  class="block text-sm font-medium text-gray-700">Amount</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 sm:text-sm">$</span>
                  </div>
                  <input
                    type="number"
                    step="0.01"
                    name="amount"
                    id="amount"
                    class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                    placeholder="0.00"
                    required
                    value="<?php echo htmlspecialchars($expense['amount']); ?>" />
                </div>
              </div>

              <div class="sm:col-span-3">
                <label
                  for="paid-by"
                  class="block text-sm font-medium text-gray-700">Paid By</label>
                <div class="mt-1">
                  <select
                    id="paid-by"
                    name="paid-by"
                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    required>
                    <option value="">Select Employee</option>
                    <option value="Ahmed Khan" <?php echo ($expense['paid_by'] == 'Ahmed Khan') ? 'selected' : ''; ?>>Ahmed Khan</option>
                    <option value="Sara Ali" <?php echo ($expense['paid_by'] == 'Sara Ali') ? 'selected' : ''; ?>>Sara Ali</option>
                    <option value="Bilal Ahmad" <?php echo ($expense['paid_by'] == 'Bilal Ahmad') ? 'selected' : ''; ?>>Bilal Ahmad</option>
                    <option value="Ayesha Malik" <?php echo ($expense['paid_by'] == 'Ayesha Malik') ? 'selected' : ''; ?>>Ayesha Malik</option>
                    <option value="Omar Farooq" <?php echo ($expense['paid_by'] == 'Omar Farooq') ? 'selected' : ''; ?>>Omar Farooq</option>
                  </select>
                </div>
              </div>

              <div class="sm:col-span-3">
                <label
                  for="status"
                  class="block text-sm font-medium text-gray-700">Status</label>
                <div class="mt-1">
                  <select
                    id="status"
                    name="status"
                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    required>
                    <option value="Paid" <?php echo ($expense['status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                    <option value="Pending" <?php echo ($expense['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Rejected" <?php echo ($expense['status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
                  </select>
                </div>
              </div>

              <div class="sm:col-span-3">
                <label
                  for="payment-method"
                  class="block text-sm font-medium text-gray-700">Payment Method</label>
                <div class="mt-1">
                  <select
                    id="payment-method"
                    name="payment-method"
                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    required>
                    <option value="Cash" <?php echo ($expense['payment_method'] == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                    <option value="Bank Transfer" <?php echo ($expense['payment_method'] == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                    <option value="Credit Card" <?php echo ($expense['payment_method'] == 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                    <option value="Other" <?php echo ($expense['payment_method'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                  </select>
                </div>
              </div>

              <div class="sm:col-span-6">
                <label
                  for="receipt"
                  class="block text-sm font-medium text-gray-700">Receipt</label>
                <?php if (!empty($expense['receipt_path'])): ?>
                  <div class="mt-2 mb-4">
                    <p class="text-sm text-gray-500">Current Receipt:
                      <a href="<?php echo htmlspecialchars($expense['receipt_path']); ?>" target="_blank" class="text-primary-600 hover:text-primary-500">
                        View Receipt
                      </a>
                    </p>
                  </div>
                <?php endif; ?>
                <div
                  class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                  <div class="space-y-1 text-center">
                    <svg
                      class="mx-auto h-12 w-12 text-gray-400"
                      stroke="currentColor"
                      fill="none"
                      viewBox="0 0 48 48"
                      aria-hidden="true">
                      <path
                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4h-12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600">
                      <label
                        for="file-upload"
                        class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                        <span>Upload a new file</span>
                        <input
                          id="file-upload"
                          name="file-upload"
                          type="file"
                          class="sr-only" />
                      </label>
                      <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">
                      PNG, JPG, PDF up to 10MB
                    </p>
                  </div>
                </div>
              </div>

              <div class="sm:col-span-6">
                <label
                  for="notes"
                  class="block text-sm font-medium text-gray-700">Notes</label>
                <div class="mt-1">
                  <textarea
                    id="notes"
                    name="notes"
                    rows="3"
                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md"><?php echo htmlspecialchars($expense['notes']); ?></textarea>
                </div>
              </div>
            </div>

            <div
              <!-- JavaScript for interactivity -->
              <script>
                // Date display
                const currentDate = new Date();
                const options = {
                  weekday: "long",
                  year: "numeric",
                  month: "long",
                  day: "numeric",
                };
                document.getElementById("current-date").textContent =
                  currentDate.toLocaleDateString("en-US", options);

                // User dropdown toggle
                const userMenuButton = document.getElementById("user-menu-button");
                const userDropdown = document.getElementById("user-dropdown");

                userMenuButton.addEventListener("click", () => {
                  userDropdown.classList.toggle("hidden");
                });

                // Close dropdown when clicking outside
                document.addEventListener("click", (event) => {
                  if (
                    !userMenuButton.contains(event.target) &&
                    !userDropdown.contains(event.target)
                  ) {
                    userDropdown.classList.add("hidden");
                  }
                });

                // Mobile sidebar toggle functionality
                const sidebarToggle = document.getElementById("sidebarToggle");

                if (sidebarToggle) {
                  sidebarToggle.addEventListener("click", () => {
                    // Add your sidebar toggle logic here
                  });
                }
              </script>
</body>

</html>