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
  <?php include 'includes/css-links.php' ?>

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

<body class="bg-gray-50 min-h-screen flex flex-col">
  <?php include 'includes/navbar.php' ?>

  <!-- Main Content -->
  <main class="flex-grow py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <!-- Form Header -->
        <div class="bg-primary-600 px-6 py-4">
          <h2 class="text-2xl font-bold text-white">Create Your Account</h2>
          <p class="text-primary-100 mt-1">Join BS Traders as a valued customer</p>
        </div>

        <!-- Progress Indicator -->
        <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <div class="flex items-center">
              <span class="h-8 w-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold">1</span>
              <span class="ml-2 font-medium text-gray-900">Personal Information</span>
            </div>
            <div class="h-0.5 w-12 bg-gray-300"></div>
            <div class="flex items-center">
              <span class="h-8 w-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold">2</span>
              <span class="ml-2 font-medium text-gray-900">Contract Details</span>
            </div>
            <div class="h-0.5 w-12 bg-gray-300"></div>
            <div class="flex items-center">
              <span class="h-8 w-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold">3</span>
              <span class="ml-2 font-medium text-gray-900">Account Setup</span>
            </div>
          </div>
        </div>

        <!-- Form Content -->
        <div class="p-6">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
            <!-- Personal Information Section -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-primary-700 mb-4 flex items-center">
                <span class="h-6 w-6 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-bold mr-2">1</span>
                Personal Information
              </h3>
              <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                      </div>
                      <input type="text" name="name" id="name" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-3 py-2 sm:text-sm border border-gray-400 shadow-sm rounded-md" placeholder="John Doe" value="<?php echo $name; ?>">
                    </div>
                    <?php if (!empty($name_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $name_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                      </div>
                      <input type="email" name="email" id="email" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-3 py-2 sm:text-sm border border-gray-400 shadow-sm rounded-md" placeholder="you@example.com" value="<?php echo $email; ?>">
                    </div>
                    <?php if (!empty($email_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $email_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-phone text-gray-400"></i>
                      </div>
                      <input type="text" name="phone" id="phone" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-3 py-2 sm:text-sm border border-gray-400 shadow-sm rounded-md" placeholder="+92 300 1234567" value="<?php echo $phone; ?>">
                    </div>
                    <?php if (!empty($phone_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $phone_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <div>
                    <label for="cnic" class="block text-sm font-medium text-gray-700 mb-1">CNIC Number <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-id-card text-gray-400"></i>
                      </div>
                      <input type="text" name="cnic" id="cnic" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-3 py-2 sm:text-sm border border-gray-400 shadow-sm rounded-md" placeholder="12345-1234567-1" value="<?php echo $cnic; ?>">
                    </div>
                    <?php if (!empty($cnic_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $cnic_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-home text-gray-400"></i>
                      </div>
                      <textarea name="address" id="address" rows="3" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-3 py-2 sm:text-sm border border-gray-400 shadow-sm rounded-md" placeholder="Your complete address"><?php echo $address; ?></textarea>
                    </div>
                    <?php if (!empty($address_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $address_err; ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contract Information Section -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-primary-700 mb-4 flex items-center">
                <span class="h-6 w-6 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-bold mr-2">2</span>
                Contract Information
              </h3>
              <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <div>
                    <label for="contract_start" class="block text-sm font-medium text-gray-700 mb-1">Contract Start Date <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                      </div>
                      <input type="date" name="contract_start" id="contract_start" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-3 py-2 sm:text-sm border border-gray-400 shadow-sm rounded-md" value="<?php echo $contract_start; ?>">
                    </div>
                    <?php if (!empty($contract_start_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $contract_start_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <div>
                    <label for="contract_end" class="block text-sm font-medium text-gray-700 mb-1">Contract End Date <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                      </div>
                      <input type="date" name="contract_end" id="contract_end" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-3 py-2 sm:text-sm border border-gray-400 shadow-sm rounded-md" value="<?php echo $contract_end; ?>">
                    </div>
                    <?php if (!empty($contract_end_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $contract_end_err; ?></p>
                    <?php endif; ?>
                  </div>

                  <div class="md:col-span-2">
                    <label for="profile_pic" class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                    <div class="mt-1 flex items-center">
                      <div class="h-24 w-24 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center border border-gray-400 shadow-sm">
                        <i class="fas fa-user text-gray-300 text-4xl"></i>
                      </div>
                      <div class="ml-5 flex-1">
                        <label for="profile_pic" class="relative cursor-pointer bg-white py-2 px-4 border border-gray-400 shadow-sm rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                          <span>Upload a file</span>
                          <input id="profile_pic" name="profile_pic" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg">
                        </label>
                        <p class="mt-2 text-xs text-gray-500">JPG, PNG, or JPEG. Max 5MB.</p>
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

            <!-- Account Information Section -->
            <div class="mb-8">
              <h3 class="text-lg font-medium text-primary-700 mb-4 flex items-center">
                <span class="h-6 w-6 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-bold mr-2">3</span>
                Account Security
              </h3>
              <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                  <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                      </div>
                      <input type="password" name="password" id="password" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-3 py-2 sm:text-sm border border-gray-400 shadow-sm rounded-md" placeholder="••••••••">
                    </div>
                    <?php if (!empty($password_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $password_err; ?></p>
                    <?php else: ?>
                      <p class="mt-1 text-xs text-gray-500">Must be at least 6 characters long.</p>
                    <?php endif; ?>
                  </div>

                  <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <div class="relative rounded-md shadow-sm">
                      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                      </div>
                      <input type="password" name="confirm_password" id="confirm_password" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 pr-3 py-2 sm:text-sm border border-gray-400 shadow-sm rounded-md" placeholder="••••••••">
                    </div>
                    <?php if (!empty($confirm_password_err)): ?>
                      <p class="mt-1 text-sm text-red-600"><?php echo $confirm_password_err; ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Terms Agreement -->
            <div class="mb-8">
              <div class="bg-gray-50 rounded-lg p-4 border border-gray-400 shadow-sm">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-400 rounded" required>
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="terms" class="font-medium text-gray-700">I agree to the Terms and Conditions</label>
                    <p class="text-gray-500">By creating an account, you agree to our <a href="#" class="text-primary-600 hover:text-primary-500 font-medium">Terms of Service</a> and <a href="#" class="text-primary-600 hover:text-primary-500 font-medium">Privacy Policy</a>.</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
              <button type="submit" class="inline-flex justify-center py-3 px-6 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-user-plus mr-2"></i> Create Account
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Login Link -->
      <div class="text-center mt-8">
        <p class="text-base text-gray-600">
          Already have an account? <a href="login.php" class="font-medium text-primary-600 hover:text-primary-500">Sign in</a>
        </p>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?php include 'includes/footer.php' ?>

  <!-- JavaScript -->
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
    // Add this to your existing JavaScript section at the bottom of your page
    document.addEventListener('DOMContentLoaded', function() {
      // Get the CNIC input field
      const cnicInput = document.getElementById('cnic');

      // Add event listener for input
      cnicInput.addEventListener('input', function(e) {
        // Get the current value without any dashes
        let value = e.target.value.replace(/-/g, '');

        // Only allow numbers
        value = value.replace(/[^\d]/g, '');

        // Limit to 13 digits (Pakistan CNIC format)
        value = value.substring(0, 13);

        // Format with dashes
        let formattedValue = '';

        for (let i = 0; i < value.length; i++) {
          // Add dash after 5th digit
          if (i === 5) {
            formattedValue += '-';
          }
          // Add dash after 12th digit (5 + 7)
          else if (i === 12) {
            formattedValue += '-';
          }

          formattedValue += value[i];
        }

        // Set the formatted value back to the input
        e.target.value = formattedValue;
      });

      // Add an event listener for paste to ensure proper formatting for pasted content
      cnicInput.addEventListener('paste', function(e) {
        // Small delay to allow paste to complete before formatting
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