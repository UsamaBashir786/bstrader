<?php
// Include database configuration
require_once "config/config.php";

// Define variables and initialize with empty values
$name = $email = $phone = $address = $cnic = $contract_start = $contract_end = $password = $confirm_password = "";
$name_err = $email_err = $phone_err = $address_err = $cnic_err = $contract_start_err = $contract_end_err = $password_err = $confirm_password_err = $pic_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Validate name
  if (empty(trim($_POST["name"]))) {
    $name_err = "Please enter your full name.";
  } else {
    $name = trim($_POST["name"]);
  }

  // Validate email
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter an email.";
  } else {
    // Prepare a select statement
    $sql = "SELECT id FROM users WHERE email = ?";

    if ($stmt = $conn->prepare($sql)) {
      // Bind variables to the prepared statement as parameters
      $stmt->bind_param("s", $param_email);

      // Set parameters
      $param_email = trim($_POST["email"]);

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Store result
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
          $email_err = "This email is already registered.";
        } else {
          $email = trim($_POST["email"]);
        }
      } else {
        echo "Oops! Something went wrong. Please try again later.";
      }

      // Close statement
      $stmt->close();
    }
  }

  // Validate phone
  if (empty(trim($_POST["phone"]))) {
    $phone_err = "Please enter your phone number.";
  } else {
    $phone = trim($_POST["phone"]);
  }

  // Validate address
  if (empty(trim($_POST["address"]))) {
    $address_err = "Please enter your address.";
  } else {
    $address = trim($_POST["address"]);
  }

  // Validate CNIC
  if (empty(trim($_POST["cnic"]))) {
    $cnic_err = "Please enter your CNIC number.";
  } else {
    // Check CNIC format (assuming Pakistan CNIC format: 12345-1234567-1)
    $cnic = trim($_POST["cnic"]);
    if (!preg_match("/^\d{5}-\d{7}-\d{1}$/", $cnic)) {
      $cnic_err = "Please enter a valid CNIC format (e.g., 12345-1234567-1).";
    }
  }

  // Validate contract period
  if (empty(trim($_POST["contract_start"]))) {
    $contract_start_err = "Please enter contract start date.";
  } else {
    $contract_start = trim($_POST["contract_start"]);
  }

  if (empty(trim($_POST["contract_end"]))) {
    $contract_end_err = "Please enter contract end date.";
  } else {
    $contract_end = trim($_POST["contract_end"]);

    // Check if end date is after start date
    if (!empty($contract_start) && strtotime($contract_end) <= strtotime($contract_start)) {
      $contract_end_err = "Contract end date must be after start date.";
    }
  }

  // Validate profile picture
  $target_dir = "uploads/profile/";
  $profile_pic = "";

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
      $pic_err = "Error: Please select a valid file format (JPG, JPEG, PNG).";
    }

    // Verify file size - 5MB maximum
    $maxsize = 5 * 1024 * 1024;
    if ($file_size > $maxsize) {
      $pic_err = "Error: File size is larger than the allowed limit (5MB).";
    }

    // If no errors, proceed with upload
    if (empty($pic_err)) {
      // Create a unique filename
      $new_filename = uniqid() . "." . $ext;
      $target_file = $target_dir . $new_filename;

      if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $profile_pic = $new_filename;
      } else {
        $pic_err = "Error: There was a problem uploading your file. Please try again.";
      }
    }
  }

  // Validate password
  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter a password.";
  } elseif (strlen(trim($_POST["password"])) < 6) {
    $password_err = "Password must have at least 6 characters.";
  } else {
    $password = trim($_POST["password"]);
  }

  // Validate confirm password
  if (empty(trim($_POST["confirm_password"]))) {
    $confirm_password_err = "Please confirm password.";
  } else {
    $confirm_password = trim($_POST["confirm_password"]);
    if (empty($password_err) && ($password != $confirm_password)) {
      $confirm_password_err = "Passwords did not match.";
    }
  }

  // Check input errors before inserting in database
  if (
    empty($name_err) && empty($email_err) && empty($phone_err) && empty($address_err) &&
    empty($cnic_err) && empty($contract_start_err) && empty($contract_end_err) &&
    empty($password_err) && empty($confirm_password_err) && empty($pic_err)
  ) {

    // Prepare an insert statement
    $sql = "INSERT INTO users (name, email, phone, address, cnic, contract_start, contract_end, profile_pic, password, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
      // Bind variables to the prepared statement as parameters
      $stmt->bind_param(
        "ssssssssss",
        $param_name,
        $param_email,
        $param_phone,
        $param_address,
        $param_cnic,
        $param_contract_start,
        $param_contract_end,
        $param_profile_pic,
        $param_password,
        $param_role
      );

      // Set parameters
      $param_name = $name;
      $param_email = $email;
      $param_phone = $phone;
      $param_address = $address;
      $param_cnic = $cnic;
      $param_contract_start = $contract_start;
      $param_contract_end = $contract_end;
      $param_profile_pic = $profile_pic;
      $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
      $param_role = "user"; // Default role for new registrations

      // Attempt to execute the prepared statement
      if ($stmt->execute()) {
        // Redirect to login page
        header("location: login.php");
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
  <title>BS Traders - Create Account</title>
  <?php include 'includes/css-links.php' ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen">
  <?php include 'includes/navbar.php' ?>

  <div class="container mx-auto px-4 py-12">
    <!-- Header -->
    <div class="text-center mb-10">
      <h1 class="text-3xl font-bold text-indigo-800">BS Traders</h1>
      <p class="text-gray-600 mt-2">Create your trading account in minutes</p>
    </div>

    <!-- Main Card -->
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
      <!-- Header Banner -->
      <div class="bg-indigo-600 py-6 px-8">
        <h2 class="text-2xl font-bold text-white">Join Our Trading Community</h2>
        <p class="text-indigo-100 mt-1">Complete the form below to get started</p>
      </div>

      <!-- Step Indicator -->
      <div class="flex justify-between items-center px-8 py-4 bg-gray-50 border-b border-gray-200">
        <div class="flex items-center">
          <div class="bg-indigo-600 text-white rounded-full h-8 w-8 flex items-center justify-center font-semibold">1</div>
          <span class="ml-2 font-medium text-indigo-800">Personal Details</span>
        </div>
        <div class="hidden md:block h-0.5 w-16 bg-gray-300"></div>
        <div class="flex items-center">
          <div class="bg-indigo-600 text-white rounded-full h-8 w-8 flex items-center justify-center font-semibold">2</div>
          <span class="ml-2 font-medium text-indigo-800">Contract</span>
        </div>
        <div class="hidden md:block h-0.5 w-16 bg-gray-300"></div>
        <div class="flex items-center">
          <div class="bg-indigo-600 text-white rounded-full h-8 w-8 flex items-center justify-center font-semibold">3</div>
          <span class="ml-2 font-medium text-indigo-800">Security</span>
        </div>
      </div>

      <!-- Form -->
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data" class="p-8">
        <!-- Personal Information Section -->
        <div class="mb-10">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <span class="h-6 w-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-sm font-bold mr-2">1</span>
            Personal Information
          </h3>

          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name Field -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-user"></i>
                  </span>
                  <input type="text" id="name" name="name" placeholder="John Doe" value="<?php echo $name; ?>"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($name_err)) ? 'border-red-500' : ''; ?>">
                </div>
                <?php if (!empty($name_err)): ?>
                  <p class="mt-1 text-sm text-red-600"><?php echo $name_err; ?></p>
                <?php endif; ?>
              </div>

              <!-- Email Field -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
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

              <!-- Phone Field -->
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-phone"></i>
                  </span>
                  <input type="text" id="phone" name="phone" placeholder="+92 300 1234567" value="<?php echo $phone; ?>"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($phone_err)) ? 'border-red-500' : ''; ?>">
                </div>
                <?php if (!empty($phone_err)): ?>
                  <p class="mt-1 text-sm text-red-600"><?php echo $phone_err; ?></p>
                <?php endif; ?>
              </div>

              <!-- CNIC Field -->
              <div>
                <label for="cnic" class="block text-sm font-medium text-gray-700 mb-1">CNIC Number <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-id-card"></i>
                  </span>
                  <input type="text" id="cnic" name="cnic" placeholder="12345-1234567-1" value="<?php echo $cnic; ?>"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($cnic_err)) ? 'border-red-500' : ''; ?>">
                </div>
                <?php if (!empty($cnic_err)): ?>
                  <p class="mt-1 text-sm text-red-600"><?php echo $cnic_err; ?></p>
                <?php endif; ?>
              </div>

              <!-- Address Field - Full Width -->
              <div class="md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute top-3 left-3 text-gray-400">
                    <i class="fas fa-home"></i>
                  </span>
                  <textarea id="address" name="address" rows="3" placeholder="Your complete address"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($address_err)) ? 'border-red-500' : ''; ?>"><?php echo $address; ?></textarea>
                </div>
                <?php if (!empty($address_err)): ?>
                  <p class="mt-1 text-sm text-red-600"><?php echo $address_err; ?></p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Contract Information Section -->
        <div class="mb-10">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <span class="h-6 w-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-sm font-bold mr-2">2</span>
            Contract Information
          </h3>

          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Start Date Field -->
              <div>
                <label for="contract_start" class="block text-sm font-medium text-gray-700 mb-1">Contract Start Date <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-calendar-alt"></i>
                  </span>
                  <input type="date" id="contract_start" name="contract_start" value="<?php echo $contract_start; ?>"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($contract_start_err)) ? 'border-red-500' : ''; ?>">
                </div>
                <?php if (!empty($contract_start_err)): ?>
                  <p class="mt-1 text-sm text-red-600"><?php echo $contract_start_err; ?></p>
                <?php endif; ?>
              </div>

              <!-- End Date Field -->
              <div>
                <label for="contract_end" class="block text-sm font-medium text-gray-700 mb-1">Contract End Date <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-calendar-alt"></i>
                  </span>
                  <input type="date" id="contract_end" name="contract_end" value="<?php echo $contract_end; ?>"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($contract_end_err)) ? 'border-red-500' : ''; ?>">
                </div>
                <?php if (!empty($contract_end_err)): ?>
                  <p class="mt-1 text-sm text-red-600"><?php echo $contract_end_err; ?></p>
                <?php endif; ?>
              </div>

              <!-- Profile Picture Field -->
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                <div class="flex flex-col md:flex-row md:items-center">
                  <div class="h-20 w-20 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center border border-gray-300">
                    <i class="fas fa-user text-gray-400 text-3xl"></i>
                  </div>
                  <div class="mt-4 md:mt-0 md:ml-5">
                    <label for="profile_pic" class="cursor-pointer inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                      <i class="fas fa-upload mr-2 text-gray-500"></i>
                      Upload Photo
                      <input id="profile_pic" name="profile_pic" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg">
                    </label>
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG, or JPEG. Max 5MB.</p>
                    <p class="mt-1 text-xs text-gray-500" id="selected-file">No file selected</p>
                    <?php if (!empty($pic_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $pic_err; ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Account Security Section -->
        <div class="mb-10">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <span class="h-6 w-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-sm font-bold mr-2">3</span>
            Account Security
          </h3>

          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Password Field -->
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                  </span>
                  <input type="password" id="password" name="password" placeholder="••••••••"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>">
                </div>
                <?php if (!empty($password_err)): ?>
                  <p class="mt-1 text-sm text-red-600"><?php echo $password_err; ?></p>
                <?php else: ?>
                  <p class="mt-1 text-xs text-gray-500">Must be at least 6 characters long</p>
                <?php endif; ?>
              </div>

              <!-- Confirm Password Field -->
              <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <i class="fas fa-lock"></i>
                  </span>
                  <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••"
                    class="pl-10 w-full py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>">
                </div>
                <?php if (!empty($confirm_password_err)): ?>
                  <p class="mt-1 text-sm text-red-600"><?php echo $confirm_password_err; ?></p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="mb-8">
          <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="flex items-start">
              <div class="flex items-center h-5">
                <input id="terms" name="terms" type="checkbox" required
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
              </div>
              <div class="ml-3">
                <label for="terms" class="text-sm font-medium text-gray-700">I agree to the Terms and Conditions</label>
                <p class="text-xs text-gray-500 mt-1">By creating an account, you agree to our <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a> and <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer Buttons -->
        <div class="flex justify-between items-center">
          <a href="login.php" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Login
          </a>
          <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-user-plus mr-2"></i>
            Create Account
          </button>
        </div>
      </form>
    </div>

    <!-- Login Link -->
    <div class="text-center mt-8">
      <p class="text-sm text-gray-600">
        Already have an account? <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
      </p>
    </div>
  </div>

  <?php include 'includes/footer.php' ?>

  <script>
    // File input preview
    const fileInput = document.getElementById('profile_pic');
    const fileNameDisplay = document.getElementById('selected-file');

    fileInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        fileNameDisplay.textContent = this.files[0].name;
      } else {
        fileNameDisplay.textContent = 'No file selected';
      }
    });

    // CNIC formatting
    document.addEventListener('DOMContentLoaded', function() {
      const cnicInput = document.getElementById('cnic');

      cnicInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/-/g, '');
        value = value.replace(/[^\d]/g, '');
        value = value.substring(0, 13);

        let formattedValue = '';
        for (let i = 0; i < value.length; i++) {
          if (i === 5) {
            formattedValue += '-';
          } else if (i === 12) {
            formattedValue += '-';
          }
          formattedValue += value[i];
        }

        e.target.value = formattedValue;
      });

      cnicInput.addEventListener('paste', function(e) {
        setTimeout(function() {
          const pastedValue = cnicInput.value.replace(/-/g, '').replace(/[^\d]/g, '').substring(0, 13);
          let formattedValue = '';

          for (let i = 0; i < pastedValue.length; i++) {
            if (i === 5 || i === 12) {
              formattedValue += '-';
            }
            formattedValue += pastedValue[i];
          }

          cnicInput.value = formattedValue;
        }, 10);
      });
    });
  </script>
</body>

</html>