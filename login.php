<?php
// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
  // Redirect based on user role
  if ($_SESSION['role'] == 'admin') {
    header("Location: admin/index.php");
  } else {
    header("Location: user/index.php");
  }
  exit;
}

// Include database configuration
require_once "config/config.php";

// Initialize variables
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate email
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter your email.";
  } else {
    $email = trim($_POST["email"]);
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter your password.";
  } else {
    $password = trim($_POST["password"]);
  }

  // Validate credentials
  if (empty($email_err) && empty($password_err)) {
    // Prepare a select statement
    $sql = "SELECT id, email, password, role, name FROM users WHERE email = ?";

    if ($stmt = $conn->prepare($sql)) {
      // Bind variables to the prepared statement as parameters
      $stmt->bind_param("s", $param_email);

      // Set parameters
      $param_email = $email;

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Store result
        $stmt->store_result();

        // Check if email exists, if yes then verify password
        if ($stmt->num_rows == 1) {
          // Bind result variables
          $stmt->bind_result($id, $email, $hashed_password, $role, $name);
          if ($stmt->fetch()) {
            if (password_verify($password, $hashed_password)) {
              // Password is correct, start a new session
              session_start();

              // Store data in session variables
              $_SESSION["loggedin"] = true;
              $_SESSION["user_id"] = $id;
              $_SESSION["email"] = $email;
              $_SESSION["role"] = $role;
              $_SESSION["name"] = $name;

              // Redirect user to appropriate dashboard
              if ($role == "admin") {
                header("location: admin/index.php");
              } else {
                header("location: user/index.php");
              }
            } else {
              // Password is not valid
              $login_err = "Invalid email or password.";
            }
          }
        } else {
          // Email doesn't exist
          $login_err = "Invalid email or password.";
        }
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      $stmt->close();
    }
  }

  // Close connection
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BS Traders - Sign In</title>
  <?php include 'includes/css-links.php' ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
  <?php include 'includes/navbar.php' ?>

  <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo and Header -->
      <div class="text-center">
        <h1 class="text-3xl font-bold text-indigo-800">BS Traders</h1>
        <p class="text-gray-600 mt-2">Access your trading account</p>
      </div>
      
      <!-- Main Card -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header Banner -->
        <div class="bg-indigo-600 py-6 px-8">
          <h2 class="text-2xl font-bold text-white">Welcome Back</h2>
          <p class="text-indigo-100 mt-1">Sign in to continue to your dashboard</p>
        </div>
        
        <!-- Form -->
        <div class="p-8">
          <?php if (!empty($login_err)): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
              <div class="flex">
                <div class="flex-shrink-0">
                  <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="ml-3">
                  <p class="text-sm"><?php echo $login_err; ?></p>
                </div>
              </div>
            </div>
          <?php endif; ?>
          
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="space-y-6">
            <!-- Email Field -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                  <i class="fas fa-envelope"></i>
                </span>
                <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo $email; ?>"
                  class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>">
              </div>
              <?php if (!empty($email_err)): ?>
                <p class="mt-1 text-sm text-red-600"><?php echo $email_err; ?></p>
              <?php endif; ?>
            </div>
            
            <!-- Password Field -->
            <div>
              <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                  <i class="fas fa-lock"></i>
                </span>
                <input type="password" id="password" name="password" placeholder="••••••••"
                  class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>">
              </div>
              <?php if (!empty($password_err)): ?>
                <p class="mt-1 text-sm text-red-600"><?php echo $password_err; ?></p>
              <?php endif; ?>
            </div>
            
            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input id="remember-me" name="remember-me" type="checkbox" 
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="remember-me" class="ml-2 block text-sm text-gray-700">Remember me</label>
              </div>
              <div class="text-sm">
                <a href="forgot_password.php" class="font-medium text-indigo-600 hover:text-indigo-500">Forgot password?</a>
              </div>
            </div>
            
            <!-- Submit Button -->
            <div>
              <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-sign-in-alt mr-2"></i> Sign in
              </button>
            </div>
          </form>
          
          <!-- Divider -->
          <div class="mt-8 relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">Or continue with</span>
            </div>
          </div>
          
          <!-- Social Login -->
          <div class="mt-6 grid grid-cols-2 gap-3">
            <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
              <i class="fab fa-google text-red-500"></i>
              <span class="ml-2">Google</span>
            </a>
            <a href="#" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors">
              <i class="fab fa-facebook-f text-blue-600"></i>
              <span class="ml-2">Facebook</span>
            </a>
          </div>
        </div>
      </div>
      
      <!-- Register Link -->
      <div class="text-center">
        <p class="text-sm text-gray-600">
          Don't have an account yet? <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">Create an account</a>
        </p>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-gray-800 mt-auto">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="text-gray-300 text-sm">
          &copy; 2025 BS Traders. All rights reserved.
        </div>
        <div class="mt-4 md:mt-0">
          <div class="flex space-x-6">
            <a href="#" class="text-gray-400 hover:text-white transition-colors">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors">
              <i class="fab fa-linkedin-in"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>