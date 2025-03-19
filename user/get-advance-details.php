<?php
// Include user authentication
require_once "../config/user-auth.php";
require_once "../admin/includes/SalaryModel.php";

// Initialize the salary model
$salaryModel = new SalaryModel();

// Get user ID from session
$user_id = $_SESSION["user_id"];

// Validate the request
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="text-red-500 p-4 text-center">Invalid request</div>';
    exit;
}

$advance_id = intval($_GET['id']);

// Get the advance salary details
$advance = $salaryModel->getAdvanceSalaryById($advance_id);

// Check if the advance exists and belongs to the current user
if (!$advance || $advance['user_id'] != $user_id) {
    echo '<div class="text-red-500 p-4 text-center">Advance request not found or you don\'t have permission to view it</div>';
    exit;
}

// Generate HTML for the advance details
?>

<div class="bg-gray-50 p-4 rounded-lg mb-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-500">Request Date</p>
            <p class="font-medium"><?php echo date('M j, Y', strtotime($advance['request_date'])); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <p class="font-medium">
                <?php if ($advance['status'] == 'pending'): ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Pending
                    </span>
                <?php elseif ($advance['status'] == 'approved'): ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        Approved
                    </span>
                <?php elseif ($advance['status'] == 'rejected'): ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Rejected
                    </span>
                <?php elseif ($advance['status'] == 'partially_paid'): ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                        Partially Paid
                    </span>
                <?php elseif ($advance['status'] == 'paid'): ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Paid
                    </span>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Amount</p>
            <p class="font-medium"><?php echo SalaryModel::formatMoney($advance['amount']); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Remaining</p>
            <p class="font-medium"><?php echo SalaryModel::formatMoney($advance['remaining_amount']); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Repayment Method</p>
            <p class="font-medium"><?php echo ucfirst($advance['repayment_method']); ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500">Installments</p>
            <p class="font-medium"><?php echo $advance['installments']; ?></p>
        </div>
    </div>
    <div class="mt-3">
        <p class="text-sm text-gray-500">Reason</p>
        <p class="font-medium"><?php echo nl2br(htmlspecialchars($advance['reason'])); ?></p>
    </div>
    <?php if (!empty($advance['notes'])): ?>
    <div class="mt-3">
        <p class="text-sm text-gray-500">Notes</p>
        <p class="font-medium"><?php echo nl2br(htmlspecialchars($advance['notes'])); ?></p>
    </div>
    <?php endif; ?>
    <?php if ($advance['status'] == 'approved' || $advance['status'] == 'rejected'): ?>
    <div class="mt-3">
        <p class="text-sm text-gray-500">Approved/Rejected By</p>
        <p class="font-medium"><?php echo htmlspecialchars($advance['approver_name']); ?> on <?php echo date('M j, Y', strtotime($advance['approval_date'])); ?></p>
    </div>
    <?php endif; ?>
</div>

<?php if (!empty($advance['payments'])): ?>
<h4 class="text-md font-medium text-gray-900 mt-4 mb-2">Payment History</h4>
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($advance['payments'] as $payment): ?>
            <tr>
                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y', strtotime($payment['payment_date'])); ?></td>
                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900"><?php echo SalaryModel::formatMoney($payment['amount']); ?></td>
                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo ucfirst($payment['payment_method']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if ($advance['status'] == 'pending'): ?>
<div class="mt-4 border-t border-gray-200 pt-4">
    <p class="text-sm text-gray-600">Your request is currently under review. You will be notified once it's approved or rejected.</p>
</div>
<?php endif; ?>