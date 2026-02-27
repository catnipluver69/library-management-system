<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Fine.php';
require_once '../../classes/Student.php';
require_once '../../classes/BorrowRecord.php';
require_once '../../classes/Book.php';

if (!User::isLoggedIn() || User::getRole() !== 'manager') {
    header('Location: ../../login.php');
    exit;
}

$username = $_SESSION['username'];
$fine = new Fine();
$student = new Student();
$borrowRecord = new BorrowRecord();
$book = new Book();

// Get statistics
$fineStats = $fine->getStatistics();
$studentStats = $student->getStatistics();
$bookStats = $book->getStatistics();
$pendingManagerApprovals = $borrowRecord->getPendingManagerApprovals();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - LMS</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="dashboard.php" class="navbar-brand">Library Management System</a>
            <ul class="navbar-menu">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="fines.php">Fines</a></li>
                <li><a href="approvals.php">Approvals</a></li>
                <li><a href="blacklist.php">Blacklist</a></li>
                <li class="navbar-user">Welcome, <?php echo htmlspecialchars($username); ?></li>
                <li><a href="../../process/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard">
            <h2>Manager Dashboard</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Students</h3>
                    <div class="stat-value"><?php echo $studentStats['total_students']; ?></div>
                </div>
                <div class="stat-card red">
                    <h3>Blacklisted Students</h3>
                    <div class="stat-value"><?php echo $studentStats['blacklisted_count']; ?></div>
                </div>
                <div class="stat-card orange">
                    <h3>Unpaid Fines</h3>
                    <div class="stat-value">$<?php echo number_format($fineStats['total_unpaid_amount'], 2); ?></div>
                </div>
                <div class="stat-card green">
                    <h3>Total Books</h3>
                    <div class="stat-value"><?php echo $bookStats['total_books']; ?></div>
                </div>
            </div>

            <!-- Pending Manager Approvals -->
            <h3>Pending Authorization Requests (Students with Fines)</h3>
            <?php if (empty($pendingManagerApprovals)): ?>
                <p>No pending authorization requests.</p>
            <?php else: ?>
                <div class="alert alert-warning">
                    <strong>Note:</strong> These students have unpaid fines and require your authorization to borrow books.
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>Student</th>
                                <th>Username</th>
                                <th>Book</th>
                                <th>Request Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingManagerApprovals as $approval): ?>
                                <tr>
                                    <td><?php echo $approval['record_id']; ?></td>
                                    <td><?php echo htmlspecialchars($approval['name']); ?></td>
                                    <td><?php echo htmlspecialchars($approval['username']); ?></td>
                                    <td><?php echo htmlspecialchars($approval['title']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($approval['created_at'])); ?></td>
                                    <td>
                                        <form action="../../process/manager_approval.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="record_id" value="<?php echo $approval['record_id']; ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-success btn-sm">Authorize</button>
                                        </form>
                                        <form action="../../process/manager_approval.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="record_id" value="<?php echo $approval['record_id']; ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <!-- Recent Statistics -->
            <h3 class="mt-20">Fine Statistics</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th>Count</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Fines</td>
                            <td><?php echo $fineStats['total_fines']; ?></td>
                            <td>$<?php echo number_format($fineStats['total_unpaid_amount'] + $fineStats['total_paid_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Unpaid Fines</td>
                            <td><?php echo $fineStats['unpaid_count']; ?></td>
                            <td class="text-danger">$<?php echo number_format($fineStats['total_unpaid_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <td>Paid Fines</td>
                            <td><?php echo $fineStats['paid_count']; ?></td>
                            <td class="text-success">$<?php echo number_format($fineStats['total_paid_amount'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
