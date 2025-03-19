<?php

/**
 * BS Traders - Salary Management System
 * Model for handling salary and advance salary operations
 */

class SalaryModel
{
  private $conn;

  /**
   * Constructor - establishes database connection
   */
  public function __construct()
  {
    $this->conn = new mysqli('localhost', 'root', '', 'bs_trader');

    if ($this->conn->connect_error) {
      die("Connection failed: " . $this->conn->connect_error);
    }
  }

  /**
   * Get all salaries with optional filtering
   *
   * @param array $filters Optional filters (month, year, status, user_id)
   * @return array Array of salary records
   */
  public function getAllSalaries($filters = [])
  {
    $where_clause = "1=1"; // Always true condition to start
    $filter_params = [];
    $param_types = "";

    if (!empty($filters['month'])) {
      $where_clause .= " AND month = ?";
      $filter_params[] = $filters['month'];
      $param_types .= "s";
    }

    if (!empty($filters['year'])) {
      $where_clause .= " AND year = ?";
      $filter_params[] = $filters['year'];
      $param_types .= "s";
    }

    if (!empty($filters['status'])) {
      $where_clause .= " AND status = ?";
      $filter_params[] = $filters['status'];
      $param_types .= "s";
    }

    if (!empty($filters['user_id'])) {
      $where_clause .= " AND user_id = ?";
      $filter_params[] = $filters['user_id'];
      $param_types .= "i";
    }

    $query = "SELECT s.*, u.name as user_name, u.email as user_email, u.role as user_role
                  FROM salary s
                  JOIN users u ON s.user_id = u.id
                  WHERE $where_clause
                  ORDER BY s.year DESC, s.month DESC, u.name ASC";

    $salaries = [];

    if (!empty($param_types)) {
      $stmt = $this->conn->prepare($query);
      $stmt->bind_param($param_types, ...$filter_params);
      $stmt->execute();
      $result = $stmt->get_result();
    } else {
      $result = $this->conn->query($query);
    }

    while ($row = $result->fetch_assoc()) {
      $salaries[] = $row;
    }

    return $salaries;
  }

  /**
   * Get a single salary record by ID
   * 
   * @param int $id Salary record ID
   * @return array|null Salary record or null if not found
   */
  public function getSalaryById($id)
  {
    $stmt = $this->conn->prepare("SELECT s.*, u.name as user_name, u.email as user_email, u.role as user_role 
                                      FROM salary s
                                      JOIN users u ON s.user_id = u.id
                                      WHERE s.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
      return null;
    }

    return $result->fetch_assoc();
  }

  /**
   * Add a new salary record
   * 
   * @param array $data Salary data
   * @return int|bool Inserted ID or false on failure
   */
  public function addSalary($data)
  {
    $stmt = $this->conn->prepare("INSERT INTO salary (user_id, amount, bonus, month, year, payment_date, payment_method, status, notes) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
      "iddssssss",
      $data['user_id'],
      $data['amount'],
      $data['bonus'],
      $data['month'],
      $data['year'],
      $data['payment_date'],
      $data['payment_method'],
      $data['status'],
      $data['notes']
    );

    if ($stmt->execute()) {
      return $this->conn->insert_id;
    }

    return false;
  }

  /**
   * Update a salary record
   * 
   * @param int $id Salary ID
   * @param array $data Updated salary data
   * @return bool Success or failure
   */
  public function updateSalary($id, $data)
  {
    $stmt = $this->conn->prepare("UPDATE salary 
                                     SET user_id = ?, amount = ?, bonus = ?, month = ?, year = ?, 
                                         payment_date = ?, payment_method = ?, status = ?, notes = ?
                                     WHERE id = ?");

    $stmt->bind_param(
      "iddssssssi",
      $data['user_id'],
      $data['amount'],
      $data['bonus'],
      $data['month'],
      $data['year'],
      $data['payment_date'],
      $data['payment_method'],
      $data['status'],
      $data['notes'],
      $id
    );

    return $stmt->execute();
  }

  /**
   * Delete a salary record
   * 
   * @param int $id Salary ID
   * @return bool Success or failure
   */
  public function deleteSalary($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM salary WHERE id = ?");
    $stmt->bind_param("i", $id);

    return $stmt->execute();
  }

  /**
   * Process payment for a salary (mark as paid)
   * 
   * @param int $id Salary ID
   * @param string $payment_date Payment date (Y-m-d format)
   * @param string $payment_method Payment method
   * @return bool Success or failure
   */
  public function processSalaryPayment($id, $payment_date, $payment_method)
  {
    $stmt = $this->conn->prepare("UPDATE salary 
                                     SET status = 'paid', payment_date = ?, payment_method = ?, updated_at = CURRENT_TIMESTAMP
                                     WHERE id = ?");

    $stmt->bind_param("ssi", $payment_date, $payment_method, $id);

    return $stmt->execute();
  }

  /**
   * Get salary statistics by year
   * 
   * @param int $year Year to get statistics for, defaults to current year
   * @return array Statistics data
   */
  public function getSalaryStatsByYear($year = null)
  {
    if ($year === null) {
      $year = date('Y');
    }

    $query = "SELECT 
                    SUM(amount + bonus) as total_paid,
                    COUNT(*) as total_records,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status = 'paid' THEN amount + bonus ELSE 0 END) as paid_amount,
                    SUM(CASE WHEN status = 'pending' THEN amount + bonus ELSE 0 END) as pending_amount,
                    SUM(bonus) as total_bonus
                  FROM salary 
                  WHERE year = ?";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("s", $year);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
  }

  /**
   * Get all advance salary requests with optional filtering
   * 
   * @param array $filters Optional filters (status, user_id)
   * @return array Array of advance salary records
   */
  public function getAllAdvanceSalaries($filters = [])
  {
    $where_clause = "1=1"; // Always true condition to start
    $filter_params = [];
    $param_types = "";

    if (!empty($filters['status'])) {
      $where_clause .= " AND adv.status = ?";
      $filter_params[] = $filters['status'];
      $param_types .= "s";
    }

    if (!empty($filters['user_id'])) {
      $where_clause .= " AND adv.user_id = ?";
      $filter_params[] = $filters['user_id'];
      $param_types .= "i";
    }

    $query = "SELECT adv.*, 
                        u.name as user_name, u.email as user_email,
                        a.name as approver_name
                  FROM advance_salary adv
                  JOIN users u ON adv.user_id = u.id
                  LEFT JOIN users a ON adv.approved_by = a.id
                  WHERE $where_clause
                  ORDER BY adv.created_at DESC";

    $advances = [];

    if (!empty($param_types)) {
      $stmt = $this->conn->prepare($query);
      $stmt->bind_param($param_types, ...$filter_params);
      $stmt->execute();
      $result = $stmt->get_result();
    } else {
      $result = $this->conn->query($query);
    }

    while ($row = $result->fetch_assoc()) {
      $row['payments'] = $this->getAdvanceSalaryPayments($row['id']);
      $advances[] = $row;
    }

    return $advances;
  }

  /**
   * Get a single advance salary record by ID
   * 
   * @param int $id Advance salary ID
   * @return array|null Advance salary record or null if not found
   */
  public function getAdvanceSalaryById($id)
  {
    $stmt = $this->conn->prepare("SELECT adv.*, 
                                             u.name as user_name, u.email as user_email,
                                             a.name as approver_name
                                      FROM advance_salary adv
                                      JOIN users u ON adv.user_id = u.id
                                      LEFT JOIN users a ON adv.approved_by = a.id
                                      WHERE adv.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
      return null;
    }

    $advance = $result->fetch_assoc();
    $advance['payments'] = $this->getAdvanceSalaryPayments($id);

    return $advance;
  }

  /**
   * Get all payments for an advance salary
   * 
   * @param int $advance_id Advance salary ID
   * @return array Array of payment records
   */
  public function getAdvanceSalaryPayments($advance_id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM advance_salary_payments 
                                      WHERE advance_salary_id = ?
                                      ORDER BY payment_date ASC");
    $stmt->bind_param("i", $advance_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $payments = [];
    while ($row = $result->fetch_assoc()) {
      $payments[] = $row;
    }

    return $payments;
  }

  /**
   * Request a new advance salary
   * 
   * @param array $data Advance salary data
   * @return int|bool Inserted ID or false on failure
   */
  public function requestAdvanceSalary($data)
  {
    // Check that all required fields exist in the data array
    $requiredFields = ['user_id', 'amount', 'request_date', 'reason', 'repayment_method', 'installments'];
    foreach ($requiredFields as $field) {
      if (!isset($data[$field])) {
        return false;
      }
    }

    // Set remaining amount equal to full amount initially
    $data['remaining_amount'] = $data['amount'];
    $data['status'] = 'pending';

    // Make sure notes is set even if it's empty
    if (!isset($data['notes'])) {
      $data['notes'] = '';
    }

    $stmt = $this->conn->prepare("INSERT INTO advance_salary 
                                      (user_id, amount, request_date, reason, repayment_method, 
                                       installments, remaining_amount, status, notes) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
      "idssidsss",
      $data['user_id'],
      $data['amount'],
      $data['request_date'],
      $data['reason'],
      $data['repayment_method'],
      $data['installments'],
      $data['remaining_amount'],
      $data['status'],
      $data['notes']
    );

    if ($stmt->execute()) {
      return $this->conn->insert_id;
    }

    return false;
  }
  // In SalaryModel.php, add this method:
  public function getTotalUsers()
  {
    $query = "SELECT COUNT(*) as total FROM users";
    $result = $this->conn->query($query);
    $row = $result->fetch_assoc();
    return $row['total'];
  }
  /**
   * Approve or reject an advance salary request
   * 
   * @param int $id Advance salary ID
   * @param string $status New status ('approved' or 'rejected')
   * @param int $approver_id ID of the user approving/rejecting
   * @param string $approval_date Date of approval/rejection
   * @param string $notes Additional notes
   * @return bool Success or failure
   */
  public function updateAdvanceSalaryStatus($id, $status, $approver_id, $approval_date, $notes = null)
  {
    $stmt = $this->conn->prepare("UPDATE advance_salary 
                                     SET status = ?, approved_by = ?, approval_date = ?, notes = ?
                                     WHERE id = ?");

    $stmt->bind_param("sissi", $status, $approver_id, $approval_date, $notes, $id);

    return $stmt->execute();
  }

  /**
   * Process payment for an advance salary
   * 
   * @param int $id Advance salary ID
   * @param array $payment_data Payment data
   * @return bool Success or failure
   */
  public function processAdvancePayment($id, $payment_data)
  {
    // Start transaction
    $this->conn->begin_transaction();

    try {
      // Add payment record
      $stmt = $this->conn->prepare("INSERT INTO advance_salary_payments 
                                         (advance_salary_id, amount, payment_date, payment_method, notes)
                                         VALUES (?, ?, ?, ?, ?)");

      $stmt->bind_param(
        "idsss",
        $id,
        $payment_data['amount'],
        $payment_data['payment_date'],
        $payment_data['payment_method'],
        $payment_data['notes']
      );

      $stmt->execute();

      // Get advance details
      $advance = $this->getAdvanceSalaryById($id);

      // Calculate new remaining amount
      $new_remaining = $advance['remaining_amount'] - $payment_data['amount'];

      // Determine new status
      $new_status = 'partially_paid';
      if ($new_remaining <= 0) {
        $new_status = 'paid';
        $new_remaining = 0; // Ensure we don't have negative remaining amount
      }

      // Update advance record
      $stmt = $this->conn->prepare("UPDATE advance_salary 
                                         SET remaining_amount = ?, status = ?, payment_date = ?
                                         WHERE id = ?");

      $stmt->bind_param(
        "dssi",
        $new_remaining,
        $new_status,
        $payment_data['payment_date'],
        $id
      );

      $stmt->execute();

      // Commit the transaction
      $this->conn->commit();
      return true;
    } catch (Exception $e) {
      // Rollback on error
      $this->conn->rollback();
      return false;
    }
  }

  /**
   * Delete an advance salary record
   * 
   * @param int $id Advance salary ID
   * @return bool Success or failure
   */
  public function deleteAdvanceSalary($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM advance_salary WHERE id = ?");
    $stmt->bind_param("i", $id);

    return $stmt->execute();
  }

  /**
   * Get all users for dropdown menus
   * 
   * @return array Array of users with id, name, and email
   */
  public function getAllUsers()
  {
    $query = "SELECT id, name, email, role FROM users ORDER BY name";
    $result = $this->conn->query($query);

    $users = [];
    while ($row = $result->fetch_assoc()) {
      $users[] = $row;
    }

    return $users;
  }

  /**
   * Get advance salary statistics
   * 
   * @return array Statistics
   */
  public function getAdvanceSalaryStats()
  {
    $query = "SELECT 
                    COUNT(*) as total_requests,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_count,
                    SUM(CASE WHEN status = 'partially_paid' THEN 1 ELSE 0 END) as partial_count,
                    SUM(amount) as total_amount,
                    SUM(remaining_amount) as total_remaining
                  FROM advance_salary";

    $result = $this->conn->query($query);

    return $result->fetch_assoc();
  }

  /**
   * Format currency amount
   * 
   * @param float $amount Amount to format
   * @param boolean $include_currency Whether to include currency symbol
   * @return string Formatted amount
   */
  public static function formatMoney($amount, $include_currency = true)
  {
    return ($include_currency ? 'Rs. ' : '') . number_format($amount, 2);
  }

  /**
   * Get months array for dropdowns
   * 
   * @return array Array of months
   */
  public static function getMonths()
  {
    return [
      'January',
      'February',
      'March',
      'April',
      'May',
      'June',
      'July',
      'August',
      'September',
      'October',
      'November',
      'December'
    ];
  }

  /**
   * Get years array for dropdowns (current year and 5 years before and after)
   * 
   * @return array Array of years
   */
  public static function getYears()
  {
    $current_year = date('Y');
    $years = [];

    for ($i = $current_year - 5; $i <= $current_year + 5; $i++) {
      $years[] = $i;
    }

    return $years;
  }

  /**
   * Destructor - close the database connection
   */
  public function __destruct()
  {
    $this->conn->close();
  }
}
