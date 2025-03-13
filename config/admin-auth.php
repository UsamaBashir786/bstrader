<?php
// admin_auth.php - For checking admin authentication
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: ../login.php");
  exit;
}

// Check if the user is an admin
if ($_SESSION["role"] !== "admin") {
  // Redirect to user dashboard if not admin
  header("location: ../user/index.php");
  exit;
}
